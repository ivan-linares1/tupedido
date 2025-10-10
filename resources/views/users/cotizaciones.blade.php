@extends('layouts.app')

@section('title', 'Cotizaciones')

@section('contenido')

@vite(['resources/css/tablas.css'])

<div class="table-responsive mt-4">
    <h3 class="mb-3 fw-bold">COTIZACIONES</h3>

    <div class="mb-3 d-flex justify-content-between align-items-center">
        <a href="{{ route('NuevaCotizacion') }}" class="btn btn-primary">Nueva Cotización</a>
        <div class="d-flex gap-2">
            <input type="text" id="buscarCotizacion" class="form-control" placeholder="Buscar...">
            <input type="date" id="fechaCotizacion" class="form-control" max="{{ date('Y-m-d') }}">
        </div>
    </div>

    <table class="table table-bordered table-striped m-8">
        <thead class="text-center">
            <tr>
                <th>Folio</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Vendedor</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody id="tablaCotizaciones">
            @forelse($cotizaciones as $cotizacion)
                <tr>
                    <td>
                        <a href="{{ route('detalles', $cotizacion->DocEntry) }}" 
                        style="cursor: pointer; color: blue; text-decoration: underline;">
                        CO - {{ $cotizacion->DocEntry }}
                        </a>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($cotizacion->DocDate)->format('d-m-Y') }}</td>
                    <td>{{ $cotizacion->CardName }}</td>
                    <td>{{ $cotizacion->vendedor_nombre }}</td>
                    <td>${{ number_format($cotizacion->Total, 2) }} {{ $cotizacion->moneda_nombre }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Sin cotizaciones disponibles</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        function filtrarCotizaciones() {
            let texto = $('#buscarCotizacion').val().toLowerCase();
            let fecha = $('#fechaCotizacion').val(); // YYYY-MM-DD

            let coincidencias = 0;

            $('#tablaCotizaciones tr').each(function() {
                let filaTexto = $(this).text().toLowerCase();
                let filaFecha = $(this).find('td:nth-child(2)').text(); // columna fecha en formato d-m-Y

                // Convertimos filaFecha a formato YYYY-MM-DD
                let partes = filaFecha.split('-'); // ['dd','mm','yyyy']
                let filaFechaISO = partes[2] + '-' + partes[1] + '-' + partes[0];

                let textoCoincide = filaTexto.indexOf(texto) > -1;
                let fechaCoincide = !fecha || filaFechaISO === fecha;

                let mostrar = textoCoincide && fechaCoincide;
                $(this).toggle(mostrar);

                if (mostrar) coincidencias++;
            });

            // Si no hay coincidencias, mostramos una fila de mensaje
            $('#sinResultados').remove(); // eliminar mensaje previo si existe
            if (coincidencias === 0) {
                $('#tablaCotizaciones').append(`
                    <tr id="sinResultados">
                        <td colspan="5" class="text-center text-muted">
                            No se encontraron resultados
                        </td>
                    </tr>
                `);
            }
        }

        // Eventos
        $('#buscarCotizacion').on('keyup', filtrarCotizaciones);
        $('#fechaCotizacion').on('change', filtrarCotizaciones);
    });
</script>


@endsection
