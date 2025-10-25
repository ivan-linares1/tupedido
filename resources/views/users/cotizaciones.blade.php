@extends('layouts.app')

@section('title', 'Cotizaciones')

@section('contenido')

@vite(['resources/css/tablas.css'])

<div class="table-responsive mt-4">
    <h3 class="mb-3 fw-bold">COTIZACIONES</h3>

    {{-- Contenedor de filtros y botón --}}
    <div class="mb-3 d-flex justify-content-between align-items-center">
        {{-- Botón Nueva Cotización --}}
        @if($configuracionVacia && in_array(Auth::user()->rol_id, [3,4]))
            <div class="d-inline-block position-relative">
                <button class="btn btn-primary" disabled>Nueva Cotización</button>
                <small class="mensaje-cambio text-danger">⚠️ {!! 'Contacte a soporte: <br> Problema de configuración.' !!}</small>
            </div>
        @elseif($configuracionVacia && in_array(Auth::user()->rol_id, [1,2]))
            <div class="d-inline-block position-relative">
                <button class="btn btn-primary" onclick="alertConfig()">Nueva Cotización</button>
            </div>
            <script>
                // Alerta para usuarios administradores si la configuración está vacía
                function alertConfig() {
                    Swal.fire({
                        title: '⚠️ Configuración incompleta',
                        html: 'Debes terminar de configurar el sistema.',
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
            <a href="{{ route('NuevaCotizacion') }}" class="btn btn-primary">Nueva Cotización</a>
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

        {{-- Filtros de búsqueda y paginación --}}
        <div class="d-flex gap-2">
            <input type="text" id="buscarCotizacion" class="form-control" placeholder="Buscar...">
            <input type="date" id="fechaCotizacion" class="form-control" max="{{ date('Y-m-d') }}">
        </div>
    </div>

    {{-- Contenedor de tabla --}}
    <div id="tablaCotizacionesContainer">
        @include('partials.tabla_cotizacion')
    </div>
</div>

<script>
$(document).ready(function() {

    function fetchCotizaciones(url) {
        if(!url) url = "{{ route('cotizaciones') }}";

        const buscar = $('#buscarCotizacion').val();
        const fecha = $('#fechaCotizacion').val();
        const mostrar = $('#mostrar').val();

        $.ajax({
            url: url,
            method: 'GET',
            data: { buscar, fecha, mostrar },
            success: function(data) {
                $('#tablaCotizacionesContainer').html(data);
            },
            error: function(xhr) {
                console.error(xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudieron cargar las cotizaciones.'
                });
            }
        });
    }

    // Búsqueda y filtros
    $('#buscarCotizacion').on('keyup', function() { fetchCotizaciones(); });
    $('#fechaCotizacion, #mostrar').on('change', function() { fetchCotizaciones(); });

    // Paginación AJAX
    $(document).on('click', '#tablaCotizacionesContainer .pagination a', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        if(url) fetchCotizaciones(url);
    });

});

</script>

@endsection
