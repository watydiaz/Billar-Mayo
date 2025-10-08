@extends('layouts.app')

@section('title', 'Registrarse')

@section('main-class', 'container')

@section('content')
<div class="row justify-content-center py-5">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow">
            <div class="card-header bg-success text-white text-center">
                <h4 class="mb-0">
                    <i class="bi bi-person-plus me-2"></i>
                    Crear Cuenta
                </h4>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    
                    <!-- Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label">
                            <i class="bi bi-person me-1"></i>
                            Nombre Completo
                        </label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}" 
                               required 
                               autocomplete="name" 
                               autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope me-1"></i>
                            Correo Electrónico
                        </label>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autocomplete="email">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock me-1"></i>
                            Contraseña
                        </label>
                        <input type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               required 
                               autocomplete="new-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Mínimo 8 caracteres</div>
                    </div>

                    <!-- Password Confirmation -->
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">
                            <i class="bi bi-lock-fill me-1"></i>
                            Confirmar Contraseña
                        </label>
                        <input type="password" 
                               class="form-control" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               required 
                               autocomplete="new-password">
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="bi bi-person-plus me-2"></i>
                            Crear Cuenta
                        </button>
                    </div>
                </form>

                <hr class="my-4">

                <div class="text-center">
                    <p class="text-muted mb-2">¿Ya tienes una cuenta?</p>
                    <a href="{{ route('login') }}" class="btn btn-outline-primary">
                        <i class="bi bi-box-arrow-in-right me-2"></i>
                        Iniciar Sesión
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection