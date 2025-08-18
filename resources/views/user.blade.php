@extends('layouts.app')

@section('title', 'Dashboard')

@section('contenido')

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white rounded-top">
        <h5 class="mb-0">Usuarios</h5>
        <button class="btn btn-light btn-sm">Nuevo usuario</button>
    </div>

    <div class="card-body">
        <div class="row mb-4 g-3">
            <div class="col-md-2">
                <label for="mostrar" class="form-label fw-semibold">Mostrar</label>
                <select id="mostrar" class="form-select form-select-sm">
                    <option>10</option>
                    <option selected>25</option>
                    <option>50</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="estatus" class="form-label fw-semibold">Estatus</label>
                <select id="estatus" class="form-select form-select-sm">
                    <option selected>Todos</option>
                    <option>Activo</option>
                    <option>Inactivo</option>
                </select>
            </div>
            <div class="col-md-4 ms-auto">
                <label for="buscar" class="form-label fw-semibold">Buscar</label>
                <div class="input-group input-group-sm">
                    <input type="text" id="buscar" class="form-control" placeholder="Buscar...">
                    <button class="btn btn-outline-primary">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">Usuario</th>
                        <th scope="col">Nombre</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($usuarios as $usuario)
                        <tr>
                            <td>{{ $usuario->email }}</td>
                            <td>{{ $usuario->nombre }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center">No hay usuarios registrados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
