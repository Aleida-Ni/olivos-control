@extends('adminlte::page')

@section('title', 'Historial')

@section('content_header')
    <h1><i class="fas fa-clipboard-list"></i> Historial</h1>
@stop

@section('content')
@if($despachos->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-history fa-4x text-muted mb-3"></i>
            <h3>No hay productos recibidos hoy</h3>
            <p class="text-muted">Los despachos recibidos aparecerán aquí durante el día.</p>
        </div>
    </div>
@else
    @foreach($despachos as $despacho)
        <div class="card card-success mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">
                    <i class="fas fa-truck"></i>
                    Despacho #{{ $despacho->id }}
                </h3>
                <span class="badge badge-light">
                    {{ $despacho->confirmado_en->format('H:i') }}
                </span>
            </div>

            <div class="card-body">
                <p class="mb-3">
                    <strong>Chofer:</strong>
                    {{ $despacho->chofer->nombre ?? 'Sin chofer' }}
                </p>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="text-right">Cantidad recibida</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($despacho->detalles as $detalle)
                                <tr>
                                    <td>{{ $detalle->producto->nombre ?? 'Producto' }}</td>
                                    <td class="text-right">{{ $detalle->cantidad }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
@endif
@stop