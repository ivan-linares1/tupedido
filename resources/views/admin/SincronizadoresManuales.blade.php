@extends('layouts.app')

@section('title', 'Panel de Sincronizadores')

@section('contenido')

<style>
/* Contenedor principal */
.sincronizadores-panel {
    max-width: 1400px;
    padding: 50px 20px;
    background-color: #f8f9fa;
    border-radius: 20px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
}

/* Título principal */
.sincronizadores-panel h1 {
    color: #343a40;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 50px;
    text-align: center;
    text-shadow: 1px 1px 3px rgba(0,0,0,0.1);
}

/* Tarjetas */
.sincronizadores-panel .card {
    border-radius: 20px;
    transition: transform 0.3s, box-shadow 0.3s;
    cursor: pointer;
    overflow: hidden;
    box-shadow: 0 6px 18px rgba(0,0,0,0.12);
    background: #ffffff;
    min-height: 260px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

/* Hover en tarjetas */
.sincronizadores-panel .card:hover {
    transform: translateY(-10px) scale(1.03);
    box-shadow: 0 15px 30px rgba(0,0,0,0.25);
}

/* Iconos circulares */
.sincronizadores-panel .card i {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px auto;
    border-radius: 50%;
    font-size: 2.5rem;
    color: #fff;
    transition: transform 0.3s, box-shadow 0.3s;
}

/* Diferentes iconos y colores por tipo */
.icon-monedas { background: #0d6efd; }       /* Azul */
.icon-marcas { background: #6f42c1; }        /* Morado */
.icon-categorias { background: #198754; }    /* Verde */
.icon-articulos { background: #fd7e14; }     /* Naranja */
.icon-precios { background: #dc3545; }       /* Rojo */
.icon-clientes { background: #0dcaf0; }   /* Celeste / Turquesa */
.icon-direcciones { background: #20c997; }  /* Verde azulado tipo */
.icon-descuentos {background: #ffc107; }/* Amarillo dorado */
.icon-descuento {background: #28a745; }/* verde claro*/


.sincronizadores-panel .card:hover i {
    transform: scale(1.25);
    box-shadow: 0 6px 20px rgba(0,0,0,0.25);
}

/* Títulos y textos */
.sincronizadores-panel .card-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: #212529;
    margin-bottom: 10px;
    text-align: center;
}

.sincronizadores-panel .card-text {
    font-size: 0.95rem;
    color: #495057;
    margin-bottom: 20px;
    text-align: center;
}

/* Botones */
.sincronizadores-panel .btn {
    font-weight: 600;
    padding: 12px 0;
    border-radius: 12px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.12);
    transition: all 0.3s;
    font-size: 1rem;
}

.sincronizadores-panel .btn-success {
    background-color: #20c997;
    border: none;
}

.sincronizadores-panel .btn-success:hover {
    background-color: #198754;
    transform: scale(1.05);
    box-shadow: 0 6px 15px rgba(25,135,84,0.4);
}

/* Responsive */
@media (max-width: 992px) {
    .sincronizadores-panel .card i { width: 70px; height: 70px; font-size: 2rem; }
    .sincronizadores-panel .card-title { font-size: 1.2rem; }
    .sincronizadores-panel .card-text { font-size: 0.9rem; }
    .sincronizadores-panel .btn { font-size: 0.95rem; padding: 10px 0; }
}

@media (max-width: 576px) {
    .sincronizadores-panel .card { min-height: 240px; }
    .sincronizadores-panel .card i { width: 60px; height: 60px; font-size: 1.7rem; }
    .sincronizadores-panel .card-title { font-size: 1.1rem; }
    .sincronizadores-panel .card-text { font-size: 0.85rem; }
}
</style>

<div class="container mt-5 sincronizadores-panel">
    <h1>Panel de Control de Sincronizadores Manuales</h1>

    <div class="row g-4 justify-content-center">

        <!-- Monedas OCRN-->
        <div class="col-md-3 col-sm-6">
            <form action="{{ route('Sincronizar', ['servicio'=>'Monedas', 'metodo'=>'SBOMonedas_OCRN']) }}" method="POST">
                @csrf
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-currency-dollar icon-monedas"></i>
                        <h5 class="card-title">Monedas</h5>
                        <p class="card-text">Sincroniza todas las monedas existentes.</p>
                        <button type="submit" class="btn btn-success w-100">Ejecutar</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Marcas OITB-->
        <div class="col-md-3 col-sm-6">
            <form action="{{ route('Sincronizar', ['servicio'=>'Marcas', 'metodo'=>'SBO_GPO_Articulo_OITB']) }}" method="POST">
                @csrf
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-grid-1x2-fill icon-marcas"></i>
                        <h5 class="card-title">Grupos de Artículos</h5>
                        <p class="card-text">Sincroniza los grupos de artículos o marcas.</p>
                        <button type="submit" class="btn btn-success w-100">Ejecutar</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Categorías Lista Precios OPLN-->
        <div class="col-md-3 col-sm-6">
            <form action="{{ route('Sincronizar', ['servicio'=>'Categoria_Lista_Precios', 'metodo'=>'SBO_CAT_LP_OPLN']) }}" method="POST">
                @csrf
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-list-check icon-categorias"></i>
                        <h5 class="card-title">Cat. Listas Precios</h5>
                        <p class="card-text">Sincroniza las categorías de listas de precios.</p>
                        <button type="submit" class="btn btn-success w-100">Ejecutar</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Artículos OITM-->
        <div class="col-md-3 col-sm-6">
            <form action="{{ route('Sincronizar', ['servicio'=>'Articulos', 'metodo'=>'SBOArticulos_OITM']) }}" method="POST">
                @csrf
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-box-seam icon-articulos"></i>
                        <h5 class="card-title">Artículos</h5>
                        <p class="card-text">Sincroniza todos los artículos disponibles.</p>
                        <button type="submit" class="btn btn-success w-100">Ejecutar</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Lista de Precios ITM1 -->
        <div class="col-md-3 col-sm-6">
            <form action="{{ route('Sincronizar', ['servicio'=>'Lista_Precios', 'metodo'=>'SBOListaPrecios_ITM1']) }}" method="POST">
                @csrf
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-cash-stack icon-precios"></i>
                        <h5 class="card-title">Lista de Precios</h5>
                        <p class="card-text">Sincroniza los precios de lista.</p>
                        <button type="submit" class="btn btn-success w-100">Ejecutar</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Clientes OCRD-->
        <div class="col-md-3 col-sm-6">
            <form action="{{ route('Sincronizar', ['servicio'=>'Clientes', 'metodo'=>'SBO_Clientes_OCRD']) }}" method="POST">
                @csrf
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-people-fill icon-clientes"></i>
                        <h5 class="card-title">Lista de Clientes</h5>
                        <p class="card-text">Sincroniza la lista de Clientes.</p>
                        <button type="submit" class="btn btn-success w-100">Ejecutar</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Direcciones de Clientes CRD1-->
        <div class="col-md-3 col-sm-6">
            <form action="{{ route('Sincronizar', ['servicio'=>'Direcciones', 'metodo'=>'SBO_Clientes_Direcciones_CRD1']) }}" method="POST">
                @csrf
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-geo-alt-fill icon-direcciones"></i>
                        <h5 class="card-title">Direcciones de Clientes</h5>
                        <p class="card-text">Sincroniza la direccion de Clientes.</p>
                        <button type="submit" class="btn btn-success w-100">Ejecutar</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Grupos de Desceuntos OEDG-->
        <div class="col-md-3 col-sm-6">
            <form action="{{ route('Sincronizar', ['servicio'=>'Grupo_Descuentos', 'metodo'=>'SBO_Grupos_Descuentos_OEDG']) }}" method="POST">
                @csrf
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-tags-fill icon-descuentos"></i>
                        <h5 class="card-title">Grupos de Descuentos</h5>
                        <p class="card-text">Sincroniza grupos de descuentos.</p>
                        <button type="submit" class="btn btn-success w-100">Ejecutar</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Desceuntos EDG1-->
        <div class="col-md-3 col-sm-6">
            <form action="{{ route('Sincronizar', ['servicio'=>'Descuentos_Detalle', 'metodo'=>'SBO_Grupos_Descuentos_EDG1']) }}" method="POST">
                @csrf
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-percent icon-descuento"></i>
                        <h5 class="card-title">Descuentos</h5>
                        <p class="card-text">Sincroniza los descuentos.</p>
                        <button type="submit" class="btn btn-success w-100">Ejecutar</button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
