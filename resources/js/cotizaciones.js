//coloca los datos de la fecha del dia
const hoy = new Date().toISOString().split('T')[0];
document.getElementById('fechaCreacion').value = hoy;
document.getElementById('fechaEntrega').value = hoy;
//Evitar que el calendario permita escojer dias pasados
document.getElementById('fechaEntrega').setAttribute('min', hoy);

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
                    console.log(data);
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

window.agregarArticulo = function(art) {
    const tabla = document.querySelector("#tablaArticulos tbody");
    const fila = document.createElement("tr");

    const cantidadInicial = 1;

    // Suponemos que quieres el cambio de hoy
    const fechaHoy = new Date().toISOString().slice(0, 10);

    // Buscamos el cambio en la relación
    const cambio = art.precios[0].moneda.cambios.find(c => c.RateDate === fechaHoy); //consultando la base de datos 
    const rate = cambio ? parseFloat(cambio.Rate) : 1; //obteniendo el valor de rate de cambio

    // Calculamos el precio convertido
    const precioUnitario = parseFloat(art.precios[0].Price) * rate;
    const PrecioMXM = precioUnitario * cantidadInicial;

    fila.innerHTML = `
        <td><button class="btn btn-sm btn-danger" onclick="this.closest('tr').remove();calcularTotales()">X</button></td>
        <td>${art.ItemCode}</td>
        <td>${art.FrgnName}</td>
        <td>Unidad de medida</td>
        <td class="precio">${PrecioMXM}</td>
        <td>Moneda</td>
        <td>Impuesto</td>
        <td><input type="number" value="${cantidadInicial}" min="1" class="form-control form-control-sm cantidad"></td>
        <td>Promociones</td>
        <td class="total"></td>
        <td>% Descuento</td>
        <td>Presion tras el descuento</td>
        <td>Total Extranjero</td>
        <td>Precio Unit.Doc</td>
        <td>Total (doc)</td>
    `;

    // Inserta la fila antes de la última fila de la tabla (si la tienes)
    tabla.insertBefore(fila, tabla.lastElementChild);

    // Recalcular al cambiar la cantidad
    fila.querySelector('.cantidad').addEventListener('input', calcularTotales);

    calcularTotales();

    // Cierra el modal
    bootstrap.Modal.getInstance(document.getElementById('modalArticulos')).hide();
}

// Calcular totales generales
function calcularTotales() {
    const filas = document.querySelectorAll("#tablaArticulos tbody tr");

    filas.forEach(fila => {
        const cantidad = parseFloat(fila.querySelector(".cantidad")?.value || 0);
        const precio = parseFloat(fila.querySelector(".precio")?.textContent || 0);
        let total = cantidad * precio;
        
        if (fila.querySelector('.total')) {
            fila.querySelector('.total').textContent = total.toFixed(2);
        }
    });
}


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