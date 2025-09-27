@extends('layouts.app') {{-- Ajusta según tu layout principal --}}

@section('contenido')
<div class="container-fluid mt-4">

    <h2 class="mb-4">Catálogo de Vendedores</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row mb-3">
                {{-- Filtro estatus --}}
                <div class="col-md-3">
                    <select id="estatusFiltro" class="form-select">
                        <option value="">Todos</option>
                        <option value="Activos">Activos</option>
                        <option value="Inactivos">Inactivos</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table id="tablaVendedores" class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>SlpCode</th>
                            <th>SlpName</th>
                            <th>Activo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vendedores as $item)
                            <tr>
                                <td>{{ $item->SlpCode }}</td>
                                <td>{{ $item->SlpName }}</td>
                                <td>
                                    @if($item->Active == 'Y')
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-danger">Inactivo</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginación de Laravel (por si quieres usarla además de DataTables) --}}
            <div class="mt-2">
                {{ $vendedores->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
    {{-- jQuery y DataTables --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            var tabla = $('#tablaVendedores').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/es-MX.json'
                },
                pageLength: 25, // valor por defecto
                lengthMenu: [ [25, 50, 100], [25, 50, 100] ], // selector de registros
                ordering: true,
                searching: true
            });

            // Filtro estatus dinámico
            $('#estatusFiltro').on('change', function() {
                var valor = $(this).val();
                if(valor === "") {
                    tabla.column(2).search('').draw(); // Todos
                } else if(valor === "Activos") {
                    tabla.column(2).search('Activo').draw();
                } else if(valor === "Inactivos") {
                    tabla.column(2).search('Inactivo').draw();
                }
            });
        });
    </script>
@endpush
