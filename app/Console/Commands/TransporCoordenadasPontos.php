<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TransporCoordenadasPontos extends Command
{
    protected $signature = 'pontos:vincular-enderecos
                            {--dry-run : Simula sem gravar alterações}
                            {--raio=200 : Raio máximo de busca em metros}';

    protected $description = 'Vincula pontos ao endereço de porta mais próximo baseado nas coordenadas (usa endereco_atualizado)';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $raio = (float) $this->option('raio');

        if ($dryRun) {
            $this->warn('Modo simulação - nenhuma alteração será gravada');
        }

        $this->info("Raio máximo de busca: {$raio}m");

        // Buscar pontos com coordenadas
        $pontos = DB::table('pontos')
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->where('lat', '!=', 0)
            ->where('lng', '!=', 0)
            ->get();

        $this->info("Encontrados {$pontos->count()} pontos com coordenadas");

        $bar = $this->output->createProgressBar($pontos->count());
        $bar->start();

        $atualizados = 0;
        $foraDoRaio = 0;
        $erros = 0;

        foreach ($pontos as $ponto) {
            // Verificar se ponto tem coordenadas válidas
            if (empty($ponto->lat) || empty($ponto->lng) || ! is_numeric($ponto->lat) || ! is_numeric($ponto->lng)) {
                $bar->advance();

                continue;
            }

            try {
                // Converter raio para graus (aproximação para BH: ~111km por grau)
                // Usar margem de 50% para garantir que pegamos endereços próximos
                $margemGraus = ($raio * 1.5) / 111000;

                // Buscar endereço de porta mais próximo na base de endereços atualizada
                // Usando bounding box para filtrar antes de ordenar (otimização)
                $enderecoAtualizado = DB::table('endereco_atualizados')
                    ->select([
                        'id',
                        'SIGLA_TIPO_LOGRADOURO as tipo',
                        'NOME_LOGRADOURO as logradouro',
                        'NUMERO_IMOVEL as numero',
                        'NOME_BAIRRO_OFICIAL as bairro',
                        'NOME_REGIONAL as regional',
                        'lat',
                        'lng',
                    ])
                    ->whereNotNull('lat')
                    ->whereNotNull('lng')
                    ->whereBetween('lat', [$ponto->lat - $margemGraus, $ponto->lat + $margemGraus])
                    ->whereBetween('lng', [$ponto->lng - $margemGraus, $ponto->lng + $margemGraus])
                    ->orderByRaw('POWER(lat - ?, 2) + POWER(lng - ?, 2)', [$ponto->lat, $ponto->lng])
                    ->first();

                if (! $enderecoAtualizado) {
                    $erros++;
                    $bar->advance();

                    continue;
                }

                // Calcular distância em metros
                $distancia = $this->calcularDistancia(
                    $ponto->lat,
                    $ponto->lng,
                    $enderecoAtualizado->lat,
                    $enderecoAtualizado->lng
                );

                // Verificar se está dentro do raio máximo permitido
                if ($distancia > $raio) {
                    $foraDoRaio++;
                    $bar->advance();

                    continue;
                }

                // Montar referência do endereço
                $referenciaEndereco = sprintf(
                    '%dm de %s %s, %s',
                    round($distancia),
                    $enderecoAtualizado->tipo,
                    $enderecoAtualizado->logradouro,
                    round($enderecoAtualizado->numero)
                );

                if (! $dryRun) {
                    // Atualizar o ponto com endereco_atualizado_id e complemento
                    DB::table('pontos')
                        ->where('id', $ponto->id)
                        ->update([
                            'endereco_atualizado_id' => $enderecoAtualizado->id,
                            'complemento' => DB::raw("COALESCE(NULLIF(complemento, ''), ".DB::connection()->getPdo()->quote($referenciaEndereco).')'),
                            'updated_at' => now(),
                        ]);
                }

                $atualizados++;
            } catch (\Exception $e) {
                $this->error("\nErro no ponto {$ponto->id}: {$e->getMessage()}");
                $erros++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('Concluído!');
        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Total de pontos', $pontos->count()],
                ['Atualizados', $atualizados],
                ['Fora do raio ('.$raio.'m)', $foraDoRaio],
                ['Erros', $erros],
            ]
        );

        if ($dryRun) {
            $this->warn('Execute sem --dry-run para aplicar as alterações');
        }

        return Command::SUCCESS;
    }

    /**
     * Calcula distância em metros entre duas coordenadas usando fórmula de Haversine.
     */
    private function calcularDistancia(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $raioTerra = 6371000; // metros

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $raioTerra * $c;
    }
}
