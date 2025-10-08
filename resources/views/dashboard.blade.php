@extends('layouts.app')

@section('title', 'Dashboard')

@section('main-class', 'container-fluid')

@section('content')
<div class="container py-4">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="bi bi-speedometer2 text-primary me-2"></i>
                        Bienvenido, {{ Auth::user()->name }}!
                    </h2>
                    <p class="text-muted mb-0">Panel de control del sistema de billar</p>
                </div>
                <div class="text-muted">
                    <i class="bi bi-calendar3 me-1"></i>
                    {{ now()->format('d/m/Y') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase fw-bold mb-1">Torneos Activos</h6>
                            <h2 class="mb-0">3</h2>
                        </div>
                        <div class="opacity-75">
                            <i class="bi bi-calendar-event" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase fw-bold mb-1">Jugadores</h6>
                            <h2 class="mb-0">42</h2>
                        </div>
                        <div class="opacity-75">
                            <i class="bi bi-people-fill" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase fw-bold mb-1">Partidas Hoy</h6>
                            <h2 class="mb-0">12</h2>
                        </div>
                        <div class="opacity-75">
                            <i class="bi bi-play-circle" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase fw-bold mb-1">Mesas Libres</h6>
                            <h2 class="mb-0">5</h2>
                        </div>
                        <div class="opacity-75">
                            <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row g-4">
        <!-- Quick Actions -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning-charge text-warning me-2"></i>
                        Acciones Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>
                            Nuevo Torneo
                        </button>
                        <button class="btn btn-success">
                            <i class="bi bi-person-plus me-2"></i>
                            Registrar Jugador
                        </button>
                        <button class="btn btn-warning">
                            <i class="bi bi-play me-2"></i>
                            Iniciar Partida
                        </button>
                        <button class="btn btn-info">
                            <i class="bi bi-bar-chart me-2"></i>
                            Ver Estadísticas
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history text-primary me-2"></i>
                        Actividad Reciente
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-success rounded-circle p-2 text-white">
                                    <i class="bi bi-trophy"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Torneo "Mayo Championship" finalizado</h6>
                                <p class="text-muted mb-0 small">
                                    <i class="bi bi-clock me-1"></i>
                                    Hace 2 horas
                                </p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-primary rounded-circle p-2 text-white">
                                    <i class="bi bi-person-plus"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Nuevo jugador registrado: Carlos Mendez</h6>
                                <p class="text-muted mb-0 small">
                                    <i class="bi bi-clock me-1"></i>
                                    Hace 4 horas
                                </p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-warning rounded-circle p-2 text-white">
                                    <i class="bi bi-play-circle"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Partida iniciada en Mesa 3</h6>
                                <p class="text-muted mb-0 small">
                                    <i class="bi bi-clock me-1"></i>
                                    Hace 6 horas
                                </p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-info rounded-circle p-2 text-white">
                                    <i class="bi bi-calendar-event"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Nuevo torneo programado para mañana</h6>
                                <p class="text-muted mb-0 small">
                                    <i class="bi bi-clock me-1"></i>
                                    Hace 8 horas
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('head')
<style>
    .timeline {
        position: relative;
    }
    
    .card-body .timeline::before {
        content: '';
        position: absolute;
        left: 20px;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: #dee2e6;
        z-index: 1;
    }
    
    .timeline > div:not(:last-child) {
        position: relative;
        z-index: 2;
    }
</style>
@endpush
@endsection