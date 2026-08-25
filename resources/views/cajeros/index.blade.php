@extends('adminlte::page')

@section('title', 'Cajeros')

@section('content_header')

    <div class="d-flex justify-content-between align-items-center">

        <h1>
            <i class="fas fa-users"></i>
            Cajeros
        </h1>

        <a
            href="{{ route('cajeros.create') }}"
            class="btn btn-success"
        >

            <i class="fas fa-user-plus"></i>

            Registrar Cajero

        </a>

    </div>

@stop


@section('content')

<div class="container-fluid">

    @if(session('success'))

        <div class="alert alert-success">

            <i class="fas fa-check-circle"></i>

            {{ session('success') }}

        </div>

    @endif


    <div class="card">

        <div class="card-header">

            <h3 class="card-title">
                Cajeros registrados
            </h3>

        </div>


        <div class="card-body">

            @if($cajeros->count() > 0)

                <table class="table table-bordered">

                    <thead>

                        <tr>

                            <th>
                                Nombre
                            </th>

                            <th>
                                Estado
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($cajeros as $cajero)

                            <tr>

                                <td>
                                    {{ $cajero->name }}
                                </td>

                                <td>

                                    @if($cajero->activo)

                                        <span class="badge badge-success">
                                            ACTIVO
                                        </span>

                                    @else

                                        <span class="badge badge-danger">
                                            INACTIVO
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            @else

                <div class="text-center py-5">

                    <i class="fas fa-users fa-3x text-muted"></i>

                    <h4 class="mt-3">
                        No hay cajeros registrados
                    </h4>

                    <a
                        href="{{ route('cajeros.create') }}"
                        class="btn btn-success"
                    >

                        Registrar el primero

                    </a>

                </div>

            @endif

        </div>

    </div>

</div>

@stop