@extends('adminlte::page')

@section('title', 'Historial')

@section('content_header')
    <h1><i class="fas fa-clipboard-list"></i> Historial</h1>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-calendar"></i> Buscar despachos</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('tienda.historial') }}" method="GET">
            <div class="row">
                <div class="col-md-4">
                    <label for="fecha">Fecha de recepción</label>
                    <input id="fecha" type="date" name="fecha" class="form-control" value="{{ request('fecha') }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                    <a href="{{ route('tienda.historial') }}" class="btn btn-secondary">
                        <i class="fas fa-sync"></i> Mostrar todos
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@if($despachos->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-history fa-4x text-muted mb-3"></i>
            <h3>No hay despachos recibidos</h3>
            <p class="text-muted">
                {{ request('fecha') ? 'No existen despachos recibidos en esa fecha.' : 'Todavía no se ha confirmado ningún despacho.' }}
            </p>
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

                <p class="mb-3">
                    <strong>Recibido:</strong>
                    {{ $despacho->confirmado_en->format('d/m/Y H:i') }}
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