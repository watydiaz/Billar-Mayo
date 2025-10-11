<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PagoDetalle extends Model
{
    protected $table = 'pago_detalles';
    
    protected $fillable = [
        'pago_id',
        'producto_id',
        'nombre_producto',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'notas'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    // Relación con pago
    public function pago(): BelongsTo
    {
        return $this->belongsTo(Pago::class);
    }

    // Relación con producto
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}
