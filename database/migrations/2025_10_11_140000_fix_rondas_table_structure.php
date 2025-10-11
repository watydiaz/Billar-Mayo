<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Deshabilitar verificaciones de foreign keys
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        
        try {
            Schema::table('rondas', function (Blueprint $table) {
                // Eliminar columna pedido_id sin foreign key
                $table->dropColumn('pedido_id');
                
                // Cambiar numero_ronda a string para códigos como R20251011-001
                $table->string('numero_ronda', 50)->change();
                
                // Agregar campo cliente que falta
                $table->string('cliente')->nullable()->after('numero_ronda');
            });
        } finally {
            // Rehabilitar verificaciones
            DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
        }
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