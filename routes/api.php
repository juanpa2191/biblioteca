<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AutorController;
use App\Http\Controllers\Api\LibroController;
use App\Http\Controllers\Api\PrestamoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Biblioteca
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login'])->name('api.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/me', [AuthController::class, 'me'])->name('api.me');

    // Libros
    Route::apiResource('libros', LibroController::class);

    // Autores
    Route::apiResource('autores', AutorController::class)
        ->parameters(['autores' => 'autor']);

    // Prestamos
    Route::get('prestamos', [PrestamoController::class, 'index'])->name('prestamos.index');
    Route::post('prestamos', [PrestamoController::class, 'store'])->name('prestamos.store');
    Route::put('prestamos/{id}/devolver', [PrestamoController::class, 'devolver'])
        ->whereNumber('id')
        ->name('prestamos.devolver');
});
