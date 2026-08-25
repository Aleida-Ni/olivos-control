@extends('adminlte::page')

@section('title', 'Inicio despacho')

@section('content_header')
    <h1><i class="fas fa-truck"></i> Panel de despacho</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="card card-danger shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-truck fa-3x mb-3"></i>
                    <h3>Crear envío</h3>
                    <p class="text-muted">Enviar productos o pedidos a tienda.</p>
                    <a href="{{ route('despachos.create') }}" class="btn btn-danger">Ir a despachar</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-warning shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-3x mb-3"></i>
                    <h3>Pendientes</h3>
                    <p class="text-muted">Despachos sin recibir en tienda.</p>
                    <a href="{{ route('despachos.pendientes') }}" class="btn btn-warning">Ver pendientes</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-success shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-3x mb-3"></i>
                    <h3>Recibidos</h3>
                    <p class="text-muted">Historial de despachos ya confirmados.</p>
                    <a href="{{ route('despachos.recibidos') }}" class="btn btn-success">Ver recibidos</a>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
