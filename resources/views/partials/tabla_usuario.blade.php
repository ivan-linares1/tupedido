<div class="table-responsive">
    <table id="tablaUsuarios" class="table table-hover table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th scope="col">Usuario</th>
                <th scope="col">Nombre</th>
                <th scope="col">Rol</th>
                <th scope="col">Status</th>
                <th scope="col">Máx. Sesiones</th>
                <th scope="col" class="text-center">Acción</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->email }}</td>
                    <td>{{ $usuario->nombre }}</td>
                    <td>{{ $usuario->rol?->nombre }}</td>
                    <td>
                        @if ($usuario->activo === true)
                            <span class="badge bg-success rounded-pill px-3 py-2">
                                <i class="bi bi-check-circle me-1"></i> Activo
                            </span>
                        @else
                            <span class="badge bg-danger rounded-pill px-3 py-2">
                                <i class="bi bi-x-circle me-1"></i> Inactivo
                            </span>
                        @endif
                    </td>
                     {{-- NUEVO CAMPO PARA CONFIGURAR MÁXIMO --}}
                    <td>
                        <input 
                            type="number" 
                            min="1" 
                            class="form-control max-sessions-input"
                            value="{{ $usuario->max_sessions ?? 1 }}"
                            data-id="{{ $usuario->id }}"
                            data-old="{{ $usuario->max_sessions ?? 1 }}"
                            style="width: 90px;"
                            @if ($usuario->rol_id == 1) disabled @endif>
                    </td>

                    <td class="text-center">
                        @if ($usuario->rol_id != 1)
                            <label class="switch">
                                <input 
                                    type="checkbox" 
                                    class="toggle-estado-usuarios"
                                    data-id="{{ $usuario->id }}"
                                    data-field="activo"
                                    data-url="{{ route('estado.Usuario') }}"
                                    {{ $usuario->activo == 1 ? 'checked' : '' }}>
                                <span class="slider round"></span>
                            </label>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No se encontraron usuarios</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-end mt-2">
        {!! $usuarios->links('pagination::bootstrap-5') !!}
    </div>
</div>


<script>
    $(document).on('change', '.max-sessions-input', function () {
    let input = $(this);
    let userId = input.data('id');
    let oldValue = input.data('old');
    let newValue = input.val();

    Swal.fire({
        title: "¿Actualizar valor?",
        text: "¿Deseas cambiar el número máximo de sesiones?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, cambiar",
        cancelButtonText: "Cancelar"
    }).then((result) => {

        if (result.isConfirmed) {
            // Enviar actualización
            $.ajax({
                url: '{{ route("usuario.update.maxSessions") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: userId,
                    max_sessions: newValue
                },
                success: function () {
                    Swal.fire("Actualizado", "El valor fue modificado correctamente.", "success");
                    input.data('old', newValue);
                },
                error: function () {
                    Swal.fire("Error", "No se pudo actualizar.", "error");
                    input.val(oldValue); 
                }
            });

        } else {
            // Cancelado → regresamos el valor anterior
            input.val(oldValue);
        }
    });
});

</script>