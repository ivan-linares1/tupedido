@extends('layouts.app')

@section('title', 'Catálogo de Grupos de Productos')

@section('contenido')

<div id="flash-messages" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    {{-- Aquí se insertarán los alerts --}}
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white rounded-top">
        <h5 class="mb-0">
            <i class="bi bi-tags-fill me-2"></i> Catálogo de Grupos de Productos
        </h5>
    </div>

    <div class="card-body">
        {{-- Filtros --}}
        <div class="row mb-4 g-3 align-items-end">
            <div class="col-md-2">
                <label for="mostrar" class="form-label fw-semibold">Mostrar</label>
                <select id="mostrar" class="form-select form-select-sm rounded-3">
                    <option>25</option>
                    <option>50</option>
                    <option>100</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="estatus" class="form-label fw-semibold">Estatus</label>
                <select id="estatus" class="form-select form-select-sm rounded-3">
                    <option selected>Todos</option>
                    <option>Activo</option>
                    <option>Inactivo</option>
                </select>
            </div>
            <div class="col-md-4 ms-auto">
                <label for="buscar" class="form-label fw-semibold">Buscar</label>
                <div class="input-group input-group-sm rounded-3">
                    <input type="text" id="buscar" class="form-control rounded-start" placeholder="Buscar Grupo de Producto...">
                    <span class="input-group-text bg-white rounded-end"><i class="bi bi-search"></i></span>
                </div>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="table-responsive">
            <table id="tablaMarcas" class="table table-hover table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ItmsGrpCod</th>
                        <th>ItmsGrpNam</th>
                        <th>Estatus</th>
                        <th>Object</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($marcas as $item)
                        <tr>
                            <td>{{ $item->ItmsGrpCod }}</td>
                            <td>{{ $item->ItmsGrpNam }}</td>
                            <td class="status-text">{{ $item->Locked == 'N' ? 'Activo' : 'Inactivo' }}</td>
                            <td>{{ $item->Object }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

<style>
/* Paginación a la derecha */
.dataTables_wrapper .dataTables_paginate {
    float: right !important;
    margin-top: 10px;
}

/* Texto de información alineado a la izquierda */
.dataTables_wrapper .dataTables_info {
    float: left;
    margin-top: 10px;
}
</style>


@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    var table = $('#tablaMarcas').DataTable({
        pageLength: 25,
        lengthChange: false,
        dom: 'rtip',
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.8/i18n/es-MX.json"
        }
    });

    // Mostrar N registros
    $('#mostrar').on('change', function() {
        const val = parseInt($(this).val()) || 25;
        table.page.len(val).draw();
    });

    // Filtro estatus
    $('#estatus').on('change', function() {
        const raw = $(this).val();
        let filtro = '';
        if(!raw || raw.toLowerCase() === 'todos') filtro = '';
        else if(raw.toLowerCase() === 'activo') filtro = '^Activo$';
        else filtro = '^Inactivo$';
        table.column(2).search(filtro, true, false, true).draw();
    });

    // Buscar dinámico
    $('#buscar').on('keyup', function() {
        table.search(this.value).draw();
    });
});
</script>



@endpush
