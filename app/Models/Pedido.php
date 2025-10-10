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

    public function getEstadoTextoAttribute()
    {
        return $this->estado == '1' ? 'Activo' : 'Cerrado';
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', '1');
    }

    public function scopeCerrados($query)
    {
        return $query->where('estado', '0');
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

    public function mesaAlquileres(): HasMany
    {
        return $this->hasMany(MesaAlquiler::class);
    }

    public function mesaAlquilerActivo()
    {
        return $this->mesaAlquileres()->where('estado', 'activo')->first();
    }

    public function getMesaAttribute()
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
        
        // Calcular tiempo transcurrido desde la fecha de inicio hasta ahora
        return $alquiler->fecha_inicio->diffInMinutes(now());
    }

    public function getTiempoInicioAttribute()
    {
        $alquiler = $this->mesaAlquilerActivo();
        return $alquiler ? $alquiler->fecha_inicio : null;
    }

    // Cálculo de costo actual del tiempo
    public function getCostoTiempoActualAttribute()
    {
        $alquiler = $this->mesaAlquilerActivo();
        if (!$alquiler || !$alquiler->fecha_inicio) {
            return 0;
        }

        $minutosTranscurridos = $this->tiempo_transcurrido;
        $costoPorMinuto = $alquiler->precio_hora_aplicado / 60;
        
        return $minutosTranscurridos * $costoPorMinuto;
    }

    // Debug method
    public function debug()
    {
        $alquiler = $this->mesaAlquilerActivo();
        return [
            'pedido_id' => $this->id,
            'alquileres_count' => $this->mesaAlquileres->count(),
            'alquiler_activo' => $alquiler ? $alquiler->toArray() : null,
            'mesa' => $alquiler && $alquiler->mesa ? $alquiler->mesa->toArray() : null
        ];
    }

    public function getEstadoBadgeAttribute()
    {
        return match($this->estado) {
            '1' => 'bg-success',
            '0' => 'bg-secondary',
            default => 'bg-primary'
        };
    }
}
