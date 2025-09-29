@extends('layouts.app')

@section('title', 'Productos')

@section('contenido')

<div id="flash-messages"></div>

<div class="container mt-4">
    <h3 class="mb-3 fw-bold">Catálogo de Productos / Servicios</h3>

    <!-- Filtros -->
    <form method="GET" action="{{ route('articulos') }}">
        <div class="row mb-3 align-items-center">
            <div class="col-md-2">
                <label class="form-label">Mostrar</label>
                <select class="form-select form-select-sm" name="mostrar" onchange="this.form.submit()">
                    <option {{ request('mostrar') == 25 ? 'selected' : '' }}>25</option>
                    <option {{ request('mostrar') == 50 ? 'selected' : '' }}>50</option>
                    <option {{ request('mostrar') == 100 ? 'selected' : '' }}>100</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Estatus</label>
                <select class="form-select form-select-sm" name="estatus" onchange="this.form.submit()">
                    <option value="Activos" {{ request('estatus') == 'Activos' ? 'selected' : '' }}>Activos</option>
                    <option value="Inactivos" {{ request('estatus') == 'Inactivos' ? 'selected' : '' }}>Inactivos</option>
                    <option value="Todos" {{ request('estatus') == 'Todos' ? 'selected' : '' }}>todos</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Grupo de Productos</label>
                <select class="form-select form-select-sm" name="grupo" onchange="this.form.submit()">
                        <option value="" >Selecciona una marca...</option>
                        @foreach ($marcas as $marca)
                            <option value="{{ $marca->ItmsGrpCod }}" {{ request('grupo') == $marca->ItmsGrpCod ? 'selected' : '' }}>{{ $marca->ItmsGrpNam }} </option>
                        @endforeach
                </select>
            </div>
            <div class="col-md-3 offset-md-2">
                <label class="form-label">Buscar</label>
                <div class="input-group input-group-sm">
                    <input type="text" name="buscar" class="form-control" value="{{ request('buscar') }}" placeholder="Buscar...">
                    <button class="btn btn-primary"><i class="bi bi-search"></i></button>
                </div>
            </div>
        </div>
    </form>

    <!-- Tabla -->
    <div class="table-responsive ">
        <table class="table table-bordered align-middle">
            <thead class="table-info  text-center">
                <tr>
                    <th>Clave</th>
                    <th>Producto / Servicio</th>
                    <th>Descripción</th>
                    <th>Grupo de Productos</th>
                    <th>Imagen</th>
                    @if (Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2)<th>Activo</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse($articulos as $articulo)
                <tr>
                    <td>{{ $articulo->ItemCode }}</td>
                    <td class="text-primary">
                        <a href="#" class="text-decoration-none fw-semibold">
                            {{ $articulo->ItemName }}
                        </a>
                    </td>
                    <td>{{ $articulo->FrgnName }}</td>
                    <td>{{ $articulo->marca->ItmsGrpNam}}</td>
                    <td><img src="{{ asset($articulo->imagen->Ruta_imagen) }}" alt="Imagen" style="width:70px;height:auto;"></td>
                    @if (Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2)<td class="text-center">
                        <div class="form-check form-switch d-flex justify-content-center">
                            <input 
                                class="form-check-input toggle-estado" 
                                type="checkbox" 
                                role="switch"
                                id="estado-{{ $articulo->ItemCode }}"
                                data-id="{{ $articulo->ItemCode }}"
                                data-field="Active"
                                data-url="{{ route('estado.Articulo') }}"
                                {{  $articulo->Active == 'Y' ? 'checked' : ''  }}>
                        </div>
                    </td>@endif
                </tr>
                @empty
                <tr>
                    @if (Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2)
                    <td colspan="6" class="text-center text-muted">No se encontraron resultados</td>
                    @else
                    <td colspan="5" class="text-center text-muted">No se encontraron resultados</td>
                    @endif
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación Bootstrap -->
    <div class="d-flex justify-content-center">
        {{ $articulos->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>

</div>

@endsection
