<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mesa extends Model
{
    protected $table = 'mesas';
    
    protected $fillable = [
        'numero',
        'tipo',
        'precio_por_hora',
        'estado',
        'descripcion',
        'activa'
    ];

    protected $casts = [
        'precio_por_hora' => 'decimal:2',
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

    // Relación con MesaRondas (reemplaza MesaAlquileres)
    public function mesaRondas(): HasMany
    {
        return $this->hasMany(MesaRonda::class, 'mesa_id');
    }

    // Alias para compatibilidad con código existente
    public function mesaAlquileres(): HasMany
    {
        return $this->mesaRondas();
    }

    // Obtener la ronda activa actual de esta mesa
    public function alquilerActivo()
    {
        return $this->mesaRondas()->where('estado', 'activo')->first();
    }

    // Ronda activa en la mesa (nueva estructura)
    public function rondaActiva()
    {
        return $this->mesaRondas()->where('estado', 'activo')->first();
    }

    // Precio por minuto para cálculo de tiempo
    public function getPrecioPorMinutoAttribute()
    {
        return $this->precio_hora / 60;
    }

    // Pedido activo en la mesa (compatibilidad)
    public function pedidoActivo()
    {
        $alquiler = $this->alquilerActivo();
        return $alquiler ? $alquiler->pedido : null;
    }

    public function getEstadoBadgeAttribute()
    {
        // Verificar si hay alquiler activo
        $alquilerActivo = $this->alquilerActivo();
        
        if ($alquilerActivo) {
            return match($alquilerActivo->estado) {
                'activo' => 'bg-warning', // Ocupada/Reservada
                'pausado' => 'bg-info', // Pausada
                default => 'bg-success'
            };
        }
        
        return $this->activa ? 'bg-success' : 'bg-secondary';
    }

    public function getEstadoTextoAttribute()
    {
        $alquilerActivo = $this->alquilerActivo();
        
        if ($alquilerActivo) {
            return match($alquilerActivo->estado) {
                'activo' => 'Ocupada',
                'pausado' => 'Pausada', 
                default => 'Disponible'
            };
        }
        
        return $this->activa ? 'Disponible' : 'Inactiva';
    }
}
