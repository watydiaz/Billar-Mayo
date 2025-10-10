<!-- Información del Pedido -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Información del Pedido
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <strong>Cliente:</strong><br>
                        <span class="text-muted">{{ $pedido->nombre_cliente }}</span>
                    </div>
                    <div class="col-6">
                        <strong>Mesa:</strong><br>
                        <span class="text-muted">
                            @if($pedido->mesa)
                                Mesa {{ $pedido->mesa->numero }}
                            @else
                                Sin mesa asignada
                            @endif
                        </span>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-6">
                        <strong>Estado:</strong><br>
                        <span class="badge {{ $pedido->estado_badge }}">
                            {{ $pedido->estado_texto }}
                        </span>
                    </div>
                    <div class="col-6">
                        <strong>Total:</strong><br>
                        <span class="text-success fw-bold fs-5">
                            ${{ number_format($pedido->total, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">
                    <i class="bi bi-clock me-2"></i>
                    Control de Tiempo
                </h6>
            </div>
            <div class="card-body">
                @if($pedido->tiempo_inicio)
                    <div class="row mb-3">
                        <div class="col-6">
                            <strong>Inicio:</strong><br>
                            <span class="text-muted">
                                {{ $pedido->tiempo_inicio->format('H:i:s') }}
                            </span>
                        </div>
                        <div class="col-6">
                            <strong>Transcurrido:</strong><br>
                            <span class="text-primary fw-bold">
                                {{ $pedido->tiempo_transcurrido }} minutos
                            </span>
                        </div>
                    </div>
                    @if($pedido->estado == '1' && $pedido->mesa)
                        <form method="POST" action="{{ route('pedidos.finalizar-tiempo', $pedido) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm">
                                <i class="bi bi-stop-circle me-1"></i>
                                Finalizar Tiempo
                            </button>
                        </form>
                    @endif
                @else
                    <p class="text-muted mb-3">El tiempo en mesa no ha sido iniciado</p>
                    @if($pedido->mesa && $pedido->estado == '1')
                        <form method="POST" action="{{ route('pedidos.iniciar-tiempo', $pedido) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="bi bi-play-circle me-1"></i>
                                Iniciar Tiempo en Mesa
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Rondas del Pedido -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="bi bi-cup-hot me-2"></i>
                    Rondas del Pedido ({{ $pedido->rondas->count() }})
                </h6>
                <button class="btn btn-dark btn-sm" data-bs-toggle="modal" 
                        data-bs-target="#nuevaRondaModal{{ $pedido->id }}">
                    <i class="bi bi-plus me-1"></i>
                    Agregar Ronda
                </button>
            </div>
            <div class="card-body">
                @if($pedido->rondas->isEmpty())
                    <div class="text-center py-3">
                        <i class="bi bi-cup text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-2 mb-0">No hay rondas agregadas</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Responsable</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unit.</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pedido->rondas as $ronda)
                                    <tr>
                                        <td>
                                            <i class="bi bi-person me-1 text-primary"></i>
                                            {{ $ronda->responsable }}
                                        </td>
                                        <td>{{ $ronda->producto->nombre ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $ronda->cantidad }}</span>
                                        </td>
                                        <td>${{ number_format($ronda->precio_unitario, 0, ',', '.') }}</td>
                                        <td class="fw-bold text-success">
                                            ${{ number_format($ronda->subtotal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="4" class="text-end">Total:</th>
                                    <th class="text-success">
                                        ${{ number_format($pedido->rondas->sum('subtotal'), 0, ',', '.') }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Acciones del Pedido -->
<div class="row">
    <div class="col-12">
        <div class="d-flex gap-2">
            <form method="POST" action="{{ route('pedidos.eliminar', $pedido) }}" class="d-inline"
                  onsubmit="return confirm('¿Estás seguro de eliminar este pedido?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="bi bi-trash me-1"></i>
                    Eliminar Pedido
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal Nueva Ronda -->
<div class="modal fade" id="nuevaRondaModal{{ $pedido->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-2"></i>
                    Nueva Ronda - {{ $pedido->nombre_cliente }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('pedidos.agregar-ronda', $pedido) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="responsable{{ $pedido->id }}" class="form-label">
                            <i class="bi bi-person me-1"></i>
                            Responsable de la Ronda
                        </label>
                        <input type="text" class="form-control" 
                               id="responsable{{ $pedido->id }}" 
                               name="responsable" required>
                    </div>
                    <div class="mb-3">
                        <label for="producto_id{{ $pedido->id }}" class="form-label">
                            <i class="bi bi-cup-hot me-1"></i>
                            Producto
                        </label>
                        <select class="form-select" id="producto_id{{ $pedido->id }}" 
                                name="producto_id" required>
                            <option value="">Seleccionar producto...</option>
                            @foreach($productos as $producto)
                                <option value="{{ $producto->id }}" data-precio="{{ $producto->precio }}">
                                    {{ $producto->nombre }} - ${{ number_format($producto->precio, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="cantidad{{ $pedido->id }}" class="form-label">
                            <i class="bi bi-hash me-1"></i>
                            Cantidad
                        </label>
                        <input type="number" class="form-control" 
                               id="cantidad{{ $pedido->id }}" 
                               name="cantidad" min="1" value="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check me-1"></i>
                        Agregar Ronda
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>