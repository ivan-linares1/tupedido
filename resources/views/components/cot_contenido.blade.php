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
                <th></th>
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
            <tr>
                <td>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalArticulos">+</button>
                </td>
                <td colspan="26"></td>
            </tr>
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
                <td id="totalAntesDescuento">$0.00</td>
            </tr>
            <tr>
                <th>Descuento del:</th>
                <td id="DescuentoD">$0.00</td>
            </tr>
            <tr>
                <th>Total con el descuento</th>
                <td id="totalConDescuento">$0.00</td>
            </tr>
            <tr>
                <th>Impuesto (IVA {{ $IVA }}%)</th>
                <td id="iva">$0.00</td>
            </tr>
            <tr>
                <th>Total</th>
                <td id="total">$0.00</td>
            </tr>
        </table>
    </div>
</div>


<!-- Modal Artículos -->
<div class="modal fade" id="modalArticulos" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Seleccionar Artículo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                        @foreach($articulos as $articulo)
                            <tr>
                                <td>{{ $articulo->ItemCode }}</td>
                                <td>{{ $articulo->FrgnName }}</td>
                                <td>Precio Pendiente</td>
                                <td><img src="{{ asset($articulo->imagen->Ruta_imagen) }}" alt="Imagen" style="width:50px;height:auto;"></td>
                                <td>
                                    <button class="btn" style="background-color: blue; color: white; border: none; padding: 10px 20px; border-radius: 5px;" onclick='agregarArticulo(@json($articulo))'>Agregar</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
