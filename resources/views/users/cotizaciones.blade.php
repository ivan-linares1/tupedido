@extends('layouts.app')

@section('title', 'Cotizaciones')

@section('contenido')

<div class="table-responsive mt-4">
    <h3 class="mb-3 fw-bold">COTIZACIONES</h3>

    <a href="{{ route('NuevaCotizacion') }}" class="btn btn-primary">Nueva Cotización</a>

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
            @forelse($cotizaciones as $cotizacion)
                <tr>
                    <td>
                        <a href="{{ route('detalles', $cotizacion->DocEntry) }}" 
                        style="cursor: pointer; color: blue; text-decoration: underline;">
                        CO - {{ $cotizacion->DocEntry }}
                        </a>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($cotizacion->DocDate)->format('d-m-Y') }}</td>
                    <td>{{ $cotizacion->CardName }}</td>
                    <td>{{ $cotizacion->vendedor_nombre }}</td>
                    <td>${{ number_format($cotizacion->Total, 2) }} {{ $cotizacion->moneda_nombre }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Sin cotizaciones disponibles</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
