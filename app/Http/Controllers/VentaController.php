<?php

namespace App\Http\Controllers;

use App\Models\Despacho;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VentaController extends Controller
{
    /**
     * Pantalla principal de ventas.
     */
    public function index()
    {
        $pendientes = Despacho::where('estado', 'ENVIADO')->count();

        app(TiendaController::class)->menuTienda($pendientes);

        $productos = Producto::where('activo', true)
            ->with(['categoria', 'inventario'])
            ->orderBy('nombre')
            ->get();

        return view(
            'tienda.ventas.index',
            compact('productos')
        );
    }

    /**
     * Registra una venta y descuenta el inventario disponible.
     */
    public function store(Request $request)
    {
        $usuario = $request->user() ?: User::query()->first();

        if (!$usuario) {
            throw ValidationException::withMessages([
                'venta' => 'Debe existir al menos un usuario cajero para registrar ventas.',
            ]);
        }

        $datos = $request->validate([
            'nit_ci' => ['nullable', 'string', 'max:50'],
            'cliente' => ['nullable', 'string', 'max:120'],
            'metodo_pago' => ['required', 'in:EFECTIVO,TARJETA,QR'],
            'efectivo_recibido' => ['nullable', 'numeric', 'min:0'],
            'descuento_porcentaje' => ['required', 'numeric', 'min:0', 'max:100'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer', 'distinct', 'exists:productos,id'],
            'items.*.cantidad' => ['required', 'integer', 'min:1'],
            'items.*.precio' => ['required', 'numeric', 'min:0'],
        ]);

        $venta = DB::transaction(function () use ($datos, $usuario) {
            $productos = Producto::whereIn(
                'id',
                collect($datos['items'])->pluck('id')
            )->with('inventario')->lockForUpdate()->get()->keyBy('id');

            $subtotal = 0;
            $detalles = [];

            foreach ($datos['items'] as $item) {
                $producto = $productos->get($item['id']);
                $stock = (int) $producto->inventario->sum('stock');

                if ($stock < $item['cantidad']) {
                    throw ValidationException::withMessages([
                        'items' => "No hay stock suficiente de {$producto->nombre}.",
                    ]);
                }

                $precio = (float) $item['precio'];
                $linea = round($precio * $item['cantidad'], 2);
                $subtotal += $linea;

                $detalles[] = [
                    'producto_id' => $producto->id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $precio,
                    'subtotal' => $linea,
                ];
            }

            $descuentoPorcentaje = (float) $datos['descuento_porcentaje'];
            $descuento = round($subtotal * ($descuentoPorcentaje / 100), 2);
            $total = round($subtotal - $descuento, 2);
            $efectivo = $datos['metodo_pago'] === 'EFECTIVO'
                ? (float) ($datos['efectivo_recibido'] ?? 0)
                : null;

            if ($datos['metodo_pago'] === 'EFECTIVO' && $efectivo < $total) {
                throw ValidationException::withMessages([
                    'efectivo_recibido' => 'El efectivo recibido no alcanza para cubrir la venta.',
                ]);
            }

            $venta = Venta::create([
                'user_id' => $usuario->id,
                'nit_ci' => $datos['nit_ci'] ?: null,
                'cliente' => $datos['cliente'] ?: null,
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'total' => $total,
                'metodo_pago' => $datos['metodo_pago'],
                'efectivo_recibido' => $efectivo,
                'cambio' => $efectivo === null ? 0 : round($efectivo - $total, 2),
                'estado' => 'COMPLETADA',
            ]);

            foreach ($detalles as $detalle) {
                $venta->detalles()->create($detalle);
                $restante = $detalle['cantidad'];

                foreach ($productos->get($detalle['producto_id'])->inventario as $inventario) {
                    if ($restante === 0) {
                        break;
                    }

                    $descontar = min($restante, $inventario->stock);
                    $inventario->decrement('stock', $descontar);
                    $restante -= $descontar;
                }
            }

            return $venta->load('detalles.producto');
        });

        $venta->descuento_porcentaje = (float) $datos['descuento_porcentaje'];

        return response()->json([
            'message' => 'Venta registrada correctamente.',
            'venta' => $venta,
        ]);
    }
}