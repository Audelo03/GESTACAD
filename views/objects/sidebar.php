<?php
// Obtener la página actual del sistema de enrutamiento
$current_page = '';
$request_uri = $_SERVER['REQUEST_URI'];

// Remover la base del proyecto y parámetros
$base_path = '/GESTACAD/';
if (strpos($request_uri, $base_path) === 0) {
  $path = substr($request_uri, strlen($base_path));
  $path = explode('?', $path)[0]; // Remover parámetros GET
  $path = explode('#', $path)[0]; // Remover fragmentos
  $current_page = trim($path, '/');
}

if (empty($current_page)) {
  $current_page = 'dashboard';
}


// Etiqueta amigable para mostrar en el encabezado del sidebar
$page_label = $current_page;
$page_label = preg_replace('/\.php$/', '', $page_label);
$page_label = str_replace('-', ' ', $page_label);
$page_label = ucfirst($page_label);



if (!function_exists('active')) {
  /**
   * Función para determinar si un enlace del sidebar está activo
   * @param array|string $pages - Páginas a verificar
   * @return string - Clase CSS 'active' si coincide, cadena vacía si no
   */
  function active($pages)
  {
    global $current_page;
    $pages_array = (array) $pages;

    // Verificar si la página actual coincide con alguna de las páginas especificadas
    foreach ($pages_array as $page) {
      // Coincidencia exacta
      if ($current_page === $page || $current_page === $page . '.php') {
        return 'active';
      }
      // Coincidencia con rutas anidadas (ej: tutorias/pat)
      if (strpos($current_page, $page) === 0 || strpos($current_page, $page . '/') === 0) {
        return 'active';
      }
    }
    return '';
  }
}
if (isset($_SESSION))
  $nivel = (int) $_SESSION["usuario_nivel"];

// Obtener grupos del tutor si es nivel 3 (TUTOR)
$grupos_tutor = [];
if ($nivel == 3 && isset($_SESSION['usuario_id'])) {
    // Verificar si $conn está disponible, si no, intentar obtenerlo
    if (!isset($conn)) {
        if (file_exists(__DIR__ . '/../config/db.php')) {
            require_once __DIR__ . '/../config/db.php';
        }
    }
    if (isset($conn)) {
        try {
            $sql = "SELECT g.id_grupo, g.nombre
                    FROM grupos g
                    WHERE g.usuarios_id_usuario_tutor = :usuario_id AND g.estatus = 1
                    ORDER BY g.nombre ASC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":usuario_id", $_SESSION['usuario_id'], PDO::PARAM_INT);
            $stmt->execute();
            $grupos_tutor = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Si hay error, dejar array vacío
            $grupos_tutor = [];
        }
    }
}

// Debug: Mostrar información del usuario (remover en producción)
// if (isset($_SESSION["usuario_nivel"])) {
//   echo "<!-- DEBUG: Nivel de usuario: " . $_SESSION["usuario_nivel"] . " (tipo: " . gettype($_SESSION["usuario_nivel"]) . ") -->";
//   echo "<!-- DEBUG: Nivel convertido: " . $nivel . " (tipo: " . gettype($nivel) . ") -->";
// }

if (!isset($modificacion_ruta)) {
  $modificacion_ruta = "";
}
?>

<!-- Overlay para móviles -->
<div class="sidebar-overlay"></div>

<nav id="app-sidebar" class="sidebar bg-dark text-white position-fixed h-100 p-3 collapsed">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <span class="fs-5 fw-bold sidebar-title">GESTACAD</span>
    <button id="btn-toggle-sidebar" class="btn btn-sm btn-outline-light btn-toggle-sidebar" aria-label="Colapsar menú">
      <i class="bi bi-x sidebar-icon-open sidebar-icon" style="display: none;"></i>
      <i class="bi bi-list sidebar-icon-collapsed sidebar-icon"></i>
    </button>
  </div>

  <div id="slow" class="slow">
    <ul class="nav nav-pills flex-column mb-auto">

      <?php if ($nivel == 1): // ADMINISTRADOR ?>
        <!-- Administrador: Dashboard, Alumnos, Estadísticas, Seguimientos -->
        <li class="nav-item mobile-hidden">
          <a href="/GESTACAD/dashboard" class="nav-link sidebar-link <?= active(['dashboard']); ?>" <?= active(['dashboard']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Panel Principal"' ?>>
            <i class="bi bi-speedometer2 me-2 sidebar-icon"></i> <span class="sidebar-text">Dashboard</span>
          </a>
        </li>

        <li class="mobile-hidden">
          <a href="/GESTACAD/listas" class="nav-link sidebar-link <?= active(['listas']); ?>" <?= active(['listas']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Lista de Alumnos"' ?>>
            <i class="bi bi-people me-2 sidebar-icon"></i> <span class="sidebar-text">Alumnos</span>
          </a>
        </li>

        <h6 class="text-uppercase text-secondary fw-bold small mt-3 mb-2 sidebar-section-title">Estadísticas</h6>

        <li class="mobile-hidden">
          <a href="/GESTACAD/estadisticas" class="nav-link sidebar-link <?= active(['estadisticas']); ?>"
            <?= active(['estadisticas']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Estadísticas"' ?>>
            <i class="bi bi-bar-chart-fill me-2 sidebar-icon"></i> <span class="sidebar-text">Estadísticas</span>
          </a>
        </li>

        <li class="mobile-hidden">
          <a href="/GESTACAD/reportes" class="nav-link sidebar-link <?= active(['reportes']); ?>"
            <?= active(['reportes']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Reportes por Parcial y Grupo"' ?>>
            <i class="bi bi-file-earmark-bar-graph me-2 sidebar-icon"></i> <span class="sidebar-text">Reportes</span>
          </a>
        </li>

        <li class="mobile-hidden">
          <a href="/GESTACAD/seguimientos" class="nav-link sidebar-link <?= active(['seguimientos']); ?>"
            <?= active(['seguimientos']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Seguimientos"' ?>>
            <i class="bi bi-journal-text me-2 sidebar-icon"></i> <span class="sidebar-text">Seguimientos</span>
          </a>
        </li>

        <li class="mobile-hidden">
          <a href="/GESTACAD/alumnos-riesgo" class="nav-link sidebar-link <?= active(['alumnos-riesgo', 'alumnos_riesgo']); ?>"
            <?= active(['alumnos-riesgo', 'alumnos_riesgo']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Alumnos en Riesgo"' ?>>
            <i class="bi bi-exclamation-triangle-fill me-2 sidebar-icon"></i> <span class="sidebar-text">Alumnos en Riesgo</span>
          </a>
        </li>

        <h6 class="text-uppercase text-secondary fw-bold small mt-3 mb-2 sidebar-section-title">Gestión</h6>

        <li>
          <a href="/GESTACAD/usuarios" class="nav-link sidebar-link <?= active(['usuarios']); ?>" <?= active(['usuarios']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Gestión de Usuarios"' ?>>
            <i class="bi bi-person-vcard me-2 sidebar-icon"></i> <span class="sidebar-text">Usuarios</span>
          </a>
        </li>

        <li>
          <a href="/GESTACAD/alumnos" class="nav-link sidebar-link <?= active(['alumnos']); ?>" <?= active(['alumnos']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Gestión de Alumnos"' ?>>
            <i class="bi bi-person-workspace me-2 sidebar-icon"></i> <span class="sidebar-text">Alumnos</span>
          </a>
        </li>

        <li>
          <a href="/GESTACAD/carreras" class="nav-link sidebar-link <?= active(['carreras']); ?>" <?= active(['carreras']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Gestión de Carreras"' ?>>
            <i class="bi bi-book me-2 sidebar-icon"></i> <span class="sidebar-text">Carreras</span>
          </a>
        </li>

        <li>
          <a href="/GESTACAD/grupos" class="nav-link sidebar-link <?= active(['grupos']); ?>" <?= active(['grupos']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Gestión de Grupos"' ?>>
            <i class="bi bi-person-video2 me-2 sidebar-icon"></i> <span class="sidebar-text">Grupos</span>
          </a>
        </li>

        <li>
          <a href="/GESTACAD/modalidades" class="nav-link sidebar-link <?= active(['modalidades']); ?>"
            <?= active(['modalidades']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Gestión de Modalidades"' ?>>
            <i class="bi bi-person-video3 me-2 sidebar-icon"></i> <span class="sidebar-text">Modalidades</span>
          </a>
        </li>

        <li>
          <a href="/GESTACAD/tipo-seguimiento"
            class="nav-link sidebar-link <?= active(['tipo-seguimiento', 'tipo_seguimiento']); ?>"
            <?= active(['tipo-seguimiento', 'tipo_seguimiento']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Gestión de tipos de seguimientos"' ?>>
            <i class="bi bi-person-rolodex me-2 sidebar-icon"></i> <span class="sidebar-text">Tipo de Seguimientos</span>
          </a>
        </li>

        <h6 class="text-uppercase text-secondary fw-bold small mt-3 mb-2 sidebar-section-title">Académico</h6>

        <li>
          <a href="/GESTACAD/divisiones" class="nav-link sidebar-link <?= active(['divisiones']); ?>" <?= active(['divisiones']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Gestión de Divisiones"' ?>>
            <i class="bi bi-building me-2 sidebar-icon"></i> <span class="sidebar-text">Divisiones</span>
          </a>
        </li>

        <li>
          <a href="/GESTACAD/periodos" class="nav-link sidebar-link <?= active(['periodos']); ?>" <?= active(['periodos']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Gestión de Periodos"' ?>>
            <i class="bi bi-calendar-range me-2 sidebar-icon"></i> <span class="sidebar-text">Periodos</span>
          </a>
        </li>

        <li>
          <a href="/GESTACAD/asignaturas" class="nav-link sidebar-link <?= active(['asignaturas']); ?>"
            <?= active(['asignaturas']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Gestión de Asignaturas"' ?>>
            <i class="bi bi-journal-bookmark me-2 sidebar-icon"></i> <span class="sidebar-text">Asignaturas</span>
          </a>
        </li>

        <li>
          <a href="/GESTACAD/clases" class="nav-link sidebar-link <?= active(['clases']); ?>" <?= active(['clases']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Gestión de Clases"' ?>>
            <i class="bi bi-easel me-2 sidebar-icon"></i> <span class="sidebar-text">Clases</span>
          </a>
        </li>

        <li>
          <a href="/GESTACAD/inscripciones" class="nav-link sidebar-link <?= active(['inscripciones']); ?>"
            <?= active(['inscripciones']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Gestión de Inscripciones"' ?>>
            <i class="bi bi-pencil-square me-2 sidebar-icon"></i> <span class="sidebar-text">Inscripciones</span>
          </a>
        </li>

        <li>
          <a href="/GESTACAD/becas" class="nav-link sidebar-link <?= active(['becas']); ?>" <?= active(['becas']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Gestión de Becas"' ?>>
            <i class="bi bi-award me-2 sidebar-icon"></i> <span class="sidebar-text">Becas</span>
          </a>
        </li>

        <h6 class="text-uppercase text-secondary fw-bold small mt-3 mb-2 sidebar-section-title">Tutorías</h6>

        <li>
          <a href="/GESTACAD/tutorias/mi-pat" class="nav-link sidebar-link <?= active(['tutorias/mi-pat', 'mi-pat']); ?>" <?= active(['tutorias/mi-pat', 'mi-pat']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Mi Plan de Acción Tutorial"' ?>>
            <i class="bi bi-clipboard-check me-2 sidebar-icon"></i> <span class="sidebar-text">Mi PAT</span>
          </a>
        </li>

        <li>
          <a href="/GESTACAD/tutorias/pat" class="nav-link sidebar-link <?= active(['tutorias/pat', 'pat']); ?>" <?= active(['tutorias/pat', 'pat']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Plan de Acción Tutorial"' ?>>
            <i class="bi bi-clipboard-data me-2 sidebar-icon"></i> <span class="sidebar-text">PAT General</span>
          </a>
        </li>

        <li>
          <a href="/GESTACAD/tutorias/canalizacion" class="nav-link sidebar-link <?= active(['tutorias/canalizacion', 'canalizacion']); ?>"
            <?= active(['tutorias/canalizacion', 'canalizacion']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Canalización de Alumnos"' ?>>
            <i class="bi bi-arrow-right-circle me-2 sidebar-icon"></i> <span class="sidebar-text">Canalización</span>
          </a>
        </li>

      <?php elseif ($nivel == 2): // COORDINADOR ?>
        <!-- Coordinador: Dashboard, Alumnos (de su carrera), Estadísticas (de su carrera), Seguimientos -->
        <li class="nav-item mobile-hidden">
          <a href="/GESTACAD/dashboard" class="nav-link sidebar-link <?= active(['dashboard']); ?>" <?= active(['dashboard']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Panel Principal"' ?>>
            <i class="bi bi-speedometer2 me-2 sidebar-icon"></i> <span class="sidebar-text">Dashboard</span>
          </a>
        </li>

        <li class="mobile-hidden">
          <a href="/GESTACAD/listas" class="nav-link sidebar-link <?= active(['listas']); ?>" <?= active(['listas']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Lista de Alumnos de mi Carrera"' ?>>
            <i class="bi bi-people me-2 sidebar-icon"></i> <span class="sidebar-text">Alumnos</span>
          </a>
        </li>

        <h6 class="text-uppercase text-secondary fw-bold small mt-3 mb-2 sidebar-section-title">Estadísticas</h6>

        <li class="mobile-hidden">
          <a href="/GESTACAD/estadisticas" class="nav-link sidebar-link <?= active(['estadisticas']); ?>"
            <?= active(['estadisticas']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Estadísticas de mi Carrera"' ?>>
            <i class="bi bi-bar-chart-fill me-2 sidebar-icon"></i> <span class="sidebar-text">Estadísticas</span>
          </a>
        </li>

        <li class="mobile-hidden">
          <a href="/GESTACAD/reportes" class="nav-link sidebar-link <?= active(['reportes']); ?>"
            <?= active(['reportes']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Reportes por Parcial y Grupo"' ?>>
            <i class="bi bi-file-earmark-bar-graph me-2 sidebar-icon"></i> <span class="sidebar-text">Reportes</span>
          </a>
        </li>

        <li class="mobile-hidden">
          <a href="/GESTACAD/seguimientos" class="nav-link sidebar-link <?= active(['seguimientos']); ?>"
            <?= active(['seguimientos']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Seguimientos"' ?>>
            <i class="bi bi-journal-text me-2 sidebar-icon"></i> <span class="sidebar-text">Seguimientos</span>
          </a>
        </li>

        <li class="mobile-hidden">
          <a href="/GESTACAD/alumnos-riesgo" class="nav-link sidebar-link <?= active(['alumnos-riesgo', 'alumnos_riesgo']); ?>"
            <?= active(['alumnos-riesgo', 'alumnos_riesgo']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Alumnos en Riesgo"' ?>>
            <i class="bi bi-exclamation-triangle-fill me-2 sidebar-icon"></i> <span class="sidebar-text">Alumnos en Riesgo</span>
          </a>
        </li>

        <h6 class="text-uppercase text-secondary fw-bold small mt-3 mb-2 sidebar-section-title">Gestión</h6>

        <!-- Coordinador: CRUDs limitados a su carrera/división -->
        <li>
          <a href="/GESTACAD/alumnos" class="nav-link sidebar-link <?= active(['alumnos']); ?>" <?= active(['alumnos']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Gestión de Alumnos de mi Carrera"' ?>>
            <i class="bi bi-person-workspace me-2 sidebar-icon"></i> <span class="sidebar-text">Alumnos</span>
          </a>
        </li>

        <li>
          <a href="/GESTACAD/grupos" class="nav-link sidebar-link <?= active(['grupos']); ?>" <?= active(['grupos']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Gestión de Grupos de mi Carrera"' ?>>
            <i class="bi bi-person-video2 me-2 sidebar-icon"></i> <span class="sidebar-text">Grupos</span>
          </a>
        </li>

        <h6 class="text-uppercase text-secondary fw-bold small mt-3 mb-2 sidebar-section-title">Académico</h6>

        <li>
          <a href="/GESTACAD/divisiones" class="nav-link sidebar-link <?= active(['divisiones']); ?>" <?= active(['divisiones']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Gestión de Divisiones"' ?>>
            <i class="bi bi-building me-2 sidebar-icon"></i> <span class="sidebar-text">Divisiones</span>
          </a>
        </li>

        <li>
          <a href="/GESTACAD/periodos" class="nav-link sidebar-link <?= active(['periodos']); ?>" <?= active(['periodos']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Gestión de Periodos"' ?>>
            <i class="bi bi-calendar-range me-2 sidebar-icon"></i> <span class="sidebar-text">Periodos</span>
          </a>
        </li>

        <li>
          <a href="/GESTACAD/asignaturas" class="nav-link sidebar-link <?= active(['asignaturas']); ?>"
            <?= active(['asignaturas']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Gestión de Asignaturas"' ?>>
            <i class="bi bi-journal-bookmark me-2 sidebar-icon"></i> <span class="sidebar-text">Asignaturas</span>
          </a>
        </li>

        <li>
          <a href="/GESTACAD/clases" class="nav-link sidebar-link <?= active(['clases']); ?>" <?= active(['clases']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Gestión de Clases"' ?>>
            <i class="bi bi-easel me-2 sidebar-icon"></i> <span class="sidebar-text">Clases</span>
          </a>
        </li>

        <li>
          <a href="/GESTACAD/inscripciones" class="nav-link sidebar-link <?= active(['inscripciones']); ?>"
            <?= active(['inscripciones']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Gestión de Inscripciones"' ?>>
            <i class="bi bi-pencil-square me-2 sidebar-icon"></i> <span class="sidebar-text">Inscripciones</span>
          </a>
        </li>

        <li>
          <a href="/GESTACAD/becas" class="nav-link sidebar-link <?= active(['becas']); ?>" <?= active(['becas']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Gestión de Becas"' ?>>
            <i class="bi bi-award me-2 sidebar-icon"></i> <span class="sidebar-text">Becas</span>
          </a>
        </li>

        <h6 class="text-uppercase text-secondary fw-bold small mt-3 mb-2 sidebar-section-title">Tutorías</h6>

        <li>
          <a href="/GESTACAD/tutorias/pat" class="nav-link sidebar-link <?= active(['tutorias/pat', 'pat']); ?>" <?= active(['tutorias/pat', 'pat']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Plan de Acción Tutorial"' ?>>
            <i class="bi bi-clipboard-check me-2 sidebar-icon"></i> <span class="sidebar-text">PAT</span>
          </a>
        </li>

        <li>
          <a href="/GESTACAD/tutorias/canalizacion" class="nav-link sidebar-link <?= active(['tutorias/canalizacion', 'canalizacion']); ?>"
            <?= active(['tutorias/canalizacion', 'canalizacion']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Canalización de Alumnos"' ?>>
            <i class="bi bi-arrow-right-circle me-2 sidebar-icon"></i> <span class="sidebar-text">Canalización</span>
          </a>
        </li>

      <?php elseif ($nivel == 3): // TUTOR ?>
        <!-- Tutor: Solo grupos/alumnos, seguimientos, alumnos en riesgo, tutorías PAT, canalización, becas -->
        <li class="mobile-hidden">
          <a href="/GESTACAD/listas" class="nav-link sidebar-link <?= active(['listas']); ?>" <?= active(['listas']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Mis Grupos y Alumnos"' ?>>
            <i class="bi bi-people me-2 sidebar-icon"></i> <span class="sidebar-text">Mis Grupos</span>
          </a>
        </li>

        <li class="mobile-hidden">
          <a href="/GESTACAD/seguimientos" class="nav-link sidebar-link <?= active(['seguimientos']); ?>"
            <?= active(['seguimientos']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Seguimientos"' ?>>
            <i class="bi bi-journal-text me-2 sidebar-icon"></i> <span class="sidebar-text">Seguimientos</span>
          </a>
        </li>

        <h6 class="text-uppercase text-secondary fw-bold small mt-3 mb-2 sidebar-section-title">Estadísticas</h6>

        <li>
          <a href="/GESTACAD/reportes" class="nav-link sidebar-link <?= active(['reportes']); ?>"
            <?= active(['reportes']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Reportes por Parcial y Grupo"' ?>>
            <i class="bi bi-file-earmark-bar-graph me-2 sidebar-icon"></i> <span class="sidebar-text">Reportes</span>
          </a>
        </li>

        <h6 class="text-uppercase text-secondary fw-bold small mt-3 mb-2 sidebar-section-title">Tutorías</h6>

        <li>
          <a href="/GESTACAD/tutorias/mi-pat" class="nav-link sidebar-link <?= active(['tutorias/mi-pat', 'mi-pat']); ?>" <?= active(['tutorias/mi-pat', 'mi-pat']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Mi Plan de Acción Tutorial"' ?>>
            <i class="bi bi-clipboard-check me-2 sidebar-icon"></i> <span class="sidebar-text">Mi PAT</span>
          </a>
        </li>

        <li>
          <a href="/GESTACAD/tutorias/pat" class="nav-link sidebar-link <?= active(['tutorias/pat', 'pat']); ?>" <?= active(['tutorias/pat', 'pat']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Plan de Acción Tutorial"' ?>>
            <i class="bi bi-clipboard-data me-2 sidebar-icon"></i> <span class="sidebar-text">PAT General</span>
          </a>
        </li>

        <li>
          <a href="/GESTACAD/tutorias/canalizacion" class="nav-link sidebar-link <?= active(['tutorias/canalizacion', 'canalizacion']); ?>"
            <?= active(['tutorias/canalizacion', 'canalizacion']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Canalización de Alumnos"' ?>>
            <i class="bi bi-arrow-right-circle me-2 sidebar-icon"></i> <span class="sidebar-text">Canalización</span>
          </a>
        </li>

        <li>
          <a href="/GESTACAD/alumnos-riesgo" class="nav-link sidebar-link <?= active(['alumnos-riesgo', 'alumnos_riesgo']); ?>"
            <?= active(['alumnos-riesgo', 'alumnos_riesgo']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Alumnos en Riesgo"' ?>>
            <i class="bi bi-exclamation-triangle-fill me-2 sidebar-icon"></i> <span class="sidebar-text">Alumnos en Riesgo</span>
          </a>
        </li>

        <li>
          <a href="/GESTACAD/becas" class="nav-link sidebar-link <?= active(['becas']); ?>" <?= active(['becas']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Gestión de Becas"' ?>>
            <i class="bi bi-award me-2 sidebar-icon"></i> <span class="sidebar-text">Becas</span>
          </a>
        </li>

      <?php endif; ?>

      <h6 class="text-uppercase text-secondary fw-bold small mt-3 mb-2 sidebar-section-title">Tú</h6>

      <li>
        <a href="/GESTACAD/profile" class="nav-link sidebar-link <?= active(['profile']); ?>" <?= active(['profile']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Mi Perfil de Usuario"' ?>>
          <i class="bi bi-person-circle me-2 sidebar-icon"></i> <span class="sidebar-text">Perfil</span>
        </a>
      </li>

    </ul>

    <div class="mt-auto pt-3 border-top border-secondary">
      <div class="theme-toggle-wrapper">
        <div class="theme-toggle-header">
          <i class="bi bi-palette-fill theme-toggle-icon"></i>
          <span class="theme-toggle-label">Tema</span>
        </div>
        <label class="theme-switch-container" for="themeToggle">
          <input type="checkbox" id="themeToggle" class="theme-switch-input">
          <div class="theme-switch-slider">
            <div class="theme-switch-thumb">
              <i class="bi bi-moon-stars-fill theme-switch-icon theme-icon-dark"></i>
            </div>
          </div>
        </label>
      </div>
    </div>

    <!-- Botón de cerrar sesión solo en móvil -->
    <div class="d-lg-none mt-4">
      <a href="/GESTACAD/logout" id="logout-link-mobile"
        class="btn btn-outline-light w-100 d-flex align-items-center justify-content-center gap-2 mobile-logout-btn">
        <i class="bi bi-box-arrow-right"></i>
        <span>Cerrar sesión</span>
      </a>
    </div>
  </div>
</nav>

<!-- Barra de navegación inferior (solo móvil) -->
<nav id="mobile-bottom-nav" class="d-lg-none">
  <?php if ($nivel == 1): // ADMINISTRADOR: Dashboard, Estadísticas, Alumnos, Configuración, Gestión ?>
    <a href="/GESTACAD/dashboard" class="bottom-nav-item <?= active(['dashboard']) ? 'active' : '' ?>">
      <i class="bi bi-house-door-fill"></i>
      <span>Dashboard</span>
    </a>
    <a href="/GESTACAD/estadisticas" class="bottom-nav-item <?= active(['estadisticas']) ? 'active' : '' ?>">
      <i class="bi bi-bar-chart-line-fill"></i>
      <span>Estadísticas</span>
    </a>
    <a href="/GESTACAD/listas" class="bottom-nav-item <?= active(['listas', 'alumnos']) ? 'active' : '' ?>">
      <i class="bi bi-people-fill"></i>
      <span>Alumnos</span>
    </a>
    <button id="bottom-nav-config" type="button" class="bottom-nav-item">
      <i class="bi bi-gear-fill"></i>
      <span>Configuración</span>
    </button>
    <button id="bottom-nav-manage" type="button" class="bottom-nav-item">
      <i class="bi bi-columns-gap"></i>
      <span>Gestión</span>
    </button>

  <?php elseif ($nivel == 2): // COORDINADOR: Dashboard, Estadísticas, Alumnos, Seguimientos, Configuración ?>
    <a href="/GESTACAD/dashboard" class="bottom-nav-item <?= active(['dashboard']) ? 'active' : '' ?>">
      <i class="bi bi-house-door-fill"></i>
      <span>Dashboard</span>
    </a>
    <a href="/GESTACAD/estadisticas" class="bottom-nav-item <?= active(['estadisticas']) ? 'active' : '' ?>">
      <i class="bi bi-bar-chart-line-fill"></i>
      <span>Estadísticas</span>
    </a>
    <a href="/GESTACAD/listas" class="bottom-nav-item <?= active(['listas', 'alumnos']) ? 'active' : '' ?>">
      <i class="bi bi-people-fill"></i>
      <span>Alumnos</span>
    </a>
    <a href="/GESTACAD/seguimientos" class="bottom-nav-item <?= active(['seguimientos']) ? 'active' : '' ?>">
      <i class="bi bi-journal-text"></i>
      <span>Seguimientos</span>
    </a>
    <button id="bottom-nav-config" type="button" class="bottom-nav-item">
      <i class="bi bi-gear-fill"></i>
      <span>Configuración</span>
    </button>

  <?php elseif ($nivel == 3): // TUTOR: Alumnos, Seguimientos, [BOTÓN PRINCIPAL TOMAR LISTA], Mi PAT, Configuración ?>
    <a href="/GESTACAD/listas" class="bottom-nav-item <?= active(['listas', 'alumnos']) ? 'active' : '' ?>">
      <i class="bi bi-people-fill"></i>
      <span>Alumnos</span>
    </a>
    <a href="/GESTACAD/seguimientos" class="bottom-nav-item <?= active(['seguimientos']) ? 'active' : '' ?>">
      <i class="bi bi-journal-text"></i>
      <span>Seguimientos</span>
    </a>
    <button id="bottom-nav-tomar-lista" type="button" class="bottom-nav-item bottom-nav-item-primary" 
            data-bs-toggle="modal" data-bs-target="#modalSeleccionarGrupoLista">
      <i class="bi bi-calendar-plus-fill"></i>
      <span>Tomar Lista</span>
    </button>
    <a href="/GESTACAD/tutorias/mi-pat" class="bottom-nav-item <?= active(['tutorias/mi-pat', 'mi-pat']) ? 'active' : '' ?>">
      <i class="bi bi-clipboard-check"></i>
      <span>Mi PAT</span>
    </a>
    <button id="bottom-nav-config" type="button" class="bottom-nav-item">
      <i class="bi bi-gear-fill"></i>
      <span>Configuración</span>
    </button>
  <?php endif; ?>
</nav>

<?php if ($nivel == 3 && !empty($grupos_tutor)): ?>
<!-- Modal para seleccionar grupo y tomar lista -->
<div class="modal fade" id="modalSeleccionarGrupoLista" tabindex="-1" aria-labelledby="modalSeleccionarGrupoListaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalSeleccionarGrupoListaLabel">
          <i class="bi bi-calendar-plus-fill me-2"></i>
          Seleccionar Grupo para Tomar Lista
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted mb-3">Selecciona el grupo para el cual deseas tomar la lista de asistencia grupal:</p>
        <div class="list-group">
          <?php foreach ($grupos_tutor as $grupo): ?>
            <button type="button" 
                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center grupo-seleccionar-lista"
                    data-grupo-id="<?= htmlspecialchars($grupo['id_grupo']) ?>"
                    data-grupo-nombre="<?= htmlspecialchars($grupo['nombre']) ?>">
              <div>
                <i class="bi bi-people-fill text-primary me-2"></i>
                <strong><?= htmlspecialchars($grupo['nombre']) ?></strong>
              </div>
              <i class="bi bi-chevron-right text-muted"></i>
            </button>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar la selección de grupo para tomar lista
    const botonesGrupo = document.querySelectorAll('.grupo-seleccionar-lista');
    const modalSeleccionar = document.getElementById('modalSeleccionarGrupoLista');
    
    botonesGrupo.forEach(btn => {
        btn.addEventListener('click', function() {
            const grupoId = this.getAttribute('data-grupo-id');
            const grupoNombre = this.getAttribute('data-grupo-nombre');
            
            // Cerrar el modal de selección
            if (modalSeleccionar) {
                const bsModal = bootstrap.Modal.getInstance(modalSeleccionar);
                if (bsModal) {
                    bsModal.hide();
                }
            }
            
            // Intentar abrir el modal de tutoría grupal directamente
            // Si no está disponible (modales no cargados), redirigir
            const modalTutoriaGrupal = document.getElementById('modalTutoriaGrupal');
            if (modalTutoriaGrupal) {
                // Disparar evento para que el código de tutorias.js lo maneje
                // Simular click en botón con las clases correctas
                const tempBtn = document.createElement('button');
                tempBtn.className = 'btn-tutoria-grupal-today';
                tempBtn.setAttribute('data-grupo-id', grupoId);
                tempBtn.setAttribute('data-grupo-nombre', grupoNombre);
                tempBtn.style.display = 'none';
                document.body.appendChild(tempBtn);
                tempBtn.click();
                document.body.removeChild(tempBtn);
            } else {
                // Si el modal no está disponible, redirigir
                window.location.href = `/GESTACAD/gestionar-listas?id_grupo=${grupoId}`;
            }
        });
    });
});
</script>
<?php endif; ?>
