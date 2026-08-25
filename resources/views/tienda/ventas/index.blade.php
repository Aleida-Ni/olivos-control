@extends('adminlte::page')

@section('title', 'Ventas')

@section('css')
<style>
    .ventas-shell { max-width: 1500px; }
    .ventas-hero { background: linear-gradient(135deg, #123b5d, #197b8f); color: #fff; border-radius: 12px; }
    .ventas-hero .text-muted { color: rgba(255, 255, 255, .75) !important; }
    .cart-card { position: sticky; top: 1rem; }
    .producto-btn { min-height: 168px; transition: transform .18s ease, box-shadow .18s ease; }
    .producto-btn:hover { transform: translateY(-3px); box-shadow: 0 8px 18px rgba(18, 59, 93, .16); }
    .producto-btn:disabled { opacity: .6; cursor: not-allowed; }
    .stock-pill { font-size: 11px; }
    .receipt-paper { max-width: 560px; margin: auto; font-family: Consolas, monospace; }
    .receipt-paper table { width: 100%; }
    .payment-panel { border-top: 1px solid #e9ecef; margin-top: 1rem; padding-top: 1rem; }
    .payment-methods .btn { flex: 1; }
    @media print {
        body * { visibility: hidden !important; }
        #fichaVenta, #fichaVenta * { visibility: visible !important; }
        #fichaVenta { position: absolute; left: 0; top: 0; width: 100%; }
        .modal-footer, .close { display: none !important; }
    }
</style>
@stop

@section('content_header')

    <h1>
        <i class="fas fa-shopping-cart"></i>
        Ventas
    </h1>

@stop


@section('content')

<div class="container-fluid ventas-shell">

    <div class="ventas-hero p-4 mb-4 shadow-sm">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <p class="text-uppercase small mb-1">Punto de venta</p>
                <h2 class="mb-1">Nueva venta</h2>
                <p class="text-muted mb-0">Selecciona productos y cobra en pocos pasos.</p>
            </div>
            <i class="fas fa-cash-register fa-3x mt-3 mt-md-0"></i>
        </div>
    </div>

    <div class="row">

        {{-- ========================================= --}}
        {{-- CARRITO --}}
        {{-- ========================================= --}}

        <div class="col-md-4">

            <div class="card cart-card shadow-sm">

                <div class="card-header bg-primary">

                    <h3 class="card-title mb-0">
                        <i class="fas fa-receipt mr-2"></i>
                        Venta actual
                    </h3>

                </div>


                <div class="card-body">

                    <div id="carrito">

                        <p class="text-muted text-center">
                            No hay productos agregados.
                        </p>

                    </div>

                </div>


                <div class="card-footer">

                    <div class="d-flex justify-content-between text-muted small mb-2">
                        <span>Subtotal</span>
                        <span id="subtotalVenta">0.00 Bs</span>
                    </div>

                    <div class="d-flex justify-content-between text-success small mb-2">
                        <span>Descuento</span>
                        <span id="descuentoVenta">0.00 Bs</span>
                    </div>

                    <div class="d-flex justify-content-between">

                        <strong>
                            TOTAL
                        </strong>

                        <strong
                            id="totalVenta"
                            style="font-size: 24px;"
                        >
                            0.00 Bs
                        </strong>

                    </div>

                    <div class="payment-panel">
                        <div class="form-row">
                            <div class="form-group col-sm-6 mb-2">
                                <label for="ventaNit" class="small mb-1">NIT / CI <span class="text-muted">(opcional)</span></label>
                                <input type="text" id="ventaNit" class="form-control form-control-sm" placeholder="Sin especificar">
                            </div>
                            <div class="form-group col-sm-6 mb-2">
                                <label for="ventaCliente" class="small mb-1">Cliente <span class="text-muted">(opcional)</span></label>
                                <input type="text" id="ventaCliente" class="form-control form-control-sm" placeholder="Consumidor final">
                            </div>
                        </div>

                        <label class="small mb-1">Forma de pago</label>
                        <div class="btn-group btn-group-toggle d-flex payment-methods mb-3">
                            <label class="btn btn-success btn-sm active">
                                <input type="radio" name="metodo_pago_venta" value="EFECTIVO" checked> <i class="fas fa-money-bill-wave"></i> Efectivo
                            </label>
                            <label class="btn btn-primary btn-sm">
                                <input type="radio" name="metodo_pago_venta" value="TARJETA"> <i class="fas fa-credit-card"></i> Tarjeta
                            </label>
                            <label class="btn btn-info btn-sm">
                                <input type="radio" name="metodo_pago_venta" value="QR"> <i class="fas fa-qrcode"></i> QR
                            </label>
                        </div>

                        <div id="ventaEfectivoArea" class="form-row">
                            <div class="form-group col-6 mb-2">
                                <label for="ventaEfectivo" class="small mb-1">Recibido</label>
                                <input type="number" id="ventaEfectivo" class="form-control form-control-sm" min="0" step="0.01" oninput="calcularCambio()">
                            </div>
                            <div class="form-group col-6 mb-2">
                                <label for="ventaCambio" class="small mb-1">Cambio</label>
                                <input type="text" id="ventaCambio" class="form-control form-control-sm" readonly>
                            </div>
                        </div>

                        <div class="form-group mb-2">
                            <label for="ventaDescuento" class="small mb-1">Descuento (%)</label>
                            <div class="input-group input-group-sm">
                                <input type="number" id="ventaDescuento" class="form-control" min="0" max="100" step="0.01" value="0" oninput="actualizarTotales()">
                                <div class="input-group-append"><span class="input-group-text">%</span></div>
                            </div>
                        </div>
                    </div>


                    <button
                        type="button"
                        class="btn btn-success btn-lg btn-block mt-3"
                        onclick="confirmarVenta()"
                        id="confirmarVentaBtn"
                    >

                        <i class="fas fa-cash-register"></i>

                        REGISTRAR E IMPRIMIR

                    </button>

                </div>

            </div>

        </div>


        {{-- ========================================= --}}
        {{-- PRODUCTOS --}}
        {{-- ========================================= --}}

        <div class="col-md-8">

            <div class="card">

                <div class="card-header">

                    <div class="d-flex justify-content-between">

                        <h3 class="card-title">
                            Productos
                        </h3>

                        <div class="input-group" style="max-width: 320px;">
                        <input
                            type="text"
                            id="buscarProducto"
                            class="form-control"
                            style="width: 300px;"
                            placeholder="Buscar por nombre..."
                        >
                        <div class="input-group-append"><span class="input-group-text"><i class="fas fa-search"></i></span></div>
                        </div>

                    </div>

                </div>


                <div class="card-body">

                    {{-- CATEGORÍAS --}}

                    <div class="mb-3">

                        <button
                            type="button"
                            class="btn btn-danger categoria-btn mr-2 mb-2"
                            data-categoria="todos"
                        >
                            Todos
                        </button>

                        @foreach(
                            $productos
                                ->pluck('categoria')
                                ->unique('id')
                            as $categoria
                        )

                            <button
                                type="button"
                                class="btn btn-secondary categoria-btn mr-2 mb-2"
                                data-categoria="{{ strtolower($categoria->nombre) }}"
                            >

                                {{ $categoria->nombre }}

                            </button>

                        @endforeach

                    </div>


                    {{-- PRODUCTOS --}}

                    <div
                        class="row"
                        id="listaProductos"
                    >

                        @foreach($productos as $producto)

                            @php($stock = $producto->inventario->sum('stock'))
                            <div
                                class="col-lg-3 col-md-4 col-sm-6 mb-3 producto-card"
                                data-categoria="{{ strtolower($producto->categoria->nombre) }}"
                                data-nombre="{{ strtolower($producto->nombre) }}"
                                data-stock="{{ $stock }}"
                            >

                                <button
                                    type="button"
                                    class="btn btn-light border w-100 h-100 producto-btn"
                                    @disabled($stock < 1)
                                    onclick="agregarProducto(
                                        {{ $producto->id }},
                                        '{{ addslashes($producto->nombre) }}',
                                        {{ $producto->precio }}
                                    )"
                                >

                                    <div
                                        style="font-size: 35px;"
                                    >
                                        📦
                                    </div>


                                    <strong class="d-block mb-2">
                                        {{ $producto->nombre }}
                                    </strong>


                                    <br>


                                    <small class="text-muted">

                                        {{ $producto->categoria->nombre }}

                                    </small>


                                    <br>


                                    <strong class="text-danger">

                                        {{ number_format($producto->precio, 2) }}
                                        Bs

                                    </strong>
                                    <span class="badge {{ $stock > 0 ? 'badge-success' : 'badge-secondary' }} stock-pill d-block mt-2">
                                        {{ $stock > 0 ? $stock . ' disponibles' : 'Sin stock' }}
                                    </span>

                                </button>

                            </div>

                        @endforeach

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- ================================================= --}}
{{-- MODAL DE PAGO --}}
{{-- ================================================= --}}

<div
    class="modal fade"
    id="modalPago"
    tabindex="-1"
>

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header bg-success text-white">

                <h4 class="modal-title">

                    <i class="fas fa-money-bill-wave"></i>

                    Registrar pago

                </h4>

                <button
                    type="button"
                    class="close text-white"
                    data-dismiss="modal"
                >
                    ×
                </button>

            </div>


            <div class="modal-body">

                {{-- CLIENTE --}}

                <div class="form-group">

                    <label for="nit_ci">
                        NIT / CI
                    </label>

                    <input
                        type="text"
                        id="nit_ci"
                        class="form-control"
                        placeholder="Opcional"
                    >

                </div>


                <div class="form-group">

                    <label for="cliente">
                        Nombre
                    </label>

                    <input
                        type="text"
                        id="cliente"
                        class="form-control"
                        placeholder="Consumidor final"
                    >

                </div>


                <hr>

                <div class="form-group">
                    <label for="descuentoPorcentaje">Descuento (%)</label>
                    <div class="input-group">
                        <input
                            type="number"
                            id="descuentoPorcentaje"
                            class="form-control"
                            min="0"
                            max="100"
                            step="0.01"
                            value="0"
                            oninput="actualizarTotales()"
                        >
                        <div class="input-group-append"><span class="input-group-text">%</span></div>
                    </div>
                    <small class="form-text text-muted">Escribe 20 para aplicar un 20% de descuento.</small>
                </div>


                <h4>

                    Total:

                    <strong id="modalTotal">
                        0.00 Bs
                    </strong>

                </h4>


                {{-- MÉTODO DE PAGO --}}

                <div class="form-group">

                    <label>
                        Forma de pago
                    </label>


                    <div class="btn-group btn-group-toggle d-flex">

                        <label class="btn btn-success">

                            <input
                                type="radio"
                                name="metodo_pago"
                                value="EFECTIVO"
                                checked
                            >

                            💵 Efectivo

                        </label>


                        <label class="btn btn-primary">

                            <input
                                type="radio"
                                name="metodo_pago"
                                value="TARJETA"
                            >

                            💳 Tarjeta

                        </label>


                        <label class="btn btn-info">

                            <input
                                type="radio"
                                name="metodo_pago"
                                value="QR"
                            >

                            📱 QR

                        </label>

                    </div>

                </div>


                {{-- EFECTIVO --}}

                <div
                    id="efectivoArea"
                    class="form-group"
                >

                    <label>
                        Efectivo recibido
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        id="efectivo"
                        class="form-control form-control-lg"
                        oninput="calcularCambio()"
                    >


                    <label class="mt-2">
                        Cambio
                    </label>

                    <input
                        type="text"
                        id="cambio"
                        class="form-control form-control-lg"
                        readonly
                    >

                </div>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-dismiss="modal"
                >
                    Cancelar
                </button>


                <button
                    type="button"
                    class="btn btn-success"
                    onclick="confirmarVenta()"
                    id="confirmarVentaBtn"
                >

                    <i class="fas fa-check"></i>

                    REGISTRAR VENTA

                </button>

            </div>

        </div>

    </div>

</div>

<div class="modal fade" id="modalFicha" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title"><i class="fas fa-check-circle mr-2"></i>Venta registrada</h4>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="fichaVenta" class="receipt-paper"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="fas fa-print mr-1"></i> Imprimir ficha
                </button>
                <button type="button" class="btn btn-success" onclick="nuevaVenta()">
                    <i class="fas fa-plus mr-1"></i> Nueva venta
                </button>
            </div>
        </div>
    </div>
</div>

@stop

@section('js')

<script>

let carrito = {};


// ==========================================
// AGREGAR PRODUCTO
// ==========================================

function agregarProducto(id, nombre, precio)
{
    if (!carrito[id]) {

        carrito[id] = {
            id: id,
            nombre: nombre,
            precio: parseFloat(precio),
            cantidad: 0
        };

    }

    carrito[id].cantidad++;

    mostrarCarrito();
}


// ==========================================
// QUITAR PRODUCTO
// ==========================================

function quitarProducto(id)
{
    if (!carrito[id]) {
        return;
    }

    carrito[id].cantidad--;

    if (carrito[id].cantidad <= 0) {

        delete carrito[id];

    }

    mostrarCarrito();
}


// ==========================================
// MOSTRAR CARRITO
// ==========================================

function mostrarCarrito()
{
    let html = '';

    let total = 0;

    Object.values(carrito).forEach(producto => {

        let subtotal =
            producto.cantidad *
            producto.precio;

        total += subtotal;


        html += `

            <div class="border-bottom py-3">

                <div class="d-flex justify-content-between">

                    <strong>
                        ${producto.nombre}
                    </strong>

                    <strong>
                        ${subtotal.toFixed(2)} Bs
                    </strong>

                </div>


                <div class="d-flex justify-content-between mt-2">

                    <div>

                        <button
                            class="btn btn-sm btn-danger"
                            onclick="quitarProducto(${producto.id})"
                        >
                            −
                        </button>


                        <span class="mx-3">
                            ${producto.cantidad}
                        </span>


                        <button
                            class="btn btn-sm btn-success"
                            onclick="agregarProducto(
                                ${producto.id},
                                '${producto.nombre.replace(/'/g, "\\'")}',
                                ${producto.precio}
                            )"
                        >
                            +
                        </button>

                    </div>


                    <div class="input-group input-group-sm" style="max-width: 145px;">
                        <input
                            type="number"
                            class="form-control"
                            min="0"
                            step="0.01"
                            value="${producto.precio.toFixed(2)}"
                            oninput="actualizarPrecio(${producto.id}, this.value)"
                        >
                        <div class="input-group-append"><span class="input-group-text">Bs</span></div>
                    </div>

                </div>

            </div>

        `;

    });


    if (html === '') {

        html = `
            <p class="text-muted text-center">
                No hay productos agregados.
            </p>
        `;

    }


    document.getElementById('carrito').innerHTML =
        html;


    actualizarTotales();
}

function actualizarPrecio(id, precio)
{
    carrito[id].precio = Math.max(0, parseFloat(precio) || 0);
    mostrarCarrito();
}


// ==========================================
// TOTAL
// ==========================================

function obtenerTotal()
{
    return obtenerSubtotal() * (1 - obtenerDescuentoPorcentaje() / 100);
}

function obtenerSubtotal()
{
    return Object.values(carrito).reduce(
        (total, producto) => total + producto.cantidad * producto.precio,
        0
    );
}

function obtenerDescuentoPorcentaje()
{
    return Math.min(
        100,
        Math.max(0, parseFloat(document.getElementById('ventaDescuento').value) || 0)
    );
}

function actualizarTotales()
{
    let subtotal = obtenerSubtotal();
    let porcentaje = obtenerDescuentoPorcentaje();
    let descuento = subtotal * porcentaje / 100;
    let total = subtotal - descuento;

    document.getElementById('subtotalVenta').innerText = subtotal.toFixed(2) + ' Bs';
    document.getElementById('descuentoVenta').innerText = descuento.toFixed(2) + ' Bs';
    document.getElementById('totalVenta').innerText = total.toFixed(2) + ' Bs';
    document.getElementById('modalTotal').innerText = total.toFixed(2) + ' Bs';
}


// ==========================================
// ABRIR PAGO
// ==========================================

function abrirPago()
{
    if (Object.keys(carrito).length === 0) {

        alert(
            'Agrega al menos un producto.'
        );

        return;
    }


    actualizarTotales();


    $('#modalPago').modal('show');
}


// ==========================================
// CAMBIO
// ==========================================

function calcularCambio()
{
    let total = obtenerTotal();

    let efectivo =
        parseFloat(
            document.getElementById('ventaEfectivo').value
        ) || 0;


    let cambio =
        efectivo - total;


    if (cambio < 0) {

        document.getElementById('ventaCambio').value =
            'Falta ' +
            Math.abs(cambio).toFixed(2) +
            ' Bs';

        return;

    }


    document.getElementById('ventaCambio').value =
        cambio.toFixed(2) + ' Bs';
}


// ==========================================
// CAMBIO DE MÉTODO
// ==========================================

document
    .querySelectorAll('input[name="metodo_pago_venta"]')
    .forEach(function(radio)
    {
        radio.addEventListener('change', function()
        {

            let area =
                document.getElementById('ventaEfectivoArea');


            if (this.value === 'EFECTIVO') {

                area.style.display = 'block';

            } else {

                area.style.display = 'none';

            }

        });
    });


// ==========================================
// CATEGORÍAS
// ==========================================

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


                document
                    .querySelectorAll('.producto-card')
                    .forEach(function(card)
                    {

                        if (
                            categoria === 'todos' ||
                            card.dataset.categoria === categoria
                        ) {

                            card.style.display = '';

                        } else {

                            card.style.display = 'none';

                        }

                    });

            }
        );

    });


// ==========================================
// BUSCAR
// ==========================================

document
    .getElementById('buscarProducto')
    .addEventListener('input', function()
    {

        let texto =
            this.value
                .toLowerCase()
                .trim();


        document
            .querySelectorAll('.producto-card')
            .forEach(function(card)
            {

                let nombre =
                    card.dataset.nombre;


                card.style.display =
                    nombre.includes(texto)
                        ? ''
                        : 'none';

            });

    });


// ==========================================
// CONFIRMAR VENTA
// ==========================================

function confirmarVenta()
{
    let metodo =
        document.querySelector(
            'input[name="metodo_pago_venta"]:checked'
        ).value;


    let total =
        obtenerTotal();

    let efectivo =
        parseFloat(
            document.getElementById('ventaEfectivo').value
        ) || 0;


    if (metodo === 'EFECTIVO') {
        if (efectivo < total) {
            alert('El efectivo recibido no alcanza para cubrir la venta.');

            return;
        }

    }

    let boton = document.getElementById('confirmarVentaBtn');
    boton.disabled = true;
    boton.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Registrando...';

    fetch('{{ route('tienda.ventas.store') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            nit_ci: document.getElementById('ventaNit').value.trim(),
            cliente: document.getElementById('ventaCliente').value.trim(),
            metodo_pago: metodo,
            efectivo_recibido: metodo === 'EFECTIVO' ? efectivo : null,
            descuento_porcentaje: obtenerDescuentoPorcentaje(),
            items: Object.values(carrito).map(producto => ({
                id: producto.id,
                cantidad: producto.cantidad,
                precio: producto.precio
            }))
        })
    })
    .then(async response => {
        let data = await response.json();
        if (!response.ok) {
            throw new Error(data.message || Object.values(data.errors || {}).flat()[0] || 'No se pudo registrar la venta.');
        }
        return data.venta;
    })
    .then(venta => {
        $('#modalPago').modal('hide');
        mostrarFicha(venta);
        limpiarVenta();
        $('#modalFicha').modal('show');
        setTimeout(() => window.print(), 400);
    })
    .catch(error => alert(error.message))
    .finally(() => {
        boton.disabled = false;
        boton.innerHTML = '<i class="fas fa-check mr-1"></i> Registrar venta';
    });
}

function escaparHtml(texto)
{
    return String(texto ?? '').replace(/[&<>'"]/g, caracter => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;'
    }[caracter]));
}

function mostrarFicha(venta)
{
    let filas = venta.detalles.map(detalle => `
        <tr>
            <td>${escaparHtml(detalle.producto.nombre)} x${detalle.cantidad}</td>
            <td class="text-right">${Number(detalle.subtotal).toFixed(2)} Bs</td>
        </tr>
    `).join('');

    document.getElementById('fichaVenta').innerHTML = `
        <div class="text-center border-bottom pb-3 mb-3">
            <h3 class="mb-1">OLIVOS CONTROL</h3>
            <div>FICHA DE VENTA #${venta.id}</div>
            <small>${new Date(venta.created_at).toLocaleString('es-BO')}</small>
        </div>
        <p class="mb-1"><strong>Cliente:</strong> ${escaparHtml(venta.cliente || 'Consumidor final')}</p>
        <p class="mb-3"><strong>NIT / CI:</strong> ${escaparHtml(venta.nit_ci || 'Sin especificar')}</p>
        <table class="mb-3"><tbody>${filas}</tbody></table>
        <div class="d-flex justify-content-between"><span>Subtotal</span><span>${Number(venta.subtotal).toFixed(2)} Bs</span></div>
        <div class="d-flex justify-content-between text-success"><span>Descuento (${Number(venta.descuento_porcentaje || 0).toFixed(2)}%)</span><span>- ${Number(venta.descuento).toFixed(2)} Bs</span></div>
        <div class="border-top pt-2 d-flex justify-content-between"><strong>TOTAL</strong><strong>${Number(venta.total).toFixed(2)} Bs</strong></div>
        <p class="mt-2 mb-0"><strong>Pago:</strong> ${venta.metodo_pago}${venta.metodo_pago === 'EFECTIVO' ? ` | Recibido: ${Number(venta.efectivo_recibido).toFixed(2)} Bs | Cambio: ${Number(venta.cambio).toFixed(2)} Bs` : ''}</p>
    `;
}

function nuevaVenta()
{
    limpiarVenta();
    $('#modalFicha').modal('hide');
}

function limpiarVenta()
{
    carrito = {};
    document.getElementById('ventaNit').value = '';
    document.getElementById('ventaCliente').value = '';
    document.getElementById('ventaEfectivo').value = '';
    document.getElementById('ventaCambio').value = '';
    document.getElementById('ventaDescuento').value = '0';
    document.querySelector('input[name="metodo_pago_venta"][value="EFECTIVO"]').checked = true;
    document.getElementById('ventaEfectivoArea').style.display = 'flex';
    mostrarCarrito();
}

</script>

@stop