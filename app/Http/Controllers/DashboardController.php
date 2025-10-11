<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ronda;
use App\Models\Mesa;
use App\Models\MesaRonda;
use App\Models\Producto;

class DashboardController extends Controller
{
    public function index()
    {
        // Estadísticas generales del negocio
        $estadisticas = [
            'pedidos_activos' => Ronda::where('estado', 'activa')->count(),
            'mesas_ocupadas' => MesaRonda::where('estado', 'activo')->count(),
            'mesas_disponibles' => Mesa::where('activa', true)->count() - MesaRonda::where('estado', 'activo')->count(),
            'total_mesas' => Mesa::where('activa', true)->count(),
            'ingresos_dia' => MesaRonda::whereDate('created_at', today())
                ->where('estado', 'finalizado')
                ->sum('costo_tiempo'),
            'productos_disponibles' => Producto::where('activo', true)->count()
        ];

        // Rondas recientes (últimos 5) - reemplaza pedidos
        $pedidosRecientes = Ronda::with(['mesaRonda.mesa'])
            ->where('estado', 'activa')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Mesas con estado actual
        $mesasEstado = Mesa::with(['mesaRondas' => function($query) {
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