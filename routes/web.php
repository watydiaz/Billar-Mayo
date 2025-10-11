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

// Rutas de Pedidos (requieren autenticación)
Route::middleware('auth')->group(function () {
    Route::get('/pedidos', [App\Http\Controllers\PedidoController::class, 'index'])->name('pedidos.index');
    Route::post('/pedidos', [App\Http\Controllers\PedidoController::class, 'store'])->name('pedidos.store');
    Route::post('/pedidos/{pedido}/agregar-ronda', [App\Http\Controllers\PedidoController::class, 'agregarRonda'])->name('pedidos.agregar-ronda');
    Route::delete('/pedidos/{pedido}', [App\Http\Controllers\PedidoController::class, 'eliminar'])->name('pedidos.eliminar');
    
    // Rutas de rondas dentro de pedidos
    Route::post('/pedidos/{pedido}/rondas/{ronda}/asignar-mesa', [App\Http\Controllers\PedidoController::class, 'asignarMesaRonda'])->name('pedidos.rondas.asignar-mesa');
    Route::post('/pedidos/{pedido}/rondas/{ronda}/iniciar-tiempo', [App\Http\Controllers\PedidoController::class, 'iniciarTiempoRonda'])->name('pedidos.rondas.iniciar-tiempo');
    Route::post('/pedidos/{pedido}/rondas/{ronda}/finalizar-tiempo', [App\Http\Controllers\PedidoController::class, 'finalizarTiempoRonda'])->name('pedidos.rondas.finalizar-tiempo');
    Route::post('/pedidos/{pedido}/rondas/{ronda}/asignar-responsable', [App\Http\Controllers\PedidoController::class, 'asignarResponsable'])->name('pedidos.rondas.asignar-responsable');
    Route::get('/pedidos/{pedido}/rondas/{ronda}/tiempo-real', [App\Http\Controllers\PedidoController::class, 'tiempoRealRonda'])->name('pedidos.rondas.tiempo-real');
    Route::post('/pedidos/{pedido}/rondas/{ronda}/agregar-productos', [App\Http\Controllers\PedidoController::class, 'agregarProductos'])->name('pedidos.rondas.agregar-productos');
    
    // API para obtener productos
    Route::get('/productos', function() {
        $productos = \App\Models\Producto::with('categoria')
            ->where('activo', true)
            ->get()
            ->map(function($producto) {
                return [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'precio' => $producto->precio_venta,
                    'categoria' => $producto->categoria->nombre ?? 'Sin categoría'
                ];
            });
        
        return response()->json($productos);
    })->name('productos.api');
    
    // Endpoint optimizado para obtener todos los timers activos
    Route::get('/pedidos/timers-activos', [App\Http\Controllers\PedidoController::class, 'timersActivos'])->name('pedidos.timers-activos');

});
