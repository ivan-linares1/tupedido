@extends('layouts.app')

@section('title', 'Clientes')

@section('contenido')
<div id="flash-messages" class="alert-top-end"></div>

<div class="card shadow-sm border-0 rounded-3 mt-4">
    <div class="card-header d-flex justify-content-between align-items-center bg-success text-white rounded-top">
        <h5 class="mb-0">
            <i class="bi bi-people-fill me-2"></i> Catálogo de Clientes
        </h5>
    </div>
    <div class="card-body">

        <!-- Filtros -->
        <div class="row mb-3 g-3 align-items-end">
            <div class="col-md-2">
                <label for="mostrar" class="form-label fw-semibold">Mostrar</label>
                <select id="mostrar" class="form-select form-select-sm rounded-3">
                    <option value="25" {{ request('mostrar') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('mostrar') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('mostrar') == 100 ? 'selected' : '' }}>100</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="estatus" class="form-label fw-semibold">Estatus</label>
                <select id="estatus" class="form-select form-select-sm rounded-3">
                    <option value="Todos">Todos</option>
                    <option value="Activos" {{ request('estatus') == 'Y' ? 'selected' : '' }}>Activos</option>
                    <option value="Inactivos" {{ request('estatus') == 'N' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>
            <div class="col-md-4 ms-auto">
                <label for="buscarCliente" class="form-label fw-semibold">Buscar</label>
                <div class="input-group input-group-sm rounded-3">
                    <input type="text" id="buscarCliente" class="form-control rounded-start" placeholder="Buscar cliente...">
                    <span class="input-group-text bg-white rounded-end"><i class="bi bi-search"></i></span>
                </div>
            </div>
        </div>

        <!-- Tabla -->
        <div class="table-responsive" id="tabla-container">
            @include('partials.tabla_cliente')
        </div>
        
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/catalogo.css') }}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/clientes.js') }}"></script>
<script>
    // Filtro de búsqueda
    $(document).ready(function() {
        function fetchClientes(url = "{{ route('clientes') }}") {
            const buscar = $('#buscarCliente').val();
            const estatus = $('#estatus').val();
            const mostrar = $('#mostrar').val();

            $.ajax({
                url: url,
                method: "GET",
                data: { buscar, estatus, mostrar },
                success: function(data) {
                    $('#tabla-container').html(data);
                },
                error: function() {
                    alert('Error al cargar los clientes.');
                }
            });
        }

        // Búsqueda al escribir
        $('#buscarCliente').on('keyup', function() {
            fetchClientes();
        });

        // Filtros de selects
        $('#estatus, #mostrar').on('change', function() {
            fetchClientes();
        });

        // Paginación AJAX
        $(document).on('click', '#tabla-container .pagination a', function(e) {
            e.preventDefault();
            const url = $(this).attr('href');
            fetchClientes(url);
            $('html, body').animate({ scrollTop: 0 }, 200);
        });
    });


</script>
@endpush