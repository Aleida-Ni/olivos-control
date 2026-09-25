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

                    <label for="buscarProducto">

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
                                                @json($producto->nombre)
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
                    Escribe el número de pedido para buscarlo.
                </p>


                {{-- BUSCAR PEDIDO --}}

                <div class="mb-4">

                    <label for="buscarPedido">

                        <i class="fas fa-search"></i>

                        Buscar número de pedido

                    </label>

                    <input
                        type="text"
                        id="buscarPedido"
                        class="form-control form-control-lg"
                        placeholder="Ejemplo: 56"
                        autocomplete="off"
                    >

                </div>


                {{-- CARGANDO --}}

                <div
                    id="cargandoPedido"
                    class="text-center py-4"
                    style="display:none;"
                >

                    <i class="fas fa-spinner fa-spin fa-2x"></i>

                    <p class="mt-2 mb-0">
                        Buscando pedido...
                    </p>

                </div>


                {{-- MENSAJE INICIAL --}}

                <div
                    id="mensajePedido"
                    class="alert alert-info"
                >

                    <i class="fas fa-info-circle"></i>

                    Escribe el número de pedido para buscar un pedido
                    RECOGERA de hoy.

                </div>


                {{-- LISTA DE PEDIDOS --}}

                <div
                    class="row"
                    id="listaPedidos"
                ></div>


                {{-- SIN PEDIDOS --}}

                <div
                    id="sinPedidos"
                    class="text-center text-muted py-4"
                    style="display:none;"
                >

                    <i class="fas fa-search fa-2x mb-2"></i>

                    <p>
                        No se encontró ningún pedido RECOGERA con ese número.
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

        <div id="inputsProductos"></div>

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


    .pedido-dato {
        margin-bottom: 6px;
    }


    .pedido-dato strong {
        min-width: 120px;
        display: inline-block;
    }


    .pedido-completo {
        max-height: 250px;
        overflow-y: auto;
    }

</style>

@stop


@section('js')

<script>

// ============================================================
// VARIABLES
// ============================================================

let cantidades = {};

let pedidosAPI = {};

let pedidoActualEncontrado = null;

let temporizadorBusqueda = null;


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
                        ${escapeHtml(producto.nombre)}
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
// BUSCAR PEDIDO RECOGERA EN API
// ============================================================

document
    .getElementById('buscarPedido')
    .addEventListener(
        'input',
        function()
        {

            let numero =
                this.value
                    .trim();


            clearTimeout(temporizadorBusqueda);


            document
                .getElementById('listaPedidos')
                .innerHTML = '';


            document
                .getElementById('sinPedidos')
                .style.display = 'none';


            document
                .getElementById('cargandoPedido')
                .style.display = 'none';


            pedidoActualEncontrado = null;


            if (numero === '')
            {

                document
                    .getElementById('mensajePedido')
                    .style.display = 'block';

                return;

            }


            document
                .getElementById('mensajePedido')
                .style.display = 'none';


            temporizadorBusqueda =
                setTimeout(
                    function()
                    {
                        buscarPedidoAPI(numero);
                    },
                    400
                );

        }
    );


// ============================================================
// CONSULTAR API
// ============================================================

async function buscarPedidoAPI(numero)
{
    let lista =
        document.getElementById('listaPedidos');

    let cargando =
        document.getElementById('cargandoPedido');

    let sinPedidos =
        document.getElementById('sinPedidos');


    cargando.style.display = 'block';

    sinPedidos.style.display = 'none';


    try {

        const respuesta = await fetch(
            `/api/pedidos/recogera/${encodeURIComponent(numero)}`,
            {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            }
        );


        const resultado =
            await respuesta.json();


        if (!respuesta.ok) {

            lista.innerHTML = '';

            sinPedidos.style.display = 'block';

            return;

        }


        if (!resultado.data) {

            lista.innerHTML = '';

            sinPedidos.style.display = 'block';

            return;

        }


        mostrarPedidoAPI(resultado.data);


    } catch (error) {

        console.error(
            'Error al consultar la API:',
            error
        );

        lista.innerHTML = '';

        sinPedidos.style.display = 'block';

    } finally {

        cargando.style.display = 'none';

    }
}


// ============================================================
// MOSTRAR PEDIDO ENCONTRADO
// ============================================================

function mostrarPedidoAPI(pedido)
{
    let lista =
        document.getElementById('listaPedidos');


    pedidoActualEncontrado =
        pedido;


    lista.innerHTML = '';


    let datosCompletos = '';


    /*
     * Mostrar toda la fila original
     * que viene del enlace.
     */

    if (
        pedido.fila &&
        typeof pedido.fila === 'object'
    ) {

        Object.entries(pedido.fila)
            .forEach(function ([campo, valor])
            {

                if (
                    valor === null ||
                    valor === undefined ||
                    String(valor).trim() === ''
                ) {
                    return;
                }


                datosCompletos += `

                    <div class="pedido-dato">

                        <strong>
                            ${escapeHtml(
                                formatearCampo(campo)
                            )}
                        </strong>

                        <span>
                            ${escapeHtml(valor)}
                        </span>

                    </div>

                `;

            });

    }


    let tarjeta =
        document.createElement('div');


    tarjeta.className =
        'col-xl-5 col-lg-6 col-md-8 col-sm-12 mb-3';


    tarjeta.innerHTML = `

        <div
            class="card pedido-card h-100 shadow-sm"
            id="pedido-card-api-${escapeHtml(
                String(pedido.numero_pedido)
            )}"
        >

            <div class="card-body">

                {{-- CABECERA --}}

                <div
                    class="d-flex
                           justify-content-between
                           align-items-start"
                >

                    <div>

                        <h3 class="font-weight-bold mb-1">

                            #${escapeHtml(
                                String(
                                    pedido.numero_pedido
                                    || ''
                                )
                            )}

                        </h3>

                        <div class="text-muted">

                            ${escapeHtml(
                                pedido.cliente ||
                                'Sin cliente'
                            )}

                        </div>

                    </div>


                    <span class="badge badge-primary">

                        RECOGERA

                    </span>

                </div>


                <hr>


                {{-- DATOS PRINCIPALES --}}

                <div class="pedido-dato">

                    <strong>
                        Cliente:
                    </strong>

                    <span>
                        ${escapeHtml(
                            pedido.cliente ||
                            'No registrado'
                        )}
                    </span>

                </div>


                <div class="pedido-dato">

                    <strong>
                        Teléfono:
                    </strong>

                    <span>
                        ${escapeHtml(
                            pedido.telefono ||
                            'No registrado'
                        )}
                    </span>

                </div>


                <div class="pedido-dato">

                    <strong>
                        Fecha:
                    </strong>

                    <span>
                        ${escapeHtml(
                            pedido.fecha ||
                            ''
                        )}
                    </span>

                </div>


                <div class="pedido-dato">

                    <strong>
                        Pago:
                    </strong>

                    <span>
                        ${escapeHtml(
                            pedido.transferencia ||
                            'No registrado'
                        )}
                    </span>

                </div>


                <div class="pedido-dato">

                    <strong>
                        Saldo:
                    </strong>

                    <span>
                        ${escapeHtml(
                            pedido.saldo ||
                            '0'
                        )}
                    </span>

                </div>


                {{-- INFORMACIÓN COMPLETA --}}

                <hr>

                <details>

                    <summary
                        class="text-primary"
                        style="cursor:pointer;"
                    >
                        <i class="fas fa-list"></i>
                        Ver toda la información
                    </summary>


                    <div class="pedido-completo mt-3">

                        ${
                            datosCompletos ||
                            '<p class="text-muted">No hay más información disponible.</p>'
                        }

                    </div>

                </details>


                {{-- SELECCIONAR --}}

                <button
                    type="button"
                    class="btn btn-outline-danger btn-block mt-3"
                    onclick="seleccionarPedidoPorNumero(
                        ${JSON.stringify(
                            String(
                                pedido.numero_pedido || ''
                            )
                        )}
                    )"
                >

                    <i class="fas fa-plus"></i>

                    SELECCIONAR

                </button>

            </div>

        </div>

    `;


    lista.appendChild(tarjeta);
}


// ============================================================
// SELECCIONAR PEDIDO
// ============================================================

function seleccionarPedidoPorNumero(numero)
{
    let pedido =
        pedidoActualEncontrado;


    if (
        !pedido ||
        String(pedido.numero_pedido) !== String(numero)
    ) {

        return;

    }


    let clave =
        String(pedido.numero_pedido);


    let tarjeta =
        document.getElementById(
            'pedido-card-api-' + clave
        );


    if (pedidosAPI[clave]) {

        delete pedidosAPI[clave];


        if (tarjeta) {

            tarjeta
                .querySelector('button')
                .classList.remove('btn-success');

            tarjeta
                .querySelector('button')
                .classList.add('btn-outline-danger');

            tarjeta
                .querySelector('button')
                .innerHTML =
                    '<i class="fas fa-plus"></i> SELECCIONAR';

            tarjeta
                .classList.remove(
                    'pedido-seleccionado'
                );

        }

    }
    else {

        pedidosAPI[clave] =
            pedido;


        if (tarjeta) {

            tarjeta
                .querySelector('button')
                .classList.remove(
                    'btn-outline-danger'
                );

            tarjeta
                .querySelector('button')
                .classList.add(
                    'btn-success'
                );

            tarjeta
                .querySelector('button')
                .innerHTML =
                    '<i class="fas fa-check"></i> SELECCIONADO';

            tarjeta
                .classList.add(
                    'pedido-seleccionado'
                );

        }

    }


    mostrarResumenPedidos();

    actualizarBarraDespacho();

    prepararInputsPedidos();

}


// ============================================================
// RESUMEN PEDIDOS
// ============================================================

function mostrarResumenPedidos()
{
    let contenedor =
        document.getElementById(
            'resumenPedidos'
        );


    let pedidos =
        Object.values(pedidosAPI);


    if (pedidos.length === 0) {

        contenedor.innerHTML = `

            <p class="text-muted">
                Todavía no seleccionaste pedidos.
            </p>

        `;

        return;
    }


    let html = '';


    pedidos.forEach(function(pedido)
    {

        html += `

            <div
                class="d-flex
                       justify-content-between
                       align-items-center
                       border-bottom py-3"
            >

                <div>

                    <strong>
                        #${escapeHtml(
                            String(
                                pedido.numero_pedido
                            )
                        )}
                    </strong>

                    <div class="text-muted">

                        ${escapeHtml(
                            pedido.cliente ||
                            'Sin cliente'
                        )}

                    </div>

                </div>


                <span class="badge badge-success">

                    SELECCIONADO

                </span>

            </div>

        `;

    });


    contenedor.innerHTML =
        html;
}


// ============================================================
// ACTUALIZAR BARRA DE DESPACHO
// ============================================================

function actualizarBarraDespacho()
{
    let total =
        0;


    Object.values(cantidades)
        .forEach(function(producto)
        {

            total +=
                Number(producto.cantidad || 0);

        });


    let totalPedidos =
        Object.keys(pedidosAPI).length;


    let barra =
        document.getElementById(
            'barraDespacho'
        );


    if (
        total > 0 ||
        totalPedidos > 0
    ) {

        barra.style.display =
            'block';

    }
    else {

        barra.style.display =
            'none';

    }


    document
        .getElementById(
            'totalProductos'
        )
        .innerText =
            total;


    document
        .getElementById(
            'totalPedidos'
        )
        .innerText =
            totalPedidos;
}


// ============================================================
// PREPARAR INPUTS DE PEDIDOS
// ============================================================

function prepararInputsPedidos()
{
    let contenedor =
        document.getElementById(
            'inputsPedidos'
        );


    contenedor.innerHTML =
        '';


    let pedidos =
        Object.values(pedidosAPI);


    pedidos.forEach(function(pedido)
    {

        /*
         * Por ahora guardamos los datos básicos del pedido.
         *
         * En el siguiente paso modificaremos
         * DespachoController::store() para procesarlos
         * correctamente.
         */

        let numero =
            document.createElement('input');

        numero.type =
            'hidden';

        numero.name =
            'pedidos_excel[' +
            escapeInputName(
                pedido.numero_pedido
            ) +
            '][numero_pedido]';

        numero.value =
            pedido.numero_pedido || '';

        contenedor.appendChild(
            numero
        );


        let cliente =
            document.createElement('input');

        cliente.type =
            'hidden';

        cliente.name =
            'pedidos_excel[' +
            escapeInputName(
                pedido.numero_pedido
            ) +
            '][cliente]';

        cliente.value =
            pedido.cliente || '';

        contenedor.appendChild(
            cliente
        );


        let tipo =
            document.createElement('input');

        tipo.type =
            'hidden';

        tipo.name =
            'pedidos_excel[' +
            escapeInputName(
                pedido.numero_pedido
            ) +
            '][tipo_entrega]';

        tipo.value =
            pedido.tipo_entrega || 'RECOGERA';

        contenedor.appendChild(
            tipo
        );
    });
}


// ============================================================
// ESCAPAR NOMBRE DE INPUT
// ============================================================

function escapeInputName(valor)
{
    return String(valor)
        .replace(/[^a-zA-Z0-9_-]/g, '_');
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
                    .querySelectorAll(
                        '.categoria-btn'
                    )
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
// FILTRAR PRODUCTOS
// ============================================================

function filtrarProductos(categoria)
{
    let visibles =
        0;


    document
        .querySelectorAll(
            '.producto-card'
        )
        .forEach(function(card)
        {

            let categoriaProducto =
                card.dataset.categoria;


            if (
                categoria === 'todos' ||
                categoriaProducto === categoria
            )
            {

                card.style.display =
                    '';

                visibles++;

            }
            else
            {

                card.style.display =
                    'none';

            }

        });


    document
        .getElementById(
            'sinResultados'
        )
        .style.display =
            visibles === 0
                ? 'block'
                : 'none';
}


// ============================================================
// BUSCADOR PRODUCTOS
// ============================================================

document
    .getElementById(
        'buscarProducto'
    )
    .addEventListener(
        'input',
        function()
        {

            let texto =
                this.value
                    .toLowerCase()
                    .trim();


            let visibles =
                0;


            document
                .querySelectorAll(
                    '.producto-card'
                )
                .forEach(function(card)
                {

                    let nombre =
                        card.dataset.nombre;


                    if (
                        texto === '' ||
                        nombre.includes(texto)
                    )
                    {

                        card.style.display =
                            '';

                        visibles++;

                    }
                    else
                    {

                        card.style.display =
                            'none';

                    }

                });


            document
                .getElementById(
                    'sinResultados'
                )
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
    .getElementById(
        'chofer_id_principal'
    )
    .addEventListener(
        'change',
        function()
        {

            document
                .getElementById(
                    'chofer_id'
                )
                .value =
                    this.value;


            document
                .getElementById(
                    'chofer_id_barra'
                )
                .value =
                    this.value;

        }
    );


document
    .getElementById(
        'chofer_id_barra'
    )
    .addEventListener(
        'change',
        function()
        {

            document
                .getElementById(
                    'chofer_id'
                )
                .value =
                    this.value;


            document
                .getElementById(
                    'chofer_id_principal'
                )
                .value =
                    this.value;

        }
    );


// ============================================================
// ENVIAR DESPACHO
// ============================================================

function enviarDespacho()
{
    let chofer =
        document
            .getElementById(
                'chofer_id_principal'
            )
            .value;


    if (!chofer)
    {

        alert(
            'Primero selecciona el chofer.'
        );

        return;

    }


    document
        .getElementById(
            'chofer_id'
        )
        .value =
            chofer;


    let hayProductos =
        Object.values(cantidades)
            .some(
                producto =>
                    Number(producto.cantidad) > 0
            );


    let hayPedidos =
        Object.keys(pedidosAPI).length > 0;


    if (
        !hayProductos &&
        !hayPedidos
    )
    {

        alert(
            'Agrega al menos un producto o selecciona un pedido RECOGERA.'
        );

        return;

    }


    // ========================================================
    // LIMPIAR INPUTS PRODUCTOS
    // ========================================================

    document
        .getElementById(
            'inputsProductos'
        )
        .innerHTML =
            '';


    // ========================================================
    // CREAR INPUTS PRODUCTOS
    // ========================================================

    Object.keys(cantidades)
        .forEach(function(id)
        {

            let cantidad =
                Number(
                    cantidades[id].cantidad
                );


            if (cantidad > 0)
            {

                let input =
                    document.createElement(
                        'input'
                    );


                input.type =
                    'hidden';


                input.name =
                    'productos[' +
                    id +
                    ']';


                input.value =
                    cantidad;


                document
                    .getElementById(
                        'inputsProductos'
                    )
                    .appendChild(
                        input
                    );

            }

        });


    /*
     * Los pedidos de la API ya fueron preparados
     * en prepararInputsPedidos().
     *
     * Aún NO cambiamos el nombre a pedidos[],
     * porque el DespachoController todavía necesita
     * ser adaptado para recibir estos pedidos correctamente.
     */


    let mensaje =
        hayPedidos
            ? '¿Estás segura de que deseas enviar estos pedidos RECOGERA y productos a tienda?'
            : '¿Estás segura de que deseas despachar estos productos a la tienda?';


    if (
        confirm(mensaje)
    )
    {

        document
            .getElementById(
                'formDespacho'
            )
            .submit();

    }

}


// ============================================================
// ESCAPAR HTML
// ============================================================

function escapeHtml(text)
{
    let div =
        document.createElement(
            'div'
        );

    div.innerText =
        text ?? '';

    return div.innerHTML;
}


// ============================================================
// FORMATEAR NOMBRE DE CAMPO
// ============================================================

function formatearCampo(campo)
{
    return String(campo)
        .replace(/_/g, ' ')
        .replace(/\b\w/g, function(letra)
        {
            return letra.toUpperCase();
        });
}

</script>

@stop