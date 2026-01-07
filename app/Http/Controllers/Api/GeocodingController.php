<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\EnderecoBaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingController extends Controller
{
    public function __construct(
        private EnderecoBaseService $enderecoBaseService
    ) {}

    /**
     * Geocodifica um endereço.
     * Primeiro busca na tabela endereco_base, depois usa Nominatim (OpenStreetMap) como fallback.
     */
    public function geocode(Request $request): JsonResponse
    {
        $request->validate([
            'logradouro' => 'required|string',
            'numero' => 'nullable|string',
            'bairro' => 'nullable|string',
            'cidade' => 'nullable|string',
        ]);

        $logradouro = $request->input('logradouro');
        $numero = $request->input('numero');
        $bairro = $request->input('bairro', '');

        // 1. Tenta buscar na tabela endereco_base primeiro
        $enderecoBase = $this->enderecoBaseService->geocodificarEndereco($logradouro, $numero, $bairro);

        if ($enderecoBase && $enderecoBase->lat && $enderecoBase->lng) {
            $enderecoEncontrado = $enderecoBase->tipo.' '.$enderecoBase->logradouro.', '.intval($enderecoBase->numero);
            if ($enderecoBase->bairro) {
                $enderecoEncontrado .= ' - '.$enderecoBase->bairro;
            }

            return response()->json([
                'success' => true,
                'lat' => (float) $enderecoBase->lat,
                'lng' => (float) $enderecoBase->lng,
                'source' => 'endereco_base',
                'display_name' => $enderecoEncontrado,
                'address' => [
                    'tipo' => $enderecoBase->tipo,
                    'logradouro' => $enderecoBase->logradouro,
                    'numero' => intval($enderecoBase->numero),
                    'bairro' => $enderecoBase->bairro,
                    'regional' => $enderecoBase->regional,
                ],
            ]);
        }

        // 2. Fallback para Nominatim (OpenStreetMap)
        return $this->geocodeViaNominatim($logradouro, $numero, $bairro, $request->input('cidade', 'Belo Horizonte'));
    }

    /**
     * Geocodifica usando Nominatim (OpenStreetMap)
     */
    private function geocodeViaNominatim(string $logradouro, ?string $numero, ?string $bairro, string $cidade): JsonResponse
    {
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
                        'source' => 'nominatim',
                        'display_name' => $data[0]['display_name'] ?? $endereco,
                        'address' => $data[0]['address'] ?? null,
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Endereço não encontrado na base local nem no OpenStreetMap.',
            ], 404);

        } catch (\Exception $e) {
            Log::error('Erro na geocodificação via Nominatim', [
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
