<table id="tablaVendedores" class="table table-hover table-striped align-middle">
    <thead class="table-dark">
        <tr>
            <th>SlpCode</th>
            <th>Nombre</th>
            <th>Estatus</th>
            <th class="text-center">Acción</th>
        </tr>
    </thead>
    <tbody>
        @forelse($vendedores as $item)
            <tr data-id="{{ $item->SlpCode }}">
                <td>{{ $item->SlpCode }}</td>
                <td>{{ $item->SlpName }}</td>
                <td class="status-text">{{ $item->Active == 'Y' ? 'Activo' : 'Inactivo' }}</td>
                <td class="text-center">
                    <label class="switch">
                        <input 
                            type="checkbox" 
                            class="toggle-estado-vendedor"
                            data-id="{{ $item->SlpCode }}"
                            data-url="{{ route('admin.vendedores.toggleActivo') }}"
                            {{ $item->Active == 'Y' ? 'checked' : '' }}
                        >
                        <span class="slider round"></span>
                    </label>
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
