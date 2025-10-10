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
                            <h6 class="text-uppercase fw-bold mb-1">Pedidos Activos</h6>
                            <h3 class="mb-0">{{ $pedidos->where('estado', '1')->count() }}</h3>
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
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-list-ul me-2"></i>
                        Pedidos Activos ({{ $pedidos->count() }})
                    </h5>
                    @if($pedidos->count() > 0)
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary" onclick="expandirTodos()">
                                <i class="bi bi-arrows-expand me-1"></i>
                                Expandir Todos
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="contraerTodos()">
                                <i class="bi bi-arrows-collapse me-1"></i>
                                Contraer Todos
                            </button>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    @if($pedidos->isEmpty())
                        <div class="text-center py-5">
                            <div class="bg-light rounded-circle mx-auto mb-4" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                            </div>
                            <h4 class="text-muted">No hay pedidos activos</h4>
                            <p class="text-muted mb-4">Crea un nuevo pedido para comenzar a gestionar las mesas</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoPedidoModal">
                                <i class="bi bi-plus-circle me-2"></i>Crear Primer Pedido
                            </button>
                        </div>
                    @else
                        <div class="accordion" id="pedidosAccordion">
                            @foreach($pedidos as $pedido)
                                <div class="accordion-item mb-3 border-0 shadow-sm rounded-3">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed rounded-3 bg-light border-0" type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#pedido{{ $pedido->id }}" 
                                                aria-expanded="false"
                                                style="box-shadow: none;">
                                            <div class="d-flex w-100 justify-content-between align-items-center me-3">
                                                <!-- Información del cliente -->
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-white rounded-circle p-2 me-3 shadow-sm">
                                                        <i class="bi bi-person-fill text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1 fw-bold text-dark">{{ $pedido->nombre_cliente }}</h6>
                                                        <small class="text-muted">
                                                            <i class="bi bi-hash me-1"></i>{{ $pedido->numero_pedido }}
                                                        </small>
                                                        <br>
                                                        <small class="text-muted">
                                                            <i class="bi bi-clock me-1"></i>{{ $pedido->created_at->format('d/m/Y H:i') }}
                                                        </small>
                                                    </div>
                                                </div>

                                                <!-- Mesa asignada -->
                                                <div class="text-center">
                                                    @if($pedido->mesa)
                                                        <div class="bg-success text-white rounded-pill px-3 py-2">
                                                            <i class="bi bi-table me-1"></i>
                                                            <strong>Mesa {{ $pedido->mesa->numero_mesa ?? $pedido->mesa->id }}</strong>
                                                        </div>
                                                    @else
                                                        <div class="bg-warning text-dark rounded-pill px-3 py-2">
                                                            <i class="bi bi-exclamation-triangle me-1"></i>
                                                            <strong>Sin mesa</strong>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Estado y tiempo -->
                                                <div class="text-center">
                                                    <span class="badge {{ $pedido->estado_badge }} px-3 py-2 mb-1">
                                                        {{ $pedido->estado_texto }}
                                                    </span>
                                                    @if($pedido->tiempo_inicio)
                                                        <div class="small">
                                                            <i class="bi bi-stopwatch text-info"></i>
                                                            <strong data-tiempo-inicio="{{ $pedido->tiempo_inicio }}">{{ $pedido->tiempo_transcurrido }} min</strong>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Total -->
                                                <div class="text-end">
                                                    <div class="bg-success text-white rounded-pill px-3 py-2">
                                                        <i class="bi bi-cash me-1"></i>
                                                        <strong>${{ number_format($pedido->total, 0, ',', '.') }}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="pedido{{ $pedido->id }}" class="accordion-collapse collapse">
                                        <div class="accordion-body bg-white rounded-bottom-3 border-top">
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
            <div class="modal-body text-center py-5">
                <p class="text-muted mb-4">¿Qué acción deseas realizar?</p>
                <button type="button" class="btn btn-primary btn-lg" 
                        data-bs-dismiss="modal" 
                        data-bs-toggle="modal" 
                        data-bs-target="#crearRondaModal">
                    <i class="bi bi-plus-circle me-2"></i>
                    Crear Ronda
                </button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear Ronda -->
<div class="modal fade" id="crearRondaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-2"></i>
                    Crear Nueva Ronda
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="crearRondaForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cliente_ronda" class="form-label">
                                    <i class="bi bi-person me-1"></i>
                                    Nombre del Cliente
                                </label>
                                <input type="text" class="form-control" id="cliente_ronda" 
                                       name="cliente_ronda" placeholder="Ingresa el nombre del cliente" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mesa_ronda" class="form-label">
                                    <i class="bi bi-table me-1"></i>
                                    Mesa Asignada
                                </label>
                                <select class="form-select" id="mesa_ronda" name="mesa_ronda">
                                    <option value="">Seleccionar mesa...</option>
                                    @foreach($mesas as $mesa)
                                        <option value="{{ $mesa->id }}">
                                            Mesa {{ $mesa->numero_mesa ?? $mesa->id }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-cart me-1"></i>
                                    Productos Disponibles
                                </label>
                                <div class="row">
                                    @foreach($productos as $producto)
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100 producto-card" style="cursor: pointer;" 
                                                 onclick="seleccionarProducto({{ $producto->id }}, '{{ $producto->nombre }}', {{ $producto->precio_venta }})">
                                                <div class="card-body text-center">
                                                    <i class="bi bi-cup text-primary mb-2" style="font-size: 2rem;"></i>
                                                    <h6 class="card-title">{{ $producto->nombre }}</h6>
                                                    <p class="card-text text-success fw-bold">
                                                        ${{ number_format($producto->precio_venta, 0, ',', '.') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="productosSeleccionados" style="display: none;">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Productos Seleccionados
                                </label>
                                <div id="listaProductos" class="border rounded p-3 bg-light">
                                    <!-- Productos seleccionados aparecerán aquí -->
                                </div>
                                <div class="text-end mt-2">
                                    <strong>Total: $<span id="totalRonda">0</span></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-primary" onclick="crearRonda()">
                    <i class="bi bi-check me-1"></i>
                    Crear Ronda
                </button>
            </div>
        </div>
    </div>
</div>

@push('head')
<style>
    .accordion-button:not(.collapsed) {
        background-color: #f8f9fa !important;
        border-bottom: 1px solid #dee2e6;
        box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .125);
    }
    
    .accordion-button:focus {
        box-shadow: none !important;
        border-color: transparent;
    }
    
    .accordion-button {
        transition: all 0.3s ease;
    }
    
    .accordion-button:hover {
        background-color: #e9ecef !important;
    }
    
    .accordion-item {
        transition: all 0.3s ease;
    }
    
    .accordion-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
    }
    
    .badge {
        font-size: 0.75rem;
    }
    
    .producto-card {
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    
    .producto-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border-color: #0d6efd;
    }
    
    .producto-card:active {
        transform: scale(0.98);
    }
</style>
@endpush

@push('scripts')
<script>
function expandirTodos() {
    const acordeones = document.querySelectorAll('.accordion-collapse');
    const botones = document.querySelectorAll('.accordion-button');
    
    acordeones.forEach(acordeon => {
        acordeon.classList.add('show');
    });
    
    botones.forEach(boton => {
        boton.classList.remove('collapsed');
        boton.setAttribute('aria-expanded', 'true');
    });
}

function contraerTodos() {
    const acordeones = document.querySelectorAll('.accordion-collapse');
    const botones = document.querySelectorAll('.accordion-button');
    
    acordeones.forEach(acordeon => {
        acordeon.classList.remove('show');
    });
    
    botones.forEach(boton => {
        boton.classList.add('collapsed');
        boton.setAttribute('aria-expanded', 'false');
    });
}

// Variables para manejar productos seleccionados
let productosSeleccionados = [];
let totalRonda = 0;

// Función para seleccionar productos
function seleccionarProducto(id, nombre, precio) {
    const productoExistente = productosSeleccionados.find(p => p.id === id);
    
    if (productoExistente) {
        productoExistente.cantidad++;
    } else {
        productosSeleccionados.push({
            id: id,
            nombre: nombre,
            precio: precio,
            cantidad: 1
        });
    }
    
    actualizarListaProductos();
}

// Función para actualizar la lista de productos seleccionados
function actualizarListaProductos() {
    const contenedor = document.getElementById('listaProductos');
    const seccionProductos = document.getElementById('productosSeleccionados');
    
    if (productosSeleccionados.length === 0) {
        seccionProductos.style.display = 'none';
        return;
    }
    
    seccionProductos.style.display = 'block';
    
    let html = '';
    totalRonda = 0;
    
    productosSeleccionados.forEach((producto, index) => {
        const subtotal = producto.precio * producto.cantidad;
        totalRonda += subtotal;
        
        html += `
            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-white rounded">
                <div>
                    <strong>${producto.nombre}</strong>
                    <small class="text-muted">($${producto.precio.toLocaleString()})</small>
                </div>
                <div class="d-flex align-items-center">
                    <button type="button" class="btn btn-sm btn-outline-secondary me-2" 
                            onclick="cambiarCantidad(${index}, -1)">-</button>
                    <span class="mx-2"><strong>${producto.cantidad}</strong></span>
                    <button type="button" class="btn btn-sm btn-outline-secondary me-2" 
                            onclick="cambiarCantidad(${index}, 1)">+</button>
                    <span class="text-success fw-bold me-2">$${subtotal.toLocaleString()}</span>
                    <button type="button" class="btn btn-sm btn-outline-danger" 
                            onclick="eliminarProducto(${index})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
    });
    
    contenedor.innerHTML = html;
    document.getElementById('totalRonda').textContent = totalRonda.toLocaleString();
}

// Función para cambiar cantidad
function cambiarCantidad(index, cambio) {
    productosSeleccionados[index].cantidad += cambio;
    
    if (productosSeleccionados[index].cantidad <= 0) {
        productosSeleccionados.splice(index, 1);
    }
    
    actualizarListaProductos();
}

// Función para eliminar producto
function eliminarProducto(index) {
    productosSeleccionados.splice(index, 1);
    actualizarListaProductos();
}

// Función para crear la ronda
function crearRonda() {
    const cliente = document.getElementById('cliente_ronda').value;
    const mesa = document.getElementById('mesa_ronda').value;
    
    if (!cliente.trim()) {
        alert('Por favor ingresa el nombre del cliente');
        return;
    }
    
    if (productosSeleccionados.length === 0) {
        alert('Por favor selecciona al menos un producto');
        return;
    }
    
    // Aquí iría la lógica para enviar los datos al servidor
    console.log('Datos de la ronda:', {
        cliente: cliente,
        mesa: mesa,
        productos: productosSeleccionados,
        total: totalRonda
    });
    
    // Por ahora solo mostramos un mensaje de éxito
    alert(`Ronda creada exitosamente para ${cliente}. Total: $${totalRonda.toLocaleString()}`);
    
    // Limpiar formulario y cerrar modal
    document.getElementById('crearRondaForm').reset();
    productosSeleccionados = [];
    totalRonda = 0;
    actualizarListaProductos();
    
    // Cerrar modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('crearRondaModal'));
    modal.hide();
}

// Auto-refresh cada 30 segundos para actualizar tiempos
setInterval(function() {
    const tiempos = document.querySelectorAll('[data-tiempo-inicio]');
    tiempos.forEach(tiempo => {
        const inicio = new Date(tiempo.dataset.tiempoInicio);
        const ahora = new Date();
        const transcurrido = Math.floor((ahora - inicio) / 1000 / 60);
        tiempo.textContent = transcurrido + ' min';
    });
}, 30000);
</script>
@endpush

@endsection