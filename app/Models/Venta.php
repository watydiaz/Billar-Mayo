<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_venta',
        'subtotal',
        'descuento',
        'total',
        'estado',
        'tipo_pago',
        'observaciones'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relación con detalles de venta
    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class);
    }

    // Método para generar número de venta automático
    public static function generarNumeroVenta()
    {
        $ultimaVenta = self::orderBy('id', 'desc')->first();
        $numero = $ultimaVenta ? $ultimaVenta->id + 1 : 1;
        return 'VTA-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }

    // Calcular total automáticamente
    public function calcularTotal()
    {
        $subtotal = $this->detalles()->sum('subtotal');
        $this->subtotal = $subtotal;
        $this->total = $subtotal - $this->descuento;
        $this->save();
        return $this->total;
    }
}