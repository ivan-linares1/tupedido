<table id="tablaClientes" class="table table-hover table-striped align-middle">
    <thead class="table-dark text-center">
        <tr>
            <th>Codigo</th>
            <th>Nombre</th>
            <th>Telefono</th>
            <th>E-mail</th>
            <th>Estado</th>
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
                @if ($cliente->Active === 'Y')
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
            <td colspan="5" class="text-center text-muted">No se encontraron resultados</td>
        </tr>
        @endforelse
    </tbody>
</table>

<!-- Paginación -->
<div class="d-flex justify-content-center mt-3">
    {{ $clientes->appends(request()->query())->links('pagination::bootstrap-5') }}
</div>