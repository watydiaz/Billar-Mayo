<footer class="bg-dark text-light py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h5 class="mb-3">
                    <i class="bi bi-trophy-fill me-2"></i>
                    {{ config('app.name', 'Billar Mayo') }}
                </h5>
                <p class="text-muted mb-0">
                    Sistema de gestión para torneos de billar. 
                    Organiza, compite y disfruta del mejor billar.
                </p>
            </div>
            <div class="col-md-3">
                <h6 class="mb-3">Enlaces Rápidos</h6>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-muted text-decoration-none">Torneos</a></li>
                    <li><a href="#" class="text-muted text-decoration-none">Jugadores</a></li>
                    <li><a href="#" class="text-muted text-decoration-none">Estadísticas</a></li>
                    <li><a href="#" class="text-muted text-decoration-none">Reglas</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h6 class="mb-3">Contacto</h6>
                <ul class="list-unstyled text-muted">
                    <li><i class="bi bi-geo-alt me-2"></i>Tu dirección aquí</li>
                    <li><i class="bi bi-telephone me-2"></i>+1 234 567 890</li>
                    <li><i class="bi bi-envelope me-2"></i>info@billarmayo.com</li>
                </ul>
            </div>
        </div>
        <hr class="my-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="text-muted mb-0">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Billar Mayo') }}. Todos los derechos reservados.
                </p>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="#" class="text-muted me-3"><i class="bi bi-facebook"></i></a>
                <a href="#" class="text-muted me-3"><i class="bi bi-twitter"></i></a>
                <a href="#" class="text-muted me-3"><i class="bi bi-instagram"></i></a>
                <a href="#" class="text-muted"><i class="bi bi-youtube"></i></a>
            </div>
        </div>
    </div>
</footer>