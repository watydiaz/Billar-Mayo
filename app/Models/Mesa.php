<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mesa extends Model
{
    protected $table = 'mesas';
    
    protected $fillable = [
        'numero_mesa',
        'capacidad',
        'precio_hora',
        'activa',
        'descripcion'
    ];

    protected $casts = [
        'precio_hora' => 'decimal:2',
        'capacidad' => 'integer',
        'activa' => 'boolean'
    ];

    // Accessors para mantener compatibilidad
    public function getNumeroAttribute()
    {
        return $this->numero_mesa;
    }

    public function getNombreAttribute()
    {
        return "Mesa {$this->numero_mesa}";
    }

    public function getEstadoAttribute()
    {
        return $this->activa ? 'disponible' : 'inactiva';
    }

    // Relación con Pedidos
    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }

    // Pedido activo en la mesa
    public function pedidoActivo()
    {
        return $this->pedidos()->whereIn('estado', ['abierto', 'en_mesa'])->first();
    }

    public function getEstadoBadgeAttribute()
    {
        // Verificar si hay pedido activo
        $pedidoActivo = $this->pedidoActivo();
        
        if ($pedidoActivo) {
            return match($pedidoActivo->estado) {
                'en_mesa' => 'bg-danger', // Ocupada
                'abierto' => 'bg-warning', // Reservada
                default => 'bg-success'
            };
        }
        
        return $this->activa ? 'bg-success' : 'bg-secondary';
    }

    public function getEstadoTextoAttribute()
    {
        $pedidoActivo = $this->pedidoActivo();
        
        if ($pedidoActivo) {
            return match($pedidoActivo->estado) {
                'en_mesa' => 'Ocupada',
                'abierto' => 'Reservada',
                default => 'Disponible'
            };
        }
        
        return $this->activa ? 'Disponible' : 'Inactiva';
    }
}
