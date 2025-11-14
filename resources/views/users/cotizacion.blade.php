@extends('layouts.app')

@section('title', 'Cotización')

@section('contenido')

<!-- Importación de datos de preseleccionados entre la vista y el javascript -->
<script>
window.preseleccionadoCliente = @json($preseleccionados['cliente'] ?? null);
window.preseleccionadoClienteText = @json( $preseleccionados['cliente'] ? ($clienteBase->CardCode.' - '.$clienteBase->CardName) : null );
window.preseleccionadoClientePhone = @json($cotizacion->Phone1 ?? '');
window.preseleccionadoClienteEmail = @json($cotizacion->E_Mail ?? '');
window.preseleccionadoClienteCardName = @json($clienteBase->CardName ?? '');
window.preseleccionadoClienteDescuentos = @json($cotizacion->descuentos ?? []);
window.preseleccionadoClienteDireccionFiscal = @json($cotizacion->Address ?? '');
window.preseleccionadoClienteDireccionEntrega = @json($cotizacion->Address2 ?? '');
</script>

<!-- Importación de JS y CSS -->
@vite(['resources/js/cotizaciones.js', 'resources/css/formulario.css'])

@php
    $cotizacion ??= null; // Asegura que $cotizacion existe
    $modo ??= 0; // Modo 0 por defecto (nueva cotización)
    $fechaCreacion ??= $cotizacion->DocDate ?? \Carbon\Carbon::today()->format('Y-m-d');
    $fechaEntrega ??= $cotizacion->DocDueDate ?? \Carbon\Carbon::tomorrow()->format('Y-m-d');
@endphp

<div class="container my-4">
    <h3 class="mb-3 fw-bold" data-DocEntryAux='@json($cotizacion->DocEntry ?? "")'>
        @if($modo == 0)
            Nueva Cotización
        @else
            CO - {{ $cotizacion->DocEntry ?? '' }}
            @if(Auth::user()->rol_id != 3)
            @if($cotizacion->DocStatus == 'A')
                <i class="bi bi-unlock-fill text-success ms-2" title="Cotización abierta"></i>
            @else
                <i class="bi bi-lock-fill text-danger ms-2" title="Cotización cerrada"></i>
            @endif @endif
        @endif
    </h3>

    <div class="row">
        <!-- COLUMNA IZQUIERDA: Datos del Cliente -->
        <div class="col-md-6">
            <h4>CLIENTES</h4>
            <div class="mb-3">
                <label>Cliente</label>
                <select class="form-select" name="cliente" id="selectCliente" @if($modo == 1 || in_array(Auth::user()->rol_id, [3])) disabled @endif required>
                    <option value="" selected disabled>Selecciona un cliente...</option>
                </select>
            </div>

            <div id="infoCliente" class="mt-2" style="font-size: 14px;">
                <span id="telefono">{{ $modo == 1 ? ($cotizacion->Phone1 ?? '') : '' }}</span><br>
                <span id="correo">
                    @if($modo == 1 && $cotizacion)
                        @php
                            $email = $cotizacion->E_Mail ?? 'Sin correo';
                            $emailFormatted = $email;
                            if ($email !== 'Sin correo') {
                                $emails = array_map('trim', explode(',', $email));
                                $emailFormatted = '<ul style="padding-left:20px;margin:0;">';
                                foreach ($emails as $e) { $emailFormatted .= "<li>{$e}</li>"; }
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
                    <span id="direccionFiscal" class="form-control" style="white-space: pre-wrap; display:block;">
                        {{ $cotizacion->Address ?? '' }}
                    </span>
                </div>
                <div class="col-md-6 col-sm-12 mb-3">
                    <label>Dirección de Entrega</label>
                    <span id="direccionEntrega" class="form-control" style="white-space: pre-wrap; display:block;">
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
                    <label>Fecha de Creación</label>
                    <input type="date" id="fechaCreacion" class="form-control" value="{{ $fechaCreacion }}" readonly>
                </div>
                <div class="col-md-4">
                    <label>Válido Entrega</label>
                    <input type="date" id="fechaEntrega" class="form-control" value="{{ $fechaEntrega }}" @if($modo == 1 || Auth::user()->rol_id > 2) readonly @endif>
                </div>
            </div>

            <div class="mb-3">
                <label>Moneda</label>
                <select class="form-select" name="currency_id" id="selectMoneda"
                        data-monedas='@json($monedas)' data-iva='@json($IVA)'
                        @if($modo == 1 || in_array(Auth::user()->rol_id, [3,4])) disabled @endif>
                    <option value="" selected disabled>Selecciona una moneda</option>
                    @foreach($monedas as $moneda)
                        <option value="{{ $moneda->Currency_ID }}" @if(($preseleccionados['moneda'] ?? '') == $moneda->Currency_ID) selected @endif
                            @if($moneda->cambios->isEmpty() && $modo == 0) disabled @endif>
                            {{ $moneda->Currency.' - '.$moneda->CurrName }} @if($moneda->cambios->isEmpty() && $modo == 0) (Sin tipo de cambio) @endif
                        </option>
                    @endforeach
                </select>
            </div>

            
            <div class="mb-3" @if(Auth::user()->rol_id == 3) hidden @endif>
                <label>Vendedor</label>
                <select class="form-select" name="vendedor_SlpCode" id="selectVendedor"
                        @if($modo == 1 || Auth::user()->rol_id == 4) disabled @endif>
                    <option value="" selected disabled>Selecciona un vendedor</option>
                    @foreach($vendedores as $vendedor)
                        <option value="{{ $vendedor->SlpCode }}" data-SlpName="{{ $vendedor->SlpName }}"
                            @if(($preseleccionados['vendedor'] ?? '') == $vendedor->SlpCode) selected @endif>
                            {{ $vendedor->SlpName }}
                        </option>
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
            <a class="nav-link active" data-bs-toggle="tab" href="#contenido">Cotización</a>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="contenido">
            @include('components.cot_contenido', ['articulos' => $articulos ?? []])
        </div>
    </div>

    @if(!empty($pedido))
        <span>
            Esta cotización tiene una relación con el 
            <a href="{{ route('detallesP', ['id' => $pedido->DocEntry ?? 0]) }}">
                Pedido {{ $pedido->DocEntry ?? '' }}
            </a>
        </span>
    @endif
</div>

<!-- Form para guardar cotización -->
<form id="formCotizacion" action="{{ route('cotizacionSave') }}" method="POST">
    @csrf
    <input type="hidden" name="DocEntry_Aux" id="DocEntry_AuxH">
    <input type="hidden" name="cliente" id="clienteH">
    <input type="hidden" name="fechaCreacion" id="fechaCreacionH">
    <input type="hidden" name="fechaEntrega" id="fechaEntregaH">
    <input type="hidden" name="CardName" id="CardNameH">
    <input type="hidden" name="SlpCode" id="SlpCodeH">
    <input type="hidden" name="phone1" id="phone1H">
    <input type="hidden" name="email" id="emailH">
    <input type="hidden" name="DocCur" id="DocCurH">
    <input type="hidden" name="ShipToCode" id="ShipToCodeH">
    <input type="hidden" name="PayToCode" id="PayToCodeH">
    <input type="hidden" name="direccionFiscal" id="direccionFiscalH">
    <input type="hidden" name="direccionEntrega" id="direccionEntregaH">
    <input type="hidden" name="TotalSinPromo" id="TotalSinPromoH">
    <input type="hidden" name="Descuento" id="DescuentoH">
    <input type="hidden" name="Subtotal" id="SubtotalH">
    <input type="hidden" name="iva" id="ivaH">
    <input type="hidden" name="total" id="totalH">
    <input type="hidden" name="comentarios" id="comentariosH">
    <input type="hidden" name="articulos" id="articulosH">
</form>

<!-- formulario para guardar el pedido de cotizacion hacia pedido -->
<form id="formCotizacionPedido" action="{{ route('PedidoSave') }}" method="POST">
    @csrf
    <input type="hidden" name="cliente" id="aux" value="{{ $cotizacion->DocEntry ?? ''}} ">{{--Solo para saber que una cotizacion existey se quiere crear su pedido--}}
    <input type="hidden" name="BaseEntry" id="BaseEntryP" value="{{ $cotizacion->DocEntry ?? ''}}">
    <input type="hidden" name="cliente" id="clienteP">
    <input type="hidden" name="fechaCreacion" id="fechaCreacionP">
    <input type="hidden" name="fechaEntrega" id="fechaEntregaP">
    <input type="hidden" name="CardName" id="CardNameP">
    <input type="hidden" name="SlpCode" id="SlpCodeP">
    <input type="hidden" name="phone1" id="phone1P">
    <input type="hidden" name="email" id="emailP">
    <input type="hidden" name="DocCur" id="DocCurP">
    <input type="hidden" name="ShipToCode" id="ShipToCodeP">
    <input type="hidden" name="PayToCode" id="PayToCodeH">
    <input type="hidden" name="direccionFiscal" id="direccionFiscalP">
    <input type="hidden" name="direccionEntrega" id="direccionEntregaP">
    <input type="hidden" name="TotalSinPromo" id="TotalSinPromoP">
    <input type="hidden" name="Descuento" id="DescuentoP">
    <input type="hidden" name="Subtotal" id="SubtotalP">
    <input type="hidden" name="iva" id="ivaP">
    <input type="hidden" name="total" id="totalP">
    <input type="hidden" name="comentarios" id="comentariosP">
    <input type="hidden" name="articulos" id="articulosP">
</form>


<div class="container my-4 d-flex justify-content-start gap-2">
    @if($modo == 0){{--Cuando es modo nuevo que es de creacion de la cotizacion se tiene los botones de cancelar y guardar--}}
        <button type="button" class="btn btn-danger" onclick="window.location='{{ url('/') }}'">
            <i class="bi bi-x-circle"></i> Cancelar
        </button>

        @if($moneda->cambios->isEmpty()){{--verificamos la existencia de monedas del dia si no existen se procede a deshabilñitar el boton de guardar y se muestra un mensaje
        con el error --}}
            <div class="d-inline-block position-relative">
                <button class="btn btn-primary" disabled><i class="bi bi-save"></i> Guardar</button>
                {{--<button class="btn btn-success" disabled><i class="bi bi-bag"></i> Pedido</button>--}}
                <small class="mensaje-cambio text-danger">⚠️ Contacte a soporte: no existen tipos de cambio registrados para hoy.</small>
            </div>
        @else {{--si si existen monedas se habilita el boton de guardar--}}
            <button type="button" id="guardarCotizacion" class="btn btn-primary Save">
                <i class="bi bi-save"></i> Guardar
            </button>
        @endif
    @else 
        <button type="button" class="btn btn-danger" onclick="window.open('{{ route('cotizacion.pdf', $cotizacion->DocEntry ?? 0) }}','_blank')">
            <i class="bi bi-filetype-pdf"></i> PDF
        </button>
        @if($moneda->cambios->isEmpty() || $pedido || $cotizacion->DocStatus == 'C')
            <div class="d-inline-block position-relative">
                <button class="btn btn-secondary" disabled><i class="bi bi-pencil-square"></i> Editar</button>
                <button class="btn btn-success" disabled><i class="bi bi-bag"></i> Crear Pedido</button>
                <small class="mensaje-cambio text-danger">
                    ⚠️ {!! $pedido ? 'Ya tiene un pedido creado.' : 'Contacte a soporte: no existen tipos de cambio registrados para hoy o Se Cerro la Cotizacion.' !!}
                </small>
            </div>
        @else
            <a href="{{ route('NuevaCotizacion', ['DocEntry' => $cotizacion->DocEntry ?? '']) }}" class="btn btn-secondary">
                <i class="bi bi-pencil-square"></i> Editar
            </a>
            @if($preseleccionados['crearPedido'] === false)
                <a href="{{ route('NuevaPedido', ['DocEntry' => $cotizacion->DocEntry ?? '']) }}" class="btn btn-success">
                    <i class="bi bi-bag"></i> Crear Pedido
                </a>
            @else
                <button type="button" id="btnPedido" class="btn btn-success">
                    <i class="bi bi-save"></i> Crear Pedido
                </button>
            @endif
            
        @endif
    @endif
</div>

<style>
.mensaje-cambio {
    position: absolute;
    top: 105%;
    left: 0;
    background: rgba(255,245,245,0.95);
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
.d-inline-block:hover .mensaje-cambio {
    opacity: 1;
    transform: translateY(0);
}
</style>

@endsection
