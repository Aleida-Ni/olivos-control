<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Despacho;
class TiendaController extends Controller
{
    /**
     * Menú exclusivo de tienda.
     */
    private function menuTienda()
    {
        config([
            'adminlte.menu' => [

                [
                    'text' => 'Inicio',
                    'route' => 'tienda.index',
                    'icon' => 'fas fa-fw fa-home',
                ],

                [
    'text' => 'Recibir Productos',
    'route' => 'tienda.recibir',
    'icon' => 'fas fa-fw fa-box-open',
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


    public function index()
    {
        $this->menuTienda();

        return view('tienda.index');
    }


public function inventario()
{
    $this->menuTienda();

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

public function recibir()
{
    $this->menuTienda();

    $despachos = \App\Models\Despacho::with([
        'chofer',
        'detalles.producto'
    ])
    ->where('estado', 'ENVIADO')
    ->orderBy('created_at', 'desc')
    ->get();

    return view('tienda.recibir', compact('despachos'));
}

    public function historial()
    {
        $this->menuTienda();

        return view('tienda.historial');
    }


    public function ventas()
    {
        $this->menuTienda();

        return view('tienda.ventas');
    }


    public function caja()
    {
        $this->menuTienda();

        return view('tienda.caja');
    }


    public function cierres()
    {
        $this->menuTienda();

        return view('tienda.cierres');
    }

    /**
 * Mostrar productos pendientes de recibir.
 */
public function recibirProductos()
{
    $this->menuTienda();

    $despachos = Despacho::with([
        'chofer',
        'detalles.producto'
    ])
    ->where('estado', 'ENVIADO')
    ->orderBy('created_at', 'desc')
    ->get();

    return view(
        'tienda.recibir',
        compact('despachos')
    );
}


/**
 * Confirmar recepción de un despacho.
 */
public function confirmarRecepcion(Despacho $despacho)
{
    $despacho->update([
        'estado' => 'RECIBIDO',
        'confirmado_en' => now(),
    ]);

    return redirect()
        ->route('tienda.recibir')
        ->with(
            'success',
            'Productos recibidos correctamente.'
        );
}
}