<?php

namespace App\Http\Controllers;

use App\Models\Despacho;
use App\Models\Chofer;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;

class DespachoController extends Controller
{
    /**
     * Menú del área de despacho.
     */
    private function menuDespacho()
    {
        config([
            'adminlte.menu' => [

                [
                    'text' => 'Inicio',
                    'route' => 'dashboard',
                    'icon' => 'fas fa-fw fa-home',
                ],

                [
                    'text' => 'Despachar',
                    'route' => 'despachos.index',
                    'icon' => 'fas fa-fw fa-truck',
                ],

                [
                    'text' => 'Inventario',
                    'url' => '#',
                    'icon' => 'fas fa-fw fa-boxes',
                ],

                [
                    'text' => 'Historial',
                    'url' => '#',
                    'icon' => 'fas fa-fw fa-clipboard-list',
                ],

                [
                    'header' => 'ADMINISTRACIÓN',
                ],

                [
                    'text' => 'Productos',
                    'route' => 'productos.index',
                    'icon' => 'fas fa-fw fa-shopping-bag',
                ],

            ],
        ]);
    }


    /**
     * Mostrar despachos.
     */
    public function index()
    {
        $this->menuDespacho();

        $choferes = Chofer::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $productos = Producto::where('activo', true)
            ->with('categoria')
            ->orderBy('nombre')
            ->get();

        return view('despachos.index', compact(
            'choferes',
            'productos'
        ));
    }


    /**
     * Crear despacho.
     */
    public function create()
    {
        $this->menuDespacho();

        $choferes = Chofer::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $productos = Producto::where('activo', true)
            ->with('categoria')
            ->orderBy('nombre')
            ->get();

        return view('despachos.create', compact(
            'choferes',
            'productos'
        ));
    }


    /**
     * Guardar despacho.
     */
    public function store(Request $request)
    {
        $request->validate([
            'chofer_id' => 'required|exists:choferes,id',
            'productos' => 'nullable|array',
            'pedidos' => 'nullable|array',
        ]);

        if (
            empty($request->productos) &&
            empty($request->pedidos)
        ) {
            return back()
                ->withErrors([
                    'productos' => 'Debes agregar al menos un producto o pedido.'
                ])
                ->withInput();
        }

        $despacho = Despacho::create([
            'chofer_id' => $request->chofer_id,
            'fecha' => now()->toDateString(),
            'estado' => 'ENVIADO',
            'observacion' => $request->observacion,
        ]);

        // PRODUCTOS NORMALES
        if ($request->productos) {

            foreach ($request->productos as $productoId => $cantidad) {

                if ($cantidad > 0) {

                    $despacho->detalles()->create([
                        'producto_id' => $productoId,
                        'cantidad' => $cantidad,
                        'es_pedido' => false,
                        'numero_pedido' => null,
                        'cliente' => null,
                    ]);
                }
            }
        }

        // PEDIDOS
        if ($request->pedidos) {

            foreach ($request->pedidos as $pedido) {

                if (
                    !empty($pedido['producto_id']) &&
                    !empty($pedido['cantidad'])
                ) {

                    $despacho->detalles()->create([
                        'producto_id' => $pedido['producto_id'],
                        'cantidad' => $pedido['cantidad'],
                        'es_pedido' => true,
                        'numero_pedido' => $pedido['numero_pedido'] ?? null,
                        'cliente' => $pedido['cliente'] ?? null,
                    ]);
                }
            }
        }

        return redirect()
            ->route('pendientes')
            ->with(
                'success',
                'Despacho enviado correctamente. Ahora está pendiente de recepción.'
            );
    }


    /**
     * Despachos pendientes.
     */
    public function pendientes()
    {
        $this->menuDespacho();

        $despachos = Despacho::with([
            'chofer',
            'detalles.producto'
        ])
            ->where('estado', 'ENVIADO')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pendientes.index', compact('despachos'));
    }


    /**
     * Confirmar recepción.
     */
    public function recibir(Despacho $despacho)
    {
        $despacho->update([
            'estado' => 'RECIBIDO',
            'confirmado_en' => now(),
        ]);

        return redirect()
            ->route('pendientes')
            ->with(
                'success',
                'Despacho recibido correctamente.'
            );
    }
}