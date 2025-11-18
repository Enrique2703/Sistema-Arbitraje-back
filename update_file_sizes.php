<?php

/**
 * Script para actualizar los tamaños de archivos existentes en participe_documento_archivos
 * Ejecutar con: php update_file_sizes.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

echo "Actualizando tamaños de archivos...\n\n";

// Obtener todos los archivos sin tamaño
$archivos = DB::table('participe_documento_archivos')
    ->whereNull('tamano')
    ->orWhere('tamano', 0)
    ->get();

$total = $archivos->count();
$actualizados = 0;
$errores = 0;

echo "Total de archivos a procesar: $total\n\n";

foreach ($archivos as $archivo) {
    $rutaArchivo = $archivo->archivo_adjunto;
    
    // Intentar obtener el tamaño del archivo desde storage/app/public
    $rutaCompleta = storage_path('app/public/' . $rutaArchivo);
    
    if (file_exists($rutaCompleta)) {
        $tamano = filesize($rutaCompleta);
        
        DB::table('participe_documento_archivos')
            ->where('id', $archivo->id)
            ->update(['tamano' => $tamano]);
        
        $tamanoKB = round($tamano / 1024, 2);
        echo "✓ Archivo ID {$archivo->id}: {$tamanoKB} KB\n";
        $actualizados++;
    } else {
        echo "✗ Archivo ID {$archivo->id}: No encontrado en {$rutaCompleta}\n";
        $errores++;
    }
}

echo "\n========================================\n";
echo "Proceso completado:\n";
echo "- Actualizados: $actualizados\n";
echo "- Errores: $errores\n";
echo "- Total procesados: " . ($actualizados + $errores) . "/$total\n";
echo "========================================\n";
