@extends('adminlte::page')

@section('title', 'Ventas realizadas')

@section('content_header')
    <h1><i class="fas fa-receipt"></i> Ventas realizadas</h1>
@stop

@section('content')
<div class="container-fluid">
    @if($ventas->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Cliente</th>
                        <th>Cajero</th>
                        <th>Método</th>
                        <th>Subtotal</th>
                        <th>Descuento</th>
                        <th>Total</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ventas as $venta)
                        <tr>
                            <td>{{ $venta->id }}</td>
                            <td>{{ $venta->cliente ?? 'Consumidor final' }}</td>
                            <td>{{ $venta->cajero->name ?? 'Sin cajero' }}</td>
                            <td>{{ $venta->metodo_pago }}</td>
                            <td>Bs {{ number_format($venta->subtotal, 2) }}</td>
                            <td>Bs {{ number_format($venta->descuento, 2) }}</td>
                            <td>Bs {{ number_format($venta->total, 2) }}</td>
                            <td>{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-receipt fa-4x text-muted mb-3"></i>
                <h3>No hay ventas realizadas</h3>
            </div>
        </div>
    @endif
</div>
@stop
