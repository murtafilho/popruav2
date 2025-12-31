<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingController extends Controller
{
    /**
     * Geocodifica um endereço usando Nominatim (OpenStreetMap)
     */
    public function geocode(Request $request): JsonResponse
    {
        $request->validate([
            'logradouro' => 'required|string',
            'numero' => 'nullable|string',
            'bairro' => 'nullable|string',
            'cidade' => 'nullable|string|default:Belo Horizonte',
        ]);

        $logradouro = $request->input('logradouro');
        $numero = $request->input('numero');
        $bairro = $request->input('bairro', '');
        $cidade = $request->input('cidade', 'Belo Horizonte');

        // Monta o endereço completo
        $endereco = trim($logradouro);
        if ($numero) {
            $endereco .= ', '.$numero;
        }
        if ($bairro) {
            $endereco .= ', '.$bairro;
        }
        $endereco .= ', '.$cidade.', MG, Brasil';

        try {
            // Usa Nominatim para geocodificação
            $response = Http::timeout(10)->get('https://nominatim.openstreetmap.org/search', [
                'q' => $endereco,
                'format' => 'json',
                'limit' => 1,
                'addressdetails' => 1,
                'countrycodes' => 'br',
                'accept-language' => 'pt-BR',
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (! empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
                    return response()->json([
                        'success' => true,
                        'lat' => (float) $data[0]['lat'],
                        'lng' => (float) $data[0]['lon'],
                        'display_name' => $data[0]['display_name'] ?? $endereco,
                        'address' => $data[0]['address'] ?? null,
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Endereço não encontrado',
            ], 404);

        } catch (\Exception $e) {
            Log::error('Erro na geocodificação', [
                'endereco' => $endereco,
                'erro' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar coordenadas: '.$e->getMessage(),
            ], 500);
        }
    }
}
