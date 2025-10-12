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
            // Renombrar precio actual a precio_venta
            $table->renameColumn('precio', 'precio_venta');
            
            // Agregar precio_compra
            $table->decimal('precio_compra', 10, 2)->nullable()->after('descripcion');
            
            // Agregar campo para URL de imagen
            $table->string('imagen_url', 500)->nullable()->after('precio_compra');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            // Revertir los cambios
            $table->renameColumn('precio_venta', 'precio');
            $table->dropColumn(['precio_compra', 'imagen_url']);
        });
    }
};
