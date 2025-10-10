<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class MesaRonda extends Model
{
    protected $table = 'mesa_rondas';
    
    protected $fillable = [
        'ronda_id',
        'mesa_id',
        'inicio_tiempo',
        'fin_tiempo',
        'duracion_minutos',
        'costo_tiempo',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'inicio_tiempo' => 'datetime',
        'fin_tiempo' => 'datetime',
        'duracion_minutos' => 'integer',
        'costo_tiempo' => 'decimal:2'
    ];

    // Relación con Ronda
    public function ronda(): BelongsTo
    {
        return $this->belongsTo(Ronda::class);
    }

    // Relación con Mesa
    public function mesa(): BelongsTo
    {
        return $this->belongsTo(Mesa::class);
    }

    // Calcular duración en tiempo real (en minutos)
    public function getDuracionActualAttribute()
    {
        if (!$this->inicio_tiempo) {
            return 0;
        }

        $fin = $this->fin_tiempo ? Carbon::parse($this->fin_tiempo) : now();
        return Carbon::parse($this->inicio_tiempo)->diffInMinutes($fin);
    }

    // Calcular duración en segundos para el temporizador
    public function getDuracionSegundosAttribute()
    {
        if (!$this->inicio_tiempo) {
            return 0;
        }

        $fin = $this->fin_tiempo ? Carbon::parse($this->fin_tiempo) : now();
        return Carbon::parse($this->inicio_tiempo)->diffInSeconds($fin);
    }

    // Calcular costo en tiempo real
    public function getCostoActualAttribute()
    {
        $duracionMinutos = $this->duracion_actual;
        $mesa = $this->mesa;
        
        if (!$mesa || !$duracionMinutos) {
            return 0;
        }
        
        // Obtener precio por hora y convertir a por minuto
        $precioHora = $mesa->precio_hora ?? 0;
        $precioPorMinuto = $precioHora / 60;
        
        return round($duracionMinutos * $precioPorMinuto, 2);
    }

    // Verificar si está activo
    public function isActivo(): bool
    {
        return $this->estado === 'activo' && $this->inicio_tiempo && !$this->fin_tiempo;
    }

    // Iniciar tiempo
    public function iniciar()
    {
        $this->update([
            'inicio_tiempo' => now(),
            'estado' => 'activo'
        ]);
    }

    // Finalizar tiempo
    public function finalizar()
    {
        $this->update([
            'fin_tiempo' => now(),
            'duracion_minutos' => $this->duracion_actual,
            'costo_tiempo' => $this->costo_actual,
            'estado' => 'finalizado'
        ]);
    }
}