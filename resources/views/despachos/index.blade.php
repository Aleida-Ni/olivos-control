@extends('adminlte::page')

@section('title', 'Despachar')

@section('content_header')

    <div class="d-flex justify-content-between align-items-center">

        <div>
            <h1>
                <i class="fas fa-truck text-danger"></i>
                Despachar
            </h1>

            <p class="text-muted mb-0">
                Selecciona los productos que serán enviados a tienda.
            </p>
        </div>

    </div>

@stop


@section('content')

<div class="container-fluid">

    {{-- ========================================================= --}}
    {{-- BARRA FIJA DE DESPACHO --}}
    {{-- ========================================================= --}}

    <div
        id="barraDespacho"
        class="card shadow"
        style="
            display:none;
            position:sticky;
            top:10px;
            z-index:1000;
        "
    >

        <div class="card-body py-2">

            <div class="row align-items-center">

                {{-- INFORMACIÓN --}}
                <div class="col-md-7">

                    <div class="d-flex align-items-center">

                        <div
                            class="bg-danger text-white rounded-circle mr-3"
                            style="
                                width:45px;
                                height:45px;
                                display:flex;
                                align-items:center;
                                justify-content:center;
                            "
                        >

                            <i class="fas fa-shopping-basket"></i>

                        </div>

                        <div>

                            <strong>
                                Despacho actual
                            </strong>

                            <div class="text-muted">

                                <span id="totalProductos">
                                    0
                                </span>

                                productos seleccionados

                            </div>

                        </div>

                    </div>

                </div>


                {{-- CHOFER --}}
                <div class="col-md-2">

                    <select
                        class="form-control form-control-sm"
                        id="chofer_id_barra"
                    >

                        <option value="">
                            Chofer
                        </option>

                        @foreach($choferes as $chofer)

                            <option value="{{ $chofer->id }}">
                                {{ $chofer->nombre }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- BOTÓN --}}
                <div class="col-md-3 text-right">

                    <button
                        type="button"
                        class="btn btn-success btn-lg"
                        onclick="enviarDespacho()"
                    >

                        <i class="fas fa-paper-plane"></i>

                        DESPACHAR A TIENDA

                    </button>

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- FORMULARIO --}}
    {{-- ========================================================= --}}

    <form
        action="{{ route('despachos.store') }}"
        method="POST"
        id="formDespacho"
    >

        @csrf


        {{-- ===================================================== --}}
        {{-- CHOFER --}}
        {{-- ===================================================== --}}

        <div class="card">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-user-tie"></i>

                    Información del despacho

                </h3>

            </div>

            <div class="card-body">

                <div class="form-group mb-0">

                    <label for="chofer_id_principal">
                        Chofer
                    </label>

                    <select
                        class="form-control"
                        id="chofer_id_principal"
                        required
                    >

                        <option value="">
                            Seleccione un chofer
                        </option>

                        @foreach($choferes as $chofer)

                            <option value="{{ $chofer->id }}">
                                {{ $chofer->nombre }}
                            </option>

                        @endforeach

                    </select>

                    <small class="text-muted">
                        Selecciona el chofer que llevará este despacho.
                    </small>

                </div>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- PRODUCTOS --}}
        {{-- ===================================================== --}}

        <div class="card">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-boxes"></i>

                    Productos

                </h3>

            </div>


            <div class="card-body">


                {{-- BUSCADOR --}}

                <div class="mb-4">

                    <label>

                        <i class="fas fa-search"></i>

                        Buscar producto

                    </label>

                    <input
                        type="text"
                        id="buscarProducto"
                        class="form-control form-control-lg"
                        placeholder="Escribe el nombre del producto..."
                    >

                </div>


                {{-- ================================================= --}}
                {{-- CATEGORÍAS --}}
                {{-- ================================================= --}}

                <div class="mb-4">

                    <button
                        type="button"
                        class="btn btn-danger categoria-btn mr-2 mb-2"
                        data-categoria="todos"
                    >
                        Todos
                    </button>


                    @php

                        $categorias = $productos
                            ->pluck('categoria')
                            ->filter()
                            ->unique('id');

                    @endphp


                    @foreach($categorias as $categoria)

                        <button
                            type="button"
                            class="btn btn-secondary categoria-btn mr-2 mb-2"
                            data-categoria="{{ strtolower($categoria->nombre) }}"
                        >

                            {{ $categoria->nombre }}

                        </button>

                    @endforeach

                </div>


                {{-- ================================================= --}}
                {{-- PRODUCTOS --}}
                {{-- ================================================= --}}

                <div
                    class="row"
                    id="listaProductos"
                >

                    @foreach($productos as $producto)

                        <div
                            class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-4 producto-card"
                            data-categoria="{{ strtolower($producto->categoria->nombre ?? '') }}"
                            data-nombre="{{ strtolower($producto->nombre) }}"
                        >

                            <div
                                class="card h-100 shadow-sm producto-cuadro"
                                style="
                                    border-radius:15px;
                                    border:2px solid #eeeeee;
                                "
                            >

                                <div class="card-body text-center">

                                    <div
                                        style="
                                            font-size:42px;
                                            margin-bottom:10px;
                                        "
                                    >
                                        📦
                                    </div>


                                    <h5
                                        class="font-weight-bold"
                                        style="min-height:45px;"
                                    >

                                        {{ $producto->nombre }}

                                    </h5>


                                    <small class="text-muted">

                                        {{ $producto->categoria->nombre ?? '' }}

                                    </small>


                                    {{-- + / - --}}

                                    <div class="mt-4">

                                        <button
                                            type="button"
                                            class="btn btn-outline-danger btn-lg"
                                            onclick="disminuir({{ $producto->id }})"
                                        >

                                            <i class="fas fa-minus"></i>

                                        </button>


                                        <span
                                            class="mx-4 font-weight-bold"
                                            style="font-size:22px;"
                                            id="cantidad-{{ $producto->id }}"
                                        >
                                            0
                                        </span>


                                        <button
                                            type="button"
                                            class="btn btn-danger btn-lg"
                                            onclick="aumentar(
                                                {{ $producto->id }},
                                                '{{ addslashes($producto->nombre) }}'
                                            )"
                                        >

                                            <i class="fas fa-plus"></i>

                                        </button>

                                    </div>

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>


                {{-- SIN RESULTADOS --}}

                <div
                    id="sinResultados"
                    class="text-center text-muted py-5"
                    style="display:none;"
                >

                    <i class="fas fa-search fa-3x mb-3"></i>

                    <h5>
                        No se encontraron productos.
                    </h5>

                </div>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- RESUMEN --}}
        {{-- ===================================================== --}}

        <div class="card">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-clipboard-list"></i>

                    Despacho actual

                </h3>

            </div>


            <div class="card-body">

                <div id="resumen">

                    <p class="text-muted">

                        Todavía no agregaste productos.

                    </p>

                </div>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- PEDIDOS --}}
        {{-- ===================================================== --}}

        <div class="card">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-file-alt"></i>

                    Pedidos especiales

                </h3>

            </div>


            <div class="card-body">

                <button
                    type="button"
                    class="btn btn-outline-primary"
                    onclick="agregarPedido()"
                >

                    <i class="fas fa-plus"></i>

                    Agregar pedido

                </button>


                <div
                    id="pedidos"
                    class="mt-3"
                ></div>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- OBSERVACIÓN --}}
        {{-- ===================================================== --}}

        <div class="card">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-comment"></i>

                    Observación

                </h3>

            </div>


            <div class="card-body">

                <textarea
                    name="observacion"
                    class="form-control"
                    rows="3"
                    placeholder="Observaciones del despacho (opcional)"
                ></textarea>

            </div>

        </div>


        {{-- INPUT OCULTO DEL CHOFER --}}

        <input
            type="hidden"
            name="chofer_id"
            id="chofer_id"
        >

    </form>

</div>

@stop


@section('css')

<style>

    .producto-cuadro {
        transition: all 0.2s ease;
    }

    .producto-cuadro:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.12) !important;
    }

</style>

@stop


@section('js')

<script>

let cantidades = {};


// ============================================================
// AUMENTAR
// ============================================================

function aumentar(id, nombre)
{
    if (!cantidades[id]) {

        cantidades[id] = {
            nombre: nombre,
            cantidad: 0
        };

    }

    cantidades[id].cantidad++;

    actualizarCantidad(id);

    mostrarResumen();

    actualizarBarraDespacho();
}


// ============================================================
// DISMINUIR
// ============================================================

function disminuir(id)
{
    if (!cantidades[id]) {
        return;
    }

    if (cantidades[id].cantidad > 0) {

        cantidades[id].cantidad--;

    }

    actualizarCantidad(id);

    mostrarResumen();

    actualizarBarraDespacho();
}


// ============================================================
// ACTUALIZAR CANTIDAD
// ============================================================

function actualizarCantidad(id)
{
    let elemento =
        document.getElementById('cantidad-' + id);

    if (elemento) {

        elemento.innerText =
            cantidades[id]?.cantidad ?? 0;

    }
}


// ============================================================
// MOSTRAR RESUMEN
// ============================================================

function mostrarResumen()
{
    let html = '';

    let hayProductos = false;

    Object.keys(cantidades).forEach(function(id)
    {

        let producto = cantidades[id];

        if (producto.cantidad > 0)
        {

            hayProductos = true;

            html += `

                <div
                    class="d-flex justify-content-between
                           align-items-center
                           border-bottom py-3"
                >

                    <span>
                        ${producto.nombre}
                    </span>

                    <strong>
                        ${producto.cantidad}
                    </strong>

                </div>

            `;

        }

    });


    if (!hayProductos)
    {

        html = `

            <p class="text-muted">

                Todavía no agregaste productos.

            </p>

        `;

    }


    document.getElementById('resumen').innerHTML =
        html;
}


// ============================================================
// ACTUALIZAR BARRA SUPERIOR
// ============================================================

function actualizarBarraDespacho()
{
    let total = 0;

    Object.values(cantidades).forEach(function(producto)
    {
        total += producto.cantidad;
    });


    let pedidos =
        document.querySelectorAll('#pedidos .pedido-card').length;


    let barra =
        document.getElementById('barraDespacho');


    if (total > 0 || pedidos > 0)
    {

        barra.style.display = 'block';

    }
    else
    {

        barra.style.display = 'none';

    }


    document.getElementById('totalProductos')
        .innerText = total;
}


// ============================================================
// AGREGAR PEDIDO
// ============================================================

function agregarPedido()
{
    let id = Date.now();

    let html = `

        <div
            class="card border mb-3 pedido-card"
            id="pedido-${id}"
        >

            <div class="card-body">

                <div class="row">

                    <div class="col-md-3">

                        <label>
                            Número de pedido
                        </label>

                        <input
                            type="text"
                            name="pedidos[${id}][numero_pedido]"
                            class="form-control"
                            placeholder="Ej: 25"
                        >

                    </div>


                    <div class="col-md-3">

                        <label>
                            Cliente
                        </label>

                        <input
                            type="text"
                            name="pedidos[${id}][cliente]"
                            class="form-control"
                            placeholder="Nombre del cliente"
                        >

                    </div>


                    <div class="col-md-4">

                        <label>
                            Producto
                        </label>

                        <select
                            name="pedidos[${id}][producto_id]"
                            class="form-control"
                        >

                            <option value="">
                                Seleccione producto
                            </option>

                            @foreach($productos as $producto)

                                <option value="{{ $producto->id }}">

                                    {{ $producto->nombre }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-1">

                        <label>
                            Cantidad
                        </label>

                        <input
                            type="number"
                            name="pedidos[${id}][cantidad]"
                            class="form-control"
                            value="1"
                            min="1"
                        >

                    </div>


                    <div class="col-md-1">

                        <label>
                            &nbsp;
                        </label>

                        <button
                            type="button"
                            class="btn btn-danger"
                            onclick="
                                document
                                .getElementById('pedido-${id}')
                                .remove();

                                actualizarBarraDespacho();
                            "
                        >

                            <i class="fas fa-trash"></i>

                        </button>

                    </div>

                </div>

            </div>

        </div>

    `;


    document
        .getElementById('pedidos')
        .insertAdjacentHTML(
            'beforeend',
            html
        );


    actualizarBarraDespacho();
}


// ============================================================
// CATEGORÍAS
// ============================================================

document
    .querySelectorAll('.categoria-btn')
    .forEach(function(button)
    {

        button.addEventListener(
            'click',
            function()
            {

                let categoria =
                    this.dataset.categoria;


                document
                    .querySelectorAll('.categoria-btn')
                    .forEach(function(btn)
                    {

                        btn.classList.remove(
                            'btn-danger'
                        );

                        btn.classList.add(
                            'btn-secondary'
                        );

                    });


                this.classList.remove(
                    'btn-secondary'
                );

                this.classList.add(
                    'btn-danger'
                );


                filtrarProductos(
                    categoria
                );

            }
        );

    });


// ============================================================
// FILTRAR CATEGORÍAS
// ============================================================

function filtrarProductos(categoria)
{
    let visibles = 0;


    document
        .querySelectorAll('.producto-card')
        .forEach(function(card)
        {

            let categoriaProducto =
                card.dataset.categoria;


            if (
                categoria === 'todos' ||
                categoriaProducto === categoria
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
            visibles === 0
                ? 'block'
                : 'none';
}


// ============================================================
// BUSCADOR
// ============================================================

document
    .getElementById('buscarProducto')
    .addEventListener(
        'input',
        function()
        {

            let texto =
                this.value
                    .toLowerCase()
                    .trim();


            let visibles = 0;


            document
                .querySelectorAll('.producto-card')
                .forEach(function(card)
                {

                    let nombre =
                        card.dataset.nombre;


                    if (
                        texto === '' ||
                        nombre.includes(texto)
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
                    visibles === 0
                        ? 'block'
                        : 'none';

        }
    );


// ============================================================
// CAMBIAR CHOFER
// ============================================================

document
    .getElementById('chofer_id_principal')
    .addEventListener(
        'change',
        function()
        {

            document
                .getElementById('chofer_id')
                .value = this.value;

        }
    );


// ============================================================
// ENVIAR DESPACHO
// ============================================================

function enviarDespacho()
{

    let chofer =
        document.getElementById('chofer_id_principal')
        .value;


    if (!chofer)
    {

        alert(
            'Primero selecciona el chofer.'
        );

        return;

    }


    document
        .getElementById('chofer_id')
        .value = chofer;


    let hayProductos =
        Object.values(cantidades)
            .some(
                producto =>
                    producto.cantidad > 0
            );


    let hayPedidos =
        document
            .querySelectorAll(
                '#pedidos .pedido-card'
            )
            .length > 0;


    if (!hayProductos && !hayPedidos)
    {

        alert(
            'Agrega al menos un producto o pedido.'
        );

        return;

    }


    // ========================================================
    // CREAR INPUTS DE PRODUCTOS
    // ========================================================

    Object.keys(cantidades)
        .forEach(function(id)
        {

            let cantidad =
                cantidades[id].cantidad;


            if (cantidad > 0)
            {

                let input =
                    document.getElementById(
                        'producto-input-' + id
                    );


                if (!input)
                {

                    input =
                        document.createElement(
                            'input'
                        );

                    input.type = 'hidden';

                    input.id =
                        'producto-input-' + id;

                    input.name =
                        'productos[' + id + ']';


                    document
                        .getElementById(
                            'formDespacho'
                        )
                        .appendChild(input);

                }


                input.value =
                    cantidad;

            }

        });


    // ========================================================
    // CONFIRMAR
    // ========================================================

    if (
        confirm(
            '¿Estás segura de que deseas despachar estos productos a la tienda?'
        )
    )
    {

        document
            .getElementById(
                'formDespacho'
            )
            .submit();

    }

}

</script>

@stop