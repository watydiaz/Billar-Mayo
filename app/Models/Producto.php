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
        'precio_compra',
        'precio_venta',
        'imagen_url',
        'stock',
        'stock_minimo',
        'categoria_id',
        'es_servicio',
        'activo'
    ];

    protected $casts = [
        'precio_compra' => 'decimal:2',
        'precio_venta' => 'decimal:2',
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

    // Calcular margen de ganancia
    public function getMargenGananciaAttribute()
    {
        if (!$this->precio_compra || !$this->precio_venta) {
            return null;
        }
        
        return (($this->precio_venta - $this->precio_compra) / $this->precio_compra) * 100;
    }

    // Relación con Categoria
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }
}
