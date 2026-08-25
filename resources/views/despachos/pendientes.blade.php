@extends('adminlte::page')

@section('title', 'Despachos Pendientes')

@section('content_header')

    <h1>
        <i class="fas fa-clock text-warning"></i>
        Despachos Pendientes
    </h1>

@stop


@section('content')

@if(session('success'))

    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>

@endif


@if($despachos->count() > 0)

    <div class="row">

        @foreach($despachos as $despacho)

            <div class="col-md-6">

                <div class="card card-warning">

                    <div class="card-header">

                        <h3 class="card-title">

                            <i class="fas fa-truck"></i>

                            Despacho #{{ $despacho->id }}

                        </h3>

                    </div>


                    <div class="card-body">

                        <p>
                            <strong>Chofer:</strong>
                            {{ $despacho->chofer->nombre ?? 'Sin chofer' }}
                        </p>

                        <p>
                            <strong>Fecha:</strong>
                            {{ $despacho->created_at->format('d/m/Y H:i') }}
                        </p>

                        <hr>

                        <h5>
                            Productos enviados
                        </h5>


                        <ul class="list-group">

                            @foreach($despacho->detalles as $detalle)

                                <li class="list-group-item
                                           d-flex
                                           justify-content-between">

                                    <span>
                                        {{ $detalle->producto->nombre ?? 'Producto' }}
                                    </span>

                                    <strong>
                                        {{ $detalle->cantidad }}
                                    </strong>

                                </li>

                            @endforeach

                        </ul>


                        <div class="mt-3">

                            <span class="badge badge-warning">

                                <i class="fas fa-clock"></i>

                                PENDIENTE

                            </span>

                        </div>

                    </div>


                    <div class="card-footer">

                        <span class="text-muted">
                            <i class="fas fa-store"></i>
                            Pendiente de confirmación en tienda
                        </span>

                    </div>

                </div>

            </div>

        @endforeach

    </div>

@else

    <div class="card">

        <div class="card-body text-center py-5">

            <i class="fas fa-check-circle fa-4x text-success mb-3"></i>

            <h3>
                No hay despachos pendientes
            </h3>

            <p class="text-muted">
                Todos los despachos han sido recibidos.
            </p>

        </div>

    </div>

@endif

@stop