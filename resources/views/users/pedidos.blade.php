@extends('layouts.app')

@section('title', 'Cotizaciones')
@section('contenido')

<div class="table-responsive mt-4">
    <h3 class="mb-3 fw-bold">PEDIDOS</h3>

    
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <a href="{{ route('NuevaPedido') }}" class="btn btn-primary">Nuevo Pedido</a>
         <div class="d-flex gap-2">
            <input type="text" id="buscarPedido" class="form-control" placeholder="Buscar...">
            <input type="date" id="fechaPedido" class="form-control" max="{{ date('Y-m-d') }}">
        </div>
    </div>

    <table class="table table-bordered table-striped m-8">
        <thead class="table-info  text-center">
            <tr>
                <th>Folio</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Vendedor</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody id="tablaPedido">
        @forelse($pedidos as $pedido)
            <tr>
                <td>
                    <a href="{{ route('detallesP', $pedido->CotizacionDocEntry) }}" 
                        style="cursor: pointer; color: blue; text-decoration: underline;">
                        PE - {{ $pedido->DocEntry }}
                    </a>
                </td>
                <td>{{ \Carbon\Carbon::parse($pedido->CotizacionFecha)->format('d-m-Y') }}</td>
                <td>{{ $pedido->Cliente }}</td>
                <td>{{ $pedido->Vendedor }}</td>
                <td>${{ number_format($pedido->Total, 2) }} {{ $pedido->Moneda }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">Sin pedidos disponibles</td>
            </tr>
        @endforelse
    </tbody>
    </table>
</div>


<script>
    $(document).ready(function() {
        function filtrarPedidos() {
            let texto = $('#buscarPedido').val().toLowerCase();
            let fecha = $('#fechaPedido').val(); // YYYY-MM-DD

            let coincidencias = 0;

            $('#tablaPedido tr').each(function() {
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
                $('#tablaPedido').append(`
                    <tr id="sinResultados">
                        <td colspan="5" class="text-center text-muted">
                            No se encontraron resultados
                        </td>
                    </tr>
                `);
            }
        }

        // Eventos
        $('#buscarPedido').on('keyup', filtrarPedidos);
        $('#fechaPedido').on('change', filtrarPedidos);
    });
</script>
    
@endsection
