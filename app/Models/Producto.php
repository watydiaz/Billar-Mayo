<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Producto extends Model
{
    protected $table = 'productos';
    
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'categoria_id',
        'precio_venta',
        'precio_costo',
        'stock_actual',
        'stock_minimo',
        'unidad_medida',
        'activo',
        'imagen_url'
    ];

    protected $casts = [
        'precio_venta' => 'decimal:2',
        'precio_costo' => 'decimal:2',
        'stock_actual' => 'integer',
        'stock_minimo' => 'integer',
        'activo' => 'boolean'
    ];

    // Accessor para mantener compatibilidad
    public function getPrecioAttribute()
    {
        return $this->precio_venta;
    }

    // Relación con Categoria
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }
}
