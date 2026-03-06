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

    // Mapa e Vistorias
    Route::get('/mapa', [MapaController::class, 'index'])->name('mapa.index');
    Route::get('/pontos', [\App\Http\Controllers\PontoController::class, 'index'])->name('pontos.index');
    Route::get('/pontos/nao-georreferenciados', [\App\Http\Controllers\PontoController::class, 'naoGeorreferenciados'])->name('pontos.nao-georreferenciados');
    Route::get('/pontos/{id}', [\App\Http\Controllers\PontoController::class, 'show'])->name('pontos.show');
    Route::get('/pontos/{ponto}/vistorias/create', [VistoriaController::class, 'createForPonto'])->name('pontos.vistorias.create');
    Route::get('/vistorias', [VistoriaController::class, 'index'])->name('vistorias.index');
    Route::get('/vistorias/create', [VistoriaController::class, 'create'])->name('vistorias.create');
    Route::get('/vistorias/{vistoria}', [VistoriaController::class, 'show'])->name('vistorias.show');
    Route::get('/vistorias/{vistoria}/relatorio', [VistoriaController::class, 'report'])->name('vistorias.report');
    Route::get('/vistorias/{vistoria}/edit', [VistoriaController::class, 'edit'])->name('vistorias.edit');
    Route::post('/vistorias', [VistoriaController::class, 'store'])->name('vistorias.store');
    Route::put('/vistorias/{vistoria}', [VistoriaController::class, 'update'])->name('vistorias.update');
    Route::delete('/vistorias/{vistoria}', [VistoriaController::class, 'destroy'])->name('vistorias.destroy');

    // Moradores
    Route::resource('moradores', MoradorController::class)->parameters(['moradores' => 'morador']);

    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('roles', RoleController::class)->except(['show']);
        Route::resource('permissions', PermissionController::class)->only(['index', 'create', 'store', 'destroy']);
        Route::get('users', [UserRoleController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserRoleController::class, 'create'])->name('users.create');
        Route::post('users', [UserRoleController::class, 'store'])->name('users.store');
        Route::put('users/{user}/roles', [UserRoleController::class, 'updateRoles'])->name('users.roles.update');
    });
});

// Power BI - rota publica (sem autenticacao)
Route::get('/powerbi', function () {
    return view('powerbi.index');
})->name('powerbi.index');

// Discussao - rota publica (sem autenticacao)
Route::get('/discussao', function () {
    return view('discussao.index');
})->name('discussao.index');

require __DIR__.'/auth.php';
