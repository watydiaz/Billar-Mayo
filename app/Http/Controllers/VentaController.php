<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Venta::with('detalles');
        
        // Aplicar filtros
        if ($request->has('filtro_fecha') && !empty($request->filtro_fecha)) {
            switch ($request->filtro_fecha) {
                case 'hoy':
                    $query->whereDate('created_at', Carbon::today());
                    break;
                case 'ayer':
                    $query->whereDate('created_at', Carbon::yesterday());
                    break;
                case 'semana':
                    $query->whereBetween('created_at', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                    break;
                case 'mes_actual':
                    $query->whereYear('created_at', Carbon::now()->year)
                          ->whereMonth('created_at', Carbon::now()->month);
                    break;
                case 'mes_anterior':
                    $query->whereYear('created_at', Carbon::now()->subMonth()->year)
                          ->whereMonth('created_at', Carbon::now()->subMonth()->month);
                    break;
                case 'mes_personalizado':
                    if ($request->has('mes') && $request->has('año')) {
                        $query->whereYear('created_at', $request->año)
                              ->whereMonth('created_at', $request->mes);
                    }
                    break;
                case 'rango_personalizado':
                    if ($request->has('fecha_inicio') && $request->has('fecha_fin')) {
                        $query->whereBetween('created_at', [
                            Carbon::parse($request->fecha_inicio)->startOfDay(),
                            Carbon::parse($request->fecha_fin)->endOfDay()
                        ]);
                    }
                    break;
            }
        }

        // Filtro por estado
        if ($request->has('estado') && $request->estado !== '') {
            $query->where('estado', $request->estado);
        }

        // Filtro por tipo de pago
        if ($request->has('tipo_pago') && $request->tipo_pago !== '') {
            $query->where('tipo_pago', $request->tipo_pago);
        }

        // Búsqueda por número de venta
        if ($request->has('buscar') && $request->buscar !== '') {
            $query->where('numero_venta', 'LIKE', '%' . $request->buscar . '%');
        }

        // Ordenar por fecha más reciente
        $ventas = $query->orderBy('created_at', 'desc')->paginate(50);
        
        // Calcular totales
        $totales = $this->calcularTotales($request);
        
        if ($request->ajax()) {
            return response()->json([
                'ventas' => view('ventas.table', compact('ventas'))->render(),
                'totales' => $totales,
                'paginacion' => $ventas->links()->render()
            ]);
        }

        return view('ventas.index', compact('ventas', 'totales'));
    }

    /**
     * Calcular totales según los filtros aplicados
     */
    public function calcularTotales(Request $request)
    {
        $query = Venta::query();
        
        // Aplicar los mismos filtros que en index
        if ($request->has('filtro_fecha') && !empty($request->filtro_fecha)) {
            switch ($request->filtro_fecha) {
                case 'hoy':
                    $query->whereDate('created_at', Carbon::today());
                    break;
                case 'ayer':
                    $query->whereDate('created_at', Carbon::yesterday());
                    break;
                case 'semana':
                    $query->whereBetween('created_at', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                    break;
                case 'mes_actual':
                    $query->whereYear('created_at', Carbon::now()->year)
                          ->whereMonth('created_at', Carbon::now()->month);
                    break;
                case 'mes_anterior':
                    $query->whereYear('created_at', Carbon::now()->subMonth()->year)
                          ->whereMonth('created_at', Carbon::now()->subMonth()->month);
                    break;
                case 'mes_personalizado':
                    if ($request->has('mes') && $request->has('año')) {
                        $query->whereYear('created_at', $request->año)
                              ->whereMonth('created_at', $request->mes);
                    }
                    break;
                case 'rango_personalizado':
                    if ($request->has('fecha_inicio') && $request->has('fecha_fin')) {
                        $query->whereBetween('created_at', [
                            Carbon::parse($request->fecha_inicio)->startOfDay(),
                            Carbon::parse($request->fecha_fin)->endOfDay()
                        ]);
                    }
                    break;
            }
        }

        if ($request->has('estado') && $request->estado !== '') {
            $query->where('estado', $request->estado);
        }

        if ($request->has('tipo_pago') && $request->tipo_pago !== '') {
            $query->where('tipo_pago', $request->tipo_pago);
        }

        if ($request->has('buscar') && $request->buscar !== '') {
            $query->where('numero_venta', 'LIKE', '%' . $request->buscar . '%');
        }

        return [
            'total_ventas' => $query->sum('total'),
            'total_subtotal' => $query->sum('subtotal'),
            'total_descuentos' => $query->sum('descuento'),
            'cantidad_ventas' => $query->count(),
            'promedio_venta' => $query->count() > 0 ? $query->avg('total') : 0
        ];
    }

    /**
     * Obtener estadísticas rápidas para el dashboard
     */
    public function estadisticas()
    {
        $hoy = Carbon::today();
        $ayer = Carbon::yesterday();
        $inicioMes = Carbon::now()->startOfMonth();
        $finMes = Carbon::now()->endOfMonth();

        return response()->json([
            'ventas_hoy' => [
                'total' => Venta::whereDate('created_at', $hoy)->sum('total'),
                'cantidad' => Venta::whereDate('created_at', $hoy)->count()
            ],
            'ventas_ayer' => [
                'total' => Venta::whereDate('created_at', $ayer)->sum('total'),
                'cantidad' => Venta::whereDate('created_at', $ayer)->count()
            ],
            'ventas_mes' => [
                'total' => Venta::whereBetween('created_at', [$inicioMes, $finMes])->sum('total'),
                'cantidad' => Venta::whereBetween('created_at', [$inicioMes, $finMes])->count()
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $venta = Venta::with('detalles.producto')->findOrFail($id);
        
        if (request()->ajax()) {
            return response()->json([
                'venta' => $venta,
                'html' => view('ventas.show', compact('venta'))->render()
            ]);
        }

        return view('ventas.show', compact('venta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
