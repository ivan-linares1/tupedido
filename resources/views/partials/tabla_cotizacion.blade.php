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
    <tbody>
        @forelse($cotizaciones as $cotizacion)
            <tr>
                <td>
                    <a href="{{ route('detalles', $cotizacion->DocEntry) }}" style="cursor:pointer;color:blue;text-decoration:underline;">
                        CO - {{ $cotizacion->DocEntry }}
                    </a>
                </td>
                <td>{{ \Carbon\Carbon::parse($cotizacion->DocDate)->format('d-m-Y') }}</td>
                <td>{{ $cotizacion->CardName }}</td>
                <td>{{ $cotizacion->vendedor->SlpName ?? '' }}</td>
                <td>${{ number_format($cotizacion->Total,2) }} {{ $cotizacion->moneda->Currency ?? '' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">Sin cotizaciones disponibles</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- Paginación --}}
<div class="d-flex justify-content-center mt-3">
    {{ $cotizaciones->links('pagination::bootstrap-5') }}
</div>
