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

    function showFlash(message, type = "success") {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: type,
            title: message,
            showConfirmButton: false,
            timer: 2000
        });
    }

    // Toggle estado con confirmación SweetAlert2
    $(document).on('change', '.toggle-estado', function (e) {
        e.preventDefault();
        const $this = $(this);
        const id = $this.data('id');
        const url = $this.data('url');
        const field = $this.data('field');
        const newValue = $this.is(':checked') ? 'Y' : 'N';
        const prevState = !$this.is(':checked');

        // Detener el toggle hasta confirmar
        $this.prop('checked', prevState);

        Swal.fire({
            title: '¿Estás seguro?',
            text: `Vas a cambiar el estado a ${newValue === 'Y' ? 'Activo' : 'Inactivo'}`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#05564f',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $this.prop('disabled', true);
                $.post(url, {_token: document.querySelector('meta[name="csrf-token"]').content, id: id, field: field, value: newValue})
                .done(function(response){
                    if(response.success){
                        $this.prop('checked', newValue === 'Y');
                        $this.closest('tr').attr('data-status', newValue);
                        showFlash("Estado actualizado correctamente", "success");
                    } else {
                        showFlash("Error al actualizar", "error");
                    }
                })
                .fail(function(){
                    showFlash("Error de conexión", "error");
                })
                .always(function(){
                    $this.prop('disabled', false);
                });
            }
        });
    });

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