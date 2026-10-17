<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\MotocicletaController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\PortalPedidoController;
use App\Http\Controllers\RegistroClienteController;
use App\Http\Controllers\RepuestoController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificacionCorreoController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

// Solo invitados
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('registro', [RegistroClienteController::class, 'create'])->name('registro');
    Route::post('registro', [RegistroClienteController::class, 'store'])->middleware('throttle:10,1');
});

// Cualquier usuario con sesión (el cliente sin verificar incluido)
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('verificar-correo', [VerificacionCorreoController::class, 'show']);
    Route::post('verificar-correo', [VerificacionCorreoController::class, 'verificar'])->middleware('throttle:6,1');
    Route::post('verificar-correo/reenviar', [VerificacionCorreoController::class, 'reenviar'])->middleware('throttle:3,1');
});

// Portal del cliente (verificado)
Route::middleware(['auth', 'cliente'])->group(function () {
    Route::get('portal', [PortalController::class, 'index'])->name('portal');
    Route::get('portal/facturas/{factura}/pdf', [PortalController::class, 'pdf'])->name('portal.facturas.pdf');

    Route::get('portal/catalogo', [PortalPedidoController::class, 'catalogo'])->name('portal.catalogo');
    Route::get('portal/pedidos', [PortalPedidoController::class, 'index'])->name('portal.pedidos');
    Route::post('portal/pedidos', [PortalPedidoController::class, 'store'])->name('portal.pedidos.store');
});

// Personal del taller
Route::middleware(['auth', 'personal'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('clientes', ClienteController::class);
    Route::resource('motocicletas', MotocicletaController::class);
    Route::resource('repuestos', RepuestoController::class)->except(['show']);
    Route::resource('servicios', ServicioController::class)->except(['show']);
    Route::resource('usuarios', UserController::class)->except(['show']);

    Route::get('calendario', [CalendarioController::class, 'index'])->name('calendario');

    Route::post('servicios/{servicio}/repuestos', [ServicioController::class, 'agregarRepuesto'])
        ->name('servicios.repuestos.store');
    Route::delete('servicios/{servicio}/repuestos/{detalle}', [ServicioController::class, 'quitarRepuesto'])
        ->name('servicios.repuestos.destroy');

    Route::resource('facturas', FacturaController::class)->except(['show']);

    Route::get('facturas/{factura}/pdf', [FacturaController::class, 'pdf'])->name('facturas.pdf');

    Route::get('pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
    Route::patch('pedidos/{pedido}', [PedidoController::class, 'update'])->name('pedidos.update');
});
