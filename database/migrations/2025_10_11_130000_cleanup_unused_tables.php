<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Deshabilitar verificaciones de claves foráneas temporalmente
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        
        try {
            // Eliminar tablas innecesarias
            
            // 1. Tablas de vistas/resúmenes que se pueden regenerar
            Schema::dropIfExists('productos_mas_vendidos');
            Schema::dropIfExists('productos_stock_bajo');
            Schema::dropIfExists('resumen_financiero_diario');
            Schema::dropIfExists('resumen_pedidos_activos');
            
            // 2. Tablas de cambios/movimientos que no usamos
            Schema::dropIfExists('producto_cambios');
            Schema::dropIfExists('inventario_movimientos');
            Schema::dropIfExists('actividad_log'); // También eliminar logs si no los necesitamos
            
            // 3. Tablas de mesa duplicadas
            Schema::dropIfExists('mesa_alquileres');
            Schema::dropIfExists('mesas_estado_actual');
            
            // 4. Sistema antiguo reemplazado por ventas
            Schema::dropIfExists('pedidos');
            Schema::dropIfExists('pagos');
            
            echo "Tablas innecesarias eliminadas correctamente.\n";
            
        } finally {
            // Rehabilitar verificaciones de claves foráneas
            DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
        }
    }

    public function down(): void
    {
        // No recreamos las tablas en rollback para evitar pérdida de datos
        // Si se necesita rollback, se debe hacer manualmente
        echo "Rollback no disponible para esta migración por seguridad.\n";
    }
};