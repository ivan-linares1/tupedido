@extends('layouts.app')

@section('title', 'Cotizaciones')

@section('contenido')

 <!-- Importación de JS -->
@vite(['resources/js/cotizaciones.js'])

<div class="container my-4">
    <h3 class="mb-4">Nueva Cotización</h3>

    <div class="row">
        <!-- COLUMNA IZQUIERDA: Datos del Cliente -->
        <div class="col-md-6">
            <h5>CLIENTES</h5>
            <div class="mb-3">
                <label>Cliente</label>
                <select class="form-select" name="cliente" id="selectCliente">
                    <option value="" selected disabled>Selecciona un cliente</option>
                    @foreach($clientes as $cliente)
                        <option value="{{ $cliente->CardCode }}">{{ $cliente->CardCode.' - '.$cliente->CardName }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label>Dirección Fiscal</label>
                <span id="direccionFiscal" class="form-control" style="white-space: pre-wrap; display: block;"></span>
            </div>

            <div class="mb-3">
                <label>Dirección de Entrega</label>
                <span id="direccionEntrega" class="form-control" style="white-space: pre-wrap; display: block;"></span>
            </div>
        </div>

        <!-- COLUMNA DERECHA: Datos Generales -->
        <div class="col-md-6">
            <h5>GENERALES</h5>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label>Fecha de Creacion</label>
                    <input type="date" id="fechaCreacion" class="form-control" readonly>
                </div>
                <div class="col-md-4">
                    <label>Válido Entrega</label>
                    <input type="date" id="fechaEntrega" class="form-control" >
                </div>
            </div>

            <div class="mb-3">
                <label>Moneda</label>
                <select class="form-select" name="currency_id" id="selectMoneda" data-monedas='@json($monedas)' data-iva='@json($IVA)'>
                    <option value="" selected disabled>Selecciona una moneda</option>
                    @foreach($monedas as $moneda)
                        <option value="{{ $moneda->Currency_ID }}">{{ $moneda->Currency }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>



<div class="container my-4">
    <!-- TABS -->
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#contenido">Cotizacion</a>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="contenido">
            @include('components.cot_contenido', ['articulos' => $articulos])
        </div>
    </div>
</div>
@endsection
