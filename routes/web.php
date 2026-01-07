<?php

use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserRoleController;
use App\Http\Controllers\MapaController;
use App\Http\Controllers\MoradorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VistoriaController;
use Illuminate\Support\Facades\Route;

// Todas as rotas protegidas por autenticação
Route::middleware('auth')->group(function () {
    // Home redireciona para dashboard
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Power BI
    Route::get('/powerbi', function () {
        return view('powerbi.index');
    })->name('powerbi.index')->can('ver relatorios');

    // Mapa e Vistorias
    Route::get('/mapa', [MapaController::class, 'index'])->name('mapa.index');
    Route::get('/pontos', [\App\Http\Controllers\PontoController::class, 'index'])->name('pontos.index');
    Route::get('/pontos/nao-georreferenciados', [\App\Http\Controllers\PontoController::class, 'naoGeorreferenciados'])->name('pontos.nao-georreferenciados');
    Route::get('/pontos/{id}', [\App\Http\Controllers\PontoController::class, 'show'])->name('pontos.show');
    Route::get('/vistorias', [VistoriaController::class, 'index'])->name('vistorias.index');
    Route::get('/vistorias/create', [VistoriaController::class, 'create'])->name('vistorias.create');
    Route::post('/vistorias', [VistoriaController::class, 'store'])->name('vistorias.store');

    // Moradores
    Route::resource('moradores', MoradorController::class)->parameters(['moradores' => 'morador']);

    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('roles', RoleController::class)->except(['show']);
        Route::resource('permissions', PermissionController::class)->only(['index', 'create', 'store', 'destroy']);
        Route::get('users', [UserRoleController::class, 'index'])->name('users.index');
        Route::put('users/{user}/roles', [UserRoleController::class, 'updateRoles'])->name('users.roles.update');
    });
});

require __DIR__.'/auth.php';
