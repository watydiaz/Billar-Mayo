<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentaDetalle extends Model
{
    use HasFactory;

    protected $fillable = [
        'venta_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'descripcion'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    // Relación con venta
    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    // Relación con producto
    public function producto()
    {
        return $this->belongsTo(Product::class, 'producto_id');
    }

    // Calcular subtotal automáticamente
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($detalle) {
            $detalle->subtotal = $detalle->cantidad * $detalle->precio_unitario;
        });
    }
}