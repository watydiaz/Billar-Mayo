<?php

require_once 'bootstrap/app.php';

$app = require_once 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;

echo "=== REVISANDO PEDIDOS/RONDAS ===\n\n";

try {
    // Obtener todas las rondas
    $rondas = DB::table('rondas')
        ->select('id', 'numero_ronda', 'cliente', 'estado', 'created_at')
        ->orderBy('created_at', 'desc')
        ->get();

    echo "Total de rondas: " . count($rondas) . "\n\n";

    foreach ($rondas as $ronda) {
        echo "ID: {$ronda->id}\n";
        echo "Número: {$ronda->numero_ronda}\n";
        echo "Cliente: {$ronda->cliente}\n";
        echo "Estado: {$ronda->estado}\n";
        echo "Creado: {$ronda->created_at}\n";
        echo "------------------------\n";
    }

    // Verificar duplicados por número de ronda
    $duplicados = DB::table('rondas')
        ->select('numero_ronda', DB::raw('count(*) as total'))
        ->groupBy('numero_ronda')
        ->having('total', '>', 1)
        ->get();

    if (count($duplicados) > 0) {
        echo "\n🚨 PEDIDOS DUPLICADOS ENCONTRADOS:\n";
        foreach ($duplicados as $dup) {
            echo "- Número: {$dup->numero_ronda} (aparece {$dup->total} veces)\n";
        }
    } else {
        echo "\n✅ No se encontraron duplicados\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== PROCESO COMPLETADO ===\n";