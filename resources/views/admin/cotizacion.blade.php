@extends('layouts.app')

@section('title', 'Cotizacion')

@section('contenido')

 <!-- Importación de JS -->
@vite(['resources/js/validaciones.js', 'resources/js/cotizaciones.js'])

<div class="container my-4">
    <h3 class="mb-4">
        @if($modo == 0)
            Nueva Cotización
        @else
            CO - {{ $cotizacion->DocEntry }}
        @endif
    </h3>

    <div class="row">
        <!-- COLUMNA IZQUIERDA: Datos del Cliente -->
        <div class="col-md-6">
            <h5>CLIENTES</h5>
            <div class="mb-3">
                <label>Cliente</label>
                <select class="form-select campo-editable" name="cliente" id="selectCliente" @if(isset($modo) && $modo == 1) disabled @endif>>
                    <option value="" selected disabled>Selecciona un cliente...</option>
                    @foreach($clientes as $cliente)
                        <option 
                            value="{{ $cliente->CardCode }}"  
                            data-phone="{{ $cliente->phone1 }}" 
                            data-email="{{ $cliente->{'e-mail'} }}"
                            data-cardname="{{ $cliente->CardName }}"
                            data-descuentos='@json($cliente->descuentos->flatMap(function($d) {
                                return $d->detalles->map(function($dd) {
                                    return [
                                        "ObjKey" => $dd->ObjKey,
                                        "Discount" => $dd->Discount
                                    ];
                                });
                            }))' @if(isset($cotizacion) && $cotizacion->CardCode == $cliente->CardCode) selected @endif >
                            {{ $cliente->CardCode.' - '.$cliente->CardName }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Aquí se mostrarán los datos -->
            <div id="infoCliente" class="mt-2" style="font-size: 14px;">
                <span id="telefono">{{ $cotizacion->Phone1 ?? '' }}</span><br>
                <span id="correo">{{ $cotizacion->E_Mail ?? '' }}</span><br>
            </div>

            <div class="mb-3">
                <label>Dirección Fiscal</label>
                <span id="direccionFiscal" class="form-control" style="white-space: pre-wrap; display: block;"> {{ $cotizacion->Address ?? '' }} </span>
            </div>

            <div class="mb-3">
                <label>Dirección de Entrega</label>
                <span id="direccionEntrega" class="form-control" style="white-space: pre-wrap; display: block;"> {{ $cotizacion->Address2 ?? '' }} </span>
            </div>
        </div>

        <!-- COLUMNA DERECHA: Datos Generales -->
        <div class="col-md-6">
            <h5>GENERALES</h5>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label>Fecha de Creacion</label>
                    <input type="date" id="fechaCreacion" class="form-control" value="{{ isset($cotizacion) ? $cotizacion->DocDate : '' }}" readonly>
                </div>
                <div class="col-md-4">
                    <label>Válido Entrega</label>
                    <input type="date" id="fechaEntrega" class="form-control" value="{{ isset($cotizacion) ? $cotizacion->DocDate : '' }}" @if(isset($modo) && $modo == 1) readonly @endif >
                </div>
            </div>

            <div class="mb-3">
                <label>Moneda</label>
                <select class="form-select campo-editable" name="currency_id" id="selectMoneda"
                    data-monedas='@json($monedas)' data-iva='@json($IVA)'
                    @if(isset($modo) && $modo == 1) disabled @endif>
                    <option value="" selected disabled>Selecciona una moneda</option>
                    @foreach($monedas as $moneda)
                        <option value="{{ $moneda->Currency_ID }}" @if($moneda->cambios->isEmpty()) disabled @endif @if(isset($cotizacion) && $cotizacion->DocCur == $moneda->Currency_ID) selected @endif > {{--si no hay monedas de cambio disponibles se inhabilita--}}
                            {{ $moneda->Currency }}
                            @if($moneda->cambios->isEmpty() && isset($modo) && $modo == 0) (Sin tipo de cambio) @endif
                        </option>
                    @endforeach
                </select>
            </div>

            @if( $vendedores )
                <div class="mb-3">
                    <label>Vendedores</label>
                    <select class="form-select campo-editable" name="vendedor_SlpCode" id="selectVendedor"  @if(isset($modo) && $modo == 1) disabled @endif>
                        <option value="" selected disabled>Selecciona un Vendedor</option>
                        @foreach($vendedores as $vendedor)
                            <option value="{{ $vendedor->SlpCode }}" @if(isset($cotizacion) && $cotizacion->SlpCode == $vendedor->SlpCode) selected @endif
                                data-SlpName="{{ $vendedor->SlpName }}">
                                {{ $vendedor->SlpCode. ' - ' .$vendedor->SlpName }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

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


<!-- Form para enviar la cotización -->
<form id="formCotizacion" action="{{ route('cotizacionSave') }}" method="POST">
    @csrf
    <!-- Datos del cliente -->
    <input type="hidden" name="cliente" id="clienteH">  {{--cardcode--}}
    <input type="hidden" name="fechaCreacion" id="fechaCreacionH"> {{--DocDate--}}
    <input type="hidden" name="fechaEntrega" id="fechaEntregaH"> {{--DocDue--}}
    <input type="hidden" name="CardName" id="CardNameH"> {{--CardName--}}
    <input type="hidden" name="SlpCode" id="SlpCodeH"> {{--SlpCode--}}
    <input type="hidden" name="phone1" id="phone1H"> {{--phone1--}}
    <input type="hidden" name="email" id="emailH"> {{--email--}}
    <input type="hidden" name="DocCur" id="DocCurH"> {{--DocCur--}}
    <input type="hidden" name="ShipToCode" id="ShipToCodeH"> {{--ShipToCode--}}
    <input type="hidden" name="PayToCode" id="PayToCodeH"> {{--PayToCode--}}
    <input type="hidden" name="direccionFiscal" id="direccionFiscalH"> {{--Address--}}
    <input type="hidden" name="direccionEntrega" id="direccionEntregaH"> {{--Address--}}
    <input type="hidden" name="TotalSinPromo" id="TotalSinPromoH"> {{--TotalSinPromo--}}
    <input type="hidden" name="Descuento" id="DescuentoH"> {{--Descuento--}}
    <input type="hidden" name="Subtotal" id="SubtotalH"> {{--Subtotal--}}
    <input type="hidden" name="iva" id="ivaH"> {{--Iva--}}
    <input type="hidden" name="total" id="totalH"> {{--Total--}}

    <!-- Artículos -->
    <input type="hidden" name="articulos" id="articulosH">
</form>

<div class="container my-4 d-flex justify-content-start gap-2">

    @if($modo == 0)
        <!-- Botón Cancelar -->
        <button type="button" class="btn btn-danger" onclick="window.location='{{ url('/') }}'">
            <i class="bi bi-x-circle"></i> Cancelar
        </button>

        <!-- Botón Guardar -->
        <button type="button" id="guardarCotizacion" class="btn btn-primary">
            <i class="bi bi-save"></i> Guardar
        </button>
    @else
        <!-- Botón PDF -->
        <button type="button" class="btn btn-danger" onclick="window.location='#'">
            <i class="bi bi-filetype-pdf"></i> PDF
        </button>

        <!-- Botón Editar -->
        <button type="button" id="editarCotizacion" class="btn btn-secondary">
            <i class="bi bi-pencil-square"></i> Editar
        </button>
    @endif
    

    <!-- Botón Pedido -->
    <button type="button" class="btn btn-success">
        <i class="bi bi-bag"></i> Pedido
    </button>
</div>

@endsection
