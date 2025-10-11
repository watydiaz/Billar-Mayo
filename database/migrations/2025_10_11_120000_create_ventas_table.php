<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_venta')->unique(); // VTA-001, VTA-002, etc.
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->enum('estado', ['0', '1'])->default('0'); // 0=pendiente, 1=completado
            $table->string('tipo_pago')->default('efectivo'); // efectivo, tarjeta, transferencia
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            $table->index(['estado', 'created_at']);
            $table->index('numero_venta');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};