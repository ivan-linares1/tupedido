@extends('layouts.app')

@section('title', 'Dashboard')

@section('contenido')

 {{-- Contenedor para mensajes flash dinámicos --}}
    <div id="flash-messages" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        {{-- Aquí se insertarán los alerts --}}
    </div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white rounded-top">
        <h5 class="mb-0">Usuarios</h5>
        <!-- Botón abre modal -->
        <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
            Nuevo Cliente
        </button>
        <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoVendedor">
            Nuevo Vendedor
        </button>
    </div>

    <div class="card-body">
        <div class="row mb-4 g-3">
            <div class="col-md-2">
                <label for="mostrar" class="form-label fw-semibold">Mostrar</label>
                <select id="mostrar" class="form-select form-select-sm">
                    <option>10</option>
                    <option selected>25</option>
                    <option>50</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="estatus" class="form-label fw-semibold">Estatus</label>
                <select id="estatus" class="form-select form-select-sm">
                    <option selected>Todos</option>
                    <option>Activo</option>
                    <option>Inactivo</option>
                </select>
            </div>
            <div class="col-md-4 ms-auto">
                <label for="buscar" class="form-label fw-semibold">Buscar</label>
                <div class="input-group input-group-sm">
                    <input type="text" id="buscar" class="form-control" placeholder="Buscar...">
                    <button class="btn btn-outline-primary">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <!-- Agregué solo id="tablaUsuarios" para que los scripts funcionen -->
            <table id="tablaUsuarios" class="table table-hover table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">Usuario</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Rol</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-center">Acción</th> <!-- <-- nueva columna -->
                    </tr>
                </thead>

                <tbody>
                    @foreach ($usuarios as $usuario)
                        <tr>
                            <td>{{ $usuario->email }}</td>
                            <td>{{ $usuario->nombre }}</td>
                            <td>{{ $usuario->rol?->nombre }}</td>
                            <td>{{ $usuario->activo ? 'activo' : 'inactivo' }}</td>
                            <td class="text-center">
                                <label class="switch">
                                    <input 
                                        type="checkbox" 
                                        class="toggle-estado-usuarios"
                                        data-id="{{ $usuario->id }}"
                                        data-field="activo"
                                        data-url="{{ route('estado.Usuario') }}"
                                        {{ $usuario->activo == 1 ? 'checked' : '' }}
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

<!-- Modal Nuevo Usuario (CLIENTE) -->
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
                                <option value="{{ $cliente->CardCode }}">
                                    {{ $cliente->CardCode }} - {{ $cliente->CardName }}
                                </option>
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

<!-- Modal Nuevo Vendedor -->
<div class="modal fade" id="modalNuevoVendedor" tabindex="-1" aria-labelledby="modalNuevoVendedorLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-3 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalNuevoVendedorLabel">Crear Nuevo Vendedor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formNuevoVendedor" method="POST" action="{{ route('admin.usuarios.store') }}">
                @csrf
                <input type="hidden" name="rol" value="Vendedor">
                <div class="modal-body">
                    
                    <!-- Selección Vendedor -->
                    <div class="mb-3">
                        <label for="slpcode" class="form-label fw-semibold">Seleccionar Vendedor</label>
                        <select id="slpcode" name="slpcode" class="form-select" style="width:100%">
                            <option value="">Seleccione un vendedor...</option>
                            @foreach($vendedores as $vendedor)
                                @if($vendedor->Active == 'Y')
                                    <option value="{{ $vendedor->SlpCode }}">
                                        {{ $vendedor->SlpCode }} - {{ $vendedor->SlpName }}
                                    </option>
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


<script>
    // fallback global por si el data-url no está disponible en el DOM re-renderizado
    window.usuariosEstadoUrl = "{{ route('estado.Usuario') }}";
</script>



<!-- Select2 y lógica de filtros -->
<script>



$(document).ready(function() {

    // Inicializamos DataTable y guardamos la instancia en "table"
    var table = null;
    if ($('#tablaUsuarios').length) {
        table = $('#tablaUsuarios').DataTable({
            pageLength: 25,
            lengthChange: false,
            dom: 'rtip',
            language: {
    url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
}

        });

        // Mostrar N registros
        $('#mostrar').on('change', function() {
            const val = parseInt($(this).val()) || 25;
            table.page.len(val).draw();
        });

        // Filtro estatus (case-insensitive)
       $('#estatus').on('change', function() {
            const raw = $(this).val();
            let filtro = '';

            if (!raw || raw.toLowerCase() === 'todos') {
                filtro = '';
            } else if (raw.toLowerCase() === 'activo') {
                filtro = '^activo$'; // regex exacto
            } else if (raw.toLowerCase() === 'inactivo') {
                filtro = '^inactivo$'; // regex exacto
            }

            table.column(3).search(filtro, true, false, true).draw();
        });


        // Buscar dinámico
        $('#buscar').on('keyup', function() {
            table.search(this.value).draw();
        });
    }

    // ============================
    // Select2 y AJAX para modales (sin cambios)
    // ============================
    $('#cliente_usuario').select2({
        dropdownParent: $('#modalNuevoUsuario'),
        placeholder: "Seleccione un cliente",
        allowClear: true
    });

    $('#cliente_usuario').on('change', function(){
        let cardCode = $(this).val();
        if(cardCode){
            $.ajax({
                url: "/admin/ocrd/" + cardCode,
                type: "GET",
                success: function(data){
                    $('#codigo_cliente').val(data.CardCode);
                    $('#nombres').val(data.Nombres);
                    $('#apellido_paterno').val(data.ApellidoPaterno);
                    $('#apellido_materno').val(data.ApellidoMaterno);
                    $('#telefono').val(data.Telefono);

                    if (Array.isArray(data.EmailContacto)) {
                        $('#email_contacto').val(data.EmailContacto.join("\n"));
                    } else {
                        $('#email_contacto').val(data.EmailContacto);
                    }
                    $('#direccion_fiscal').val(data.DireccionFiscal);
                    $('#direccion_envio').val(data.DireccionEnvio);
                }
            });
        } else {
            $('#codigo_cliente, #nombres, #apellido_paterno, #apellido_materno, #telefono, #email_contacto, #direccion_fiscal, #direccion_envio').val('');
        }
    });

    $('#slpcode').select2({
        dropdownParent: $('#modalNuevoVendedor'),
        placeholder: "Seleccione un vendedor",
        allowClear: true
    });

    $('#slpcode').on('change', function(){
        let slpCode = $(this).val();
        if(slpCode){
            $.ajax({
                url: "/admin/oslp/" + slpCode,
                type: "GET",
                success: function(data){
                    $('#codigo_vendedor').val(data.SlpCode);
                    $('#nombre_vendedor').val(data.SlpName);
                }
            });
        } else {
            $('#codigo_vendedor, #nombre_vendedor').val('');
        }
    });


    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
    }
});


     // Evento para toggle con confirmación y notificación
    $(document).on('change', '.toggle-estado-usuarios', function () {
        var $checkbox = $(this);
        var id = $checkbox.data('id');
        var url = $checkbox.data('url') || window.usuariosEstadoUrl;
        var newState = $checkbox.is(':checked') ? 1 : 0;
        var prevState = newState ? 0 : 1;
        var $row = $checkbox.closest('tr');
        var $statusCell = $row.find('td').eq(3);

        // Detenemos el cambio hasta confirmar
        $checkbox.prop('checked', prevState === 1);

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
            if (result.isConfirmed) {
                $checkbox.prop('disabled', true);

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        id: id,
                        field: 'activo',
                        value: newState
                    },
                    success: function(response) {
                        if (response.success) {
                            $checkbox.prop('checked', newState === 1);
                            $statusCell.text(newState ? 'activo' : 'inactivo');

                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Estado actualizado correctamente',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        } else {
                            Swal.fire('Error', 'No se pudo actualizar el estado en el servidor.', 'error');
                            $checkbox.prop('checked', prevState === 1);
                            $statusCell.text(prevState === 1 ? 'activo' : 'inactivo');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Ocurrió un error de conexión.', 'error');
                        $checkbox.prop('checked', prevState === 1);
                        $statusCell.text(prevState === 1 ? 'activo' : 'inactivo');
                    },
                    complete: function() {
                        $checkbox.prop('disabled', false);
                    }
                });
            } else {
                // Si cancela, dejamos el estado anterior
                $checkbox.prop('checked', prevState === 1);
            }
        });
    });





});


</script>




<style>
/* Toggle switch estilo iOS */
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
  background-color: #28a745; /* verde éxito */
}

input:focus + .slider {
  box-shadow: 0 0 1px #28a745;
}

input:checked + .slider:before {
  transform: translateX(24px);
}

/* redondeado */
.slider.round {
  border-radius: 26px;
}
</style>
@endpush

