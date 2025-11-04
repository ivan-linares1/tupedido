<table id="tablaVendedores" class="table table-hover table-striped align-middle">
    <thead class="table-dark">
        <tr>
            <th>SlpCode</th>
            <th>Nombre</th>
            <th>Estatus</th>
        </tr>
    </thead>
    <tbody>
        @forelse($vendedores as $item)
            <tr data-id="{{ $item->SlpCode }}">
                <td>{{ $item->SlpCode }}</td>
                <td>{{ $item->SlpName }}</td>
                <td class="status-text">
                    @if ($item->Active === 'Y')
                        <span class="badge bg-success rounded-pill px-3 py-2">
                            <i class="bi bi-check-circle me-1"></i> Activo
                        </span>
                    @else
                        <span class="badge bg-danger rounded-pill px-3 py-2">
                            <i class="bi bi-x-circle me-1"></i> Inactivo
                        </span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center text-muted">No se encontraron vendedores.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- Paginación -->
<div class="d-flex justify-content-center mt-3">
    {{ $vendedores->appends(request()->query())->links('pagination::bootstrap-5') }}
</div>
