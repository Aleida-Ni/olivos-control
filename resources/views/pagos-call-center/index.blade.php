@extends('adminlte::page')

@section('title', 'Pagos Call Center')

@section('content_header')
    <h1><i class="fas fa-phone-alt"></i> Pagos Call Center</h1>
@stop

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Listado de pagos y pedidos</h3>
        </div>
        <div class="card-body p-0">
            @if($pedidos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Pedido</th>
                                <th>Cliente</th>
                                <th>Producto</th>
                                <th>Total</th>
                                <th>Pagado</th>
                                <th>Saldo</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pedidos as $pedido)
                                @php
                                    $totalPagado = (float) ($pedido->pagos->sum('monto') ?? 0);
                                    $saldo = max(0, (float) ($pedido->total ?? 0) - $totalPagado);
                                @endphp
                                <tr>
                                    <td>#{{ $pedido->numero_pedido ?? $pedido->id }}</td>
                                    <td>{{ $pedido->cliente ?? 'Sin cliente' }}</td>
                                    <td>{{ $pedido->producto->nombre ?? 'Sin producto' }}</td>
                                    <td>Bs {{ number_format((float) ($pedido->total ?? 0), 2) }}</td>
                                    <td>Bs {{ number_format($totalPagado, 2) }}</td>
                                    <td>Bs {{ number_format($saldo, 2) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $saldo > 0 ? 'warning' : 'success' }}">
                                            {{ $saldo > 0 ? 'PARCIAL' : 'PAGADO' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#pagoModal{{ $pedido->id }}">
                                            <i class="fas fa-money-bill-wave"></i> Registrar pago
                                        </button>
                                    </td>
                                </tr>

                                <div class="modal fade" id="pagoModal{{ $pedido->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="{{ route('pagos-call-center.store') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="pedido_id" value="{{ $pedido->id }}">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Registrar pago</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Monto</label>
                                                        <input type="number" step="0.01" min="0" name="monto" class="form-control" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Método de pago</label>
                                                        <select name="metodo_pago" class="form-control" required>
                                                            <option value="EFECTIVO">Efectivo</option>
                                                            <option value="TARJETA">Tarjeta</option>
                                                            <option value="TRANSFERENCIA">Transferencia</option>
                                                            <option value="QR">QR</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Observación</label>
                                                        <textarea name="observacion" class="form-control" rows="3"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                                    <button type="submit" class="btn btn-primary">Guardar pago</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center text-muted py-5">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p class="mb-0">No hay pedidos registrados para pagos.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@stop
