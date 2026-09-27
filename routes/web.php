<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\MotocicletaController;
use App\Http\Controllers\RepuestoController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::resource('clientes', ClienteController::class);
    Route::resource('motocicletas', MotocicletaController::class);
    Route::resource('repuestos', RepuestoController::class)->except(['show']);
    Route::resource('servicios', ServicioController::class)->except(['show']);
    Route::resource('usuarios', UserController::class)->except(['show']);

    Route::post('servicios/{servicio}/repuestos', [ServicioController::class, 'agregarRepuesto'])
        ->name('servicios.repuestos.store');
    Route::delete('servicios/{servicio}/repuestos/{detalle}', [ServicioController::class, 'quitarRepuesto'])
        ->name('servicios.repuestos.destroy');

    Route::resource('facturas', FacturaController::class)->except(['show']);

    Route::get('facturas/{factura}/pdf', [FacturaController::class, 'pdf'])->name('facturas.pdf');
});
