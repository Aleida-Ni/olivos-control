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

    @if($detalleDespachos->count() > 0)
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
