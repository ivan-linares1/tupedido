@extends('layouts.app')

@section('title', 'Cotizacion')

@section('contenido')

 <!-- Importación de JS -->
@vite(['resources/js/validaciones.js', 'resources/js/cotizaciones.js'])

<div class="container my-4">
    <h3 class="mb-4">Nueva Cotización</h3>

    <div class="row">
        <!-- COLUMNA IZQUIERDA: Datos del Cliente -->
        <div class="col-md-6">
            <h5>CLIENTES</h5>
            <div class="mb-3">
                <label>Cliente</label>
                <select class="form-select" name="cliente" id="selectCliente">
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
                            }))'>
                            {{ $cliente->CardCode.' - '.$cliente->CardName }}
                        </option>
                    @endforeach
                </select>
            </div>
            <!-- Aquí se mostrarán los datos -->
            <div id="infoCliente" class="mt-2" style="font-size: 14px;">
                <span id="telefono"></span><br>
                <span id="correo"></span><br>
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
                        <option value="{{ $moneda->Currency_ID }}" @if($moneda->cambios->isEmpty()) disabled @endif> {{--Si no hay cambios del dia deshabilita el selector--}}
                            {{ $moneda->Currency }}
                            @if($moneda->cambios->isEmpty()) (Sin tipo de cambio) @endif{{--Si no hay cambios del dia agrega la leyenda sin tipo de cambio--}}
                        </option>
                    @endforeach
                </select>
            </div>

            @if( $vendedores )
                <div class="mb-3">
                    <label>Vendedores</label>
                    <select class="form-select" name="vendedor_SlpCode" id="selectVendedor">
                        <option value="" selected disabled>Selecciona un Vendedor</option>
                        @foreach($vendedores as $vendedor)
                            <option value="{{ $vendedor->SlpCode }}"
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
    <!-- Botón Cancelar -->
    <button type="button" class="btn btn-danger" onclick="window.location='{{ url('/') }}'">
        <i class="bi bi-x-circle"></i> Cancelar
    </button>

    <!-- Botón Guardar -->
    <button type="button" id="guardarCotizacion" class="btn btn-primary">
        <i class="bi bi-save"></i> Guardar
    </button>

    <!-- Botón Pedido -->
    <button type="button" class="btn btn-success">
        <i class="bi bi-bag"></i> Pedido
    </button>
</div>

@endsection
