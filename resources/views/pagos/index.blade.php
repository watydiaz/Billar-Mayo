@extends('layouts.app')

@section('title', 'Sistema de Pagos - Rondas')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-warning bg-opacity-25 rounded">
                <h1 class="h3 mb-0">
                    <i class="fas fa-cash-register text-warning me-2"></i>
                    Sistema de Pagos
                </h1>
                <button type="button" class="btn btn-warning" onclick="cargarResumenGeneral()">
                    <i class="fas fa-sync-alt me-1"></i>
                    Actualizar
                </button>
            </div>
        </div>
    </div>

    <!-- Resumen de clientes con cuentas activas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>
                        Clientes con Rondas Activas
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div id="clientes-container">
                        <div class="text-center p-4">
                            <div class="spinner-border text-info" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="mt-2 text-muted">Cargando información de clientes...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modales -->
    <!-- Modal para pago individual de ronda -->
    <div class="modal fade" id="modalPagoRonda" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">
                        <i class="fas fa-receipt me-2"></i>
                        Pago Individual - Ronda
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="detalle-ronda-individual"></div>
                    
                    <form id="formPagoRonda">
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label">Tipo de Pago</label>
                                <select name="tipo_pago" class="form-select" required>
                                    <option value="efectivo">Efectivo</option>
                                    <option value="tarjeta">Tarjeta</option>
                                    <option value="transferencia">Transferencia</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Descuento (opcional)</label>
                                <input type="number" name="descuento" class="form-control" min="0" step="0.01" placeholder="0.00">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-warning bg-opacity-25">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-warning" onclick="procesarPagoRonda()">
                        <i class="fas fa-cash-register me-1"></i>
                        Procesar Pago
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para cerrar cuenta completa -->
    <div class="modal fade" id="modalCuentaCompleta" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-file-invoice-dollar me-2"></i>
                        Cerrar Cuenta Completa
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="detalle-cuenta-completa"></div>
                    
                    <form id="formCuentaCompleta">
                        <input type="hidden" name="cliente" id="clienteCuentaCompleta">
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label">Tipo de Pago</label>
                                <select name="tipo_pago" class="form-select" required>
                                    <option value="efectivo">Efectivo</option>
                                    <option value="tarjeta">Tarjeta</option>
                                    <option value="transferencia">Transferencia</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Descuento Total (opcional)</label>
                                <input type="number" name="descuento" class="form-control" min="0" step="0.01" placeholder="0.00">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="procesarCuentaCompleta()">
                        <i class="fas fa-check-circle me-1"></i>
                        Cerrar Cuenta
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let rondaSeleccionada = null;
let clienteSeleccionado = null;

// Cargar resumen general al inicio
$(document).ready(function() {
    cargarResumenGeneral();
});

// Función para cargar resumen general
function cargarResumenGeneral() {
    $.get('/rondas/resumen-ventas')
        .done(function(response) {
            if (response.success && response.clientes) {
                mostrarClientesActivos(response.clientes);
            } else {
                mostrarSinClientes();
            }
        })
        .fail(function() {
            mostrarError('Error al cargar información de clientes');
        });
}

// Mostrar clientes con rondas activas
function mostrarClientesActivos(clientes) {
    let html = '';
    
    if (clientes.length === 0) {
        mostrarSinClientes();
        return;
    }

    clientes.forEach(function(cliente) {
        html += `
            <div class="border-bottom p-3">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <h6 class="mb-1">
                            <i class="fas fa-user me-2"></i>
                            ${cliente.cliente}
                        </h6>
                        <small class="text-muted">${cliente.total_rondas} ronda(s) activa(s)</small>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="text-primary fw-bold">Productos</div>
                            <div class="h5 mb-0">$${cliente.subtotal_productos.toFixed(2)}</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-center">
                            <div class="text-warning fw-bold">Tiempo</div>
                            <div class="h5 mb-0">$${cliente.tiempo_mesa_total.toFixed(2)}</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-center">
                            <div class="text-success fw-bold">Total</div>
                            <div class="h4 mb-0">$${cliente.total_general.toFixed(2)}</div>
                        </div>
                    </div>
                    <div class="col-md-2 text-end">
                        <button type="button" class="btn btn-success" 
                                onclick="abrirCuentaCompleta('${cliente.cliente}')"
                                title="Cerrar todas las rondas del cliente">
                            <i class="fas fa-file-invoice-dollar me-2"></i>
                            <div>Cerrar</div>
                            <div>Cuenta Completa</div>
                        </button>
                    </div>
                </div>
                
                <!-- Detalle de rondas -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ronda</th>
                                        <th>Mesa</th>
                                        <th>Productos</th>
                                        <th>Tiempo</th>
                                        <th>Total Ronda</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>`;

        cliente.rondas.forEach(function(ronda) {
            const totalRonda = ronda.subtotal + ronda.tiempo_costo;
            html += `
                                    <tr>
                                        <td><span class="badge bg-primary">#${ronda.numero_ronda}</span></td>
                                        <td>
                                            ${ronda.mesa ? 
                                                `<i class="fas fa-table text-success me-1"></i>${ronda.mesa}` : 
                                                '<i class="fas fa-times text-muted me-1"></i>Sin mesa'
                                            }
                                        </td>
                                        <td><span class="text-primary fw-bold">$${ronda.subtotal.toFixed(2)}</span></td>
                                        <td>
                                            ${ronda.tiempo_costo > 0 ? 
                                                `<span class="text-warning fw-bold">$${ronda.tiempo_costo.toFixed(2)}</span> <small class="text-muted">(${ronda.tiempo_minutos || 0} min)</small>` : 
                                                '<span class="text-muted">Sin tiempo</span>'
                                            }
                                        </td>
                                        <td><span class="fw-bold text-success">$${totalRonda.toFixed(2)}</span></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-warning" 
                                                    onclick="abrirPagoRonda(${ronda.id})" 
                                                    title="Pagar esta ronda únicamente">
                                                <i class="fas fa-credit-card me-1"></i>
                                                Pagar Ronda
                                            </button>
                                        </td>
                                    </tr>`;
        });

        html += `
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>`;
    });

    $('#clientes-container').html(html);
}

// Mostrar mensaje cuando no hay clientes activos
function mostrarSinClientes() {
    $('#clientes-container').html(`
        <div class="text-center p-5">
            <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
            <h4 class="mt-3">No hay rondas activas</h4>
            <p class="text-muted">Todos los clientes han pagado sus cuentas</p>
        </div>
    `);
}

// Abrir modal para pago individual de ronda
function abrirPagoRonda(rondaId) {
    rondaSeleccionada = rondaId;
    
    // Para pago individual, mostrar directamente el modal con información básica
    mostrarDetalleRondaSimple(rondaId);
    $('#modalPagoRonda').modal('show');
}

// Abrir modal para cuenta completa
function abrirCuentaCompleta(cliente) {
    clienteSeleccionado = cliente;
    $('#clienteCuentaCompleta').val(cliente);
    
    // Cargar detalles del cliente
    $.get(`/rondas/resumen-ventas/${cliente}`)
        .done(function(response) {
            if (response.success) {
                mostrarDetalleCuentaCompleta(response.resumen);
                $('#modalCuentaCompleta').modal('show');
            }
        })
        .fail(function() {
            mostrarError('Error al cargar detalles del cliente');
        });
}

// Procesar pago individual de ronda
function procesarPagoRonda() {
    if (!rondaSeleccionada) return;
    
    const formData = new FormData(document.getElementById('formPagoRonda'));
    
    $.ajax({
        url: `/rondas/${rondaSeleccionada}/pagar-ronda`,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .done(function(response) {
        if (response.success) {
            $('#modalPagoRonda').modal('hide');
            mostrarExito(`Ronda pagada exitosamente. Venta: ${response.numero_venta}`);
            cargarResumenGeneral();
        } else {
            mostrarError(response.message);
        }
    })
    .fail(function() {
        mostrarError('Error al procesar el pago');
    });
}

// Procesar cierre de cuenta completa
function procesarCuentaCompleta() {
    if (!clienteSeleccionado) return;
    
    const formData = new FormData(document.getElementById('formCuentaCompleta'));
    
    $.ajax({
        url: '/rondas/cerrar-cuenta-completa',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .done(function(response) {
        if (response.success) {
            $('#modalCuentaCompleta').modal('hide');
            mostrarExito(`Cuenta cerrada exitosamente. Venta: ${response.numero_venta} - Total: $${response.total}`);
            cargarResumenGeneral();
        } else {
            mostrarError(response.message);
        }
    })
    .fail(function() {
        mostrarError('Error al cerrar la cuenta');
    });
}

// Funciones auxiliares para mostrar detalles
function mostrarDetalleRondaSimple(rondaId) {
    // Buscar la ronda en los datos ya cargados
    let rondaEncontrada = null;
    let clienteEncontrado = null;
    
    // Buscar en los datos del resumen general actual
    $('.border-bottom').each(function() {
        $(this).find('tr').each(function() {
            const botonPagar = $(this).find('button[onclick*="abrirPagoRonda"]');
            if (botonPagar.length > 0) {
                const onclick = botonPagar.attr('onclick');
                const id = onclick.match(/\d+/);
                if (id && parseInt(id[0]) === rondaId) {
                    const celdas = $(this).find('td');
                    rondaEncontrada = {
                        numero: celdas.eq(0).text(),
                        mesa: celdas.eq(1).text(),
                        productos: celdas.eq(2).text(),
                        tiempo: celdas.eq(3).text(),
                        total: celdas.eq(4).text()
                    };
                    clienteEncontrado = $(this).closest('.border-bottom').find('h6').text().replace('👤', '').trim();
                    return false;
                }
            }
        });
        if (rondaEncontrada) return false;
    });
    
    if (rondaEncontrada) {
        const html = `
            <div class="row">
                <div class="col-md-8">
                    <h6>Cliente: ${clienteEncontrado}</h6>
                    <p class="text-muted">Ronda: ${rondaEncontrada.numero}</p>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <tbody>
                                <tr><td><strong>Mesa:</strong></td><td>${rondaEncontrada.mesa}</td></tr>
                                <tr><td><strong>Productos:</strong></td><td>${rondaEncontrada.productos}</td></tr>
                                <tr><td><strong>Tiempo de mesa:</strong></td><td>${rondaEncontrada.tiempo}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="card bg-warning bg-opacity-10">
                        <div class="card-body py-3">
                            <div class="text-center">
                                <div class="h4 mb-0 text-warning">Total Ronda</div>
                                <div class="h3 fw-bold text-dark">${rondaEncontrada.total}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        $('#detalle-ronda-individual').html(html);
    }
}

function mostrarDetalleCuentaCompleta(resumen) {
    let html = `
        <div class="row mb-3">
            <div class="col-md-8">
                <h6><i class="fas fa-user me-2"></i>${resumen.cliente}</h6>
                <p class="text-muted">${resumen.total_rondas} ronda(s) activa(s)</p>
            </div>
            <div class="col-md-4 text-end">
                <div class="card bg-success bg-opacity-10">
                    <div class="card-body py-2">
                        <div class="small">Productos: $${resumen.subtotal_productos.toFixed(2)}</div>
                        <div class="small">Tiempo: $${resumen.tiempo_mesa_total.toFixed(2)}</div>
                        <hr class="my-1">
                        <div class="fw-bold h5 mb-0 text-success">Total: $${resumen.total_general.toFixed(2)}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-sm table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Ronda</th>
                        <th>Mesa</th>
                        <th>Productos</th>
                        <th>Tiempo</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>`;
    
    resumen.rondas.forEach(function(ronda) {
        const totalRonda = ronda.subtotal + ronda.tiempo_costo;
        html += `
                    <tr>
                        <td>#${ronda.numero_ronda}</td>
                        <td>${ronda.mesa || 'Sin mesa'}</td>
                        <td>$${ronda.subtotal.toFixed(2)}</td>
                        <td>${ronda.tiempo_costo > 0 ? 
                            `$${ronda.tiempo_costo.toFixed(2)} (${ronda.tiempo_minutos || 0} min)` : 
                            'Sin tiempo'}</td>
                        <td class="text-end fw-bold">$${totalRonda.toFixed(2)}</td>
                    </tr>`;
    });
    
    html += `
                </tbody>
                <tfoot class="table-light">
                    <tr class="fw-bold">
                        <td colspan="4" class="text-end">TOTAL CUENTA:</td>
                        <td class="text-end h5 text-success">$${resumen.total_general.toFixed(2)}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    `;
    
    $('#detalle-cuenta-completa').html(html);
}

// Funciones de notificación
function mostrarExito(mensaje) {
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: mensaje,
        timer: 3000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
}

function mostrarError(mensaje) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: mensaje,
        confirmButtonText: 'Entendido',
        confirmButtonColor: '#dc3545'
    });
}
</script>
@endpush

@endsection