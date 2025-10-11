<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RondaController;

// Dashboard principal
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/home', function () {
    return view('home');
})->name('home');

// Test de búsqueda
Route::get('/test-busqueda', function () {
    $productos = \App\Models\Producto::with('categoria')->where('activo', true)->get();
    return view('test-busqueda', compact('productos'));
})->name('test.busqueda');

// Rutas de autenticación
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Dashboard (requiere autenticación)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

// Rutas de Rondas (requieren autenticación) - Reemplaza el sistema de Pedidos
Route::middleware(['auth'])->group(function () {
    Route::get('/pedidos', [App\Http\Controllers\RondaController::class, 'index'])->name('pedidos.index');
    Route::post('/pedidos', [App\Http\Controllers\RondaController::class, 'store'])->name('pedidos.store');
    Route::post('/pedidos/{ronda}/agregar-ronda', [App\Http\Controllers\RondaController::class, 'agregarRonda'])->name('pedidos.agregar-ronda');
    Route::delete('/pedidos/{ronda}', [App\Http\Controllers\RondaController::class, 'eliminar'])->name('pedidos.eliminar');
    
    // Rutas de rondas
    Route::post('/rondas/{ronda}/asignar-mesa', [App\Http\Controllers\RondaController::class, 'asignarMesaRonda'])->name('pedidos.rondas.asignar-mesa');
    Route::post('/rondas/{ronda}/iniciar-tiempo', [App\Http\Controllers\RondaController::class, 'iniciarTiempoRonda'])->name('pedidos.rondas.iniciar-tiempo');
    Route::post('/rondas/{ronda}/finalizar-tiempo', [App\Http\Controllers\RondaController::class, 'finalizarTiempoRonda'])->name('pedidos.rondas.finalizar-tiempo');
    Route::post('/rondas/{ronda}/asignar-responsable', [App\Http\Controllers\RondaController::class, 'asignarResponsable'])->name('pedidos.rondas.asignar-responsable');
    Route::get('/rondas/{ronda}/tiempo-real', [App\Http\Controllers\RondaController::class, 'tiempoRealRonda'])->name('pedidos.rondas.tiempo-real');
    
    // Rutas para confirmar ventas desde rondas - SISTEMA DUAL DE PAGOS
    // Resumen de ventas (general o por cliente específico)
    Route::get('/rondas/resumen-ventas/{cliente?}', [App\Http\Controllers\RondaController::class, 'resumenVenta'])->name('pedidos.rondas.resumen-venta');
    
    // Pago individual por ronda
    Route::post('/rondas/{ronda}/pagar-ronda', [App\Http\Controllers\RondaController::class, 'confirmarVentaRonda'])->name('pedidos.rondas.pagar-ronda');
    
    // Pago de cuenta completa del cliente
    Route::post('/rondas/cerrar-cuenta-completa', [App\Http\Controllers\RondaController::class, 'cerrarCuentaCompleta'])->name('pedidos.rondas.cerrar-cuenta');
    
    // Vista principal del sistema de pagos
    Route::get('/pagos', function () {
        return view('pagos.index');
    })->name('pagos.index');

    
    // Endpoint optimizado para obtener todos los timers activos
    Route::get('/pedidos/timers-activos', [App\Http\Controllers\RondaController::class, 'timersActivos'])->name('pedidos.timers-activos');
    
    // API para venta rápida
    Route::get('/venta-rapida/productos', [App\Http\Controllers\VentaRapidaController::class, 'obtenerProductos'])->name('venta-rapida.productos');
    Route::post('/venta-rapida/procesar', [App\Http\Controllers\VentaRapidaController::class, 'procesarVenta'])->name('venta-rapida.procesar');

    // Rutas para gestión de productos
    Route::resource('productos', App\Http\Controllers\ProductController::class);
    Route::put('/productos/{id}/field', [App\Http\Controllers\ProductController::class, 'updateField'])->name('productos.update-field');
    Route::put('/productos/{id}/toggle-status', [App\Http\Controllers\ProductController::class, 'toggleStatus'])->name('productos.toggle-status');

    // Rutas para gestión de ventas
    Route::get('/ventas/estadisticas', [App\Http\Controllers\VentaController::class, 'estadisticas'])->name('ventas.estadisticas');
    Route::resource('ventas', App\Http\Controllers\VentaController::class);

});
