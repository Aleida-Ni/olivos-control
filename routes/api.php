<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PedidoController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// =====================================================
// PEDIDOS
// =====================================================

// Lista de pedidos
Route::get('/pedidos', [PedidoController::class, 'index']);

// Crear pedido
Route::post('/pedidos', [PedidoController::class, 'store']);

// Buscar pedido RECOGERA por número
Route::get('/pedidos/recogera/{numero}', [
    PedidoController::class,
    'buscarRecogera'
]);

// Enviar pedido a tienda
Route::post('/pedidos/{pedido}/enviar-tienda', [
    PedidoController::class,
    'enviarATienda'
]);

// Pedidos que están esperando ser recibidos en tienda
Route::get('/pedidos/tienda', [
    PedidoController::class,
    'pedidosTienda'
]);

// Confirmar que el pedido llegó a tienda
Route::patch('/pedidos/{pedido}/recibir', [
    PedidoController::class,
    'recibir'
]);