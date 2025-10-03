@extends('layouts.app')

@section('title', 'Pedido')

@section('contenido')

 <!-- Importación de JS -->
@vite(['resources/js/cotizaciones.js'])

<div class="container my-4">
    <h3 class="mb-3 fw-bold">
        @if($modo == 0)
            Nuevo Pedido
        @else
            PE - {{ $idPedido }}
        @endif
    </h3>

    <div class="row">
        <!-- COLUMNA IZQUIERDA: Datos del Cliente -->
        <div class="col-md-6">
            <h4>CLIENTES</h4>
            <div class="mb-3">
                <label>Cliente</label>
                <select class="form-select" name="cliente" id="selectCliente" @if(isset($modo) && $modo == 1) disabled @endif>
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
                            }))'
                            @if(isset($preseleccionados['cliente']) && $preseleccionados['cliente'] == $cliente->CardCode) selected @endif>
                            {{ $cliente->CardCode.' - '.$cliente->CardName }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Aquí se mostrarán los datos -->
            <div id="infoCliente" class="mt-2" style="font-size: 14px;">
                <span id="telefono">@if(isset($modo) && $modo == 1){{ $cotizacion->Phone1  }}@endif</span><br>
                <span id="correo">
                    @if(isset($modo) && $modo == 1)
                        @php
                            $email = $cotizacion->E_Mail ?? 'Sin correo';
                            $emailFormatted = $email;

                            if ($email !== 'Sin correo') {
                                $emails = array_map('trim', explode(',', $email));
                                $emailFormatted = '<ul style="padding-left: 20px; margin: 0;">';
                                foreach ($emails as $e) {
                                    $emailFormatted .= "<li>{$e}</li>";
                                }
                                $emailFormatted .= '</ul>';
                            }
                        @endphp

                        {!! $emailFormatted !!}
                    @endif
                </span><br>
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
                    <input type="date" id="fechaCreacion" class="form-control" value="{{ $fechaCreacion }}"  readonly>
                </div>
                <div class="col-md-4">
                    <label>Válido Entrega</label>
                    <input type="date" id="fechaEntrega" class="form-control" value="{{ $fechaEntrega }}"   @if(isset($modo) && $modo == 1) readonly @endif >
                </div>
            </div>

            <div class="mb-3">
                <label>Moneda</label>
                <select class="form-select" name="currency_id" id="selectMoneda"
                    data-monedas='@json($monedas)' data-iva='@json($IVA)'
                    @if(isset($modo) && $modo == 1) disabled @endif>
                    <option value="" selected disabled>Selecciona una moneda</option>
                    @foreach($monedas as $moneda)
                        <option value="{{ $moneda->Currency_ID }}" @if($moneda->cambios->isEmpty()) disabled @endif 
                            @if(isset($preseleccionados['moneda']) && $preseleccionados['moneda'] == $moneda->Currency_ID) selected @endif > {{--si no hay monedas de cambio disponibles se inhabilita--}}
                            {{ $moneda->Currency }}
                            @if($moneda->cambios->isEmpty() && isset($modo) && $modo == 0) (Sin tipo de cambio) @endif
                        </option>
                    @endforeach
                </select>
            </div>

            @if( $vendedores )
                <div class="mb-3">
                    <label>Vendedores</label>
                    <select class="form-select" name="vendedor_SlpCode" id="selectVendedor"  @if(isset($modo) && $modo == 1) disabled @endif>
                        <option value="" selected disabled>Selecciona un Vendedor</option>
                        @foreach($vendedores as $vendedor)
                            <option value="{{ $vendedor->SlpCode }}" 
                                @if(isset($preseleccionados['vendedor']) && $preseleccionados['vendedor'] == $vendedor->SlpCode) selected @endif
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
            <a class="nav-link active" data-bs-toggle="tab" href="#contenido">Pedido</a>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="contenido">
            @include('components.cot_contenido', ['articulos' => $articulos])
        </div>
    </div>

    @if($cotizacion)
        <span>
            Este pedido tiene una relación con la
            <a href="{{ route('detalles', ['id' => $cotizacion->DocEntry]) }}">
                Cotizacion {{ $cotizacion->DocEntry }}
            </a>
        </span>
    @endif

</div>


<!-- Form para enviar la cotización ya pedida -->
<form id="formCotizacionPedido" action="{{ route('cotizacionSavePedido') }}" method="POST">
    @csrf
    <!-- Datos del cliente -->
    <input type="hidden" name="cliente" id="clienteP">  {{--cardcode--}}
    <input type="hidden" name="fechaCreacion" id="fechaCreacionP"> {{--DocDate--}}
    <input type="hidden" name="fechaEntrega" id="fechaEntregaP"> {{--DocDue--}}
    <input type="hidden" name="CardName" id="CardNameP"> {{--CardName--}}
    <input type="hidden" name="SlpCode" id="SlpCodeP"> {{--SlpCode--}}
    <input type="hidden" name="phone1" id="phone1P"> {{--phone1--}}
    <input type="hidden" name="email" id="emailP"> {{--email--}}
    <input type="hidden" name="DocCur" id="DocCurP"> {{--DocCur--}}
    <input type="hidden" name="ShipToCode" id="ShipToCodeP"> {{--ShipToCode--}}
    <input type="hidden" name="PayToCode" id="PayToCodeH"> {{--PayToCode--}}
    <input type="hidden" name="direccionFiscal" id="direccionFiscalP"> {{--Address--}}
    <input type="hidden" name="direccionEntrega" id="direccionEntregaP"> {{--Address--}}
    <input type="hidden" name="TotalSinPromo" id="TotalSinPromoP"> {{--TotalSinPromo--}}
    <input type="hidden" name="Descuento" id="DescuentoP"> {{--Descuento--}}
    <input type="hidden" name="Subtotal" id="SubtotalP"> {{--Subtotal--}}
    <input type="hidden" name="iva" id="ivaP"> {{--Iva--}}
    <input type="hidden" name="total" id="totalP"> {{--Total--}}

    <!-- Artículos -->
    <input type="hidden" name="articulos" id="articulosP">
</form>

<div class="container my-4 d-flex justify-content-start gap-2">

    @if($modo == 0)
        <!-- Botón Cancelar -->
        <button type="button" class="btn btn-danger" onclick="window.location='{{ url('/') }}'">
            <i class="bi bi-x-circle"></i> Cancelar
        </button>

        <!-- Botón Guardar -->
        <button type="button" id="btnPedido" class="btn btn-success">
            <i class="bi bi-save"></i> Crear Pedido
        </button>
    @else
        <!-- Botón PDF -->
        <button type="button" class="btn btn-danger" onclick="window.open('{{ route('pedido.pdf', $cotizacion->DocEntry) }}', '_blank')">
            <i class="bi bi-filetype-pdf"></i> PDF
        </button>
    @endif
</div>

@endsection
