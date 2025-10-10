@extends('layouts.app')

@section('title', 'Gestión de Pedidos')

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
                        Gestión de Pedidos
                    </h2>
                    <p class="text-muted mb-0">Control de pedidos y alquileres de mesas</p>
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
                            <h6 class="text-uppercase fw-bold mb-1">Pedidos Abiertos</h6>
                            <h3 class="mb-0">{{ $pedidos->where('estado', 'abierto')->count() }}</h3>
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
                            <h6 class="text-uppercase fw-bold mb-1">En Mesa</h6>
                            <h3 class="mb-0">{{ $pedidos->where('estado', 'en_mesa')->count() }}</h3>
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
                            <h6 class="text-uppercase fw-bold mb-1">Total Rondas</h6>
                            <h3 class="mb-0">{{ $pedidos->sum(function($p) { return $p->rondas->count(); }) }}</h3>
                        </div>
                        <i class="bi bi-cup-hot" style="font-size: 2rem; opacity: 0.7;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase fw-bold mb-1">Ingresos</h6>
                            <h3 class="mb-0">${{ number_format($pedidos->sum('total'), 0, ',', '.') }}</h3>
                        </div>
                        <i class="bi bi-currency-dollar" style="font-size: 2rem; opacity: 0.7;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pedidos Activos en Acordeones -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-list-ul me-2"></i>
                        Pedidos Activos
                    </h5>
                </div>
                <div class="card-body">
                    @if($pedidos->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <h4 class="text-muted mt-3">No hay pedidos activos</h4>
                            <p class="text-muted">Crea un nuevo pedido para comenzar</p>
                        </div>
                    @else
                        <div class="accordion" id="pedidosAccordion">
                            @foreach($pedidos as $pedido)
                                <div class="accordion-item mb-3 border rounded">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#pedido{{ $pedido->id }}" 
                                                aria-expanded="false">
                                            <div class="d-flex w-100 justify-content-between align-items-center me-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-person-circle me-3 text-primary"></i>
                                                    <div>
                                                        <h6 class="mb-1 fw-bold">{{ $pedido->nombre_cliente }}</h6>
                                                        <small class="text-muted">
                                                            @if($pedido->mesa)
                                                                Mesa {{ $pedido->mesa->numero }} - {{ $pedido->mesa->nombre }}
                                                            @else
                                                                Sin mesa asignada
                                                            @endif
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center gap-3">
                                                    <span class="badge {{ $pedido->estado_badge }} px-3 py-2">
                                                        {{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}
                                                    </span>
                                                    @if($pedido->tiempo_inicio)
                                                        <div class="text-end">
                                                            <small class="text-muted d-block">Tiempo transcurrido</small>
                                                            <span class="fw-bold">{{ $pedido->tiempo_transcurrido }} min</span>
                                                        </div>
                                                    @endif
                                                    <div class="text-end">
                                                        <small class="text-muted d-block">Total</small>
                                                        <span class="fw-bold text-success">${{ number_format($pedido->total, 0, ',', '.') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="pedido{{ $pedido->id }}" class="accordion-collapse collapse">
                                        <div class="accordion-body">
                                            @include('pedidos.detalle', ['pedido' => $pedido])
                                        </div>
                                    </div>
                                </div>
                            @endforeach
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
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-2"></i>
                    Nuevo Pedido
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('pedidos.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nombre_cliente" class="form-label">
                            <i class="bi bi-person me-1"></i>
                            Nombre del Cliente
                        </label>
                        <input type="text" class="form-control" id="nombre_cliente" 
                               name="nombre_cliente" required>
                    </div>
                    <div class="mb-3">
                        <label for="mesa_id" class="form-label">
                            <i class="bi bi-grid-3x3-gap me-1"></i>
                            Mesa (Opcional)
                        </label>
                        <select class="form-select" id="mesa_id" name="mesa_id">
                            <option value="">Sin mesa asignada</option>
                            @foreach($mesas->where('estado', 'disponible') as $mesa)
                                <option value="{{ $mesa->id }}">
                                    Mesa {{ $mesa->numero }} - {{ $mesa->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check me-1"></i>
                        Crear Pedido
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('head')
<style>
    .accordion-button:not(.collapsed) {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    
    .accordion-button:focus {
        box-shadow: none;
    }
    
    .badge {
        font-size: 0.75rem;
    }
</style>
@endpush
@endsection