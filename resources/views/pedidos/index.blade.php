@extends('layouts.app')

@section('title', 'Gestión de Pedidos y Rondas')

@section('main-class', 'container-fluid')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="bi bi-receipt text-primary me-2"></i>
                        Gestión de Pedidos y Rondas
                    </h2>
                    <p class="text-muted mb-0">Control de pedidos con rondas y tiempo de mesa</p>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoPedidoModal">
                    <i class="bi bi-plus-circle me-2"></i>
                    Nuevo Pedido
                </button>
            </div>
        </div>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase fw-bold mb-1">Pedidos Activos</h6>
                            <h3 class="mb-0">{{ $pedidos->count() }}</h3>
                        </div>
                        <i class="bi bi-receipt" style="font-size: 2rem; opacity: 0.7;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase fw-bold mb-1">Total Rondas</h6>
                            <h3 class="mb-0">{{ $pedidos->sum(function($p) { return $p->rondas->count(); }) }}</h3>
                        </div>
                        <i class="bi bi-stopwatch" style="font-size: 2rem; opacity: 0.7;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase fw-bold mb-1">Tiempo Activo</h6>
                            <h3 class="mb-0">{{ $pedidos->flatMap(function($p) { return $p->rondas; })->filter(function($r) { return $r->mesaRonda && $r->mesaRonda->isActivo(); })->count() }}</h3>
                        </div>
                        <i class="bi bi-play-circle" style="font-size: 2rem; opacity: 0.7;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase fw-bold mb-1">Total Ingresos</h6>
                            <h3 class="mb-0">${{ number_format($pedidos->sum('total_pedido'), 0, ',', '.') }}</h3>
                        </div>
                        <i class="bi bi-currency-dollar" style="font-size: 2rem; opacity: 0.7;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pedidos con Rondas -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-list-ul me-2"></i>
                        Pedidos Activos ({{ $pedidos->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    @if($pedidos->count() > 0)
                        <div class="accordion" id="pedidosAccordion">
                            @foreach($pedidos as $pedido)
                                <div class="accordion-item mb-3 border rounded">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button {{ session('nuevo_pedido_id') == $pedido->id ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#pedido{{ $pedido->id }}" aria-expanded="{{ session('nuevo_pedido_id') == $pedido->id ? 'true' : 'false' }}">
                                            <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-person-fill text-primary me-2"></i>
                                                    <div>
                                                        <strong>{{ $pedido->nombre_cliente }}</strong>
                                                        <small class="text-muted ms-2">{{ $pedido->numero_pedido }}</small>
                                                        <br>
                                                        <small class="text-muted">{{ $pedido->created_at->format('d/m/Y H:i') }}</small>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-primary me-2">{{ $pedido->rondas->count() }} rondas</span>
                                                    <span class="text-success fw-bold">${{ number_format($pedido->total_pedido, 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="pedido{{ $pedido->id }}" class="accordion-collapse collapse {{ session('nuevo_pedido_id') == $pedido->id ? 'show' : '' }}" data-bs-parent="#pedidosAccordion">
                                        <div class="accordion-body">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <!-- Pestañas de Rondas -->
                                                    @if($pedido->rondas->count() > 0)
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <ul class="nav nav-tabs card-header-tabs" id="rondas{{ $pedido->id }}" role="tablist">
                                                                    @foreach($pedido->rondas as $index => $ronda)
                                                                        <li class="nav-item" role="presentation">
                                                                            <button class="nav-link {{ $index === count($pedido->rondas) - 1 ? 'active' : '' }}" 
                                                                                    id="ronda{{ $ronda->id }}-tab" 
                                                                                    data-bs-toggle="tab" 
                                                                                    data-bs-target="#ronda{{ $ronda->id }}" 
                                                                                    type="button" role="tab">
                                                                                Ronda {{ $ronda->numero_ronda }}
                                                                                @if($ronda->mesaRonda && $ronda->mesaRonda->isActivo())
                                                                                    <span class="badge bg-warning ms-1">ACTIVA</span>
                                                                                @endif
                                                                            </button>
                                                                        </li>
                                                                    @endforeach
                                                                    <li class="nav-item">
                                                                        <button class="nav-link" data-bs-toggle="modal" data-bs-target="#nuevaRondaModal{{ $pedido->id }}">
                                                                            <i class="bi bi-plus-circle me-1"></i>Nueva
                                                                        </button>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="tab-content">
                                                                    @foreach($pedido->rondas as $index => $ronda)
                                                                        <div class="tab-pane fade {{ $index === count($pedido->rondas) - 1 ? 'show active' : '' }}" 
                                                                             id="ronda{{ $ronda->id }}" 
                                                                             role="tabpanel">
                                                                            
                                                                            <!-- Información de la Ronda -->
                                                                            <div class="row mb-3">
                                                                                <div class="col-md-6">
                                                                                    <h6><i class="bi bi-info-circle text-primary me-1"></i> Información</h6>
                                                                                    <p class="mb-1"><strong>Número:</strong> Ronda {{ $ronda->numero_ronda }}</p>
                                                                                    @if($ronda->responsable)
                                                                                        <p class="mb-1"><strong>Responsable:</strong> {{ $ronda->responsable }}</p>
                                                                                    @endif
                                                                                    <p class="mb-1"><strong>Estado:</strong> 
                                                                                        <span class="badge {{ $ronda->estado == 'activa' ? 'bg-success' : ($ronda->estado == 'pagada' ? 'bg-primary' : 'bg-secondary') }}">
                                                                                            {{ ucfirst($ronda->estado) }}
                                                                                        </span>
                                                                                    </p>
                                                                                    <p class="mb-0"><strong>Total:</strong> ${{ number_format($ronda->total_ronda, 0, ',', '.') }}</p>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    @if($ronda->mesaRonda && $ronda->mesaRonda->mesa)
                                                                                        <h6><i class="bi bi-grid-3x3-gap text-success me-1"></i> Mesa Asignada</h6>
                                                                                        <p class="mb-1"><strong>Mesa:</strong> {{ $ronda->mesaRonda->mesa->numero_mesa }}</p>
                                                                                        <p class="mb-0"><strong>Estado:</strong> 
                                                                                            <span class="badge bg-{{ $ronda->mesaRonda->estado === 'activo' ? 'success' : ($ronda->mesaRonda->estado === 'pendiente' ? 'warning' : 'secondary') }}">
                                                                                                {{ ucfirst($ronda->mesaRonda->estado) }}
                                                                                            </span>
                                                                                        </p>
                                                                                    @else
                                                                                        <h6><i class="bi bi-exclamation-triangle text-warning me-1"></i> Sin Mesa</h6>
                                                                                        <p class="text-muted mb-2">No hay mesa asignada a esta ronda</p>
                                                                                        <form method="POST" action="{{ route('pedidos.rondas.asignar-mesa', [$pedido, $ronda]) }}" class="d-flex">
                                                                                            @csrf
                                                                                            <select name="mesa_id" class="form-select form-select-sm me-2" required>
                                                                                                <option value="">Seleccionar mesa...</option>
                                                                                                @foreach($mesas as $mesa)
                                                                                                    <option value="{{ $mesa->id }}">Mesa {{ $mesa->numero_mesa }}</option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                            <button type="submit" class="btn btn-sm btn-outline-primary">Asignar</button>
                                                                                        </form>
                                                                                    @endif
                                                                                </div>
                                                                            </div>

                                                                            <!-- Control de Tiempo -->
                                                                            @if($ronda->mesaRonda)
                                                                                @if($ronda->mesaRonda->isActivo())
                                                                                    <!-- Tiempo Activo -->
                                                                                    <div class="alert alert-warning">
                                                                                        <div class="row align-items-center">
                                                                                            <div class="col-md-6">
                                                                                                <h6><i class="bi bi-stopwatch text-warning me-1"></i> Tiempo Activo</h6>
                                                                                                <div class="display-6 text-success timer" data-pedido-id="{{ $pedido->id }}" data-ronda-id="{{ $ronda->id }}">00:00:00</div>
                                                                                                <small class="text-muted">Tiempo transcurrido</small>
                                                                                            </div>
                                                                                            <div class="col-md-6 text-center">
                                                                                                <div class="h5 text-warning costo-tiempo" data-pedido-id="{{ $pedido->id }}" data-ronda-id="{{ $ronda->id }}">$0</div>
                                                                                                <small class="text-muted">Costo actual</small>
                                                                                                <br><br>
                                                                                                <form method="POST" action="{{ route('pedidos.rondas.finalizar-tiempo', [$pedido, $ronda]) }}" class="d-inline">
                                                                                                    @csrf
                                                                                                    <button type="submit" class="btn btn-danger">
                                                                                                        <i class="bi bi-stop-circle me-1"></i>
                                                                                                        Finalizar Tiempo
                                                                                                    </button>
                                                                                                </form>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                @elseif($ronda->mesaRonda->estado === 'pendiente')
                                                                                    <!-- Mesa asignada, tiempo no iniciado -->
                                                                                    <div class="alert alert-info text-center">
                                                                                        <i class="bi bi-play-circle text-info" style="font-size: 3rem;"></i>
                                                                                        <h6 class="mt-2">Mesa {{ $ronda->mesaRonda->mesa->numero_mesa }} lista</h6>
                                                                                        <form method="POST" action="{{ route('pedidos.rondas.iniciar-tiempo', [$pedido, $ronda]) }}">
                                                                                            @csrf
                                                                                            <button type="submit" class="btn btn-success">
                                                                                                <i class="bi bi-play-circle me-1"></i>
                                                                                                Iniciar Tiempo
                                                                                            </button>
                                                                                        </form>
                                                                                    </div>
                                                                                @else
                                                                                    <!-- Tiempo finalizado -->
                                                                                    <div class="alert alert-success">
                                                                                        <div class="text-center">
                                                                                            <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                                                                                            <h6 class="mt-2">Tiempo Finalizado</h6>
                                                                                            <p class="mb-1">Duración: {{ $ronda->mesaRonda->duracion_minutos }} minutos</p>
                                                                                            <p class="mb-3">Costo: ${{ number_format($ronda->mesaRonda->costo_tiempo, 0, ',', '.') }}</p>
                                                                                        </div>
                                                                                        
                                                                                        @if(!$ronda->responsable)
                                                                                            <!-- Asignar responsable -->
                                                                                            <div class="border-top pt-3">
                                                                                                <h6 class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i> Asignar Responsable</h6>
                                                                                                <p class="small text-muted mb-2">¿Quién perdió en esta ronda?</p>
                                                                                                <form method="POST" action="{{ route('pedidos.rondas.asignar-responsable', [$pedido, $ronda]) }}" class="d-flex">
                                                                                                    @csrf
                                                                                                    <input type="text" name="responsable" class="form-control form-control-sm me-2" placeholder="Nombre del responsable..." required>
                                                                                                    <button type="submit" class="btn btn-warning btn-sm">
                                                                                                        <i class="bi bi-check-lg me-1"></i>
                                                                                                        Asignar
                                                                                                    </button>
                                                                                                </form>
                                                                                            </div>
                                                                                        @else
                                                                                            <!-- Responsable ya asignado -->
                                                                                            <div class="border-top pt-3 text-center">
                                                                                                <span class="badge bg-primary fs-6">
                                                                                                    <i class="bi bi-person-check me-1"></i>
                                                                                                    Responsable: {{ $ronda->responsable }}
                                                                                                </span>
                                                                                            </div>
                                                                                        @endif
                                                                                    </div>
                                                                                @endif
                                                                            @endif

                                                                            <!-- Productos de la Ronda -->
                                                                            <div class="mt-4 pt-3 border-top">
                                                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                                                    <h6 class="mb-0">
                                                                                        <i class="bi bi-cart3 text-info me-1"></i>
                                                                                        Productos de la Ronda
                                                                                    </h6>
                                                                                    <button type="button" class="btn btn-outline-info btn-sm" 
                                                                                            data-bs-toggle="modal" 
                                                                                            data-bs-target="#agregarProductoModal" 
                                                                                            data-ronda-id="{{ $ronda->id }}"
                                                                                            data-pedido-id="{{ $pedido->id }}">
                                                                                        <i class="bi bi-plus-circle me-1"></i>
                                                                                        Agregar Producto
                                                                                    </button>
                                                                                </div>
                                                                                
                                                                                <!-- DEBUG: Mostrar información de detalles -->
                                                                                <div class="alert alert-info small mb-2">
                                                                                    <strong>DEBUG:</strong> 
                                                                                    Ronda ID: {{ $ronda->id }} | 
                                                                                    Detalles: {{ $ronda->detalles ? $ronda->detalles->count() : 'NULL' }} | 
                                                                                    Relación cargada: {{ $ronda->relationLoaded('detalles') ? 'SÍ' : 'NO' }}
                                                                                </div>
                                                                                
                                                                                @if($ronda->detalles && $ronda->detalles->count() > 0)
                                                                                    <div class="table-responsive">
                                                                                        <table class="table table-sm">
                                                                                            <thead class="table-light">
                                                                                                <tr>
                                                                                                    <th>Producto</th>
                                                                                                    <th>Cant.</th>
                                                                                                    <th>Precio Unit.</th>
                                                                                                    <th>Subtotal</th>
                                                                                                    <th class="text-center">Acciones</th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody>
                                                                                                @foreach($ronda->detalles as $detalle)
                                                                                                    <tr>
                                                                                                        <td>
                                                                                                            @if($detalle->es_producto_personalizado)
                                                                                                                <i class="bi bi-gear text-warning me-1" title="Producto personalizado"></i>
                                                                                                            @endif
                                                                                                            {{ $detalle->nombre_producto }}
                                                                                                            @if($detalle->notas)
                                                                                                                <br><small class="text-muted">{{ $detalle->notas }}</small>
                                                                                                            @endif
                                                                                                        </td>
                                                                                                        <td>{{ $detalle->cantidad }}</td>
                                                                                                        <td>${{ number_format($detalle->precio_unitario, 0, ',', '.') }}</td>
                                                                                                        <td>
                                                                                                            @if($detalle->es_descuento)
                                                                                                                <span class="text-danger">-${{ number_format(abs($detalle->subtotal), 0, ',', '.') }}</span>
                                                                                                            @else
                                                                                                                <span class="text-success">${{ number_format($detalle->subtotal, 0, ',', '.') }}</span>
                                                                                                            @endif
                                                                                                        </td>
                                                                                                        <td class="text-center">
                                                                                                            <button type="button" class="btn btn-outline-danger btn-sm" 
                                                                                                                    onclick="eliminarDetalle({{ $detalle->id }})"
                                                                                                                    title="Eliminar producto">
                                                                                                                <i class="bi bi-trash"></i>
                                                                                                            </button>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                @endforeach
                                                                                            </tbody>
                                                                                            <tfoot class="table-light">
                                                                                                <tr>
                                                                                                    <td colspan="3" class="text-end"><strong>Total Productos:</strong></td>
                                                                                                    <td><strong class="text-success">${{ number_format($ronda->detalles->sum('subtotal'), 0, ',', '.') }}</strong></td>
                                                                                                    <td></td>
                                                                                                </tr>
                                                                                            </tfoot>
                                                                                        </table>
                                                                                    </div>
                                                                                @else
                                                                                    <div class="text-center py-3">
                                                                                        <i class="bi bi-cart-x text-muted" style="font-size: 2.5rem;"></i>
                                                                                        <h6 class="text-muted mt-2">Sin productos</h6>
                                                                                        <p class="text-muted small mb-3">Esta ronda aún no tiene productos agregados</p>
                                                                                        <button type="button" class="btn btn-info btn-sm" 
                                                                                                data-bs-toggle="modal" 
                                                                                                data-bs-target="#agregarProductoModal"
                                                                                                data-ronda-id="{{ $ronda->id }}"
                                                                                                data-pedido-id="{{ $pedido->id }}">
                                                                                            <i class="bi bi-plus-circle me-1"></i>
                                                                                            Agregar Primer Producto
                                                                                        </button>
                                                                                    </div>
                                                                                @endif
                                                                            </div>

                                                                            <!-- Botón de Pago Individual de Ronda -->
                                                                            <div class="mt-4 pt-3 border-top pago-section">
                                                                                <div class="row align-items-center">
                                                                                    <div class="col-md-8">
                                                                                        <h6 class="text-warning mb-1">
                                                                                            <i class="fas fa-credit-card me-1"></i>
                                                                                            Pago Individual de Ronda
                                                                                        </h6>
                                                                                        <small class="text-muted">
                                                                                            Pagar únicamente esta ronda (Ronda {{ $ronda->numero_ronda }})
                                                                                        </small>
                                                                                    </div>
                                                                                    <div class="col-md-4 text-end">
                                                                                        <div class="mb-2">
                                                                                            <strong class="text-success h5">
                                                                                                ${{ number_format($ronda->total_ronda, 0, ',', '.') }}
                                                                                            </strong>
                                                                                        </div>
                                                                                        <button type="button" class="btn btn-warning btn-pago-ronda" 
                                                                                                onclick="abrirPagoRonda({{ $ronda->id }})"
                                                                                                title="Pagar esta ronda únicamente">
                                                                                            <i class="bi bi-cash-coin me-1"></i>
                                                                                            Pagar Ronda
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <!-- No hay rondas -->
                                                        <div class="text-center py-4">
                                                            <i class="bi bi-stopwatch text-muted" style="font-size: 3rem;"></i>
                                                            <h6 class="text-muted mt-2">Sin rondas</h6>
                                                            <p class="text-muted">Este pedido no tiene rondas creadas</p>
                                                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevaRondaModal{{ $pedido->id }}">
                                                                <i class="bi bi-plus-circle me-1"></i>
                                                                Crear Primera Ronda
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="col-md-4">
                                                    <!-- Resumen del Pedido -->
                                                    <div class="card bg-light">
                                                        <div class="card-body">
                                                            <h6 class="text-primary mb-3">
                                                                <i class="bi bi-receipt me-1"></i>
                                                                Resumen del Pedido
                                                            </h6>
                                                            <div class="mb-3">
                                                                <p class="mb-1"><strong>Cliente:</strong> {{ $pedido->nombre_cliente }}</p>
                                                                <p class="mb-1"><strong>Número:</strong> {{ $pedido->numero_pedido }}</p>
                                                                <p class="mb-1"><strong>Rondas:</strong> {{ $pedido->rondas->count() }}</p>
                                                                <p class="mb-1"><strong>Total:</strong> <span class="text-success fw-bold">${{ number_format($pedido->total_pedido, 0, ',', '.') }}</span></p>
                                                                <p class="mb-0"><strong>Estado:</strong> 
                                                                    <span class="badge bg-success">Activo</span>
                                                                </p>
                                                            </div>

                                                            <!-- Botón Pagar Pedido Completo -->
                                                            <div class="mb-3">
                                                                <button type="button" class="btn btn-success btn-pago-completo w-100" 
                                                                        onclick="abrirCuentaCompleta('{{ $pedido->nombre_cliente }}')"
                                                                        title="Cerrar cuenta completa del pedido">
                                                                    <i class="bi bi-credit-card-2-front me-2"></i>
                                                                    Pagar Pedido Completo
                                                                    <div class="small mt-1 opacity-75">
                                                                        Cerrar todas las rondas
                                                                    </div>
                                                                </button>
                                                            </div>

                                                            <div class="d-grid gap-2">
                                                                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#nuevaRondaModal{{ $pedido->id }}">
                                                                    <i class="bi bi-plus-circle me-1"></i>
                                                                    Nueva Ronda
                                                                </button>
                                                                <button class="btn btn-outline-danger btn-sm">
                                                                    <i class="bi bi-trash me-1"></i>
                                                                    Eliminar Pedido
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                            <h5 class="text-muted mt-3">No hay pedidos activos</h5>
                            <p class="text-muted">Crea un nuevo pedido para comenzar</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nuevo Pedido -->
<div class="modal fade" id="nuevoPedidoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('pedidos.store') }}">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-plus-circle me-2"></i>
                        Nuevo Pedido
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre del Cliente</label>
                        <input type="text" name="nombre_cliente" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>
                        Crear Pedido
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modales Nueva Ronda (uno por cada pedido) -->
@foreach($pedidos as $pedido)
<div class="modal fade" id="nuevaRondaModal{{ $pedido->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('pedidos.agregar-ronda', $pedido) }}">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-plus-circle me-2"></i>
                        Nueva Ronda - {{ $pedido->nombre_cliente }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>¿Crear nueva ronda?</strong>
                <br>
                <small>El responsable se asignará al finalizar el tiempo (quien pierda)</small>
            </div>
            <p class="text-center mb-3">
                Se creará: <strong>Ronda {{ (int)($pedido->rondas->max('numero_ronda') ?? 0) + 1 }}</strong>
            </p>                    <!-- Opción de Mesa -->
                    <div class="mb-3">
                        <label class="form-label">Mesa (Opcional)</label>
                        <select name="mesa_id" class="form-select" id="mesaSelect{{ $pedido->id }}">
                            <option value="">Sin mesa - Asignar después</option>
                            @foreach($mesas as $mesa)
                                @php
                                    $mesaOcupada = false;
                                    // Verificar si la mesa está ocupada en cualquier ronda activa
                                    foreach($pedidos as $p) {
                                        foreach($p->rondas as $r) {
                                            if($r->mesaRonda && $r->mesaRonda->mesa_id == $mesa->id && $r->mesaRonda->estado == 'activo') {
                                                $mesaOcupada = true;
                                                break 2;
                                            }
                                        }
                                    }
                                @endphp
                                @if(!$mesaOcupada)
                                    <option value="{{ $mesa->id }}">Mesa {{ $mesa->numero_mesa }} - ${{ number_format($mesa->precio_hora, 0, ',', '.') }}/hora</option>
                                @endif
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Si seleccionas una mesa, el tiempo iniciará automáticamente</small>
                    </div>
                    
                    <!-- Opción de iniciar tiempo -->
                    <div class="form-check" id="iniciarTiempoCheck{{ $pedido->id }}" style="display: none;">
                        <input class="form-check-input" type="checkbox" name="iniciar_tiempo" value="1" id="iniciarTiempo{{ $pedido->id }}" checked>
                        <label class="form-check-label" for="iniciarTiempo{{ $pedido->id }}">
                            <i class="bi bi-play-circle text-success me-1"></i>
                            <strong>Iniciar tiempo inmediatamente</strong>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-1"></i>
                        Crear Ronda
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Modal para Agregar Producto -->
<div class="modal fade" id="agregarProductoModal" tabindex="-1" aria-labelledby="agregarProductoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formAgregarProducto">
                @csrf
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="agregarProductoModalLabel">
                        <i class="bi bi-cart-plus me-2"></i>
                        Agregar Producto a la Ronda
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="ronda_id_producto" name="ronda_id">
                    <input type="hidden" id="pedido_id_producto" name="pedido_id">
                    
                    <!-- Selector de Producto -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">
                                <i class="bi bi-search me-1"></i>
                                Seleccionar Producto
                            </label>
                            <select class="form-select" id="producto_id" name="producto_id" required>
                                <option value="">Buscar producto...</option>
                                @foreach($productos as $producto)
                                    <option value="{{ $producto->id }}" 
                                            data-precio="{{ $producto->precio_venta }}" 
                                            data-stock="{{ $producto->stock_actual }}"
                                            data-nombre="{{ $producto->nombre }}">
                                        {{ $producto->nombre }} - ${{ number_format($producto->precio_venta, 0, ',', '.') }}
                                        @if($producto->stock_actual <= $producto->stock_minimo)
                                            (⚠️ Stock bajo: {{ $producto->stock_actual }})
                                        @else
                                            (Stock: {{ $producto->stock_actual }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">
                                <i class="bi bi-calculator me-1"></i>
                                Cantidad
                            </label>
                            <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" value="1" required>
                        </div>
                    </div>
                    
                    <!-- Información del Producto Seleccionado -->
                    <div class="card bg-light mb-3" id="infoProducto" style="display: none;">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="mb-1" id="nombreProducto"></h6>
                                    <p class="mb-1"><strong>Precio unitario:</strong> <span id="precioUnitario"></span></p>
                                    <p class="mb-0"><strong>Stock disponible:</strong> <span id="stockDisponible"></span></p>
                                </div>
                                <div class="col-md-6 text-end">
                                    <div class="h4 text-success mb-0">
                                        <strong>Subtotal: $<span id="subtotalCalculado">0</span></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Opciones Avanzadas -->
                    <div class="accordion" id="opcionesAvanzadas">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOpciones">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOpciones" aria-expanded="false">
                                    <i class="bi bi-gear me-2"></i>
                                    Opciones Avanzadas
                                </button>
                            </h2>
                            <div id="collapseOpciones" class="accordion-collapse collapse" data-bs-parent="#opcionesAvanzadas">
                                <div class="accordion-body">
                                    <!-- Costo Editable -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-currency-dollar me-1"></i>
                                            Costo Unitario
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control" id="costo_unitario" name="costo_unitario" 
                                                   step="0.01" min="0" placeholder="0.00" readonly>
                                            <button class="btn btn-outline-secondary" type="button" id="editarCosto" 
                                                    onclick="habilitarEdicionCosto()" title="Editar costo">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        </div>
                                        <div class="form-text">
                                            <span id="estadoCosto">Usando precio del catálogo</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Precio Personalizado (Oculto - para compatibilidad) -->
                                    <input type="hidden" id="precioPersonalizado" name="precioPersonalizado">
                                    <input type="hidden" id="precio_unitario_custom" name="precio_unitario_custom">
                                    
                                    <!-- Es Descuento -->
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="es_descuento" name="es_descuento">
                                            <label class="form-check-label" for="es_descuento">
                                                <i class="bi bi-percent text-danger me-1"></i>
                                                Este es un descuento (valor negativo)
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <!-- Notas -->
                                    <div class="mb-3">
                                        <label class="form-label">
                                            <i class="bi bi-chat-dots me-1"></i>
                                            Notas adicionales
                                        </label>
                                        <textarea class="form-control" id="notas" name="notas" rows="2" placeholder="Observaciones especiales..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-info" id="btnAgregarProducto">
                        <i class="bi bi-cart-plus me-1"></i>
                        Agregar Producto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<!-- ===== SISTEMA DE PAGOS (DEFINIDO INMEDIATAMENTE) ===== -->
<script>
// Definir funciones INMEDIATAMENTE (antes de cualquier otro script)
console.log('🚀 Definiendo funciones de pago...');

// Variables globales
var rondaSeleccionada = null;
var clienteSeleccionado = null;

// PAGO INDIVIDUAL POR RONDA - FUNCIÓN GLOBAL
function abrirPagoRonda(rondaId) {
    console.log('🎯 Función abrirPagoRonda llamada con ID:', rondaId);
    
    // Información del contexto para el usuario
    const mensaje = `🎯 PAGO INDIVIDUAL DE RONDA\n\n` +
                   `Ronda ID: ${rondaId}\n\n` +
                   `💰 Se cobrará:\n` +
                   `⏱️ Tiempo de mesa (si aplica)\n` +
                   `🛒 Productos de esta ronda\n\n` +
                   `Las demás rondas quedan activas.\n\n` +
                   `(Modal próximamente)`;
    
    alert(mensaje);
    rondaSeleccionada = rondaId;
    
    console.log('✅ Función abrirPagoRonda ejecutada correctamente');
    return true;
}

// PAGO COMPLETO DEL PEDIDO - FUNCIÓN GLOBAL  
// Incluye: TODAS las rondas (tiempos + productos)
function abrirCuentaCompleta(cliente) {
    console.log('🏦 Función abrirCuentaCompleta llamada para cliente:', cliente);
    
    // Información del contexto para el usuario
    const mensaje = `🏦 PAGO COMPLETO DEL PEDIDO\n\n` +
                   `Cliente: ${cliente}\n\n` +
                   `💳 Se cobrará:\n` +
                   `🔄 TODAS las rondas activas\n` +
                   `⏱️ Todos los tiempos de mesa\n` +
                   `🛒 Todos los productos consumidos\n\n` +
                   `Esto cerrará completamente la cuenta.\n\n` +
                   `(Modal próximamente)`;
    
    alert(mensaje);
    clienteSeleccionado = cliente;
    
    console.log('✅ Función abrirCuentaCompleta ejecutada correctamente');
    return true;
}

// Funciones de procesamiento (para cuando estén los modales)
function procesarPagoRonda() {
    if (!rondaSeleccionada) {
        alert('❌ Error: No hay ronda seleccionada');
        return;
    }
    
    const mensaje = `💳 ¿Confirmar pago de la ronda ${rondaSeleccionada}?\n\n` +
                   `Se cobrará únicamente:\n` +
                   `⏱️ Tiempo de mesa de esta ronda\n` +
                   `🛒 Productos consumidos en esta ronda\n\n` +
                   `Las demás rondas permanecerán activas.`;
    
    if (confirm(mensaje)) {
        console.log('✅ Procesando pago de ronda:', rondaSeleccionada);
        
        // TODO: Aquí irá la llamada AJAX al RondaController
        alert(`✅ ¡Pago procesado!\n\nRonda ${rondaSeleccionada} pagada exitosamente.\n\n🔄 Backend próximamente`);
    }
}

function procesarCuentaCompleta() {
    if (!clienteSeleccionado) {
        alert('❌ Error: No hay cliente seleccionado');
        return;
    }
    
    const mensaje = `🏦 ¿Cerrar cuenta completa de ${clienteSeleccionado}?\n\n` +
                   `Se cobrarán TODAS las rondas:\n` +
                   `🔄 Todos los tiempos de mesa\n` +
                   `🛒 Todos los productos consumidos\n\n` +
                   `Esto cerrará completamente la cuenta del cliente.`;
    
    if (confirm(mensaje)) {
        console.log('✅ Cerrando cuenta completa para:', clienteSeleccionado);
        
        // TODO: Aquí irá la llamada AJAX al RondaController
        alert(`✅ ¡Cuenta cerrada!\n\nCliente: ${clienteSeleccionado}\nTodas las rondas procesadas.\n\n🔄 Backend próximamente`);
    }
}

// Test para verificar que las funciones están disponibles
console.log('✅ Funciones de pago definidas correctamente');
console.log('🔍 abrirPagoRonda:', typeof abrirPagoRonda);
console.log('🔍 abrirCuentaCompleta:', typeof abrirCuentaCompleta);
</script>

<!-- ===== SISTEMA DE PRODUCTOS ===== -->
<script>
// Variables globales para productos
var productos = @json($productos ?? []);

// Configurar modal cuando se abre
document.getElementById('agregarProductoModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const rondaId = button.getAttribute('data-ronda-id');
    const pedidoId = button.getAttribute('data-pedido-id');
    
    // Configurar campos ocultos
    document.getElementById('ronda_id_producto').value = rondaId;
    document.getElementById('pedido_id_producto').value = pedidoId;
    
    // Actualizar título
    document.getElementById('agregarProductoModalLabel').innerHTML = 
        '<i class="bi bi-cart-plus me-2"></i>Agregar Producto a la Ronda ' + rondaId;
    
    // Limpiar formulario
    document.getElementById('formAgregarProducto').reset();
    document.getElementById('infoProducto').style.display = 'none';
    document.getElementById('costo_unitario').value = '';
    resetearEdicionCosto();
    
    console.log('🛒 Modal de productos abierto - Ronda:', rondaId, 'Pedido:', pedidoId);
});

// Cuando se selecciona un producto
document.getElementById('producto_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const infoProducto = document.getElementById('infoProducto');
    
    if (this.value) {
        const precio = parseFloat(selectedOption.getAttribute('data-precio'));
        const stock = selectedOption.getAttribute('data-stock');
        const nombre = selectedOption.getAttribute('data-nombre');
        
        // Mostrar información del producto
        document.getElementById('nombreProducto').textContent = nombre;
        document.getElementById('precioUnitario').textContent = '$' + precio.toLocaleString('es-CO');
        document.getElementById('stockDisponible').textContent = stock;
        
        // Actualizar campo de costo editable
        document.getElementById('costo_unitario').value = precio.toFixed(2);
        resetearEdicionCosto();
        
        infoProducto.style.display = 'block';
        
        // Calcular subtotal inicial
        calcularSubtotal();
    } else {
        infoProducto.style.display = 'none';
        document.getElementById('costo_unitario').value = '';
    }
});

// Calcular subtotal cuando cambie la cantidad
document.getElementById('cantidad').addEventListener('input', calcularSubtotal);

// Calcular subtotal cuando cambie el costo unitario
document.getElementById('costo_unitario').addEventListener('input', calcularSubtotal);

// Función para calcular subtotal
function calcularSubtotal() {
    const productoSelect = document.getElementById('producto_id');
    const cantidad = parseInt(document.getElementById('cantidad').value) || 0;
    const costoUnitario = parseFloat(document.getElementById('costo_unitario').value) || 0;
    
    if (productoSelect.value && cantidad > 0 && costoUnitario > 0) {
        const subtotal = costoUnitario * cantidad;
        document.getElementById('subtotalCalculado').textContent = subtotal.toLocaleString('es-CO');
    } else {
        document.getElementById('subtotalCalculado').textContent = '0';
    }
}

// Funciones para manejar edición de costo
function habilitarEdicionCosto() {
    const costoInput = document.getElementById('costo_unitario');
    const btnEditar = document.getElementById('editarCosto');
    const estadoCosto = document.getElementById('estadoCosto');
    
    costoInput.readOnly = false;
    costoInput.focus();
    costoInput.select();
    costoInput.classList.add('editing-cost');
    
    // Evento para confirmar con Enter
    costoInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            confirmarEdicionCosto();
        }
    });
    
    // Evento para cancelar con Escape
    costoInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            cancelarEdicionCosto();
        }
    });
    
    btnEditar.innerHTML = '<i class="bi bi-check"></i>';
    btnEditar.onclick = confirmarEdicionCosto;
    btnEditar.classList.remove('btn-outline-secondary');
    btnEditar.classList.add('btn-success');
    btnEditar.title = 'Confirmar costo (Enter)';
    
    estadoCosto.innerHTML = '<i class="bi bi-pencil-square me-1"></i>Editando costo personalizado <small>(Enter: confirmar, Esc: cancelar)</small>';
    estadoCosto.style.color = '#0d6efd';
}

function confirmarEdicionCosto() {
    const costoInput = document.getElementById('costo_unitario');
    const btnEditar = document.getElementById('editarCosto');
    const estadoCosto = document.getElementById('estadoCosto');
    
    const nuevoCosto = parseFloat(costoInput.value) || 0;
    
    if (nuevoCosto <= 0) {
        alert('❌ El costo debe ser mayor a 0');
        costoInput.focus();
        return;
    }
    
    finalizarEdicionCosto();
    
    estadoCosto.innerHTML = '<i class="bi bi-check-circle me-1"></i>Usando costo personalizado: <strong>$' + nuevoCosto.toLocaleString('es-CO') + '</strong>';
    estadoCosto.style.color = '#198754';
    
    // Recalcular subtotal
    calcularSubtotal();
}

function cancelarEdicionCosto() {
    const productoSelect = document.getElementById('producto_id');
    
    if (productoSelect.value) {
        const selectedOption = productoSelect.options[productoSelect.selectedIndex];
        const precioOriginal = parseFloat(selectedOption.getAttribute('data-precio'));
        document.getElementById('costo_unitario').value = precioOriginal.toFixed(2);
    }
    
    finalizarEdicionCosto();
    resetearEdicionCosto();
}

function finalizarEdicionCosto() {
    const costoInput = document.getElementById('costo_unitario');
    const btnEditar = document.getElementById('editarCosto');
    
    costoInput.readOnly = true;
    costoInput.classList.remove('editing-cost');
    
    // Remover event listeners
    costoInput.replaceWith(costoInput.cloneNode(true));
    
    // Reconfigurar el input
    const newCostoInput = document.getElementById('costo_unitario');
    newCostoInput.addEventListener('input', calcularSubtotal);
    
    btnEditar.innerHTML = '<i class="bi bi-pencil"></i>';
    btnEditar.onclick = habilitarEdicionCosto;
    btnEditar.classList.remove('btn-success');
    btnEditar.classList.add('btn-outline-secondary');
    btnEditar.title = 'Editar costo';
}

function resetearEdicionCosto() {
    const estadoCosto = document.getElementById('estadoCosto');
    
    estadoCosto.innerHTML = '<i class="bi bi-tag me-1"></i>Usando precio del catálogo';
    estadoCosto.style.color = '#6c757d';
}

// Manejar envío del formulario
document.getElementById('formAgregarProducto').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const rondaId = document.getElementById('ronda_id_producto').value;
    const pedidoId = document.getElementById('pedido_id_producto').value;
    
    // Validaciones básicas
    if (!formData.get('producto_id')) {
        alert('❌ Por favor selecciona un producto');
        return;
    }
    
    const cantidad = parseInt(formData.get('cantidad'));
    if (!cantidad || cantidad <= 0) {
        alert('❌ La cantidad debe ser mayor a 0');
        return;
    }
    
    // Obtener precio final del campo de costo editable
    const precioUnitario = parseFloat(document.getElementById('costo_unitario').value);
    if (!precioUnitario || precioUnitario <= 0) {
        alert('❌ El costo debe ser mayor a 0');
        return;
    }
    
    // Preparar datos para enviar
    const datosProducto = {
        ronda_id: rondaId,
        producto_id: formData.get('producto_id'),
        cantidad: cantidad,
        costo_unitario: precioUnitario,
        es_descuento: document.getElementById('es_descuento').checked ? 1 : 0,
        notas: formData.get('notas') || null,
        _token: formData.get('_token')
    };
    
    console.log('📦 Enviando producto:', datosProducto);
    console.log('🔗 URL de destino:', `/pedidos/${pedidoId}/rondas/${rondaId}/productos`);
    
    // Deshabilitar botón mientras se procesa
    const btnAgregar = document.getElementById('btnAgregarProducto');
    btnAgregar.disabled = true;
    btnAgregar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Agregando...';
    
    // Enviar petición AJAX
    fetch(`/pedidos/${pedidoId}/rondas/${rondaId}/productos`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(datosProducto)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('agregarProductoModal'));
            modal.hide();
            
            // Mostrar mensaje de éxito
            alert('✅ Producto agregado exitosamente');
            
            // Recargar la página para mostrar los cambios
            window.location.reload();
        } else {
            alert('❌ Error: ' + (data.message || 'No se pudo agregar el producto'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Error de conexión. Intenta nuevamente.');
    })
    .finally(() => {
        // Rehabilitar botón
        btnAgregar.disabled = false;
        btnAgregar.innerHTML = '<i class="bi bi-cart-plus me-1"></i>Agregar Producto';
    });
});

// Función para eliminar detalle
function eliminarDetalle(detalleId) {
    if (!confirm('¿Estás seguro de que deseas eliminar este producto de la ronda?')) {
        return;
    }
    
    fetch(`/ronda-detalles/${detalleId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ Producto eliminado exitosamente');
            window.location.reload();
        } else {
            alert('❌ Error: ' + (data.message || 'No se pudo eliminar el producto'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Error de conexión. Intenta nuevamente.');
    });
}

console.log('🛒 Sistema de productos inicializado correctamente');
</script>

<!-- Test JavaScript -->
<script src="{{ asset('js/test-pedidos.js') }}"></script>
<!-- Sistema de Pedidos JavaScript -->  
<script src="{{ asset('js/pedidos.js') }}"></script>

<!-- Inicialización del sistema -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    if (window.timerSystem) {
        setTimeout(() => window.timerSystem.start(), 1000);
    }
    
    // Sistema de modal nueva ronda automático
    configurarModalNuevaRonda();
    
    console.log('✅ Sistema Terkkos completamente inicializado');
});

// Función para configurar el modal de nueva ronda automático
function configurarModalNuevaRonda() {
    // Verificar si se debe mostrar el modal de nueva ronda
    @if(session('mostrar_modal_nueva_ronda'))
        const pedidoId = {{ session('nuevo_pedido_id', 0) }};
        
        if (pedidoId) {
            console.log('🎯 Mostrando modal nueva ronda para pedido:', pedidoId);
            
            // Mostrar el modal de nueva ronda automáticamente
            setTimeout(() => {
                const modal = new bootstrap.Modal(document.getElementById(`nuevaRondaModal${pedidoId}`));
                modal.show();
                
                // También abrir el acordeón del pedido recién creado
                const acordeon = document.getElementById(`pedido${pedidoId}`);
                if (acordeon) {
                    const collapse = new bootstrap.Collapse(acordeon, { show: true });
                }
            }, 1000);
        }
    @endif
}
</script>
@endsection

@section('styles')
<style>
/* Estilos para el campo de costo editable */
#costo_unitario[readonly] {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

#costo_unitario:not([readonly]) {
    background-color: #fff3cd;
    border-color: #ffeaa7;
    box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
}

#estadoCosto {
    font-size: 0.875rem;
    font-weight: 500;
}

.input-group .btn {
    border-left: none;
}

/* Animación suave para los cambios de estado */
#costo_unitario, #estadoCosto {
    transition: all 0.3s ease;
}

/* Mejorar apariencia del botón de editar */
#editarCosto {
    min-width: 45px;
}

/* Estilos para el estado de edición */
.editing-cost {
    background-color: #fff3cd !important;
    border-color: #ffc107 !important;
    box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25) !important;
    animation: pulse-border 2s infinite;
}

@keyframes pulse-border {
    0%, 100% { border-color: #ffc107; }
    50% { border-color: #fd7e14; }
}

/* Mejorar la visualización del estado */
#estadoCosto {
    min-height: 20px;
    display: flex;
    align-items: center;
}

#estadoCosto small {
    font-size: 0.75rem;
    opacity: 0.8;
}

/* Efectos de hover para el botón de editar */
#editarCosto:hover {
    transform: scale(1.05);
}

/* Animación suave para los iconos */
#estadoCosto i {
    transition: transform 0.2s ease;
}

#estadoCosto i.bi-pencil-square {
    animation: wiggle 0.5s ease-in-out;
}

@keyframes wiggle {
    0%, 7% { transform: rotateZ(0); }
    15% { transform: rotateZ(-15deg); }
    20% { transform: rotateZ(10deg); }
    25% { transform: rotateZ(-10deg); }
    30% { transform: rotateZ(6deg); }
    35% { transform: rotateZ(-4deg); }
    40%, 100% { transform: rotateZ(0); }
}
</style>
@endsection
