@extends('adminlte::page')

@section('title', 'Pedidos')

@section('content_header')
    <h1><i class="fas fa-clipboard-list"></i> Pedidos del día</h1>
@stop

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-file-excel mr-1"></i>
                Pedidos del Excel para recoger hoy
            </h3>
        </div>
        <div class="card-body p-0">
            @if(count($pedidosExcel) > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Número de pedido</th>
                                <th>Cliente</th>
                                <th>Fecha de entrega</th>
                                <th>Transferencia</th>
                                <th>Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pedidosExcel as $pedido)
                                <tr>
                                    <td><strong>{{ $pedido['numero'] ?: 'Sin número' }}</strong></td>
                                    <td>{{ $pedido['cliente'] ?: 'Sin nombre' }}</td>
                                    <td>{{ $pedido['fecha'] ?: 'Sin fecha' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $pedido['transferencia'] ? 'success' : 'warning' }}">
                                            {{ $pedido['transferencia'] ?: 'No indicado' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $pedido['saldo'] ? 'warning' : 'success' }}">
                                            {{ $pedido['saldo'] ?: 'Sin saldo pendiente' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center text-muted py-4">
                    <i class="fas fa-inbox fa-2x mb-2"></i>
                    <p class="mb-0">No hay pedidos para recoger hoy o el Excel no está disponible.</p>
                </div>
            @endif
        </div>
    </div>

    @if($detalleDespachos->count() > 0)
        <h3 class="mt-4 mb-3">Pedidos registrados en el sistema</h3>
        <div class="row">
            @foreach($detalleDespachos as $detalle)
                @php
                    $totalPedido = ($detalle->precio_unitario ?? 0) * $detalle->cantidad;
                    $saldo = (float) ($detalle->saldo ?? 0);
                    $pagado = (float) ($detalle->monto_pagado ?? 0);
                @endphp

                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm border-left-{{ $saldo > 0 ? 'warning' : 'success' }}">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-box"></i>
                                Pedido #{{ $detalle->id }}
                            </h3>
                        </div>
                        <div class="card-body">
                            <p><strong>Producto:</strong> {{ $detalle->producto->nombre ?? 'Sin producto' }}</p>
                            <p><strong>Cliente:</strong> {{ $detalle->cliente ?? 'Sin cliente' }}</p>
                            <p><strong>Número de pedido:</strong> {{ $detalle->numero_pedido ?? 'Sin número' }}</p>
                            <p><strong>Cantidad:</strong> {{ $detalle->cantidad }}</p>
                            <p><strong>Precio unitario:</strong> Bs {{ number_format($detalle->precio_unitario ?? 0, 2) }}</p>
                            <p><strong>Total:</strong> Bs {{ number_format($totalPedido, 2) }}</p>
                            <p><strong>Pagado:</strong> Bs {{ number_format($pagado, 2) }}</p>
                            <p><strong>Saldo:</strong> <span class="badge badge-{{ $saldo > 0 ? 'warning' : 'success' }}">Bs {{ number_format($saldo, 2) }}</span></p>
                            <p><strong>Estado:</strong> <span class="badge badge-{{ $saldo > 0 ? 'warning' : 'success' }}">{{ $detalle->estado ?? ($saldo > 0 ? 'PARCIAL' : 'PAGADO') }}</span></p>

                            <div class="mt-3">
                                <form action="{{ route('tienda.pedidos.cobrar', $detalle->id) }}" method="POST" class="d-flex gap-2">
                                    @csrf
                                    <input type="number" step="0.01" min="0" name="monto" class="form-control" placeholder="Monto a cobrar" required>
                                    <button type="submit" class="btn btn-success"><i class="fas fa-money-bill-wave"></i> Cobrar</button>
                                </form>
                            </div>

                            <div class="mt-3">
                                <form action="{{ route('tienda.pedidos.recibir-inventario', $detalle->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-box-open"></i> Registrar en inventario</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                <h3>No hay pedidos del día</h3>
                <p class="text-muted">Los pedidos enviados por despacho aparecerán aquí.</p>
            </div>
        </div>
    @endif
</div>
@stop
