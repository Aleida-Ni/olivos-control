@extends('adminlte::page')

@section('title', 'Registrar Producto')

@section('content_header')
    <h1>Registrar Producto</h1>
@stop

@section('content')

<div class="row justify-content-center">

    <div class="col-md-8">

        <div class="card">

            <div class="card-header">
                <h3 class="card-title">
                    Nuevo Producto
                </h3>
            </div>

            <form action="{{ route('productos.store') }}" method="POST">

                @csrf

                <div class="card-body">

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
                            value="{{ old('nombre') }}"
                            placeholder="Ej: Torta Chocolate"
                        >

                        @error('nombre')
                            <span class="invalid-feedback">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>

                    <div class="form-group">
                        <label for="precio">Precio de venta (Bs)</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">Bs</span></div>
                            <input
                                type="number"
                                name="precio"
                                id="precio"
                                class="form-control @error('precio') is-invalid @enderror"
                                value="{{ old('precio') }}"
                                min="0"
                                step="0.01"
                                placeholder="0.00"
                            >
                        </div>
                        @error('precio')
                            <span class="text-danger small">{{ $message }}</span>
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

                            <option value="">
                                Seleccionar categoría
                            </option>

                            @foreach($categorias as $categoria)

                                <option
                                    value="{{ $categoria->id }}"
                                    {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}
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

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        El código del producto será generado automáticamente por el sistema.
                    </div>

                </div>


                <div class="card-footer">

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Registrar Producto
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