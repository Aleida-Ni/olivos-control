<?php

namespace App\Http\Controllers;

use App\Models\Despacho;
use App\Models\Chofer;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DespachoController extends Controller
{
    /**
     * Lista principal de despachos.
     */
    public function index()
    {
        $choferes = Chofer::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $productos = Producto::where('activo', true)
            ->with('categoria')
            ->orderBy('nombre')
            ->get();

        $pendientes = Despacho::where('estado', 'ENVIADO')->count();

        $this->menuDespacho($pendientes);

        return view('despachos.index', compact(
            'choferes',
            'productos',
            'pendientes'
        ));
    }

    /**
     * Inicio del módulo de despacho.
     */
    public function inicio()
    {
        $pendientes = Despacho::where('estado', 'ENVIADO')->count();
        $recibidos = Despacho::where('estado', 'RECIBIDO')->count();

        $this->menuDespacho($pendientes);

        return view('despachos.inicio', compact(
            'pendientes',
            'recibidos'
        ));
    }


    /**
     * Menú exclusivo de despacho.
     */
    public function menuDespacho($pendientes = 0)
    {
        config([
            'adminlte.menu' => [
                [
                    'text' => 'Inicio',
                    'route' => 'despachos.inicio',
                    'icon' => 'fas fa-fw fa-home',
                ],
                [
                    'text' => 'Despachar',
                    'route' => 'despachos.create',
                    'icon' => 'fas fa-fw fa-truck',
                ],
                [
                    'text' => 'Pedidos',
                    'url' => route('tienda.pedidos', ['origen' => 'despacho']),
                    'icon' => 'fas fa-fw fa-clipboard-list',
                ],
                [
                    'text' => 'PAGOS CALL CENTER',
                    'url' => route('tienda.pedidos', ['origen' => 'despacho']),
                    'icon' => 'fas fa-fw fa-phone-alt',
                ],
                [
                    'text' => 'Historial de Despachos',
                    'url' => '#',
                    'icon' => 'fas fa-fw fa-history',
                    'submenu' => [
                        [
                            'text' => 'Pendientes',
                            'route' => 'despachos.pendientes',
                            'icon' => 'fas fa-fw fa-clock',
                            'label' => $pendientes > 0 ? $pendientes : null,
                            'label_color' => 'danger',
                        ],
                        [
                            'text' => 'Recibidos',
                            'route' => 'despachos.recibidos',
                            'icon' => 'fas fa-fw fa-check-circle',
                        ],
                    ],
                ],
                [
                    'text' => 'Inventario',
                    'url' => '#',
                    'icon' => 'fas fa-fw fa-boxes',
                ],
                [
                    'header' => 'ADMINISTRACIÓN',
                ],
                [
                    'text' => 'Productos',
                    'route' => 'productos.index',
                    'icon' => 'fas fa-fw fa-shopping-basket',
                ],
                [
                    'text' => 'Tienda',
                    'route' => 'tienda.index',
                    'icon' => 'fas fa-fw fa-store',
                ],
            ],
        ]);
    }


    /**
     * Formulario para crear despacho.
     */
    public function create()
    {
        $choferes = Chofer::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $productos = Producto::where('activo', true)
            ->with('categoria')
            ->orderBy('nombre')
            ->get();

        $pendientes = Despacho::where('estado', 'ENVIADO')->count();

        $this->menuDespacho($pendientes);

        return view('despachos.index', compact(
            'choferes',
            'productos',
            'pendientes'
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

            'productos.*' => 'integer|min:1',

            'pedidos' => 'nullable|array',

            'observacion' => 'nullable|string|max:1000',
        ]);


        if (
            empty($request->productos) &&
            empty($request->pedidos)
        ) {
            return back()
                ->withErrors([
                    'productos' => 'Debes agregar al menos un producto o un pedido para enviar a tienda.',
                ])
                ->withInput();
        }

        try {
            DB::transaction(function () use ($request) {

                /*
                 * CREAR CABECERA DEL DESPACHO
                 */
                $despacho = Despacho::create([

                    'chofer_id' => $request->chofer_id,

                    'fecha' => now()->toDateString(),

                    'estado' => 'ENVIADO',

                    'observacion' => $request->observacion,

                ]);


                /*
                 * PRODUCTOS NORMALES
                 */
                if ($request->filled('productos')) {

                    foreach ($request->productos as $productoId => $cantidad) {

                        if ((int) $cantidad <= 0) {
                            continue;
                        }

                        $producto = Producto::findOrFail($productoId);

                        $despacho->detalles()->create([

                            'producto_id' => $producto->id,

                            'cantidad' => $cantidad,

                            'es_pedido' => false,

                            'numero_pedido' => null,

                            'cliente' => null,

                        ]);
                    }
                }


                /*
                 * PEDIDOS
                 */
                if ($request->filled('pedidos')) {

                    foreach ($request->pedidos as $pedido) {

                        if (
                            empty($pedido['producto_id']) ||
                            empty($pedido['cantidad'])
                        ) {
                            continue;
                        }

                        $producto = Producto::findOrFail(
                            $pedido['producto_id']
                        );

                        $despacho->detalles()->create([

                            'producto_id' => $producto->id,

                            'cantidad' => $pedido['cantidad'],

                            'es_pedido' => true,

                            'numero_pedido' =>
                                $pedido['numero_pedido'] ?? null,

                            'cliente' =>
                                $pedido['cliente'] ?? null,

                        ]);
                    }
                }

            });

        } catch (\Exception $e) {

            return back()
                ->withErrors([
                    'error' =>
                        'No se pudo guardar el despacho: ' .
                        $e->getMessage()
                ])
                ->withInput();
        }


        return redirect()
            ->route('despachos.pendientes')
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
        $despachos = Despacho::with([
            'chofer',
            'detalles.producto'
        ])
            ->where('estado', 'ENVIADO')
            ->orderBy('created_at', 'desc')
            ->get();

        $pendientes = $despachos->count();

        $this->menuDespacho($pendientes);

        return view(
            'despachos.pendientes',
            compact('despachos')
        );
    }


    /**
     * Historial de despachos recibidos.
     */
    public function recibidos(Request $request)
    {
        $query = Despacho::with([
            'chofer',
            'detalles.producto'
        ])
            ->where('estado', 'RECIBIDO');


        /*
         * FILTRO POR FECHA
         */
        if ($request->filled('fecha')) {

            $query->whereDate(
                'confirmado_en',
                $request->fecha
            );
        }


        /*
         * POR DEFECTO MOSTRAR LOS MÁS RECIENTES
         */
        $despachos = $query
            ->orderBy('confirmado_en', 'desc')
            ->get();


        $pendientes = Despacho::where(
            'estado',
            'ENVIADO'
        )->count();

        $this->menuDespacho($pendientes);

        return view(
            'despachos.recibidos',
            compact('despachos')
        );
    }


}