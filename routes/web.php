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

Route::resource('despachos', DespachoController::class)
    ->only([
        'index',
        'create',
        'store'
    ]);


// PENDIENTES

Route::get(
    '/despachos/pendientes',
    [DespachoController::class, 'pendientes']
)
    ->name('despachos.pendientes');


// RECIBIDOS

Route::get(
    '/despachos/recibidos',
    [DespachoController::class, 'recibidos']
)
    ->name('despachos.recibidos');


// CONFIRMAR RECEPCIÓN

Route::patch(
    '/despachos/{despacho}/recibir',
    [DespachoController::class, 'recibir']
)
    ->name('despachos.recibir');

// =====================================================
// TIENDA
// =====================================================

Route::prefix('tienda')->group(function () {

    // INICIO
    Route::get('/', [TiendaController::class, 'index'])
        ->name('tienda.index');


    // RECIBIR PRODUCTOS
    Route::get('/recibir', [TiendaController::class, 'recibir'])
        ->name('tienda.recibir');


    // CONFIRMAR RECEPCIÓN
    Route::patch(
        '/recibir/{despacho}',
        [TiendaController::class, 'confirmarRecepcion']
    )
        ->name('tienda.recibir.confirmar');


    // INVENTARIO
    Route::get('/inventario', [TiendaController::class, 'inventario'])
        ->name('tienda.inventario');


    // HISTORIAL
    Route::get('/historial', [TiendaController::class, 'historial'])
        ->name('tienda.historial');


    // VENTAS
    Route::get('/ventas', [TiendaController::class, 'ventas'])
        ->name('tienda.ventas');


    // CAJA
    Route::get('/caja', [TiendaController::class, 'caja'])
        ->name('tienda.caja');


    // CIERRES
    Route::get('/cierres', [TiendaController::class, 'cierres'])
        ->name('tienda.cierres');

});