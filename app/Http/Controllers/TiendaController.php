<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TiendaController extends Controller
{
    /**
     * Menú exclusivo de tienda.
     */
    private function menuTienda()
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
                    'url' => '#',
                    'icon' => 'fas fa-fw fa-box-open',
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


                // =========================
                // TIENDA
                // =========================

                [
                    'header' => 'TIENDA',
                ],

                [
                    'text' => 'Ventas',
                    'url' => '#',
                    'icon' => 'fas fa-fw fa-shopping-cart',
                ],

                [
                    'text' => 'Caja',
                    'url' => '#',
                    'icon' => 'fas fa-fw fa-cash-register',
                ],

                [
                    'text' => 'Cierres',
                    'url' => '#',
                    'icon' => 'fas fa-fw fa-file-invoice-dollar',
                ],

            ],
        ]);
    }


    /**
     * Inicio de tienda.
     */
    public function index()
    {
        $this->menuTienda();

        return view('tienda.index');
    }
        public function recibir()
    {
        $this->menuTienda();

        return view('tienda.recibir');
    }
}