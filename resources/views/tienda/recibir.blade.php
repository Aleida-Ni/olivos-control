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

        @foreach($despachos as $despacho)

            <div class="card card-warning">

                <div class="card-header">

                    <h3 class="card-title">

                        <i class="fas fa-truck"></i>

                        Despacho #{{ $despacho->id }}

                    </h3>

                </div>


                <div class="card-body">

                    <div class="row mb-3">

                        <div class="col-md-4">

                            <strong>Chofer:</strong>

                            {{ $despacho->chofer->nombre ?? 'Sin chofer' }}

                        </div>


                        <div class="col-md-4">

                            <strong>Fecha:</strong>

                            {{ $despacho->fecha }}

                        </div>


                        <div class="col-md-4">

                            <strong>Estado:</strong>

                            <span class="badge badge-warning">

                                PENDIENTE

                            </span>

                        </div>

                    </div>


                    <hr>


                    <h5>

                        <i class="fas fa-boxes"></i>

                        Productos enviados

                    </h5>


                    <div class="table-responsive">

                        <table class="table table-bordered">

                            <thead>

                                <tr>

                                    <th>Producto</th>

                                    <th width="150">
                                        Cantidad
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @foreach($despacho->detalles as $detalle)

                                    <tr>

                                        <td>

                                            {{ $detalle->producto->nombre ?? 'Producto' }}

                                        </td>


                                        <td class="text-center">

                                            <strong>

                                                {{ $detalle->cantidad }}

                                            </strong>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>


                <div class="card-footer text-right">

                    <form
                        action="{{ route('tienda.recibir.confirmar', $despacho) }}"
                        method="POST"
                    >

                        @csrf

                        @method('PATCH')


                        <button
                            type="submit"
                            class="btn btn-success btn-lg"
                            onclick="return confirm('¿Confirmar que estos productos llegaron a la tienda?')"
                        >

                            <i class="fas fa-check"></i>

                            RECIBIR PRODUCTOS

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

                    Los despachos enviados aparecerán aquí.

                </p>

            </div>

        </div>

    @endif

</div>

@stop