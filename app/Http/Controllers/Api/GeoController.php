<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class GeoController extends Controller
{
    public function bairros(): JsonResponse
    {
        $bairros = DB::table('geo_bairros')
            ->select('id', 'codigo', 'nome', 'area_km2', 'perimetro_m', 'geometry')
            ->get();

        $features = $bairros->map(function ($bairro) {
            return [
                'type' => 'Feature',
                'properties' => [
                    'id' => $bairro->id,
                    'codigo' => $bairro->codigo,
                    'nome' => $bairro->nome,
                    'area_km2' => $bairro->area_km2,
                    'perimetro_m' => $bairro->perimetro_m,
                ],
                'geometry' => json_decode($bairro->geometry),
            ];
        });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }

    public function regionais(): JsonResponse
    {
        $regionais = DB::table('geo_regionais')
            ->select('id', 'codigo', 'sigla', 'nome', 'area_km2', 'perimetro_m', 'geometry')
            ->get();

        $features = $regionais->map(function ($regional) {
            return [
                'type' => 'Feature',
                'properties' => [
                    'id' => $regional->id,
                    'codigo' => $regional->codigo,
                    'sigla' => $regional->sigla,
                    'nome' => $regional->nome,
                    'area_km2' => $regional->area_km2,
                    'perimetro_m' => $regional->perimetro_m,
                ],
                'geometry' => json_decode($regional->geometry),
            ];
        });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }

    public function limiteMunicipio(): JsonResponse
    {
        $limite = DB::table('geo_limite_municipio')
            ->select('id', 'area_km2', 'perimetro_m', 'geometry')
            ->first();

        if (!$limite) {
            return response()->json(['error' => 'Limite não encontrado'], 404);
        }

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => [
                [
                    'type' => 'Feature',
                    'properties' => [
                        'id' => $limite->id,
                        'area_km2' => $limite->area_km2,
                        'perimetro_m' => $limite->perimetro_m,
                    ],
                    'geometry' => json_decode($limite->geometry),
                ],
            ],
        ]);
    }
}
