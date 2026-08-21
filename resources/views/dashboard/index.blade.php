@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')

    <div class="row">

        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>0</h3>
                    <p>Despachos pendientes</p>
                </div>

                <div class="icon">
                    <i class="fas fa-truck"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>0</h3>
                    <p>Pedidos enviados hoy</p>
                </div>

                <div class="icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>0</h3>
                    <p>Productos en inventario</p>
                </div>

                <div class="icon">
                    <i class="fas fa-boxes"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>0</h3>
                    <p>Stock bajo</p>
                </div>

                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>

    </div>

@stop