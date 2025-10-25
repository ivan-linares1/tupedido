//solo permite que se escriban numeros en los inputs
$(document).on('input', '.cantidad, #telefono', function() {
    this.value = this.value.replace(/[^0-9]/g, ''); 
});

//No permite que se inserten las las letras e y algunos simbolos de operacion y el punto
$(document).on('keydown', '.cantidad, #telefono', function(e) {
    if (["e", "E", "+", "-", "."].includes(e.key)) {
        e.preventDefault();
    }
});

//acciones de las alerts 
document.addEventListener('DOMContentLoaded', function() {
    // Cerrar manualmente
    document.querySelectorAll('.panel-alert .close-btn').forEach(function(btn){
        btn.addEventListener('click', function(){
            this.parentElement.remove();
        });
    });

    // Cerrar automáticamente después de 10 segundos
    setTimeout(() => {
        document.querySelectorAll('.panel-alert').forEach(function(alert){
            alert.remove();
        });
    }, 6000);
});


//acciones de los cambios de estado de los catalogos de clientes y productos
$(document).ready(function() {
    // Búsqueda dinámica
    $('#buscarCliente').on('keyup', function() {
        const valor = $(this).val().toLowerCase();
        $('#tablaClientes tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(valor) > -1);
        });
    });

    // Filtro Mostrar y Estatus
    $('#mostrar').on('change', function(){
        let val = $(this).val();
        $('#tablaClientes tbody tr').slice(val).hide();
        $('#tablaClientes tbody tr').slice(0, val).show();
    });

    $('#estatus').on('change', function(){
        let val = $(this).val();
        $('#tablaClientes tbody tr').each(function(){
            let status = $(this).attr('data-status');
            if(val === "" || status === val){
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Inicial: aplicar filtro mostrar
    $('#mostrar').trigger('change');
});