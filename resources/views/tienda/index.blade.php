@extends('adminlte::page')

@section('title', 'Tienda')

@section('content_header')
    <h1>
        <i class="fas fa-store"></i>
        Panel de tienda
    </h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="card card-primary">
                <div class="card-body text-center">
                    <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                    <h3>Ventas</h3>
                    <a href="{{ route('tienda.ventas') }}" class="btn btn-primary">
                        Ir a ventas
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-success">
                <div class="card-body text-center">
                    <i class="fas fa-boxes fa-3x mb-3"></i>
                    <h3>Inventario</h3>
                    <a href="{{ route('tienda.inventario') }}" class="btn btn-success">
                        Ver inventario
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-warning">
                <div class="card-body text-center">
                    <i class="fas fa-box-open fa-3x mb-3"></i>
                    <h3>Recepciones pendientes</h3>
                    <a href="{{ route('tienda.recibir') }}" class="btn btn-warning">
                        Ver recepciones
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@stop