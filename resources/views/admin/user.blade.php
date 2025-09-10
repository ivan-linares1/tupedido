

@extends('layouts.app')

@section('title', 'Dashboard')

@section('contenido')

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white rounded-top">
        <h5 class="mb-0">Usuarios</h5>
        <!-- Botón abre modal -->
        <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
            Nuevo usuario
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
            <table class="table table-hover table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">Usuario</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Rol</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($usuarios as $usuario)
                        <tr>
                            <td>{{ $usuario->email }}</td>
                            <td>{{ $usuario->nombre }}</td>
                            <td>{{ $usuario->rol?->nombre }}</td>
                            <td>{{ $usuario->activo ? 'activo' : 'inactivo' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Nuevo Usuario -->
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
                        <label for="cliente" class="form-label fw-semibold">Cliente</label>
                        <select id="cliente" name="cliente" class="form-select" style="width:100%">
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
                            <label for="email" class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label for="password" class="form-label fw-semibold">Contraseña</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label for="password_confirmation" class="form-label fw-semibold">Confirmar</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                        </div>
                    </div>

                    <hr>

                    <!-- Datos bloqueados -->
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Código Cliente</label>
                            <input type="text" id="codigo_cliente" class="form-control" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Nombre(s)</label>
                            <input type="text" id="nombres" class="form-control" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Apellido Paterno</label>
                            <input type="text" id="apellido_paterno" class="form-control" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Apellido Materno</label>
                            <input type="text" id="apellido_materno" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono</label>
                            <input type="text" id="telefono" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono Celular</label>
                            <input type="text" id="telefono_celular" class="form-control" readonly>
                        </div>
                    </div>

                    <hr>

                    <!-- Datos Fiscales -->
                    <h6 class="fw-bold">Datos Fiscales</h6>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Dirección Fiscal</label>
                        <input type="text" id="direccion_fiscal" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Dirección de Envío</label>
                        <input type="text" id="direccion_envio" class="form-control" readonly>
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
<!-- Select2 para búsqueda en el select -->
<script>
$(document).ready(function() {
    $('#cliente').select2({
        dropdownParent: $('#modalNuevoUsuario'),
        placeholder: "Seleccione un cliente",
        allowClear: true
    });

   $('#cliente').on('change', function(){
    let cardCode = $(this).val();
    if(cardCode){
        $.ajax({
            url: "{{ route('admin.ocrd.show') }}",
            type: "GET",
            data: { cardCode: cardCode },
            success: function(data){
                $('#codigo_cliente').val(data.CardCode);
                $('#nombres').val(data.Nombres);
                $('#apellido_paterno').val(data.ApellidoPaterno);
                $('#apellido_materno').val(data.ApellidoMaterno);
                $('#telefono').val(data.Telefono);
                $('#telefono_celular').val(data.TelefonoCelular);
                $('#direccion_fiscal').val(data.DireccionFiscal);
                $('#direccion_envio').val(data.DireccionEnvio);
            }
        });
    }
});
});
</script>
@endpush