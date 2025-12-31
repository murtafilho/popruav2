<?php

use App\Http\Controllers\Api\GeoController;
use App\Http\Controllers\Api\PontoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/pontos', [PontoController::class, 'index']);
Route::get('/pontos/{id}', [PontoController::class, 'show']);

Route::prefix('geo')->group(function () {
    Route::get('/bairros', [GeoController::class, 'bairros']);
    Route::get('/regionais', [GeoController::class, 'regionais']);
    Route::get('/limite-municipio', [GeoController::class, 'limiteMunicipio']);
});

Route::post('/geocode', [\App\Http\Controllers\Api\GeocodingController::class, 'geocode']);
