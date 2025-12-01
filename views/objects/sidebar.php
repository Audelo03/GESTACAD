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
      if ($current_page === $page || $current_page === $page . '.php') {
        return 'active';
      }
    }
    return '';
  }
}
if (isset($_SESSION))
  $nivel = (int) $_SESSION["usuario_nivel"];

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

      <?php if ($nivel == 1): ?>
        <li class="nav-item mobile-hidden">
          <a href="/GESTACAD/dashboard" class="nav-link sidebar-link <?= active(['dashboard']); ?>" <?= active(['dashboard']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Panel Principal"' ?>>
            <i class="bi bi-speedometer2 me-2 sidebar-icon"></i> <span class="sidebar-text">Dashboard</span>
          </a>
        </li>
      <?php endif; ?>

      <li class="mobile-hidden">
        <a href="/GESTACAD/listas" class="nav-link sidebar-link <?= active(['listas']); ?>" <?= active(['listas']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Lista de Alumnos"' ?>>
          <i class="bi bi-people me-2 sidebar-icon"></i> <span class="sidebar-text">Alumnos</span>
        </a>
      </li>

      <?php if ($nivel == 1): ?>
        <li class="mobile-hidden">
          <a href="/GESTACAD/estadisticas" class="nav-link sidebar-link <?= active(['estadisticas']); ?>"
            <?= active(['estadisticas']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Estadísticas y Reportes"' ?>>
            <i class="bi bi-bar-chart-fill me-2 sidebar-icon"></i> <span class="sidebar-text">Estadísticas</span>
          </a>
        </li>
      <?php endif; ?>

      <li class="mobile-hidden">
        <a href="/GESTACAD/seguimientos" class="nav-link sidebar-link <?= active(['seguimientos']); ?>"
          <?= active(['seguimientos']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Seguimientos"' ?>>
          <i class="bi bi-journal-text me-2 sidebar-icon"></i> <span class="sidebar-text">Seguimientos</span>
        </a>
      </li>

      <?php if ($nivel == 1): ?>

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
      <?php endif; ?>

      <?php if ($nivel <= 2): // Admin y Coordinador ?>
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
      <?php endif; ?>

      <?php if ($nivel <= 3): // Admin, Coordinador y Tutor ?>
        <h6 class="text-uppercase text-secondary fw-bold small mt-3 mb-2 sidebar-section-title">Tutorías</h6>

        <li>
          <a href="/GESTACAD/tutorias/pat" class="nav-link sidebar-link <?= active(['pat']); ?>" <?= active(['pat']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Plan de Acción Tutorial"' ?>>
            <i class="bi bi-clipboard-check me-2 sidebar-icon"></i> <span class="sidebar-text">PAT</span>
          </a>
        </li>

        <li>
          <a href="/GESTACAD/tutorias/canalizacion" class="nav-link sidebar-link <?= active(['canalizacion']); ?>"
            <?= active(['canalizacion']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Canalización de Alumnos"' ?>>
            <i class="bi bi-arrow-right-circle me-2 sidebar-icon"></i> <span class="sidebar-text">Canalización</span>
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
  <?php if ($nivel != 3): // No mostrar dashboard a tutores ?>
  <a href="/GESTACAD/dashboard" class="bottom-nav-item <?= active(['dashboard']) ? 'active' : '' ?>">
    <i class="bi bi-house-door-fill"></i>
    <span>Dashboard</span>
  </a>
  <?php endif; ?>
  <a href="/GESTACAD/listas" class="bottom-nav-item <?= active(['listas', 'alumnos']) ? 'active' : '' ?>">
    <i class="bi bi-people-fill"></i>
    <span>Alumnos</span>
  </a>
  <?php if ($nivel == 1): // Solo administradores ven estadísticas ?>
  <a href="/GESTACAD/estadisticas" class="bottom-nav-item <?= active(['estadisticas']) ? 'active' : '' ?>">
    <i class="bi bi-bar-chart-line-fill"></i>
    <span>Estadísticas</span>
  </a>
  <?php endif; ?>
  <a href="/GESTACAD/seguimientos" class="bottom-nav-item <?= active(['seguimientos']) ? 'active' : '' ?>">
    <i class="bi bi-journal-text"></i>
    <span>Seguimientos</span>
  </a>
  <?php if ($nivel <= 3): // Admin, Coordinador y Tutor ven tutorías ?>
  <a href="/GESTACAD/tutorias/pat" class="bottom-nav-item <?= active(['pat']) ? 'active' : '' ?>">
    <i class="bi bi-clipboard-check"></i>
    <span>Tutorías</span>
  </a>
  <?php endif; ?>
  <a href="/GESTACAD/profile" class="bottom-nav-item <?= active(['profile']) ? 'active' : '' ?>">
    <i class="bi bi-person-circle"></i>
    <span>Perfil</span>
  </a>
  <button id="bottom-nav-config" type="button" class="bottom-nav-item">
    <i class="bi bi-gear-fill"></i>
    <span>Configuración</span>
  </button>
  <?php if ($nivel == 1): // Solo administradores ven gestión ?>
  <button id="bottom-nav-manage" type="button" class="bottom-nav-item">
    <i class="bi bi-columns-gap"></i>
    <span>Gestión</span>
  </button>
  <?php endif; ?>
</nav>
