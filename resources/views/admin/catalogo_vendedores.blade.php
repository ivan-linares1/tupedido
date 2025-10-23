@extends('layouts.app')

@section('title', 'Vendedores')

@section('contenido')
<div id="flash-messages" class="alert-top-end"></div>

<div class="card shadow-sm border-0 rounded-3 mt-4">
    <div class="card-header d-flex justify-content-between align-items-center bg-success text-white rounded-top">
        <h5 class="mb-0">
            <i class="bi bi-person-fill me-2"></i> Catálogo de Vendedores
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
                    <option value="Y" {{ request('estatus') == 'Y' ? 'selected' : '' }}>Activos</option>
                    <option value="N" {{ request('estatus') == 'N' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>

            <div class="col-md-4 ms-auto">
                <label for="buscarVendedor" class="form-label fw-semibold">Buscar</label>
                <div class="input-group input-group-sm rounded-3">
                    <input type="text" id="buscarVendedor" class="form-control rounded-start" placeholder="Buscar vendedor...">
                    <span class="input-group-text bg-white rounded-end"><i class="bi bi-search"></i></span>
                </div>
            </div>
        </div>

        <!-- Tabla -->
        <div class="table-responsive" id="tabla-container">
            @include('partials.tabla_vendedores')
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/catalogo.css') }}">
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
  background-color: #28a745;
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
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {

    function fetchVendedores(url = "{{ route('admin.catalogo.vendedores') }}") {
        const buscar = $('#buscarVendedor').val();
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
                Swal.fire('Error', 'No se pudo cargar la lista de vendedores.', 'error');
            }
        });
    }

    // 🔍 Buscar mientras se escribe
    $('#buscarVendedor').on('keyup', function() {
        fetchVendedores();
    });

    // 🎚️ Filtrar por estatus o cantidad
    $('#estatus, #mostrar').on('change', function() {
        fetchVendedores();
    });

    // 🔄 Paginación AJAX
    $(document).on('click', '#tabla-container .pagination a', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        fetchVendedores(url);
        $('html, body').animate({ scrollTop: 0 }, 200);
    });

    // ✅ Cambiar estado Activo/Inactivo
    $(document).on('change', '.toggle-estado-vendedor', function () {
        var $checkbox = $(this);
        var id = $checkbox.data('id');
        var url = $checkbox.data('url');
        var newState = $checkbox.is(':checked') ? 'Y' : 'N';
        var prevState = newState === 'Y' ? 'N' : 'Y';
        var $row = $checkbox.closest('tr');
        var $statusCell = $row.find('.status-text');

        $checkbox.prop('checked', prevState === 'Y');

        Swal.fire({
            title: '¿Estás seguro?',
            text: "Cambiarás el estado del vendedor a " + (newState === 'Y' ? "Activo" : "Inactivo"),
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
                        $checkbox.prop('checked', newState === 'Y');
                        $statusCell.text(newState === 'Y' ? 'Activo' : 'Inactivo');
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
                        $statusCell.text(prevState === 'Y' ? 'Activo' : 'Inactivo');
                    }
                }).fail(function(){
                    Swal.fire('Error','Error de conexión con el servidor','error');
                    $checkbox.prop('checked', prevState === 'Y');
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
