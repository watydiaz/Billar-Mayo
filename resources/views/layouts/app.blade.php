<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Inicio')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    @vite(['resources/css/app.css'])
    
    <!-- Page Specific Styles -->
    @yield('styles')
    
    <!-- Additional Head Content -->
    @stack('head')
</head>
<body>
    <!-- Navigation -->
    @include('layouts.navbar')

    <!-- Main Content -->
    <main class="@yield('main-class', 'container-fluid')">
        @yield('content')
    </main>

    <!-- Footer -->
    @include('layouts.footer')

    <!-- Botón Flotante Rápido -->
    <div class="floating-quick-actions">
        <!-- Botón Principal -->
        <button class="btn btn-primary btn-floating-main" id="quickActionBtn" data-bs-toggle="dropdown" aria-expanded="false" title="Acciones Rápidas">
            <i class="bi bi-plus-lg"></i>
            <span class="badge badge-notification">2</span>
        </button>
        
        <!-- Menú Desplegable -->
        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-top floating-menu" aria-labelledby="quickActionBtn">
            <li>
                <a class="dropdown-item" href="{{ route('pedidos.index') }}#nuevo-pedido" onclick="crearNuevoPedido()">
                    <i class="bi bi-file-earmark-plus text-primary me-2"></i>
                    Crear Pedido
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="#" onclick="abrirVentaRapida()">
                    <i class="bi bi-lightning-charge text-warning me-2"></i>
                    Venta Rápida
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item" href="{{ route('ventas.index') }}">
                    <i class="bi bi-graph-up text-success me-2"></i>
                    Ver Ventas
                </a>
            </li>
        </ul>
    </div>

    <!-- Modal Venta Rápida de Mostrador -->
    <div class="modal fade" id="ventaRapidaModal" tabindex="-1" aria-labelledby="ventaRapidaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="ventaRapidaModalLabel">
                        <i class="bi bi-lightning-charge me-2"></i>
                        Venta Rápida de Mostrador
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Panel Izquierdo - Búsqueda y Productos -->
                        <div class="col-md-7">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="bi bi-search me-2"></i>
                                        Buscar Productos
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <!-- Buscador -->
                                    <div class="mb-3">
                                        <input type="text" 
                                               class="form-control form-control-lg" 
                                               id="buscarProductoVenta"
                                               placeholder="Buscar producto por nombre..." 
                                               autocomplete="off">
                                    </div>
                                    
                                    <!-- Productos disponibles -->
                                    <div class="productos-grid" id="productosDisponibles" style="max-height: 400px; overflow-y: auto;">
                                        <!-- Los productos se cargarán aquí dinámicamente -->
                                        <div class="text-center text-muted py-5">
                                            <i class="bi bi-box-seam display-1"></i>
                                            <p>Cargando productos...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Panel Derecho - Carrito -->
                        <div class="col-md-5">
                            <div class="card h-100">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">
                                        <i class="bi bi-cart3 me-2"></i>
                                        Carrito de Venta (<span id="cantidadItems">0</span> items)
                                    </h6>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <!-- Items del carrito -->
                                    <div class="carrito-items flex-grow-1" id="carritoItems" style="max-height: 300px; overflow-y: auto;">
                                        <div class="text-center text-muted py-5" id="carritoVacio">
                                            <i class="bi bi-cart-x display-2"></i>
                                            <p>Carrito vacío</p>
                                            <small>Agrega productos para comenzar</small>
                                        </div>
                                    </div>

                                    <!-- Total y acciones -->
                                    <div class="border-top pt-3 mt-3">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <strong class="h4">Total:</strong>
                                            <strong class="h4 text-success" id="totalVenta">$0</strong>
                                        </div>
                                        
                                        <div class="d-grid gap-2">
                                            <button type="button" class="btn btn-success btn-lg" id="procesarVenta" disabled>
                                                <i class="bi bi-check-circle me-2"></i>
                                                Procesar Venta
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" id="limpiarCarrito">
                                                <i class="bi bi-trash me-2"></i>
                                                Limpiar Carrito
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom JS -->
    @vite(['resources/js/app.js'])
    
    <!-- Additional Scripts -->
    @stack('scripts')
    
    <!-- Quick Actions JavaScript -->
    <script>
    // Funciones del botón flotante
    function crearNuevoPedido() {
        // Si estamos en la página de pedidos, activar el modal
        if (window.location.pathname.includes('/pedidos')) {
            const modal = document.getElementById('nuevoPedidoModal');
            if (modal) {
                // Cerrar el dropdown primero
                const dropdown = bootstrap.Dropdown.getInstance(document.getElementById('quickActionBtn'));
                if (dropdown) dropdown.hide();
                
                // Mostrar modal después de un breve delay para suavizar la transición
                setTimeout(() => {
                    const bootstrapModal = new bootstrap.Modal(modal);
                    bootstrapModal.show();
                    
                    // Enfocar el primer campo del formulario
                    setTimeout(() => {
                        const firstInput = modal.querySelector('input[type="text"]');
                        if (firstInput) firstInput.focus();
                    }, 300);
                }, 150);
            }
        } else {
            // Redirigir a la página de pedidos con parámetro para abrir modal
            window.location.href = '{{ route("pedidos.index") }}?nuevo=1';
        }
    }
    
    // Sistema de Venta Rápida
    let carritoVenta = [];
    let productosVenta = [];

    function abrirVentaRapida() {
        // Cerrar dropdown
        const dropdown = bootstrap.Dropdown.getInstance(document.getElementById('quickActionBtn'));
        if (dropdown) dropdown.hide();
        
        // Mostrar modal
        const modal = new bootstrap.Modal(document.getElementById('ventaRapidaModal'));
        modal.show();
        
        // Cargar productos
        cargarProductosVenta();
        
        // Limpiar carrito al abrir
        limpiarCarritoVenta();
    }

    function cargarProductosVenta() {
        // Mostrar loading
        document.getElementById('productosDisponibles').innerHTML = `
            <div class="text-center text-muted py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p>Cargando productos...</p>
            </div>
        `;

        // Cargar productos desde el backend
        fetch('/venta-rapida/productos')
            .then(response => response.json())
            .then(productos => {
                productosVenta = productos;
                mostrarProductosVenta(productosVenta);
            })
            .catch(error => {
                console.error('Error cargando productos:', error);
                // Mostrar productos de ejemplo si falla la API
                productosVenta = [
                    {id: 1, nombre: 'Aguila 330ml', precio: 3500, categoria: 'Cervezas'},
                    {id: 2, nombre: 'Club Colombia 330ml', precio: 4000, categoria: 'Cervezas'},
                    {id: 3, nombre: 'Corona 355ml', precio: 5500, categoria: 'Cervezas'},
                    {id: 4, nombre: 'Heineken 330ml', precio: 6000, categoria: 'Cervezas'},
                    {id: 5, nombre: 'Gatorade 500ml', precio: 2500, categoria: 'Bebidas'},
                    {id: 6, nombre: 'Coca Cola 350ml', precio: 2000, categoria: 'Bebidas'},
                    {id: 7, nombre: 'Agua 500ml', precio: 1500, categoria: 'Bebidas'},
                    {id: 8, nombre: 'Papas Margarita', precio: 3000, categoria: 'Snacks'},
                    {id: 9, nombre: 'Maní Japonés', precio: 2500, categoria: 'Snacks'},
                    {id: 10, nombre: 'Doritos', precio: 3500, categoria: 'Snacks'}
                ];
                mostrarProductosVenta(productosVenta);
            });
    }

    function mostrarProductosVenta(productos) {
        const container = document.getElementById('productosDisponibles');
        
        if (productos.length === 0) {
            container.innerHTML = `
                <div class="text-center text-muted py-5">
                    <i class="bi bi-search display-1"></i>
                    <p>No se encontraron productos</p>
                </div>
            `;
            return;
        }

        const productosHtml = productos.map(producto => {
            const stockBajo = producto.stock <= 5;
            const stockClass = stockBajo ? 'text-warning' : 'text-muted';
            const stockIcon = stockBajo ? 'bi-exclamation-triangle' : 'bi-box';
            
            return `
            <div class="producto-card ${producto.stock === 0 ? 'producto-agotado' : ''}" onclick="agregarAlCarrito(${producto.id})" data-stock="${producto.stock}">
                <div class="producto-categoria">${producto.categoria}</div>
                <h6 class="producto-nombre my-2">${producto.nombre}</h6>
                <div class="producto-precio">$${producto.precio.toLocaleString()}</div>
                <div class="producto-stock ${stockClass} mt-1">
                    <i class="bi ${stockIcon} me-1"></i>
                    Stock: ${producto.stock}
                </div>
                <button class="btn btn-sm btn-outline-primary mt-2 w-100" ${producto.stock === 0 ? 'disabled' : ''}>
                    <i class="bi bi-plus-circle me-1"></i>
                    ${producto.stock === 0 ? 'Agotado' : 'Agregar'}
                </button>
            </div>
        `;
        }).join('');

        container.innerHTML = `<div class="productos-grid">${productosHtml}</div>`;
    }

    function agregarAlCarrito(productoId) {
        const producto = productosVenta.find(p => p.id === productoId);
        if (!producto) return;

        // Verificar stock disponible
        if (producto.stock <= 0) {
            alert(`❌ Producto agotado: ${producto.nombre}\n\nStock disponible: 0`);
            return;
        }

        const itemExistente = carritoVenta.find(item => item.id === productoId);
        
        if (itemExistente) {
            // Verificar que no exceda el stock disponible
            if (itemExistente.cantidad >= producto.stock) {
                alert(`⚠️ Stock insuficiente para: ${producto.nombre}\n\nCantidad en carrito: ${itemExistente.cantidad}\nStock disponible: ${producto.stock}`);
                return;
            }
            itemExistente.cantidad++;
        } else {
            carritoVenta.push({
                id: producto.id,
                nombre: producto.nombre,
                precio: producto.precio,
                categoria: producto.categoria,
                stock: producto.stock,
                cantidad: 1
            });
        }

        actualizarCarritoUI();
        
        // Efecto visual en el producto
        const productoCard = event.target.closest('.producto-card');
        if (productoCard) {
            productoCard.style.backgroundColor = '#d4edda';
            setTimeout(() => productoCard.style.backgroundColor = '', 300);
        }
    }

    function actualizarCarritoUI() {
        const container = document.getElementById('carritoItems');
        const totalElement = document.getElementById('totalVenta');
        const cantidadElement = document.getElementById('cantidadItems');
        const procesarBtn = document.getElementById('procesarVenta');

        if (carritoVenta.length === 0) {
            container.innerHTML = `
                <div class="text-center text-muted py-5" id="carritoVacio">
                    <i class="bi bi-cart-x display-2"></i>
                    <p>Carrito vacío</p>
                    <small>Agrega productos para comenzar</small>
                </div>
            `;
            totalElement.textContent = '$0';
            cantidadElement.textContent = '0';
            procesarBtn.disabled = true;
            return;
        }

        const itemsHtml = carritoVenta.map(item => `
            <div class="carrito-item">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <strong>${item.nombre}</strong>
                        <br>
                        <small class="text-muted">${item.categoria}</small>
                    </div>
                    <button class="btn btn-sm btn-outline-danger" onclick="eliminarDelCarrito(${item.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="cantidad-controls">
                        <button class="btn btn-sm btn-outline-secondary" onclick="cambiarCantidad(${item.id}, -1)">
                            <i class="bi bi-dash"></i>
                        </button>
                        <span class="cantidad-display">${item.cantidad}</span>
                        <button class="btn btn-sm btn-outline-secondary" onclick="cambiarCantidad(${item.id}, 1)">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                    <strong class="text-success">$${(item.precio * item.cantidad).toLocaleString()}</strong>
                </div>
            </div>
        `).join('');

        container.innerHTML = itemsHtml;

        // Calcular total
        const total = carritoVenta.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
        const totalItems = carritoVenta.reduce((sum, item) => sum + item.cantidad, 0);

        totalElement.textContent = `$${total.toLocaleString()}`;
        cantidadElement.textContent = totalItems;
        procesarBtn.disabled = false;
    }

    function cambiarCantidad(productoId, cambio) {
        const item = carritoVenta.find(item => item.id === productoId);
        if (!item) return;

        const nuevaCantidad = item.cantidad + cambio;
        
        if (nuevaCantidad <= 0) {
            eliminarDelCarrito(productoId);
            return;
        }

        // Verificar stock disponible al incrementar
        if (cambio > 0 && nuevaCantidad > item.stock) {
            alert(`⚠️ Stock insuficiente para: ${item.nombre}\n\nStock disponible: ${item.stock}`);
            return;
        }

        item.cantidad = nuevaCantidad;
        actualizarCarritoUI();
    }

    function eliminarDelCarrito(productoId) {
        carritoVenta = carritoVenta.filter(item => item.id !== productoId);
        actualizarCarritoUI();
    }

    function limpiarCarritoVenta() {
        carritoVenta = [];
        actualizarCarritoUI();
    }

    function procesarVentaRapida() {
        if (carritoVenta.length === 0) return;

        const total = carritoVenta.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
        const resumen = carritoVenta.map(item => 
            `${item.cantidad}x ${item.nombre} - $${(item.precio * item.cantidad).toLocaleString()}`
        ).join('\n');

        if (confirm(`🛒 Confirmar Venta Rápida\n\n${resumen}\n\nTOTAL: $${total.toLocaleString()}\n\n¿Procesar venta?`)) {
            // Deshabilitar botón durante procesamiento
            const procesarBtn = document.getElementById('procesarVenta');
            procesarBtn.disabled = true;
            procesarBtn.innerHTML = `
                <div class="spinner-border spinner-border-sm me-2" role="status">
                    <span class="visually-hidden">Procesando...</span>
                </div>
                Procesando...
            `;

            // Preparar datos para enviar
            const datosVenta = {
                items: carritoVenta.map(item => ({
                    id: item.id,
                    cantidad: item.cantidad
                })),
                total: total,
                _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            };

            // Enviar al backend
            fetch('/venta-rapida/procesar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': datosVenta._token
                },
                body: JSON.stringify(datosVenta)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar resultado exitoso
                    const productosDetalle = data.data.productos_vendidos.map(p => 
                        `${p.cantidad}x ${p.producto} - $${p.subtotal.toLocaleString()}\n   (Stock: ${p.stock_anterior} → ${p.stock_nuevo})`
                    ).join('\n');

                    alert(`✅ ¡Venta Procesada Exitosamente!\n\n` +
                          `🧾 Pago: ${data.data.numero_pago}\n` +
                          `💰 Total: $${data.data.total.toLocaleString()}\n` +
                          `📅 Fecha: ${data.data.fecha}\n\n` +
                          `📦 Productos Vendidos:\n${productosDetalle}\n\n` +
                          `✅ Inventario actualizado automáticamente`);

                    // Limpiar carrito y cerrar modal
                    limpiarCarritoVenta();
                    const modal = bootstrap.Modal.getInstance(document.getElementById('ventaRapidaModal'));
                    if (modal) modal.hide();

                    // Recargar productos para actualizar stock
                    cargarProductosVenta();
                } else {
                    // Mostrar error
                    alert(`❌ Error al procesar la venta:\n\n${data.message}`);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Error de conexión al procesar la venta.\n\nIntenta nuevamente.');
            })
            .finally(() => {
                // Restaurar botón
                procesarBtn.disabled = false;
                procesarBtn.innerHTML = `
                    <i class="bi bi-check-circle me-2"></i>
                    Procesar Venta
                `;
            });
        }
    }

    // Búsqueda en tiempo real
    function buscarProductosVenta() {
        const query = document.getElementById('buscarProductoVenta').value.toLowerCase();
        
        if (query.length < 2) {
            mostrarProductosVenta(productosVenta);
            return;
        }

        const productosFiltrados = productosVenta.filter(producto => 
            producto.nombre.toLowerCase().includes(query) ||
            producto.categoria.toLowerCase().includes(query)
        );

        mostrarProductosVenta(productosFiltrados);
    }
    
    // Animación del botón flotante al hacer scroll
    document.addEventListener('DOMContentLoaded', function() {
        const floatingBtn = document.querySelector('.btn-floating-main');
        let isVisible = true;
        
        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if (scrollTop > 100 && isVisible) {
                floatingBtn.style.opacity = '0.8';
            } else if (scrollTop <= 100 && !isVisible) {
                floatingBtn.style.opacity = '1';
            }
        });
        
        // Cerrar dropdown al hacer click fuera
        document.addEventListener('click', function(event) {
            const dropdown = document.querySelector('.floating-quick-actions .dropdown-menu');
            const button = document.querySelector('.btn-floating-main');
            
            if (dropdown && !button.contains(event.target) && !dropdown.contains(event.target)) {
                const bsDropdown = bootstrap.Dropdown.getInstance(button);
                if (bsDropdown) {
                    bsDropdown.hide();
                }
            }
        });
        
        // Abrir modal automáticamente si viene con parámetro nuevo=1
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('nuevo') === '1' && document.getElementById('nuevoPedidoModal')) {
            setTimeout(() => {
                const modal = new bootstrap.Modal(document.getElementById('nuevoPedidoModal'));
                modal.show();
                
                // Limpiar el parámetro de la URL sin recargar
                const newUrl = window.location.pathname + window.location.hash;
                window.history.replaceState({}, document.title, newUrl);
                
                // Enfocar primer campo
                setTimeout(() => {
                    const firstInput = document.querySelector('#nuevoPedidoModal input[type="text"]');
                    if (firstInput) firstInput.focus();
                }, 300);
            }, 500);
        }

        // Event listeners para venta rápida
        const buscarInput = document.getElementById('buscarProductoVenta');
        if (buscarInput) {
            buscarInput.addEventListener('input', buscarProductosVenta);
        }

        const procesarBtn = document.getElementById('procesarVenta');
        if (procesarBtn) {
            procesarBtn.addEventListener('click', procesarVentaRapida);
        }

        const limpiarBtn = document.getElementById('limpiarCarrito');
        if (limpiarBtn) {
            limpiarBtn.addEventListener('click', limpiarCarritoVenta);
        }
    });
    </script>
</body>
</html>