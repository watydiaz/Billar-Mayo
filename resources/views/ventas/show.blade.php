<div class="venta-detalle">
    <!-- Información general de la venta -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h6 class="text-muted">Información General</h6>
            <table class="table table-borderless table-sm">
                <tr>
                    <td><strong>Número:</strong></td>
                    <td>{{ $venta->numero_venta }}</td>
                </tr>
                <tr>
                    <td><strong>Fecha:</strong></td>
                    <td>{{ $venta->created_at->format('d/m/Y H:i:s') }}</td>
                </tr>
                <tr>
                    <td><strong>Estado:</strong></td>
                    <td>
                        <span class="badge bg-{{ $venta->estado == '1' ? 'success' : 'secondary' }}">
                            {{ $venta->estado == '1' ? 'Completada' : 'Cancelada' }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td><strong>Tipo de pago:</strong></td>
                    <td>
                        <span class="badge bg-primary">{{ ucfirst($venta->tipo_pago) }}</span>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="col-md-6">
            <h6 class="text-muted">Resumen</h6>
            <table class="table table-borderless table-sm">
                <tr>
                    <td><strong>Subtotal:</strong></td>
                    <td class="text-end">${{ number_format($venta->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td><strong>Descuento:</strong></td>
                    <td class="text-end text-warning">-${{ number_format($venta->descuento, 2) }}</td>
                </tr>
                <tr class="table-success">
                    <td><strong>Total:</strong></td>
                    <td class="text-end"><strong>${{ number_format($venta->total, 2) }}</strong></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Observaciones -->
    @if($venta->observaciones)
        <div class="row mb-4">
            <div class="col-12">
                <h6 class="text-muted">Observaciones</h6>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    {{ $venta->observaciones }}
                </div>
            </div>
        </div>
    @endif

    <!-- Detalles de productos -->
    <div class="row">
        <div class="col-12">
            <h6 class="text-muted">Productos Vendidos</h6>
            
            @if($venta->detalles && $venta->detalles->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>Producto</th>
                                <th width="15%" class="text-center">Cantidad</th>
                                <th width="20%" class="text-end">Precio Unit.</th>
                                <th width="20%" class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($venta->detalles as $detalle)
                                <tr>
                                    <td>
                                        <strong>{{ $detalle->producto->nombre ?? 'Producto eliminado' }}</strong>
                                        @if($detalle->producto && $detalle->producto->codigo)
                                            <br><small class="text-muted">Código: {{ $detalle->producto->codigo }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $detalle->cantidad }}</span>
                                    </td>
                                    <td class="text-end">${{ number_format($detalle->precio_unitario, 2) }}</td>
                                    <td class="text-end"><strong>${{ number_format($detalle->subtotal, 2) }}</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="2"><strong>Total productos:</strong></td>
                                <td class="text-center">
                                    <strong>{{ $venta->detalles->sum('cantidad') }}</strong>
                                </td>
                                <td class="text-end">
                                    <strong>${{ number_format($venta->detalles->sum('subtotal'), 2) }}</strong>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    No se encontraron detalles para esta venta.
                </div>
            @endif
        </div>
    </div>

    <!-- Acciones -->
    <div class="row mt-4">
        <div class="col-12 text-end">
            <button type="button" class="btn btn-outline-secondary" onclick="imprimirTicket({{ $venta->id }})">
                <i class="bi bi-printer"></i> Imprimir Ticket
            </button>
            
            @if($venta->estado == '1')
                <button type="button" class="btn btn-outline-primary" onclick="duplicarVenta({{ $venta->id }})">
                    <i class="bi bi-copy"></i> Duplicar Venta
                </button>
            @endif
        </div>
    </div>
</div>