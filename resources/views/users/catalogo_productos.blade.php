@extends('layouts.app')

@section('title', 'Productos')

@section('contenido')

<div id="flash-messages" class="alert-top-end"></div>

<div class="card shadow-sm border-0 rounded-3 mt-4">
    <div class="card-header d-flex justify-content-between align-items-center bg-success text-white rounded-top">
        <h5 class="mb-0">
            <i class="bi bi-box-seam me-2"></i> Catálogo de Productos / Servicios
        </h5>
    </div>
    <div class="card-body">

        <!-- Filtros -->
        <form method="GET" action="{{ route('articulos') }}">
            <div class="row mb-3 align-items-end g-3">
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Mostrar</label>
                    <select class="form-select form-select-sm rounded-3" name="mostrar" onchange="this.form.submit()">
                        <option {{ request('mostrar') == 25 ? 'selected' : '' }}>25</option>
                        <option {{ request('mostrar') == 50 ? 'selected' : '' }}>50</option>
                        <option {{ request('mostrar') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>

                @if(Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2)
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Estatus</label>
                    <select class="form-select form-select-sm rounded-3" name="estatus" onchange="this.form.submit()">
                        <option value="Activos" {{ request('estatus') == 'Activos' ? 'selected' : '' }}>Activos</option>
                        <option value="Inactivos" {{ request('estatus') == 'Inactivos' ? 'selected' : '' }}>Inactivos</option>
                        <option value="Todos" {{ request('estatus') == 'Todos' ? 'selected' : '' }}>Todos</option>
                    </select>
                </div>
                @endif

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Grupo de Productos</label>
                    <select class="form-select form-select-sm rounded-3" name="grupo" onchange="this.form.submit()">
                        <option value="">Selecciona una marca...</option>
                        @foreach ($marcas as $marca)
                            <option value="{{ $marca->ItmsGrpCod }}" {{ request('grupo') == $marca->ItmsGrpCod ? 'selected' : '' }}>
                                {{ $marca->ItmsGrpNam }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 ms-auto">
                    <label for="buscarArticulo" class="form-label fw-semibold">Buscar</label>
                    <div class="input-group input-group-sm rounded-3">
                        <input type="text" id="buscarArticulo" name="buscar" class="form-control rounded-start" placeholder="Buscar producto...">
                        <span class="input-group-text bg-white rounded-end"><i class="bi bi-search"></i></span>
                    </div>
                </div>
            </div>
        </form>

        <!-- Tabla -->
        <div class="table-responsive" id="tabla-container">
            @include('partials.tabla_articulos')
        </div>

    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/catalogo.css') }}">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/clientes.js') }}"></script>
<script>
    // Filtro de búsqueda
    $(document).ready(function() {
    function fetchArticulos( url = "{{ route('articulos') }}") {
        const buscar = $('#buscarArticulo').val();
        const estatus = $('select[name="estatus"]').val();
        const grupo = $('select[name="grupo"]').val();
        const mostrar = $('select[name="mostrar"]').val();

        $.ajax({
            url: url,
            method: "GET",
            data: { buscar, estatus, grupo, mostrar },
            success: function(data) {
                $('#tabla-container').html(data);
            },
            error: function() {
                alert('Error al cargar los articulos.');
            }
        });
    }

    // Búsqueda al escribir
    $('#buscarArticulo').on('keyup', function() {
        fetchArticulos();
    });

    // Filtros de selects
    $('select[name="estatus"], select[name="grupo"], select[name="mostrar"]').on('change', function() {
        fetchArticulos();
    });

    // Paginación AJAX
    $(document).on('click', '#tabla-container .pagination a', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        fetchArticulos(url);
        // Mover scroll al inicio del contenedor o de la página
        $('html, body').animate({ scrollTop: 0 }, 200);
    });
});

</script>
@endpush