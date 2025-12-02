<?php
require_once __DIR__ . '/../controllers/authController.php';
require_once __DIR__ . '/../controllers/alumnoController.php';
require_once __DIR__ . '/../config/db.php';

$auth = new AuthController($conn);
$auth->checkAuth();

$id_grupo = isset($_GET['id_grupo']) ? (int)$_GET['id_grupo'] : 0;
if ($id_grupo === 0) {
    header('Location: /GESTACAD/dashboard');
    exit();
}

$alumnoController = new AlumnoController($conn);
$alumnos = $alumnoController->getAlumnosByGrupo($id_grupo);
$nombre_grupo = $alumnoController->getNombreGrupo($id_grupo);

$page_title = "Alumnos del Grupo: " . htmlspecialchars($nombre_grupo);
include 'objects/header.php';
?>

<div class="container mt-3 mt-md-5">
    <div class="card shadow-sm mb-3 mb-md-4">
        <div class="card-body">
            <h1 class="h4 h3-md mb-1"><i class="bi bi-people-fill me-2"></i>Alumnos del Grupo</h1>
            <h2 class="h6 h5-md text-muted mb-0"><?= htmlspecialchars($nombre_grupo) ?></h2>
        </div>
    </div>

    <!-- Botón para capturar calificaciones -->
    <div class="card shadow-sm mb-3 mb-md-4">
        <div class="card-body text-center p-2 p-md-3">
            <button type="button" class="btn btn-primary w-100 w-md-auto btn-lg" id="btnCapturarCalificaciones">
                <i class="bi bi-journal-check me-2"></i>Capturar Calificaciones
            </button>
        </div>
    </div>

    <!-- Tabla de alumnos -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="h5 h6-md mb-0"><i class="bi bi-list-ul me-2"></i>Lista de Alumnos</h3>
        </div>
        <div class="card-body p-2 p-md-3">
            <!-- Buscador -->
            <div class="mb-3">
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-primary text-white">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" 
                           class="form-control" 
                           id="buscadorAlumnos" 
                           placeholder="Buscar por matrícula o nombre...">
                    <button class="btn btn-outline-secondary d-none d-md-block" type="button" id="btnLimpiarBusqueda" title="Limpiar búsqueda">
                        <i class="bi bi-x-circle"></i>
                    </button>
                </div>
                <button class="btn btn-outline-secondary btn-sm w-100 d-md-none mt-2" type="button" id="btnLimpiarBusquedaMobile">
                    <i class="bi bi-x-circle me-1"></i>Limpiar búsqueda
                </button>
            </div>
            
            <!-- Vista de tabla para desktop -->
            <div class="table-responsive d-none d-md-block" style="max-height: 600px; overflow-y: auto; position: relative;">
                <table class="table table-hover table-striped mb-0">
                        <thead class="table-light sticky-top">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Matrícula</th>
                            <th>Nombre Completo</th>
                            <th style="min-width: 200px; width: 200px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaAlumnosBody">
                        <?php foreach ($alumnos as $index => $alumno): ?>
                            <tr class="fila-alumno" 
                                data-matricula="<?= htmlspecialchars(strtolower($alumno['matricula'])) ?>"
                                data-nombre="<?= htmlspecialchars(strtolower($alumno['nombre_completo'])) ?>">
                                <td><?= $index + 1 ?></td>
                                <td><strong><?= htmlspecialchars($alumno['matricula']) ?></strong></td>
                                <td><?= htmlspecialchars($alumno['nombre_completo']) ?></td>
                                <td style="position: relative;">
                                    <div class="d-flex gap-1 flex-wrap">
                                        <button type="button"
                                                class="btn btn-primary btn-sm btn-inscribir-alumno"
                                                data-alumno-id="<?= $alumno['id_alumno'] ?>"
                                                data-alumno-nombre="<?= htmlspecialchars($alumno['nombre_completo']) ?>"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalInscribirAlumno"
                                                title="Inscribir en una clase">
                                            <i class="bi bi-plus-circle me-1"></i>Inscribir
                                        </button>
                                        <button type="button"
                                                class="btn btn-success btn-sm btn-calificaciones-alumno"
                                                data-alumno-id="<?= $alumno['id_alumno'] ?>"
                                                data-alumno-nombre="<?= htmlspecialchars($alumno['nombre_completo']) ?>"
                                                data-alumno-matricula="<?= htmlspecialchars($alumno['matricula']) ?>"
                                                title="Capturar Calificaciones">
                                            <i class="bi bi-journal-check me-1"></i>Calif.
                                        </button>
                                        <div class="btn-group btn-group-sm" style="position: static;">
                                            <button type="button" 
                                                    class="btn btn-outline-secondary dropdown-toggle" 
                                                    data-bs-toggle="dropdown" 
                                                    data-bs-auto-close="true"
                                                    aria-expanded="false"
                                                    title="Más acciones">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end" style="z-index: 1050; position: absolute;">
                                                <li>
                                                    <a class="dropdown-item btn-editar-clases-alumno" 
                                                       href="#"
                                                       data-alumno-id="<?= $alumno['id_alumno'] ?>"
                                                       data-alumno-nombre="<?= htmlspecialchars($alumno['nombre_completo']) ?>"
                                                       data-bs-toggle="modal"
                                                       data-bs-target="#modalEditarClasesAlumno">
                                                        <i class="bi bi-pencil-square me-2"></i>Editar Clases
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item btn-inferencias-alumno" 
                                                       href="#"
                                                       data-alumno-id="<?= $alumno['id_alumno'] ?>"
                                                       data-alumno-nombre="<?= htmlspecialchars($alumno['nombre_completo']) ?>"
                                                       data-alumno-matricula="<?= htmlspecialchars($alumno['matricula']) ?>"
                                                       data-bs-toggle="modal"
                                                       data-bs-target="#modalInferenciasAlumno">
                                                        <i class="bi bi-graph-up-arrow me-2"></i>Ver Inferencias
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item btn-inscribir-beca" 
                                                       href="#"
                                                       data-alumno-id="<?= $alumno['id_alumno'] ?>"
                                                       data-alumno-nombre="<?= htmlspecialchars($alumno['nombre_completo']) ?>"
                                                       data-bs-toggle="modal"
                                                       data-bs-target="#modalInscribirBeca">
                                                        <i class="bi bi-award me-2"></i>Inscribir en Beca
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item btn-canalizar-alumno" 
                                                       href="#"
                                                       data-alumno-id="<?= $alumno['id_alumno'] ?>"
                                                       data-alumno-nombre="<?= htmlspecialchars($alumno['nombre_completo']) ?>"
                                                       data-alumno-matricula="<?= htmlspecialchars($alumno['matricula']) ?>"
                                                       data-bs-toggle="modal"
                                                       data-bs-target="#modalCanalizarAlumno">
                                                        <i class="bi bi-arrow-right-circle me-2"></i>Canalizar
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Vista de cards para móvil -->
            <div class="d-md-none" id="cardsAlumnosContainer" style="max-height: 600px; overflow-y: auto; position: relative;">
                <?php foreach ($alumnos as $index => $alumno): ?>
                    <div class="card mb-3 fila-alumno-card" 
                         data-matricula="<?= htmlspecialchars(strtolower($alumno['matricula'])) ?>"
                         data-nombre="<?= htmlspecialchars(strtolower($alumno['nombre_completo'])) ?>">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="flex-grow-1">
                                    <h6 class="card-title mb-1 fw-bold"><?= htmlspecialchars($alumno['nombre_completo']) ?></h6>
                                    <p class="card-text text-muted mb-0 small">
                                        <i class="bi bi-person-badge me-1"></i>
                                        <strong>Matrícula:</strong> <?= htmlspecialchars($alumno['matricula']) ?>
                                    </p>
                                </div>
                                <span class="badge bg-secondary">#<?= $index + 1 ?></span>
                            </div>
                            <div class="d-grid gap-2 mt-3">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <button type="button"
                                                class="btn btn-primary w-100 btn-inscribir-alumno"
                                                data-alumno-id="<?= $alumno['id_alumno'] ?>"
                                                data-alumno-nombre="<?= htmlspecialchars($alumno['nombre_completo']) ?>"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalInscribirAlumno">
                                            <i class="bi bi-plus-circle me-1"></i>Inscribir
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <button type="button"
                                                class="btn btn-success w-100 btn-calificaciones-alumno"
                                                data-alumno-id="<?= $alumno['id_alumno'] ?>"
                                                data-alumno-nombre="<?= htmlspecialchars($alumno['nombre_completo']) ?>"
                                                data-alumno-matricula="<?= htmlspecialchars($alumno['matricula']) ?>">
                                            <i class="bi bi-journal-check me-1"></i>Calif.
                                        </button>
                                    </div>
                                </div>
                                <div class="dropdown" style="position: static;">
                                    <button class="btn btn-outline-secondary w-100 dropdown-toggle" 
                                            type="button" 
                                            id="dropdownMenuButton<?= $alumno['id_alumno'] ?>" 
                                            data-bs-toggle="dropdown" 
                                            data-bs-auto-close="true"
                                            aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical me-1"></i>Más acciones
                                    </button>
                                    <ul class="dropdown-menu w-100" aria-labelledby="dropdownMenuButton<?= $alumno['id_alumno'] ?>" style="z-index: 9999 !important; position: absolute !important;">
                                        <li>
                                            <a class="dropdown-item btn-editar-clases-alumno" 
                                               href="#"
                                               data-alumno-id="<?= $alumno['id_alumno'] ?>"
                                               data-alumno-nombre="<?= htmlspecialchars($alumno['nombre_completo']) ?>"
                                               data-bs-toggle="modal"
                                               data-bs-target="#modalEditarClasesAlumno">
                                                <i class="bi bi-pencil-square me-2"></i>Editar Clases
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item btn-inferencias-alumno" 
                                               href="#"
                                               data-alumno-id="<?= $alumno['id_alumno'] ?>"
                                               data-alumno-nombre="<?= htmlspecialchars($alumno['nombre_completo']) ?>"
                                               data-alumno-matricula="<?= htmlspecialchars($alumno['matricula']) ?>"
                                               data-bs-toggle="modal"
                                               data-bs-target="#modalInferenciasAlumno">
                                                <i class="bi bi-graph-up-arrow me-2"></i>Ver Inferencias
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item btn-inscribir-beca" 
                                               href="#"
                                               data-alumno-id="<?= $alumno['id_alumno'] ?>"
                                               data-alumno-nombre="<?= htmlspecialchars($alumno['nombre_completo']) ?>"
                                               data-bs-toggle="modal"
                                               data-bs-target="#modalInscribirBeca">
                                                <i class="bi bi-award me-2"></i>Inscribir en Beca
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item btn-canalizar-alumno" 
                                               href="#"
                                               data-alumno-id="<?= $alumno['id_alumno'] ?>"
                                               data-alumno-nombre="<?= htmlspecialchars($alumno['nombre_completo']) ?>"
                                               data-alumno-matricula="<?= htmlspecialchars($alumno['matricula']) ?>"
                                               data-bs-toggle="modal"
                                               data-bs-target="#modalCanalizarAlumno">
                                                <i class="bi bi-arrow-right-circle me-2"></i>Canalizar
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal para inscribir alumno -->
<div class="modal fade" id="modalInscribirAlumno" tabindex="-1" aria-labelledby="modalInscribirAlumnoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalInscribirAlumnoLabel">Inscribir Alumno en Clase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formInscribirAlumno">
                    <input type="hidden" id="inscribir-alumno-id" name="alumno_id">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Alumno:</label>
                        <p id="inscribir-alumno-nombre" class="mb-0"></p>
                    </div>
                    <div class="mb-3">
                        <label for="selectClase" class="form-label fw-bold">Seleccionar Clase:</label>
                        <select id="selectClase" name="clase_id" class="form-select" required>
                            <option value="">Cargando clases...</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer flex-column flex-sm-row gap-2">
                <button type="button" class="btn btn-secondary w-100 w-sm-auto" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary w-100 w-sm-auto" id="btnConfirmarInscripcion">
                    <i class="bi bi-check-circle me-1"></i>Inscribir
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para inscribir alumno en beca -->
<div class="modal fade" id="modalInscribirBeca" tabindex="-1" aria-labelledby="modalInscribirBecaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold" id="modalInscribirBecaLabel">
                    Inscribir Alumno en Beca
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formInscribirBeca">
                    <input type="hidden" id="beca-alumno-id" name="alumno_id">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Alumno:</label>
                        <p id="beca-alumno-nombre" class="mb-0"></p>
                    </div>
                    <div class="mb-3">
                        <label for="selectBeca" class="form-label fw-bold">
                            Seleccionar Beca <span class="text-danger">*</span>
                        </label>
                        <select id="selectBeca" name="beca_id" class="form-select" required>
                            <option value="">Cargando becas...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="selectPeriodoBeca" class="form-label fw-bold">
                            Periodo Escolar <span class="text-danger">*</span>
                        </label>
                        <select id="selectPeriodoBeca" name="periodo_id" class="form-select" required>
                            <option value="">Cargando periodos...</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="porcentajeBeca" class="form-label fw-bold">
                                Porcentaje (%)
                            </label>
                            <input type="number" id="porcentajeBeca" name="porcentaje" class="form-control" 
                                   min="0" max="100" step="0.01" placeholder="0.00">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="montoBeca" class="form-label fw-bold">
                                Monto ($)
                            </label>
                            <input type="number" id="montoBeca" name="monto" class="form-control" 
                                   min="0" step="0.01" placeholder="0.00">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer flex-column flex-sm-row gap-2">
                <button type="button" class="btn btn-secondary w-100 w-sm-auto" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning w-100 w-sm-auto" id="btnConfirmarInscripcionBeca">
                    Asignar Beca
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para canalizar alumno -->
<div class="modal fade" id="modalCanalizarAlumno" tabindex="-1" aria-labelledby="modalCanalizarAlumnoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="modalCanalizarAlumnoLabel">
                    <i class="bi bi-arrow-right-circle me-2"></i>
                    Canalizar Alumno
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formCanalizarAlumno">
                    <input type="hidden" id="canalizar-alumno-id" name="alumno_id">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Alumno:</label>
                        <div class="alert alert-info mb-0">
                            <strong id="canalizar-alumno-nombre">-</strong>
                            <br>
                            <small class="text-muted">Matrícula: <span id="canalizar-alumno-matricula">-</span></small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="canalizar-periodo-id" class="form-label fw-bold">
                                Periodo Escolar <span class="text-danger">*</span>
                            </label>
                            <select id="canalizar-periodo-id" name="periodo_id" class="form-select" required>
                                <option value="">Cargando periodos...</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="canalizar-area-id" class="form-label fw-bold">
                                Área de Canalización <span class="text-danger">*</span>
                            </label>
                            <select id="canalizar-area-id" name="area_id" class="form-select" required>
                                <option value="">Cargando áreas...</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="canalizar-observacion" class="form-label fw-bold">
                            Observación / Motivo <span class="text-danger">*</span>
                        </label>
                        <textarea id="canalizar-observacion" 
                                  name="observacion" 
                                  class="form-control" 
                                  rows="4" 
                                  required
                                  placeholder="Describe el motivo de la canalización, situación del alumno, y cualquier información relevante..."></textarea>
                        <small class="text-muted">Proporciona información detallada sobre la situación que requiere la canalización</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer flex-column flex-sm-row gap-2">
                <button type="button" class="btn btn-secondary w-100 w-sm-auto" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary w-100 w-sm-auto" id="btnConfirmarCanalizacion">
                    <i class="bi bi-check-circle me-1"></i>Guardar Canalización
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar clases del alumno -->
<div class="modal fade" id="modalEditarClasesAlumno" tabindex="-1" aria-labelledby="modalEditarClasesAlumnoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-md-down modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarClasesAlumnoLabel"><i class="bi bi-book me-2"></i>Clases Inscritas del Alumno</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 mb-md-4">
                    <label class="form-label fw-bold">Alumno:</label>
                    <p id="editar-clases-alumno-nombre" class="mb-0 nombre-alumno-mobile"></p>
                </div>
                <div id="editar-clases-lista" class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <p class="text-center text-muted">Cargando clases...</p>
                </div>
            </div>
            <div class="modal-footer flex-column flex-sm-row gap-2">
                <button type="button" class="btn btn-secondary w-100 w-sm-auto" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para capturar calificaciones -->
<div class="modal fade" id="modalCapturarCalificaciones" tabindex="-1" aria-labelledby="modalCapturarCalificacionesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-md-down modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalCapturarCalificacionesLabel">
                    <i class="bi bi-journal-check me-2"></i>Capturar Calificaciones
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Navegación y progreso -->
                <div class="row mb-3 mb-md-4">
                    <div class="col-12">
                        <div class="d-flex flex-column flex-md-row align-items-center justify-content-center gap-2 gap-md-3">
                            <button type="button" class="btn btn-outline-primary w-100 w-md-auto order-2 order-md-1" id="btnAlumnoAnterior">
                                <i class="bi bi-chevron-left"></i> <span class="d-none d-sm-inline">Anterior</span>
                            </button>
                            <div class="text-center order-1 order-md-2 flex-grow-1">
                                <h5 class="mb-0 h6 h5-md" id="calificaciones-alumno-nombre">-</h5>
                                <small class="text-muted d-block" id="calificaciones-alumno-matricula">-</small>
                                <div class="mt-2">
                                    <span class="badge bg-info fs-6" id="calificaciones-progreso">0 / 0</span>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary w-100 w-md-auto order-3" id="btnAlumnoSiguiente">
                                <span class="d-none d-sm-inline">Siguiente</span> <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Pestañas de parciales -->
                <div class="mb-3 mb-md-4">
                    <ul class="nav nav-pills nav-justified flex-nowrap" id="parciales-tabs" role="tablist">
                        <li class="nav-item flex-fill" role="presentation">
                            <button class="nav-link active small" id="parcial-1-tab" data-bs-toggle="pill" data-bs-target="#parcial-1" type="button" role="tab" data-parcial="1">
                                <i class="bi bi-1-circle d-none d-sm-inline me-1"></i><span class="d-sm-none">P1</span><span class="d-none d-sm-inline">Parcial 1</span>
                            </button>
                        </li>
                        <li class="nav-item flex-fill" role="presentation">
                            <button class="nav-link small" id="parcial-2-tab" data-bs-toggle="pill" data-bs-target="#parcial-2" type="button" role="tab" data-parcial="2">
                                <i class="bi bi-2-circle d-none d-sm-inline me-1"></i><span class="d-sm-none">P2</span><span class="d-none d-sm-inline">Parcial 2</span>
                            </button>
                        </li>
                        <li class="nav-item flex-fill" role="presentation">
                            <button class="nav-link small" id="parcial-3-tab" data-bs-toggle="pill" data-bs-target="#parcial-3" type="button" role="tab" data-parcial="3">
                                <i class="bi bi-3-circle d-none d-sm-inline me-1"></i><span class="d-sm-none">P3</span><span class="d-none d-sm-inline">Parcial 3</span>
                            </button>
                        </li>
                        <li class="nav-item flex-fill" role="presentation">
                            <button class="nav-link small" id="parcial-4-tab" data-bs-toggle="pill" data-bs-target="#parcial-4" type="button" role="tab" data-parcial="4">
                                <i class="bi bi-4-circle d-none d-sm-inline me-1"></i><span class="d-sm-none">P4</span><span class="d-none d-sm-inline">Parcial 4</span>
                            </button>
                        </li>
                    </ul>
                </div>
                
                <!-- Contenido de las pestañas -->
                <div class="tab-content" id="parciales-tab-content">
                    <div class="tab-pane fade show active" id="parcial-1" role="tabpanel">
                        <div id="calificaciones-materias-container">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="parcial-2" role="tabpanel">
                        <div id="calificaciones-materias-container-2">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="parcial-3" role="tabpanel">
                        <div id="calificaciones-materias-container-3">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="parcial-4" role="tabpanel">
                        <div id="calificaciones-materias-container-4">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" id="btnGuardarCalificaciones">
                    <i class="bi bi-save me-1"></i>Guardar Calificaciones
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para capturar calificaciones individuales (sin navegación) -->
<div class="modal fade" id="modalCalificacionesIndividual" tabindex="-1" aria-labelledby="modalCalificacionesIndividualLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-md-down modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalCalificacionesIndividualLabel">
                    <i class="bi bi-journal-check me-2"></i>Calificaciones del Alumno
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Información del alumno -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="text-center">
                            <h5 class="mb-0" id="calif-individual-alumno-nombre">-</h5>
                            <small class="text-muted" id="calif-individual-alumno-matricula">-</small>
                        </div>
                    </div>
                </div>
                
                <!-- Pestañas de parciales -->
                <div class="mb-4">
                    <ul class="nav nav-pills nav-justified" id="parciales-tabs-individual" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="parcial-individual-1-tab" data-bs-toggle="pill" data-bs-target="#parcial-individual-1" type="button" role="tab" data-parcial="1">
                                <i class="bi bi-1-circle me-1"></i>Parcial 1
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="parcial-individual-2-tab" data-bs-toggle="pill" data-bs-target="#parcial-individual-2" type="button" role="tab" data-parcial="2">
                                <i class="bi bi-2-circle me-1"></i>Parcial 2
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="parcial-individual-3-tab" data-bs-toggle="pill" data-bs-target="#parcial-individual-3" type="button" role="tab" data-parcial="3">
                                <i class="bi bi-3-circle me-1"></i>Parcial 3
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="parcial-individual-4-tab" data-bs-toggle="pill" data-bs-target="#parcial-individual-4" type="button" role="tab" data-parcial="4">
                                <i class="bi bi-4-circle me-1"></i>Parcial 4
                            </button>
                        </li>
                    </ul>
                </div>
                
                <!-- Contenido de las pestañas -->
                <div class="tab-content" id="parciales-tab-content-individual">
                    <div class="tab-pane fade show active" id="parcial-individual-1" role="tabpanel">
                        <div id="calif-individual-materias-container-1">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="parcial-individual-2" role="tabpanel">
                        <div id="calif-individual-materias-container-2">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="parcial-individual-3" role="tabpanel">
                        <div id="calif-individual-materias-container-3">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="parcial-individual-4" role="tabpanel">
                        <div id="calif-individual-materias-container-4">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" id="btnGuardarCalificacionesIndividual">
                    <i class="bi bi-save me-1"></i>Guardar Calificaciones
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para mostrar inferencias del alumno -->
<div class="modal fade" id="modalInferenciasAlumno" tabindex="-1" aria-labelledby="modalInferenciasAlumnoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-md-down modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="modalInferenciasAlumnoLabel">
                    <i class="bi bi-graph-up-arrow me-2"></i>Análisis de Inferencias
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalInferenciasBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-info" role="status">
                        <span class="visually-hidden">Cargando inferencias...</span>
                    </div>
                    <p class="mt-3 text-muted">Analizando datos del alumno...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<?php
include 'objects/footer.php';
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables globales
    let alumnosList = <?= json_encode(array_map(function($a) { return ['id' => $a['id_alumno'], 'nombre' => $a['nombre_completo'], 'matricula' => $a['matricula']]; }, $alumnos)) ?>;
    
    // ========== MODAL INSCRIBIR ALUMNO ==========
    const modalInscribir = document.getElementById('modalInscribirAlumno');
    const formInscribir = document.getElementById('formInscribirAlumno');
    const btnConfirmar = document.getElementById('btnConfirmarInscripcion');
    const inputAlumnoId = document.getElementById('inscribir-alumno-id');
    const nombreAlumnoInscribir = document.getElementById('inscribir-alumno-nombre');
    const selectClase = document.getElementById('selectClase');
    let select2Instance = null;

    // Event listeners para botones de inscribir
    document.querySelectorAll('.btn-inscribir-alumno').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const alumnoId = this.getAttribute('data-alumno-id');
            const alumnoNombre = this.getAttribute('data-alumno-nombre');
            inputAlumnoId.value = alumnoId;
            nombreAlumnoInscribir.textContent = alumnoNombre;
            cargarClases();
        });
    });

    // Cargar clases disponibles
    function cargarClases() {
        fetch('/GESTACAD/controllers/clasesController.php?action=index')
            .then(response => response.json())
            .then(clases => {
                // Destruir Select2 si existe
                if (select2Instance && typeof $ !== 'undefined' && $(selectClase).hasClass('select2-hidden-accessible')) {
                    $(selectClase).select2('destroy');
                    select2Instance = null;
                }
                
                selectClase.innerHTML = '<option value="">Seleccione una clase...</option>';
                clases.forEach(function(clase) {
                    const option = document.createElement('option');
                    option.value = clase.id;
                    const texto = `${clase.asignatura_nombre || 'N/A'} (${clase.asignatura_clave || 'N/A'}) - Sección: ${clase.seccion || 'N/A'} - ${clase.docente_nombre || ''} ${clase.docente_apellido || ''} - ${clase.aula || 'N/A'} - ${clase.modalidad_nombre || 'N/A'} - ${clase.periodo_nombre || 'N/A'} - Cupo: ${clase.cupo || 0}`;
                    option.textContent = texto;
                    selectClase.appendChild(option);
                });

                // Inicializar Select2 solo si jQuery está disponible
                if (typeof $ !== 'undefined') {
                    $(selectClase).select2({
                        theme: 'bootstrap-5',
                        dropdownParent: $(modalInscribir),
                        placeholder: 'Buscar clase...',
                        width: '100%',
                        language: {
                            noResults: function() {
                                return "No se encontraron resultados";
                            },
                            searching: function() {
                                return "Buscando...";
                            }
                        }
                    });
                    select2Instance = selectClase;
                }
            })
            .catch(error => {
                console.error('Error al cargar clases:', error);
                selectClase.innerHTML = '<option value="">Error al cargar clases</option>';
            });
    }

    // Limpiar Select2 al cerrar modal
    modalInscribir.addEventListener('hidden.bs.modal', function() {
        if (select2Instance && typeof $ !== 'undefined' && $(selectClase).hasClass('select2-hidden-accessible')) {
            $(selectClase).select2('destroy');
            select2Instance = null;
        }
    });

    // Confirmar inscripción
    btnConfirmar.addEventListener('click', function() {
        const alumnoId = inputAlumnoId.value;
        const claseId = selectClase.value;

        if (!alumnoId || !claseId) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Datos incompletos',
                    text: 'Por favor, seleccione una clase.'
                });
            } else {
                alert('Por favor, seleccione una clase.');
            }
            return;
        }

        const formData = new FormData();
        formData.append('alumno_id', alumnoId);
        formData.append('clase_id', claseId);

        fetch('/GESTACAD/controllers/inscripcionesController.php?action=store', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'ok') {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'El alumno ha sido inscrito correctamente.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    alert('El alumno ha sido inscrito correctamente.');
                }
                const bsModal = bootstrap.Modal.getInstance(modalInscribir);
                if (bsModal) bsModal.hide();
            } else {
                throw new Error(data.message || 'Error al inscribir al alumno');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'No se pudo inscribir al alumno.'
                });
            } else {
                alert('Error: ' + error.message);
            }
        });
    });

    // ========== MODAL INSCRIBIR EN BECA ==========
    const modalInscribirBeca = document.getElementById('modalInscribirBeca');
    const formInscribirBeca = document.getElementById('formInscribirBeca');
    const btnConfirmarBeca = document.getElementById('btnConfirmarInscripcionBeca');
    const inputAlumnoIdBeca = document.getElementById('beca-alumno-id');
    const nombreAlumnoBeca = document.getElementById('beca-alumno-nombre');
    const selectBeca = document.getElementById('selectBeca');
    const selectPeriodoBeca = document.getElementById('selectPeriodoBeca');

    // Event listeners para botones de inscribir en beca
    document.querySelectorAll('.btn-inscribir-beca').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            // Cerrar dropdown si está abierto
            const dropdown = this.closest('.dropdown');
            if (dropdown) {
                const dropdownInstance = bootstrap.Dropdown.getInstance(dropdown.querySelector('[data-bs-toggle="dropdown"]'));
                if (dropdownInstance) dropdownInstance.hide();
            }
            const alumnoId = this.getAttribute('data-alumno-id');
            const alumnoNombre = this.getAttribute('data-alumno-nombre');
            inputAlumnoIdBeca.value = alumnoId;
            nombreAlumnoBeca.textContent = alumnoNombre;
            cargarBecas();
            cargarPeriodos();
        });
    });

    // Cargar becas disponibles
    function cargarBecas() {
        selectBeca.innerHTML = '<option value="">Cargando becas...</option>';
        
        fetch('/GESTACAD/controllers/becasController.php?action=getBecasActivas')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    selectBeca.innerHTML = '<option value="">Seleccione una beca...</option>';
                    data.data.forEach(beca => {
                        const option = document.createElement('option');
                        option.value = beca.id;
                        option.textContent = `${beca.clave} - ${beca.nombre}`;
                        selectBeca.appendChild(option);
                    });
                } else {
                    selectBeca.innerHTML = '<option value="">No hay becas disponibles</option>';
                }
            })
            .catch(error => {
                console.error('Error al cargar becas:', error);
                selectBeca.innerHTML = '<option value="">Error al cargar becas</option>';
            });
    }

    // Cargar periodos disponibles
    function cargarPeriodos() {
        selectPeriodoBeca.innerHTML = '<option value="">Cargando periodos...</option>';
        
        fetch('/GESTACAD/controllers/periodosController.php?action=index')
            .then(response => response.json())
            .then(periodos => {
                if (periodos && periodos.length > 0) {
                    selectPeriodoBeca.innerHTML = '<option value="">Seleccione un periodo...</option>';
                    periodos.forEach(periodo => {
                        const option = document.createElement('option');
                        option.value = periodo.id;
                        option.textContent = periodo.nombre;
                        selectPeriodoBeca.appendChild(option);
                    });
                } else {
                    selectPeriodoBeca.innerHTML = '<option value="">No hay periodos disponibles</option>';
                }
            })
            .catch(error => {
                console.error('Error al cargar periodos:', error);
                selectPeriodoBeca.innerHTML = '<option value="">Error al cargar periodos</option>';
            });
    }

    // Confirmar inscripción en beca
    btnConfirmarBeca.addEventListener('click', function() {
        const alumnoId = inputAlumnoIdBeca.value;
        const becaId = selectBeca.value;
        const periodoId = selectPeriodoBeca.value;

        if (!alumnoId || !becaId || !periodoId) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Datos incompletos',
                    text: 'Por favor, complete todos los campos requeridos.'
                });
            } else {
                alert('Por favor, complete todos los campos requeridos.');
            }
            return;
        }

        const formData = new FormData(formInscribirBeca);

        btnConfirmarBeca.disabled = true;
        btnConfirmarBeca.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Asignando...';

        fetch('/GESTACAD/controllers/becasController.php?action=asignarBecaAlumno', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: data.message || 'La beca ha sido asignada correctamente.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    alert(data.message || 'La beca ha sido asignada correctamente.');
                }
                const bsModal = bootstrap.Modal.getInstance(modalInscribirBeca);
                if (bsModal) bsModal.hide();
                formInscribirBeca.reset();
            } else {
                throw new Error(data.message || 'Error al asignar la beca');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'No se pudo asignar la beca.'
                });
            } else {
                alert('Error: ' + error.message);
            }
        })
        .finally(() => {
            btnConfirmarBeca.disabled = false;
            btnConfirmarBeca.innerHTML = 'Asignar Beca';
        });
    });

    // ========== MODAL CANALIZAR ALUMNO ==========
    const modalCanalizarAlumno = document.getElementById('modalCanalizarAlumno');
    const formCanalizarAlumno = document.getElementById('formCanalizarAlumno');
    const btnConfirmarCanalizacion = document.getElementById('btnConfirmarCanalizacion');
    const inputAlumnoIdCanalizar = document.getElementById('canalizar-alumno-id');
    const nombreAlumnoCanalizar = document.getElementById('canalizar-alumno-nombre');
    const matriculaAlumnoCanalizar = document.getElementById('canalizar-alumno-matricula');
    const selectPeriodoCanalizar = document.getElementById('canalizar-periodo-id');
    const selectAreaCanalizar = document.getElementById('canalizar-area-id');
    const observacionCanalizar = document.getElementById('canalizar-observacion');

    // Event listeners para botones de canalizar
    document.querySelectorAll('.btn-canalizar-alumno').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            // Cerrar dropdown si está abierto
            const dropdown = this.closest('.dropdown');
            if (dropdown) {
                const dropdownInstance = bootstrap.Dropdown.getInstance(dropdown.querySelector('[data-bs-toggle="dropdown"]'));
                if (dropdownInstance) dropdownInstance.hide();
            }
            const alumnoId = this.getAttribute('data-alumno-id');
            const alumnoNombre = this.getAttribute('data-alumno-nombre');
            const alumnoMatricula = this.getAttribute('data-alumno-matricula');
            
            inputAlumnoIdCanalizar.value = alumnoId;
            nombreAlumnoCanalizar.textContent = alumnoNombre || '-';
            matriculaAlumnoCanalizar.textContent = alumnoMatricula || '-';
            
            cargarPeriodosCanalizar();
            cargarAreasCanalizar();
        });
    });

    // Cargar periodos para canalización
    function cargarPeriodosCanalizar() {
        selectPeriodoCanalizar.innerHTML = '<option value="">Cargando periodos...</option>';
        
        fetch('/GESTACAD/controllers/periodosController.php?action=index')
            .then(response => response.json())
            .then(periodos => {
                if (periodos && periodos.length > 0) {
                    selectPeriodoCanalizar.innerHTML = '<option value="">Seleccione un periodo...</option>';
                    periodos.forEach(periodo => {
                        const option = document.createElement('option');
                        option.value = periodo.id;
                        option.textContent = periodo.nombre;
                        selectPeriodoCanalizar.appendChild(option);
                    });
                } else {
                    selectPeriodoCanalizar.innerHTML = '<option value="">No hay periodos disponibles</option>';
                }
            })
            .catch(error => {
                console.error('Error al cargar periodos:', error);
                selectPeriodoCanalizar.innerHTML = '<option value="">Error al cargar periodos</option>';
            });
    }

    // Cargar áreas de canalización
    function cargarAreasCanalizar() {
        selectAreaCanalizar.innerHTML = '<option value="">Cargando áreas...</option>';
        
        fetch('/GESTACAD/controllers/canalizacionController.php?action=getAreas')
            .then(response => response.json())
            .then(areas => {
                if (areas && areas.length > 0) {
                    selectAreaCanalizar.innerHTML = '<option value="">Seleccione un área...</option>';
                    areas.forEach(area => {
                        const option = document.createElement('option');
                        option.value = area.id;
                        option.textContent = area.nombre;
                        selectAreaCanalizar.appendChild(option);
                    });
                } else {
                    selectAreaCanalizar.innerHTML = '<option value="">No hay áreas disponibles</option>';
                }
            })
            .catch(error => {
                console.error('Error al cargar áreas:', error);
                selectAreaCanalizar.innerHTML = '<option value="">Error al cargar áreas</option>';
            });
    }

    // Confirmar canalización
    btnConfirmarCanalizacion.addEventListener('click', function() {
        if (!formCanalizarAlumno.checkValidity()) {
            formCanalizarAlumno.reportValidity();
            return;
        }

        const formData = new FormData(formCanalizarAlumno);

        btnConfirmarCanalizacion.disabled = true;
        btnConfirmarCanalizacion.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        fetch('/GESTACAD/controllers/canalizacionController.php?action=store', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'ok') {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: data.message || 'Canalización registrada exitosamente.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    alert(data.message || 'Canalización registrada exitosamente.');
                }
                const bsModal = bootstrap.Modal.getInstance(modalCanalizarAlumno);
                if (bsModal) bsModal.hide();
                formCanalizarAlumno.reset();
            } else {
                throw new Error(data.message || 'Error al registrar la canalización');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'No se pudo registrar la canalización.'
                });
            } else {
                alert('Error: ' + error.message);
            }
        })
        .finally(() => {
            btnConfirmarCanalizacion.disabled = false;
            btnConfirmarCanalizacion.innerHTML = '<i class="bi bi-check-circle me-1"></i>Guardar Canalización';
        });
    });

    // ========== MODAL EDITAR CLASES ==========
    const modalEditarClases = document.getElementById('modalEditarClasesAlumno');
    const nombreAlumnoEditar = document.getElementById('editar-clases-alumno-nombre');
    const listaClases = document.getElementById('editar-clases-lista');
    let clasesAlumnoActual = null;
    let alumnoIdClasesActual = null;

    document.querySelectorAll('.btn-editar-clases-alumno').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            // Cerrar dropdown si está abierto
            const dropdown = this.closest('.dropdown');
            if (dropdown) {
                const dropdownInstance = bootstrap.Dropdown.getInstance(dropdown.querySelector('[data-bs-toggle="dropdown"]'));
                if (dropdownInstance) dropdownInstance.hide();
            }
            const alumnoId = this.getAttribute('data-alumno-id');
            const alumnoNombre = this.getAttribute('data-alumno-nombre');
            nombreAlumnoEditar.textContent = alumnoNombre;
            cargarClasesAlumno(alumnoId);
        });
    });

    // Redibujar cuando cambie el tamaño de la ventana
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            if (clasesAlumnoActual && alumnoIdClasesActual) {
                renderizarClases(clasesAlumnoActual, alumnoIdClasesActual);
            }
        }, 250);
    });

    function cargarClasesAlumno(alumnoId) {
        listaClases.innerHTML = '<p class="text-center text-muted">Cargando clases...</p>';
        
        fetch(`/GESTACAD/controllers/inscripcionesController.php?action=index&alumno_id=${alumnoId}`)
            .then(response => response.json())
            .then(inscripciones => {
                clasesAlumnoActual = inscripciones;
                alumnoIdClasesActual = alumnoId;
                
                if (inscripciones.length === 0) {
                    listaClases.innerHTML = '<div class="alert alert-info">Este alumno no está inscrito en ninguna clase.</div>';
                    return;
                }

                renderizarClases(inscripciones, alumnoId);
            })
            .catch(error => {
                console.error('Error:', error);
                listaClases.innerHTML = '<div class="alert alert-danger">Error al cargar las clases.</div>';
            });
    }

    function renderizarClases(inscripciones, alumnoId) {
        // Detectar si es móvil
        const isMobile = window.innerWidth < 768;
        
        let html = '';
        
        if (isMobile) {
            // Vista de tarjetas para móvil
            inscripciones.forEach(function(inscripcion) {
                const estadoBadge = inscripcion.estado === 'CURSANDO' ? 'primary' : (inscripcion.estado === 'APROBADO' ? 'success' : 'danger');
                const estadoTexto = inscripcion.estado || 'CURSANDO';
                const asignaturaNombre = inscripcion.asignatura_nombre || 'N/A';
                const asignaturaClave = inscripcion.asignatura_clave || 'N/A';
                const docenteNombre = (inscripcion.docente_nombre || '') + ' ' + (inscripcion.docente_apellido || '');
                const seccion = inscripcion.seccion || 'N/A';
                
                html += '<div class="clases-mobile-card">';
                html += '<div class="clase-header">';
                html += `<div class="clase-nombre">${asignaturaNombre} <small class="text-muted">(${asignaturaClave})</small></div>`;
                html += `<div class="clase-estado"><span class="badge bg-${estadoBadge}">${estadoTexto}</span></div>`;
                html += '</div>';
                html += '<div class="clase-info">';
                html += `<div class="info-item"><span class="info-label">Sección:</span><span class="info-value">${seccion}</span></div>`;
                html += `<div class="info-item"><span class="info-label">Docente:</span><span class="info-value">${docenteNombre.trim() || 'N/A'}</span></div>`;
                html += '</div>';
                html += '<div class="clase-acciones">';
                html += `<button class="btn btn-sm btn-danger btn-baja-inscripcion" data-inscripcion-id="${inscripcion.id}" data-alumno-id="${alumnoId}"><i class="bi bi-x-circle me-1"></i>Dar de Baja</button>`;
                html += '</div>';
                html += '</div>';
            });
        } else {
            // Vista de tabla para desktop
            html = '<table class="table table-hover"><thead><tr><th>Asignatura</th><th>Sección</th><th>Docente</th><th>Estado</th><th>Acciones</th></tr></thead><tbody>';
            inscripciones.forEach(function(inscripcion) {
                html += '<tr>';
                html += `<td>${inscripcion.asignatura_nombre || 'N/A'} (${inscripcion.asignatura_clave || 'N/A'})</td>`;
                html += `<td>${inscripcion.seccion || 'N/A'}</td>`;
                html += `<td>${inscripcion.docente_nombre || ''} ${inscripcion.docente_apellido || ''}</td>`;
                html += `<td><span class="badge bg-${inscripcion.estado === 'CURSANDO' ? 'primary' : (inscripcion.estado === 'APROBADO' ? 'success' : 'danger')}">${inscripcion.estado || 'CURSANDO'}</span></td>`;
                html += `<td><button class="btn btn-sm btn-danger btn-baja-inscripcion" data-inscripcion-id="${inscripcion.id}" data-alumno-id="${alumnoId}"><i class="bi bi-x-circle me-1"></i>Dar de Baja</button></td>`;
                html += '</tr>';
            });
            html += '</tbody></table>';
        }
        
        listaClases.innerHTML = html;

        // Event listeners para dar de baja
        document.querySelectorAll('.btn-baja-inscripcion').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const inscripcionId = this.getAttribute('data-inscripcion-id');
                const alumnoIdBtn = this.getAttribute('data-alumno-id');
                darDeBajaInscripcion(inscripcionId, alumnoIdBtn);
            });
        });
    }

    function darDeBajaInscripcion(inscripcionId, alumnoId) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '¿Dar de baja?',
                text: '¿Está seguro de dar de baja esta inscripción?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, dar de baja',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    realizarBaja(inscripcionId, alumnoId);
                }
            });
        } else {
            if (confirm('¿Está seguro de dar de baja esta inscripción?')) {
                realizarBaja(inscripcionId, alumnoId);
            }
        }
    }

    function realizarBaja(inscripcionId, alumnoId) {
        const formData = new FormData();
        formData.append('id', inscripcionId);

        fetch('/GESTACAD/controllers/inscripcionesController.php?action=delete', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'La inscripción ha sido dada de baja correctamente.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    alert('La inscripción ha sido dada de baja correctamente.');
                }
                cargarClasesAlumno(alumnoId);
            } else {
                throw new Error(data.message || 'Error al dar de baja la inscripción');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'No se pudo dar de baja la inscripción.'
                });
            } else {
                alert('Error: ' + error.message);
            }
        });
    }

    // ========== MODAL CAPTURAR CALIFICACIONES (CON NAVEGACIÓN) ==========
    const btnCapturarCalif = document.getElementById('btnCapturarCalificaciones');
    const modalCalificaciones = document.getElementById('modalCapturarCalificaciones');
    const nombreAlumnoCalif = document.getElementById('calificaciones-alumno-nombre');
    const matriculaAlumnoCalif = document.getElementById('calificaciones-alumno-matricula');
    const progresoCalif = document.getElementById('calificaciones-progreso');
    const btnAnterior = document.getElementById('btnAlumnoAnterior');
    const btnSiguiente = document.getElementById('btnAlumnoSiguiente');
    const btnGuardarCalif = document.getElementById('btnGuardarCalificaciones');

    let modalCalificacionesInstance = null;
    let indiceAlumnoActual = 0;
    let inscripcionesActuales = [];
    let parcialActual = 1;
    let estadosOriginales = {}; // Almacenar estados originales para comparar cambios
    let cambiosPendientes = {}; // Almacenar todos los cambios pendientes: { "inscripcionId_parcial": estado }

    function initModalCalificaciones() {
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            try {
                const existingInstance = bootstrap.Modal.getInstance(modalCalificaciones);
                if (existingInstance) {
                    modalCalificacionesInstance = existingInstance;
                } else {
                    modalCalificacionesInstance = new bootstrap.Modal(modalCalificaciones, {
                        backdrop: true,
                        keyboard: true,
                        focus: true
                    });
                }
            } catch (e) {
                console.error('Error al inicializar el modal:', e);
            }
        } else {
            setTimeout(initModalCalificaciones, 100);
        }
    }

    initModalCalificaciones();

    btnCapturarCalif.addEventListener('click', function(e) {
        e.preventDefault();
        if (modalCalificacionesInstance) {
            modalCalificacionesInstance.show();
        } else {
            initModalCalificaciones();
            if (modalCalificacionesInstance) {
                modalCalificacionesInstance.show();
            }
        }
    });

    function cargarCalificacionesAlumno(parcial = parcialActual) {
        if (alumnosList.length === 0) {
            const containerId = `calificaciones-materias-container${parcial === 1 ? '' : '-' + parcial}`;
            const container = document.getElementById(containerId);
            if (container) {
                container.innerHTML = '<div class="alert alert-info">No hay alumnos en el grupo.</div>';
            }
            return;
        }

        const alumno = alumnosList[indiceAlumnoActual];
        nombreAlumnoCalif.textContent = alumno.nombre;
        matriculaAlumnoCalif.textContent = 'Matrícula: ' + alumno.matricula;
        progresoCalif.textContent = `${indiceAlumnoActual + 1} / ${alumnosList.length}`;

        btnAnterior.disabled = indiceAlumnoActual === 0;
        btnSiguiente.disabled = indiceAlumnoActual === alumnosList.length - 1;

        const containerId = `calificaciones-materias-container${parcial === 1 ? '' : '-' + parcial}`;
        const containerMaterias = document.getElementById(containerId);

        if (!containerMaterias) {
            console.error('Contenedor no encontrado:', containerId);
            return;
        }

        containerMaterias.innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></div>';

        fetch(`/GESTACAD/controllers/inscripcionesController.php?action=index&alumno_id=${alumno.id}`)
            .then(response => {
                const contentType = response.headers.get("content-type");
                if (!contentType || !contentType.includes("application/json")) {
                    return response.text().then(text => { throw new Error('Respuesta no válida: ' + text.substring(0, 100)); });
                }
                return response.json();
            })
            .then(inscripciones => {
                if (parcial === 1) {
                    inscripcionesActuales = inscripciones || [];
                }

                const inscripcionesParaMostrar = inscripciones || [];
                
                // Guardar estados originales para comparar cambios y aplicar cambios pendientes
                inscripcionesParaMostrar.forEach(function(inscripcion) {
                    const key = `${inscripcion.id}_p${parcial}`;
                    const estadoCampo = `estado_parcial${parcial}`;
                    
                    // Guardar estado original solo la primera vez
                    if (!estadosOriginales[key]) {
                        estadosOriginales[key] = inscripcion[estadoCampo] || inscripcion.estado || 'CURSANDO';
                    }
                    
                    // Aplicar cambios pendientes si existen (sobrescribir el estado en el objeto)
                    if (cambiosPendientes[key]) {
                        inscripcion[estadoCampo] = cambiosPendientes[key];
                    }
                });

                if (inscripcionesParaMostrar.length === 0) {
                    containerMaterias.innerHTML = '<div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>Este alumno no está inscrito en ninguna clase.</div>';
                    return;
                }

                let html = '<div class="row g-2 g-md-3">';
                inscripcionesParaMostrar.forEach(function(inscripcion) {
                    const asignatura = inscripcion.asignatura_nombre || 'N/A';
                    const clave = inscripcion.asignatura_clave || '';
                    const seccion = inscripcion.seccion || 'N/A';
                    // Obtener el estado del parcial correspondiente
                    const estadoCampo = `estado_parcial${parcial}`;
                    const estadoActual = inscripcion[estadoCampo] || inscripcion.estado || 'CURSANDO';

                    html += '<div class="col-12 col-md-6 col-lg-4">';
                    html += '<div class="card h-100">';
                    html += '<div class="card-body p-2 p-md-3">';
                    html += `<h6 class="card-title mb-2 small fw-bold">${asignatura}${clave ? ' (' + clave + ')' : ''}</h6>`;
                    html += `<p class="card-text small text-muted mb-2 mb-md-3">Sección: ${seccion}</p>`;

                    html += '<div class="btn-group-vertical w-100" role="group">';

                    const estados = [
                        { valor: 'CURSANDO', label: 'Cursando/Asesorías', clase: 'outline-primary' },
                        { valor: 'APROBADO', label: 'Aprobado', clase: 'outline-success' },
                        { valor: 'REPROBADO', label: 'Reprobado', clase: 'outline-danger' }
                    ];

                    // Verificar si hay un cambio pendiente para esta inscripción y parcial
                    const keyCambio = `${inscripcion.id}_p${parcial}`;
                    const estadoAMostrar = cambiosPendientes[keyCambio] || estadoActual;
                    
                    estados.forEach(function(estado) {
                        const checked = estadoAMostrar === estado.valor ? 'checked' : '';
                        html += `<input type="radio" class="btn-check" name="estado_${inscripcion.id}_p${parcial}" id="estado_${inscripcion.id}_p${parcial}_${estado.valor}" value="${estado.valor}" ${checked}>`;
                        html += `<label class="btn btn-${estado.clase}" for="estado_${inscripcion.id}_p${parcial}_${estado.valor}">${estado.label}</label>`;
                    });

                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                });

                html += '</div>';
                containerMaterias.innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                containerMaterias.innerHTML = '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>Error al cargar las materias: ' + error.message + '</div>';
            });
    }

    document.querySelectorAll('[data-bs-toggle="pill"][data-parcial]').forEach(function(tab) {
        tab.addEventListener('shown.bs.tab', function(event) {
            // Capturar cambios antes de cambiar de parcial
            capturarCambiosActuales();
            const parcial = parseInt(event.target.getAttribute('data-parcial'));
            parcialActual = parcial;
            cargarCalificacionesAlumno(parcial);
        });
    });

    // Función para capturar cambios del estudiante actual antes de navegar
    function capturarCambiosActuales() {
        // Recorrer todos los parciales para capturar cambios
        for (let parcial = 1; parcial <= 4; parcial++) {
            const radiosSeleccionados = document.querySelectorAll(`input[name^="estado_"][name$="_p${parcial}"]:checked`);
            
            radiosSeleccionados.forEach(function(radio) {
                const nameMatch = radio.name.match(/^estado_(\d+)_p(\d+)$/);
                if (nameMatch) {
                    const inscripcionId = parseInt(nameMatch[1]);
                    const parcialNum = parseInt(nameMatch[2]);
                    const nuevoEstado = radio.value;
                    const key = `${inscripcionId}_p${parcialNum}`;
                    const estadoOriginal = estadosOriginales[key];
                    
                    // Si el estado cambió, guardarlo en cambios pendientes
                    if (estadoOriginal && estadoOriginal !== nuevoEstado) {
                        cambiosPendientes[key] = nuevoEstado;
                    } else if (estadoOriginal === nuevoEstado && cambiosPendientes[key]) {
                        // Si volvió al estado original, eliminar el cambio pendiente
                        delete cambiosPendientes[key];
                    }
                }
            });
        }
    }

    btnAnterior.addEventListener('click', function() {
        if (indiceAlumnoActual > 0) {
            // Capturar cambios antes de cambiar de estudiante
            capturarCambiosActuales();
            indiceAlumnoActual--;
            cargarCalificacionesAlumno(parcialActual);
        }
    });

    btnSiguiente.addEventListener('click', function() {
        if (indiceAlumnoActual < alumnosList.length - 1) {
            // Capturar cambios antes de cambiar de estudiante
            capturarCambiosActuales();
            indiceAlumnoActual++;
            cargarCalificacionesAlumno(parcialActual);
        }
    });

    modalCalificaciones.addEventListener('shown.bs.modal', function(event) {
        estadosOriginales = {};
        cambiosPendientes = {}; // Limpiar cambios pendientes al abrir el modal
        indiceAlumnoActual = 0;
        parcialActual = 1;
        const firstTab = document.querySelector('#parcial-1-tab');
        if (firstTab && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
            const tab = new bootstrap.Tab(firstTab);
            tab.show();
        }
        cargarCalificacionesAlumno(1);
    });

    btnGuardarCalif.addEventListener('click', async function() {
        // Capturar cambios del estudiante actual antes de guardar
        capturarCambiosActuales();
        
        // Convertir cambios pendientes a formato de updates
        const updates = [];
        for (const key in cambiosPendientes) {
            const match = key.match(/^(\d+)_p(\d+)$/);
            if (match) {
                const inscripcionId = parseInt(match[1]);
                const parcialNum = parseInt(match[2]);
                const nuevoEstado = cambiosPendientes[key];
                
                updates.push({
                    id: inscripcionId,
                    estado: nuevoEstado,
                    parcial: parcialNum
                });
            }
        }

        if (updates.length === 0) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'info',
                    title: 'Sin cambios',
                    text: 'No hay cambios para guardar.'
                });
            } else {
                alert('No hay cambios para guardar.');
            }
            return;
        }

        const confirmar = typeof Swal !== 'undefined' ?
            await Swal.fire({
                icon: 'question',
                title: '¿Guardar todas las calificaciones?',
                text: `Se guardarán ${updates.length} calificación(es) de todos los alumnos y parciales.`,
                showCancelButton: true,
                confirmButtonText: 'Sí, guardar',
                cancelButtonText: 'Cancelar'
            }) :
            confirm(`¿Guardar ${updates.length} calificación(es)?`);

        if (!confirmar || (confirmar && !confirmar.isConfirmed)) {
            return;
        }

        btnGuardarCalif.disabled = true;
        btnGuardarCalif.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        try {
            // Los updates ya incluyen id, estado y parcial, no necesitamos consolidarlos
            const updatesArray = updates;

            const response = await fetch('/GESTACAD/controllers/inscripcionesController.php?action=updateEstados', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ updates: updatesArray })
            });

            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                const text = await response.text();
                console.error('Respuesta no JSON:', text);
                throw new Error('Error del servidor: respuesta no válida');
            }

            const data = await response.json();

            if (data.status === 'ok') {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: `Se guardaron ${updatesArray.length} calificación(es) correctamente.`,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    alert(`Se guardaron ${updatesArray.length} calificación(es) correctamente.`);
                }
                // Limpiar cambios pendientes y estados originales después de guardar
                cambiosPendientes = {};
                estadosOriginales = {};
                // Recargar el estudiante actual para reflejar los cambios guardados
                cargarCalificacionesAlumno(parcialActual);
            } else {
                throw new Error(data.message || 'Error al guardar las calificaciones');
            }
        } catch (error) {
            console.error('Error:', error);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudieron guardar las calificaciones. ' + error.message
                });
            } else {
                alert('Error al guardar las calificaciones: ' + error.message);
            }
        } finally {
            btnGuardarCalif.disabled = false;
            btnGuardarCalif.innerHTML = '<i class="bi bi-save me-1"></i>Guardar Calificaciones';
        }
    });

    // ========== MODAL CALIFICACIONES INDIVIDUALES ==========
    const modalCalificacionesIndividual = document.getElementById('modalCalificacionesIndividual');
    const nombreAlumnoCalifIndividual = document.getElementById('calif-individual-alumno-nombre');
    const matriculaAlumnoCalifIndividual = document.getElementById('calif-individual-alumno-matricula');
    const btnGuardarCalifIndividual = document.getElementById('btnGuardarCalificacionesIndividual');

    let modalCalificacionesIndividualInstance = null;
    let alumnoIdIndividualActual = null;
    let inscripcionesIndividualActuales = [];
    let parcialIndividualActual = 1;

    function initModalCalificacionesIndividual() {
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal && modalCalificacionesIndividual) {
            try {
                const existingInstance = bootstrap.Modal.getInstance(modalCalificacionesIndividual);
                if (existingInstance) {
                    modalCalificacionesIndividualInstance = existingInstance;
                } else {
                    modalCalificacionesIndividualInstance = new bootstrap.Modal(modalCalificacionesIndividual, {
                        backdrop: true,
                        keyboard: true,
                        focus: true
                    });
                }
            } catch (e) {
                console.error('Error al inicializar el modal individual:', e);
            }
        } else {
            setTimeout(initModalCalificacionesIndividual, 100);
        }
    }

    if (modalCalificacionesIndividual && nombreAlumnoCalifIndividual && matriculaAlumnoCalifIndividual) {
        initModalCalificacionesIndividual();

        function cargarCalificacionesIndividual(alumnoId, parcial = 1) {
            if (!alumnoId) return;

            const alumnoIdNum = parseInt(alumnoId);
            const alumno = alumnosList.find(a => {
                const aIdNum = parseInt(a.id);
                return aIdNum === alumnoIdNum || a.id == alumnoId || a.id === alumnoId;
            });

            if (!alumno) {
                const btnCalif = document.querySelector(`[data-alumno-id="${alumnoId}"]`);
                if (btnCalif) {
                    const nombre = btnCalif.getAttribute('data-alumno-nombre');
                    const matricula = btnCalif.getAttribute('data-alumno-matricula');
                    if (nombre && matricula) {
                        nombreAlumnoCalifIndividual.textContent = nombre;
                        matriculaAlumnoCalifIndividual.textContent = 'Matrícula: ' + matricula;
                    } else {
                        return;
                    }
                } else {
                    return;
                }
            } else {
                nombreAlumnoCalifIndividual.textContent = alumno.nombre;
                matriculaAlumnoCalifIndividual.textContent = 'Matrícula: ' + alumno.matricula;
            }

            const containerId = `calif-individual-materias-container-${parcial}`;
            const containerMaterias = document.getElementById(containerId);

            if (!containerMaterias) {
                console.error('Contenedor no encontrado:', containerId);
                return;
            }

            containerMaterias.innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></div>';

            fetch(`/GESTACAD/controllers/inscripcionesController.php?action=index&alumno_id=${alumnoId}`)
                .then(response => {
                    const contentType = response.headers.get("content-type");
                    if (!contentType || !contentType.includes("application/json")) {
                        return response.text().then(text => { throw new Error('Respuesta no válida: ' + text.substring(0, 100)); });
                    }
                    return response.json();
                })
                .then(inscripciones => {
                    inscripcionesIndividualActuales = inscripciones || [];

                    if (inscripcionesIndividualActuales.length === 0) {
                        containerMaterias.innerHTML = '<div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>Este alumno no está inscrito en ninguna clase.</div>';
                        return;
                    }

                    let html = '<div class="row g-2 g-md-3">';
                    inscripcionesIndividualActuales.forEach(function(inscripcion) {
                        const asignatura = inscripcion.asignatura_nombre || 'N/A';
                        const clave = inscripcion.asignatura_clave || '';
                        const seccion = inscripcion.seccion || 'N/A';
                        // Obtener el estado del parcial correspondiente
                        const estadoCampo = `estado_parcial${parcial}`;
                        const estadoActual = inscripcion[estadoCampo] || inscripcion.estado || 'CURSANDO';

                        html += '<div class="col-12 col-md-6 col-lg-4">';
                        html += '<div class="card h-100">';
                        html += '<div class="card-body p-2 p-md-3">';
                        html += `<h6 class="card-title mb-2 small fw-bold">${asignatura}${clave ? ' (' + clave + ')' : ''}</h6>`;
                        html += `<p class="card-text small text-muted mb-2 mb-md-3">Sección: ${seccion}</p>`;

                        html += '<div class="btn-group-vertical w-100" role="group">';

                        const estados = [
                            { valor: 'CURSANDO', label: 'Cursando/Asesorías', clase: 'outline-primary' },
                            { valor: 'APROBADO', label: 'Aprobado', clase: 'outline-success' },
                            { valor: 'REPROBADO', label: 'Reprobado', clase: 'outline-danger' }
                        ];

                        estados.forEach(function(estado) {
                            const checked = estadoActual === estado.valor ? 'checked' : '';
                            html += `<input type="radio" class="btn-check" name="estado_${inscripcion.id}_p${parcial}_ind" id="estado_${inscripcion.id}_p${parcial}_ind_${estado.valor}" value="${estado.valor}" ${checked}>`;
                            html += `<label class="btn btn-${estado.clase}" for="estado_${inscripcion.id}_p${parcial}_ind_${estado.valor}">${estado.label}</label>`;
                        });

                        html += '</div>';
                        html += '</div>';
                        html += '</div>';
                        html += '</div>';
                    });

                    html += '</div>';
                    containerMaterias.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error:', error);
                    containerMaterias.innerHTML = '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>Error al cargar las materias: ' + error.message + '</div>';
                });
        }

        document.querySelectorAll('[id^="parcial-individual-"][data-parcial]').forEach(function(tab) {
            tab.addEventListener('shown.bs.tab', function(event) {
                const parcial = parseInt(event.target.getAttribute('data-parcial'));
                parcialIndividualActual = parcial;
                if (alumnoIdIndividualActual) {
                    cargarCalificacionesIndividual(alumnoIdIndividualActual, parcial);
                }
            });
        });

        document.addEventListener('click', function(e) {
            const btnCalif = e.target.closest('.btn-calificaciones-alumno');
            if (btnCalif) {
                e.preventDefault();
                e.stopPropagation();
                const alumnoId = parseInt(btnCalif.getAttribute('data-alumno-id'));

                if (!alumnoId) {
                    console.error('No se pudo obtener el ID del alumno del botón');
                    return;
                }

                alumnoIdIndividualActual = alumnoId;
                parcialIndividualActual = 1;

                const firstTab = document.querySelector('#parcial-individual-1-tab');
                if (firstTab && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                    const tab = new bootstrap.Tab(firstTab);
                    tab.show();
                }

                if (modalCalificacionesIndividualInstance) {
                    modalCalificacionesIndividualInstance.show();
                    cargarCalificacionesIndividual(alumnoId, 1);
                } else {
                    initModalCalificacionesIndividual();
                    setTimeout(function() {
                        if (modalCalificacionesIndividualInstance) {
                            modalCalificacionesIndividualInstance.show();
                            cargarCalificacionesIndividual(alumnoId, 1);
                        }
                    }, 100);
                }
            }
        });

        if (btnGuardarCalifIndividual) {
            btnGuardarCalifIndividual.addEventListener('click', async function() {
                if (!alumnoIdIndividualActual) return;

                const updates = [];

                inscripcionesIndividualActuales.forEach(function(inscripcion) {
                    const radioSeleccionado = document.querySelector(`input[name="estado_${inscripcion.id}_p${parcialIndividualActual}_ind"]:checked`);
                    const estadoCampo = `estado_parcial${parcialIndividualActual}`;
                    const estadoOriginal = inscripcion[estadoCampo] || inscripcion.estado || 'CURSANDO';
                    if (radioSeleccionado && radioSeleccionado.value !== estadoOriginal) {
                        updates.push({
                            id: inscripcion.id,
                            estado: radioSeleccionado.value,
                            parcial: parcialIndividualActual
                        });
                    }
                });

                if (updates.length === 0) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'info',
                            title: 'Sin cambios',
                            text: 'No hay cambios para guardar.'
                        });
                    } else {
                        alert('No hay cambios para guardar.');
                    }
                    return;
                }

                try {
                    const response = await fetch('/GESTACAD/controllers/inscripcionesController.php?action=updateEstados', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ updates: updates })
                    });

                    const contentType = response.headers.get("content-type");
                    if (!contentType || !contentType.includes("application/json")) {
                        const text = await response.text();
                        console.error('Respuesta no JSON:', text);
                        throw new Error('Error del servidor: respuesta no válida');
                    }

                    const data = await response.json();

                    if (data.status === 'ok') {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: 'Las calificaciones han sido guardadas correctamente.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            alert('Las calificaciones han sido guardadas correctamente.');
                        }
                        cargarCalificacionesIndividual(alumnoIdIndividualActual, parcialIndividualActual);
                    } else {
                        throw new Error(data.message || 'Error al guardar las calificaciones');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudieron guardar las calificaciones. ' + error.message
                        });
                    } else {
                        alert('Error al guardar las calificaciones: ' + error.message);
                    }
                }
            });
        }
    }

    // ========== BUSCADOR DE ALUMNOS ==========
    const buscadorAlumnos = document.getElementById('buscadorAlumnos');
    const btnLimpiarBusqueda = document.getElementById('btnLimpiarBusqueda');
    const btnLimpiarBusquedaMobile = document.getElementById('btnLimpiarBusquedaMobile');
    const tablaAlumnosBody = document.getElementById('tablaAlumnosBody');
    const cardsAlumnosContainer = document.getElementById('cardsAlumnosContainer');

    function filtrarAlumnos(termino) {
        termino = termino.toLowerCase().trim();
        let contador = 0;
        let hayResultados = false;

        // Filtrar tabla (desktop)
        if (tablaAlumnosBody) {
            const filas = tablaAlumnosBody.querySelectorAll('.fila-alumno');
            filas.forEach(function(fila) {
                const matricula = fila.getAttribute('data-matricula') || '';
                const nombre = fila.getAttribute('data-nombre') || '';
                
                if (matricula.includes(termino) || nombre.includes(termino)) {
                    fila.style.display = '';
                    hayResultados = true;
                    // Actualizar el número de fila
                    const celdaNumero = fila.querySelector('td:first-child');
                    if (celdaNumero) {
                        contador++;
                        celdaNumero.textContent = contador;
                    }
                } else {
                    fila.style.display = 'none';
                }
            });

            // Mostrar mensaje si no hay resultados en tabla
            const mensajeTabla = tablaAlumnosBody.querySelector('.sin-resultados');
            if (termino && !hayResultados) {
                if (!mensajeTabla) {
                    const mensaje = document.createElement('tr');
                    mensaje.className = 'sin-resultados';
                    mensaje.innerHTML = `
                        <td colspan="4" class="text-center py-4">
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-info-circle me-2"></i>No se encontraron alumnos que coincidan con "${termino}"
                            </div>
                        </td>
                    `;
                    tablaAlumnosBody.appendChild(mensaje);
                }
            } else if (mensajeTabla) {
                mensajeTabla.remove();
            }
        }

        // Filtrar cards (móvil)
        if (cardsAlumnosContainer) {
            const cards = cardsAlumnosContainer.querySelectorAll('.fila-alumno-card');
            let hayResultadosCards = false;
            contador = 0;

            cards.forEach(function(card) {
                const matricula = card.getAttribute('data-matricula') || '';
                const nombre = card.getAttribute('data-nombre') || '';
                
                if (matricula.includes(termino) || nombre.includes(termino)) {
                    card.style.display = '';
                    hayResultadosCards = true;
                    // Actualizar el número en el badge
                    const badge = card.querySelector('.badge');
                    if (badge) {
                        contador++;
                        badge.textContent = '#' + contador;
                    }
                } else {
                    card.style.display = 'none';
                }
            });

            // Mostrar mensaje si no hay resultados en cards
            const mensajeCards = cardsAlumnosContainer.querySelector('.sin-resultados-card');
            if (termino && !hayResultadosCards) {
                if (!mensajeCards) {
                    const mensaje = document.createElement('div');
                    mensaje.className = 'sin-resultados-card alert alert-info text-center py-4';
                    mensaje.innerHTML = `
                        <i class="bi bi-info-circle me-2"></i>No se encontraron alumnos que coincidan con "${termino}"
                    `;
                    cardsAlumnosContainer.appendChild(mensaje);
                }
            } else if (mensajeCards) {
                mensajeCards.remove();
            }
        }
    }

    if (buscadorAlumnos) {
        buscadorAlumnos.addEventListener('input', function() {
            filtrarAlumnos(this.value);
        });
    }

    // Limpiar búsqueda
    function limpiarBusqueda() {
        if (buscadorAlumnos) {
            buscadorAlumnos.value = '';
            filtrarAlumnos('');
            buscadorAlumnos.focus();
        }
    }

    if (btnLimpiarBusqueda) {
        btnLimpiarBusqueda.addEventListener('click', limpiarBusqueda);
    }

    if (btnLimpiarBusquedaMobile) {
        btnLimpiarBusquedaMobile.addEventListener('click', limpiarBusqueda);
    }

    // ============================================
    // MANEJO DE INFERENCIAS
    // ============================================
    const API_BASE_URL = 'http://localhost:5000';
    
    // Crear instancia única del modal de inferencias
    const modalInferenciasElement = document.getElementById('modalInferenciasAlumno');
    let modalInferenciasInstance = null;
    if (modalInferenciasElement) {
        modalInferenciasInstance = new bootstrap.Modal(modalInferenciasElement, {
            backdrop: true,
            keyboard: true,
            focus: true
        });
    }
    
    // Event listener para botones de inferencias
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-inferencias-alumno');
        if (btn) {
            e.preventDefault();
            // Cerrar dropdown si está abierto
            const dropdown = btn.closest('.dropdown');
            if (dropdown) {
                const dropdownInstance = bootstrap.Dropdown.getInstance(dropdown.querySelector('[data-bs-toggle="dropdown"]'));
                if (dropdownInstance) dropdownInstance.hide();
            }
            const alumnoId = btn.getAttribute('data-alumno-id');
            const alumnoNombre = btn.getAttribute('data-alumno-nombre');
            const alumnoMatricula = btn.getAttribute('data-alumno-matricula');
            
            // Actualizar título del modal
            document.getElementById('modalInferenciasAlumnoLabel').innerHTML = 
                `<i class="bi bi-graph-up-arrow me-2"></i>Análisis de Inferencias - ${alumnoNombre}`;
            
            // Mostrar loading
            const modalBody = document.getElementById('modalInferenciasBody');
            modalBody.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-info" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Cargando inferencias...</span>
                    </div>
                    <p class="mt-3 text-muted">Analizando datos del alumno...</p>
                    <small class="text-muted d-block mt-2">Matrícula: ${alumnoMatricula}</small>
                </div>
            `;
            
            // Mostrar modal usando la instancia única
            if (modalInferenciasInstance) {
                modalInferenciasInstance.show();
            }
            
            // Cargar inferencias
            cargarInferenciasAlumno(alumnoId, alumnoNombre, alumnoMatricula);
        }
    });
    
    function cargarInferenciasAlumno(alumnoId, alumnoNombre, alumnoMatricula) {
        const modalBody = document.getElementById('modalInferenciasBody');
        
        // Obtener periodo activo (opcional, puedes obtenerlo de algún lugar)
        const periodoId = null; // Puedes obtenerlo de un select o de la sesión
        
        let url = `${API_BASE_URL}/api/riesgo/${alumnoId}/detallado`;
        if (periodoId) {
            url += `?periodo_id=${periodoId}`;
        }
        
        console.log('Llamando a la API:', url);
        
        // Crear controller para timeout manual
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 segundos
        
        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
            },
            signal: controller.signal
        })
            .then(response => {
                console.log('Respuesta recibida:', response.status);
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.error || `Error HTTP: ${response.status}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                clearTimeout(timeoutId);
                console.log('Datos recibidos:', data);
                if (data.error) {
                    throw new Error(data.error);
                }
                mostrarInferencias(data, alumnoNombre, alumnoMatricula);
            })
            .catch(error => {
                clearTimeout(timeoutId);
                console.error('Error completo al cargar inferencias:', error);
                let errorMessage = error.message;
                
                // Detectar tipo de error
                if (error.name === 'AbortError' || error.message.includes('timeout') || error.message.includes('aborted')) {
                    errorMessage = 'La API tardó demasiado en responder. Verifica que esté ejecutándose.';
                } else if (error.message.includes('Failed to fetch') || error.message.includes('NetworkError') || error.message.includes('CORS')) {
                    errorMessage = 'No se pudo conectar con la API. Verifica que esté ejecutándose en http://localhost:5000';
                }
                
                modalBody.innerHTML = `
                    <div class="alert alert-danger">
                        <h5 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Error al cargar inferencias</h5>
                        <p class="mb-2"><strong>Error:</strong> ${errorMessage}</p>
                        <hr>
                        <p class="mb-2 small">
                            <strong>Pasos para solucionar:</strong>
                        </p>
                        <ol class="small mb-3">
                            <li>Verifica que la API esté ejecutándose:
                                <br><code>cd api && python app.py</code>
                            </li>
                            <li>Abre en el navegador: <a href="http://localhost:5000" target="_blank">http://localhost:5000</a></li>
                            <li>Verifica la consola del navegador (F12) para más detalles</li>
                        </ol>
                        <div class="mt-3">
                            <button class="btn btn-sm btn-outline-primary" onclick="cargarInferenciasAlumno(${alumnoId}, '${alumnoNombre}', '${alumnoMatricula}')">
                                <i class="bi bi-arrow-clockwise me-1"></i>Reintentar
                            </button>
                            <a href="/GESTACAD/api/test_conexion.php" target="_blank" class="btn btn-sm btn-outline-info ms-2">
                                <i class="bi bi-tools me-1"></i>Verificar API
                            </a>
                        </div>
                    </div>
                `;
            });
    }
    
    function mostrarInferencias(data, alumnoNombre, alumnoMatricula) {
        const modalBody = document.getElementById('modalInferenciasBody');
        const analisis = data.analisis_riesgo || {};
        const estadisticas = data.estadisticas || {};
        
        // Determinar color según nivel de riesgo
        let riesgoColor = 'secondary';
        let riesgoIcon = 'bi-circle';
        if (analisis.nivel_riesgo === 'CRITICO') {
            riesgoColor = 'danger';
            riesgoIcon = 'bi-exclamation-triangle-fill';
        } else if (analisis.nivel_riesgo === 'ALTO') {
            riesgoColor = 'warning';
            riesgoIcon = 'bi-exclamation-circle-fill';
        } else if (analisis.nivel_riesgo === 'MEDIO') {
            riesgoColor = 'info';
            riesgoIcon = 'bi-info-circle-fill';
        } else {
            riesgoColor = 'success';
            riesgoIcon = 'bi-check-circle-fill';
        }
        
        // Calcular porcentaje del score
        const scorePorcentaje = analisis.score_riesgo || 50;
        
        let html = `
            <!-- Información del Alumno -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-1"><i class="bi bi-person-circle me-2"></i>${alumnoNombre}</h5>
                            <p class="text-muted mb-0"><i class="bi bi-person-badge me-1"></i>Matrícula: ${alumnoMatricula}</p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <span class="badge bg-${riesgoColor} fs-6 px-3 py-2">
                                <i class="bi ${riesgoIcon} me-1"></i>${analisis.nivel_riesgo || 'N/A'}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Score de Riesgo -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title mb-3"><i class="bi bi-speedometer2 me-2"></i>Score de Riesgo</h6>
                    <div class="progress mb-2" style="height: 30px;">
                        <div class="progress-bar bg-${riesgoColor} progress-bar-striped progress-bar-animated" 
                             role="progressbar" 
                             style="width: ${scorePorcentaje}%"
                             aria-valuenow="${scorePorcentaje}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            <strong>${scorePorcentaje}%</strong>
                        </div>
                    </div>
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Score calculado: ${scorePorcentaje}/100 (${analisis.nivel_riesgo || 'N/A'})
                    </small>
                </div>
            </div>
            
            <!-- Estadísticas Principales -->
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="card-title text-muted mb-2"><i class="bi bi-calendar-check me-2"></i>Asistencia</h6>
                            <h3 class="mb-0 ${estadisticas.asistencia_promedio < 70 ? 'text-danger' : estadisticas.asistencia_promedio < 85 ? 'text-warning' : 'text-success'}">
                                ${estadisticas.asistencia_promedio || 0}%
                            </h3>
                            <small class="text-muted">
                                ${estadisticas.asistencias || 0} de ${estadisticas.total_clases || 0} clases
                            </small>
                            ${estadisticas.faltas_consecutivas > 0 ? `<br><span class="badge bg-danger mt-1">${estadisticas.faltas_consecutivas} faltas consecutivas</span>` : ''}
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="card-title text-muted mb-2"><i class="bi bi-journal-text me-2"></i>Calificación Promedio</h6>
                            <h3 class="mb-0 ${estadisticas.calificacion_promedio < 6 ? 'text-danger' : estadisticas.calificacion_promedio < 8 ? 'text-warning' : 'text-success'}">
                                ${estadisticas.calificacion_promedio || 0}
                            </h3>
                            <small class="text-muted">
                                ${estadisticas.materias_aprobadas || 0} aprobadas, ${estadisticas.materias_reprobadas || 0} reprobadas
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Explicación -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-lightbulb me-2"></i>Explicación del Análisis</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-${riesgoColor} mb-0">
                        ${(analisis.explicacion || 'No hay explicación disponible').replace(/\n/g, '<br>')}
                    </div>
                </div>
            </div>
        `;
        
        // Reglas Aplicadas
        if (analisis.reglas_aplicadas && analisis.reglas_aplicadas.length > 0) {
            html += `
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="bi bi-list-check me-2"></i>Reglas de Inferencia Aplicadas (${analisis.reglas_aplicadas.length})</h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
            `;
            
            analisis.reglas_aplicadas.forEach((regla, index) => {
                let reglaColor = 'secondary';
                if (regla.nivel === 'CRITICO') reglaColor = 'danger';
                else if (regla.nivel === 'ALTO') reglaColor = 'warning';
                else if (regla.nivel === 'MEDIO') reglaColor = 'info';
                else reglaColor = 'success';
                
                html += `
                    <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    <span class="badge bg-${reglaColor} me-2">${regla.nivel}</span>
                                    Regla ${index + 1}
                                </h6>
                                <p class="mb-1 small">${regla.descripcion}</p>
                                <small class="text-muted">
                                    <i class="bi bi-star me-1"></i>Prioridad: ${regla.prioridad} | 
                                    <i class="bi bi-graph-up me-1"></i>Score: ${regla.score}
                                </small>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += `
                        </div>
                    </div>
                </div>
            `;
        }
        
        // Recomendaciones
        if (analisis.recomendaciones && analisis.recomendaciones.length > 0) {
            html += `
                <div class="card mb-4 border-0 shadow-sm border-start border-${riesgoColor} border-4">
                    <div class="card-header bg-${riesgoColor} text-white">
                        <h6 class="mb-0"><i class="bi bi-clipboard-check me-2"></i>Recomendaciones</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
            `;
            
            analisis.recomendaciones.forEach(rec => {
                html += `<li class="mb-2"><i class="bi bi-arrow-right-circle me-2 text-${riesgoColor}"></i>${rec}</li>`;
            });
            
            html += `
                        </ul>
                    </div>
                </div>
            `;
        }
        
        // Estadísticas Adicionales
        html += `
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Estadísticas Detalladas</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong><i class="bi bi-calendar-x me-2"></i>Seguimientos:</strong><br>
                            <span class="badge bg-info">${estadisticas.seguimientos_abiertos || 0} abiertos</span>
                            <span class="badge bg-success">${estadisticas.seguimientos_cerrados || 0} cerrados</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="bi bi-people me-2"></i>Tutorías:</strong><br>
                            <span class="badge bg-primary">${estadisticas.asistencia_tutorias || 0}% asistencia</span>
                            <span class="badge bg-secondary">${estadisticas.tutorias_disponibles || 0} disponibles</span>
                        </div>
                        ${estadisticas.tendencia_asistencia !== undefined ? `
                        <div class="col-md-6 mb-3">
                            <strong><i class="bi bi-arrow-${estadisticas.tendencia_asistencia >= 0 ? 'up' : 'down'}-circle me-2"></i>Tendencia Asistencia:</strong><br>
                            <span class="badge bg-${estadisticas.tendencia_asistencia >= 0 ? 'success' : 'danger'}">
                                ${estadisticas.tendencia_asistencia >= 0 ? '+' : ''}${estadisticas.tendencia_asistencia || 0}%
                            </span>
                        </div>
                        ` : ''}
                        ${estadisticas.tendencia_calificacion !== undefined ? `
                        <div class="col-md-6 mb-3">
                            <strong><i class="bi bi-arrow-${estadisticas.tendencia_calificacion >= 0 ? 'up' : 'down'}-circle me-2"></i>Tendencia Calificación:</strong><br>
                            <span class="badge bg-${estadisticas.tendencia_calificacion >= 0 ? 'success' : 'danger'}">
                                ${estadisticas.tendencia_calificacion >= 0 ? '+' : ''}${estadisticas.tendencia_calificacion || 0}
                            </span>
                        </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
        
        modalBody.innerHTML = html;
    }
    
    // ============================================
    // MANEJO DEL CIERRE DEL MODAL DE INFERENCIAS
    // ============================================
    if (modalInferenciasElement) {
        // Event listener para cuando el modal comienza a ocultarse
        modalInferenciasElement.addEventListener('hide.bs.modal', function() {
            // Remover el foco del botón de cierre antes de que el modal se oculte
            // Esto previene el error de accesibilidad con aria-hidden
            const closeButton = modalInferenciasElement.querySelector('.btn-close');
            if (closeButton && document.activeElement === closeButton) {
                closeButton.blur();
            }
        });
        
        // Event listener para cuando el modal se oculta completamente
        modalInferenciasElement.addEventListener('hidden.bs.modal', function() {
            // Remover el overlay de Bootstrap si existe (puede quedar residual)
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
            
            // Restaurar el scroll y padding del body
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            
            // Remover clase modal-open que puede quedar bloqueando
            document.body.classList.remove('modal-open');
            
            // Asegurar que el modal esté completamente oculto
            modalInferenciasElement.style.display = 'none';
            modalInferenciasElement.setAttribute('aria-hidden', 'true');
            
            // Limpiar el contenido del modal para evitar problemas en la próxima apertura
            const modalBody = document.getElementById('modalInferenciasBody');
            if (modalBody) {
                modalBody.innerHTML = `
                    <div class="text-center py-5">
                        <div class="spinner-border text-info" role="status">
                            <span class="visually-hidden">Cargando inferencias...</span>
                        </div>
                        <p class="mt-3 text-muted">Analizando datos del alumno...</p>
                    </div>
                `;
            }
        });
    }

    // ============================================
    // CORREGIR POSICIONAMIENTO DE DROPDOWNS EN TABLA Y CARDS
    // ============================================
    // Función para corregir el posicionamiento de dropdowns
    function corregirDropdown(dropdownMenu, toggle, container) {
        if (!dropdownMenu) return;
        
        // Forzar z-index muy alto
        dropdownMenu.style.zIndex = '99999';
        dropdownMenu.style.position = 'absolute';
        
        // Obtener posiciones
        const toggleRect = toggle.getBoundingClientRect();
        const containerRect = container ? container.getBoundingClientRect() : null;
        const menuRect = dropdownMenu.getBoundingClientRect();
        
        // Calcular si hay espacio abajo
        const espacioAbajo = containerRect ? (containerRect.bottom - toggleRect.bottom) : (window.innerHeight - toggleRect.bottom);
        const espacioArriba = containerRect ? (toggleRect.top - containerRect.top) : toggleRect.top;
        const alturaMenu = menuRect.height;
        
        // Si no hay espacio abajo pero sí arriba, mostrar arriba
        if (espacioAbajo < alturaMenu && espacioArriba > alturaMenu) {
            dropdownMenu.style.top = 'auto';
            dropdownMenu.style.bottom = '100%';
            dropdownMenu.style.marginBottom = '0.125rem';
            dropdownMenu.style.marginTop = '0';
        }
        
        // Asegurar que el dropdown esté visible
        requestAnimationFrame(function() {
            dropdownMenu.style.zIndex = '99999';
        });
    }

    // Para dropdowns en la tabla
    document.querySelectorAll('.table-responsive .dropdown-toggle').forEach(function(toggle) {
        toggle.addEventListener('show.bs.dropdown', function() {
            const dropdownMenu = this.nextElementSibling;
            if (dropdownMenu) {
                dropdownMenu.style.zIndex = '99999';
            }
        });
        
        toggle.addEventListener('shown.bs.dropdown', function() {
            const dropdownMenu = this.nextElementSibling;
            const container = document.querySelector('.table-responsive');
            corregirDropdown(dropdownMenu, this, container);
        });
    });

    // Para dropdowns en las cards móviles - Solución más robusta
    document.querySelectorAll('#cardsAlumnosContainer .dropdown-toggle').forEach(function(toggle) {
        let originalParent = null;
        let movedToBody = false;
        
        toggle.addEventListener('show.bs.dropdown', function() {
            const dropdownMenu = this.nextElementSibling;
            if (!dropdownMenu) return;
            
            // Guardar referencia al padre original
            originalParent = dropdownMenu.parentElement;
            
            // Mover el dropdown al body temporalmente para evitar problemas de overflow
            document.body.appendChild(dropdownMenu);
            movedToBody = true;
            
            // Calcular posición absoluta basada en el botón
            const toggleRect = this.getBoundingClientRect();
            dropdownMenu.style.position = 'fixed';
            dropdownMenu.style.top = (toggleRect.bottom + window.scrollY) + 'px';
            dropdownMenu.style.left = toggleRect.left + 'px';
            dropdownMenu.style.width = toggleRect.width + 'px';
            dropdownMenu.style.zIndex = '99999';
            dropdownMenu.style.maxHeight = '200px';
            dropdownMenu.style.overflowY = 'auto';
            
            // Agregar clase a la card padre para aumentar su z-index
            const card = this.closest('.card');
            if (card) {
                card.style.zIndex = '10';
                card.style.position = 'relative';
            }
        });
        
        toggle.addEventListener('shown.bs.dropdown', function() {
            const dropdownMenu = this.nextElementSibling;
            if (!dropdownMenu) return;
            
            // Ajustar posición si es necesario
            const toggleRect = this.getBoundingClientRect();
            const menuRect = dropdownMenu.getBoundingClientRect();
            const containerRect = document.getElementById('cardsAlumnosContainer').getBoundingClientRect();
            
            // Si el menú se sale del contenedor, ajustar
            if (menuRect.bottom > containerRect.bottom) {
                dropdownMenu.style.top = (toggleRect.top + window.scrollY - menuRect.height) + 'px';
            }
            
            dropdownMenu.style.zIndex = '99999';
        });
        
        toggle.addEventListener('hidden.bs.dropdown', function() {
            const dropdownMenu = this.nextElementSibling;
            
            // Devolver el dropdown a su posición original
            if (movedToBody && originalParent && dropdownMenu) {
                originalParent.appendChild(dropdownMenu);
                dropdownMenu.style.position = '';
                dropdownMenu.style.top = '';
                dropdownMenu.style.left = '';
                dropdownMenu.style.width = '';
                dropdownMenu.style.maxHeight = '';
                dropdownMenu.style.overflowY = '';
                movedToBody = false;
            }
            
            // Restaurar z-index de la card
            const card = this.closest('.card');
            if (card) {
                card.style.zIndex = '';
                card.style.position = '';
            }
        });
    });
});
</script>

<style>
/* Estilos para dropdowns dentro de tablas con overflow */
.table-responsive {
    position: relative;
}

.table-responsive .dropdown-menu {
    z-index: 9999 !important;
    position: absolute !important;
}

.table-responsive .btn-group {
    position: static;
}

/* Asegurar que el dropdown se muestre por encima de las filas siguientes */
.table tbody tr td {
    position: relative;
}

.table tbody tr td .dropdown-menu {
    position: absolute !important;
    z-index: 9999 !important;
}

/* Para cards móviles */
#cardsAlumnosContainer {
    position: relative;
}

#cardsAlumnosContainer .dropdown {
    position: static;
}

#cardsAlumnosContainer .dropdown-menu {
    z-index: 99999 !important;
    position: absolute !important;
}

#cardsAlumnosContainer .dropdown.show .dropdown-menu {
    z-index: 99999 !important;
}

/* Asegurar que las cards no interfieran con el dropdown */
#cardsAlumnosContainer .card {
    position: relative;
    z-index: 1;
    transition: z-index 0s;
}

#cardsAlumnosContainer .card:has(.dropdown.show) {
    z-index: 10 !important;
}

/* Para móvil */
@media (max-width: 767.98px) {
    .dropdown-menu {
        z-index: 9999 !important;
        position: absolute !important;
    }
    
    /* Asegurar que el dropdown esté por encima de las cards siguientes */
    #cardsAlumnosContainer .card {
        position: relative;
    }
}
</style>

