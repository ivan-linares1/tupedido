@extends('layouts.app')

@section('title', 'Usuarios')

@section('contenido')

{{-- Contenedor para mensajes flash dinámicos --}}
<div id="flash-messages" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white rounded-top">
        <h5 class="mb-0">Usuarios</h5>
        <div>
            <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">Nuevo Cliente</button>
            <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoVendedor">Nuevo Vendedor</button>
        </div>
    </div>

    <div class="card-body">

        <!-- Filtros -->
        <form method="GET" action="usuarios">
            <div class="row mb-3 align-items-end g-3">
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Mostrar</label>
                    <select class="form-select form-select-sm rounded-3" name="mostrar" onchange="this.form.submit()">
                        <option {{ request('mostrar') == 25 ? 'selected' : '' }}>25</option>
                        <option {{ request('mostrar') == 50 ? 'selected' : '' }}>50</option>
                        <option {{ request('mostrar') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">Estatus</label>
                    <select class="form-select form-select-sm rounded-3" name="estatus" onchange="this.form.submit()">
                        <option value="Todos" {{ request('estatus') == 'Todos' ? 'selected' : '' }}>Todos</option>
                        <option value="Activos" {{ request('estatus') == 'Activos' ? 'selected' : '' }}>Activos</option>
                        <option value="Inactivos" {{ request('estatus') == 'Inactivos' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">Tipo de Usuario</label>
                    <select class="form-select form-select-sm rounded-3" name="rol" onchange="this.form.submit()">
                        <option value="Todos" {{ request('rol') == 'Todos' ? 'selected' : '' }}>Todos</option>
                        <option value="2" {{ request('rol') == 2 ? 'selected' : '' }}>Administradores</option>
                        <option value="3" {{ request('rol') == 3 ? 'selected' : '' }}>Clientes</option>
                        <option value="4" {{ request('rol') == 4 ? 'selected' : '' }}>Vendedores</option>
                    </select>
                </div>

                <div class="col-md-3 ms-auto">
                    <label for="buscarUsuario" class="form-label fw-semibold">Buscar</label>
                    <div class="input-group input-group-sm rounded-3">
                        <input type="text" id="buscarUsuario" name="buscar" class="form-control rounded-start" placeholder="Buscar producto...">
                        <span class="input-group-text bg-white rounded-end"><i class="bi bi-search"></i></span>
                    </div>
                </div>
            </div>
        </form>

        <!-- TABLA DINÁMICA -->
        <div id="tablaUsuariosContainer">
            @include('partials.tabla_usuario', ['usuarios' => $usuarios])
        </div>
    </div>
</div>
<!-- ****************************************************************************************************************************************************** -->
<!-- MODAL NUEVO CLIENTE -->
<div class="modal fade" id="modalNuevoUsuario" tabindex="-1" aria-labelledby="modalNuevoUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-3 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalNuevoUsuarioLabel">Crear Nuevo Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formNuevoUsuario" method="POST" action="{{ route('admin.usuarios.store') }}">
                @csrf
                <div class="modal-body">
                    <!-- Selección Cliente -->
                    <div class="mb-3">
                        <label for="cliente_usuario" class="form-label fw-semibold">Cliente</label>
                        <select id="cliente_usuario" name="cliente" class="form-select" style="width:100%">
                            <option value="">Seleccione un cliente...</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->CardCode }}">{{ $cliente->CardCode }} - {{ $cliente->CardName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Credenciales -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="email_usuario" class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" id="email_usuario" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label for="password_usuario" class="form-label fw-semibold">Contraseña</label>
                            <input type="password" name="password" id="password_usuario" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label for="password_confirmation_usuario" class="form-label fw-semibold">Confirmar</label>
                            <input type="password" name="password_confirmation" id="password_confirmation_usuario" class="form-control" required>
                        </div>
                    </div>

                    <hr>

                    <!-- Datos bloqueados -->
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Código Cliente</label>
                            <input type="text" id="codigo_cliente" name="codigo_cliente" class="form-control" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Nombre(s)</label>
                            <input type="text" id="nombres" name="nombres" class="form-control" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Apellido Paterno</label>
                            <input type="text" id="apellido_paterno" name="apellido_paterno" class="form-control" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Apellido Materno</label>
                            <input type="text" id="apellido_materno" name="apellido_materno" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono</label>
                            <input type="text" id="telefono" name="telefono" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email de contacto</label>
                            <textarea id="email_contacto" name="email_contacto" class="form-control" rows="3" readonly></textarea>
                        </div>
                    </div>

                    <hr>

                    <!-- Datos Fiscales -->
                    <h6 class="fw-bold">Datos Fiscales</h6>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Dirección Fiscal</label>
                        <input type="text" id="direccion_fiscal" name="direccion_fiscal" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Dirección de Envío</label>
                        <input type="text" id="direccion_envio" name="direccion_envio" class="form-control" readonly>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- ****************************************************************************************************************************************************** -->
<!-- MODAL NUEVO VENDEDOR -->
<div class="modal fade" id="modalNuevoVendedor" tabindex="-1" aria-labelledby="modalNuevoVendedorLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-3 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalNuevoVendedorLabel">Crear Nuevo Vendedor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formNuevoVendedor" method="POST" action="{{ route('admin.usuarios.store') }}">
                @csrf
                <div class="modal-body">
                    <!-- Selección Vendedor -->
                    <div class="mb-3">
                        <label for="slpcode" class="form-label fw-semibold">Seleccionar Vendedor</label>
                        <select id="slpcode" name="slpcode" class="form-select" style="width:100%">
                            <option value="">Seleccione un vendedor...</option>
                            @foreach($vendedores as $vendedor)
                                @if($vendedor->Active == 'Y')
                                    <option value="{{ $vendedor->SlpCode }}">{{ $vendedor->SlpCode }} - {{ $vendedor->SlpName }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <!-- Datos bloqueados del vendedor -->
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Código Vendedor</label>
                            <input type="text" id="codigo_vendedor" name="codigo_vendedor" class="form-control" readonly>
                        </div>
                        <div class="col-md-9">
                            <label class="form-label fw-semibold">Nombre Vendedor</label>
                            <input type="text" id="nombre_vendedor" name="nombre_vendedor" class="form-control" readonly>
                        </div>
                    </div>

                    <hr>

                    <!-- Credenciales -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="email_vendedor" class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" id="email_vendedor" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label for="password_vendedor" class="form-label fw-semibold">Contraseña</label>
                            <input type="password" name="password" id="password_vendedor" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label for="password_confirmation_vendedor" class="form-label fw-semibold">Confirmar</label>
                            <input type="password" name="password_confirmation" id="password_confirmation_vendedor" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    
$(document).ready(function() {

    window.usuariosEstadoUrl = "{{ route('estado.Usuario') }}";

    // ---------- Inicializar Select2 dinámicamente ----------
    function initSelect2() {
        // Cliente
        $('#cliente_usuario').select2({
            dropdownParent: $('#modalNuevoUsuario'),
            placeholder: "Seleccione",
            allowClear: true,
            width: '100%'
        });

        // Vendedor
        $('#slpcode').select2({
            dropdownParent: $('#modalNuevoVendedor'),
            placeholder: "Seleccione",
            allowClear: true,
            width: '100%'
        });
    }

    initSelect2(); // Inicializamos al cargar la página

    // Re-inicializar cuando se abre modal (por si se generan dinámicamente)
    $('#modalNuevoUsuario, #modalNuevoVendedor').on('shown.bs.modal', function() {
        initSelect2();
    });

    // ---------- AJAX para cargar datos de cliente ----------
    $('#cliente_usuario').on('change', function() {
        let cardCode = $(this).val();
        if(cardCode){
            $.getJSON("/admin/ocrd/" + cardCode, function(data){
                $('#codigo_cliente').val(data.CardCode);
                $('#nombres').val(data.Nombres);
                $('#apellido_paterno').val(data.ApellidoPaterno);
                $('#apellido_materno').val(data.ApellidoMaterno);
                $('#telefono').val(data.Telefono);
                $('#email_contacto').val(Array.isArray(data.EmailContacto) ? data.EmailContacto.join("\n") : data.EmailContacto);
                $('#direccion_fiscal').val(data.DireccionFiscal);
                $('#direccion_envio').val(data.DireccionEnvio);
            });
        } else {
            $('#codigo_cliente, #nombres, #apellido_paterno, #apellido_materno, #telefono, #email_contacto, #direccion_fiscal, #direccion_envio').val('');
        }
    });

    // ---------- AJAX para cargar datos de vendedor ----------
    $('#slpcode').on('change', function() {
        let slpCode = $(this).val();
        if(slpCode){
            $.getJSON("/admin/oslp/" + slpCode, function(data){
                $('#codigo_vendedor').val(data.SlpCode);
                $('#nombre_vendedor').val(data.SlpName);
            });
        } else {
            $('#codigo_vendedor, #nombre_vendedor').val('');
        }
    });

    // ---------- Inicializar DataTable Para Filtros----------
    $(document).ready(function() {
        function fetchUsuarios(url = "{{ route('usuarios') }}") {
            const buscar = $('#buscarUsuario').val(); 
            const estatus = $('select[name="estatus"]').val();
            const mostrar = $('select[name="mostrar"]').val();
            const rol = $('select[name="rol"]').val();


            $.ajax({
                url: url,
                method: "GET",
                data: { buscar, estatus, mostrar, rol },
                success: function(data) {
                    $('#tablaUsuariosContainer').html(data);
                },
                error: function() {
                    alert('Error al cargar los usuarios.');
                }
            });
        }

        // Buscar al escribir
        $('#buscarUsuario').on('keyup', function() {
            fetchUsuarios();
        });

        // Cambios en selects
        $('select[name="estatus"], select[name="mostrar"], select[name="rol"]').on('change', function() {
            fetchUsuarios();
        });

        // Paginación AJAX para la tabla de usuarios
        $(document).on('click', '#tablaUsuariosContainer .pagination a', function(e) {
            e.preventDefault();
            const url = $(this).attr('href');
            fetchUsuarios(url);

            $('html, body').animate({ scrollTop: 0 }, 200);
        });

    });

    // ---------- Toggle estado con confirmación ----------
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    });

    // === CAMBIO DE ESTADO DE USUARIOS ===
    $(document).on('change', '.toggle-estado-usuarios', function() {
        var $checkbox = $(this);
        var id = $checkbox.data('id');
        var url = $checkbox.data('url') || window.usuariosEstadoUrl; // usa data-url si existe
        if (!url) {
            console.error('No se encontró la URL para actualizar el estado (data-url).');
            return;
        }

        // nuevo estado después del cambio (1 o 0)
        var newState = $checkbox.is(':checked') ? 1 : 0;
        // previo estado (simple y seguro): si newState = 1, prev = 0; si newState = 0, prev = 1
        var prevState = newState ? 0 : 1;

        var $row = $checkbox.closest('tr');
        var $statusCell = $row.find('td').eq(3);

        function getEstadoBadge(activo) {
            return activo
                ? `<span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-check-circle me-1"></i> Activo</span>`
                : `<span class="badge bg-danger rounded-pill px-3 py-2"><i class="bi bi-x-circle me-1"></i> Inactivo</span>`;
        }

        // Mostrar confirmación
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Vas a cambiar el estado del usuario a " + (newState ? "Activo" : "Inactivo"),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#05564f',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (!result.isConfirmed) {
                // Usuario canceló → volver al estado previo
                $checkbox.prop('checked', prevState === 1);
                return;
            }

            // Deshabilitar mientras se procesa
            $checkbox.prop('disabled', true);

            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    id: id,
                    field: 'activo',
                    value: newState
                    // no es necesario _token si ya configuramos X-CSRF-TOKEN en headers
                },
                success: function(response) {
                    // esperar que el controlador responda { success: true } o similar
                    if (response && response.success) {
                        $checkbox.prop('checked', newState === 1);
                        $statusCell.html(getEstadoBadge(newState));
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Estado actualizado correctamente',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    } else {
                        // si la API indica fallo
                        Swal.fire('Error', response.message || 'No se pudo actualizar el estado en el servidor.', 'error');
                        $checkbox.prop('checked', prevState === 1);
                        $statusCell.html(getEstadoBadge(prevState));
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire('Error', 'Ocurrió un error de conexión.', 'error');
                    $checkbox.prop('checked', prevState === 1);
                    $statusCell.html(getEstadoBadge(prevState));
                    console.error('AJAX error:', status, error, xhr.responseText);
                },
                complete: function() {
                    $checkbox.prop('disabled', false);
                }
            });
        });
    });


});




</script>

<style>
/* Toggle switch estilo iOS */
.switch { position: relative; display: inline-block; width: 50px; height: 26px; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; top:0; left:0; right:0; bottom:0; background-color:#ccc; transition:.4s; border-radius:26px; }
.slider:before { position:absolute; content:""; height:20px; width:20px; left:3px; bottom:3px; background-color:white; transition:.4s; border-radius:50%; }
input:checked + .slider { background-color:#28a745; }
input:focus + .slider { box-shadow:0 0 1px #28a745; }
input:checked + .slider:before { transform:translateX(24px); }
.slider.round { border-radius:26px; }
</style>
@endpush