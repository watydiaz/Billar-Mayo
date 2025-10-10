<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RondaDetalle extends Model
{
    protected $table = 'ronda_detalles';
    
    protected $fillable = [
        'ronda_id',
        'producto_id',
        'nombre_producto',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'es_descuento',
        'es_producto_personalizado',
        'notas'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'es_descuento' => 'boolean',
        'es_producto_personalizado' => 'boolean'
    ];

    // Relación con Ronda
    public function ronda(): BelongsTo
    {
        return $this->belongsTo(Ronda::class);
    }

    // Relación con Producto
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}