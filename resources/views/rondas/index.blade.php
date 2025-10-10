@extends('layouts.app')

@section('title', 'Gestión de Rondas')

@section('main-class', 'container-fluid')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="bi bi-stopwatch text-primary me-2"></i>
                        Gestión de Rondas y Tiempo
                    </h2>
                    <p class="text-muted mb-0">Control de rondas de billar con tiempo de mesa</p>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevaRondaModal">
                    <i class="bi bi-plus-circle me-2"></i>
                    Nueva Ronda
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
                            <h6 class="text-uppercase fw-bold mb-1">Rondas Activas</h6>
                            <h3 class="mb-0">{{ $rondasActivas->count() }}</h3>
                        </div>
                        <i class="bi bi-play-circle" style="font-size: 2rem; opacity: 0.7;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase fw-bold mb-1">Mesas Ocupadas</h6>
                            <h3 class="mb-0">{{ $mesasOcupadas }}</h3>
                        </div>
                        <i class="bi bi-grid-3x3-gap" style="font-size: 2rem; opacity: 0.7;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase fw-bold mb-1">Tiempo Total Hoy</h6>
                            <h3 class="mb-0">{{ $tiempoTotalHoy }} min</h3>
                        </div>
                        <i class="bi bi-clock" style="font-size: 2rem; opacity: 0.7;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase fw-bold mb-1">Ingresos Hoy</h6>
                            <h3 class="mb-0">${{ number_format($ingresosTiempoHoy, 0, ',', '.') }}</h3>
                        </div>
                        <i class="bi bi-currency-dollar" style="font-size: 2rem; opacity: 0.7;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rondas Activas -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-list-ul me-2"></i>
                        Rondas con Tiempo Activo
                    </h5>
                </div>
                <div class="card-body">
                    @if($rondasActivas->count() > 0)
                        <div class="accordion" id="rondasAccordion">
                            @foreach($rondasActivas as $index => $ronda)
                                <div class="accordion-item mb-2 border rounded">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#ronda{{ $ronda->id }}" aria-expanded="false" aria-controls="ronda{{ $ronda->id }}">
                                            <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-stopwatch text-primary me-2"></i>
                                                    <strong>Ronda #{{ $ronda->numero_ronda }}</strong>
                                                    <span class="mx-2">-</span>
                                                    <span>{{ $ronda->responsable }}</span>
                                                    @if($ronda->mesaRonda && $ronda->mesaRonda->mesa)
                                                        <span class="badge bg-success ms-2">Mesa {{ $ronda->mesaRonda->mesa->numero_mesa }}</span>
                                                    @endif
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    @if($ronda->mesaRonda && $ronda->mesaRonda->isActivo())
                                                        <span class="badge bg-warning me-2">TIEMPO ACTIVO</span>
                                                        <span class="text-success fw-bold timer" data-ronda-id="{{ $ronda->id }}">00:00:00</span>
                                                    @else
                                                        <span class="badge bg-secondary">PENDIENTE</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="ronda{{ $ronda->id }}" class="accordion-collapse collapse" data-bs-parent="#rondasAccordion">
                                        <div class="accordion-body">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <!-- Información de la ronda -->
                                                    <div class="card border-0 bg-light">
                                                        <div class="card-body">
                                                            <h6 class="text-primary mb-3">
                                                                <i class="bi bi-info-circle me-1"></i>
                                                                Información de la Ronda
                                                            </h6>
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <p class="mb-2"><strong>Pedido:</strong> {{ $ronda->pedido->numero_pedido }}</p>
                                                                    <p class="mb-2"><strong>Cliente:</strong> {{ $ronda->pedido->nombre_cliente }}</p>
                                                                    <p class="mb-2"><strong>Responsable:</strong> {{ $ronda->responsable }}</p>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <p class="mb-2"><strong>Total Ronda:</strong> ${{ number_format($ronda->total_ronda, 0, ',', '.') }}</p>
                                                                    <p class="mb-2"><strong>Estado:</strong> 
                                                                        <span class="badge {{ $ronda->estado == 'activa' ? 'bg-success' : 'bg-secondary' }}">
                                                                            {{ ucfirst($ronda->estado) }}
                                                                        </span>
                                                                    </p>
                                                                    <p class="mb-0"><strong>Creada:</strong> {{ $ronda->created_at->format('d/m/Y H:i') }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <!-- Control de Tiempo -->
                                                    @if($ronda->mesaRonda)
                                                        @if($ronda->mesaRonda->isActivo())
                                                            <!-- Tiempo activo -->
                                                            <div class="card border-warning">
                                                                <div class="card-body text-center">
                                                                    <h6 class="text-warning mb-3">
                                                                        <i class="bi bi-stopwatch me-1"></i>
                                                                        Control de Tiempo
                                                                    </h6>
                                                                    <div class="mb-3">
                                                                        <div class="display-6 text-success timer" data-ronda-id="{{ $ronda->id }}">00:00:00</div>
                                                                        <small class="text-muted">Tiempo transcurrido</small>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <div class="h5 text-warning costo-tiempo" data-ronda-id="{{ $ronda->id }}">$0</div>
                                                                        <small class="text-muted">Costo actual</small>
                                                                    </div>
                                                                    <form method="POST" action="{{ route('rondas.finalizar-tiempo', $ronda) }}" class="d-inline">
                                                                        @csrf
                                                                        <button type="submit" class="btn btn-danger btn-lg w-100">
                                                                            <i class="bi bi-stop-circle me-1"></i>
                                                                            PARAR TIEMPO
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        @elseif($ronda->mesaRonda->estado === 'pendiente')
                                                            <!-- Mesa asignada pero tiempo no iniciado -->
                                                            <div class="card border-info">
                                                                <div class="card-body text-center">
                                                                    <i class="bi bi-play-circle text-info" style="font-size: 3rem;"></i>
                                                                    <h6 class="mt-2 mb-1">Mesa {{ $ronda->mesaRonda->mesa->numero_mesa }}</h6>
                                                                    <p class="text-muted mb-3">Lista para iniciar</p>
                                                                    <form method="POST" action="{{ route('rondas.iniciar-tiempo', $ronda) }}">
                                                                        @csrf
                                                                        <button type="submit" class="btn btn-success btn-lg w-100">
                                                                            <i class="bi bi-play-circle me-1"></i>
                                                                            INICIAR TIEMPO
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <!-- Tiempo finalizado -->
                                                            <div class="card border-success">
                                                                <div class="card-body text-center">
                                                                    <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                                                                    <h6 class="mt-2 mb-1">Tiempo Finalizado</h6>
                                                                    <p class="mb-2">Duración: {{ $ronda->mesaRonda->duracion_minutos }} min</p>
                                                                    <p class="mb-0">Costo: ${{ number_format($ronda->mesaRonda->costo_tiempo, 0, ',', '.') }}</p>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @else
                                                        <!-- Sin mesa asignada -->
                                                        <div class="card border-secondary">
                                                            <div class="card-body text-center">
                                                                <i class="bi bi-grid-3x3-gap text-secondary" style="font-size: 3rem;"></i>
                                                                <h6 class="mt-2 mb-3">Asignar Mesa</h6>
                                                                <form method="POST" action="{{ route('rondas.asignar-mesa', $ronda) }}">
                                                                    @csrf
                                                                    <select name="mesa_id" class="form-select mb-3" required>
                                                                        <option value="">Seleccionar mesa...</option>
                                                                        @foreach($mesas as $mesa)
                                                                            <option value="{{ $mesa->id }}">Mesa {{ $mesa->numero_mesa }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    <button type="submit" class="btn btn-primary w-100">
                                                                        <i class="bi bi-check-lg me-1"></i>
                                                                        Asignar Mesa
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-stopwatch text-muted" style="font-size: 4rem;"></i>
                            <h5 class="text-muted mt-3">No hay rondas activas</h5>
                            <p class="text-muted">Crea una nueva ronda para comenzar a contar tiempo de mesa</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva Ronda -->
<div class="modal fade" id="nuevaRondaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('rondas.store') }}">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-plus-circle me-2"></i>
                        Nueva Ronda
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pedido</label>
                        <select name="pedido_id" class="form-select" required>
                            <option value="">Seleccionar pedido...</option>
                            @foreach($pedidos as $pedido)
                                <option value="{{ $pedido->id }}">{{ $pedido->numero_pedido }} - {{ $pedido->nombre_cliente }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Número de Ronda</label>
                        <input type="number" name="numero_ronda" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Responsable</label>
                        <input type="text" name="responsable" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>
                        Crear Ronda
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Función para actualizar timers en tiempo real
function updateTimers() {
    document.querySelectorAll('.timer').forEach(timer => {
        const rondaId = timer.getAttribute('data-ronda-id');
        
        fetch(`/rondas/${rondaId}/tiempo-real`)
            .then(response => response.json())
            .then(data => {
                if (data.activo) {
                    // Calcular tiempo transcurrido
                    const duracionMinutos = data.duracion;
                    const horas = Math.floor(duracionMinutos / 60);
                    const minutos = duracionMinutos % 60;
                    const segundos = 0; // Aproximado ya que trabajamos con minutos
                    
                    const tiempoFormateado = `${String(horas).padStart(2, '0')}:${String(minutos).padStart(2, '0')}:${String(segundos).padStart(2, '0')}`;
                    timer.textContent = tiempoFormateado;
                    
                    // Actualizar costo
                    const costoElement = document.querySelector(`.costo-tiempo[data-ronda-id="${rondaId}"]`);
                    if (costoElement) {
                        costoElement.textContent = `$${Math.round(data.costo).toLocaleString()}`;
                    }
                }
            })
            .catch(error => console.error('Error updating timer:', error));
    });
}

// Actualizar cada segundo
setInterval(updateTimers, 1000);

// Inicializar al cargar la página
document.addEventListener('DOMContentLoaded', updateTimers);
</script>
@endsection