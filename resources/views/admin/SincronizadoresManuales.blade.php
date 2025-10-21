@extends('layouts.app')

@section('title', 'Panel de Sincronizadores')

@section('contenido')
    @vite(['resources/css/sincronizadores.css'])


<div class="container mt-5 sincronizadores-panel">
    <h1>Panel de Control de Sincronizadores Manuales</h1>
    <x-loading />

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
            <form action="{{ route('SincronizarAux', ['servicio'=>'Descuentos_Detalle', 'metodo'=>'SBO_Grupos_Descuentos_EDG1']) }}" method="POST">
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

@endsection
