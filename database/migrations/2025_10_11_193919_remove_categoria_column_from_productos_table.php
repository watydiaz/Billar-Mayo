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
        Schema::table('productos', function (Blueprint $table) {
            // Eliminar la columna categoria que está causando conflicto con la relación
            $table->dropColumn('categoria');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            // Restaurar la columna categoria si se hace rollback
            $table->string('categoria')->nullable()->after('stock_minimo');
        });
    }
};
