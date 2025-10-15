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

<!-- Paginación -->
<div class="d-flex justify-content-center mt-3">
    {{ $clientes->appends(request()->query())->links('pagination::bootstrap-5') }}
</div>