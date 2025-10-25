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

    <!-- Paginación -->
    <div class="d-flex justify-content-center mt-3">
        {{ $pedidos->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>