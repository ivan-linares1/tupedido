@extends('layouts.app')

@section('title', 'Cotizacion')

@section('contenido')

 <!-- Importación de JS y css-->
@vite(['resources/js/cotizaciones.js', 'resources/css/formulario.css'])

<div class="container my-4">
    <h3 class="mb-3 fw-bold">
        @if($modo == 0)
            Nueva Cotización
        @else
            CO - {{ $cotizacion->DocEntry }}
        @endif
    </h3>

    <div class="row">
        <!-- COLUMNA IZQUIERDA: Datos del Cliente -->
        <div class="col-md-6">
            <h4 >CLIENTES</h4>
            <div class="mb-3">
                <label>Cliente</label>
                <select class="form-select" name="cliente" id="selectCliente" @if((isset($modo) && $modo == 1) || (Auth::user()->rol_id == 3)) disabled @endif>
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
            
           <div class="row direccion-container">
                <div class="col-md-6 col-sm-12 mb-3">
                    <label>Dirección Fiscal</label>
                    <span id="direccionFiscal" class="form-control" style="white-space: pre-wrap; display: block;">
                        {{ $cotizacion->Address ?? '' }}
                    </span>
                </div>

                <div class="col-md-6 col-sm-12 mb-3">
                    <label>Dirección de Entrega</label>
                    <span id="direccionEntrega" class="form-control" style="white-space: pre-wrap; display: block;">
                        {{ $cotizacion->Address2 ?? '' }}
                    </span>
                </div>
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
                    @if((isset($modo) && $modo == 1) ||  (Auth::user()->rol_id == 3 || Auth::user()->rol_id == 4)) disabled @endif>
                    <option value="" selected disabled>Selecciona una moneda</option>
                    @foreach($monedas as $moneda)
                        <option value="{{ $moneda->Currency_ID }}" @if($moneda->cambios->isEmpty()) disabled @endif 
                            @if(isset($preseleccionados['moneda']) && $preseleccionados['moneda'] == $moneda->Currency_ID) selected @endif > {{--si no hay monedas de cambio disponibles se inhabilita--}}
                            {{ $moneda->Currency.' - '.$moneda->CurrName }}
                            @if($moneda->cambios->isEmpty() && isset($modo) && $modo == 0) (Sin tipo de cambio) @endif
                        </option>
                    @endforeach
                </select>                
            </div>

            @if(Auth::user()->rol_id !=3)
                <div class="mb-3">
                    <label>Vendedor</label>
                    <select class="form-select" name="vendedor_SlpCode" id="selectVendedor"  @if((isset($modo) && $modo == 1) ||  (Auth::user()->rol_id == 4) ) disabled @endif>
                         <option value="" selected disabled>Selecciona un vendedor</option>
                        @foreach($vendedores as $vendedor)
                            <option value="{{ $vendedor->SlpCode }}" 
                                @if(isset($preseleccionados['vendedor']) && $preseleccionados['vendedor'] == $vendedor->SlpCode) selected @endif
                                data-SlpName="{{ $vendedor->SlpName }}">
                                {{ $vendedor->SlpName }}
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

    @if($pedido)
        <span>
            Esta cotización tiene una relación con el 
            <a href="{{ route('detallesP', ['id' => $cotizacion->DocEntry]) }}">
                Pedido {{ $pedido->DocEntry }}
            </a>
        </span>
    @endif

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
    <input type="hidden" name="comentarios" id="comentariosH"> {{--comentario--}}

    <!-- Artículos -->
    <input type="hidden" name="articulos" id="articulosH">
</form>

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
    <input type="hidden" name="comentarios" id="comentariosP"> {{--comentario--}}

    <!-- Artículos -->
    <input type="hidden" name="articulos" id="articulosP">
</form>

<div class="container my-4 d-flex justify-content-start gap-2">

    @if($modo == 0) {{--Estos son los botones que aparecen cuando una cotizacion es nueva--}}
        <!-- Botón Cancelar -->
        <button type="button" class="btn btn-danger" onclick="window.location='{{ url('/') }}'">
            <i class="bi bi-x-circle"></i> Cancelar
        </button>
        @if($moneda->cambios->isEmpty()) {{--Cuando no hay cambios de monedas estos botones se muestran deshabilitados y muestran un mensaje--}}
            <div class="d-inline-block position-relative">
                <button class="btn btn-primary" disabled><i class="bi bi-save"></i> Guardar</button>
                <button class="btn btn-success" disabled><i class="bi bi-bag"></i> Pedido</button>
                <small class="mensaje-cambio text-danger">⚠️ {!! 'Contacte a soporte: <br> no existen tipos de cambio registrados para hoy.' !!}</small>
            </div>
        @else
            <!-- Botón Guardar -->
            <button type="button" id="guardarCotizacion"  class="btn btn-primary Save">
                <i class="bi bi-save"></i> Guardar
            </button>

            <!-- Botón Pedido -->
            <button type="button" id="btnPedido" class="btn btn-success Save">
                <i class="bi bi-bag"></i> Pedido
            </button>
        @endif
    @else{{--Estos son los botones que aparecen cuando una cotizacion existe y se abrio para ver los detalles de la misma--}}
        <!-- Botón PDF -->
        <button type="button" class="btn btn-danger" onclick="window.open('{{ route('cotizacion.pdf', $cotizacion->DocEntry) }}', '_blank')">
            <i class="bi bi-filetype-pdf"></i> PDF
        </button>

        @if($moneda->cambios->isEmpty() || $pedido){{--Cuando no hay cambios de monedas estos botones se muestran deshabilitados y muestran un mensaje--}}
         <div class="d-inline-block position-relative">
            <button class="btn btn-secondary" disabled><i class="bi bi-pencil-square"></i> Editar</button>
            <button class="btn btn-success" disabled><i class="bi bi-bag"></i> Pedido</button>
            <small class="mensaje-cambio text-danger">⚠️ {!! $pedido ? 'Ya tiene un pedido creado.' : 'Contacte a soporte: <br> no existen tipos de cambio registrados para hoy.' !!}</small>
        </div>
        @else
          <!-- Botón Editar -->
            <a href="{{ route('NuevaCotizacion', ['DocEntry' => $cotizacion->DocEntry]) }}" class="btn btn-secondary">
                <i class="bi bi-pencil-square"></i> Editar
            </a>

            <a href="{{ route('NuevaPedido', ['DocEntry' => $cotizacion->DocEntry]) }}" class="btn btn-success">
                <i class="bi bi-bag"></i> Pedido
            </a>
        @endif
    @endif
    

    
</div>
  <style>
    .mensaje-cambio {
        position: absolute;
        top: 105%;
        left: 0;
        background: rgba(255, 245, 245, 0.95);
        border: 1px solid #dc3545;
        border-radius: 10px;
        padding: 6px 10px;
        font-size: 0.85rem;
        color: #dc3545;
        font-weight: 500;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        white-space: nowrap;
        z-index: 10;
        opacity: 0;
        transform: translateY(5px);
        transition: opacity 0.3s ease, transform 0.3s ease;
    }

    /* Efecto al pasar el mouse sobre los botones */
    .d-inline-block:hover .mensaje-cambio {
        opacity: 1;
        transform: translateY(0);
    }
</style>
@endsection
