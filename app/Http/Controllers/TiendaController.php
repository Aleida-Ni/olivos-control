<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Despacho;
use App\Models\Inventario;
use App\Models\MovimientoInventario;
use Illuminate\Support\Facades\DB;

class TiendaController extends Controller
{
    /**
     * Menú exclusivo de tienda.
     */
    private function menuTienda($pendientes = 0)
    {
        config([
            'adminlte.menu' => [

                // =========================
                // PRINCIPAL
                // =========================

                [
                    'text' => 'Inicio',
                    'route' => 'tienda.index',
                    'icon' => 'fas fa-fw fa-home',
                ],

                [
                    'text' => 'Recibir Productos',
                    'route' => 'tienda.recibir',
                    'icon' => 'fas fa-fw fa-box-open',
                    'label' => $pendientes > 0 ? $pendientes : null,
                    'label_color' => 'danger',
                ],

                [
                    'text' => 'Inventario',
                    'route' => 'tienda.inventario',
                    'icon' => 'fas fa-fw fa-boxes',
                ],

                [
                    'text' => 'Historial',
                    'route' => 'tienda.historial',
                    'icon' => 'fas fa-fw fa-clipboard-list',
                ],


                // =========================
                // TIENDA
                // =========================

                [
                    'header' => 'TIENDA',
                ],

                [
                    'text' => 'Ventas',
                    'route' => 'tienda.ventas',
                    'icon' => 'fas fa-fw fa-shopping-cart',
                ],

                [
                    'text' => 'Caja',
                    'route' => 'tienda.caja',
                    'icon' => 'fas fa-fw fa-cash-register',
                ],

                [
                    'text' => 'Cierres',
                    'route' => 'tienda.cierres',
                    'icon' => 'fas fa-fw fa-file-invoice-dollar',
                ],

            ],
        ]);
    }


    /**
     * INICIO
     */
    public function index()
    {
        $pendientes = Despacho::where('estado', 'ENVIADO')->count();

        $this->menuTienda($pendientes);

        return view('tienda.index');
    }


    /**
     * INVENTARIO
     */
    public function inventario()
    {
        $pendientes = Despacho::where('estado', 'ENVIADO')->count();

        $this->menuTienda($pendientes);

        $productos = Producto::where('activo', true)
            ->with([
                'categoria',
                'inventario'
            ])
            ->orderBy('nombre')
            ->get();

        return view(
            'tienda.inventario',
            compact('productos')
        );
    }


    /**
     * RECIBIR PRODUCTOS
     */
    public function recibir()
    {
        $despachos = Despacho::with([
            'chofer',
            'detalles.producto'
        ])
        ->where('estado', 'ENVIADO')
        ->orderBy('created_at', 'desc')
        ->get();

        $pendientes = $despachos->count();

        $this->menuTienda($pendientes);

        return view(
            'tienda.recibir',
            compact('despachos')
        );
    }


    /**
     * CONFIRMAR RECEPCIÓN
     */
    public function confirmarRecepcion(Despacho $despacho)
    {
        if ($despacho->estado !== 'ENVIADO') {
            return redirect()
                ->route('tienda.recibir')
                ->with('error', 'Este despacho ya fue recibido.');
        }

        DB::transaction(function () use ($despacho) {
            $despacho->load('detalles');

            foreach ($despacho->detalles as $detalle) {
                $inventario = Inventario::firstOrCreate(
                    ['producto_id' => $detalle->producto_id],
                    ['stock' => 0]
                );

                $inventario->increment('stock', $detalle->cantidad);

                MovimientoInventario::create([
                    'producto_id' => $detalle->producto_id,
                    'tipo' => 'Ingreso',
                    'cantidad' => $detalle->cantidad,
                    'referencia' => 'Despacho #' . $despacho->id,
                    'observacion' => 'Recepción de productos en tienda.',
                ]);
            }

            $despacho->update([
                'estado' => 'RECIBIDO',
                'confirmado_en' => now(),
            ]);
        });

        return redirect()
            ->route('tienda.recibir')
            ->with(
                'success',
                'Productos recibidos correctamente.'
            );
    }


    /**
     * HISTORIAL
     */
    public function historial()
    {
        $pendientes = Despacho::where('estado', 'ENVIADO')->count();

        $this->menuTienda($pendientes);

        $despachos = Despacho::with([
            'chofer',
            'detalles.producto',
        ])
            ->where('estado', 'RECIBIDO')
            ->whereDate('confirmado_en', today())
            ->orderByDesc('confirmado_en')
            ->get();

        return view('tienda.historial', compact('despachos'));
    }


    /**
     * VENTAS
     */
    public function ventas()
    {
        $pendientes = Despacho::where('estado', 'ENVIADO')->count();

        $this->menuTienda($pendientes);

        return view('tienda.ventas');
    }


    /**
     * CAJA
     */
    public function caja()
    {
        $pendientes = Despacho::where('estado', 'ENVIADO')->count();

        $this->menuTienda($pendientes);

        return view('tienda.caja');
    }


    /**
     * CIERRES
     */
    public function cierres()
    {
        $pendientes = Despacho::where('estado', 'ENVIADO')->count();

        $this->menuTienda($pendientes);

        return view('tienda.cierres');
    }
}