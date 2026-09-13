<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $pedidos = Pedido::with('producto')
            ->when($request->filled('tipo_entrega'), function ($query) use ($request) {
                $query->whereRaw('UPPER(tipo_entrega) = ?', [strtoupper((string) $request->string('tipo_entrega'))]);
            })
            ->when($request->boolean('hoy'), fn ($query) => $query->whereDate('created_at', today()))
            ->latest()
            ->get();

        return response()->json(['data' => $pedidos]);
    }

    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'numero_pedido' => ['nullable', 'string', 'max:255'],
            'cliente' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'tipo_entrega' => ['required', 'string', 'max:50'],
            'producto_id' => ['nullable', 'integer', 'exists:productos,id'],
            'cantidad' => ['nullable', 'integer', 'min:1'],
            'precio_unitario' => ['nullable', 'numeric', 'min:0'],
            'total' => ['nullable', 'numeric', 'min:0'],
            'saldo' => ['nullable', 'numeric', 'min:0'],
            'estado' => ['nullable', 'string', 'max:50'],
            'observacion' => ['nullable', 'string'],
        ]);

        $datos['tipo_entrega'] = strtoupper($datos['tipo_entrega']);
        $datos['cantidad'] ??= 1;
        $datos['estado'] ??= 'PENDIENTE';

        $pedido = Pedido::create($datos);

        return response()->json(['data' => $pedido->load('producto')], 201);
    }
}
