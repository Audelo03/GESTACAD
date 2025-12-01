<?php
/**
 * Script para aplicar la migración de bloqueo por IP
 * Ejecutar una sola vez para crear la tabla y agregar columnas necesarias
 */

require_once __DIR__ . '/config/db.php';

echo "Aplicando migración: Bloqueo por IP para asistencias...\n";

try {
    // Leer el archivo SQL
    $sql = file_get_contents(__DIR__ . '/database/add_ip_blocking_to_tokens.sql');
    
    if ($sql === false) {
        die("Error: No se pudo leer el archivo SQL\n");
    }
    
    // Ejecutar el SQL
    $conn->exec($sql);
    
    echo "✓ Migración aplicada exitosamente\n";
    echo "  - Tabla 'asistencia_ip_bloqueos' creada\n";
    echo "  - Columnas agregadas a 'asistencia_tokens'\n";
    echo "\n";
    echo "El sistema de bloqueo por IP está listo para usar.\n";
    
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'already exists') !== false || 
        strpos($e->getMessage(), 'Duplicate column') !== false ||
        strpos($e->getMessage(), 'Duplicate key') !== false) {
        echo "⚠ Algunos elementos ya existen. Verificando estado...\n";
        
        // Verificar si la tabla existe
        $stmt = $conn->query("SHOW TABLES LIKE 'asistencia_ip_bloqueos'");
        if ($stmt->rowCount() > 0) {
            echo "  ✓ Tabla 'asistencia_ip_bloqueos' existe\n";
        } else {
            echo "  ✗ Tabla 'asistencia_ip_bloqueos' no existe\n";
        }
        
        // Verificar columnas
        $stmt = $conn->query("SHOW COLUMNS FROM asistencia_tokens LIKE 'ip_address'");
        if ($stmt->rowCount() > 0) {
            echo "  ✓ Columna 'ip_address' existe\n";
        } else {
            echo "  ✗ Columna 'ip_address' no existe\n";
        }
        
        $stmt = $conn->query("SHOW COLUMNS FROM asistencia_tokens LIKE 'ultimo_uso'");
        if ($stmt->rowCount() > 0) {
            echo "  ✓ Columna 'ultimo_uso' existe\n";
        } else {
            echo "  ✗ Columna 'ultimo_uso' no existe\n";
        }
    } else {
        echo "✗ Error al aplicar la migración: " . $e->getMessage() . "\n";
        exit(1);
    }
}

