<?php
/**
 * SISTEMA DE ENRUTAMIENTO PRINCIPAL - GORA
 * 
 * Este archivo maneja todas las rutas de la aplicación usando URLs limpias.
 * Procesa las solicitudes HTTP y redirige a las vistas correspondientes.
 */

// Iniciar sesión para mantener el estado del usuario
session_start();

// Incluir conexión a la base de datos
require_once 'config/db.php';

// Incluir funciones utilitarias del sistema
require_once 'public/functions_util.php';

// ========================================
// PROCESAMIENTO DE RUTAS
// ========================================

// Obtener la ruta de la URL solicitada
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = ltrim($path, '/');

// Remover la ruta base del proyecto si existe
$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath !== '/') {
    $path = substr($path, strlen($basePath));
}

// Remover barra inicial después de remover la ruta base
$path = ltrim($path, '/');

// Manejar archivos .php removiendo la extensión
if (substr($path, -4) === '.php') {
    $path = substr($path, 0, -4);
}

// Si no hay ruta, redirigir al login por defecto
if (empty($path)) {
    $path = 'login';
}

// ========================================
// MAPEO DE RUTAS A VISTAS
// ========================================

/**
 * Mapeo de rutas URL limpias a archivos de vista
 * Permite URLs amigables sin extensiones .php
 */
$viewMap = [
    // Páginas principales
    'login' => 'views/login.php',
    'dashboard' => 'views/dashboard.php',
    'profile' => 'views/profile.php',
    'logout' => 'views/logout.php',

    // CRUDs de gestión
    'alumnos' => 'views/CRUDS/alumnos.php',
    'usuarios' => 'views/CRUDS/usuarios.php',
    'carreras' => 'views/CRUDS/carreras.php',
    'grupos' => 'views/CRUDS/grupos.php',
    'modalidades' => 'views/CRUDS/modalidades.php',
    'tipo-seguimiento' => 'views/CRUDS/tipo_seguimiento.php',

    // Páginas de funcionalidad
    'alumnos-paginados' => 'views/alumnos_paginados.php',
    'alumnos_paginados' => 'views/alumnos_paginados.php',
    'asistencia' => 'views/asistencia.php',
    'estadisticas' => 'views/estadisticas.php',
    'reportes' => 'views/reportes.php',
    'listas' => 'views/listas.php',
    'seguimientos' => 'views/seguimientos.php',
    'alumnos-riesgo' => 'views/alumnos_riesgo.php',
    'alumnos_riesgo' => 'views/alumnos_riesgo.php',

    // Gestión de seguimientos
    'crear-seguimiento' => 'views/crear_seguimiento.php',
    'crear_seguimiento' => 'views/crear_seguimiento.php',
    'editar-seguimiento' => 'views/editar_seguimiento.php',
    'editar_seguimiento' => 'views/editar_seguimiento.php',
    'ver-seguimientos' => 'views/ver_seguimientos.php',
    'ver_seguimientos' => 'views/ver_seguimientos.php',

    // Gestión de listas
    'gestionar-listas' => 'views/gestionar_listas.php',
    'gestionar_listas' => 'views/gestionar_listas.php',
    'guardar_asistencia' => 'views/guardar_asistencia.php',
    'ver-alumnos-grupo' => 'views/ver_alumnos_grupo.php',
    'ver_alumnos_grupo' => 'views/ver_alumnos_grupo.php',
    'marcar-asistencia' => 'views/marcar_asistencia.php',
    'marcar_asistencia' => 'views/marcar_asistencia.php',

    // Estructura Académica
    'divisiones' => 'views/CRUDS/divisiones.php',
    'periodos' => 'views/CRUDS/periodos.php',
    'asignaturas' => 'views/CRUDS/asignaturas.php',

    // Gestión de Clases
    'clases' => 'views/CRUDS/clases.php',
    'inscripciones' => 'views/CRUDS/inscripciones.php',

    // Tutorías y Seguimiento
    'tutorias' => 'views/tutorias/dashboard.php',
    'tutorias/pat' => 'views/tutorias/pat.php',
    'tutorias/mi-pat' => 'views/tutorias/mi_pat.php',
    'tutorias/canalizacion' => 'views/tutorias/canalizacion.php',

    // Becas
    'becas' => 'views/CRUDS/becas.php'
];

// ========================================
// CARGA DE VISTAS
// ========================================

// Verificar si la ruta existe en el mapeo
if (isset($viewMap[$path])) {
    $viewFile = $viewMap[$path];
    if (file_exists($viewFile)) {
        include $viewFile;
        exit;
    }
}

// Intentar cargar la vista directamente
$viewFile = "views/{$path}.php";
if (file_exists($viewFile)) {
    include $viewFile;
    exit;
}

// ========================================
// PÁGINA 404 - NO ENCONTRADA
// ========================================

http_response_code(404);
echo "<h1>404 - Página No Encontrada</h1>";
echo "<p>La página que solicitaste no pudo ser encontrada.</p>";
echo "<a href='/GESTACAD/'>Ir al Inicio</a>";
?>