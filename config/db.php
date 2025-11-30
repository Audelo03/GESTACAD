<?php
$host = "localhost";        
$user = "root";            
$password = "";             
$database = "gestacadv2";

try {
    $conn = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verificar que la conexión funciona
    $stmt = $conn->query("SELECT 1");
    if (!$stmt) {
        throw new Exception("No se pudo ejecutar consulta de prueba");
    }
    
} catch (PDOException $e) {
    error_log("Error de conexión PDO: " . $e->getMessage());
    die("Error de conexión: " . $e->getMessage());
} catch (Exception $e) {
    error_log("Error general de conexión: " . $e->getMessage());
    die("Error de conexión: " . $e->getMessage());
}

?>