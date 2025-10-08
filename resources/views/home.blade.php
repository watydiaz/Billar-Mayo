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
                    Alquila nuestras mesas profesionales por horas y disfruta del mejor ambiente billarístico.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="#" class="btn btn-dark btn-lg">
                        <i class="bi bi-grid-3x3-gap me-2"></i>Ver Mesas Disponibles
                    </a>
                    <a href="#" class="btn btn-outline-dark btn-lg">
                        <i class="bi bi-calendar-check me-2"></i>Reservar Mesa
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
                <h2 class="h2 mb-3">Servicios del Club</h2>
                <p class="lead text-muted">Todo lo que necesitas para disfrutar del mejor billar</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-dark text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-grid-3x3-gap"></i>
                        </div>
                        <h5 class="card-title">Mesas Profesionales</h5>
                        <p class="card-text text-muted">
                            Mesas de billar de alta calidad con paños nuevos. 
                            Equipamiento profesional para la mejor experiencia de juego.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <h5 class="card-title">Alquiler por Horas</h5>
                        <p class="card-text text-muted">
                            Sistema flexible de alquiler por horas. 
                            Reserva tu mesa con anticipación o arrienda cuando llegues.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-warning text-dark rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-cup-hot"></i>
                        </div>
                        <h5 class="card-title">Cafetería y Snacks</h5>
                        <p class="card-text text-muted">
                            Disfruta de bebidas refrescantes y comida ligera 
                            mientras juegas en nuestras cómodas instalaciones.
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
                    <h3 class="display-4 text-dark fw-bold mb-2">12</h3>
                    <p class="text-muted mb-0">Mesas de Billar</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-item">
                    <h3 class="display-4 text-success fw-bold mb-2">500+</h3>
                    <p class="text-muted mb-0">Clientes Satisfechos</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-item">
                    <h3 class="display-4 fw-bold mb-2" style="color: #fbff14; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">13hrs</h3>
                    <p class="text-muted mb-0">Horario Diario</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-item">
                    <h3 class="display-4 text-info fw-bold mb-2">5 años</h3>
                    <p class="text-muted mb-0">De Experiencia</p>
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
                <h2 class="h2 mb-4">¿Listo para disfrutar del mejor billar?</h2>
                <p class="lead text-muted mb-4">
                    Ven al Terkkos Billiards Club y disfruta de nuestras mesas profesionales en el mejor ambiente de la ciudad.
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