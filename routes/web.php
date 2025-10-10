<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

// Dashboard principal
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/home', function () {
    return view('home');
})->name('home');

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
    Route::post('/pedidos/{pedido}/iniciar-tiempo', [App\Http\Controllers\PedidoController::class, 'iniciarTiempo'])->name('pedidos.iniciar-tiempo');
    Route::post('/pedidos/{pedido}/finalizar-tiempo', [App\Http\Controllers\PedidoController::class, 'finalizarTiempo'])->name('pedidos.finalizar-tiempo');
    Route::post('/pedidos/{pedido}/agregar-ronda', [App\Http\Controllers\PedidoController::class, 'agregarRonda'])->name('pedidos.agregar-ronda');
    Route::delete('/pedidos/{pedido}', [App\Http\Controllers\PedidoController::class, 'eliminar'])->name('pedidos.eliminar');
});
