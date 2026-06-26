<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AutorController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\LibroController;
use App\Http\Controllers\Api\PrestamoController;
use App\Http\Controllers\Api\UsuarioController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Biblioteca
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login'])->name('api.login');

Route::middleware('auth:sanctum')->name('api.')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/me', [AuthController::class, 'me'])->name('me');

    // Dashboard agregado
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');

    // Libros
    Route::apiResource('libros', LibroController::class);

    // Autores
    Route::apiResource('autores', AutorController::class)
        ->parameters(['autores' => 'autor']);

    // Usuarios (solo lectura para dropdown del form de préstamos)
    Route::get('usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');

    // Prestamos
    Route::get('prestamos', [PrestamoController::class, 'index'])->name('prestamos.index');
    Route::post('prestamos', [PrestamoController::class, 'store'])->name('prestamos.store');
    Route::put('prestamos/{id}/devolver', [PrestamoController::class, 'devolver'])
        ->whereNumber('id')
        ->name('prestamos.devolver');
});
