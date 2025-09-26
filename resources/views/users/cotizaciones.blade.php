@extends('layouts.app')

@section('title', 'Cotizaciones')

@section('contenido')

<div class="table-responsive mt-4">
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
            @foreach($cotizaciones as $cotizacion)
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
            @endforeach
        </tbody>
    </table>
</div>

@endsection
