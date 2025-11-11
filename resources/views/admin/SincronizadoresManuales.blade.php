@extends('layouts.app')

@section('title', 'Panel de Sincronizadores')

@section('contenido')
    @vite(['resources/css/sincronizadores.css'])

<div class="container mt-5 sincronizadores-panel">
    <h1>Panel de Control de Sincronizadores Manuales</h1>
    <x-loading /> <!--animacion de cargando-->

    <div class="row g-4 justify-content-center">

        <!-- templated para general las cards -->
        @php
            $cards = [
                /*ORTT 2*/ ['icon' => 'bi-currency-exchange icon-currency', 'titulo' => 'Divisas del Día',         'texto' => 'Sincroniza las divisas del día.',                    'servicio' => 'Cambios_Monedas',         'metodo1' => 'SBO_Tipo_Cambio_ORTT',                             'metodo2' => 'SBO_Actualiza_Tipo_Cambio_ORTT'],
                /*OQUT 2*/ ['icon' => 'bi-file-earmark-text icon-DocNum',   'titulo' => 'Cotizaciones',            'texto' => 'Sincroniza los DocNum desde Sap.',                   'servicio' => 'DocNum',                  'metodo1' => 'SBO_No_Coti_SAP_OQUT_TODAS',                       'metodo2' => 'SBO_No_Coti_SAP_OQUT'],
                /*ORDR X*/ ['icon' => 'bi-cart-check icon-DocNumP',         'titulo' => 'Pedidos',                 'texto' => 'Sincroniza los DocNum desde Sap.',                   'servicio' => 'DocNumP',                  'metodo1' => '#',                       'metodo2' => '#'],
                /*OCRN 1*/ ['icon' => 'bi-currency-dollar icon-monedas',    'titulo' => 'Monedas',                 'texto' => 'Sincroniza todas las monedas existentes.',           'servicio' => 'Monedas',                 'metodo1' => 'SBO_Monedas_OCRN',                                 ],
                /*OITB 2*/ ['icon' => 'bi-grid-1x2-fill icon-marcas',       'titulo' => 'Grupos de Artículos',     'texto' => 'Sincroniza los grupos de artículos o marcas.',       'servicio' => 'Marcas',                  'metodo1' => 'SBO_GPO_AgregaTodo_Marca_OITB',                    'metodo2' => 'SBO_GPO_Actualiza_Marca_OITB'],
                /*OPLN 2*/ ['icon' => 'bi-list-check icon-categorias',      'titulo' => 'Cat. Listas Precios',     'texto' => 'Sincroniza las categorías de listas de precios.',    'servicio' => 'Categoria_Lista_Precios', 'metodo1' => 'SBO_CAT_LP_Agrega_Todo_OPLN',                      'metodo2' => 'SBO_CAT_LP_Actualiza_OPLN'],
                /*OITM 2*/ ['icon' => 'bi-box-seam icon-articulos',         'titulo' => 'Artículos',               'texto' => 'Sincroniza todos los artículos disponibles.',        'servicio' => 'Articulos',               'metodo1' => 'SBO_Articulos_AgregaTodo_OITM',                    'metodo2' => 'SBO_Articulos_Actualiza_OITM'],
                /*ITM1 2*/ ['icon' => 'bi-cash-stack icon-precios',         'titulo' => 'Lista de Precios',        'texto' => 'Sincroniza los precios de lista.',                   'servicio' => 'Lista_Precios',           'metodo1' => 'SBO_ListaPrecios_AgregaTodo_ITM1',                 'metodo2' => 'SBO_ListaPrecios_Actualiza_ITM1'],
                /*OCRD 2*/ ['icon' => 'bi-people-fill icon-clientes',       'titulo' => 'Lista de Clientes',       'texto' => 'Sincroniza la lista de Clientes.',                   'servicio' => 'Clientes',                'metodo1' => 'SBO_Clientes_Agrega_Todo_OCRD',                    'metodo2' => 'SBO_Clientes_Actualiza_OCRD'],
                /*CRD1 2*/ ['icon' => 'bi-geo-alt-fill icon-direcciones',   'titulo' => 'Direcciones de Clientes', 'texto' => 'Sincroniza la dirección de Clientes.',               'servicio' => 'Direcciones',             'metodo1' => 'SBO_Clientes_Agrega_Todo_Direcciones_CRD1',        'metodo2' => 'SBO_Clientes_Actualiza_Direcciones_CRD1'],
                /*OEDG 2*/ ['icon' => 'bi-tags-fill icon-descuentos',       'titulo' => 'Grupos de Descuentos',    'texto' => 'Sincroniza grupos de descuentos.',                   'servicio' => 'Grupo_Descuentos',        'metodo1' => 'SBO_Grupos_Agrgega_Todo_Descuentos_OEDG',          'metodo2' => 'SBO_Grupos_Actualiza_Descuentos_OEDG'],
                /*EDG1 2*/ ['icon' => 'bi-percent icon-descuento',          'titulo' => 'Descuentos',              'texto' => 'Sincroniza los descuentos.',                         'servicio' => 'Descuentos_Detalle',      'metodo1' => 'SBO_Grupos_Descuentos_EDG1',                       'metodo2' => 'SBO_Grupos_Actualiza_Descuentos_EDG1'],
                /*OSLP 1*/ ['icon' => 'bi-person-lines-fill icon-vendedor', 'titulo' => 'Vendedores',              'texto' => 'Sincroniza los vendedores.',                         'servicio' => 'Vendedores',              'metodo1' => 'SBO_Agrega_Todo_Vendedores_OSLP',                   ],
            ];
        @endphp

        @foreach ($cards as $card)
        <div class="col-md-3 col-sm-6">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi {{ $card['icon'] }}"></i>
                    <h5 class="card-title">{{ $card['titulo'] }}</h5>
                    <p class="card-text">{{ $card['texto'] }}</p>

                    <div class="d-flex justify-content-center gap-3 flex-wrap"> 
                        <!--Este formulario de boton esta configurado para que se muestre en todas las card-->
                        <form action="{{ route($card['servicio'] === 'Descuentos_Detalle' ? 'SincronizarAux' : 'Sincronizar', ['servicio' => $card['servicio'], 'metodo' => $card['metodo1'], 'modo' => 'Carga Total']) }}" 
                            method="POST" class="flex-fill" style="flex: 1 1 48%;">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">Cargar Todo</button>
                        </form>
                        <!--Este formulario de boton esta condicionado para que algunas card de servicio especifico no se muestre ya que no cuentan con un segundo metodo-->
                        @if ($card['servicio'] != 'Monedas' && $card['servicio'] != 'Vendedores')
                            <form action="{{ route('Sincronizar', ['servicio' => $card['servicio'], 'metodo' => $card['metodo2'], 'modo' => 'Actualizacion']) }}" 
                                method="POST" class="flex-fill" style="flex: 1 1 48%;">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">Carga Diaria</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
