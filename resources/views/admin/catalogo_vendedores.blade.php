@extends('layouts.app')

@section('title', 'Dashboard')

@section('contenido')

<div id="flash-messages" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    {{-- Aquí se insertarán los alerts --}}
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-header d-flex justify-content-between align-items-center bg-success text-white rounded-top">
        <h5 class="mb-0">
            <i class="bi bi-person-fill me-2"></i> Catálogo de Vendedores
        </h5>
    </div>

    <div class="card-body">
        <div class="row mb-4 g-3 align-items-end">
            <div class="col-md-2">
                <label for="mostrar" class="form-label fw-semibold">Mostrar</label>
                <select id="mostrar" class="form-select form-select-sm rounded-3">
                    <option>10</option>
                    <option selected>25</option>
                    <option>50</option>
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
                    <input type="text" id="buscar" class="form-control rounded-start" placeholder="Buscar Vendedor...">
                    <span class="input-group-text bg-white rounded-end"><i class="bi bi-search"></i></span>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table id="tablaVendedores" class="table table-hover table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>SlpCode</th>
                        <th>SlpName</th>
                        <th>Status</th>
                        <th class="text-center">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vendedores as $item)
                        <tr data-id="{{ $item->SlpCode }}">
                            <td>{{ $item->SlpCode }}</td>
                            <td>{{ $item->SlpName }}</td>
                            <td class="status-text">{{ $item->Active == 'Y' ? 'Activo' : 'Inactivo' }}</td>
                            <td class="text-center">
                                {{-- Toggle estilo iOS --}}
                                <label class="switch">
                                    <input 
                                        type="checkbox" 
                                        class="toggle-estado-vendedor"
                                        data-id="{{ $item->SlpCode }}"
                                        data-url="{{ route('admin.vendedores.toggleActivo') }}"
                                        {{ $item->Active == 'Y' ? 'checked' : '' }}
                                    >
                                    <span class="slider round"></span>
                                </label>
                            </td>
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
/* Toggle estilo iOS */
.switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 26px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  transition: .4s;
  border-radius: 26px;
}

.slider:before {
  position: absolute;
  content: "";
  height: 20px;
  width: 20px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
}

input:checked + .slider {
  background-color: #28a745; /* verde */
}

input:focus + .slider {
  box-shadow: 0 0 1px #28a745;
}

input:checked + .slider:before {
  transform: translateX(24px);
}

.slider.round {
  border-radius: 26px;
}

/* Paginación derecha */
.dataTables_wrapper .dataTables_paginate {
    float: right;
}
</style>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    var table = $('#tablaVendedores').DataTable({
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

    // Toggle estado con confirmación
    $(document).on('change', '.toggle-estado-vendedor', function () {
        var $checkbox = $(this);
        var id = $checkbox.data('id');
        var url = $checkbox.data('url');
        var newState = $checkbox.is(':checked') ? 'Y' : 'N';
        var prevState = newState === 'Y' ? 'N' : 'Y';
        var $row = $checkbox.closest('tr');
        var $statusCell = $row.find('.status-text');

        // Detener cambio visual hasta confirmar
        $checkbox.prop('checked', prevState === 'Y');

        Swal.fire({
            title: '¿Estás seguro?',
            text: "Vas a cambiar el estado del vendedor a " + (newState === 'Y' ? "Activo" : "Inactivo"),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#05564f',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if(result.isConfirmed){
                $checkbox.prop('disabled', true);

                $.post(url, {_token: "{{ csrf_token() }}", id: id}, function(response){
                    if(response.success){
                        // Actualizar UI
                        $checkbox.prop('checked', newState === 'Y');
                        var newText = (newState === 'Y' ? 'Activo' : 'Inactivo');
                        $statusCell.text(newText);

                        // 👉 Actualizar DataTables para que el filtro funcione correctamente
                        table.cell($statusCell.get(0)).data(newText);
                        table.row($row.get(0)).invalidate().draw(false);

                        Swal.fire({
                            toast:true,
                            position:'top-end',
                            icon:'success',
                            title:'Estado actualizado correctamente',
                            showConfirmButton:false,
                            timer:2000
                        });
                    } else {
                        Swal.fire('Error','No se pudo actualizar el estado','error');
                        $checkbox.prop('checked', prevState === 'Y');
                        var prevText = (prevState === 'Y' ? 'Activo' : 'Inactivo');
                        $statusCell.text(prevText);
                        table.cell($statusCell.get(0)).data(prevText);
                        table.row($row.get(0)).invalidate().draw(false);
                    }
                }).fail(function(){
                    Swal.fire('Error','Ocurrió un error de conexión','error');
                    $checkbox.prop('checked', prevState === 'Y');
                    var prevText = (prevState === 'Y' ? 'Activo' : 'Inactivo');
                    $statusCell.text(prevText);
                    table.cell($statusCell.get(0)).data(prevText);
                    table.row($row.get(0)).invalidate().draw(false);
                }).always(function(){
                    $checkbox.prop('disabled', false);
                });
            } else {
                $checkbox.prop('checked', prevState === 'Y');
            }
        });
    });

});
</script>

@endpush
