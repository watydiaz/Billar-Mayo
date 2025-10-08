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
                    <a class="nav-link text-dark fw-semibold {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ url('/') }}">
                        <i class="bi bi-house-door me-1"></i>Inicio
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark fw-semibold" href="#">
                        <i class="bi bi-calendar-event me-1"></i>Torneos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark fw-semibold" href="#">
                        <i class="bi bi-people-fill me-1"></i>Jugadores
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark fw-semibold" href="#">
                        <i class="bi bi-bar-chart me-1"></i>Estadísticas
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