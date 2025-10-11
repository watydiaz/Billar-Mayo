<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== OBTENIENDO ESTRUCTURA DE BASE DE DATOS ===\n\n";

try {
    // Obtener todas las tablas
    $tables = DB::select('SHOW TABLES');
    $databaseName = DB::getDatabaseName();
    
    echo "Base de datos: {$databaseName}\n";
    echo "Total de tablas: " . count($tables) . "\n\n";
    
    $structure = [];
    $structure['database'] = $databaseName;
    $structure['generated_at'] = now()->format('Y-m-d H:i:s');
    $structure['tables'] = [];
    
    foreach ($tables as $table) {
        $tableName = array_values((array) $table)[0];
        echo "📋 Procesando tabla: {$tableName}\n";
        
        try {
            // Obtener estructura de la tabla
            $columns = DB::select("DESCRIBE {$tableName}");
        
        // Obtener información adicional de la tabla
        $tableInfo = DB::select("SHOW TABLE STATUS LIKE '{$tableName}'");
        
        // Obtener índices
        $indexes = DB::select("SHOW INDEX FROM {$tableName}");
        
        // Obtener claves foráneas
        $foreignKeys = DB::select("
            SELECT 
                COLUMN_NAME,
                CONSTRAINT_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM 
                information_schema.KEY_COLUMN_USAGE 
            WHERE 
                REFERENCED_TABLE_SCHEMA = '{$databaseName}' 
                AND TABLE_NAME = '{$tableName}'
        ");
        
        $tableStructure = [
            'name' => $tableName,
            'engine' => $tableInfo[0]->Engine ?? null,
            'collation' => $tableInfo[0]->Collation ?? null,
            'rows' => $tableInfo[0]->Rows ?? 0,
            'columns' => [],
            'indexes' => [],
            'foreign_keys' => []
        ];
        
        // Procesar columnas
        foreach ($columns as $column) {
            $tableStructure['columns'][] = [
                'name' => $column->Field,
                'type' => $column->Type,
                'null' => $column->Null === 'YES',
                'key' => $column->Key,
                'default' => $column->Default,
                'extra' => $column->Extra
            ];
        }
        
        // Procesar índices
        $processedIndexes = [];
        foreach ($indexes as $index) {
            $indexName = $index->Key_name;
            if (!isset($processedIndexes[$indexName])) {
                $processedIndexes[$indexName] = [
                    'name' => $indexName,
                    'type' => $index->Index_type,
                    'unique' => !$index->Non_unique,
                    'columns' => []
                ];
            }
            $processedIndexes[$indexName]['columns'][] = $index->Column_name;
        }
        $tableStructure['indexes'] = array_values($processedIndexes);
        
        // Procesar claves foráneas
        foreach ($foreignKeys as $fk) {
            $tableStructure['foreign_keys'][] = [
                'column' => $fk->COLUMN_NAME,
                'constraint_name' => $fk->CONSTRAINT_NAME,
                'referenced_table' => $fk->REFERENCED_TABLE_NAME,
                'referenced_column' => $fk->REFERENCED_COLUMN_NAME
            ];
        }
        
        $structure['tables'][] = $tableStructure;
        
        } catch (Exception $e) {
            echo "⚠️ Error procesando tabla {$tableName}: " . $e->getMessage() . "\n";
            continue;
        }
    }
    
    // Guardar estructura en archivo JSON
    $jsonFile = 'database_structure.json';
    file_put_contents($jsonFile, json_encode($structure, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    // Crear archivo Markdown legible
    $markdownContent = generateMarkdownStructure($structure);
    file_put_contents('database_structure.md', $markdownContent);
    
    echo "\n✅ Estructura guardada exitosamente:\n";
    echo "📄 {$jsonFile} (formato JSON)\n";
    echo "📖 database_structure.md (formato Markdown)\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

function generateMarkdownStructure($structure) {
    $md = "# 📊 Estructura de Base de Datos - Terkkos Billar\n\n";
    $md .= "**Base de datos:** `{$structure['database']}`  \n";
    $md .= "**Generado:** {$structure['generated_at']}  \n";
    $md .= "**Total de tablas:** " . count($structure['tables']) . "\n\n";
    
    $md .= "---\n\n";
    
    // Índice de tablas
    $md .= "## 📋 Índice de Tablas\n\n";
    foreach ($structure['tables'] as $table) {
        $md .= "- [`{$table['name']}`](#{$table['name']}) - {$table['rows']} registros\n";
    }
    $md .= "\n---\n\n";
    
    // Detalle de cada tabla
    foreach ($structure['tables'] as $table) {
        $md .= "## 📋 `{$table['name']}`\n\n";
        
        // Información general
        $md .= "**Motor:** {$table['engine']}  \n";
        $md .= "**Collation:** {$table['collation']}  \n";
        $md .= "**Registros:** {$table['rows']}  \n\n";
        
        // Columnas
        $md .= "### Columnas\n\n";
        $md .= "| Campo | Tipo | Nulo | Clave | Defecto | Extra |\n";
        $md .= "|-------|------|------|-------|---------|-------|\n";
        
        foreach ($table['columns'] as $column) {
            $null = $column['null'] ? '✅' : '❌';
            $key = $column['key'] ?: '-';
            $default = $column['default'] ?: '-';
            $extra = $column['extra'] ?: '-';
            
            $md .= "| `{$column['name']}` | {$column['type']} | {$null} | {$key} | {$default} | {$extra} |\n";
        }
        
        // Índices
        if (!empty($table['indexes'])) {
            $md .= "\n### Índices\n\n";
            $md .= "| Nombre | Tipo | Único | Columnas |\n";
            $md .= "|--------|------|-------|----------|\n";
            
            foreach ($table['indexes'] as $index) {
                $unique = $index['unique'] ? '✅' : '❌';
                $columns = implode(', ', $index['columns']);
                $md .= "| `{$index['name']}` | {$index['type']} | {$unique} | {$columns} |\n";
            }
        }
        
        // Claves foráneas
        if (!empty($table['foreign_keys'])) {
            $md .= "\n### Claves Foráneas\n\n";
            $md .= "| Campo Local | Tabla Referencias | Campo Referencias | Constraint |\n";
            $md .= "|-------------|-------------------|-------------------|------------|\n";
            
            foreach ($table['foreign_keys'] as $fk) {
                $md .= "| `{$fk['column']}` | `{$fk['referenced_table']}` | `{$fk['referenced_column']}` | {$fk['constraint_name']} |\n";
            }
        }
        
        $md .= "\n---\n\n";
    }
    
    return $md;
}

echo "\n=== PROCESO COMPLETADO ===\n";