<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pedido extends Model
{
    protected $table = 'pedidos';
    
    protected $fillable = [
        'numero_pedido',
        'nombre_cliente',
        'estado',
        'total_pedido',
        'notas'
    ];

    protected $casts = [
        'total_pedido' => 'decimal:2'
    ];

    // Accessors para mantener compatibilidad
    public function getTotalAttribute()
    {
        return $this->total_pedido;
    }

    public function getObservacionesAttribute()
    {
        return $this->notas;
    }

    // Relación con Mesa a través de mesa_alquileres
    public function mesaAlquiler(): HasMany
    {
        return $this->hasMany(MesaAlquiler::class);
    }

    public function mesaAlquilerActivo()
    {
        return $this->mesaAlquiler()->whereIn('estado', ['activo', 'en_proceso'])->first();
    }

    public function mesa()
    {
        $alquiler = $this->mesaAlquilerActivo();
        return $alquiler ? $alquiler->mesa : null;
    }

    // Relación con Rondas
    public function rondas(): HasMany
    {
        return $this->hasMany(Ronda::class);
    }

    // Métodos auxiliares
    public function getTiempoTranscurridoAttribute()
    {
        $alquiler = $this->mesaAlquilerActivo();
        if (!$alquiler || !$alquiler->fecha_inicio) {
            return 0;
        }
        
        return $alquiler->tiempo_transcurrido;
    }

    public function getTiempoInicioAttribute()
    {
        $alquiler = $this->mesaAlquilerActivo();
        return $alquiler ? $alquiler->fecha_inicio : null;
    }

    public function getEstadoBadgeAttribute()
    {
        return match($this->estado) {
            'abierto' => 'bg-warning',
            'en_mesa' => 'bg-success',
            'finalizado' => 'bg-secondary',
            'cancelado' => 'bg-danger',
            default => 'bg-primary'
        };
    }
}
