<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Mesa;
use App\Models\Producto;

class DashboardController extends Controller
{
    public function index()
    {
        // Estadísticas generales del negocio
        $estadisticas = [
            'pedidos_activos' => Pedido::where('estado', '1')->count(),
            'mesas_ocupadas' => Mesa::whereHas('mesaAlquileres', function($query) {
                $query->where('estado', 'activo');
            })->count(),
            'mesas_disponibles' => Mesa::where('activa', true)
                ->whereDoesntHave('mesaAlquileres', function($query) {
                    $query->where('estado', 'activo');
                })->count(),
            'total_mesas' => Mesa::where('activa', true)->count(),
            'ingresos_dia' => Pedido::where('estado', '1')
                ->whereDate('created_at', today())
                ->sum('total_pedido'),
            'productos_disponibles' => Producto::where('activo', true)->count()
        ];

        // Pedidos recientes (últimos 5)
        $pedidosRecientes = Pedido::with(['mesaAlquileres.mesa'])
            ->where('estado', '1')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Mesas con estado actual
        $mesasEstado = Mesa::with(['mesaAlquileres' => function($query) {
            $query->where('estado', 'activo')
                  ->orderBy('created_at', 'desc');
        }])->where('activa', true)->get();

        // Productos más vendidos (simulado por ahora)
        $productosPopulares = Producto::where('activo', true)
            ->orderBy('nombre')
            ->take(6)
            ->get();

        return view('dashboard.index', compact(
            'estadisticas',
            'pedidosRecientes', 
            'mesasEstado',
            'productosPopulares'
        ));
    }
}