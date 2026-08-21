@extends('adminlte::page')

@section('title', 'Tienda')

@section('content_header')

    <h1>
        <i class="fas fa-store"></i>
        Tienda
    </h1>

@stop


@section('content')

<div class="container-fluid">

    <div class="row">

        {{-- RECIBIR --}}
        <div class="col-md-4">

            <div class="card card-primary">

                <div class="card-body text-center">

                    <i class="fas fa-box-open fa-4x mb-3"></i>

                    <h3>
                        Recibir Productos
                    </h3>

                    <p class="text-muted">
                        Revisar y confirmar los productos
                        enviados desde despacho.
                    </p>

                </div>

            </div>

        </div>


        {{-- VENTAS --}}
        <div class="col-md-4">

            <div class="card card-success">

                <div class="card-body text-center">

                    <i class="fas fa-shopping-cart fa-4x mb-3"></i>

                    <h3>
                        Ventas
                    </h3>

                    <p class="text-muted">
                        Registrar las ventas realizadas
                        en tienda.
                    </p>

                </div>

            </div>

        </div>


        {{-- CAJA --}}
        <div class="col-md-4">

            <div class="card card-warning">

                <div class="card-body text-center">

                    <i class="fas fa-cash-register fa-4x mb-3"></i>

                    <h3>
                        Caja
                    </h3>

                    <p class="text-muted">
                        Controlar ingresos, egresos
                        y efectivo.
                    </p>

                </div>

            </div>

        </div>

    </div>


    <div class="row">

        {{-- INVENTARIO --}}
        <div class="col-md-6">

            <div class="card">

                <div class="card-body text-center">

                    <i class="fas fa-boxes fa-4x mb-3"></i>

                    <h3>
                        Inventario
                    </h3>

                    <p class="text-muted">
                        Consultar los productos disponibles
                        en tienda.
                    </p>

                </div>

            </div>

        </div>


        {{-- CIERRES --}}
        <div class="col-md-6">

            <div class="card">

                <div class="card-body text-center">

                    <i class="fas fa-file-invoice-dollar fa-4x mb-3"></i>

                    <h3>
                        Cierres
                    </h3>

                    <p class="text-muted">
                        Consultar los cierres de turno
                        y las cuentas de caja.
                    </p>

                </div>

            </div>

        </div>

    </div>

</div>

@stop