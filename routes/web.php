<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\DespachoController;
use App\Http\Controllers\Tienda\TiendaController;
use App\Http\Controllers\CajeroController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\Tienda\PagoCallCenterController;


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


// INICIO DESPACHO
Route::get(
    '/despachos/inicio',
    [DespachoController::class, 'inicio']
)->name('despachos.inicio');


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
    Route::get('/ventas', [VentaController::class, 'index'])
        ->name('tienda.ventas');

    Route::get('/ventas/realizadas', [VentaController::class, 'realizadas'])
        ->name('tienda.ventas.realizadas');

    Route::get('/pedidos', [TiendaController::class, 'pedidos'])
        ->name('tienda.pedidos');

    Route::get('/pagos-call-center', [PagoCallCenterController::class, 'index'])
        ->name('pagos-call-center.index');

    Route::post('/pagos-call-center', [PagoCallCenterController::class, 'store'])
        ->name('pagos-call-center.store');

    Route::post('/pedidos/{detalleDespacho}/cobrar', [TiendaController::class, 'cobrarPedido'])
        ->name('tienda.pedidos.cobrar');

    Route::post('/pedidos/{detalleDespacho}/recibir-inventario', [TiendaController::class, 'recibirPedidoInventario'])
        ->name('tienda.pedidos.recibir-inventario');

    Route::post('/ventas', [VentaController::class, 'store'])
        ->name('tienda.ventas.store');

    // CAJA
    Route::get('/caja', [TiendaController::class, 'caja'])
        ->name('tienda.caja');

    Route::get('/caja/ingresos', [TiendaController::class, 'ingresos'])
        ->name('tienda.caja.ingresos');

    Route::get('/caja/egresos', [TiendaController::class, 'egresos'])
        ->name('tienda.caja.egresos');

    // CIERRES
    Route::get('/cierres', [TiendaController::class, 'cierres'])
        ->name('tienda.cierres');

    Route::get('/cierres/historial', [TiendaController::class, 'historialCierres'])
        ->name('tienda.cierres.historial');

    Route::resource('cajeros', CajeroController::class)
    ->only([
        'index',
        'create',
        'store',
    ]);

});