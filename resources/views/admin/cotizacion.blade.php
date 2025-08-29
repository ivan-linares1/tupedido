@extends('layouts.app')

@section('title', 'Cotizaciones')

@section('contenido')
<div class="container my-4">
    <h3>COTIZACIONES</h3>
    <div class="container my-4">
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="row mb-2">
                    <div class="col-md-4">
                        <label>Código Cliente</label>
                        <div class="input-group">
                            <input type="text" id="codigoCliente" class="form-control" placeholder="Código Cliente" readonly>
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalClientes">...</button>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <label>Nombre</label>
                        <input type="text" id="nombreCliente" class="form-control" placeholder="Nombre Cliente" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label>Moneda</label>
                        <select class="form-select">
                            <option>MXN</option>
                            <option>USD</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="row mb-2">
                    <div class="col-md-4">
                        <label>Fecha de contabilización</label>
                        <input type="date" id="fechaContabilizacion" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>Válido hasta</label>
                        <input type="date" id="fechaValidoHasta" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>Fecha del documento</label>
                        <input type="date" id="fechaDocumento" class="form-control">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#contenido">Contenido</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#logistica">Logistica</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#finanzas">Finanzas</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#">Anexos</a></li>
    </ul>


<div class="tab-content">
    <div class="tab-pane fade show active" id="contenido">
        @include('components.cot_contenido')
    </div>
    <div class="tab-pane fade" id="finanzas">
        @include('components.Finanzas')
    </div>
</div>



@push('scripts')
<script>
    const hoy = new Date().toISOString().split('T')[0];
    document.getElementById('fechaContabilizacion').value = hoy;
    document.getElementById('fechaValidoHasta').value = hoy;
    document.getElementById('fechaDocumento').value = hoy;

    const clientes = [
        {codigo: 'CLI-001', nombre: 'Empresa Ejemplo S.A. de C.V.', descuento: 10},
        {codigo: 'CLI-002', nombre: 'Comercializadora Demo', descuento: 0},
        {codigo: 'CLI-003', nombre: 'Cliente Premium', descuento: 15}
    ];

    // Funciones para manejar la selección de cliente dentro del modal
    function seleccionarCliente(codigo, nombre, descuento) {
        document.getElementById("codigoCliente").value = codigo;
        document.getElementById("nombreCliente").value = nombre;
        descuentoCliente = descuento;

        document.querySelectorAll("#tablaArticulos tbody tr").forEach(tr => {
            if (tr.querySelector(".descuento")) {
                tr.querySelector(".descuento").textContent = descuentoCliente;
            }
        });

        calcularTotales();
        bootstrap.Modal.getInstance(document.getElementById('modalClientes')).hide();
    }

    // Función para cargar los datos del los clientes en el modal
    function cargarClientes() {
        const tbody = document.querySelector('#tablaClientes tbody');
        tbody.innerHTML = '';
        clientes.forEach(c => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${c.codigo}</td>
                <td>${c.nombre}</td>
                <td>${c.descuento}%</td>
                <td><button class="btn btn-sm btn-success" onclick="seleccionarCliente('${c.codigo}','${c.nombre}',${c.descuento})">Seleccionar</button></td>
            `;
            tbody.appendChild(tr);
        });
    }

    document.getElementById('modalClientes').addEventListener('show.bs.modal', cargarClientes);
</script>
@endpush

@endsection