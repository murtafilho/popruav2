<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class EnderecoBaseService
{
    /**
     * Raio máximo de busca em metros
     */
    private const RAIO_BUSCA_METROS = 200;

    /**
     * Busca o endereço de porta mais próximo às coordenadas informadas.
     *
     * @param  float  $lat  Latitude
     * @param  float  $lng  Longitude
     * @return object|null Endereço mais próximo ou null se não encontrado
     */
    public function buscarEnderecoMaisProximo(float $lat, float $lng): ?object
    {
        // Calcula bounding box para otimizar a busca (aproximadamente 200m em graus)
        // 1 grau de latitude ≈ 111km, 1 grau de longitude ≈ 111km * cos(lat)
        $deltaLat = self::RAIO_BUSCA_METROS / 111000;
        $deltaLng = self::RAIO_BUSCA_METROS / (111000 * cos(deg2rad($lat)));

        return DB::table('endereco_base')
            ->select([
                'id',
                'SIGLA_TIPO as tipo',
                'NOME_LOGRA as logradouro',
                'NUMERO_IMO as numero',
                'LETRA_IMOV as complemento',
                'NOME_BAIRR as bairro',
                'NOME_REGIO as regional',
                'lat',
                'lng',
            ])
            ->selectRaw(
                'ST_Distance_Sphere(POINT(lng, lat), POINT(?, ?)) as distancia',
                [$lng, $lat]
            )
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->whereBetween('lat', [$lat - $deltaLat, $lat + $deltaLat])
            ->whereBetween('lng', [$lng - $deltaLng, $lng + $deltaLng])
            ->havingRaw('distancia <= ?', [self::RAIO_BUSCA_METROS])
            ->orderBy('distancia', 'asc')
            ->first();
    }

    /**
     * Busca ou cria um registro na tabela ender baseado no endereço encontrado.
     *
     * @param  object  $enderecoBase  Resultado da busca em endereco_base
     * @return int ID do registro em ender
     */
    public function obterOuCriarEnder(object $enderecoBase): int
    {
        // Busca ender existente por logradouro + bairro
        $enderExistente = DB::table('ender')
            ->where('logradouro', $enderecoBase->logradouro)
            ->where('bairro', $enderecoBase->bairro)
            ->first();

        if ($enderExistente) {
            return $enderExistente->id;
        }

        // Cria novo registro em ender
        return DB::table('ender')->insertGetId([
            'tipo' => $enderecoBase->tipo,
            'logradouro' => $enderecoBase->logradouro,
            'bairro' => $enderecoBase->bairro,
            'regional' => $enderecoBase->regional,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Geocodifica um endereço buscando na tabela endereco_base.
     * Primeiro tenta correspondência exata, depois busca número mais próximo.
     *
     * @param  string  $logradouro  Nome do logradouro (pode incluir tipo: "RUA EXEMPLO")
     * @param  string|null  $numero  Número do imóvel
     * @param  string|null  $bairro  Nome do bairro
     * @return object|null Endereço encontrado com lat/lng ou null
     */
    public function geocodificarEndereco(string $logradouro, ?string $numero = null, ?string $bairro = null): ?object
    {
        // Normaliza o logradouro removendo acentos e convertendo para maiúsculas
        $logradouroNormalizado = $this->normalizarTexto($logradouro);

        // Remove o tipo do logradouro se presente (RUA, AV, PCA, etc.)
        $logradouroSemTipo = $this->removerTipoLogradouro($logradouroNormalizado);

        // Converte número para inteiro se possível
        $numeroInt = $numero ? (int) preg_replace('/[^0-9]/', '', $numero) : null;

        // 1. Tenta correspondência exata (logradouro + número + bairro)
        if ($numeroInt && $bairro) {
            $resultado = $this->buscarEnderecoExato($logradouroSemTipo, $numeroInt, $bairro);
            if ($resultado) {
                return $resultado;
            }
        }

        // 2. Tenta correspondência por logradouro + número (sem bairro)
        if ($numeroInt) {
            $resultado = $this->buscarEnderecoExato($logradouroSemTipo, $numeroInt, null);
            if ($resultado) {
                return $resultado;
            }
        }

        // 3. Busca número mais próximo no mesmo logradouro/bairro
        if ($numeroInt && $bairro) {
            $resultado = $this->buscarNumeroMaisProximo($logradouroSemTipo, $numeroInt, $bairro);
            if ($resultado) {
                return $resultado;
            }
        }

        // 4. Busca número mais próximo no mesmo logradouro (qualquer bairro)
        if ($numeroInt) {
            $resultado = $this->buscarNumeroMaisProximo($logradouroSemTipo, $numeroInt, null);
            if ($resultado) {
                return $resultado;
            }
        }

        // 5. Busca qualquer endereço no logradouro/bairro
        if ($bairro) {
            $resultado = $this->buscarPrimeiroEnderecoLogradouro($logradouroSemTipo, $bairro);
            if ($resultado) {
                return $resultado;
            }
        }

        // 6. Busca qualquer endereço no logradouro
        return $this->buscarPrimeiroEnderecoLogradouro($logradouroSemTipo, null);
    }

    /**
     * Normaliza texto removendo acentos e convertendo para maiúsculas.
     */
    private function normalizarTexto(string $texto): string
    {
        $texto = mb_strtoupper($texto, 'UTF-8');
        $texto = preg_replace(
            ['/[ÁÀÃÂÄ]/u', '/[ÉÈÊË]/u', '/[ÍÌÎÏ]/u', '/[ÓÒÕÔÖ]/u', '/[ÚÙÛÜ]/u', '/[Ç]/u'],
            ['A', 'E', 'I', 'O', 'U', 'C'],
            $texto
        );

        return trim($texto);
    }

    /**
     * Remove o tipo do logradouro (RUA, AV, PCA, etc.).
     */
    private function removerTipoLogradouro(string $logradouro): string
    {
        $tipos = ['RUA', 'AVENIDA', 'AVE', 'AV', 'PRACA', 'PCA', 'TRAVESSA', 'TV', 'ALAMEDA', 'AL', 'BECO', 'BC', 'LARGO', 'LG', 'RODOVIA', 'ROD'];

        foreach ($tipos as $tipo) {
            if (str_starts_with($logradouro, $tipo.' ')) {
                return trim(substr($logradouro, strlen($tipo)));
            }
        }

        return $logradouro;
    }

    /**
     * Busca endereço com correspondência exata.
     */
    private function buscarEnderecoExato(string $logradouro, int $numero, ?string $bairro): ?object
    {
        $query = DB::table('endereco_base')
            ->select([
                'id',
                'SIGLA_TIPO as tipo',
                'NOME_LOGRA as logradouro',
                'NUMERO_IMO as numero',
                'LETRA_IMOV as complemento',
                'NOME_BAIRR as bairro',
                'NOME_REGIO as regional',
                'lat',
                'lng',
            ])
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->where('NOME_LOGRA', 'LIKE', '%'.$logradouro.'%')
            ->where('NUMERO_IMO', $numero);

        if ($bairro) {
            $bairroNormalizado = $this->normalizarTexto($bairro);
            $query->where('NOME_BAIRR', 'LIKE', '%'.$bairroNormalizado.'%');
        }

        return $query->first();
    }

    /**
     * Busca o número mais próximo no mesmo logradouro.
     */
    private function buscarNumeroMaisProximo(string $logradouro, int $numero, ?string $bairro): ?object
    {
        $query = DB::table('endereco_base')
            ->select([
                'id',
                'SIGLA_TIPO as tipo',
                'NOME_LOGRA as logradouro',
                'NUMERO_IMO as numero',
                'LETRA_IMOV as complemento',
                'NOME_BAIRR as bairro',
                'NOME_REGIO as regional',
                'lat',
                'lng',
            ])
            ->selectRaw('ABS(NUMERO_IMO - ?) as diferenca_numero', [$numero])
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->whereNotNull('NUMERO_IMO')
            ->where('NOME_LOGRA', 'LIKE', '%'.$logradouro.'%');

        if ($bairro) {
            $bairroNormalizado = $this->normalizarTexto($bairro);
            $query->where('NOME_BAIRR', 'LIKE', '%'.$bairroNormalizado.'%');
        }

        return $query->orderBy('diferenca_numero', 'asc')
            ->first();
    }

    /**
     * Busca o primeiro endereço de um logradouro.
     */
    private function buscarPrimeiroEnderecoLogradouro(string $logradouro, ?string $bairro): ?object
    {
        $query = DB::table('endereco_base')
            ->select([
                'id',
                'SIGLA_TIPO as tipo',
                'NOME_LOGRA as logradouro',
                'NUMERO_IMO as numero',
                'LETRA_IMOV as complemento',
                'NOME_BAIRR as bairro',
                'NOME_REGIO as regional',
                'lat',
                'lng',
            ])
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->where('NOME_LOGRA', 'LIKE', '%'.$logradouro.'%');

        if ($bairro) {
            $bairroNormalizado = $this->normalizarTexto($bairro);
            $query->where('NOME_BAIRR', 'LIKE', '%'.$bairroNormalizado.'%');
        }

        return $query->orderBy('NUMERO_IMO', 'asc')
            ->first();
    }

    /**
     * Processa um ponto recém-criado, vinculando ao endereço mais próximo.
     *
     * @param  int  $pontoId  ID do ponto
     * @param  float  $lat  Latitude
     * @param  float  $lng  Longitude
     * @return bool True se encontrou e vinculou endereço, false caso contrário
     */
    public function vincularEnderecoAoPonto(int $pontoId, float $lat, float $lng): bool
    {
        $enderecoBase = $this->buscarEnderecoMaisProximo($lat, $lng);

        if (! $enderecoBase) {
            return false;
        }

        $enderId = $this->obterOuCriarEnder($enderecoBase);

        // Formata o número (pode ser decimal no banco)
        $numero = $enderecoBase->numero
            ? (string) intval($enderecoBase->numero)
            : 'S/N';

        // Adiciona letra/complemento se existir
        $complemento = trim($enderecoBase->complemento ?? '');

        DB::table('pontos')
            ->where('id', $pontoId)
            ->update([
                'endereco_id' => $enderId,
                'numero' => $numero,
                'complemento' => $complemento ?: null,
                'updated_at' => now(),
            ]);

        return true;
    }
}
