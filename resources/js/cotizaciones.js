// ================================
// FECHA MÍNIMA PARA ENTREGA
// ================================

// Obtenemos la fecha del día de hoy
const hoy = new Date().toISOString().split('T')[0];

// Evitar que el calendario permita escoger días pasados
document.getElementById('fechaEntrega').setAttribute('min', hoy);

// ================================
// DATOS DE MONEDA Y IVA
// ================================

// Recibe el dato de monedas desde el controlador (JSON en dataset)
const monedas = JSON.parse(selectMoneda.dataset.monedas);

// Recibe el IVA desde el controlador
const IVA = JSON.parse(selectMoneda.dataset.iva);

// ================================
// SELECT CLIENTE CON BUSQUEDA
// ================================
$(document).ready(function() {
    const $select = $('#selectCliente');
    $('#selectCliente').select2({
        placeholder: "Selecciona un cliente",
        allowClear: true,
        width: '100%',
        ajax: {
            url: '/clientes/buscar',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term || '', // texto buscado (vacío = mostrar todos)
                    page: params.page || 1 // número de página
                };
            },
             processResults: function(data, params) {
            params.page = params.page || 1;
            // 🔹 Aseguramos que cada cliente tenga todos los campos
                const items = data.items.map(cli => ({
                    id: cli.id,
                    text: cli.text,
                    cardname: cli.cardName,
                    phone: cli.phone || 'Sin teléfono',
                    email: cli.email || 'Sin correo',
                    active: cli.active
                }));
                return {
                    results: data.items,
                    pagination: {
                        more: data.more
                    }
                };
            },
            cache: true
        },
        minimumInputLength: 0, // permite abrir sin escribir
        templateResult: function(repo) {
            if (repo.loading) return "Cargando...";
            return repo.text;
        },
        templateSelection: function(repo) {
            return repo.text || repo.id;
        }
    });

     // 🔹 Preselección correcta con todos los datos
    if (window.preseleccionadoCliente) {
        const option = new Option(
            window.preseleccionadoClienteText,
            window.preseleccionadoCliente,
            true,
            true
        );
        $select.append(option).trigger('change');

        // Guardar datos extra en Select2
        const select2Data = $select.select2('data');
        if (select2Data.length) {
            select2Data[0].cardname = window.preseleccionadoClienteCardName;
            select2Data[0].phone = window.preseleccionadoClientePhone || 'Sin teléfono';
            select2Data[0].email = window.preseleccionadoClienteEmail || 'Sin correo';
            select2Data[0].descuentos = window.preseleccionadoClienteDescuentos;
            select2Data[0].direccionFiscal = window.preseleccionadoClienteDireccionFiscal;
            select2Data[0].direccionEntrega = window.preseleccionadoClienteDireccionEntrega;
            $select.select2('data', select2Data);
        }

        // Llenar teléfono, correo y direcciones
        actualizarDatosCliente();
    }

    // Esto permite que al abrir el select (sin escribir) cargue la primera página
    $('#selectCliente').on('select2:open', function() {
        if (!$('.select2-results__option').length) {
            $(this).data('select2').trigger('query', {
                term: '',
                page: 1
            });
        }
    });
});



// ================================
// ACTUALIZAR DATOS DEL CLIENTE
// ================================
function actualizarDatosCliente() {
    const cliente = $('#selectCliente').select2('data')[0]; // obtiene el cliente seleccionado
    if (!cliente) return;

    const phone = cliente.phone || 'Sin teléfono';
    const email = cliente.email || 'Sin correo';
    const cardCode = cliente.id;

    let emailFormatted = email.split(',').join('<br>');
    if (email !== 'Sin correo') {
        let emails = email.split(',').map(e => `<li>${e.trim()}</li>`).join('');
        emailFormatted = `<ul style="padding-left: 20px; margin: 0;">${emails}</ul>`;
    }

    $('#telefono').text("Teléfono: " + phone);
    $('#correo').html("Correos:<br>" + emailFormatted);

    // 🔹 Traer direcciones vía AJAX
    $.ajax({
        url: `/Cotizaciones/cliente/${cardCode}/direcciones`,
        type: 'GET',
        success: function (data) {
            $('#direccionFiscal').text(data.fiscal);
            $('#direccionEntrega').text(data.entrega);
        },
        error: function () {
            alert('No se pudieron obtener las direcciones del cliente.');
        }
    });
}


// Ejecutar cuando cambie el select del cliente
$('#selectCliente').on('change', actualizarDatosCliente);

// Ejecutar al cargar la página si ya hay un cliente preseleccionado
$(document).ready(function () {
    if ($('#selectCliente').val()) {
        actualizarDatosCliente();
    }
});

// Script basico de inicializacion del select2 para poder buscar un vendedor dentro del select 
//solo busca con los datos ya cargados no esta paginado ya que son pocos vendedores
$(document).ready(function() {
    $('#selectVendedor').select2({
        placeholder: "Selecciona un vendedor",
        allowClear: true,
        width: '100%' 
    });
});

// ================================
// ACTUALIZAR DATOS DE LA MONEDA
// ================================
function actualizarDatosMoneda(){
    const monedaCambioID = parseInt(this.value);
    const monedaCambio = monedas.find(m => m.Currency_ID == monedaCambioID);

    // Recorremos todas las filas de la tabla y actualizamos el precio y la moneda
    const filas = document.querySelectorAll("#tablaArticulos tbody tr");
    filas.forEach(fila => {
        if (!fila.dataset.precioOriginal || !fila.dataset.monedaOriginal) return;

        const precioOriginal = parseFloat(fila.dataset.precioOriginal);
        const monedaOriginal = JSON.parse(fila.dataset.monedaOriginal);

        // Calculamos el precio convertido
        const precioConvertido = conversionesMonedas(precioOriginal, monedaOriginal, monedaCambio);
        fila.querySelector('.precio').textContent = precioConvertido.toFixed(2);

        // Actualizamos la columna de moneda
        fila.querySelector('.moneda').textContent = monedaCambio ? monedaCambio.Currency : monedaOriginal.Currency;
    });

    // Recalculamos totales
    calcularTotales();
}

// Ejecutar cuando cambie el select de moneda
$('#selectMoneda').on('change', actualizarDatosMoneda);

// ================================
// ACTUALIZAR DESCUENTOS SEGÚN CLIENTE
// ================================
$('#selectCliente').on('change', function() {
    const clienteOption = this.options[this.selectedIndex];
    const descuentosCliente = clienteOption ? JSON.parse(clienteOption.dataset.descuentos || '[]') : [];

    // Recorremos las filas de artículos
    const filas = document.querySelectorAll("#tablaArticulos tbody tr");
    filas.forEach(fila => {
        const itmsGrpCod = fila.dataset.itmsGrpCod;
        if (!itmsGrpCod) return;

        // Buscar descuento por grupo
        const detalle = descuentosCliente.find(det => String(det.ObjKey) === String(itmsGrpCod));
        const descuento = detalle ? parseFloat(detalle.Discount) : 0;

        // Actualizar columna de porcentaje
        fila.querySelector('.descuentoporcentaje').textContent = `${descuento} %`;

        // Recalcular totales
        calcularTotales();
    });
});

// ================================
// AGREGAR ARTÍCULO A LA TABLA
// ================================
window.agregarArticulo = function(art) {
    const tabla = document.querySelector("#tablaArticulos tbody");
    const fila = document.createElement("tr");

    const cantidad = art.Quantity ? Number(art.Quantity) : 1;

    // Guardamos info útil en data-attributes
    fila.dataset.precioOriginal = art.precio.Price;
    fila.dataset.monedaOriginal = JSON.stringify(art.precio.moneda);
    fila.dataset.itmsGrpCod = art.ItmsGrpCod;

    const monedaCambioID = parseInt(document.querySelector('select[name="currency_id"]').value);
    const monedaCambio = monedas.find(m => m.Currency_ID == monedaCambioID);
    const precio = conversionesMonedas(art.precio.Price, art.precio.moneda, monedaCambio);

    // Descuento según cliente y grupo de artículo
    const clienteSelect = document.getElementById('selectCliente');
    const clienteOption = clienteSelect.options[clienteSelect.selectedIndex];
    const descuentosCliente = clienteOption ? JSON.parse(clienteOption.dataset.descuentos || '[]') : [];
    const detalle = descuentosCliente.find(det => String(det.ObjKey) === String(art.ItmsGrpCod));
    const descuento = detalle ? parseFloat(detalle.Discount) : 0;

    // Construcción del HTML de la fila
    fila.innerHTML = `
        <td><button class="btn btn-sm btn-danger">X</button></td>
        <td class="itemcode">${art.ItemCode}</td>
        <td class="frgnName">${art.FrgnName}</td>
        <td class="imagen" data-imagen="${art.Id_imagen}"><img src="${art.imagen?.Ruta_imagen}" alt="Imagen" style="width: 50px; height: auto;"></td>
        <td class="medida">${art.SalUnitMsr}</td>
        <td class="precio">${Number(precio || 0).toFixed(2)}</td>
        <td class="moneda">${monedaCambio ? monedaCambio.Currency : art.precio.moneda.Currency}</td>
        <td class="ivaPorcentaje">IVA ${Number(IVA.Rate).toFixed(0)}%</td>
        <td><input type="number" value="${cantidad}" min="1" class="form-control form-control-sm cantidad"></td>
        <td class="promocion">Promociones</td>
        <td class="subtotal"></td>
        <td class="descuentoporcentaje">${descuento} %</td>
        <td class="desMoney"></td>
        <td class="totalFinal">Total (doc)</td>
    `;

    // Insertamos la fila antes de la última (botón agregar)
    tabla.insertBefore(fila, tabla.lastElementChild);

    // Eventos
    fila.querySelector('.cantidad').addEventListener('input', calcularTotales);
    fila.querySelector('button').addEventListener('click', function() { eliminarFila(this); });

    calcularTotales();

    // Cerrar modal si está abierto
    const modalEl = document.getElementById('modalArticulos');
    if (modalEl) {
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) {
            modalInstance.hide();
            fila.querySelector('.cantidad')?.focus();
        }
    }
};

// ================================
// ACTUALIZAR PRECIOS
// ================================
function actualizarPrecios() {
    const monedaCambioID = parseInt(document.querySelector('select[name="currency_id"]').value);
    const monedaCambio = monedas.find(m => m.Currency_ID == monedaCambioID);

    document.querySelectorAll(".precio-celda").forEach(cell => {
        const precioOriginal = parseFloat(cell.dataset.precio);
        const monedaOriginal = JSON.parse(cell.dataset.moneda);

        const precioConvertido = conversionesMonedas(precioOriginal, monedaOriginal, monedaCambio);
        const simboloMoneda = monedaCambio ? monedaCambio.Currency : monedaOriginal.Currency;

        cell.innerHTML = `$${Number(precioConvertido).toFixed(2)} ${simboloMoneda}`;
    });
}

document.addEventListener("DOMContentLoaded", actualizarPrecios);
document.querySelector('select[name="currency_id"]').addEventListener("change", actualizarPrecios);

// ================================
// TEXTAREA COMENTARIOS CON LÍMITE DE CARACTERES
// ================================
const textarea = document.getElementById("comentarios");
const contador = document.getElementById("contador");
const limiteCaracteres = 200;

// Inicializa contador
contador.textContent = `Te quedan ${limiteCaracteres} caracteres`;

// Escucha input y corta si excede
textarea.addEventListener("input", function() {
    let texto = this.value;

    if (texto.length > limiteCaracteres) {
        this.value = texto.slice(0, limiteCaracteres);
        texto = this.value;
    }

    const restantes = limiteCaracteres - texto.length;
    contador.textContent = `Te quedan ${restantes} caracteres`;
});

// ================================
// FUNCIONES DE MONEDA Y TOTALES
// ================================

// Convierte precios según tipo de moneda
function conversionesMonedas(precioOriginal, monedaOriginal, monedaConvertir) {
    if(!monedaConvertir) return parseFloat(precioOriginal).toFixed(2);

    let rate = monedaOriginal.cambios[0]?.Rate ?? 1;
    const precioBase = parseFloat(precioOriginal) * rate;
    rate = monedaConvertir.cambios[0]?.Rate ?? 1;

    return precioBase / rate;
}

// Calcular totales generales de la tabla
function calcularTotales() {
    const filas = document.querySelectorAll("#tablaArticulos tbody tr");
    let totalAntesDescuento = 0, totalDescuento = 0, totalFinalGeneral = 0;
    const moneda = filas[0]?.querySelector('td.moneda')?.textContent || '';

    filas.forEach(fila => {
        const cantidad = parseFloat(fila.querySelector(".cantidad")?.value) || 0;
        const precio = parseFloat(fila.querySelector(".precio")?.textContent) || 0;
        const descuentoP = parseFloat(fila.querySelector(".descuentoporcentaje")?.textContent.replace('%', '')) || 0;

        const subtotal = cantidad * precio;
        const descuentoMoney = subtotal * (descuentoP / 100);
        const totalConDescuento = subtotal - descuentoMoney;

        totalAntesDescuento += subtotal;
        totalDescuento += descuentoMoney;
        totalFinalGeneral += totalConDescuento;

        const cells = {
            subtotal: fila.querySelector('.subtotal'),
            desMoney: fila.querySelector('.desMoney'),
            totalFinal: fila.querySelector('.totalFinal')
        };

        if (cells.subtotal) cells.subtotal.textContent = subtotal.toFixed(2);
        if (cells.desMoney) cells.desMoney.textContent = descuentoMoney.toFixed(2);
        if (cells.totalFinal) cells.totalFinal.textContent = totalConDescuento.toFixed(2);
    });

    const iva = totalFinalGeneral * (IVA.Rate / 100);
    const totalConIva = totalFinalGeneral + iva;

    const setTotal = (id, value) => {
        const el = document.getElementById(id);
        if (el) el.textContent = `$${value} ${moneda}`;
    };

    setTotal('totalAntesDescuento', totalAntesDescuento.toFixed(2));
    setTotal('DescuentoD', totalDescuento.toFixed(2));
    setTotal('totalConDescuento', totalFinalGeneral.toFixed(2));
    setTotal('iva', iva.toFixed(2));
    setTotal('total', totalConIva.toFixed(2));
}

// Eliminar fila y recalcular totales
function eliminarFila(boton) {
    boton.closest("tr").remove();
    calcularTotales();
}

// ================================
// FILTROS Y MODAL DE ARTÍCULOS
// ================================
$(document).ready(function() {
    var tablaModal = $('#tablaModalArticulos').DataTable({
        pageLength: 25,
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json' },
        ordering: false,
        searching: true
    });

    function scrollModalAlInicio() {
        setTimeout(() => $('#modalArticulos .modal-body').scrollTop(0), 50);
    }

    $('#buscadorModal').on('keyup', function() { tablaModal.search(this.value).draw(); scrollModalAlInicio(); });
    $('#filtroMostrar').on('change', function() { tablaModal.page.len($(this).val()).draw(); scrollModalAlInicio(); });
    $('#tablaModalArticulos').on('page.dt', function() { scrollModalAlInicio(); });
    $('#modalArticulos').on('shown.bs.modal', function() { scrollModalAlInicio(); });

    $('#modalArticulos').on('hidden.bs.modal', function () {
        $('#buscadorModal').val('');
        tablaModal.search('').draw();
        $('#filtroMostrar').val('25');
        tablaModal.page.len(25).draw();
        tablaModal.page('first').draw('page');
        scrollModalAlInicio();
    });
});

// ================================
// GUARDAR COTIZACIÓN
// ================================
$("#guardarCotizacion").on("click", function() {
    // Llenar inputs ocultos con valores de la página
    const cliente = $('#selectCliente').select2('data')[0] || {};
     // Llenar inputs ocultos con valores de la página
    $("#clienteH").val(cliente.id || '');
    $("#fechaCreacionH").val($("#fechaCreacion").val());
    $("#fechaEntregaH").val($("#fechaEntrega").val());
    $("#CardNameH").val(cliente.cardname || '');
    $("#SlpCodeH").val($("#selectVendedor").val());
    $("#phone1H").val(cliente.phone || '');
    $("#emailH").val(cliente.email || '');
    $("#DocCurH").val($("#selectMoneda").val());
    $("#direccionFiscalH").val($("#direccionFiscal").text());
    $("#direccionEntregaH").val($("#direccionEntrega").text());
    $("#TotalSinPromoH").val($("#totalAntesDescuento").text().replace('$',''));
    $("#DescuentoH").val($("#DescuentoD").text().replace('$',''));
    $("#SubtotalH").val($("#totalConDescuento").text().replace('$',''));
    $("#ivaH").val($("#iva").text().replace('$',''));
    $("#totalH").val($("#total").text().replace('$',''));
    $("#comentariosH").val($('#comentarios').val());


    // Recopilar artículos
    let articulos = [];
    $("#tablaArticulos tbody tr:not(:last)").each(function() {
        articulos.push({
            itemCode: $(this).find(".itemcode").text(),
            descripcion: $(this).find(".frgnName").text(),
            unidad: $(this).find(".medida").text(),
            precio: $(this).find(".precio").text(),
            descuentoPorcentaje: $(this).find(".descuentoporcentaje").text(),
            cantidad: $(this).find(".cantidad").val(),
            imagen: $(this).find(".imagen").data("imagen"),
            ivaPorcentaje:IVA.Code
        });
    });

    $("#articulosH").val(JSON.stringify(articulos));
    $("#formCotizacion").submit();
});

// ================================
// GUARDAR PEDIDO
// ================================
$("#btnPedido").on("click", function() {
    const cliente = $('#selectCliente').select2('data')[0] || {};

    $("#clienteP").val(cliente.id || '');
    $("#fechaCreacionP").val($("#fechaCreacion").val());
    $("#fechaEntregaP").val($("#fechaEntrega").val());
    $("#CardNameP").val(cliente.cardname || '');
    $("#SlpCodeP").val($("#selectVendedor").val());
    $("#phone1P").val(cliente.phone || '');
    $("#emailP").val(cliente.email || '');
    $("#DocCurP").val($("#selectMoneda").val());
    $("#direccionFiscalP").val($("#direccionFiscal").text());
    $("#direccionEntregaP").val($("#direccionEntrega").text());
    $("#TotalSinPromoP").val($("#totalAntesDescuento").text().replace('$',''));
    $("#DescuentoP").val($("#DescuentoD").text().replace('$',''));
    $("#SubtotalP").val($("#totalConDescuento").text().replace('$',''));
    $("#ivaP").val($("#iva").text().replace('$',''));
    $("#totalP").val($("#total").text().replace('$',''));
    $("#comentariosP").val($('#comentarios').val());

    let articulos = [];
    $("#tablaArticulos tbody tr:not(:last)").each(function() {
        articulos.push({
            itemCode: $(this).find(".itemcode").text(),
            descripcion: $(this).find(".frgnName").text(),
            unidad: $(this).find(".medida").text(),
            precio: $(this).find(".precio").text(),
            descuentoPorcentaje: $(this).find(".descuentoporcentaje").text(),
            cantidad: $(this).find(".cantidad").val(),
            imagen: $(this).find(".imagen").data("imagen"),
            ivaPorcentaje:IVA.Code
        });
    });
    
    $("#articulosP").val(JSON.stringify(articulos));
    $("#BaseEntry").val("{{ $cotizacion->DocEntry ?? '' }}");
    $("#formCotizacionPedido").submit();
});