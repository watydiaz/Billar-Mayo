@extends('layouts.app')

@section('title', 'Bienvenido al Sistema de Billar')

@section('content')
<!-- Hero Section -->
<section class="hero-section bg-white text-dark py-5">
    <div class="container">
        <div class="row align-items-center min-vh-50">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4 text-dark">
                    <img src="https://billar.diaztecnologia.com/img/logo.jpg" 
                         alt="Terkkos Logo" 
                         height="60" 
                         class="me-3 rounded logo-hover">
                    Terkkos Billiards Club
                </h1>
                <p class="lead mb-4 text-dark">
                    El club de billar más prestigioso de la ciudad. 
                    Disfruta de nuestras mesas profesionales, torneos emocionantes y el mejor ambiente billarístico.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="#" class="btn btn-dark btn-lg">
                        <i class="bi bi-play-circle me-2"></i>Comenzar Partida
                    </a>
                    <a href="#" class="btn btn-outline-dark btn-lg">
                        <i class="bi bi-people me-2"></i>Ver Jugadores
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="hero-image">
                    <i class="bi bi-circle-fill text-success" style="font-size: 8rem; opacity: 0.2;"></i>
                    <i class="bi bi-triangle-fill text-warning position-absolute" style="font-size: 4rem; transform: translate(-50%, -50%);"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col">
                <h2 class="h2 mb-3">Características Principales</h2>
                <p class="lead text-muted">Todo lo que necesitas para organizar torneos profesionales</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-dark text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <h5 class="card-title">Gestión de Torneos</h5>
                        <p class="card-text text-muted">
                            Crea y administra torneos con diferentes modalidades. 
                            Configuración flexible para adaptarse a tus necesidades.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <h5 class="card-title">Registro de Jugadores</h5>
                        <p class="card-text text-muted">
                            Mantén una base de datos completa de todos los jugadores 
                            con sus estadísticas y historial de participación.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-warning text-dark rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-bar-chart-fill"></i>
                        </div>
                        <h5 class="card-title">Estadísticas Avanzadas</h5>
                        <p class="card-text text-muted">
                            Análisis detallado del rendimiento de los jugadores 
                            y estadísticas completas de los torneos.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 mb-4">
                <div class="stat-item">
                    <h3 class="display-4 text-dark fw-bold mb-2">25+</h3>
                    <p class="text-muted mb-0">Torneos Organizados</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-item">
                    <h3 class="display-4 text-success fw-bold mb-2">150+</h3>
                    <p class="text-muted mb-0">Jugadores Registrados</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-item">
                    <h3 class="display-4 fw-bold mb-2" style="color: #fbff14; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">500+</h3>
                    <p class="text-muted mb-0">Partidas Jugadas</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-item">
                    <h3 class="display-4 text-info fw-bold mb-2">12hrs</h3>
                    <p class="text-muted mb-0">Horario Diario</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h2 class="h2 mb-4">¿Listo para organizar tu próximo torneo?</h2>
                <p class="lead text-muted mb-4">
                    Únete a nuestra plataforma y lleva la gestión de tus torneos de billar al siguiente nivel.
                </p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-dark btn-lg">
                            <i class="bi bi-person-plus me-2"></i>Registrarse Ahora
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-dark btn-lg">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                        </a>
                    @else
                        <a href="{{ url('/dashboard') }}" class="btn btn-dark btn-lg">
                            <i class="bi bi-speedometer2 me-2"></i>Ir al Dashboard
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</section>

@push('head')
<style>
    .hero-section {
        position: relative;
        overflow: hidden;
    }
    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(251, 255, 20, 0.05) 0%, rgba(255, 255, 255, 0.1) 100%);
        z-index: 1;
    }
    .hero-section .container {
        position: relative;
        z-index: 2;
    }
    .min-vh-50 {
        min-height: 50vh;
    }
    .feature-icon {
        font-size: 1.5rem;
    }
    .stat-item h3 {
        line-height: 1;
    }
</style>
@endpush
@endsection