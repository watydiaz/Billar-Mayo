<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MesaAlquiler extends Model
{
    protected $table = 'mesa_alquileres';
    
    protected $fillable = [
        'pedido_id',
        'mesa_id',
        'fecha_inicio',
        'fecha_fin',
        'tiempo_minutos',
        'tiempo_segundos',
        'costo_total',
        'precio_hora_aplicado',
        'estado',
        'contador_pausas',
        'tiempo_pausado_minutos',
        'notas'
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'tiempo_minutos' => 'integer',
        'tiempo_segundos' => 'integer',
        'costo_total' => 'decimal:2',
        'precio_hora_aplicado' => 'decimal:2',
        'contador_pausas' => 'integer',
        'tiempo_pausado_minutos' => 'integer'
    ];

    // Relaciones
    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    public function mesa(): BelongsTo
    {
        return $this->belongsTo(Mesa::class);
    }

    // Métodos auxiliares
    public function getTiempoTranscurridoAttribute()
    {
        if (!$this->fecha_inicio) {
            return 0;
        }
        
        $fin = $this->fecha_fin ?? now();
        return $this->fecha_inicio->diffInMinutes($fin);
    }
}
