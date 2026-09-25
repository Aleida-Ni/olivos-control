<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\PedidoExcelService;

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
        /**
     * Buscar un pedido RECOGERA por número.
     */
public function buscarRecogera(
    string $numero,
    PedidoExcelService $excel
): JsonResponse {

    $pedidos = $excel->recogeraDeHoy($numero);

    if (empty($pedidos)) {
        return response()->json([
            'message' => 'No se encontró un pedido RECOGERA con ese número para hoy.'
        ], 404);
    }

    return response()->json([
        'data' => $pedidos[0]
    ]);
}

    /**
     * Enviar pedido a tienda.
     */
    public function enviarATienda(Pedido $pedido): JsonResponse
    {
        if (strtoupper($pedido->tipo_entrega) !== 'RECOGERA') {
            return response()->json([
                'message' => 'El pedido no es RECOGERA.'
            ], 422);
        }

        if ($pedido->estado === 'ENVIADO') {
            return response()->json([
                'message' => 'El pedido ya fue enviado a tienda.'
            ], 422);
        }

        $pedido->update([
            'estado' => 'ENVIADO'
        ]);

        return response()->json([
            'message' => 'Pedido enviado a tienda correctamente.',
            'data' => $pedido->fresh()->load('producto')
        ]);
    }


    /**
     * Lista de pedidos enviados a tienda.
     */
    public function pedidosTienda(): JsonResponse
    {
        $pedidos = Pedido::with([
            'producto',
            'despacho.chofer'
        ])
            ->where('estado', 'ENVIADO')
            ->whereRaw('UPPER(tipo_entrega) = ?', ['RECOGERA'])
            ->latest()
            ->get();

        return response()->json([
            'data' => $pedidos
        ]);
    }


    /**
     * Confirmar recepción del pedido en tienda.
     */
    public function recibir(Pedido $pedido): JsonResponse
    {
        if ($pedido->estado !== 'ENVIADO') {
            return response()->json([
                'message' => 'El pedido no está pendiente de recepción.'
            ], 422);
        }

        $pedido->update([
            'estado' => 'RECIBIDO'
        ]);

        return response()->json([
            'message' => 'Pedido recibido correctamente en tienda.',
            'data' => $pedido->fresh()->load('producto')
        ]);
    }
}
