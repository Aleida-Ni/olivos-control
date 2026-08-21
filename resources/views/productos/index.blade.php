@extends('adminlte::page')

@section('title', 'Productos')

@section('content_header')

    <div class="d-flex justify-content-between align-items-center">

        <h1>
            🛍️ Productos
        </h1>

        <a href="{{ route('productos.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Agregar producto
        </a>

    </div>

@stop


@section('content')

<div class="card">

    <div class="card-header">
        <h3 class="card-title">
            Productos registrados
        </h3>
    </div>

    <div class="card-body">

        @if(session('success'))

            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>

        @endif


        @if($productos->count() > 0)

            <div class="table-responsive">

                <table class="table table-bordered table-hover">

                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Estado</th>
                            <th width="160">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($productos as $producto)

                            <tr>

                                <td>
                                    <strong>{{ $producto->codigo }}</strong>
                                </td>

                                <td>
                                    {{ $producto->nombre }}
                                </td>

                                <td>
                                    {{ $producto->categoria->nombre ?? 'Sin categoría' }}
                                </td>

                                <td>

                                    @if($producto->activo)

                                        <span class="badge badge-success">
                                            Activo
                                        </span>

                                    @else

                                        <span class="badge badge-secondary">
                                            Inactivo
                                        </span>

                                    @endif

                                </td>

                                <td>

                                    <a
                                        href="{{ route('productos.edit', $producto) }}"
                                        class="btn btn-warning btn-sm"
                                        title="Editar"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </a>


                                    @if($producto->activo)

                                        <form
                                            action="{{ route('productos.destroy', $producto) }}"
                                            method="POST"
                                            style="display:inline;"
                                            onsubmit="return confirm('¿Estás segura de que quieres desactivar este producto?');"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-danger btn-sm"
                                                title="Eliminar"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </button>

                                        </form>

                                    @endif

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="text-center py-5">

                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>

                <h4>No hay productos registrados</h4>

                <p class="text-muted">
                    Comienza agregando tu primer producto.
                </p>

                <a
                    href="{{ route('productos.create') }}"
                    class="btn btn-primary"
                >
                    <i class="fas fa-plus"></i>
                    Agregar producto
                </a>

            </div>

        @endif

    </div>

</div>

@stop