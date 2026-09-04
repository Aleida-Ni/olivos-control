<?php

namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;
use App\Models\Despacho;
use App\Models\Inventario;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TiendaController extends Controller
{
    /**
     * Menú exclusivo de tienda.
     */
    public function menuTienda($pendientes = 0)
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
                [
                    'text' => 'Pedidos',
                    'route' => 'tienda.pedidos',
                    'icon' => 'fas fa-fw fa-clipboard-list',
                ],
                [
                    'text' => 'Pagos Call Center',
                    'url' => route('pagos-call-center.index', ['origen' => 'tienda']),
                    'icon' => 'fas fa-fw fa-phone-alt',
                ],
                [
                    'header' => 'TIENDA',
                ],
                [
                    'text' => 'Ventas',
                    'icon' => 'fas fa-fw fa-shopping-cart',
                    'submenu' => [
                        [
                            'text' => 'Nueva venta',
                            'route' => 'tienda.ventas',
                            'icon' => 'fas fa-fw fa-cart-plus',
                        ],
                        [
                            'text' => 'Ventas realizadas',
                            'route' => 'tienda.ventas.realizadas',
                            'icon' => 'fas fa-fw fa-receipt',
                        ],
                    ],
                ],
                [
                    'text' => 'Cajeros',
                    'route' => 'cajeros.index',
                    'icon' => 'fas fa-fw fa-users',
                ],
                [
                    'text' => 'Caja',
                    'icon' => 'fas fa-fw fa-cash-register',
                    'submenu' => [
                        [
                            'text' => 'Mi turno',
                            'route' => 'tienda.caja',
                            'icon' => 'fas fa-fw fa-user-clock',
                        ],
                        [
                            'text' => 'Ingresos',
                            'route' => 'tienda.caja.ingresos',
                            'icon' => 'fas fa-fw fa-arrow-down',
                        ],
                        [
                            'text' => 'Egresos',
                            'route' => 'tienda.caja.egresos',
                            'icon' => 'fas fa-fw fa-arrow-up',
                        ],
                    ],
                ],
                [
                    'text' => 'Cierres',
                    'icon' => 'fas fa-fw fa-file-invoice-dollar',
                    'submenu' => [
                        [
                            'text' => 'Cerrar turno',
                            'route' => 'tienda.cierres',
                            'icon' => 'fas fa-fw fa-lock',
                        ],
                        [
                            'text' => 'Historial de cierres',
                            'route' => 'tienda.cierres.historial',
                            'icon' => 'fas fa-fw fa-history',
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function index()
    {
        $pendientes = Despacho::where('estado', 'ENVIADO')->count();
        $this->menuTienda($pendientes);

        return view('tienda.index');
    }

    public function inventario()
    {
        $pendientes = Despacho::where('estado', 'ENVIADO')->count();
        $this->menuTienda($pendientes);

        $productos = Producto::where('activo', true)
            ->with(['categoria', 'inventario'])
            ->orderBy('nombre')
            ->get();

        return view('tienda.inventario', compact('productos'));
    }

    public function recibir()
    {
        $despachos = Despacho::with(['chofer', 'detalles.producto'])
            ->where('estado', 'ENVIADO')
            ->orderBy('created_at', 'desc')
            ->get();

        $pendientes = $despachos->count();
        $this->menuTienda($pendientes);

        return view('tienda.recibir', compact('despachos'));
    }

    public function confirmarRecepcion(Despacho $despacho)
    {
        if ($despacho->estado !== 'ENVIADO') {
            return redirect()->route('tienda.recibir')->with('error', 'Este despacho ya fue recibido.');
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

        return redirect()->route('tienda.historial')->with('success', 'Despacho recibido correctamente. El inventario fue actualizado.');
    }

    public function historial(Request $request)
    {
        $pendientes = Despacho::where('estado', 'ENVIADO')->count();
        $this->menuTienda($pendientes);

        $query = Despacho::with(['chofer', 'detalles.producto'])
            ->where('estado', 'RECIBIDO');

        if ($request->filled('fecha')) {
            $query->whereDate('confirmado_en', $request->fecha);
        }

        $despachos = $query->orderBy('confirmado_en', 'desc')->get();

        return view('tienda.historial', compact('despachos'));
    }

    public function ventas()
    {
        $pendientes = Despacho::where('estado', 'ENVIADO')->count();
        $this->menuTienda($pendientes);

        return view('tienda.ventas');
    }

    public function pedidos()
    {
        $pendientes = Despacho::where('estado', 'ENVIADO')->count();

        if (request()->query('origen') === 'despacho') {
            $despachoController = new \App\Http\Controllers\DespachoController();
            $despachoController->menuDespacho($pendientes);
        } else {
            $this->menuTienda($pendientes);
        }

        $detalleDespachos = \App\Models\DetalleDespacho::with(['despacho', 'producto'])
            ->where('es_pedido', true)
            ->whereDate('created_at', today())
            ->orderByDesc('created_at')
            ->get();

        $pedidosExcel = $this->pedidosDelExcel();

        return view('tienda.pedidos', compact('detalleDespachos', 'pedidosExcel'));
    }

    private function pedidosDelExcel(): array
    {
        $url = config('services.pedidos_excel.url');

        if (!$url) {
            return [];
        }

        try {
            $response = Http::timeout(10)->get($url);

            if (!$response->successful()) {
                return [];
            }

            $lines = preg_split('/\r\n|\n|\r/', trim($response->body()));
            $headers = array_map(fn ($header) => $this->normalizarTexto($header), str_getcsv(array_shift($lines)));
            $pedidos = [];

            foreach ($lines as $line) {
                if (trim($line) === '') {
                    continue;
                }

                $values = str_getcsv($line);
                $row = [];

                foreach ($headers as $index => $header) {
                    $row[$header] = trim($values[$index] ?? '');
                }

                $fecha = $this->valorColumna($row, ['fecha de entrega', 'fecha', 'dia']);
                $textoFila = $this->normalizarTexto(implode(' ', $values));

                if (!$this->esFechaDeHoy($fecha) || !str_contains($textoFila, 'recoger')) {
                    continue;
                }

                $pedidos[] = [
                    'numero' => $this->valorColumna($row, ['n', 'n°', 'numero', 'numero de pedido', 'pedido']),
                    'fecha' => $fecha,
                    'cliente' => $this->valorColumna($row, ['nombre', 'cliente']),
                    'transferencia' => $this->valorColumna($row, ['transferencia', 'forma de pago', 'metodo de pago']),
                    'saldo' => $this->valorColumna($row, ['saldo', 'restante', 'pendiente']),
                    'fila' => $row,
                ];
            }

            return $pedidos;
        } catch (\Throwable $exception) {
            report($exception);
            return [];
        }
    }

    private function valorColumna(array $row, array $nombres): string
    {
        foreach ($nombres as $nombre) {
            $normalizado = $this->normalizarTexto($nombre);

            if (isset($row[$normalizado]) && $row[$normalizado] !== '') {
                return $row[$normalizado];
            }
        }

        return '';
    }

    private function normalizarTexto(string $texto): string
    {
        $texto = mb_strtolower(trim($texto));
        return strtr($texto, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ñ' => 'n']);
    }

    private function esFechaDeHoy(string $fecha): bool
    {
        foreach (['d/m/Y', 'd-m-Y', 'Y-m-d', 'd/m/Y H:i', 'Y-m-d H:i:s'] as $formato) {
            try {
                if (Carbon::createFromFormat($formato, trim($fecha))->isToday()) {
                    return true;
                }
            } catch (\Throwable $exception) {
                continue;
            }
        }

        return false;
    }

    public function cobrarPedido(\App\Models\DetalleDespacho $detalleDespacho, Request $request)
    {
        $request->validate([
            'monto' => ['required', 'numeric', 'min:0'],
        ]);

        $monto = (float) $request->monto;
        $totalPedido = (float) ($detalleDespacho->precio_unitario * $detalleDespacho->cantidad);

        $nuevoMontoPagado = min($monto + (float) ($detalleDespacho->monto_pagado ?? 0), $totalPedido);
        $saldo = round($totalPedido - $nuevoMontoPagado, 2);

        $detalleDespacho->update([
            'monto_pagado' => round($nuevoMontoPagado, 2),
            'saldo' => round($saldo, 2),
            'estado' => $saldo <= 0 ? 'PAGADO' : 'PARCIAL',
        ]);

        return redirect()->route('tienda.pedidos', ['origen' => 'despacho'])->with('success', 'Cobro registrado correctamente.');
    }

    public function recibirPedidoInventario(\App\Models\DetalleDespacho $detalleDespacho)
    {
        if ($detalleDespacho->producto_id === null) {
            return back()->with('error', 'No hay producto asociado al pedido.');
        }

        $inventario = Inventario::firstOrCreate(
            ['producto_id' => $detalleDespacho->producto_id],
            ['stock' => 0]
        );

        $inventario->increment('stock', $detalleDespacho->cantidad);

        MovimientoInventario::create([
            'producto_id' => $detalleDespacho->producto_id,
            'tipo' => 'Ingreso',
            'cantidad' => $detalleDespacho->cantidad,
            'referencia' => 'Pedido #' . $detalleDespacho->id,
            'observacion' => 'Pedido recibido en tienda desde despacho.',
        ]);

        $detalleDespacho->update([
            'estado' => 'EN_TIENDA',
        ]);

        return redirect()->route('tienda.pedidos')->with('success', 'Pedido recibido en inventario correctamente.');
    }

    public function caja()
    {
        $pendientes = Despacho::where('estado', 'ENVIADO')->count();
        $this->menuTienda($pendientes);

        return view('tienda.caja');
    }

    public function ingresos()
    {
        $pendientes = Despacho::where('estado', 'ENVIADO')->count();
        $this->menuTienda($pendientes);

        return view('tienda.caja-ingresos');
    }

    public function egresos()
    {
        $pendientes = Despacho::where('estado', 'ENVIADO')->count();
        $this->menuTienda($pendientes);

        return view('tienda.caja-egresos');
    }

    public function cierres()
    {
        $pendientes = Despacho::where('estado', 'ENVIADO')->count();
        $this->menuTienda($pendientes);

        return view('tienda.cierres');
    }

    public function historialCierres()
    {
        $pendientes = Despacho::where('estado', 'ENVIADO')->count();
        $this->menuTienda($pendientes);

        return view('tienda.historial-cierres');
    }

    public function ventasRealizadas()
    {
        $pendientes = Despacho::where('estado', 'ENVIADO')->count();
        $this->menuTienda($pendientes);

        $ventas = \App\Models\Venta::with(['detalles.producto', 'cajero'])
            ->orderByDesc('created_at')
            ->get();

        return view('tienda.ventas-realizadas', compact('ventas'));
    }
}
