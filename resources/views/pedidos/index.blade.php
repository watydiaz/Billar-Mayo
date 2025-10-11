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



@endsection

@section('scripts')
<!-- Test JavaScript -->
<script src="{{ asset('js/test-pedidos.js') }}"></script>
<!-- Sistema de Pedidos JavaScript -->
<script src="{{ asset('js/pedidos.js') }}"></script>
<script>
// Configuración específica de la vista
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
