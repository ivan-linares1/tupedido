<table id="tablaArticulos" class="table table-hover table-striped align-middle">
    <thead class="table-dark text-center">
        <tr>
            <th style="width: 100px !important">Clave</th>
            <th style="width: 200px !important">Producto / Servicio</th>
            <th style="width: 350px !important">Descripción</th>
            <th style="width: 200px !important">Grupo de Productos</th>
            <th style="text-align:center; width: 150px !important;">Imagen</th>
            @if (Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2)
                <th style="width: 220px !important; text-align:center;">Precio Original</th>
                <th style="width: 80px !important">Activo</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @forelse($articulos as $articulo)
        <tr data-status="{{ $articulo->Active }}">
            <td style="width: 100px !important">{{ $articulo->ItemCode }}</td>
            <td class="text-primary fw-semibold" style="width: 200px !important">{{ $articulo->ItemName }}</td>
            <td style="width: 350px !important">{{ $articulo->FrgnName }}</td>
            <td style="width: 200px !important"> {{ $articulo->marca->ItmsGrpNam }}</td>
            <td style="text-align:center; width:150px"><img src="{{ asset($articulo->imagen->Ruta_imagen) }}" alt="Imagen" style="width:70px;height:auto;"></td>
            @if (Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2)
            <td style="width: 220px !important;">
                @if($articulo->precio)
                    {{ number_format($articulo->precio->Price, 2) }} {{ $articulo->precio->moneda->Currency }}
                @else
                    <span style="color:red; font-weight:bold;">SIN PRECIO</span>
                @endif
            </td>
             <td class="text-center">
                @if ($articulo->Active === 'Y')
                    <span class="badge bg-success rounded-pill px-3 py-2">
                        <i class="bi bi-check-circle me-1"></i> Activo
                    </span>
                @else
                    <span class="badge bg-danger rounded-pill px-3 py-2">
                        <i class="bi bi-x-circle me-1"></i> Inactivo
                    </span>
                @endif
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
