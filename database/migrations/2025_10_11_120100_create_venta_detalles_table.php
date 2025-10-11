<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venta_detalles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('venta_id');
            $table->unsignedInteger('producto_id'); // Sin foreign key por ahora
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 8, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
            
            $table->foreign('venta_id')->references('id')->on('ventas')->onDelete('cascade');
            // Comentamos la foreign key de productos por ahora
            // $table->foreign('producto_id')->references('id')->on('productos')->onDelete('cascade');
            $table->index(['venta_id', 'producto_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venta_detalles');
    }
};