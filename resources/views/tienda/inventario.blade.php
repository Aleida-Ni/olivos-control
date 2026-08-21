@extends('adminlte::page')

@section('title', 'Inventario de Tienda')

@section('content_header')

    <h1>
        <i class="fas fa-boxes"></i>
        Inventario de Tienda
    </h1>

@stop


@section('content')

<div class="container-fluid">

    <div class="card">

        <div class="card-header">

            <h3 class="card-title">
                <i class="fas fa-boxes"></i>
                Productos disponibles
            </h3>

        </div>


        <div class="card-body">

            {{-- ========================= --}}
            {{-- BUSCADOR --}}
            {{-- ========================= --}}

            <div class="mb-4">

                <label for="buscarProducto">

                    <i class="fas fa-search"></i>

                    Buscar producto

                </label>

                <input
                    type="text"
                    id="buscarProducto"
                    class="form-control"
                    placeholder="Escribe el nombre del producto..."
                >

            </div>


            {{-- ========================= --}}
            {{-- CATEGORÍAS --}}
            {{-- ========================= --}}

            <div class="mb-4">

                <button
                    type="button"
                    class="btn btn-danger categoria-btn mr-2 mb-2"
                    data-categoria="todos"
                >
                    Todos
                </button>


                @foreach($productos->pluck('categoria')->filter()->unique('id') as $categoria)

                    <button
                        type="button"
                        class="btn btn-secondary categoria-btn mr-2 mb-2"
                        data-categoria="{{ strtolower($categoria->nombre) }}"
                    >

                        {{ $categoria->nombre }}

                    </button>

                @endforeach

            </div>


            {{-- ========================= --}}
            {{-- PRODUCTOS --}}
            {{-- ========================= --}}

            <div class="row" id="listaProductos">

                @foreach($productos as $producto)

                    @php

                        $stock = $producto->inventario->sum('stock');

                    @endphp


                    <div
                        class="col-lg-3 col-md-4 col-sm-6 mb-4 producto-card"

                        data-categoria="{{ strtolower($producto->categoria->nombre ?? '') }}"

                        data-nombre="{{ strtolower($producto->nombre) }}"
                    >

                        <div class="card producto-tienda h-100 shadow-sm">


                            {{-- ========================= --}}
                            {{-- STOCK --}}
                            {{-- ========================= --}}

                            <div
                                class="stock-badge
                                @if($stock <= 0)
                                    stock-cero
                                @elseif($stock < 5)
                                    stock-bajo
                                @else
                                    stock-normal
                                @endif"
                            >

                                {{ $stock }}

                            </div>


                            {{-- ========================= --}}
                            {{-- PRODUCTO --}}
                            {{-- ========================= --}}

                            <div class="card-body text-center">


                                <div class="producto-icon">

                                    📦

                                </div>


                                <h5 class="font-weight-bold">

                                    {{ $producto->nombre }}

                                </h5>


                                <small class="text-muted">

                                    {{ $producto->categoria->nombre ?? 'Sin categoría' }}

                                </small>


                            </div>

                        </div>

                    </div>

                @endforeach

            </div>


            {{-- ========================= --}}
            {{-- SIN RESULTADOS --}}
            {{-- ========================= --}}

            <div
                id="sinResultados"
                class="text-center text-muted"
                style="display:none;"
            >

                <i class="fas fa-search fa-3x mb-3"></i>

                <h5>
                    No se encontraron productos.
                </h5>

            </div>

        </div>

    </div>

</div>

@stop


@section('css')

<style>

/* ========================================= */
/* TARJETA DEL PRODUCTO */
/* ========================================= */

.producto-tienda {

    position: relative;

    min-height: 180px;

    border-radius: 12px;

    transition: 0.2s;

}


.producto-tienda:hover {

    transform: translateY(-3px);

}


/* ========================================= */
/* ICONO */
/* ========================================= */

.producto-icon {

    font-size: 45px;

    margin-bottom: 10px;

}


/* ========================================= */
/* STOCK */
/* ========================================= */

.stock-badge {

    position: absolute;

    top: 10px;

    right: 10px;

    min-width: 38px;

    height: 38px;

    padding: 5px;

    border-radius: 50%;

    color: white;

    font-weight: bold;

    font-size: 15px;

    display: flex;

    align-items: center;

    justify-content: center;

}


/* STOCK NORMAL */

.stock-normal {

    background: #28a745;

}


/* STOCK BAJO */

.stock-bajo {

    background: #ffc107;

    color: #212529;

}


/* SIN STOCK */

.stock-cero {

    background: #dc3545;

}


/* ========================================= */
/* BOTONES DE CATEGORÍA */
/* ========================================= */

.categoria-btn {

    min-width: 110px;

}

</style>

@stop


@section('js')

<script>


/* ================================================= */
/* CATEGORÍAS */
/* ================================================= */

document
    .querySelectorAll('.categoria-btn')
    .forEach(function(button)
    {

        button.addEventListener('click', function()
        {

            let categoria =
                this.dataset.categoria;


            /*
             * Cambiar botón activo
             */

            document
                .querySelectorAll('.categoria-btn')
                .forEach(function(btn)
                {

                    btn.classList.remove('btn-danger');

                    btn.classList.add('btn-secondary');

                });


            this.classList.remove('btn-secondary');

            this.classList.add('btn-danger');


            /*
             * Filtrar
             */

            filtrarProductos(categoria);

        });

    });



/* ================================================= */
/* FILTRAR PRODUCTOS */
/* ================================================= */

function filtrarProductos(categoria)
{

    let texto =
        document
            .getElementById('buscarProducto')
            .value
            .toLowerCase()
            .trim();


    let visibles = 0;


    document
        .querySelectorAll('.producto-card')
        .forEach(function(card)
        {

            let categoriaProducto =
                card.dataset.categoria;


            let nombre =
                card.dataset.nombre;


            let coincideCategoria =
                categoria === 'todos' ||
                categoriaProducto === categoria;


            let coincideBusqueda =
                texto === '' ||
                nombre.includes(texto);


            if (
                coincideCategoria &&
                coincideBusqueda
            )
            {

                card.style.display = '';

                visibles++;

            }
            else
            {

                card.style.display = 'none';

            }

        });


    document
        .getElementById('sinResultados')
        .style.display =
            visibles === 0 ? 'block' : 'none';

}



/* ================================================= */
/* BUSCADOR */
/* ================================================= */

document
    .getElementById('buscarProducto')
    .addEventListener('input', function()
    {

        /*
         * Buscar dentro de la categoría
         * que esté seleccionada.
         */

        let categoriaActiva =
            document
                .querySelector('.categoria-btn.btn-danger')
                ?.dataset.categoria
                ?? 'todos';


        filtrarProductos(categoriaActiva);

    });


</script>

@stop