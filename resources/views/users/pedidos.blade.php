@extends('layouts.app')

@section('title', 'Cotizaciones')
@section('contenido')
    <div class="table-responsive mt-4">
        <table class="table table-bordered table-striped">
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
