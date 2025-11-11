//Aqui se validan los alerts, los imputs y la animacion de cargando

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



//valida las animaciones de cargando...
document.addEventListener('DOMContentLoaded', function() {
    const loading = document.getElementById('loading');

    // Mostrar loader al enviar formularios dentro de sincronizadores-panel
    document.querySelectorAll('.sincronizadores-panel form').forEach(form => {
        form.addEventListener('submit', function() {
            loading.style.display = 'grid';
        });
    });

    // Mostrar loader al hacer clic en enlaces con data-loading="true"
    document.querySelectorAll('a[data-loading="true"]').forEach(link => {
        link.addEventListener('click', function() {
            loading.style.display = 'grid';
            // no evitamos la redirección — solo mostramos el loader
        });
    });
});