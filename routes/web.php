<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\DespachoController;
use App\Http\Controllers\TiendaController;


// =====================================================
// INICIO
// =====================================================

Route::get('/', [DashboardController::class, 'index'])
    ->name('dashboard');


// =====================================================
// PRODUCTOS
// =====================================================

Route::resource('productos', ProductoController::class);


// =====================================================
// DESPACHOS
// =====================================================

Route::resource('despachos', DespachoController::class);

Route::get('/pendientes', [DespachoController::class, 'pendientes'])
    ->name('pendientes');

Route::patch('/pendientes/{despacho}/recibir', [DespachoController::class, 'recibir'])
    ->name('pendientes.recibir');


// =====================================================
// TIENDA
// =====================================================


Route::prefix('tienda')->group(function () {

    Route::get('/', [TiendaController::class, 'index'])
        ->name('tienda.index');

    Route::get('/inventario', [TiendaController::class, 'inventario'])
        ->name('tienda.inventario');

});

Route::get('/tienda/recibir', [TiendaController::class, 'recibir'])
    ->name('tienda.recibir');

Route::get('/tienda/inventario', [TiendaController::class, 'inventario'])
    ->name('tienda.inventario');

Route::get('/tienda/historial', [TiendaController::class, 'historial'])
    ->name('tienda.historial');

Route::get('/tienda/ventas', [TiendaController::class, 'ventas'])
    ->name('tienda.ventas');

Route::get('/tienda/caja', [TiendaController::class, 'caja'])
    ->name('tienda.caja');

Route::get('/tienda/cierres', [TiendaController::class, 'cierres'])
    ->name('tienda.cierres');

    Route::get('/tienda/recibir-productos', [TiendaController::class, 'recibirProductos'])
    ->name('tienda.recibir');

Route::patch('/tienda/recibir-productos/{despacho}', [TiendaController::class, 'confirmarRecepcion'])
    ->name('tienda.recibir.confirmar');