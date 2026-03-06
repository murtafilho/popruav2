<?php

use App\Http\Controllers\Api\GeoController;
use App\Http\Controllers\Api\MoradorController;
use App\Http\Controllers\Api\PontoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/pontos', [PontoController::class, 'index']);
Route::get('/pontos/{id}', [PontoController::class, 'show']);

// Busca de endereços
Route::get('/enderecos/logradouros', [PontoController::class, 'buscarLogradouros']);
Route::get('/enderecos/buscar', [PontoController::class, 'buscarEndereco']);
Route::get('/enderecos/por-coordenadas', [PontoController::class, 'buscarEnderecoPorCoordenadas']);
Route::patch('/pontos/{id}/coordenadas', [PontoController::class, 'updateCoordenadas']);
Route::get('/pontos/nao-georreferenciados/logradouros', [PontoController::class, 'buscarLogradourosNaoGeorreferenciados']);
Route::get('/pontos/{ponto}/moradores', [MoradorController::class, 'porPonto']);

Route::prefix('geo')->group(function () {
    Route::get('/bairros', [GeoController::class, 'bairros']);
    Route::get('/regionais', [GeoController::class, 'regionais']);
    Route::get('/limite-municipio', [GeoController::class, 'limiteMunicipio']);
});

Route::post('/geocode', [\App\Http\Controllers\Api\GeocodingController::class, 'geocode']);

// Vistorias - autocomplete de logradouros
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/vistorias/logradouros', [\App\Http\Controllers\VistoriaController::class, 'buscarLogradouros']);
});

// Fotos de Vistorias (upload offline-first via Service Worker)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/vistorias/fotos', [\App\Http\Controllers\Api\VistoriaFotoController::class, 'store']);
    Route::get('/vistorias/{vistoria}/fotos/status', [\App\Http\Controllers\Api\VistoriaFotoController::class, 'status']);
});

// Client logs (debug mobile)
Route::post('/client-logs', [\App\Http\Controllers\Api\ClientLogController::class, 'store']);

// Moradores
Route::prefix('moradores')->group(function () {
    Route::get('/', [MoradorController::class, 'index']);
    Route::get('/buscar', [MoradorController::class, 'buscar']);
    Route::get('/arquivados', [MoradorController::class, 'arquivados']);
    Route::post('/', [MoradorController::class, 'store']);
    Route::get('/{morador}', [MoradorController::class, 'show']);
    Route::put('/{morador}', [MoradorController::class, 'update']);
    Route::delete('/{morador}', [MoradorController::class, 'destroy']);
    Route::post('/{id}/restaurar', [MoradorController::class, 'restore']);
    Route::get('/{morador}/historico', [MoradorController::class, 'historico']);
    Route::post('/{morador}/entrada', [MoradorController::class, 'entrada']);
    Route::post('/{morador}/saida', [MoradorController::class, 'saida']);
    Route::post('/{morador}/transferir', [MoradorController::class, 'transferir']);
});
