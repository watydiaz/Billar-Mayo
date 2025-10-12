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
        Schema::table('rondas', function (Blueprint $table) {
            // Índices compuestos para consultas frecuentes
            $table->index(['estado', 'created_at'], 'idx_rondas_estado_fecha');
            $table->index(['cliente', 'estado'], 'idx_rondas_cliente_estado');
        });

        Schema::table('productos', function (Blueprint $table) {
            // Índices para búsquedas de productos
            $table->index(['activo', 'nombre'], 'idx_productos_activo_nombre');
            $table->index(['activo', 'categoria'], 'idx_productos_activo_categoria');
        });

        Schema::table('ronda_detalles', function (Blueprint $table) {
            // Índice para sumar totales por ronda
            $table->index(['ronda_id', 'subtotal'], 'idx_ronda_detalles_total');
        });

        Schema::table('mesas', function (Blueprint $table) {
            // Índice para mesas disponibles
            $table->index(['activa', 'estado'], 'idx_mesas_disponibles');
        });
    }

    public function down(): void
    {
        Schema::table('rondas', function (Blueprint $table) {
            $table->dropIndex('idx_rondas_estado_fecha');
            $table->dropIndex('idx_rondas_cliente_estado');
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->dropIndex('idx_productos_activo_nombre');
            $table->dropIndex('idx_productos_activo_categoria');
        });

        Schema::table('ronda_detalles', function (Blueprint $table) {
            $table->dropIndex('idx_ronda_detalles_total');
        });

        Schema::table('mesas', function (Blueprint $table) {
            $table->dropIndex('idx_mesas_disponibles');
        });
    }
};
