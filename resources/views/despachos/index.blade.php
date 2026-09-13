```php
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
                Selecciona los productos y pedidos que serán enviados a tienda.
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

                                <span class="mx-2">|</span>

                                <span id="totalPedidos">
                                    0
                                </span>

                                pedidos RECOGERA

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
        {{-- RESUMEN PRODUCTOS --}}
        {{-- ===================================================== --}}

        <div class="card">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-clipboard-list"></i>

                    Productos seleccionados

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
        {{-- PEDIDOS RECOGERA --}}
        {{-- ===================================================== --}}

        <div class="card">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-shopping-bag"></i>

                    Pedidos RECOGERA

                </h3>

            </div>


            <div class="card-body">

                <p class="text-muted">
                    Selecciona los pedidos RECOGERA que serán enviados a tienda.
                </p>


                {{-- BUSCAR PEDIDO --}}

                <div class="mb-4">

                    <label>

                        <i class="fas fa-search"></i>

                        Buscar número de pedido

                    </label>

                    <input
                        type="text"
                        id="buscarPedido"
                        class="form-control form-control-lg"
                        placeholder="Escribe el número de pedido..."
                    >

                </div>


                {{-- LISTA DE PEDIDOS --}}

                <div
                    class="row"
                    id="listaPedidos"
                >

                    @forelse($pedidos ?? [] as $pedido)

                        <div
                            class="col-xl-3 col-lg-4 col-md-6 mb-3 pedido-item"
                            data-numero="{{ strtolower($pedido->numero_pedido) }}"
                        >

                            <div
                                class="card pedido-card h-100 shadow-sm"
                                id="pedido-card-{{ $pedido->id }}"
                            >

                                <div class="card-body">

                                    <div class="d-flex justify-content-between align-items-start">

                                        <div>

                                            <h4 class="font-weight-bold mb-1">

                                                #{{ $pedido->numero_pedido }}

                                            </h4>

                                            <div class="text-muted">

                                                {{ $pedido->cliente }}

                                            </div>

                                        </div>


                                        <div>

                                            <span class="badge badge-primary">
                                                RECOGERA
                                            </span>

                                        </div>

                                    </div>


                                    {{-- INFORMACIÓN DEL PEDIDO --}}

                                    @if(isset($pedido->detalles) && $pedido->detalles->count())

                                        <hr>

                                        @foreach($pedido->detalles as $detalle)

                                            <div class="d-flex justify-content-between mb-1">

                                                <span>
                                                    {{ $detalle->producto->nombre ?? 'Producto' }}
                                                </span>

                                                <strong>
                                                    x{{ $detalle->cantidad }}
                                                </strong>

                                            </div>

                                        @endforeach

                                    @elseif(isset($pedido->producto))

                                        <hr>

                                        <div class="d-flex justify-content-between">

                                            <span>
                                                {{ $pedido->producto->nombre }}
                                            </span>

                                            <strong>
                                                x{{ $pedido->cantidad }}
                                            </strong>

                                        </div>

                                    @endif


                                    {{-- SELECCIONAR --}}

                                    <button
                                        type="button"
                                        class="btn btn-outline-danger btn-block mt-3"
                                        onclick="seleccionarPedido({{ $pedido->id }})"
                                        id="btn-pedido-{{ $pedido->id }}"
                                    >

                                        <i class="fas fa-plus"></i>

                                        SELECCIONAR

                                    </button>

                                </div>

                            </div>

                        </div>

                    @empty

                        <div class="col-12">

                            <div class="alert alert-info">

                                <i class="fas fa-info-circle"></i>

                                No hay pedidos RECOGERA pendientes para despachar.

                            </div>

                        </div>

                    @endforelse

                </div>


                {{-- SIN RESULTADOS PEDIDOS --}}

                <div
                    id="sinPedidos"
                    class="text-center text-muted py-4"
                    style="display:none;"
                >

                    <i class="fas fa-search fa-2x mb-2"></i>

                    <p>
                        No se encontró ningún pedido.
                    </p>

                </div>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- RESUMEN PEDIDOS --}}
        {{-- ===================================================== --}}

        <div class="card">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-file-invoice"></i>

                    Pedidos seleccionados

                </h3>

            </div>


            <div class="card-body">

                <div id="resumenPedidos">

                    <p class="text-muted">
                        Todavía no seleccionaste pedidos.
                    </p>

                </div>

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


        {{-- ===================================================== --}}
        {{-- INPUTS OCULTOS --}}
        {{-- ===================================================== --}}

        <input
            type="hidden"
            name="chofer_id"
            id="chofer_id"
        >


        {{-- AQUÍ SE CREARÁN LOS INPUTS DE PRODUCTOS --}}

        <div id="inputsProductos"></div>


        {{-- AQUÍ SE CREARÁN LOS INPUTS DE PEDIDOS --}}

        <div id="inputsPedidos"></div>


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


    .pedido-card {
        border: 2px solid #eeeeee;
        transition: all 0.2s ease;
    }

    .pedido-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.12) !important;
    }


    .pedido-seleccionado {
        border: 2px solid #28a745 !important;
        background: #f8fff9;
    }

</style>

@stop


@section('js')

<script>


// ============================================================
// VARIABLES
// ============================================================

let cantidades = {};

let pedidosSeleccionados = {};


// ============================================================
// AUMENTAR PRODUCTO
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
// DISMINUIR PRODUCTO
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
// MOSTRAR RESUMEN PRODUCTOS
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
// SELECCIONAR PEDIDO
// ============================================================

function seleccionarPedido(id)
{

    let tarjeta =
        document.getElementById('pedido-card-' + id);

    let boton =
        document.getElementById('btn-pedido-' + id);


    if (pedidosSeleccionados[id])
    {

        delete pedidosSeleccionados[id];


        tarjeta.classList.remove('pedido-seleccionado');


        boton.classList.remove('btn-success');

        boton.classList.add('btn-outline-danger');


        boton.innerHTML =
            '<i class="fas fa-plus"></i> SELECCIONAR';

    }
    else
    {

        pedidosSeleccionados[id] = true;


        tarjeta.classList.add('pedido-seleccionado');


        boton.classList.remove('btn-outline-danger');

        boton.classList.add('btn-success');


        boton.innerHTML =
            '<i class="fas fa-check"></i> SELECCIONADO';

    }


    mostrarResumenPedidos();

    actualizarBarraDespacho();

}


// ============================================================
// MOSTRAR RESUMEN DE PEDIDOS
// ============================================================

function mostrarResumenPedidos()
{

    let contenedor =
        document.getElementById('resumenPedidos');


    let ids =
        Object.keys(pedidosSeleccionados);


    if (ids.length === 0)
    {

        contenedor.innerHTML = `

            <p class="text-muted">
                Todavía no seleccionaste pedidos.
            </p>

        `;

        return;

    }


    let html = '';


    ids.forEach(function(id)
    {

        let tarjeta =
            document.getElementById('pedido-card-' + id);


        if (tarjeta)
        {

            let numero =
                tarjeta.querySelector('h4')?.innerText ?? 'Pedido';


            let cliente =
                tarjeta.querySelector('.text-muted')?.innerText ?? '';


            html += `

                <div
                    class="d-flex justify-content-between
                           align-items-center
                           border-bottom py-3"
                >

                    <div>

                        <strong>
                            ${numero}
                        </strong>

                        <div class="text-muted">
                            ${cliente}
                        </div>

                    </div>


                    <span class="badge badge-success">
                        SELECCIONADO
                    </span>

                </div>

            `;

        }

    });


    contenedor.innerHTML = html;

}


// ============================================================
// ACTUALIZAR BARRA
// ============================================================

function actualizarBarraDespacho()
{

    let total = 0;


    Object.values(cantidades).forEach(function(producto)
    {

        total += producto.cantidad;

    });


    let totalPedidos =
        Object.keys(pedidosSeleccionados).length;


    let barra =
        document.getElementById('barraDespacho');


    if (total > 0 || totalPedidos > 0)
    {

        barra.style.display = 'block';

    }
    else
    {

        barra.style.display = 'none';

    }


    document.getElementById('totalProductos')
        .innerText = total;


    document.getElementById('totalPedidos')
        .innerText = totalPedidos;

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


                filtrarProductos(categoria);

            }
        );

    });


// ============================================================
// FILTRAR PRODUCTOS
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
// BUSCADOR PRODUCTOS
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
// BUSCADOR PEDIDOS
// ============================================================

document
    .getElementById('buscarPedido')
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
                .querySelectorAll('.pedido-item')
                .forEach(function(item)
                {

                    let numero =
                        item.dataset.numero;


                    if (
                        texto === '' ||
                        numero.includes(texto)
                    )
                    {

                        item.style.display = '';

                        visibles++;

                    }
                    else
                    {

                        item.style.display = 'none';

                    }

                });


            document
                .getElementById('sinPedidos')
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


            document
                .getElementById('chofer_id_barra')
                .value = this.value;

        }
    );


document
    .getElementById('chofer_id_barra')
    .addEventListener(
        'change',
        function()
        {

            document
                .getElementById('chofer_id')
                .value = this.value;


            document
                .getElementById('chofer_id_principal')
                .value = this.value;

        }
    );


// ============================================================
// ENVIAR DESPACHO
// ============================================================

function enviarDespacho()
{

    let chofer =
        document
            .getElementById('chofer_id_principal')
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
        Object.keys(pedidosSeleccionados).length > 0;


    if (!hayProductos && !hayPedidos)
    {

        alert(
            'Agrega al menos un producto o selecciona un pedido RECOGERA.'
        );

        return;

    }


    // ========================================================
    // LIMPIAR INPUTS ANTERIORES
    // ========================================================

    document
        .getElementById('inputsProductos')
        .innerHTML = '';


    document
        .getElementById('inputsPedidos')
        .innerHTML = '';


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
                    document.createElement('input');


                input.type = 'hidden';


                input.name =
                    'productos[' + id + ']';


                input.value =
                    cantidad;


                document
                    .getElementById('inputsProductos')
                    .appendChild(input);

            }

        });


    // ========================================================
    // CREAR INPUTS DE PEDIDOS
    // ========================================================

    Object.keys(pedidosSeleccionados)
        .forEach(function(id)
        {

            let input =
                document.createElement('input');


            input.type = 'hidden';


            input.name =
                'pedidos[]';


            input.value =
                id;


            document
                .getElementById('inputsPedidos')
                .appendChild(input);

        });


    // ========================================================
    // CONFIRMAR
    // ========================================================

    let mensaje =
        '¿Estás segura de que deseas despachar estos productos y pedidos a la tienda?';


    if (confirm(mensaje))
    {

        document
            .getElementById('formDespacho')
            .submit();

    }

}

</script>

@stop
```
