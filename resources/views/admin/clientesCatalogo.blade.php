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
                    <option value="">Todos</option>
                    <option value="Y" {{ request('estatus') == 'Y' ? 'selected' : '' }}>Activos</option>
                    <option value="N" {{ request('estatus') == 'N' ? 'selected' : '' }}>Inactivos</option>
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
        <div class="table-responsive">
            <table id="tablaClientes" class="table table-hover table-striped align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Codigo</th>
                        <th>Nombre</th>
                        <th>Telefono</th>
                        <th>E-mail</th>
                        <th>Activo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientes as $cliente)
                    <tr data-status="{{ $cliente->Active }}">
                        <td>{{ $cliente->CardCode }}</td>
                        <td>{{ $cliente->CardName }}</td>
                        <td>{{ $cliente->phone1}}</td>
                        <td>{{ $cliente->{'e-mail'} }}</td>
                        <td class="text-center">
                            <label class="switch">
                                <input 
                                    type="checkbox" 
                                    class="toggle-estado" 
                                    data-id="{{ $cliente->CardCode }}"
                                    data-field="Active"
                                    data-url="{{ route('estado.Cliente') }}"
                                    {{ $cliente->Active == 'Y' ? 'checked' : '' }}>
                                <span class="slider round"></span>
                            </label>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No se encontraron resultados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="d-flex justify-content-center mt-3">
            {{ $clientes->appends(request()->query())->links('pagination::bootstrap-5') }}
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
@endpush
