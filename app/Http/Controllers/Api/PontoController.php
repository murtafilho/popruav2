<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PontoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'north' => 'required|numeric',
            'south' => 'required|numeric',
            'east' => 'required|numeric',
            'west' => 'required|numeric',
        ]);

        // Query com resultado da última vistoria e contagem de vistorias
        $pontos = DB::table('pontos as p')
            ->leftJoin('endereco_atualizados as ea', 'ea.id', '=', 'p.endereco_atualizado_id')
            ->leftJoin(DB::raw('(SELECT ponto_id, MAX(id) as ultima_vistoria_id, COUNT(*) as total_vistorias FROM vistorias GROUP BY ponto_id) as uv'), 'uv.ponto_id', '=', 'p.id')
            ->leftJoin('vistorias as v', 'v.id', '=', 'uv.ultima_vistoria_id')
            ->select([
                'p.id',
                DB::raw('COALESCE(ea."NUMERO_IMOVEL", p.numero) as numero'),
                'p.complemento',
                'p.lat',
                'p.lng',
                DB::raw('ea."NOME_LOGRADOURO" as logradouro'),
                DB::raw('ea."NOME_BAIRRO_POPULAR" as bairro'),
                DB::raw('ea."NOME_REGIONAL" as regional'),
                DB::raw('ea."SIGLA_TIPO_LOGRADOURO" as tipo'),
                'v.resultado_acao_id',
                DB::raw('COALESCE(uv.total_vistorias, 0) as total_vistorias'),
                'uv.ultima_vistoria_id',
                DB::raw('COALESCE(v.resistencia::int, 0) + COALESCE(v.num_reduzido::int, 0) + COALESCE(v.casal::int, 0) + COALESCE(v.catador_reciclados::int, 0) + COALESCE(v.fixacao_antiga::int, 0) + COALESCE(v.excesso_objetos::int, 0) + COALESCE(v.trafico_ilicitos::int, 0) + COALESCE(v.crianca_adolescente::int, 0) + COALESCE(v.idosos::int, 0) + COALESCE(v.gestante::int, 0) + COALESCE(v.lgbtqiapn::int, 0) + COALESCE(v.cena_uso_caracterizada::int, 0) + COALESCE(v.deficiente::int, 0) + COALESCE(v.agrupamento_quimico::int, 0) + COALESCE(v.saude_mental::int, 0) + COALESCE(v.animais::int, 0) as complexidade'),
            ])
            ->whereNotNull('p.lat')
            ->whereNotNull('p.lng')
            ->whereBetween('p.lat', [$validated['south'], $validated['north']])
            ->whereBetween('p.lng', [$validated['west'], $validated['east']])
            ->limit(5000)
            ->get();

        return response()->json($pontos);
    }

    public function show(int $id): JsonResponse
    {
        $ponto = \App\Models\Ponto::with(['enderecoAtualizado', 'ultimaVistoria', 'caracteristicaAbrigo'])
            ->withCount('vistorias as contador')
            ->withSum('vistorias', 'qtd_kg')
            ->find($id);

        if (! $ponto) {
            return response()->json(['error' => 'Ponto não encontrado'], 404);
        }

        $endereco = $ponto->enderecoAtualizado;

        return response()->json([
            'id' => $ponto->id,
            'numero' => $endereco?->NUMERO_IMOVEL ?? $ponto->numero,
            'complemento' => $ponto->complemento,
            'logradouro' => $endereco?->NOME_LOGRADOURO,
            'bairro' => $endereco?->NOME_BAIRRO_POPULAR,
            'regional' => $endereco?->NOME_REGIONAL,
            'tipo' => $endereco?->SIGLA_TIPO_LOGRADOURO,
            'lat' => $ponto->lat,
            'lng' => $ponto->lng,
            'caracteristica_abrigo' => $ponto->caracteristicaAbrigo?->nome,
            'contador' => $ponto->contador ?? 0,
            'soma_kg' => $ponto->vistorias_sum_qtd_kg ?? 0,
            'complexidade' => $ponto->complexidade,
            'resultado' => $ponto->ultimaVistoria?->resultadoAcao?->nome,
            'resultado_acao_id' => $ponto->ultimaVistoria?->resultado_acao_id,
        ]);
    }

    /**
     * Busca logradouros distintos para autocomplete.
     * Retorna: TIPO LOGRADOURO - REGIONAL
     */
    public function buscarLogradouros(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => 'required|string|min:2|max:100',
        ]);

        $termo = $validated['q'];

        // Usar subquery para contornar limitação do PostgreSQL com DISTINCT + ORDER BY
        $subquery = DB::table('endereco_atualizados')
            ->selectRaw('DISTINCT "SIGLA_TIPO_LOGRADOURO" as tipo, "NOME_LOGRADOURO" as logradouro, "NOME_REGIONAL" as regional')
            ->where('NOME_LOGRADOURO', 'ilike', "%{$termo}%");

        $logradouros = DB::query()
            ->fromSub($subquery, 'sub')
            ->select(['tipo', 'logradouro', 'regional'])
            ->orderByRaw('CASE WHEN logradouro ILIKE ? THEN 0 ELSE 1 END', ["{$termo}%"])
            ->orderBy('logradouro')
            ->orderBy('regional')
            ->limit(20)
            ->get();

        return response()->json($logradouros);
    }

    /**
     * Busca endereço por logradouro e número.
     * Se não encontrar número exato, retorna o mais próximo.
     * Se não informar número, centraliza no meio da numeração do logradouro.
     */
    public function buscarEndereco(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'logradouro' => 'required|string|min:2|max:100',
            'numero' => 'nullable|integer|min:1',
            'regional' => 'nullable|string|max:50',
        ]);

        $logradouro = $validated['logradouro'];
        $numeroInformado = array_key_exists('numero', $validated) && $validated['numero'] !== null;
        $numeroBuscado = $validated['numero'] ?? null;
        $regional = $validated['regional'] ?? null;

        // Query base
        $baseQuery = DB::table('endereco_atualizados')
            ->where('NOME_LOGRADOURO', $logradouro)
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->whereRaw("\"NUMERO_IMOVEL\" ~ '^[0-9]+$'");

        if ($regional) {
            $baseQuery->where('NOME_REGIONAL', $regional);
        }

        // Se número não foi informado, calcular o ponto médio da numeração
        if ($numeroBuscado === null) {
            $faixa = (clone $baseQuery)
                ->selectRaw('MIN(CAST("NUMERO_IMOVEL" AS INTEGER)) as numero_min, MAX(CAST("NUMERO_IMOVEL" AS INTEGER)) as numero_max')
                ->first();

            if (! $faixa || $faixa->numero_min === null) {
                return response()->json([
                    'encontrado' => false,
                    'message' => 'Logradouro não encontrado',
                ]);
            }

            // Calcula o número médio
            $numeroBuscado = (int) round(($faixa->numero_min + $faixa->numero_max) / 2);
        }

        // Busca na tabela endereco_atualizado
        $query = DB::table('endereco_atualizados')
            ->select([
                'id',
                DB::raw('"SIGLA_TIPO_LOGRADOURO" as tipo'),
                DB::raw('"NOME_LOGRADOURO" as logradouro'),
                DB::raw('"NUMERO_IMOVEL" as numero'),
                DB::raw('"NOME_BAIRRO_POPULAR" as bairro'),
                DB::raw('"NOME_REGIONAL" as regional'),
                'lat',
                'lng',
            ])
            ->where('NOME_LOGRADOURO', $logradouro)
            ->whereNotNull('lat')
            ->whereNotNull('lng');

        if ($regional) {
            $query->where('NOME_REGIONAL', $regional);
        }

        // Primeiro tenta número exato
        $exato = (clone $query)->where('NUMERO_IMOVEL', $numeroBuscado)->first();

        if ($exato) {
            return response()->json([
                'encontrado' => true,
                'exato' => true,
                'numero_informado' => $numeroInformado,
                'endereco' => $exato,
            ]);
        }

        // Se não encontrou exato, busca o mais próximo
        $maisProximo = $query
            ->whereRaw("\"NUMERO_IMOVEL\" ~ '^[0-9]+$'")
            ->orderByRaw('ABS(CAST("NUMERO_IMOVEL" AS INTEGER) - ?)', [$numeroBuscado])
            ->first();

        if ($maisProximo) {
            return response()->json([
                'encontrado' => true,
                'exato' => false,
                'numero_informado' => $numeroInformado,
                'numero_buscado' => $numeroBuscado,
                'endereco' => $maisProximo,
            ]);
        }

        return response()->json([
            'encontrado' => false,
            'message' => 'Endereço não encontrado',
        ]);
    }

    /**
     * Busca o endereço de porta mais próximo de uma coordenada.
     */
    public function buscarEnderecoPorCoordenadas(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        // Usar bounding box para otimizar
        $raio = 300; // metros
        $margemGraus = ($raio * 1.5) / 111000;

        $endereco = DB::table('endereco_atualizados')
            ->select([
                'id',
                DB::raw('"SIGLA_TIPO_LOGRADOURO" as tipo'),
                DB::raw('"NOME_LOGRADOURO" as logradouro'),
                DB::raw('"NUMERO_IMOVEL" as numero'),
                DB::raw('"NOME_BAIRRO_POPULAR" as bairro'),
                DB::raw('"NOME_REGIONAL" as regional'),
                'lat',
                'lng',
            ])
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->whereBetween('lat', [$validated['lat'] - $margemGraus, $validated['lat'] + $margemGraus])
            ->whereBetween('lng', [$validated['lng'] - $margemGraus, $validated['lng'] + $margemGraus])
            ->orderByRaw('POWER(lat - ?, 2) + POWER(lng - ?, 2)', [$validated['lat'], $validated['lng']])
            ->first();

        if (! $endereco) {
            return response()->json([
                'encontrado' => false,
                'message' => 'Nenhum endereço encontrado nas proximidades',
            ]);
        }

        // Calcular distância aproximada em metros
        $distancia = $this->calcularDistancia(
            $validated['lat'],
            $validated['lng'],
            $endereco->lat,
            $endereco->lng
        );

        return response()->json([
            'encontrado' => true,
            'endereco' => $endereco,
            'distancia_metros' => round($distancia),
        ]);
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

    /**
     * Busca logradouros distintos dos pontos não georreferenciados para autocomplete.
     */
    public function buscarLogradourosNaoGeorreferenciados(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => 'required|string|min:2|max:100',
        ]);

        $termo = $validated['q'];

        // Usar subquery para contornar limitação do PostgreSQL com DISTINCT + ORDER BY
        $subquery = DB::table('pontos as p')
            ->leftJoin('endereco_atualizados as ea', 'ea.id', '=', 'p.endereco_atualizado_id')
            ->selectRaw('DISTINCT ea."SIGLA_TIPO_LOGRADOURO" as tipo, ea."NOME_LOGRADOURO" as logradouro, ea."NOME_REGIONAL" as regional')
            ->where(function ($q) {
                $q->whereNull('p.lat')
                    ->orWhereNull('p.lng')
                    ->orWhere('p.lat', '=', 0)
                    ->orWhere('p.lng', '=', 0);
            })
            ->where('ea.NOME_LOGRADOURO', 'ilike', "%{$termo}%");

        $logradouros = DB::query()
            ->fromSub($subquery, 'sub')
            ->select(['tipo', 'logradouro', 'regional'])
            ->orderByRaw('CASE WHEN logradouro ILIKE ? THEN 0 ELSE 1 END', ["{$termo}%"])
            ->orderBy('logradouro')
            ->orderBy('regional')
            ->limit(20)
            ->get();

        return response()->json($logradouros);
    }

    /**
     * Atualiza as coordenadas de um ponto (geocodificação manual).
     */
    public function updateCoordenadas(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        // Buscar ponto com endereço
        $ponto = DB::table('pontos as p')
            ->leftJoin('endereco_atualizados as ea', 'ea.id', '=', 'p.endereco_atualizado_id')
            ->select([
                'p.id',
                DB::raw('COALESCE(ea."NUMERO_IMOVEL", p.numero) as numero'),
                'p.complemento',
                DB::raw('ea."SIGLA_TIPO_LOGRADOURO" as tipo'),
                DB::raw('ea."NOME_LOGRADOURO" as logradouro'),
                DB::raw('ea."NOME_BAIRRO_POPULAR" as bairro'),
                DB::raw('ea."NOME_REGIONAL" as regional'),
            ])
            ->where('p.id', $id)
            ->first();

        if (! $ponto) {
            return response()->json(['error' => 'Ponto não encontrado'], 404);
        }

        DB::table('pontos')
            ->where('id', $id)
            ->update([
                'lat' => $validated['lat'],
                'lng' => $validated['lng'],
                'updated_at' => now(),
            ]);

        // Usar bounding box para otimizar busca de endereço próximo
        $raio = 300;
        $margemGraus = ($raio * 1.5) / 111000;

        // Buscar endereço mais próximo na base de endereços
        $enderecoProximo = DB::table('endereco_atualizados')
            ->select([
                DB::raw('"SIGLA_TIPO_LOGRADOURO" as tipo'),
                DB::raw('"NOME_LOGRADOURO" as logradouro'),
                DB::raw('"NUMERO_IMOVEL" as numero'),
                DB::raw('"NOME_BAIRRO_POPULAR" as bairro'),
                'lat',
                'lng',
            ])
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->whereBetween('lat', [$validated['lat'] - $margemGraus, $validated['lat'] + $margemGraus])
            ->whereBetween('lng', [$validated['lng'] - $margemGraus, $validated['lng'] + $margemGraus])
            ->orderByRaw('POWER(lat - ?, 2) + POWER(lng - ?, 2)', [$validated['lat'], $validated['lng']])
            ->first();

        // Montar endereço do ponto
        $enderecoPonto = trim("{$ponto->tipo} {$ponto->logradouro}, {$ponto->numero}");
        if ($ponto->complemento) {
            $enderecoPonto .= " - {$ponto->complemento}";
        }

        // Montar endereço de referência
        $enderecoReferencia = null;
        if ($enderecoProximo) {
            $enderecoReferencia = trim("{$enderecoProximo->tipo} {$enderecoProximo->logradouro}, {$enderecoProximo->numero}");
        }

        return response()->json([
            'success' => true,
            'message' => 'Coordenadas atualizadas com sucesso',
            'ponto_id' => $id,
            'lat' => $validated['lat'],
            'lng' => $validated['lng'],
            'endereco_ponto' => $enderecoPonto,
            'bairro' => $ponto->bairro,
            'endereco_referencia' => $enderecoReferencia,
        ]);
    }
}
