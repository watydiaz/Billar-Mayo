<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    protected $table = 'categorias';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'estado'
    ];

    // Relación con Productos
    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class);
    }
}
