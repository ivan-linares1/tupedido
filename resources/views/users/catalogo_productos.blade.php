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
                        <input type="text" id="buscarArticulo" class="form-control rounded-start" placeholder="Buscar producto...">
                        <span class="input-group-text bg-white rounded-end"><i class="bi bi-search"></i></span>
                    </div>
                </div>
            </div>
        </form>

        <!-- Tabla -->
        <div class="table-responsive">
            <table id="tablaArticulos" class="table table-hover table-striped align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Clave</th>
                        <th>Producto / Servicio</th>
                        <th>Descripción</th>
                        <th>Grupo de Productos</th>
                        <th>Imagen</th>
                        @if (Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2)
                            <th>Activo</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($articulos as $articulo)
                    <tr data-status="{{ $articulo->Active }}">
                        <td>{{ $articulo->ItemCode }}</td>
                        <td class="text-primary fw-semibold">{{ $articulo->ItemName }}</td>
                        <td>{{ $articulo->FrgnName }}</td>
                        <td>{{ $articulo->marca->ItmsGrpNam }}</td>
                        <td><img src="{{ asset($articulo->imagen->Ruta_imagen) }}" alt="Imagen" style="width:70px;height:auto;"></td>
                        @if (Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2)
                        <td class="text-center">
                            <label class="switch">
                                <input 
                                    type="checkbox" 
                                    class="toggle-estado" 
                                    data-id="{{ $articulo->ItemCode }}"
                                    data-field="Active"
                                    data-url="{{ route('estado.Articulo') }}"
                                    {{ $articulo->Active == 'Y' ? 'checked' : '' }}>
                                <span class="slider round"></span>
                            </label>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No se encontraron resultados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="d-flex justify-content-center mt-3">
            {{ $articulos->appends(request()->query())->links('pagination::bootstrap-5') }}
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
        $('#buscarArticulo').on('keyup', function() {
            const valor = $(this).val().toLowerCase();
            $('#tablaArticulos tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(valor) > -1);
            });
        });
    });
</script>
@endpush
