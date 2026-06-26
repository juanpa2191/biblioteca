<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Biblioteca (frontend Blade)
|--------------------------------------------------------------------------
| La autenticación se gestiona en el cliente: cada vista verifica si existe
| un token en localStorage; si no, redirige a /login. Todas las llamadas a
| datos van a /api/* con Authorization: Bearer <token>.
*/

Route::get('/', fn () => redirect()->route('dashboard'));

Route::view('/login',     'auth.login')->name('login');
Route::view('/dashboard', 'dashboard')->name('dashboard');
Route::view('/libros',    'libros')->name('libros.index');
Route::view('/prestamos', 'prestamos')->name('prestamos.index');
