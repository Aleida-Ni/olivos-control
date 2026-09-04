<?php

namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use App\Models\Pedido;
use Illuminate\Http\Request;

class PagoCallCenterController extends Controller
{
    /**
     * Vista compartida para pagos del call center.
     */
    public function index(Request $request)
    {
        $pendientes = \App\Models\Despacho::where('estado', 'ENVIADO')->count();

        if ($request->query('origen') === 'despacho') {
            app(\App\Http\Controllers\DespachoController::class)->menuDespacho($pendientes);
        } else {
            app(\App\Http\Controllers\Tienda\TiendaController::class)->menuTienda($pendientes);
        }

    $pedidos = \App\Models\DetalleDespacho::with([
    'producto',
    'despacho'
])
    ->where('es_pedido', true)
    ->whereDate('created_at', today())
    ->orderByDesc('created_at')
    ->get();

        return view('pagos-call-center.index', compact('pedidos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pedido_id' => 'required|exists:pedidos,id',
            'monto' => 'required|numeric|min:0',
            'metodo_pago' => 'required|string|max:50',
            'observacion' => 'nullable|string|max:500',
        ]);

        $pedido = Pedido::findOrFail($request->pedido_id);

        $pago = Pago::create([
            'pedido_id' => $pedido->id,
            'monto' => $request->monto,
            'metodo_pago' => $request->metodo_pago,
            'estado' => 'PAGADO',
            'usuario_id' => auth()->id(),
            'observacion' => $request->observacion,
            'fecha_pago' => now(),
        ]);

        $totalPagado = (float) $pedido->pagos()->sum('monto');
        $pedido->update([
            'saldo' => max(0, (float) $pedido->total - $totalPagado),
            'estado' => $pedido->total <= $totalPagado ? 'PAGADO' : 'PARCIAL',
        ]);

        return redirect()->route('pagos-call-center.index')
            ->with('success', 'Pago registrado correctamente.');
    }
}
