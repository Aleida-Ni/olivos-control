@extends('adminlte::page')

@section('title', 'Recibir Productos')

@section('content_header')

    <h1>
        <i class="fas fa-box-open"></i>
        Recibir Productos
    </h1>

@stop


@section('content')

<div class="container-fluid">

    @if(session('success'))

        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>

    @endif


    @if($despachos->count() > 0)

        <div class="alert alert-warning">

            <i class="fas fa-exclamation-circle"></i>

            <strong>
                Hay {{ $despachos->count() }} despacho(s)
                pendiente(s) de recepción.
            </strong>

        </div>


        @foreach($despachos as $despacho)

            <div class="card card-warning mb-4">

                <div class="card-header">

                    <h3 class="card-title">

                        <i class="fas fa-truck"></i>

                        Despacho #{{ $despacho->id }}

                    </h3>

                </div>


                <div class="card-body">

                    <div class="row mb-3">

                        <div class="col-md-6">

                            <strong>Chofer:</strong>

                            {{ $despacho->chofer->nombre ?? 'Sin chofer' }}

                        </div>

                        <div class="col-md-6">

                            <strong>Fecha:</strong>

                            {{ $despacho->fecha }}

                        </div>

                    </div>


                    <hr>


                    <h5>

                        <i class="fas fa-boxes"></i>

                        Productos recibidos

                    </h5>


                    <div class="row">

                        @foreach($despacho->detalles as $detalle)

                            <div class="col-md-4 mb-3">

                                <div class="card shadow-sm">

                                    <div class="card-body text-center">

                                        <i class="fas fa-box fa-2x mb-2"></i>

                                        <h5>

                                            {{ $detalle->producto->nombre ?? 'Producto' }}

                                        </h5>

                                        <span class="badge badge-primary">

                                            Cantidad:
                                            {{ $detalle->cantidad }}

                                        </span>

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>

                </div>


                <div class="card-footer">

                    <form
                        action="{{ route('tienda.recibir.confirmar', $despacho) }}"
                        method="POST"
                    >

                        @csrf

                        @method('PATCH')

                        <button
                            type="submit"
                            class="btn btn-success btn-lg btn-block"
                            onclick="return confirm('¿Confirmar que estos productos fueron recibidos en tienda?')"
                        >

                            <i class="fas fa-check-circle"></i>

                            CONFIRMAR RECEPCIÓN

                        </button>

                    </form>

                </div>

            </div>

        @endforeach


    @else

        <div class="card">

            <div class="card-body text-center py-5">

                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>

                <h3>
                    No hay productos pendientes
                </h3>

                <p class="text-muted">

                    No hay despachos enviados pendientes de recepción.

                </p>

            </div>

        </div>

    @endif

</div>

@stop