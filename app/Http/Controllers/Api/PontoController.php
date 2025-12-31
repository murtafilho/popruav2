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

        // Query com resultado da última vistoria
        $pontos = DB::table('pontos as p')
            ->join('ender as e', 'e.id', '=', 'p.endereco_id')
            ->leftJoin(DB::raw('(SELECT ponto_id, MAX(id) as ultima_vistoria_id FROM vistorias GROUP BY ponto_id) as uv'), 'uv.ponto_id', '=', 'p.id')
            ->leftJoin('vistorias as v', 'v.id', '=', 'uv.ultima_vistoria_id')
            ->select([
                'p.id',
                'p.numero',
                'p.complemento',
                'p.lat',
                'p.lng',
                'e.logradouro',
                'e.bairro',
                'e.regional',
                'e.tipo',
                'v.resultado_acao_id',
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
        // Para detalhes, usa a view completa com estatísticas
        $ponto = DB::table('v_pontos')->where('id', $id)->first();

        if (!$ponto) {
            return response()->json(['error' => 'Ponto não encontrado'], 404);
        }

        return response()->json($ponto);
    }
}
