<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProyectoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| US-01: rutas de autenticación mock + raíz. La raíz es pública y redirige
| según el estado de sesión: logueado → /dashboard, no logueado → /login.
|
*/

// Página pública de login (solo accesible si NO está logueado).
Route::get('/login', [AuthController::class, 'showLogin'])
    ->middleware('auth.mock.landmark')
    ->name('login');

// POST mock de login (ambos botones del form usan esta ruta).
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Logout (mock): destruye la sesión y vuelve al login.
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth.mock')
    ->name('logout');

// Raíz: pública. Redirige según login (landmark de US-01).
Route::get('/', fn () => redirect()->guest(route('login')))
    ->middleware('auth.mock.landmark')
    ->name('home');

/*
|--------------------------------------------------------------------------
| Rutas protegidas (requieren sesión mock)
|--------------------------------------------------------------------------
*/

Route::middleware('auth.mock')->group(function () {
    Route::get('/dashboard', [ProyectoController::class, 'dashboard'])->name('dashboard');

    // US-07: reporte en PDF del portfolio.
    Route::get('/reporte', [ProyectoController::class, 'reporte'])->name('reporte');

    Route::get('/areas', [ProyectoController::class, 'areas'])->name('areas');

    // US-05: cambio de estado directo desde el listado.
    Route::post('/proyectos/{proyecto}/estado', [ProyectoController::class, 'cambiarEstado'])
        ->name('proyectos.estado');

    Route::resource('proyectos', ProyectoController::class);
});
