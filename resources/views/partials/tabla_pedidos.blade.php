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
                    <a href="{{ route('detallesP', $pedido->DocEntry) }}" style="cursor: pointer; color: blue; text-decoration: underline;" data-loading="true">
                        PE - {{ $pedido->DocEntry }}
                    </a>
                    @if(Auth::user()->rol_id != 3)
                    @if($pedido->DocStatus == 'A')
                        <i class="bi bi-unlock-fill text-success ms-2" title="Pedido abierto"></i>
                    @else
                        <i class="bi bi-lock-fill text-danger ms-2" title="Pedido cerrado"></i>
                    @endif @endif 
                </td>
                <td>{{ \Carbon\Carbon::parse($pedido->DocDate)->format('d-m-Y') }}</td>
                <td>{{ $pedido->CardName }}</td>
                <td>{{ $pedido->Vendedor->SlpName ?? '' }}</td>
                <td>{{ number_format($pedido->Total,2) }} {{ $pedido->moneda->Currency ?? '' }}</td>
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