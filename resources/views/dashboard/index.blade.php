@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Encabezado del Dashboard -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-dark">
                        <i class="bi bi-speedometer2 me-2"></i>
                        Dashboard - Terkkos Billiards Club
                    </h1>
                    <p class="text-muted mb-0">Panel de control principal</p>
                </div>
                <div class="text-end">
                    <small class="text-muted d-block">{{ \Carbon\Carbon::now()->format('l, d \d\e F \d\e Y') }}</small>
                    <strong class="text-primary">{{ \Carbon\Carbon::now()->format('H:i:s') }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas Principales -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-uppercase fw-bold mb-1 text-muted">Pedidos Activos</h6>
                        <h2 class="mb-0 text-primary">{{ $estadisticas['pedidos_activos'] }}</h2>
                    </div>
                    <div class="ms-3">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-clipboard-check text-primary" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-uppercase fw-bold mb-1 text-muted">Mesas Ocupadas</h6>
                        <h2 class="mb-0 text-warning">{{ $estadisticas['mesas_ocupadas'] }}</h2>
                        <small class="text-muted">de {{ $estadisticas['total_mesas'] }} total</small>
                    </div>
                    <div class="ms-3">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-table text-warning" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-uppercase fw-bold mb-1 text-muted">Mesas Disponibles</h6>
                        <h2 class="mb-0 text-success">{{ $estadisticas['mesas_disponibles'] }}</h2>
                        <small class="text-muted">listas para usar</small>
                    </div>
                    <div class="ms-3">
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-check-circle text-success" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-uppercase fw-bold mb-1 text-muted">Ingresos Hoy</h6>
                        <h2 class="mb-0 text-success">${{ number_format($estadisticas['ingresos_dia'], 0, ',', '.') }}</h2>
                    </div>
                    <div class="ms-3">
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-currency-dollar text-success" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección Principal -->
    <div class="row">
        <!-- Pedidos Recientes -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        Pedidos Recientes
                    </h5>
                    <a href="{{ route('pedidos.index') }}" class="btn btn-outline-primary btn-sm">
                        Ver Todos
                    </a>
                </div>
                <div class="card-body">
                    @if($pedidosRecientes->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2">No hay pedidos activos</p>
                        </div>
                    @else
                        @foreach($pedidosRecientes as $pedido)
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="bi bi-person-fill text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $pedido->nombre_cliente }}</h6>
                                        <small class="text-muted">{{ $pedido->numero_pedido }}</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-success">${{ number_format($pedido->total, 0, ',', '.') }}</span>
                                    <br>
                                    <small class="text-muted">{{ $pedido->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <!-- Accesos Rápidos -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning me-2"></i>
                        Accesos Rápidos
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="{{ route('pedidos.index') }}" class="btn btn-primary btn-lg">
                            <i class="bi bi-plus-circle me-2"></i>
                            Gestionar Pedidos
                        </a>
                        
                        <button class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#nuevaRondaModal">
                            <i class="bi bi-cup me-2"></i>
                            Nueva Ronda
                        </button>
                        
                        <button class="btn btn-info btn-lg">
                            <i class="bi bi-table me-2"></i>
                            Estado de Mesas
                        </button>
                        
                        <button class="btn btn-warning btn-lg">
                            <i class="bi bi-graph-up me-2"></i>
                            Reportes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estado de Mesas -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">
                        <i class="bi bi-grid-3x3 me-2"></i>
                        Estado Actual de las Mesas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($mesasEstado as $mesa)
                            @php
                                $alquilerActivo = $mesa->mesaAlquileres->first();
                                $ocupada = $alquilerActivo ? true : false;
                            @endphp
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                                <div class="card h-100 {{ $ocupada ? 'border-warning' : 'border-success' }}">
                                    <div class="card-body text-center">
                                        <div class="mb-2">
                                            <i class="bi bi-table {{ $ocupada ? 'text-warning' : 'text-success' }}" style="font-size: 2rem;"></i>
                                        </div>
                                        <h6 class="card-title">Mesa {{ $mesa->numero_mesa ?? $mesa->id }}</h6>
                                        @if($ocupada)
                                            <span class="badge bg-warning text-dark">Ocupada</span>
                                            <p class="card-text mt-2">
                                                <small class="text-muted">
                                                    Desde: {{ $alquilerActivo->created_at->format('H:i') }}
                                                </small>
                                            </p>
                                        @else
                                            <span class="badge bg-success">Disponible</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('head')
<style>
    .card {
        transition: transform 0.2s ease-in-out;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
    
    .bg-opacity-10 {
        background-color: rgba(var(--bs-primary-rgb), 0.1) !important;
    }
</style>
@endpush

@push('scripts')
<script>
    // Auto-refresh cada minuto para mantener datos actualizados
    setInterval(function() {
        // Aquí podríamos hacer una llamada AJAX para actualizar estadísticas
        // Por ahora solo actualizamos la hora
        const tiempoActual = new Date().toLocaleTimeString();
        // document.querySelector('.tiempo-actual').textContent = tiempoActual;
    }, 60000);
</script>
@endpush
@endsection