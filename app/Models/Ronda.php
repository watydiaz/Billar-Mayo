<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Ronda extends Model
{
    protected $table = 'rondas';
    
    protected $fillable = [
        'numero_ronda',
        'cliente',
        'total',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'numero_ronda' => 'string',
        'total' => 'decimal:2'
    ];

    // Las rondas ahora son independientes, sin relación con pedidos
    
    // Atributos virtuales para compatibilidad con vistas de pedidos
    public function getNombreClienteAttribute()
    {
        return $this->cliente;
    }

    public function getNumeroAttribute()
    {
        return $this->numero_ronda;
    }

    public function getTotalPedidoAttribute()
    {
        return $this->total_ronda;
    }

    public function getNumeroPedidoAttribute()
    {
        return $this->numero_ronda;
    }

    // Relación simulada con rondas (para compatibilidad)
    public function getRondasAttribute()
    {
        return collect([$this]);
    }

    // Relación con MesaRonda
    public function mesaRonda(): HasOne
    {
        return $this->hasOne(MesaRonda::class);
    }

    // Relación con detalles de ronda
    public function detalles(): HasMany
    {
        return $this->hasMany(RondaDetalle::class);
    }

    // Verificar si tiene tiempo de mesa activo
    public function tieneTiempoActivo(): bool
    {
        return $this->mesaRonda && $this->mesaRonda->isActivo();
    }

    // Obtener mesa asignada
    public function getMesaAttribute()
    {
        return $this->mesaRonda ? $this->mesaRonda->mesa : null;
    }
}
