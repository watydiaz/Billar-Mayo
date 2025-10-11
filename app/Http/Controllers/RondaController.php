<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ronda;
use App\Models\Mesa;
use App\Models\MesaRonda;
use App\Models\Producto;
use App\Models\RondaDetalle;

class RondaController extends Controller
{
    // Vista principal de rondas - Reemplaza la vista de pedidos
    public function index()
    {
        // Cargar rondas activas con sus relaciones
        $rondasActivas = Ronda::with(['mesaRonda.mesa', 'detalles.producto'])
            ->where('estado', 'activa')
            ->orderBy('created_at', 'desc')
            ->get();

        $mesas = Mesa::where('activa', true)->get();

        // Estadísticas
        $mesasOcupadas = MesaRonda::where('estado', 'activo')->count();
        $tiempoTotalHoy = MesaRonda::whereDate('created_at', today())
            ->where('estado', 'finalizado')
            ->sum('duracion_minutos');
        $ingresosTiempoHoy = MesaRonda::whereDate('created_at', today())
            ->where('estado', 'finalizado')
            ->sum('costo_tiempo');

        // Para compatibilidad con la vista de pedidos, adaptamos las rondas
        $pedidos = $rondasActivas->map(function($ronda) {
            // Crear un objeto que simule un pedido para compatibilidad con la vista
            // Crear una copia de la ronda para la estructura interna, manteniendo las relaciones
            $rondaInterna = clone $ronda;
            $rondaInterna->setRelations($ronda->getRelations()); // Mantener todas las relaciones
            
            $ronda->rondas = collect([$rondaInterna]); // La ronda interna mantiene los detalles
            $ronda->total_pedido = $ronda->total_ronda; // Alias para total
            $ronda->nombre_cliente = $ronda->cliente; // Alias para cliente
            $ronda->numero_pedido = $ronda->numero_ronda; // Alias para número
            return $ronda;
        });

        $productos = Producto::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('pedidos.index', compact(
            'pedidos',
            'mesas',
            'productos',
            'mesasOcupadas',
            'tiempoTotalHoy',
            'ingresosTiempoHoy'
        ));
    }

    // Crear nueva ronda (reemplaza la creación de pedidos)
    public function store(Request $request)
    {
        $request->validate([
            'nombre_cliente' => 'required|string|max:255',
            'responsable' => 'nullable|string|max:255',
            'mesa_id' => 'nullable|exists:mesas,id'
        ]);

        // Generar número de ronda único
        $numeroRonda = 'R' . date('Ymd') . '-' . str_pad(Ronda::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT);

        $ronda = Ronda::create([
            'numero_ronda' => $numeroRonda,
            'cliente' => $request->nombre_cliente,
            'responsable' => $request->responsable ?? 'No asignado',
            'total_ronda' => 0,
            'estado' => 'activa'
        ]);

        // Si se seleccionó una mesa, asignarla directamente
        if ($request->mesa_id) {
            MesaRonda::create([
                'ronda_id' => $ronda->id,
                'mesa_id' => $request->mesa_id,
                'estado' => 'pendiente'
            ]);
        }

        return redirect()->back()->with('success', 'Ronda creada exitosamente');
    }

    // Asignar mesa a una ronda
    public function asignarMesa(Request $request, Ronda $ronda)
    {
        $request->validate([
            'mesa_id' => 'required|exists:mesas,id',
        ]);

        // Verificar que la mesa no esté ocupada por otra ronda activa
        $mesaOcupada = MesaRonda::where('mesa_id', $request->mesa_id)
            ->where('estado', 'activo')
            ->exists();

        if ($mesaOcupada) {
            return redirect()->back()->with('error', 'La mesa ya está ocupada');
        }

        // Crear o actualizar la relación mesa-ronda
        MesaRonda::updateOrCreate(
            ['ronda_id' => $ronda->id],
            [
                'mesa_id' => $request->mesa_id,
                'estado' => 'pendiente'
            ]
        );

        return redirect()->back()->with('success', 'Mesa asignada a la ronda');
    }

    // Iniciar tiempo de una ronda
    public function iniciarTiempo(Ronda $ronda)
    {
        $mesaRonda = $ronda->mesaRonda;

        if (!$mesaRonda) {
            return redirect()->back()->with('error', 'Debe asignar una mesa antes de iniciar el tiempo');
        }

        if ($mesaRonda->estado === 'activo') {
            return redirect()->back()->with('error', 'El tiempo ya está activo para esta ronda');
        }

        $mesaRonda->iniciar();

        return redirect()->back()->with('success', 'Tiempo iniciado para la ronda');
    }

    // Finalizar tiempo de una ronda
    public function finalizarTiempo(Ronda $ronda)
    {
        $mesaRonda = $ronda->mesaRonda;

        if (!$mesaRonda || $mesaRonda->estado !== 'activo') {
            return redirect()->back()->with('error', 'No hay tiempo activo para esta ronda');
        }

        $mesaRonda->finalizar();

        // Actualizar el total de la ronda sumando el costo del tiempo
        $ronda->update([
            'total_ronda' => $ronda->total_ronda + $mesaRonda->costo_tiempo
        ]);

        return redirect()->back()->with('success', 'Tiempo finalizado y costo agregado a la ronda');
    }

    // Obtener datos en tiempo real para JavaScript
    public function tiempoRealTime(Ronda $ronda)
    {
        $mesaRonda = $ronda->mesaRonda;

        if (!$mesaRonda || $mesaRonda->estado !== 'activo') {
            return response()->json([
                'activo' => false,
                'duracion' => 0,
                'costo' => 0
            ]);
        }

        return response()->json([
            'activo' => true,
            'duracion' => $mesaRonda->duracion_actual,
            'costo' => $mesaRonda->costo_actual,
            'inicio' => $mesaRonda->inicio_tiempo->format('H:i:s')
        ]);
    }

    // Métodos adicionales para compatibilidad con las rutas de pedidos
    
    public function agregarRonda(Request $request, Ronda $ronda)
    {
        // Este método ahora puede crear rondas adicionales relacionadas
        return redirect()->back()->with('info', 'Funcionalidad de agregar ronda no implementada');
    }

    public function eliminar(Ronda $ronda)
    {
        if ($ronda->mesaRonda && $ronda->mesaRonda->estado === 'activo') {
            return redirect()->back()->with('error', 'No se puede eliminar una ronda con tiempo activo');
        }

        $ronda->delete();
        return redirect()->back()->with('success', 'Ronda eliminada correctamente');
    }

    public function asignarMesaRonda(Request $request, Ronda $ronda)
    {
        $request->validate([
            'mesa_id' => 'required|exists:mesas,id'
        ]);

        // Crear o actualizar mesa_ronda
        MesaRonda::updateOrCreate(
            ['ronda_id' => $ronda->id],
            [
                'mesa_id' => $request->mesa_id,
                'estado' => 'pendiente'
            ]
        );

        return redirect()->back()->with('success', 'Mesa asignada correctamente');
    }

    public function iniciarTiempoRonda(Ronda $ronda)
    {
        return $this->iniciarTiempo($ronda);
    }

    public function finalizarTiempoRonda(Ronda $ronda)
    {
        return $this->finalizarTiempo($ronda);
    }

    public function asignarResponsable(Request $request, Ronda $ronda)
    {
        $request->validate([
            'responsable' => 'required|string|max:255'
        ]);

        $ronda->update(['responsable' => $request->responsable]);
        return redirect()->back()->with('success', 'Responsable asignado correctamente');
    }

    public function tiempoRealRonda(Ronda $ronda)
    {
        return $this->tiempoRealTime($ronda);
    }

    public function timersActivos()
    {
        $mesasActivas = MesaRonda::with(['ronda', 'mesa'])
            ->where('estado', 'activo')
            ->get();

        return response()->json($mesasActivas->map(function ($mesaRonda) {
            return [
                'id' => $mesaRonda->id,
                'ronda_id' => $mesaRonda->ronda_id,
                'mesa' => $mesaRonda->mesa->nombre,
                'duracion' => $mesaRonda->duracion_actual,
                'costo' => $mesaRonda->costo_actual,
                'inicio' => $mesaRonda->inicio_tiempo->format('H:i:s')
            ];
        }));
    }

    /**
     * Confirmar venta de una ronda individual
     */
    public function confirmarVentaRonda(Request $request, $rondaId)
    {
        try {
            $ronda = Ronda::with(['detalles.producto', 'mesaRonda.mesa'])->findOrFail($rondaId);
            
            // Verificar que la ronda esté activa
            if ($ronda->estado !== 'activa') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden confirmar ventas de rondas activas'
                ], 400);
            }

            $subtotalProductos = $ronda->total_ronda;
            $descuento = $request->descuento ?? 0;
            $costoTiempo = 0;
            $detallesTiempo = [];

            // Procesar tiempo de mesa si hay una asignada y está activa
            if ($ronda->mesaRonda && $ronda->mesaRonda->isActivo()) {
                // Finalizar el tiempo de mesa
                $ronda->mesaRonda->finalizar();
                
                $costoTiempo = $ronda->mesaRonda->costo_tiempo;
                $detallesTiempo = [
                    'mesa' => $ronda->mesaRonda->mesa->nombre,
                    'duracion_minutos' => $ronda->mesaRonda->duracion_minutos,
                    'precio_por_hora' => $ronda->mesaRonda->mesa->precio_hora,
                    'costo_tiempo' => $costoTiempo
                ];
            }

            $subtotal = $subtotalProductos + $costoTiempo;

            // Crear la venta
            $venta = \App\Models\Venta::create([
                'numero_venta' => 'VTA-' . str_pad(\App\Models\Venta::count() + 1, 6, '0', STR_PAD_LEFT),
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'total' => $subtotal - $descuento,
                'estado' => '1',
                'tipo_pago' => $request->tipo_pago ?? 'efectivo',
                'observaciones' => "Pago individual - Ronda #{$ronda->numero_ronda} - Cliente: {$ronda->cliente}" . 
                                 ($detallesTiempo ? " - Mesa: {$detallesTiempo['mesa']} ({$detallesTiempo['duracion_minutos']} min)" : "")
            ]);

            // Crear detalles de venta por productos consumidos
            foreach ($ronda->detalles as $detalle) {
                \App\Models\VentaDetalle::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $detalle->producto_id,
                    'cantidad' => $detalle->cantidad,
                    'precio_unitario' => $detalle->precio_unitario,
                    'subtotal' => $detalle->subtotal
                ]);
            }

            // Crear detalle por tiempo de mesa si aplica
            if ($costoTiempo > 0) {
                \App\Models\VentaDetalle::create([
                    'venta_id' => $venta->id,
                    'producto_id' => null, // Servicio de tiempo, no producto físico
                    'cantidad' => 1,
                    'precio_unitario' => $costoTiempo,
                    'subtotal' => $costoTiempo,
                    'descripcion' => "Tiempo de mesa: {$detallesTiempo['mesa']} ({$detallesTiempo['duracion_minutos']} minutos)"
                ]);
            }

            // Marcar la ronda como pagada
            $ronda->update(['estado' => 'pagada']);

            return response()->json([
                'success' => true,
                'message' => 'Ronda pagada exitosamente',
                'tipo' => 'ronda_individual',
                'venta_id' => $venta->id,
                'numero_venta' => $venta->numero_venta,
                'total' => $venta->total,
                'desglose' => [
                    'productos' => $subtotalProductos,
                    'tiempo_mesa' => $costoTiempo,
                    'descuento' => $descuento,
                    'detalles_tiempo' => $detallesTiempo
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al confirmar la venta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cerrar cuenta completa del cliente (todas las rondas activas)
     */
    public function cerrarCuentaCompleta(Request $request)
    {
        try {
            $cliente = $request->cliente;
            
            if (!$cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debe especificar el cliente'
                ], 400);
            }

            // Obtener todas las rondas activas del cliente con sus respectivos tiempos de mesa
            $rondas = Ronda::with(['detalles.producto', 'mesaRonda.mesa'])
                ->where('cliente', $cliente)
                ->where('estado', 'activa')
                ->get();

            if ($rondas->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay rondas activas para este cliente'
                ], 400);
            }

            // Calcular totales
            $subtotalProductos = $rondas->sum('total_ronda');
            $costoTiempoTotal = 0;
            $detallesMesas = [];
            $mesasFinalizadas = [];

            // Procesar y finalizar todos los tiempos de mesa activos
            foreach ($rondas as $ronda) {
                if ($ronda->mesaRonda && $ronda->mesaRonda->isActivo()) {
                    // Finalizar tiempo de mesa
                    $ronda->mesaRonda->finalizar();
                    
                    $costoMesa = $ronda->mesaRonda->costo_tiempo;
                    $costoTiempoTotal += $costoMesa;
                    
                    $detallesMesas[] = [
                        'mesa' => $ronda->mesaRonda->mesa->nombre,
                        'ronda' => $ronda->numero_ronda,
                        'duracion_minutos' => $ronda->mesaRonda->duracion_minutos,
                        'costo' => $costoMesa
                    ];
                    
                    $mesasFinalizadas[] = $ronda->mesaRonda->mesa->nombre;
                }
            }

            $subtotal = $subtotalProductos + $costoTiempoTotal;
            $descuento = $request->descuento ?? 0;

            // Crear la venta consolidada
            $venta = \App\Models\Venta::create([
                'numero_venta' => 'VTA-' . str_pad(\App\Models\Venta::count() + 1, 6, '0', STR_PAD_LEFT),
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'total' => $subtotal - $descuento,
                'estado' => '1',
                'tipo_pago' => $request->tipo_pago ?? 'efectivo',
                'observaciones' => "Cuenta completa - Cliente: {$cliente}. " .
                                 "Rondas: " . $rondas->pluck('numero_ronda')->implode(', ') .
                                 (count($mesasFinalizadas) > 0 ? " | Mesas: " . implode(', ', array_unique($mesasFinalizadas)) : "")
            ]);

            // Crear detalles por productos consumidos (agrupados si es el mismo producto)
            $productosAgrupados = [];
            foreach ($rondas as $ronda) {
                foreach ($ronda->detalles as $detalle) {
                    $productoId = $detalle->producto_id;
                    if (isset($productosAgrupados[$productoId])) {
                        $productosAgrupados[$productoId]['cantidad'] += $detalle->cantidad;
                        $productosAgrupados[$productoId]['subtotal'] += $detalle->subtotal;
                    } else {
                        $productosAgrupados[$productoId] = [
                            'producto_id' => $detalle->producto_id,
                            'cantidad' => $detalle->cantidad,
                            'precio_unitario' => $detalle->precio_unitario,
                            'subtotal' => $detalle->subtotal
                        ];
                    }
                }
            }

            // Crear detalles de venta para productos
            foreach ($productosAgrupados as $producto) {
                \App\Models\VentaDetalle::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $producto['producto_id'],
                    'cantidad' => $producto['cantidad'],
                    'precio_unitario' => $producto['precio_unitario'],
                    'subtotal' => $producto['subtotal']
                ]);
            }

            // Crear detalles individuales por tiempo de cada mesa
            foreach ($detallesMesas as $detalleMesa) {
                \App\Models\VentaDetalle::create([
                    'venta_id' => $venta->id,
                    'producto_id' => null, // Servicio de tiempo, no producto físico
                    'cantidad' => 1,
                    'precio_unitario' => $detalleMesa['costo'],
                    'subtotal' => $detalleMesa['costo'],
                    'descripcion' => "Tiempo de {$detalleMesa['mesa']} - Ronda #{$detalleMesa['ronda']} ({$detalleMesa['duracion_minutos']} min)"
                ]);
            }

            // Marcar todas las rondas como pagadas
            Ronda::whereIn('id', $rondas->pluck('id'))
                ->update(['estado' => 'pagada']);

            return response()->json([
                'success' => true,
                'message' => 'Cuenta completa cerrada exitosamente',
                'tipo' => 'cuenta_completa',
                'venta_id' => $venta->id,
                'numero_venta' => $venta->numero_venta,
                'total' => $venta->total,
                'desglose' => [
                    'productos' => $subtotalProductos,
                    'tiempo_mesas' => $costoTiempoTotal,
                    'descuento' => $descuento,
                    'rondas_pagadas' => $rondas->count(),
                    'mesas_utilizadas' => count($detallesMesas),
                    'detalle_rondas' => $rondas->pluck('numero_ronda'),
                    'detalle_mesas' => $detallesMesas
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar la cuenta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener resumen de venta para confirmación
     */
    public function resumenVenta($cliente = null)
    {
        try {
            if ($cliente) {
                // Resumen por cliente específico (para cuenta completa)
                return $this->resumenPorCliente($cliente);
            } else {
                // Resumen general de todos los clientes con rondas activas
                return $this->resumenGeneral();
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el resumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resumen detallado por cliente
     */
    private function resumenPorCliente($cliente)
    {
        $rondas = Ronda::with(['detalles.producto', 'mesaRonda.mesa'])
            ->where('cliente', $cliente)
            ->where('estado', 'activa')
            ->get();

        if ($rondas->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No hay rondas activas para este cliente'
            ], 404);
        }

        $resumen = [
            'cliente' => $cliente,
            'total_rondas' => $rondas->count(),
            'subtotal_productos' => $rondas->sum('total_ronda'),
            'tiempo_mesa_total' => 0,
            'rondas' => []
        ];

        foreach ($rondas as $ronda) {
            $rondaData = [
                'id' => $ronda->id,
                'numero_ronda' => $ronda->numero_ronda,
                'mesa' => $ronda->mesaRonda ? $ronda->mesaRonda->mesa->nombre : null,
                'subtotal' => $ronda->total_ronda,
                'tiempo_costo' => 0,
                'productos' => []
            ];

            // Calcular costo de tiempo si hay mesa activa
            if ($ronda->mesaRonda && $ronda->mesaRonda->isActivo()) {
                $rondaData['tiempo_costo'] = $ronda->mesaRonda->costo_actual;
                $rondaData['tiempo_minutos'] = $ronda->mesaRonda->duracion_actual;
                $resumen['tiempo_mesa_total'] += $rondaData['tiempo_costo'];
            }

            foreach ($ronda->detalles as $detalle) {
                $rondaData['productos'][] = [
                    'nombre' => $detalle->producto ? $detalle->producto->nombre : 'Producto eliminado',
                    'cantidad' => $detalle->cantidad,
                    'precio_unitario' => $detalle->precio_unitario,
                    'subtotal' => $detalle->subtotal
                ];
            }

            $resumen['rondas'][] = $rondaData;
        }

        $resumen['total_general'] = $resumen['subtotal_productos'] + $resumen['tiempo_mesa_total'];

        return response()->json([
            'success' => true,
            'tipo' => 'cliente_especifico',
            'resumen' => $resumen
        ]);
    }

    /**
     * Resumen general de todos los clientes
     */
    private function resumenGeneral()
    {
        $rondas = Ronda::with(['detalles.producto', 'mesaRonda.mesa'])
            ->where('estado', 'activa')
            ->get();

        if ($rondas->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No hay rondas activas'
            ], 404);
        }

        // Agrupar por cliente
        $clientesResumen = [];
        foreach ($rondas as $ronda) {
            $cliente = $ronda->cliente;
            
            if (!isset($clientesResumen[$cliente])) {
                $clientesResumen[$cliente] = [
                    'cliente' => $cliente,
                    'total_rondas' => 0,
                    'subtotal_productos' => 0,
                    'tiempo_mesa_total' => 0,
                    'rondas' => []
                ];
            }

            $clientesResumen[$cliente]['total_rondas']++;
            $clientesResumen[$cliente]['subtotal_productos'] += $ronda->total_ronda;

            $rondaData = [
                'id' => $ronda->id,
                'numero_ronda' => $ronda->numero_ronda,
                'mesa' => $ronda->mesaRonda ? $ronda->mesaRonda->mesa->nombre : null,
                'subtotal' => $ronda->total_ronda,
                'tiempo_costo' => 0,
                'fecha_inicio' => $ronda->created_at->format('H:i')
            ];

            // Calcular tiempo de mesa
            if ($ronda->mesaRonda && $ronda->mesaRonda->isActivo()) {
                $rondaData['tiempo_costo'] = $ronda->mesaRonda->costo_actual;
                $rondaData['tiempo_minutos'] = $ronda->mesaRonda->duracion_actual;
                $clientesResumen[$cliente]['tiempo_mesa_total'] += $rondaData['tiempo_costo'];
            }

            $clientesResumen[$cliente]['rondas'][] = $rondaData;
        }

        // Calcular totales por cliente
        foreach ($clientesResumen as &$cliente) {
            $cliente['total_general'] = $cliente['subtotal_productos'] + $cliente['tiempo_mesa_total'];
        }

        return response()->json([
            'success' => true,
            'tipo' => 'resumen_general',
            'clientes' => array_values($clientesResumen),
            'total_clientes' => count($clientesResumen),
            'total_rondas_activas' => $rondas->count()
        ]);
    }

    // Agregar producto a una ronda
    public function agregarProducto(Request $request, $pedidoId, $rondaId)
    {
        \Log::info('🚀 MÉTODO agregarProducto EJECUTADO - INICIO', [
            'pedido_id' => $pedidoId,
            'ronda_id' => $rondaId,
            'method' => $request->method(),
            'url' => $request->url(),
            'all_data' => $request->all()
        ]);
        
        try {
            \Log::info('Iniciando agregarProducto', [
                'pedido_id' => $pedidoId,
                'ronda_id' => $rondaId,
                'request_data' => $request->all()
            ]);
            
            $pedido = Pedido::findOrFail($pedidoId);
            $ronda = Ronda::findOrFail($rondaId);
            $producto = Producto::findOrFail($request->producto_id);
            
            \Log::info('Entidades encontradas', [
                'pedido_id' => $pedido->id,
                'ronda_id' => $ronda->id,
                'producto_id' => $producto->id
            ]);
            
            // Verificar stock si el producto no es servicio
            if (!$producto->es_servicio && $producto->stock < $request->cantidad) {
                \Log::warning('Stock insuficiente', [
                    'producto_id' => $producto->id,
                    'stock_actual' => $producto->stock,
                    'cantidad_solicitada' => $request->cantidad
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuficiente'
                ], 400);
            }
            
            // Validar request
            $validated = $request->validate([
                'producto_id' => 'required|exists:productos,id',
                'cantidad' => 'required|numeric|min:0.01',
                'costo_unitario' => 'required|numeric|min:0'
            ]);
            
            \Log::info('Validación exitosa', ['validated' => $validated]);
            
            // Preparar datos para crear detalle
            $detalleData = [
                'ronda_id' => $ronda->id,
                'producto_id' => $request->producto_id,
                'nombre_producto' => $producto->nombre,
                'cantidad' => $request->cantidad,
                'precio_unitario' => $request->costo_unitario,
                'subtotal' => $request->cantidad * $request->costo_unitario
            ];
            
            \Log::info('Datos para crear detalle', ['detalle_data' => $detalleData]);
            
            // Crear detalle de ronda
            $detalle = RondaDetalle::create($detalleData);
            
            \Log::info('Detalle creado', [
                'detalle_id' => $detalle->id ?? 'NO_ID',
                'detalle_data' => $detalle->toArray() ?? 'NO_DATA'
            ]);
            
            // Actualizar stock si no es servicio
            if (!$producto->es_servicio) {
                $producto->decrement('stock', $request->cantidad);
                \Log::info('Stock actualizado', [
                    'producto_id' => $producto->id,
                    'nuevo_stock' => $producto->fresh()->stock
                ]);
            }
            
            // Recalcular total de la ronda
            $totalAnterior = $ronda->total;
            $ronda->total = $ronda->detalles()->sum('subtotal');
            $ronda->save();
            
            \Log::info('Total de ronda actualizado', [
                'ronda_id' => $ronda->id,
                'total_anterior' => $totalAnterior,
                'total_nuevo' => $ronda->total
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Producto agregado correctamente',
                'detalle' => $detalle
            ]);
            
        } catch (Exception $e) {
            \Log::error('Error en agregarProducto', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al agregar producto: ' . $e->getMessage()
            ], 500);
        }
    }

    // Eliminar detalle de producto de una ronda
    public function eliminarDetalle($detalleId)
    {
        try {
            $detalle = \App\Models\RondaDetalle::findOrFail($detalleId);
            $ronda = $detalle->ronda;
            $producto = $detalle->producto;

            // Si no era un descuento, restaurar el stock
            if (!$detalle->es_descuento && $producto) {
                $producto->increment('stock_actual', $detalle->cantidad);
            }

            // Eliminar el detalle
            $detalle->delete();

            // Recalcular total de la ronda
            $nuevoTotal = \App\Models\RondaDetalle::where('ronda_id', $ronda->id)->sum('subtotal');
            
            // Agregar costo de tiempo de mesa si existe
            if ($ronda->mesaRonda && $ronda->mesaRonda->costo_tiempo > 0) {
                $nuevoTotal += $ronda->mesaRonda->costo_tiempo;
            }

            $ronda->update(['total_ronda' => $nuevoTotal]);

            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado exitosamente',
                'nuevo_total' => $nuevoTotal
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar producto: ' . $e->getMessage()
            ], 500);
        }
    }
}
