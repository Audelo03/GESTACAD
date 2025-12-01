<?php
/**
 * Script para aplicar la migración de tokens de asistencia
 * Ejecutar una sola vez para crear la tabla necesaria
 */

require_once __DIR__ . '/config/db.php';

echo "Aplicando migración: Tabla de tokens de asistencia...\n";

try {
    // Leer el archivo SQL
    $sql = file_get_contents(__DIR__ . '/database/create_asistencia_tokens.sql');
    
    if ($sql === false) {
        die("Error: No se pudo leer el archivo SQL\n");
    }
    
    // Ejecutar el SQL
    $conn->exec($sql);
    
    echo "✓ Migración aplicada exitosamente\n";
    echo "  - Tabla 'asistencia_tokens' creada\n";
    echo "\n";
    echo "La tabla está lista para usar.\n";
    
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'already exists') !== false) {
        echo "⚠ La tabla ya existe. No se realizaron cambios.\n";
    } else {
        echo "✗ Error al aplicar la migración: " . $e->getMessage() . "\n";
        exit(1);
    }
}

