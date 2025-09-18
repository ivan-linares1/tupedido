@extends('layouts.app')

@section('title', 'Cotizaciones')

@section('contenido')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

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
                @foreach($cotizaciones as $cotizacion)
                    <tr>
                        <td>
                            <a href="#" 
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
