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
        'precio',
        'stock',
        'stock_minimo',
        'categoria',
        'es_servicio',
        'activo'
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'stock' => 'integer',
        'stock_minimo' => 'integer',
        'es_servicio' => 'boolean',
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
