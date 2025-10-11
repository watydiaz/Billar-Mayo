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
    
    <!-- Custom CSS -->
    @vite(['resources/css/app.css'])
    
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
                <a class="dropdown-item" href="#" onclick="ventaRapida()">
                    <i class="bi bi-lightning-charge text-warning me-2"></i>
                    Venta Rápida
                </a>
            </li>
        </ul>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    
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
    
    function ventaRapida() {
        // Cerrar el dropdown primero
        const dropdown = bootstrap.Dropdown.getInstance(document.getElementById('quickActionBtn'));
        if (dropdown) dropdown.hide();
        
        // Mostrar opciones de venta rápida
        const ventaOptions = [
            { text: 'Mesa por 1 hora', precio: 15000 },
            { text: 'Mesa por 2 horas', precio: 28000 },
            { text: 'Mesa por 3 horas', precio: 40000 },
            { text: 'Crear venta personalizada', precio: null }
        ];
        
        let optionsHtml = ventaOptions.map((option, index) => {
            const precioText = option.precio ? `- $${option.precio.toLocaleString()}` : '';
            return `<button class="btn btn-outline-primary d-block w-100 mb-2" onclick="procesarVentaRapida('${option.text}', ${option.precio})">
                ${option.text} ${precioText}
            </button>`;
        }).join('');
        
        // Crear modal dinámico
        const modalHtml = `
            <div class="modal fade" id="ventaRapidaModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-dark">
                            <h5 class="modal-title">
                                <i class="bi bi-lightning-charge me-2"></i>
                                Venta Rápida
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-3">Selecciona una opción de venta rápida:</p>
                            ${optionsHtml}
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Insertar y mostrar modal
        const existingModal = document.getElementById('ventaRapidaModal');
        if (existingModal) existingModal.remove();
        
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modal = new bootstrap.Modal(document.getElementById('ventaRapidaModal'));
        modal.show();
        
        // Limpiar modal al cerrarse
        document.getElementById('ventaRapidaModal').addEventListener('hidden.bs.modal', function() {
            this.remove();
        });
    }
    
    function procesarVentaRapida(tipo, precio) {
        console.log('Venta rápida:', tipo, precio);
        
        // Cerrar modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('ventaRapidaModal'));
        if (modal) modal.hide();
        
        if (precio) {
            // Crear pedido automático con el tipo seleccionado
            alert(`🎯 Procesando venta: ${tipo}\nPrecio: $${precio.toLocaleString()}\n\n⚠️ Funcionalidad en desarrollo`);
        } else {
            // Redirigir a crear pedido personalizado
            crearNuevoPedido();
        }
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
    });
    </script>
</body>
</html>