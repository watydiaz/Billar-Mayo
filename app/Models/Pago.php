<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    protected $table = 'pagos';
    
    protected $fillable = [
        'numero_pago',
        'tipo_pago',
        'total_pago',
        'metodo_pago',
        'descripcion',
        'pedido_id',
        'estado',
        'fecha_pago'
    ];

    protected $casts = [
        'total_pago' => 'decimal:2',
        'fecha_pago' => 'datetime'
    ];

    // Relación con detalles del pago
    public function detalles(): HasMany
    {
        return $this->hasMany(PagoDetalle::class);
    }

    // Relación con pedido (si aplica)
    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }
}
