<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Mesa;
use App\Models\MesaAlquiler;
use App\Models\Ronda;
use App\Models\Producto;

class PedidoController extends Controller
{
    public function index()
    {
        $pedidos = Pedido::with(['mesa', 'rondas.producto'])
            ->whereIn('estado', ['abierto', 'en_mesa'])
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

        // Si se seleccionó una mesa, crear el alquiler
        if ($request->mesa_id) {
            MesaAlquiler::create([
                'pedido_id' => $pedido->id,
                'mesa_id' => $request->mesa_id,
                'estado' => 'reservado',
                'precio_hora_aplicado' => Mesa::find($request->mesa_id)->precio_hora ?? 0
            ]);
        }

        return redirect()->back()->with('success', 'Pedido creado exitosamente');
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
            'responsable' => 'required|string|max:255',
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1'
        ]);

        $producto = Producto::findOrFail($request->producto_id);
        $subtotal = $producto->precio_venta * $request->cantidad;

        Ronda::create([
            'pedido_id' => $pedido->id,
            'responsable' => $request->responsable,
            'producto_id' => $request->producto_id,
            'cantidad' => $request->cantidad,
            'precio_unitario' => $producto->precio_venta,
            'subtotal' => $subtotal
        ]);

        // Actualizar total del pedido
        $pedido->update([
            'total' => $pedido->rondas()->sum('subtotal')
        ]);

        return redirect()->back()->with('success', 'Ronda agregada exitosamente');
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
