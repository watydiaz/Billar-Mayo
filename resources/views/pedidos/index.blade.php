@extends('layouts.app')

@section('title', 'Gestión de Rondas - Sistema Billar')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-clipboard-data text-primary me-2"></i>
                Gestión de Rondas
            </h1>
            <p class="text-muted">Sistema optimizado de rondas y productos</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoPedidoModal">
            <i class="bi bi-plus-circle me-1"></i>
            Nueva Ronda
        </button>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-clipboard-check display-6 me-3"></i>
                        <div>
                            <h6 class="card-title mb-0">Rondas Activas</h6>
                            <h4 class="mb-0">{{ $pedidos->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-table display-6 me-3"></i>
                        <div>
                            <h6 class="card-title mb-0">Mesas Disponibles</h6>
                            <h4 class="mb-0">{{ $mesas->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-box display-6 me-3"></i>
                        <div>
                            <h6 class="card-title mb-0">Productos</h6>
                            <h4 class="mb-0">{{ $productos->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Rondas -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-list me-2"></i>
                Rondas Activas
            </h5>
        </div>
        <div class="card-body">
            @if($pedidos->count() > 0)
                <div class="accordion" id="rondasAccordion">
                    @foreach($pedidos as $pedido)
                        <div class="accordion-item mb-3 border rounded shadow-sm">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#ronda{{ $pedido->id }}" 
                                        aria-expanded="false">
                                    <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person-circle text-primary me-3" style="font-size: 1.5rem;"></i>
                                            <div>
                                                <strong class="d-block">{{ $pedido->nombre_cliente }}</strong>
                                                <small class="text-muted">{{ $pedido->numero_pedido }} • {{ $pedido->created_at->format('d/m/Y H:i') }}</small>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-primary me-2">Activa</span>
                                            <strong class="text-success fs-5">${{ number_format($pedido->total_pedido, 0, ',', '.') }}</strong>
                                        </div>
                                    </div>
                                </button>
                            </h2>
                            <div id="ronda{{ $pedido->id }}" 
                                 class="accordion-collapse collapse" 
                                 data-bs-parent="#rondasAccordion" 
                                 data-ronda-id="{{ $pedido->id }}">
                                <div class="accordion-body" data-lazy-loaded="false">
                                    <div class="text-center p-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Cargando detalles...</span>
                                        </div>
                                        <p class="mt-2 mb-0 text-muted">Cargando información de la ronda...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-clipboard-x display-1 text-muted mb-3"></i>
                    <h4 class="text-muted mb-3">No hay rondas activas</h4>
                    <p class="text-muted mb-4">Crea una nueva ronda para comenzar a gestionar productos y servicios</p>
                    <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#nuevoPedidoModal">
                        <i class="bi bi-plus-circle me-2"></i>
                        Crear Primera Ronda
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Nuevo Pedido/Ronda -->
<div class="modal fade" id="nuevoPedidoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('pedidos.store') }}">
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
                        <label class="form-label">
                            <i class="bi bi-person me-1"></i>
                            Nombre del Cliente
                        </label>
                        <input type="text" name="nombre_cliente" class="form-control form-control-lg" 
                               placeholder="Ingresa el nombre del cliente" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-table me-1"></i>
                            Mesa (Opcional)
                        </label>
                        <select name="mesa_id" class="form-select">
                            <option value="">Seleccionar mesa...</option>
                            @foreach($mesas as $mesa)
                                <option value="{{ $mesa->id }}">{{ $mesa->numero }} - {{ $mesa->tipo }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>
                        Crear Ronda
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Agregar Producto -->
<div class="modal fade" id="agregarProductoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formAgregarProducto">
                @csrf
                <input type="hidden" id="ronda_id_producto">
                <input type="hidden" id="pedido_id_producto">
                
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="agregarProductoModalLabel">
                        <i class="bi bi-cart-plus me-2"></i>
                        Agregar Producto a Ronda
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-box me-1"></i>
                                    Producto
                                </label>
                                <select name="producto_id" id="producto_select" class="form-select form-select-lg" required>
                                    <option value="">Seleccionar producto...</option>
                                    @foreach($productos as $producto)
                                        <option value="{{ $producto->id }}" 
                                                data-precio="{{ $producto->precio }}"
                                                data-stock="{{ $producto->stock }}"
                                                data-es-servicio="{{ $producto->es_servicio }}">
                                            {{ $producto->nombre }} - ${{ number_format($producto->precio, 0, ',', '.') }}
                                            @if(!$producto->es_servicio)
                                                (Stock: {{ $producto->stock }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-hash me-1"></i>
                                    Cantidad
                                </label>
                                <input type="number" name="cantidad" id="cantidad" class="form-control form-control-lg" 
                                       value="1" min="1" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-currency-dollar me-1"></i>
                                    Precio Unit.
                                </label>
                                <input type="number" name="costo_unitario" id="costo_unitario" 
                                       class="form-control form-control-lg" 
                                       step="0.01" min="0" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="es_descuento">
                                <label class="form-check-label" for="es_descuento">
                                    <i class="bi bi-percent me-1"></i>
                                    Es un descuento
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div id="estadoCosto" class="alert alert-info mb-3" style="font-size: 0.9rem;">
                                <i class="bi bi-info-circle me-1"></i>
                                Selecciona un producto
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-chat-text me-1"></i>
                            Notas (Opcional)
                        </label>
                        <textarea name="notas" class="form-control" rows="2" 
                                  placeholder="Comentarios adicionales..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-success" id="btnAgregarProducto">
                        <i class="bi bi-cart-plus me-1"></i>
                        Agregar Producto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// 🚀 SISTEMA OPTIMIZADO DE RONDAS CON LAZY LOADING

document.addEventListener('DOMContentLoaded', function() {
    inicializarSistema();
});

function inicializarSistema() {
    // Lazy loading para acordeones
    configurarLazyLoading();
    
    // Formulario de productos
    configurarFormularioProductos();
    
    // Modal de productos
    configurarModalProductos();
}

// LAZY LOADING DE DETALLES
function configurarLazyLoading() {
    const accordions = document.querySelectorAll('[data-bs-toggle="collapse"]');
    
    accordions.forEach(button => {
        button.addEventListener('click', function() {
            const target = this.getAttribute('data-bs-target');
            const accordion = document.querySelector(target);
            const accordionBody = accordion?.querySelector('.accordion-body');
            
            if (accordionBody && accordionBody.getAttribute('data-lazy-loaded') === 'false') {
                const rondaId = accordion.getAttribute('data-ronda-id');
                if (rondaId) {
                    setTimeout(() => cargarDetallesRonda(rondaId, accordionBody), 300);
                }
            }
        });
    });
}

function cargarDetallesRonda(rondaId, container) {
    fetch(`/pedidos/${rondaId}/detalles`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                container.setAttribute('data-lazy-loaded', 'true');
                renderizarDetallesRonda(data.ronda, container);
            } else {
                container.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        No se pudieron cargar los detalles
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            container.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-x-circle me-2"></i>
                    Error de conexión
                </div>
            `;
        });
}

function renderizarDetallesRonda(ronda, container) {
    let html = `
        <div class="row">
            <div class="col-md-8">
                <h6 class="mb-3">
                    <i class="bi bi-box me-2"></i>
                    Productos de la Ronda
                </h6>
    `;
    
    if (ronda.detalles && ronda.detalles.length > 0) {
        html += `<div class="list-group">`;
        ronda.detalles.forEach(detalle => {
            html += `
                <div class="list-group-item d-flex justify-content-between align-items-center" data-detalle-id="${detalle.id}">
                    <div>
                        <strong>${detalle.nombre_producto}</strong>
                        <div class="text-muted small">
                            ${detalle.cantidad} × $${parseInt(detalle.precio_unitario).toLocaleString()}
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <strong class="text-success me-3">$${parseInt(detalle.subtotal).toLocaleString()}</strong>
                        <button class="btn btn-sm btn-outline-danger" onclick="eliminarDetalle(${detalle.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        });
        html += `</div>`;
    } else {
        html += `
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                No hay productos en esta ronda
            </div>
        `;
    }
    
    html += `
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h6 class="card-title mb-2">
                            <i class="bi bi-calculator me-2"></i>
                            Total
                        </h6>
                        <h3 class="mb-3">$${parseInt(ronda.total).toLocaleString()}</h3>
                        <button class="btn btn-light w-100" 
                                data-bs-toggle="modal" 
                                data-bs-target="#agregarProductoModal"
                                data-ronda-id="${ronda.id}"
                                data-pedido-id="${ronda.id}">
                            <i class="bi bi-plus-circle me-2"></i>
                            Agregar Producto
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.innerHTML = html;
    
    // Animar entrada
    container.style.opacity = '0';
    setTimeout(() => {
        container.style.transition = 'opacity 0.3s ease';
        container.style.opacity = '1';
    }, 50);
}

// CONFIGURACIÓN DEL MODAL DE PRODUCTOS
function configurarModalProductos() {
    const modal = document.getElementById('agregarProductoModal');
    
    modal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const rondaId = button.getAttribute('data-ronda-id');
        const pedidoId = button.getAttribute('data-pedido-id');
        
        document.getElementById('ronda_id_producto').value = rondaId;
        document.getElementById('pedido_id_producto').value = pedidoId;
        
        // Reset form
        document.getElementById('formAgregarProducto').reset();
        document.getElementById('costo_unitario').value = '';
        actualizarEstadoCosto();
    });
}

// CONFIGURACIÓN DEL FORMULARIO DE PRODUCTOS
function configurarFormularioProductos() {
    const productoSelect = document.getElementById('producto_select');
    const costoInput = document.getElementById('costo_unitario');
    const cantidadInput = document.getElementById('cantidad');
    
    productoSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const precio = selectedOption.getAttribute('data-precio');
        
        if (precio) {
            costoInput.value = precio;
            actualizarEstadoCosto();
        }
    });
    
    costoInput.addEventListener('input', actualizarEstadoCosto);
    cantidadInput.addEventListener('input', actualizarEstadoCosto);
    
    // Envío del formulario
    document.getElementById('formAgregarProducto').addEventListener('submit', function(e) {
        e.preventDefault();
        enviarProducto();
    });
}

function actualizarEstadoCosto() {
    const estadoCosto = document.getElementById('estadoCosto');
    const productoSelect = document.getElementById('producto_select');
    const costoInput = document.getElementById('costo_unitario');
    const cantidadInput = document.getElementById('cantidad');
    
    if (!productoSelect.value) {
        estadoCosto.className = 'alert alert-info mb-3';
        estadoCosto.innerHTML = '<i class="bi bi-info-circle me-1"></i>Selecciona un producto';
        return;
    }
    
    const selectedOption = productoSelect.options[productoSelect.selectedIndex];
    const precioOriginal = parseFloat(selectedOption.getAttribute('data-precio'));
    const precioActual = parseFloat(costoInput.value) || 0;
    const cantidad = parseInt(cantidadInput.value) || 1;
    
    if (precioActual === precioOriginal) {
        estadoCosto.className = 'alert alert-success mb-3';
        estadoCosto.innerHTML = '<i class="bi bi-check-circle me-1"></i>Precio del catálogo';
    } else if (precioActual > precioOriginal) {
        estadoCosto.className = 'alert alert-warning mb-3';
        estadoCosto.innerHTML = '<i class="bi bi-arrow-up me-1"></i>Precio aumentado';
    } else {
        estadoCosto.className = 'alert alert-info mb-3';
        estadoCosto.innerHTML = '<i class="bi bi-arrow-down me-1"></i>Precio con descuento';
    }
    
    if (cantidad > 1) {
        const total = precioActual * cantidad;
        estadoCosto.innerHTML += ` • Total: $${total.toLocaleString()}`;
    }
}

function enviarProducto() {
    const formData = new FormData(document.getElementById('formAgregarProducto'));
    const rondaId = document.getElementById('ronda_id_producto').value;
    const pedidoId = document.getElementById('pedido_id_producto').value;
    
    if (!formData.get('producto_id')) {
        mostrarNotificacion('Selecciona un producto', 'error');
        return;
    }
    
    const cantidad = parseInt(formData.get('cantidad'));
    const precioUnitario = parseFloat(document.getElementById('costo_unitario').value);
    
    if (!cantidad || cantidad <= 0) {
        mostrarNotificacion('La cantidad debe ser mayor a 0', 'error');
        return;
    }
    
    if (!precioUnitario || precioUnitario <= 0) {
        mostrarNotificacion('El precio debe ser mayor a 0', 'error');
        return;
    }
    
    const btnAgregar = document.getElementById('btnAgregarProducto');
    btnAgregar.disabled = true;
    btnAgregar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Agregando...';
    
    const datosProducto = {
        ronda_id: rondaId,
        producto_id: formData.get('producto_id'),
        cantidad: cantidad,
        costo_unitario: precioUnitario,
        es_descuento: document.getElementById('es_descuento').checked ? 1 : 0,
        notas: formData.get('notas') || null,
        _token: formData.get('_token')
    };
    
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
            bootstrap.Modal.getInstance(document.getElementById('agregarProductoModal')).hide();
            mostrarNotificacion('Producto agregado exitosamente', 'success');
            
            // Refrescar la ronda específica
            const accordion = document.querySelector(`[data-ronda-id="${rondaId}"]`);
            const accordionBody = accordion?.querySelector('.accordion-body');
            if (accordionBody) {
                accordionBody.setAttribute('data-lazy-loaded', 'false');
                cargarDetallesRonda(rondaId, accordionBody);
            }
            
            document.getElementById('formAgregarProducto').reset();
        } else {
            mostrarNotificacion(data.message || 'Error al agregar producto', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarNotificacion('Error de conexión', 'error');
    })
    .finally(() => {
        btnAgregar.disabled = false;
        btnAgregar.innerHTML = '<i class="bi bi-cart-plus me-1"></i>Agregar Producto';
    });
}

function eliminarDetalle(detalleId) {
    if (!confirm('¿Eliminar este producto?')) return;
    
    fetch(`/ronda-detalles/${detalleId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarNotificacion('Producto eliminado', 'success');
            
            const productoElement = document.querySelector(`[data-detalle-id="${detalleId}"]`);
            if (productoElement) {
                productoElement.style.transition = 'all 0.3s ease';
                productoElement.style.opacity = '0';
                setTimeout(() => productoElement.remove(), 300);
            }
        } else {
            mostrarNotificacion(data.message || 'Error al eliminar', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarNotificacion('Error de conexión', 'error');
    });
}

function mostrarNotificacion(mensaje, tipo = 'info') {
    const toast = document.createElement('div');
    const bgClass = tipo === 'success' ? 'bg-success' : tipo === 'error' ? 'bg-danger' : 'bg-info';
    
    toast.className = `toast align-items-center text-white ${bgClass} border-0 position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
    toast.setAttribute('role', 'alert');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <strong>${mensaje}</strong>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}
</script>
@endpush