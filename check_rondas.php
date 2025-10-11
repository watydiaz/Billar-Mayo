<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Ronda;

echo "=== VERIFICANDO ESTRUCTURA DE RONDAS ===\n\n";

$rondas = Ronda::with('mesaRonda')->get();
echo "Total rondas: " . $rondas->count() . "\n\n";

echo "Rondas por cliente:\n";
$porCliente = $rondas->groupBy('cliente');
foreach ($porCliente as $cliente => $rondasCliente) {
    echo "Cliente: {$cliente}\n";
    foreach ($rondasCliente as $ronda) {
        echo "  - Ronda #{$ronda->numero_ronda}, Estado: {$ronda->estado}, Total: \${$ronda->total_ronda}\n";
    }
    $totalCliente = $rondasCliente->sum('total_ronda');
    echo "  Total del cliente: \${$totalCliente}\n\n";
}

echo "Estados de rondas:\n";
$porEstado = $rondas->groupBy('estado');
foreach ($porEstado as $estado => $rondasEstado) {
    echo "- {$estado}: " . $rondasEstado->count() . " rondas\n";
}

echo "\n=== FIN ===\n";