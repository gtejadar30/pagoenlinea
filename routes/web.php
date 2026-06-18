<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EstadoCuentaController;
use App\Http\Controllers\InicioController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('app.auth')->group(function () {
    Route::get('/inicio', [InicioController::class, 'index'])->name('inicio');
    Route::get('/estado-cuenta', [EstadoCuentaController::class, 'index'])->name('estado-cuenta');
});
