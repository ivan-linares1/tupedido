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

<div class="table-responsive table-scroll">
            <table class="table table-bordered" id="tablaArticulos">
                <thead>
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
                    <th>
                        <div class="d-flex align-items-center gap-2">
                            Descuento
                            <input type="number" id="descuentoInput" class="form-control form-control-sm w-auto" value="0" min="0" max="100" style="width:70px;">
                        </div>
                    </th>
                    <td id="descuento">0%</td>
                </tr>
                <tr>
                    <th>Gaston Adicionales</th>
                    <td id="gastosAdicionales">$0.00</td>
                </tr>
                <tr>
                    <th>Impuesto (IVA 16%)</th>
                    <td id="iva">$0.00</td>
                </tr>
                <tr>
                    <th>Total del documento</th>
                    <td id="total">$0.00</td>
                </tr>
            </table>
        </div>
    </div>
</div>

<!-- Modal Clientes -->
<div class="modal fade" id="modalClientes" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Seleccionar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-hover" id="tablaClientes">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Descuento</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Se llena dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Artículos -->
<div class="modal fade" id="modalArticulos" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Seleccionar Artículo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-hover" id="tablaModalArticulos">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Precio</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Se llena dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Scripts dinámicos -->
@push('scripts')
<script>
const articulos = [
    {codigo: 'ART-001', descripcion: 'Producto Ejemplo', precio: 100, modelo: 'MOD-001', stock: 50, stockK001: 30, stockK007: 3, almacen: 'WMS'},
    {codigo: 'ART-002', descripcion: 'Producto Premium', precio: 250, modelo: 'MOD-002', stock: 20, stockK001: 10, stockK007: 1, almacen: 'WMS'},
    {codigo: 'ART-003', descripcion: 'Producto Económico', precio: 50, modelo: 'MOD-003', stock: 100, stockK001: 80, stockK007: 8, almacen: 'WMS'}
];

let descuentoCliente = 0;

// Cargar artículos en el modal
function cargarArticulos() {
    const tbody = document.querySelector('#tablaModalArticulos tbody');
    tbody.innerHTML = '';
    articulos.forEach((a, index) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${a.codigo}</td>
            <td>${a.descripcion}</td>
            <td>${a.precio.toFixed(2)}</td>
            <td><button class="btn btn-sm btn-success" onclick="agregarArticulo(${index})">Agregar</button></td>
        `;
        tbody.appendChild(tr);
    });
}

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