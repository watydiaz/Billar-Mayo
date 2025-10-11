<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Esta migración ya no es necesaria porque la tabla rondas se creó correctamente
        // La estructura ya está bien definida en create_rondas_table.php
    }

    public function down(): void
    {
        Schema::table('rondas', function (Blueprint $table) {
            // Restaurar estructura anterior (si se necesita)
            $table->integer('pedido_id')->nullable();
            $table->integer('numero_ronda')->change();
            $table->dropColumn('cliente');
        });
    }
};