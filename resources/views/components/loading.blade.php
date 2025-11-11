<!-- Componente Loading Mejorado se agrego de intenet solo se modifico el numero de pelotas y el overlay de pantalla completa
https://www.cadabullos.com/blog/como-crear-loader-anmimado-usando-solo-css-ejemplos -->

<div id="loading" style="display:none;">
    <div class="loading-overlay">
        <div class="cargando">
            <div class="pelotas-container">
                <div class="pelotas"></div>
                <div class="pelotas"></div>
                <div class="pelotas"></div>
                <div class="pelotas"></div>
            </div>
            <span class="texto-cargando">Cargando...</span>
        </div>
    </div>
</div>

<style>
/* Overlay Fullscreen */
#loading .loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    display: grid;
    place-items: center;
    z-index: 9999;
}

/* Componente Loading Aislado */
.cargando {
    display: flex;
    flex-direction: column;        /* pelotas arriba, texto abajo */
    align-items: center;           /* centrado horizontal */
    justify-content: center;
}

/* Contenedor de pelotas */
.pelotas-container {
    display: flex;
    justify-content: space-between;
    width: 160px;
    height: 40px;
    margin-bottom: 20px;           /* separa texto de pelotas */
}

/* Texto Cargando Mejorado */
.cargando .texto-cargando {
    font-size: 50px;
    text-transform: uppercase;
    color: #7FDBFF;
    font-family: Arial, Helvetica, sans-serif;
    font-weight: 900;
    text-align: center;
}

/* Pelotas */
.cargando .pelotas {
    width: 35px;
    height: 35px;
    background: linear-gradient(135deg, #094ee4, #00b8de);
    border-radius: 50%;
    animation: salto 0.5s alternate infinite;
}

.cargando .pelotas:nth-child(2) { animation-delay: 0.15s; }
.cargando .pelotas:nth-child(3) { animation-delay: 0.30s; }
.cargando .pelotas:nth-child(4) { animation-delay: 0.45s; }

@keyframes salto {
    from { transform: scaleX(1.25); }
    to { transform: translateY(-60px) scaleX(1); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('.sincronizadores-panel form');
    const loading = document.getElementById('loading');

    forms.forEach(form => {
        form.addEventListener('submit', function() {
            loading.style.display = 'grid'; // mostrar el loading
        });
    });
});
</script>
