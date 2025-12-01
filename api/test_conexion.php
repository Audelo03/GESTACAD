<?php
/**
 * Script para probar la conexión con la API de inferencias
 * Ejecutar desde el navegador: http://localhost/GESTACAD/api/test_conexion.php
 */

$API_BASE_URL = 'http://localhost:5000';

echo "<h2>Prueba de Conexión con la API de Inferencias</h2>";
echo "<hr>";

// Test 1: Verificar que la API esté corriendo
echo "<h3>1. Verificando que la API esté corriendo...</h3>";
$ch = curl_init($API_BASE_URL . '/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "<p style='color: green;'>✅ API está corriendo correctamente</p>";
    $data = json_decode($response, true);
    echo "<pre>" . print_r($data, true) . "</pre>";
} else {
    echo "<p style='color: red;'>❌ Error: La API no está respondiendo (Código HTTP: $httpCode)</p>";
    echo "<p><strong>Solución:</strong> Ejecuta la API con: <code>cd api && python app.py</code></p>";
    exit;
}

// Test 2: Probar endpoint de estadísticas
echo "<hr><h3>2. Probando endpoint de estadísticas (alumno_id=2)...</h3>";
$ch = curl_init($API_BASE_URL . '/api/estadisticas/2');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "<p style='color: green;'>✅ Estadísticas obtenidas correctamente</p>";
    $data = json_decode($response, true);
    echo "<pre>" . print_r($data, true) . "</pre>";
} else {
    echo "<p style='color: red;'>❌ Error al obtener estadísticas (Código HTTP: $httpCode)</p>";
    echo "<pre>$response</pre>";
}

// Test 3: Probar endpoint de riesgo detallado
echo "<hr><h3>3. Probando endpoint de riesgo detallado (alumno_id=2)...</h3>";
$ch = curl_init($API_BASE_URL . '/api/riesgo/2/detallado');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "<p style='color: green;'>✅ Análisis de riesgo obtenido correctamente</p>";
    $data = json_decode($response, true);
    echo "<h4>Resumen:</h4>";
    echo "<ul>";
    echo "<li><strong>Nivel de Riesgo:</strong> " . ($data['analisis_riesgo']['nivel_riesgo'] ?? 'N/A') . "</li>";
    echo "<li><strong>Score:</strong> " . ($data['analisis_riesgo']['score_riesgo'] ?? 'N/A') . "</li>";
    echo "<li><strong>Reglas Aplicadas:</strong> " . count($data['analisis_riesgo']['reglas_aplicadas'] ?? []) . "</li>";
    echo "<li><strong>Recomendaciones:</strong> " . count($data['analisis_riesgo']['recomendaciones'] ?? []) . "</li>";
    echo "</ul>";
    echo "<details><summary>Ver respuesta completa</summary><pre>" . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre></details>";
} else {
    echo "<p style='color: red;'>❌ Error al obtener análisis de riesgo (Código HTTP: $httpCode)</p>";
    echo "<pre>$response</pre>";
}

echo "<hr>";
echo "<h3>✅ Pruebas completadas</h3>";
echo "<p>Si todos los tests pasaron, la API está funcionando correctamente.</p>";
?>


