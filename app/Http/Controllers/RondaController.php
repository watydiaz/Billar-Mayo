<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ronda;
use App\Models\Mesa;
use App\Models\MesaRonda;
use App\Models\Pedido;

class RondaController extends Controller
{
    // Vista principal de rondas
    public function index()
    {
        $rondasActivas = Ronda::with(['pedido', 'mesaRonda.mesa'])
            ->where('estado', 'activa')
            ->orderBy('created_at', 'desc')
            ->get();

        $pedidos = Pedido::where('estado', '1')->get();
        $mesas = Mesa::where('activa', true)->get();

        // Estadísticas
        $mesasOcupadas = MesaRonda::where('estado', 'activo')->count();
        $tiempoTotalHoy = MesaRonda::whereDate('created_at', today())
            ->where('estado', 'finalizado')
            ->sum('duracion_minutos');
        $ingresosTiempoHoy = MesaRonda::whereDate('created_at', today())
            ->where('estado', 'finalizado')
            ->sum('costo_tiempo');

        return view('rondas.index', compact(
            'rondasActivas',
            'pedidos', 
            'mesas',
            'mesasOcupadas',
            'tiempoTotalHoy',
            'ingresosTiempoHoy'
        ));
    }

    // Crear nueva ronda
    public function store(Request $request)
    {
        $request->validate([
            'pedido_id' => 'required|exists:pedidos,id',
            'numero_ronda' => 'required|integer',
            'responsable' => 'required|string|max:255',
        ]);

        $ronda = Ronda::create([
            'pedido_id' => $request->pedido_id,
            'numero_ronda' => $request->numero_ronda,
            'responsable' => $request->responsable,
            'total_ronda' => 0,
            'estado' => 'activa'
        ]);

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
}
