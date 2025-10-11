<nav class="navbar navbar-expand-lg navbar-light navbar-terkkos">
    <div class="container-fluid">
        <!-- Brand/Logo -->
        <a class="navbar-brand fw-bold text-dark d-flex align-items-center" href="{{ url('/') }}">
            <img src="https://billar.diaztecnologia.com/img/logo.jpg" 
                 alt="Terkkos Logo" 
                 height="40" 
                 class="me-2 rounded logo-hover">
            <span class="fs-5">Terkkos Billiards Club</span>
        </a>

        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Links -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link text-dark fw-semibold {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2 me-1"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark fw-semibold" href="#">
                        <i class="bi bi-grid-3x3-gap me-1"></i>Mesas Disponibles
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark fw-semibold {{ request()->routeIs('pedidos.*') ? 'active' : '' }}" href="{{ route('pedidos.index') }}">
                        <i class="bi bi-receipt me-1"></i>Pedidos y Rondas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark fw-semibold {{ request()->routeIs('productos.*') ? 'active' : '' }}" href="{{ route('productos.index') }}">
                        <i class="bi bi-boxes me-1"></i>Productos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark fw-semibold {{ request()->routeIs('ventas.*') ? 'active' : '' }}" href="{{ route('ventas.index') }}">
                        <i class="bi bi-graph-up me-1"></i>Ventas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark fw-semibold" href="#">
                        <i class="bi bi-currency-dollar me-1"></i>Tarifas
                    </a>
                </li>
            </ul>

            <!-- User Authentication Links -->
            <ul class="navbar-nav">
                @if (Route::has('login'))
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-dark fw-semibold" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-1"></i>
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ url('/dashboard') }}">
                                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                                </a></li>
                                <li><a class="dropdown-item" href="#">
                                    <i class="bi bi-person-gear me-2"></i>Perfil
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link text-dark fw-semibold" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Iniciar Sesión
                            </a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link text-dark fw-semibold" href="{{ route('register') }}">
                                    <i class="bi bi-person-plus me-1"></i>Registrarse
                                </a>
                            </li>
                        @endif
                    @endauth
                @endif
            </ul>
        </div>
    </div>
</nav>