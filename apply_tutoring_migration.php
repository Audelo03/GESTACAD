<?php
/**
 * Database Migration Script
 * Applies tutoring tables to the database
 */

require_once __DIR__ . '/config/db.php';

try {
    echo "Starting database migration...\n\n";

    // Read and execute the SQL file
    $sqlFile = __DIR__ . '/database/apply_tutoring_lists.sql';

    if (!file_exists($sqlFile)) {
        throw new Exception("SQL file not found: $sqlFile");
    }

    $sql = file_get_contents($sqlFile);

    // Execute the SQL
    $conn->exec($sql);

    echo "✓ Successfully created tutoring tables:\n";
    echo "  - tutorias_grupales\n";
    echo "  - tutorias_grupales_asistencia\n";
    echo "  - tutorias_individuales\n\n";

    echo "Migration completed successfully!\n";

} catch (PDOException $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>