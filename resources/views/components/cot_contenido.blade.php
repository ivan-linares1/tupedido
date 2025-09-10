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
                <th>Presion tras<br> el descuento</th>
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

{{--

<tr>
                        <th></th>
                        <th>Código</th>
                        <th>Modelo</th>
                        <th>Descripción</th>
                        <th>En stock</th>
                        <th>Stock K001</th>
                        <th>Comprometido</th>
                        <th>Factor</th>
                        <th>Cantidad</th>
                        <th>Precio por unidad</th>
                        <th>% Descuento</th>
                        <th>Presion tras el descuento</th>
                        <th>Impuestos</th>
                        <th>Total (ML)</th>
                        <th>Total Extranjero</th>
                        <th>Precio Unit.Doc</th>
                        <th>Total (doc)</th>
                        <th>Almacen</th>
                        <th>Pais/Region de origen</th>
                        <th>Precio base segun</th>
                        <th>Fecha de Entregas de Producto</th>
                        <th>Precio Cliente</th>
                        <th>Clave del Producto/Servicio</th>
                        <th>Clave de la Unidad</th>
                        <th>Stock K007</th>
                        <th>Demanda Perdida</th>
                        <th>BackOrder</th>
                    </tr>


<!-- Scripts dinámicos -->
@push('scripts')
<script>
let descuentoCliente = 0;


// Agregar artículo a la tabla principal
function agregarArticulo(index) {
    const art = articulos[index];
    const tabla = document.querySelector("#tablaArticulos tbody");
    const fila = document.createElement("tr");

    const cantidadInicial = 1;
    const precioConDesc = art.precio * cantidadInicial * (1 - descuentoCliente / 100);
    const totalLinea = precioConDesc * 1.16; // incluye IVA 16%

    fila.innerHTML = `
        <td><button class="btn btn-sm btn-danger" onclick="this.closest('tr').remove();calcularTotales()">X</button></td>
        <td>${art.codigo}</td>
        <td>${art.modelo}</td>
        <td>${art.descripcion}</td>
        <td>${art.stock}</td>
        <td>${art.stockK001}</td>
        <td><!-- comprometido --></td>
        <td><!-- factor --></td>
        <td><input type="number" value="${cantidadInicial}" min="1" class="form-control form-control-sm cantidad"></td>
        <td class="precio">${art.precio.toFixed(2)}</td>
        <td class="descuento">${descuentoCliente}</td>
        <td class="precioConDescuento">${precioConDesc.toFixed(2)}</td>
        <td>IVAP16</td>
        <td class="totalLinea">${totalLinea.toFixed(2)}</td>
        <td><!-- totalExtranjero --></td>
        <td><!-- precioUnitDoc --></td>
        <td><!-- totalDoc --></td>
        <td>${art.almacen}</td>
        <td><!-- paisOrigen --></td>
        <td><!-- precioBase --></td>
        <td><!-- fechaEntrega --></td>
        <td><!-- precioCliente --></td>
        <td><!-- claveProducto --></td>
        <td><!-- claveUnidad --></td>
        <td>${art.stockK007}</td>
        <td><!-- demandaPerdida --></td>
        <td><!-- backOrder --></td>
    `;

    tabla.insertBefore(fila, tabla.lastElementChild);

    // Recalcular al cambiar la cantidad
    fila.querySelector('.cantidad').addEventListener('input', calcularTotales);

    calcularTotales();
    bootstrap.Modal.getInstance(document.getElementById('modalArticulos')).hide();
}

// Calcular totales generales
function calcularTotales() {
    const filas = document.querySelectorAll("#tablaArticulos tbody tr");
    let totalAntesDescuento = 0;
    let totalConDescuento = 0;

    filas.forEach(fila => {
        const cantidad = parseFloat(fila.querySelector(".cantidad")?.value || 0);
        const precio = parseFloat(fila.querySelector(".precio")?.textContent || 0);
        const descuento = parseFloat(fila.querySelector(".descuento")?.textContent || 0);

        const precioConDesc = cantidad * precio * (1 - descuento / 100);
        const totalLinea = precioConDesc * 1.16; // incluye IVA

        if (fila.querySelector('.precioConDescuento')) {
            fila.querySelector('.precioConDescuento').textContent = precioConDesc.toFixed(2);
        }
        if (fila.querySelector('.totalLinea')) {
            fila.querySelector('.totalLinea').textContent = totalLinea.toFixed(2);
        }
        totalConDescuento += precioConDesc;
        totalAntesDescuento += cantidad * precio;
    });

    const descuentoInput = parseFloat(document.getElementById('descuentoInput').value || 0);
    const iva = totalConDescuento * 0.16;
    const totalFinal = totalConDescuento + iva;

    document.getElementById('totalAntesDescuento').textContent = `$${totalAntesDescuento.toFixed(2)}`;
    document.getElementById('totalConDescuento').textContent = `$${totalConDescuento.toFixed(2)}`;
    document.getElementById('descuento').textContent = `${descuentoInput}%`;
    document.getElementById('iva').textContent = `$${iva.toFixed(2)}`;
    document.getElementById('total').textContent = `$${totalFinal.toFixed(2)}`;
}

// Recalcular al cambiar el descuento global
document.getElementById('descuentoInput').addEventListener('input', calcularTotales);

// Cargar artículos cuando se abre el modal
document.getElementById('modalArticulos').addEventListener('show.bs.modal', cargarArticulos);
</script>
@endpush
--}}