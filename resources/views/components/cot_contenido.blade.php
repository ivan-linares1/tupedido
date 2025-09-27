<style>
    .table-scroll {
    max-height: 400px;      /* Scroll vertical */
    overflow-y: auto;
    overflow-x: auto;       /* Scroll horizontal */
    white-space: nowrap;    /* Evita que los th se rompan en varias líneas */
}

.table-scroll th, 
.table-scroll td {
    white-space: nowrap;    /* Mantiene todo en una sola línea */
}
</style>

<!-- Scripts necesarios -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>


<div class="table-responsive table-scroll">
    <table class="table table-bordered" id="tablaArticulos">
        <thead>
            <tr>
                @if(isset($modo) && $modo == 0)<th></th>@endif
                <th>Clave</th>
                <th>Producto</th>
                <th>Imagen</th>
                <th>Unidad de medida</th>
                <th>Precio</th>
                <th>Moneda</th>
                <th>Impuesto</th>
                <th>Cantidad</th>
                <th>Promociones</th>
                <th>SubTotal</th>
                <th>% Descuento</th>
                <th>Descuento del</th>
                <th>Total (doc)</th>
            </tr>
        </thead>

        <tbody>
            @if(isset($cotizacion) && $cotizacion->lineas)
                @foreach($cotizacion->lineas as $linea)
                        <tr >
                            <td class="itemcode">{{ $linea->ItemCode }}</td>
                            <td class="frgnName">{{ $linea->U_Dscr }}</td>
                            <td class="imagen">
                                <img src="{{ $linea->imagen?->Ruta_imagen }}" alt="Imagen" style="width: 50px; height: auto;">
                            </td>
                            <td class="medida">Unidad de medida</td>
                            <td class="precio">{{ number_format($linea->Price, 2) }}</td>
                            <td class="moneda">  {{ $monedas->firstWhere('Currency_ID', $cotizacion->DocCur)->Currency ?? '' }}</td>
                            <td class="iva">IVA {{ $IVA }}%</td>
                            <td>
                                <span>{{ number_format($linea->Quantity, 0) }}</span>
                            </td>
                            <td class="promocion">Promociones</td>
                            <td class="subtotal">{{ number_format($linea->Price * $linea->Quantity, 2) }}</td>
                            <td class="descuentoporcentaje">{{ number_format($linea->DiscPrcnt, 0) }} %</td>
                            <td class="desMoney">{{ number_format(($linea->Price * $linea->Quantity) * ($linea->DiscPrcnt / 100), 2) }}</td>
                            <td class="totalFinal">{{ number_format(($linea->Price * $linea->Quantity) - (($linea->Price * $linea->Quantity) * ($linea->DiscPrcnt / 100)), 2) }}</td>
                        </tr>
                @endforeach
            @endif

            {{-- Modo 0: agregar artículos automáticamente desde lineasComoArticulos --}}
            @if(isset($modo) && $modo == 0 && isset($lineasComoArticulos) && count($lineasComoArticulos) > 0)
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const lineas = @json($lineasComoArticulos);
                        lineas.forEach(art => agregarArticulo(art));
                    });
                </script>
            @endif

            @if(isset($modo) && $modo == 0)
            <!-- Botón para agregar nuevos artículos -->
            <tr>
                <td>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalArticulos">+</button>
                </td>
                <td colspan="26"></td>
            </tr>
            @endif
        </tbody>

    </table>
</div>

<!-- Totales -->
<div class="row mt-3">
    <div class="col-md-8"></div>
    <div class="col-md-4">
        <table class="table">
            <tr>
                <th>Total antes del descuento</th>
                <td id="totalAntesDescuento">
                    @if($modo == 1)
                        {{ number_format($cotizacion->TotalSinPromo ?? 0, 2) }}
                    @else
                        <span id="totalAntesDescuento">$0.00</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Descuento del:</th>
                <td id="DescuentoD">
                    @if($modo == 1)
                        {{ number_format($cotizacion->Descuento ?? 0, 2) }}
                    @else
                        <span id="DescuentoD">$0.00</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Total con el descuento</th>
                <td id="totalConDescuento">
                    @if($modo == 1)
                        {{ number_format(($cotizacion->TotalSinPromo ?? 0) - ($cotizacion->Descuento ?? 0), 2) }}
                    @else
                        <span id="totalConDescuento">$0.00</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Impuesto (IVA {{ $IVA }}%)</th>
                <td id="iva">
                    @if($modo == 1)
                        {{ number_format($cotizacion->IVA ?? 0, 2) }}
                    @else
                        <span id="iva">$0.00</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Total</th>
                <td id="total">
                    @if($modo == 1)
                        {{ number_format($cotizacion->Total ?? 0, 2) }}
                    @else
                        <span id="total">$0.00</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>
</div>


<!-- Modal Artículos -->
<div class="modal fade" id="modalArticulos" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #05564f">
                <h5 class="modal-title">Seleccionar Artículo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Tabla -->
                <table class="table table-hover" id="tablaModalArticulos">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Precio</th>
                            <th>Imagen</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($articulos as $articuloM)
                            <tr>
                                <td>{{ $articuloM->ItemCode }}</td>
                                <td>{{ $articuloM->FrgnName }}</td>
                                <td>Precio Pendiente</td>
                                <td><img src="{{ asset($articuloM->imagen->Ruta_imagen) }}" alt="Imagen" style="width:50px;height:auto;"></td>
                                <td>
                                    <button class="btn" style="background-color: blue; color: white; border: none; padding: 10px 20px; border-radius: 5px;" onclick='agregarArticulo(@json($articuloM))'>Agregar</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
