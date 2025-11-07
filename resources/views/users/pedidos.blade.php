@extends('layouts.app')

@section('title', 'Pedidos')
@section('contenido')

@vite(['resources/css/tablas.css'])

<div class="table-responsive mt-4">
    <h3 class="mb-3 fw-bold">PEDIDOS</h3>
    <x-loading />

    <div class="mb-3 d-flex justify-content-between align-items-center">
        {{-- Botón Nuevo Pedido --}}
        @if($configuracionVacia == true && (Auth::user()->rol_id == 3 || Auth::user()->rol_id == 4))
            <div class="d-inline-block position-relative">
                <button class="btn btn-primary" disabled>Nuevo Pedido</button>
                <small class="mensaje-cambio text-danger">⚠️ {!! 'Contacte a soporte: <br> Problema de configuracion.' !!}</small>
            </div>
        @elseif($configuracionVacia == true && (Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2))
            <div class="d-inline-block position-relative">
                <button class="btn btn-primary" onclick="alertConfig()">Nuevo Pedido</button>
            </div>
            <script>
                function alertConfig() {
                    Swal.fire({
                        title: '⚠️ Configuración incompleta',
                        html: `Debes terminar de configurar el sistema.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ir a Configuración',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#05564f',
                        cancelButtonColor: '#d33'
                    }).then((result) => {
                        if(result.isConfirmed){
                            window.location.href = "{{ route('configuracion') }}";
                        }
                    });
                }
            </script>
        @else
            <a href="{{ route('NuevaPedido') }}" class="btn btn-primary" data-loading="true">Nuevo Pedido</a>
        @endif

        {{-- Select mostrar --}}
        <div class="d-flex gap-2">
            <label for="mostrar" class="form-label fw-semibold">Mostrar</label>
            <select id="mostrar" class="form-select form-select-sm rounded-3">
                <option value="10" {{ request('mostrar') == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('mostrar') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('mostrar') == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('mostrar') == 100 ? 'selected' : '' }}>100</option>
            </select>
        </div>

        {{-- Select para filtrar por estatus abierto o cerradas --}}
        <div class="d-flex gap-2">
            <label for="Estatus" class="form-label fw-semibold">Estatus</label>
            <select id="Estatus" name="Estatus" class="form-select form-select-sm rounded-3">
                <option value="">Todos</option>
                <option value="Y" {{ request('Estatus') == 'Y' ? 'selected' : '' }}>Abiertas</option>
                <option value="N" {{ request('Estatus') == 'N' ? 'selected' : '' }}>Cerradas</option>
            </select>
        </div>
        
        
        {{-- Filtros de búsqueda --}}
        <div class="d-flex gap-2">
            <input type="text" id="buscarPedido" class="form-control" placeholder="Buscar...">
            <input type="date" id="fechaPedido" class="form-control" max="{{ date('Y-m-d') }}">
        </div>
    </div>

    {{-- Tabla --}}
    <div id="tablaPedidosContainer">
        @include('partials.tabla_pedidos')
    </div>
</div>

<script>
$(document).ready(function() {
    function fetchPedidos(url = "{{ route('Pedidos') }}") {
        const buscar = $('#buscarPedido').val();
        const fecha = $('#fechaPedido').val();
        const mostrar = $('#mostrar').val();
        const Estatus = $('#Estatus').val();

        $.ajax({
            url: url,
            method: 'GET',
            data: { buscar, fecha, mostrar, Estatus },
            success: function(data) {
                $('#tablaPedidosContainer').html(data);
            },
            error: function() {
                alert('Error al cargar los pedidos.');
            }
        });
    }

    // Eventos de búsqueda y filtros
    $('#buscarPedido').on('keyup', function() { fetchPedidos(); });
    $('#fechaPedido, #mostrar').on('change', function() { fetchPedidos(); });
    $('#Estatus').on('change', function() { fetchPedidos(); });

    // Paginación AJAX
    $(document).on('click', '#tablaPedidosContainer .pagination a', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        fetchPedidos(url);
        $('html, body').animate({ scrollTop: 0 }, 200);
    });
});
</script>

@endsection
