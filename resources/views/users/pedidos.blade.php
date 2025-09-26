@extends('layouts.app')

@section('title', 'Cotizaciones')
@section('contenido')

<div class="table-responsive mt-4">
    <h3 class="mb-3 fw-bold">PEDIDOS</h3>

    <a href="{{ route('NuevaPedido') }}" class="btn btn-primary">Nuevo Pedido</a>

    <table class="table table-bordered table-striped m-3">
        <thead class="table-info  text-center">
            <tr>
                <th>Folio</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Zona de Ventas</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
        @foreach($pedidos as $pedido)
            <tr>
                <td>
                    <a href="{{ route('detallesP', $pedido->CotizacionDocEntry) }}" 
                        style="cursor: pointer; color: blue; text-decoration: underline;">
                        PE - {{ $pedido->DocEntry }}
                    </a>
                </td>
                <td>{{ \Carbon\Carbon::parse($pedido->CotizacionFecha)->format('d-m-Y') }}</td>
                <td>{{ $pedido->Cliente }}</td>
                <td>{{ $pedido->Vendedor }}</td>
                <td>${{ number_format($pedido->Total, 2) }} {{ $pedido->Moneda }}</td>
            </tr>
        @endforeach
    </tbody>
    </table>
</div>
    
@endsection
