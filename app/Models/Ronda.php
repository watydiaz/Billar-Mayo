<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ronda extends Model
{
    protected $table = 'rondas';
    
    protected $fillable = [
        'pedido_id',
        'responsable',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'observaciones'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2'
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
}
