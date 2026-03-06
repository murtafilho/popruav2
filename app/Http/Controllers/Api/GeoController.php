<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GeoController extends Controller
{
    // Dados geográficos são estáticos — cache de 24h
    private const TTL = 86400;

    public function bairros(): JsonResponse
    {
        $data = Cache::remember('geo:bairros', self::TTL, function () {
            return DB::table('geo_bairros')
                ->select('id', 'codigo', 'nome', 'area_km2', 'perimetro_m', 'geometry')
                ->get()
                ->map(fn ($b) => [
                    'type' => 'Feature',
                    'properties' => [
                        'id' => $b->id,
                        'codigo' => $b->codigo,
                        'nome' => $b->nome,
                        'area_km2' => $b->area_km2,
                        'perimetro_m' => $b->perimetro_m,
                    ],
                    'geometry' => json_decode($b->geometry),
                ]);
        });

        return response()->json(['type' => 'FeatureCollection', 'features' => $data]);
    }

    public function regionais(): JsonResponse
    {
        $data = Cache::remember('geo:regionais', self::TTL, function () {
            return DB::table('geo_regionais')
                ->select('id', 'codigo', 'sigla', 'nome', 'area_km2', 'perimetro_m', 'geometry')
                ->get()
                ->map(fn ($r) => [
                    'type' => 'Feature',
                    'properties' => [
                        'id' => $r->id,
                        'codigo' => $r->codigo,
                        'sigla' => $r->sigla,
                        'nome' => $r->nome,
                        'area_km2' => $r->area_km2,
                        'perimetro_m' => $r->perimetro_m,
                    ],
                    'geometry' => json_decode($r->geometry),
                ]);
        });

        return response()->json(['type' => 'FeatureCollection', 'features' => $data]);
    }

    public function limiteMunicipio(): JsonResponse
    {
        $data = Cache::remember('geo:limite-municipio', self::TTL, function () {
            $limite = DB::table('geo_limite_municipio')
                ->select('id', 'area_km2', 'perimetro_m', 'geometry')
                ->first();

            if (! $limite) {
                return null;
            }

            return [[
                'type' => 'Feature',
                'properties' => [
                    'id' => $limite->id,
                    'area_km2' => $limite->area_km2,
                    'perimetro_m' => $limite->perimetro_m,
                ],
                'geometry' => json_decode($limite->geometry),
            ]];
        });

        if (! $data) {
            return response()->json(['error' => 'Limite não encontrado'], 404);
        }

        return response()->json(['type' => 'FeatureCollection', 'features' => $data]);
    }
}
