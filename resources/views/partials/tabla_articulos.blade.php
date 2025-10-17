<table id="tablaArticulos" class="table table-hover table-striped align-middle">
    <thead class="table-dark text-center">
        <tr>
            <th style="width: 100px !important">Clave</th>
            <th style="width: 200px !important">Producto / Servicio</th>
            <th style="width: 450px !important">Descripción</th>
            <th style="width: 200px !important">Grupo de Productos</th>
            <th style="text-align:center; width: 150px !important;">Imagen</th>
            @if (Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2)
                <th style="width: 80px !important">Activo</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @forelse($articulos as $articulo)
        <tr data-status="{{ $articulo->Active }}">
            <td style="width: 100px !important">{{ $articulo->ItemCode }}</td>
            <td class="text-primary fw-semibold" style="width: 200px !important">{{ $articulo->ItemName }}</td>
            <td style="width: 450px !important">{{ $articulo->FrgnName }}</td>
            <td style="width: 200px !important"> {{ $articulo->marca->ItmsGrpNam }}</td>
            <td style="text-align:center; width:150px"><img src="{{ asset($articulo->imagen->Ruta_imagen) }}" alt="Imagen" style="width:70px;height:auto;"></td>
            @if (Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2)
            <td class="col-active" style="width: 80px !important">
                <label class="switch">
                    <input type="checkbox" class="toggle-estado" data-id="{{ $articulo->ItemCode }}" data-field="Active" data-url="{{ route('estado.Articulo') }}" {{ $articulo->Active == 'Y' ? 'checked' : '' }}>
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

<!-- Paginación -->
<div class="d-flex justify-content-center mt-3">
    {{ $articulos->appends(request()->query())->links('pagination::bootstrap-5') }}
</div>
