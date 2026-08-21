@extends('adminlte::page')

@section('title', 'Editar Producto')

@section('content_header')
    <h1>Editar Producto</h1>
@stop

@section('content')

<div class="row justify-content-center">

    <div class="col-md-8">

        <div class="card">

            <div class="card-header">
                <h3 class="card-title">
                    Editar Producto
                </h3>
            </div>

            <form
                action="{{ route('productos.update', $producto) }}"
                method="POST"
            >

                @csrf
                @method('PUT')

                <div class="card-body">

                    {{-- CÓDIGO --}}
                    <div class="form-group">

                        <label>
                            Código
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="{{ $producto->codigo }}"
                            disabled
                        >

                        <small class="text-muted">
                            El código es generado automáticamente y no puede modificarse.
                        </small>

                    </div>


                    {{-- NOMBRE --}}
                    <div class="form-group">

                        <label for="nombre">
                            Nombre del producto
                        </label>

                        <input
                            type="text"
                            name="nombre"
                            id="nombre"
                            class="form-control @error('nombre') is-invalid @enderror"
                            value="{{ old('nombre', $producto->nombre) }}"
                        >

                        @error('nombre')
                            <span class="invalid-feedback">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    {{-- CATEGORÍA --}}
                    <div class="form-group">

                        <label for="categoria_id">
                            Categoría
                        </label>

                        <select
                            name="categoria_id"
                            id="categoria_id"
                            class="form-control @error('categoria_id') is-invalid @enderror"
                        >

                            @foreach($categorias as $categoria)

                                <option
                                    value="{{ $categoria->id }}"
                                    {{ old('categoria_id', $producto->categoria_id) == $categoria->id ? 'selected' : '' }}
                                >
                                    {{ $categoria->nombre }}
                                </option>

                            @endforeach

                        </select>

                        @error('categoria_id')
                            <span class="invalid-feedback">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>

                </div>


                <div class="card-footer">

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Guardar cambios
                    </button>

                    <a
                        href="{{ route('productos.index') }}"
                        class="btn btn-secondary"
                    >
                        Cancelar
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

@stop