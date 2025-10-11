<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Mesa;
use App\Models\MesaAlquiler;
use App\Models\Ronda;
use App\Models\Producto;
use App\Models\MesaRonda;

class PedidoController extends Controller
{
    public function index()
    {
        // Cargar pedidos con sus rondas y relaciones
        $pedidos = Pedido::with(['mesaAlquileres.mesa', 'rondas.mesaRonda.mesa', 'rondas.detalles.producto'])
            ->where('estado', '1')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $mesas = Mesa::where('activa', true)->get();
        $productos = Producto::with('categoria')->where('activo', true)->get();
        
        return view('pedidos.index', compact('pedidos', 'mesas', 'productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_cliente' => 'required|string|max:255',
            'mesa_id' => 'nullable|exists:mesas,id'
        ]);

        // Generar número de pedido único
        $numeroPedido = 'P' . date('Ymd') . '-' . str_pad(Pedido::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT);

        $pedido = Pedido::create([
            'numero_pedido' => $numeroPedido,
            'nombre_cliente' => $request->nombre_cliente,
            'estado' => '1',
            'total_pedido' => 0
        ]);

        // Si se seleccionó una mesa, crear el alquiler (sin ronda por ahora)
        if ($request->mesa_id) {
            MesaAlquiler::create([
                'pedido_id' => $pedido->id,
                'mesa_id' => $request->mesa_id,
                'estado' => 'activo',
                'fecha_inicio' => now(),
                'precio_hora_aplicado' => Mesa::find($request->mesa_id)->precio_hora ?? 0
            ]);
        }

        return redirect()->back()->with([
            'success' => 'Pedido creado exitosamente',
            'nuevo_pedido_id' => $pedido->id,
            'mostrar_modal_nueva_ronda' => true
        ]);
    }

    public function iniciarTiempo(Pedido $pedido)
    {
        $alquiler = $pedido->mesaAlquilerActivo();
        
        if ($alquiler) {
            $alquiler->update([
                'fecha_inicio' => now(),
                'estado' => 'activo'
            ]);
            
            $pedido->update(['estado' => 'en_mesa']);
        }

        return redirect()->back()->with('success', 'Tiempo iniciado en la mesa');
    }

    public function finalizarTiempo(Pedido $pedido)
    {
        $alquiler = $pedido->mesaAlquilerActivo();
        
        if ($alquiler) {
            $tiempoTranscurrido = $alquiler->fecha_inicio->diffInMinutes(now());
            $costoTotal = ($tiempoTranscurrido / 60) * $alquiler->precio_hora_aplicado;
            
            $alquiler->update([
                'fecha_fin' => now(),
                'tiempo_minutos' => $tiempoTranscurrido,
                'costo_total' => $costoTotal,
                'estado' => 'finalizado'
            ]);
            
            $pedido->update(['estado' => '0']);
        }

        return redirect()->back()->with('success', 'Tiempo finalizado');
    }

    public function agregarRonda(Request $request, Pedido $pedido)
    {
        $request->validate([
            'mesa_id' => 'nullable|exists:mesas,id',
            'iniciar_tiempo' => 'nullable|boolean'
        ]);

        // Obtener el siguiente número de ronda
        $numeroRonda = $pedido->rondas()->max('numero_ronda') + 1;

        $ronda = Ronda::create([
            'pedido_id' => $pedido->id,
            'numero_ronda' => $numeroRonda,
            'total_ronda' => 0,
            'estado' => 'activa'
        ]);

        $mensaje = 'Ronda ' . $numeroRonda . ' creada exitosamente';

        // Si se seleccionó una mesa
        if ($request->mesa_id) {
            // Verificar que la mesa no esté ocupada
            $mesaOcupada = MesaRonda::where('mesa_id', $request->mesa_id)
                ->where('estado', 'activo')
                ->exists();
                
            $mesaOcupadaAlquiler = MesaAlquiler::where('mesa_id', $request->mesa_id)
                ->where('estado', 'activo')
                ->exists();

            if ($mesaOcupada || $mesaOcupadaAlquiler) {
                return redirect()->back()->with('error', 'La mesa seleccionada ya está ocupada');
            }

            $mesa = Mesa::find($request->mesa_id);

            // Crear registro en mesa_rondas
            $mesaRonda = MesaRonda::create([
                'ronda_id' => $ronda->id,
                'mesa_id' => $request->mesa_id,
                'estado' => 'pendiente'
            ]);

            // Crear registro en mesa_alquileres (compatibilidad)
            MesaAlquiler::create([
                'pedido_id' => $pedido->id,
                'mesa_id' => $request->mesa_id,
                'estado' => 'activo',
                'fecha_inicio' => now(),
                'precio_hora_aplicado' => $mesa->precio_hora ?? 0
            ]);

            $mensaje .= ' y mesa ' . $mesa->numero_mesa . ' asignada';

            // Si se solicitó iniciar tiempo automáticamente
            if ($request->iniciar_tiempo) {
                $mesaRonda->iniciar();
                
                // Actualizar mesa_alquileres
                $alquiler = MesaAlquiler::where('pedido_id', $pedido->id)
                    ->where('mesa_id', $request->mesa_id)
                    ->first();
                    
                if ($alquiler) {
                    $alquiler->update([
                        'fecha_inicio' => now(),
                        'estado' => 'activo'
                    ]);
                }

                $mensaje .= '. ¡Tiempo iniciado!';
            }
        }

        return redirect()->back()->with('success', $mensaje);
    }

    // Asignar mesa a una ronda del pedido
    public function asignarMesaRonda(Request $request, Pedido $pedido, Ronda $ronda)
    {
        $request->validate([
            'mesa_id' => 'required|exists:mesas,id',
        ]);

        // Verificar que la ronda pertenece al pedido
        if ($ronda->pedido_id !== $pedido->id) {
            return redirect()->back()->with('error', 'Ronda no válida para este pedido');
        }

        // Verificar que la mesa no esté ocupada (tanto en mesa_rondas como mesa_alquileres)
        $mesaOcupadaRonda = MesaRonda::where('mesa_id', $request->mesa_id)
            ->where('estado', 'activo')
            ->exists();
            
        $mesaOcupadaAlquiler = MesaAlquiler::where('mesa_id', $request->mesa_id)
            ->where('estado', 'activo')
            ->exists();

        if ($mesaOcupadaRonda || $mesaOcupadaAlquiler) {
            return redirect()->back()->with('error', 'La mesa ya está ocupada');
        }

        $mesa = Mesa::find($request->mesa_id);

        // Crear registro en mesa_rondas (nueva estructura)
        MesaRonda::updateOrCreate(
            ['ronda_id' => $ronda->id],
            [
                'mesa_id' => $request->mesa_id,
                'estado' => 'pendiente'
            ]
        );

        // Crear registro en mesa_alquileres (compatibilidad)
        MesaAlquiler::updateOrCreate(
            ['pedido_id' => $pedido->id, 'mesa_id' => $request->mesa_id],
            [
                'estado' => 'activo',
                'fecha_inicio' => now(),
                'precio_hora_aplicado' => $mesa->precio_hora ?? 0
            ]
        );

        return redirect()->back()->with('success', 'Mesa ' . $mesa->numero_mesa . ' asignada a la ronda ' . $ronda->numero_ronda);
    }

    // Iniciar tiempo de una ronda
    public function iniciarTiempoRonda(Pedido $pedido, Ronda $ronda)
    {
        if ($ronda->pedido_id !== $pedido->id) {
            return redirect()->back()->with('error', 'Ronda no válida para este pedido');
        }

        $mesaRonda = $ronda->mesaRonda;

        if (!$mesaRonda) {
            return redirect()->back()->with('error', 'Debe asignar una mesa antes de iniciar el tiempo');
        }

        if ($mesaRonda->estado === 'activo') {
            return redirect()->back()->with('error', 'El tiempo ya está activo para esta ronda');
        }

        // Iniciar tiempo en mesa_rondas
        $mesaRonda->iniciar();

        // Actualizar mesa_alquileres para compatibilidad
        $alquiler = MesaAlquiler::where('pedido_id', $pedido->id)
            ->where('mesa_id', $mesaRonda->mesa_id)
            ->first();
            
        if ($alquiler) {
            $alquiler->update([
                'fecha_inicio' => now(),
                'estado' => 'activo'
            ]);
        }

        return redirect()->back()->with('success', 'Tiempo iniciado para la ronda ' . $ronda->numero_ronda);
    }

    // Finalizar tiempo de una ronda
    public function finalizarTiempoRonda(Pedido $pedido, Ronda $ronda)
    {
        if ($ronda->pedido_id !== $pedido->id) {
            return redirect()->back()->with('error', 'Ronda no válida para este pedido');
        }

        $mesaRonda = $ronda->mesaRonda;

        if (!$mesaRonda || $mesaRonda->estado !== 'activo') {
            return redirect()->back()->with('error', 'No hay tiempo activo para esta ronda');
        }

        // Finalizar tiempo en mesa_rondas
        $mesaRonda->finalizar();

        // Actualizar mesa_alquileres para compatibilidad
        $alquiler = MesaAlquiler::where('pedido_id', $pedido->id)
            ->where('mesa_id', $mesaRonda->mesa_id)
            ->where('estado', 'activo')
            ->first();
            
        if ($alquiler) {
            $tiempoTranscurrido = $mesaRonda->duracion_minutos;
            $costoTotal = $mesaRonda->costo_tiempo;
            
            $alquiler->update([
                'fecha_fin' => now(),
                'tiempo_minutos' => $tiempoTranscurrido,
                'costo_total' => $costoTotal,
                'estado' => 'terminado'  // Corregido: usar valor válido del ENUM
            ]);
        }

        // Actualizar el total de la ronda sumando el costo del tiempo
        $ronda->update([
            'total_ronda' => $ronda->total_ronda + $mesaRonda->costo_tiempo
        ]);

        // Actualizar total del pedido
        $pedido->update([
            'total_pedido' => $pedido->rondas()->sum('total_ronda')
        ]);

        return redirect()->back()->with('success', 'Tiempo finalizado para la ronda ' . $ronda->numero_ronda);
    }

    // Asignar responsable a una ronda finalizada
    public function asignarResponsable(Request $request, Pedido $pedido, Ronda $ronda)
    {
        $request->validate([
            'responsable' => 'required|string|max:255'
        ]);

        if ($ronda->pedido_id !== $pedido->id) {
            return redirect()->back()->with('error', 'Ronda no válida para este pedido');
        }

        // Solo se puede asignar responsable si la ronda está finalizada
        $mesaRonda = $ronda->mesaRonda;
        if (!$mesaRonda || $mesaRonda->estado !== 'finalizado') {
            return redirect()->back()->with('error', 'Solo se puede asignar responsable a rondas finalizadas');
        }

        $ronda->update([
            'responsable' => $request->responsable,
            'estado' => 'pagada'
        ]);

        return redirect()->back()->with('success', 'Responsable asignado: ' . $request->responsable);
    }

    // API para obtener datos de tiempo en tiempo real
    public function tiempoRealRonda(Pedido $pedido, Ronda $ronda)
    {
        if ($ronda->pedido_id !== $pedido->id) {
            return response()->json(['error' => 'Ronda no válida'], 400);
        }

        $mesaRonda = $ronda->mesaRonda;

        if (!$mesaRonda || $mesaRonda->estado !== 'activo') {
            return response()->json([
                'activo' => false,
                'duracion_segundos' => 0,
                'duracion_minutos' => 0,
                'costo' => 0,
                'debug_info' => [
                    'tiene_mesa_ronda' => !!$mesaRonda,
                    'estado' => $mesaRonda ? $mesaRonda->estado : null,
                    'ronda_id' => $ronda->id,
                    'pedido_id' => $pedido->id
                ]
            ]);
        }

        return response()->json([
            'activo' => true,
            'duracion_segundos' => $mesaRonda->duracion_segundos,
            'duracion_minutos' => $mesaRonda->duracion_actual,
            'costo' => $mesaRonda->costo_actual,
            'inicio' => $mesaRonda->inicio_tiempo->format('H:i:s'),
            'inicio_timestamp' => $mesaRonda->inicio_tiempo->timestamp,
            'precio_por_hora' => $mesaRonda->mesa->precio_hora ?? 0,
            'precio_por_minuto' => ($mesaRonda->mesa->precio_hora ?? 0) / 60,
            'debug_info' => [
                'mesa_id' => $mesaRonda->mesa_id,
                'inicio_tiempo' => $mesaRonda->inicio_tiempo->toISOString(),
                'estado' => $mesaRonda->estado
            ]
        ]);
    }

    // Endpoint optimizado para obtener todos los timers activos de una vez
    public function timersActivos()
    {
        try {
            $timersActivos = [];
            
            // Obtener todas las mesa_rondas activas con sus relaciones
            $mesasRondas = MesaRonda::with(['ronda.pedido', 'mesa'])
                ->where('estado', 'activo')
                ->whereNotNull('fecha_inicio')
                ->get();
            
            foreach ($mesasRondas as $mesaRonda) {
                if ($mesaRonda->ronda && $mesaRonda->ronda->pedido) {
                    $timersActivos[] = [
                        'pedido_id' => $mesaRonda->ronda->pedido->id,
                        'ronda_id' => $mesaRonda->ronda->id,
                        'duracion_segundos' => $mesaRonda->duracion_segundos,
                        'costo' => $mesaRonda->costo_actual,
                        'mesa_numero' => $mesaRonda->mesa->numero_mesa ?? 'N/A',
                        'precio_por_minuto' => $mesaRonda->mesa->precio_por_minuto ?? 0
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'timers' => $timersActivos,
                'timestamp' => now()->timestamp,
                'total_activos' => count($timersActivos)
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error en timersActivos: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'timers' => []
            ], 500);
        }
    }

    // Agregar productos a una ronda
    public function agregarProductos(Request $request, Pedido $pedido, Ronda $ronda)
    {
        \Log::info('Intentando agregar productos', [
            'pedido_id' => $pedido->id,
            'ronda_id' => $ronda->id,
            'request_data' => $request->all()
        ]);

        if ($ronda->pedido_id !== $pedido->id) {
            \Log::error('Ronda no válida para pedido', [
                'ronda_pedido_id' => $ronda->pedido_id,
                'pedido_id' => $pedido->id
            ]);
            return redirect()->back()->with('error', 'Ronda no válida para este pedido');
        }

        // Debug de productos recibidos
        if (!$request->has('productos') || empty($request->productos)) {
            \Log::error('No se recibieron productos', ['request' => $request->all()]);
            return redirect()->back()->with('error', 'No se recibieron productos para agregar');
        }

        // Filtrar productos válidos (que tengan producto_id seleccionado)
        $productosValidos = [];
        foreach ($request->productos as $index => $producto) {
            if (!empty($producto['producto_id']) && !empty($producto['nombre_producto'])) {
                $productosValidos[] = $producto;
            }
        }

        if (empty($productosValidos)) {
            return redirect()->back()->with('error', 'No hay productos válidos seleccionados');
        }

        \Log::info('Productos válidos encontrados', ['count' => count($productosValidos), 'productos' => $productosValidos]);

        $request->validate([
            'productos' => 'required|array',
            'productos.*.producto_id' => 'nullable|exists:productos,id',
            'productos.*.nombre_producto' => 'required|string|max:300',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio_unitario' => 'required|numeric|min:0',
            'productos.*.notas' => 'nullable|string'
        ]);

        $totalAgregado = 0;

        foreach ($productosValidos as $productoData) {
            $subtotal = $productoData['cantidad'] * $productoData['precio_unitario'];
            
            \Log::info('Creando producto', [
                'ronda_id' => $ronda->id,
                'producto_data' => $productoData,
                'subtotal' => $subtotal
            ]);
            
            $detalle = RondaDetalle::create([
                'ronda_id' => $ronda->id,
                'producto_id' => $productoData['producto_id'] ?: null,
                'nombre_producto' => $productoData['nombre_producto'],
                'cantidad' => $productoData['cantidad'],
                'precio_unitario' => $productoData['precio_unitario'],
                'subtotal' => $subtotal,
                'es_descuento' => false,
                'es_producto_personalizado' => empty($productoData['producto_id']),
                'notas' => $productoData['notas'] ?? null
            ]);

            \Log::info('Producto creado', ['detalle_id' => $detalle->id]);
            
            $totalAgregado += $subtotal;
        }

        // Actualizar total de la ronda
        $ronda->update([
            'total_ronda' => $ronda->total_ronda + $totalAgregado
        ]);

        // Actualizar total del pedido
        $pedido->update([
            'total_pedido' => $pedido->rondas()->sum('total_ronda')
        ]);

        \Log::info('Productos agregados exitosamente', [
            'total_agregado' => $totalAgregado,
            'nuevo_total_ronda' => $ronda->total_ronda,
            'productos_count' => count($productosValidos)
        ]);

        return redirect()->back()->with('success', 'Se agregaron ' . count($productosValidos) . ' productos correctamente a la ronda ' . $ronda->numero_ronda . ' por un total de $' . number_format($totalAgregado, 0, ',', '.'));
    }

    public function eliminar(Pedido $pedido)
    {
        // Liberar mesa si está ocupada
        if ($pedido->mesa && $pedido->estado == 'en_mesa') {
            $pedido->mesa->update(['estado' => 'disponible']);
        }

        $pedido->delete();
        return redirect()->back()->with('success', 'Pedido eliminado');
    }
}
