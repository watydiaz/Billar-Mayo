<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ronda_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ronda_id')->constrained('rondas')->onDelete('cascade');
            $table->foreignId('producto_id')->nullable()->constrained('productos')->onDelete('set null');
            $table->string('nombre_producto', 300);
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->boolean('es_descuento')->default(false);
            $table->boolean('es_producto_personalizado')->default(false);
            $table->text('notas')->nullable();
            $table->timestamps();
            
            $table->index(['ronda_id']);
            $table->index(['producto_id']);
            $table->index(['es_descuento']);
            $table->index(['es_producto_personalizado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ronda_detalles');
    }
};
