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
        'pedido_id',
        'numero_ronda',
        'total_ronda',
        'responsable',
        'estado',
        'es_duplicada',
        'ronda_origen_id'
    ];

    protected $casts = [
        'numero_ronda' => 'integer',
        'total_ronda' => 'decimal:2',
        'es_duplicada' => 'boolean'
    ];

    // Relación con Pedido
    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    // Relación con Producto
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
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
