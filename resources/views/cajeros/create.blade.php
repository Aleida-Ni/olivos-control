@extends('adminlte::page')

@section('title', 'Registrar Cajero')

@section('content_header')
    <h1>
        <i class="fas fa-user-plus"></i>
        Registrar Cajero
    </h1>
@stop

@section('content')

<div class="container-fluid">

    <div class="card">

        <div class="card-header">
            <h3 class="card-title">
                Nuevo cajero
            </h3>
        </div>

        <form
            action="{{ route('cajeros.store') }}"
            method="POST"
        >

            @csrf

            <div class="card-body">

                {{-- NOMBRE --}}

                <div class="form-group">

                    <label for="name">
                        Nombre del cajero
                    </label>

                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="form-control"
                        value="{{ old('name') }}"
                        placeholder="Ej: Ceci"
                        required
                    >

                    @error('name')
                        <small class="text-danger">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                {{-- PIN --}}

                <div class="form-group">

                    <label for="pin">
                        PIN
                    </label>

                    <input
                        type="password"
                        name="pin"
                        id="pin"
                        class="form-control"
                        maxlength="4"
                        inputmode="numeric"
                        pattern="[0-9]{4}"
                        placeholder="4 dígitos"
                        required
                    >

                    <small class="text-muted">
                        El PIN debe tener 4 números.
                    </small>

                    @error('pin')
                        <br>
                        <small class="text-danger">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                {{-- CONFIRMAR PIN --}}

                <div class="form-group">

                    <label for="pin_confirmation">
                        Confirmar PIN
                    </label>

                    <input
                        type="password"
                        name="pin_confirmation"
                        id="pin_confirmation"
                        class="form-control"
                        maxlength="4"
                        inputmode="numeric"
                        pattern="[0-9]{4}"
                        placeholder="Repite el PIN"
                        required
                    >

                </div>

            </div>


            <div class="card-footer">

                <button
                    type="submit"
                    class="btn btn-success"
                >

                    <i class="fas fa-user-plus"></i>

                    REGISTRAR CAJERO

                </button>

                <a
                    href="{{ route('cajeros.index') }}"
                    class="btn btn-secondary"
                >

                    Cancelar

                </a>

            </div>

        </form>

    </div>

</div>

@stop