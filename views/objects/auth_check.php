<?php
/**
 * VERIFICACIÓN DE AUTENTICACIÓN Y PERMISOS - GESTACAD
 * 
 * Este archivo verifica que el usuario esté autenticado y tenga
 * los permisos necesarios para acceder a la página solicitada.
 */

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /GESTACAD/login");
    exit(); 
}

// Obtener la página actual y el nivel del usuario
$currentPage = basename($_SERVER['PHP_SELF']);
$userLevel = $_SESSION['usuario_nivel'] ?? null;

/**
 * Mapeo de permisos por página
 * Cada página tiene un array con los niveles de usuario que pueden acceder
 * 1 = Administrador, 2 = Coordinador, 3 = Tutor, 4 = Usuario especial
 */
$page_permissions = [
    // Páginas de Acceso General (para todos los usuarios)
    'profile.php'   => [1, 2, 3, 4],
    'logout.php'    => [1, 2, 3, 4],
    'listas.php'    => [1, 2, 3, 4],
    
    // Páginas de Administración (Solo Administradores)
    'dashboard.php'        => [1, 4],
    'usuarios.php'        => [1, 4],
    'carreras.php'        => [1, 4],
    'nivel_usuario.php'   => [1, 4],
    'modalidades.php'     => [1, 4], 
    'tipo_seguimiento.php'=> [1, 4], 
    'estadisticas.php'    => [1, 4],

    // Páginas de Coordinación (Administradores y Coordinadores)
    'grupos.php'    => [1, 2],
    'alumnos.php'   => [1, 2],

    // Páginas de Tutoría (Todos los niveles excepto usuarios básicos)
    'seguimientos.php'       => [1, 2, 3, 4],
    'ver_seguimientos.php'   => [1, 2, 3, 4],
    'crear_seguimiento.php'  => [1, 2, 3, 4],
    'editar_seguimiento.php' => [1, 2, 3, 4],
    'asistencia.php'         => [1, 2, 3, 4],
    'gestionar_listas.php'   => [1, 2, 3, 4],
];

// Verificar permisos de la página actual
if (isset($page_permissions[$currentPage])) {
    $allowedLevels = $page_permissions[$currentPage];

    // Verificar si el nivel del usuario está permitido para esta página
    if (!in_array($userLevel, $allowedLevels)) {
        $_SESSION['error_message'] = "No tienes los permisos necesarios para acceder a esta página.";
        header("Location: /GESTACAD/login");
        exit();
    }
} else {
    // Si la página no está definida en permisos, denegar acceso
    $_SESSION['error_message'] = "Acceso denegado a recurso no definido.";
    header("Location: /GESTACAD/login");
    exit();
}

?>
