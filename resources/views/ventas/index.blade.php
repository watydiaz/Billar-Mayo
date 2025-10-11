@extends('layouts.app')

@section('title', 'Gestión de Ventas')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Tarjetas de estadísticas -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title" id="tituloVentas">Total Ventas</h6>
                                    <h3 class="mb-0" id="ventasHoy">${{ number_format($totales['total_ventas'] ?? 0, 2) }}</h3>
                                    <small id="cantidadHoy">{{ $totales['cantidad_ventas'] ?? 0 }} ventas</small>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-calendar-day" id="iconoVentas" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Promedio</h6>
                                    <h3 class="mb-0" id="promedioVenta">${{ number_format($totales['promedio_venta'] ?? 0, 2) }}</h3>
                                    <small>por venta</small>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-graph-up" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Subtotal</h6>
                                    <h3 class="mb-0" id="subtotalVentas">${{ number_format($totales['total_subtotal'] ?? 0, 2) }}</h3>
                                    <small>antes descuentos</small>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-cash-stack" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Descuentos</h6>
                                    <h3 class="mb-0" id="totalDescuentos">${{ number_format($totales['total_descuentos'] ?? 0, 2) }}</h3>
                                    <small>aplicados</small>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-percent" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros y tabla -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-receipt"></i> Historial de Ventas
                    </h4>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary" id="refreshData">
                            <i class="bi bi-arrow-clockwise"></i> Actualizar
                        </button>
                        <button class="btn btn-success" onclick="window.location.href='/pedidos'">
                            <i class="bi bi-plus-lg"></i> Nueva Venta
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filtros -->
                    <form id="filtrosForm">
                        <div class="row mb-4">
                            <!-- Filtros de fecha como botones -->
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Filtros rápidos de fecha</label>
                                <div class="btn-group-wrapper">
                                    <div class="btn-group flex-wrap" role="group" aria-label="Filtros de fecha">
                                        <input type="radio" class="btn-check" name="filtro_fecha" id="todasFechas" value="" checked>
                                        <label class="btn btn-outline-secondary" for="todasFechas">
                                            <i class="bi bi-calendar3 me-1"></i>Todas
                                        </label>

                                        <input type="radio" class="btn-check" name="filtro_fecha" id="hoy" value="hoy">
                                        <label class="btn btn-outline-primary" for="hoy">
                                            <i class="bi bi-calendar-day me-1"></i>Hoy
                                        </label>

                                        <input type="radio" class="btn-check" name="filtro_fecha" id="ayer" value="ayer">
                                        <label class="btn btn-outline-info" for="ayer">
                                            <i class="bi bi-calendar-minus me-1"></i>Ayer
                                        </label>

                                        <input type="radio" class="btn-check" name="filtro_fecha" id="semana" value="semana">
                                        <label class="btn btn-outline-success" for="semana">
                                            <i class="bi bi-calendar-week me-1"></i>Semana
                                        </label>

                                        <input type="radio" class="btn-check" name="filtro_fecha" id="mesActual" value="mes_actual">
                                        <label class="btn btn-outline-warning" for="mesActual">
                                            <i class="bi bi-calendar-month me-1"></i>Este Mes
                                        </label>

                                        <input type="radio" class="btn-check" name="filtro_fecha" id="mesAnterior" value="mes_anterior">
                                        <label class="btn btn-outline-secondary" for="mesAnterior">
                                            <i class="bi bi-calendar-x me-1"></i>Mes Anterior
                                        </label>

                                        <input type="radio" class="btn-check" name="filtro_fecha" id="mesPersonalizado" value="mes_personalizado">
                                        <label class="btn btn-outline-dark" for="mesPersonalizado">
                                            <i class="bi bi-calendar-range me-1"></i>Mes Custom
                                        </label>

                                        <input type="radio" class="btn-check" name="filtro_fecha" id="rangoPersonalizado" value="rango_personalizado">
                                        <label class="btn btn-outline-danger" for="rangoPersonalizado">
                                            <i class="bi bi-calendar2-range me-1"></i>Rango Custom
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Filtro de mes personalizado -->
                            <div class="col-lg-2 col-md-3 mb-3" id="filtroMesContainer" style="display: none;">
                                <label class="form-label">Mes</label>
                                <select class="form-select" id="mes" name="mes">
                                    <option value="1">Enero</option>
                                    <option value="2">Febrero</option>
                                    <option value="3">Marzo</option>
                                    <option value="4">Abril</option>
                                    <option value="5">Mayo</option>
                                    <option value="6">Junio</option>
                                    <option value="7">Julio</option>
                                    <option value="8">Agosto</option>
                                    <option value="9">Septiembre</option>
                                    <option value="10">Octubre</option>
                                    <option value="11">Noviembre</option>
                                    <option value="12">Diciembre</option>
                                </select>
                            </div>

                            <div class="col-lg-2 col-md-3 mb-3" id="filtroAnoContainer" style="display: none;">
                                <label class="form-label">Año</label>
                                <select class="form-select" id="año" name="año">
                                    @for($i = 2020; $i <= date('Y') + 1; $i++)
                                        <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>

                            <!-- Rango de fechas personalizado -->
                            <div class="col-lg-3 col-md-6 mb-3" id="filtroRangoContainer" style="display: none;">
                                <label class="form-label">Fecha inicio</label>
                                <input type="date" class="form-control" id="fechaInicio" name="fecha_inicio">
                            </div>

                            <div class="col-lg-3 col-md-6 mb-3" id="filtroRangoFinContainer" style="display: none;">
                                <label class="form-label">Fecha fin</label>
                                <input type="date" class="form-control" id="fechaFin" name="fecha_fin">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <!-- Búsqueda -->
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label class="form-label">Buscar venta</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="buscar" name="buscar" placeholder="Número de venta...">
                                    <button class="btn btn-outline-secondary" type="button" id="limpiarBusqueda">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Estado -->
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label class="form-label">Estado</label>
                                <select class="form-select" id="estado" name="estado">
                                    <option value="">Todos los estados</option>
                                    <option value="1">Completadas</option>
                                    <option value="0">Canceladas</option>
                                </select>
                            </div>

                            <!-- Tipo de pago -->
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label class="form-label">Tipo de pago</label>
                                <select class="form-select" id="tipoPago" name="tipo_pago">
                                    <option value="">Todos los tipos</option>
                                    <option value="efectivo">Efectivo</option>
                                    <option value="tarjeta">Tarjeta</option>
                                    <option value="transferencia">Transferencia</option>
                                </select>
                            </div>

                            <div class="col-lg-3 col-md-6 mb-3 d-flex align-items-end">
                                <button type="button" class="btn btn-primary me-2" id="aplicarFiltros">
                                    <i class="bi bi-funnel"></i> Filtrar
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="limpiarFiltros">
                                    <i class="bi bi-arrow-counterclockwise"></i> Limpiar
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Loading indicator -->
                    <div id="loadingIndicator" style="display: none;" class="text-center py-3">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>

                    <!-- Tabla de ventas -->
                    <div id="tablaVentas">
                        @include('ventas.table', ['ventas' => $ventas])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver detalles de venta -->
<div class="modal fade" id="detalleVentaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de Venta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalleVentaContent">
                <!-- El contenido se carga dinámicamente -->
            </div>
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
.btn-group-wrapper {
    overflow-x: auto;
}

.btn-group {
    gap: 0.25rem;
    flex-wrap: wrap;
}

.btn-group .btn {
    border-radius: 0.375rem !important;
    margin-bottom: 0.25rem;
    font-size: 0.875rem;
    padding: 0.5rem 0.75rem;
    white-space: nowrap;
}

.btn-group .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn-check:checked + .btn {
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(0,0,0,0.15);
}

@media (max-width: 768px) {
    .btn-group {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.5rem;
    }
    
    .btn-group .btn {
        margin-bottom: 0;
    }
}

@media (max-width: 576px) {
    .btn-group {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection

@section('scripts')
<script src="{{ asset('js/ventas.js') }}"></script>
@endsection