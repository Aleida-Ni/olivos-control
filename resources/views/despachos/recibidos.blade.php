@extends('adminlte::page')

@section('title', 'Despachos Recibidos')

@section('content_header')

    <h1>
        <i class="fas fa-check-circle text-success"></i>
        Despachos Recibidos
    </h1>

@stop


@section('content')


{{-- FILTRO DE FECHA --}}

<div class="card">

    <div class="card-header">

        <h3 class="card-title">

            <i class="fas fa-calendar"></i>

            Buscar por fecha

        </h3>

    </div>


    <div class="card-body">

        <form
            action="{{ route('despachos.recibidos') }}"
            method="GET"
        >

            <div class="row">

                <div class="col-md-4">

                    <label>
                        Fecha
                    </label>

                    <input
                        type="date"
                        name="fecha"
                        class="form-control"
                        value="{{ request('fecha') }}"
                    >

                </div>


                <div class="col-md-4 d-flex align-items-end">

                    <button
                        type="submit"
                        class="btn btn-primary mr-2"
                    >

                        <i class="fas fa-search"></i>

                        Buscar

                    </button>


                    <a
                        href="{{ route('despachos.recibidos') }}"
                        class="btn btn-secondary"
                    >

                        <i class="fas fa-sync"></i>

                        Mostrar todos

                    </a>

                </div>

            </div>

        </form>

    </div>

</div>



@if($despachos->count() > 0)

    @foreach($despachos as $despacho)

        <div class="card card-success">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-truck"></i>

                    Despacho #{{ $despacho->id }}

                </h3>

            </div>


            <div class="card-body">

                <div class="row">

                    <div class="col-md-4">

                        <strong>Chofer</strong>

                        <p>
                            {{ $despacho->chofer->nombre ?? 'Sin chofer' }}
                        </p>

                    </div>


                    <div class="col-md-4">

                        <strong>Enviado</strong>

                        <p>
                            {{ $despacho->created_at->format('d/m/Y H:i') }}
                        </p>

                    </div>


                    <div class="col-md-4">

                        <strong>Recibido</strong>

                        <p>

                            @if($despacho->confirmado_en)

                                {{ $despacho->confirmado_en->format('d/m/Y H:i') }}

                            @else

                                -

                            @endif

                        </p>

                    </div>

                </div>


                <hr>


                <h5>
                    Productos
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

                    <span class="badge badge-success">

                        <i class="fas fa-check"></i>

                        RECIBIDO

                    </span>

                </div>

            </div>

        </div>

    @endforeach

@else

    <div class="card">

        <div class="card-body text-center py-5">

            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>

            <h3>
                No hay despachos recibidos
            </h3>

            @if(request('fecha'))

                <p class="text-muted">
                    No existen despachos recibidos en esa fecha.
                </p>

            @else

                <p class="text-muted">
                    Todavía no se ha confirmado ningún despacho.
                </p>

            @endif

        </div>

    </div>

@endif

@stop