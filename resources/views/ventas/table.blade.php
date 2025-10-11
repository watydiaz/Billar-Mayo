<div class="table-responsive">
    <table class="table table-striped table-hover mb-0">
        <thead class="table-dark">
            <tr>
                <th width="12%">Número</th>
                <th width="15%">Fecha</th>
                <th width="12%">Subtotal</th>
                <th width="10%">Descuento</th>
                <th width="12%">Total</th>
                <th width="10%">Tipo Pago</th>
                <th width="10%">Estado</th>
                <th width="19%">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ventas as $venta)
                <tr class="venta-row {{ $venta->estado == '0' ? 'table-secondary' : '' }}">
                    <td class="fw-bold">{{ $venta->numero_venta }}</td>
                    <td>
                        <small class="text-muted">
                            {{ $venta->created_at->format('d/m/Y') }}<br>
                            {{ $venta->created_at->format('H:i:s') }}
                        </small>
                    </td>
                    <td class="text-end">
                        <span class="badge bg-light text-dark">
                            ${{ number_format($venta->subtotal, 2) }}
                        </span>
                    </td>
                    <td class="text-end">
                        @if($venta->descuento > 0)
                            <span class="badge bg-warning">
                                -${{ number_format($venta->descuento, 2) }}
                            </span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <strong class="text-success">
                            ${{ number_format($venta->total, 2) }}
                        </strong>
                    </td>
                    <td>
                        <span class="badge bg-{{ $venta->tipo_pago == 'efectivo' ? 'success' : ($venta->tipo_pago == 'tarjeta' ? 'primary' : 'info') }}">
                            {{ ucfirst($venta->tipo_pago) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-{{ $venta->estado == '1' ? 'success' : 'secondary' }}">
                            {{ $venta->estado == '1' ? 'Completada' : 'Cancelada' }}
                        </span>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-info" 
                                    onclick="verDetalle({{ $venta->id }})"
                                    title="Ver detalles">
                                <i class="bi bi-eye"></i>
                            </button>
                            
                            <button class="btn btn-outline-primary" 
                                    onclick="imprimirTicket({{ $venta->id }})"
                                    title="Imprimir ticket">
                                <i class="bi bi-printer"></i>
                            </button>

                            @if($venta->observaciones)
                                <button class="btn btn-outline-warning" 
                                        title="Tiene observaciones: {{ $venta->observaciones }}"
                                        data-bs-toggle="tooltip">
                                    <i class="bi bi-chat-text"></i>
                                </button>
                            @endif

                            @if($venta->estado == '1')
                                <button class="btn btn-outline-secondary btn-sm" 
                                        onclick="duplicarVenta({{ $venta->id }})"
                                        title="Duplicar venta">
                                    <i class="bi bi-copy"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <div class="text-muted">
                            <i class="bi bi-receipt" style="font-size: 3rem;"></i>
                            <p class="mt-2">No se encontraron ventas con los filtros aplicados</p>
                            <button class="btn btn-outline-primary" id="limpiarFiltrosEmpty">
                                <i class="bi bi-funnel"></i> Limpiar filtros
                            </button>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Paginación -->
@if($ventas->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted">
            Mostrando {{ $ventas->firstItem() }} a {{ $ventas->lastItem() }} de {{ $ventas->total() }} resultados
        </div>
        <nav>
            {{ $ventas->links() }}
        </nav>
    </div>
@endif