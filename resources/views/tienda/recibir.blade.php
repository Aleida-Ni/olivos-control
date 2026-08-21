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

    <div class="card">

        <div class="card-header">

            <h3 class="card-title">
                Despachos enviados desde producción
            </h3>

        </div>

        <div class="card-body">

            <div class="text-center py-5">

                <i class="fas fa-truck-loading fa-4x text-muted mb-3"></i>

                <h3>
                    No hay despachos pendientes
                </h3>

                <p class="text-muted">
                    Los productos enviados desde despacho
                    aparecerán aquí.
                </p>

            </div>

        </div>

    </div>

</div>

@stop