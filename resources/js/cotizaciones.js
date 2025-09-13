//coloca los datos de la fecha del dia
const hoy = new Date().toISOString().split('T')[0];
document.getElementById('fechaCreacion').value = hoy;
document.getElementById('fechaEntrega').value = hoy;
//Evitar que el calendario permita escojer dias pasados
document.getElementById('fechaEntrega').setAttribute('min', hoy);
//Recibe el dato de monedas desde el controlador-vista-json-js
const monedas = JSON.parse(selectMoneda.dataset.monedas);
//Recibe el dato de monedas desde el controlador-vista-json-js
const IVA = JSON.parse(selectMoneda.dataset.iva);

//Hace que el imput del cliente permita realizar la busqueda con el mismo imput y select
$(document).ready(function() {
    $('#selectCliente').select2({
        placeholder: "Selecciona un cliente",
        allowClear: true,
        width: '100%'
    });
});


//realiza la consulta de las direcciones que correspondan al cliente seleccionado
$(document).ready(function () {
    $('#selectCliente').change(function () {
        let cardCode = $(this).val();
        if (cardCode) {
            $.ajax({
                url: `/cliente/${cardCode}/direcciones`,
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
    });
});

//se encarga de mostrar los datos de contacto del usuario
$(document).ready(function () {
    $('#selectCliente').on('change', function () {
        let selected = $(this).find('option:selected');
        let phone = selected.data('phone') || 'Sin teléfono';
        let email = selected.data('email') || 'Sin correo';

        let emailFormatted = email.split(',').join('<br>');

        if (email !== 'Sin correo') {
            // Convertir cada correo en <li>
            let emails = email.split(',').map(e => `<li>${e.trim()}</li>`).join('');
            emailFormatted = `<ul style="padding-left: 20px; margin: 0;">${emails}</ul>`;
        }

        $('#telefono').text("Telefono " + phone);
        $('#correo').html("Correos:<br>" + emailFormatted);
    });
})

//cada que cambiamos la moneda en el selector recalcula las monedas
document.getElementById('selectMoneda').addEventListener('change', function() {
    const monedaCambioID = parseInt(this.value);
    const monedaCambio = monedas.find(m => m.Currency_ID == monedaCambioID);

    // Recorremos todas las filas de la tabla y actualizamos el precio y la moneda
    const filas = document.querySelectorAll("#tablaArticulos tbody tr");
    filas.forEach(fila => {
        if (!fila.dataset.precioOriginal || !fila.dataset.monedaOriginal) {
            return; // saltamos filas que no tienen los atributos sino maerca error en la ultima fila dondo solo esta el boton agregar articulo
        }
        const precioOriginal = parseFloat(fila.dataset.precioOriginal); // Guardamos el precio original en un data attribute
        const monedaOriginal = JSON.parse(fila.dataset.monedaOriginal); // Igual, guardamos el objeto moneda original

        // Calculamos el precio convertido
        const precioConvertido = conversionesMonedas(precioOriginal, monedaOriginal, monedaCambio);
        fila.querySelector('.precio').textContent = precioConvertido.toFixed(2);

        // Actualizamos la columna de moneda
        fila.querySelector('.moneda').textContent = monedaCambio ? monedaCambio.Currency : monedaOriginal.Currency;
    });

    // Recalculamos totales
    calcularTotales();
});

//funcion que agrega los articulos al la tabla
window.agregarArticulo = function(art) {
    const tabla = document.querySelector("#tablaArticulos tbody");
    const fila = document.createElement("tr");

    // Guardamos el precio original y el objeto de moneda original en data attributes
    fila.dataset.precioOriginal = art.precio.Price;
    fila.dataset.monedaOriginal = JSON.stringify(art.precio.moneda);

    const monedaCambioID = parseInt(document.querySelector('select[name="currency_id"]').value);//guarda el id de la moneda seleccionada
    const monedaCambio =  monedas.find(m => m.Currency_ID == monedaCambioID);//obtiene el arrglo de la moneda escojida completo con su relacion de cambios
                                        //precio decimal,  arreglo de moneda, arreglo de moneda
    const precio = conversionesMonedas( art.precio.Price, art.precio.moneda, monedaCambio);//se envian los arreglos compeltos para poder realizar las consultas  

    // Cliente seleccionado
    const clienteSelect = document.getElementById('selectCliente');
    const clienteOption = clienteSelect.options[clienteSelect.selectedIndex];
    const descuentosCliente = clienteOption ? JSON.parse(clienteOption.dataset.descuentos || '[]') : [];
    console.log(descuentosCliente);
    // Descuento según grupo de artículo
    const detalle = descuentosCliente.find(det => String(det.ObjKey) === String(art.ItmsGrpCod));
    const descuento = detalle ? parseFloat(detalle.Discount) : 0;
    console.log(detalle, descuento);

    
    //<td class="imagen"><img src="${art.imagen?.Ruta_imagen}" alt="Imagen del artículo" style="width: 50px; height: auto;"></td>
    fila.innerHTML = `
        <td><button class="btn btn-sm btn-danger">X</button></td>
        <td class="itemcode">${art.ItemCode}</td>
        <td class="frgnName">${art.FrgnName}</td>
        <td class="imagen">Imagen</td>
        <td class="medida">Unidad de medida</td>
        <td class="precio">${precio}</td>
        <td class="moneda">${monedaCambio ? monedaCambio.Currency : art.precio.moneda.Currency}</td>
        <td class="iva">IVA ${IVA}%</td>
        <td><input type="number" value="1" min="1" class="form-control form-control-sm cantidad"></td>
        <td class="promocion">Promociones</td>
        <td class="total"></td>
        <td class="descuentoporcentaje">${descuento} %</td>
        <td class="desMoney">descuento$</td>
        <td class="total" >Total (doc)</td>
    `;

    //EVENTOS
    // Inserta la fila antes de la última fila de la tabla (si la tienes)
    tabla.insertBefore(fila, tabla.lastElementChild);

    // Recalcular al cambiar la cantidad
    fila.querySelector('.cantidad').addEventListener('input', calcularTotales);
    
    // eliminar fila al presionar el boton
    fila.querySelector('button').addEventListener('click', function() {
        eliminarFila(this);
    });

    calcularTotales();

    // Cierra el modal
    bootstrap.Modal.getInstance(document.getElementById('modalArticulos')).hide();
}

//Funcion para hacer las conversiones de monedas
function conversionesMonedas(precioOriginal, monedaOriginal, monedaConvertir)
{
    //si no se llega una moneda a convertir 
    if(!monedaConvertir)
        return parseFloat(precioOriginal).toFixed(2);

    let rate = monedaOriginal.cambios[0]?.Rate ?? 1; //obteniendo el valor de rate de cambio

    // Calculamos el precio base para los proximos calculos
    const precioBase = parseFloat(precioOriginal) * rate;

    //buscamos el nuevo tipo de cambio
    rate = monedaConvertir.cambios[0]?.Rate ?? 1;
    //Realizamos el cambio de moneda en precio
    return precioBase / rate;
}

// Calcular totales generales
function calcularTotales() {
    const filas = document.querySelectorAll("#tablaArticulos tbody tr");
    let TotalAntesDescuento = 0;

    filas.forEach(fila => {
        const cantidad = parseFloat(fila.querySelector(".cantidad")?.value || 0);
        const precio = parseFloat(fila.querySelector(".precio")?.textContent || 0);
        let total = cantidad * precio;
        TotalAntesDescuento += total;
        
        if (fila.querySelector('.total')) {
            fila.querySelector('.total').textContent = total.toFixed(2); //cambia el total de la fila
        }
    }); 

   document.getElementById('totalAntesDescuento').textContent = `$${TotalAntesDescuento.toFixed(2)} ${filas[0]?.querySelector('td.moneda')?.textContent || ''}`;//cambiar el valor del total antes del descuento por el nuevo total
}

// Función global para eliminar fila
function eliminarFila(boton) {
    boton.closest("tr").remove();
    calcularTotales();
}


//jquery que se encarga de los filtros en el modal de articulos
$(document).ready(function() {
    var tablaModal = $('#tablaModalArticulos').DataTable({
        pageLength: 25, // por defecto
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
        },
        ordering: false,
        searching: true
    });

    // Buscar en la tabla
    $('#buscadorModal').on('keyup', function() {
        tablaModal.search(this.value).draw();
    });

    // Cambiar cantidad de filas por página
    $('#filtroMostrar').on('change', function() {
        tablaModal.page.len($(this).val()).draw();
    });

    // Reiniciar filtros al cerrar el modal
    $('#modalArticulos').on('hidden.bs.modal', function () {
        // resetear buscador
        $('#buscadorModal').val('');
        tablaModal.search('').draw();

        // resetear select a 25
        $('#filtroMostrar').val('25');
        tablaModal.page.len(25).draw();

        // volver a la primera página
        tablaModal.page('first').draw('page');
    });
});


//Funcion para guardar los datos en un form oculto y mandarlos alcontrolador 
$("#guardarCotizacion").on("click", function() {
    // Llenamos los inputs con los valores actuales de tu página
    $("#cliente").val($("#selectCliente").val());//CardCode
    $("#telefono").val($("#telefono").text());
    $("#correo").val($("#correo").text());
    $("#direccionFiscal").val($("#direccionFiscal").text());//address
    $("#direccionEntrega").val($("#direccionEntrega").text());//address2

    $("#fechaCreacionInput").val($("#fechaCreacion").val());//docDate
    $("#fechaEntregaInput").val($("#fechaEntrega").val());//docDueDate
    $("#monedaInput").val($("#selectMoneda").val());

    $("#totalAntesDescuentoInput").val($("#totalAntesDescuento").text());
    $("#totalConDescuentoInput").val($("#totalConDescuento").text());
    $("#ivaInput").val($("#iva").text());
    $("#totalInput").val($("#total").text());

    // Recopilar artículos de la tabla
    let articulos = [];
    $("#tablaArticulos tbody tr").each(function() {
        articulos.push({
            itemCode: $(this).find(".itemcode").text(),
            descripcion: $(this).find(".frgnName").text(),
            precio: $(this).find(".precio").text(),
            cantidad: $(this).find(".cantidad").val(),
            moneda: $(this).find(".moneda").text(),
            total: $(this).find(".total").text(),
            iva: $(this).find(".iva").text(),
            descuentoPorcentaje: $(this).find(".descuentoporcentaje").text(),
            descuentoMoney: $(this).find(".desMoney").text(),
            totalDoc: $(this).find(".totalDoc").text()
        });
    });

    $("#articulosInput").val(JSON.stringify(articulos));

    // Enviar el form
    $("#formCotizacion").submit();
});