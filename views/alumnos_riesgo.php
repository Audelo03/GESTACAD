<?php
declare(strict_types=1);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../controllers/authController.php";
require_once __DIR__ . "/../controllers/alumnosRiesgoController.php";
require_once __DIR__ . "/../controllers/seguimientoController.php";
require_once __DIR__ . "/../controllers/canalizacionController.php";
require_once __DIR__ . "/../models/RiesgoDesercion.php";
require_once __DIR__ . "/../models/Periodo.php";

$auth = new AuthController($conn);
$auth->checkAuth();

$page_title = "Alumnos en Riesgo";
include 'objects/header.php';

$controller = new AlumnosRiesgoController($conn);
$seguimientoController = new SeguimientoController($conn);
$canalizacionController = new CanalizacionController($conn);

// Obtener periodo activo
$periodoModel = new Periodo($conn);
$periodo_activo = $periodoModel->getActive();
$periodo_id_activo = $periodo_activo ? $periodo_activo['id'] : null;

// Obtener filtros
$filtros = [
    'periodo_id' => isset($_GET['periodo_id']) ? (int)$_GET['periodo_id'] : null,
    'nivel_riesgo' => isset($_GET['nivel_riesgo']) ? $_GET['nivel_riesgo'] : '',
    'carrera_id' => isset($_GET['carrera_id']) ? (int)$_GET['carrera_id'] : null,
    'grupo_id' => isset($_GET['grupo_id']) ? (int)$_GET['grupo_id'] : null,
    'busqueda' => isset($_GET['busqueda']) ? trim($_GET['busqueda']) : ''
];

// Obtener datos para filtros
$periodos = $controller->obtenerPeriodos();
$carreras = $controller->obtenerCarrerasDisponibles();
$grupos = $controller->obtenerGruposDisponibles($filtros['carrera_id']);
$tipos_seguimiento = $seguimientoController->obtenerTiposSeguimiento();

// Obtener alumnos en riesgo
$alumnosRiesgo = $controller->obtenerAlumnosEnRiesgo($filtros);

// Verificar qué alumnos están marcados como riesgo de deserción
$riesgoModel = new RiesgoDesercion($conn);
$alumnos_marcados_riesgo = [];
if ($periodo_id_activo) {
    foreach ($alumnosRiesgo as $alumno) {
        $alumnos_marcados_riesgo[$alumno['id_alumno']] = $riesgoModel->estaMarcado($alumno['id_alumno'], $periodo_id_activo);
    }
}

// Obtener áreas de canalización
$areas_canalizacion = [];
if (method_exists($canalizacionController->canalizacion, 'getAreas')) {
    $areas_canalizacion = $canalizacionController->canalizacion->getAreas();
}

function getRiesgoBadge($nivel): string {
    switch ($nivel) {
        case 'CRITICO': return '<span class="badge bg-danger">Crítico</span>';
        case 'ALTO': return '<span class="badge bg-warning text-dark">Alto</span>';
        case 'MEDIO': return '<span class="badge bg-info">Medio</span>';
        case 'BAJO': return '<span class="badge bg-success">Bajo</span>';
        default: return '<span class="badge bg-secondary">Desconocido</span>';
    }
}

function getRiesgoColor($nivel): string {
    switch ($nivel) {
        case 'CRITICO': return 'danger';
        case 'ALTO': return 'warning';
        case 'MEDIO': return 'info';
        default: return 'secondary';
    }
}
?>

<style>
.card-alumno-riesgo {
    border-left: 4px solid;
    transition: transform 0.2s, box-shadow 0.2s;
}

.card-alumno-riesgo:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}

.card-alumno-riesgo.critico {
    border-left-color: #dc3545;
}

.card-alumno-riesgo.alto {
    border-left-color: #ffc107;
}

.card-alumno-riesgo.medio {
    border-left-color: #0dcaf0;
}

.riesgo-indicador {
    font-size: 0.875rem;
    font-weight: 600;
}

.score-riesgo {
    font-size: 1.5rem;
    font-weight: 700;
}
</style>

<div class="container py-3 py-md-5">
    <!-- Encabezado -->
    <div class="card shadow-sm mb-3 mb-md-4">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div>
                    <h1 class="h4 h3-md mb-1">
                        <i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i>
                        Alumnos en Riesgo
                    </h1>
                    <p class="text-muted mb-0 small">Identificación y seguimiento de alumnos con riesgo de deserción o académico</p>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-danger">Crítico</span>
                    <span class="badge bg-warning text-dark">Alto</span>
                    <span class="badge bg-info">Medio</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow-sm mb-3 mb-md-4">
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="/GESTACAD/alumnos-riesgo" id="formFiltros">
                <div class="row g-3">
                    <div class="col-12 col-md-6 col-lg-3">
                        <label for="periodo_id" class="form-label fw-bold">
                            <i class="bi bi-calendar-range me-1"></i>Período
                        </label>
                        <select name="periodo_id" id="periodo_id" class="form-select">
                            <option value="">Todos los períodos</option>
                            <?php foreach ($periodos as $periodo): ?>
                                <option value="<?= $periodo['id'] ?>" <?= ($filtros['periodo_id'] == $periodo['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($periodo['nombre']) ?>
                                    <?= $periodo['activo'] ? ' (Activo)' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-md-6 col-lg-3">
                        <label for="nivel_riesgo" class="form-label fw-bold">
                            <i class="bi bi-exclamation-triangle me-1"></i>Nivel de Riesgo
                        </label>
                        <select name="nivel_riesgo" id="nivel_riesgo" class="form-select">
                            <option value="">Todos los niveles</option>
                            <option value="CRITICO" <?= ($filtros['nivel_riesgo'] == 'CRITICO') ? 'selected' : '' ?>>Crítico</option>
                            <option value="ALTO" <?= ($filtros['nivel_riesgo'] == 'ALTO') ? 'selected' : '' ?>>Alto</option>
                            <option value="MEDIO" <?= ($filtros['nivel_riesgo'] == 'MEDIO') ? 'selected' : '' ?>>Medio</option>
                        </select>
                    </div>

                    <?php if ($_SESSION['usuario_nivel'] == 1): // Administrador puede filtrar por carrera ?>
                    <div class="col-12 col-md-6 col-lg-3">
                        <label for="carrera_id" class="form-label fw-bold">
                            <i class="bi bi-book me-1"></i>Carrera
                        </label>
                        <select name="carrera_id" id="carrera_id" class="form-select">
                            <option value="">Todas las carreras</option>
                            <?php foreach ($carreras as $carrera): ?>
                                <option value="<?= $carrera['id_carrera'] ?>" <?= ($filtros['carrera_id'] == $carrera['id_carrera']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($carrera['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div class="col-12 col-md-6 col-lg-3">
                        <label for="grupo_id" class="form-label fw-bold">
                            <i class="bi bi-people me-1"></i>Grupo
                        </label>
                        <select name="grupo_id" id="grupo_id" class="form-select">
                            <option value="">Todos los grupos</option>
                            <?php foreach ($grupos as $grupo): ?>
                                <option value="<?= $grupo['id_grupo'] ?>" <?= ($filtros['grupo_id'] == $grupo['id_grupo']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($grupo['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-md-6 col-lg-3">
                        <label for="busqueda" class="form-label fw-bold">
                            <i class="bi bi-search me-1"></i>Buscar
                        </label>
                        <input type="text" name="busqueda" id="busqueda" class="form-control" 
                               placeholder="Matrícula o nombre..." value="<?= htmlspecialchars($filtros['busqueda']) ?>">
                    </div>

                    <div class="col-12 col-md-6 col-lg-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-funnel me-2"></i>Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Resultados -->
    <?php if (empty($alumnosRiesgo)): ?>
        <div class="card shadow-sm">
            <div class="card-body text-center p-5">
                <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                <h5 class="mt-3">No se encontraron alumnos en riesgo</h5>
                <p class="text-muted">No hay alumnos que cumplan los criterios de riesgo seleccionados.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($alumnosRiesgo as $alumno): 
                $nivelRiesgo = $alumno['riesgo']['nivel_riesgo'] ?? 'MEDIO';
                $colorClase = strtolower($nivelRiesgo);
                $score = $alumno['riesgo']['score_riesgo'] ?? 0;
                $estadisticas = $alumno['estadisticas'] ?? [];
            ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card shadow-sm card-alumno-riesgo <?= $colorClase ?>">
                        <div class="card-body p-3">
                            <!-- Encabezado del alumno -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="mb-1 fw-bold">
                                        <?= htmlspecialchars($alumno['nombre'] . ' ' . $alumno['apellido_paterno'] . ' ' . ($alumno['apellido_materno'] ?? '')) ?>
                                    </h6>
                                    <p class="text-muted small mb-0">
                                        <i class="bi bi-person-badge me-1"></i>
                                        <?= htmlspecialchars($alumno['matricula']) ?>
                                    </p>
                                </div>
                                <div class="text-end">
                                    <?= getRiesgoBadge($nivelRiesgo) ?>
                                    <div class="score-riesgo text-<?= getRiesgoColor($nivelRiesgo) ?> mt-1">
                                        <?= number_format($score, 0) ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Información adicional -->
                            <div class="mb-3">
                                <p class="small mb-1">
                                    <i class="bi bi-book me-1"></i>
                                    <strong>Carrera:</strong> <?= htmlspecialchars($alumno['carrera_nombre'] ?? 'N/A') ?>
                                </p>
                                <p class="small mb-1">
                                    <i class="bi bi-people me-1"></i>
                                    <strong>Grupo:</strong> <?= htmlspecialchars($alumno['grupo_nombre'] ?? 'N/A') ?>
                                </p>
                                <?php if (isset($estadisticas['materias_reprobadas'])): ?>
                                    <p class="small mb-1">
                                        <i class="bi bi-x-circle me-1 text-danger"></i>
                                        <strong>Materias reprobadas:</strong> <?= $estadisticas['materias_reprobadas'] ?>
                                    </p>
                                <?php endif; ?>
                                <?php if (isset($estadisticas['calificacion_promedio'])): ?>
                                    <p class="small mb-1">
                                        <i class="bi bi-star me-1"></i>
                                        <strong>Promedio:</strong> <?= number_format($estadisticas['calificacion_promedio'], 2) ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <!-- Botones de acción -->
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-sm btn-primary" 
                                        onclick="crearSeguimiento(<?= $alumno['id_alumno'] ?>)">
                                    <i class="bi bi-journal-plus me-1"></i>
                                    Crear Seguimiento
                                </button>
                                <button type="button" class="btn btn-sm btn-info" 
                                        onclick="crearCanalizacion(<?= $alumno['id_alumno'] ?>)">
                                    <i class="bi bi-arrow-right-circle me-1"></i>
                                    Canalizar
                                </button>
                                <?php if ($periodo_id_activo): 
                                $estaMarcadoRiesgo = isset($alumnos_marcados_riesgo[$alumno['id_alumno']]) && $alumnos_marcados_riesgo[$alumno['id_alumno']];
                                ?>
                                <button type="button" 
                                        class="btn btn-sm <?= $estaMarcadoRiesgo ? 'btn-danger' : 'btn-warning' ?> btn-toggle-riesgo-desercion" 
                                        data-alumno-id="<?= $alumno['id_alumno'] ?>"
                                        data-periodo-id="<?= $periodo_id_activo ?>"
                                        data-marcado="<?= $estaMarcadoRiesgo ? '1' : '0' ?>">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                    <span class="text-toggle-riesgo-desercion">
                                        <?= $estaMarcadoRiesgo ? 'Desmarcar de Riesgo' : 'Marcar como Riesgo' ?>
                                    </span>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal para crear canalización -->
<div class="modal fade" id="modalCanalizacion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="bi bi-arrow-right-circle me-2"></i>
                    Crear Canalización
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCanalizacion" method="POST" action="/GESTACAD/tutorias/canalizacion">
                <input type="hidden" name="alumno_id" id="canalizacion_alumno_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="periodo_canalizacion" class="form-label">Período *</label>
                        <select name="periodo_id" id="periodo_canalizacion" class="form-select" required>
                            <option value="">Seleccione un período</option>
                            <?php foreach ($periodos as $periodo): ?>
                                <?php if ($periodo['activo']): ?>
                                    <option value="<?= $periodo['id'] ?>">
                                        <?= htmlspecialchars($periodo['nombre']) ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="area_canalizacion" class="form-label">Área de Canalización *</label>
                        <select name="area_id" id="area_canalizacion" class="form-select" required>
                            <option value="">Seleccione un área</option>
                            <?php foreach ($areas_canalizacion as $area): ?>
                                <option value="<?= $area['id'] ?>">
                                    <?= htmlspecialchars($area['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="observacion_canalizacion" class="form-label">Observación *</label>
                        <textarea name="observacion" id="observacion_canalizacion" class="form-control" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info">Crear Canalización</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function crearSeguimiento(alumnoId) {
    // Redirigir directamente a la página de crear seguimiento con el ID del alumno
    window.location.href = '/GESTACAD/crear-seguimiento?id_alumno=' + alumnoId;
}

function crearCanalizacion(alumnoId) {
    document.getElementById('canalizacion_alumno_id').value = alumnoId;
    document.getElementById('observacion_canalizacion').value = '';
    document.getElementById('area_canalizacion').value = '';
    
    // Seleccionar período activo por defecto
    const periodoSelect = document.getElementById('periodo_canalizacion');
    const periodoActivo = Array.from(periodoSelect.options).find(opt => opt.value && opt.value !== '');
    if (periodoActivo) {
        periodoSelect.value = periodoActivo.value;
    }
    
    const modal = new bootstrap.Modal(document.getElementById('modalCanalizacion'));
    modal.show();
}

// Manejar el submit del formulario de canalización con AJAX
document.addEventListener('DOMContentLoaded', function() {
    const formCanalizacion = document.getElementById('formCanalizacion');
    
    if (formCanalizacion) {
        formCanalizacion.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Deshabilitar botón y mostrar loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';
            
            // Enviar datos al controlador
            fetch('/GESTACAD/controllers/canalizacionController.php?action=store', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'ok') {
                    // Mostrar mensaje de éxito
                    alert('Canalización creada exitosamente');
                    
                    // Cerrar modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalCanalizacion'));
                    modal.hide();
                    
                    // Recargar la página para mostrar la nueva canalización si es necesario
                    // window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Error al crear la canalización'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar la solicitud');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }
    
    // Auto-submit cuando cambia la carrera (para actualizar grupos)
    const carreraSelect = document.getElementById('carrera_id');
    if (carreraSelect) {
        carreraSelect.addEventListener('change', function() {
            document.getElementById('formFiltros').submit();
        });
    }

    // Manejar toggle de riesgo de deserción
    document.querySelectorAll('.btn-toggle-riesgo-desercion').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const alumnoId = this.getAttribute('data-alumno-id');
            const periodoId = this.getAttribute('data-periodo-id');
            const estaMarcado = this.getAttribute('data-marcado') === '1';
            const btnText = this.querySelector('.text-toggle-riesgo-desercion');
            const icon = this.querySelector('i');
            
            if (!alumnoId || !periodoId) {
                alert('Error: No se pudo obtener la información del alumno');
                return;
            }
            
            // Deshabilitar botón mientras procesa
            this.disabled = true;
            const originalText = btnText.textContent;
            btnText.textContent = 'Procesando...';
            
            // Enviar petición
            const formData = new FormData();
            formData.append('alumno_id', alumnoId);
            formData.append('periodo_id', periodoId);
            formData.append('nivel', 'MEDIO');
            formData.append('motivo', 'Marcado manualmente desde alumnos en riesgo');
            formData.append('fuente', 'Manual');
            
            fetch('/GESTACAD/controllers/riesgoController.php?action=toggle', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'ok') {
                    // Actualizar estado
                    const nuevoEstado = data.marcado;
                    this.setAttribute('data-marcado', nuevoEstado ? '1' : '0');
                    btnText.textContent = nuevoEstado ? 'Desmarcar de Riesgo' : 'Marcar como Riesgo';
                    
                    // Cambiar color del botón
                    this.classList.remove('btn-danger', 'btn-warning');
                    if (nuevoEstado) {
                        this.classList.add('btn-danger');
                        icon.className = 'bi bi-exclamation-triangle-fill me-1';
                    } else {
                        this.classList.add('btn-warning');
                        icon.className = 'bi bi-exclamation-triangle-fill me-1';
                    }
                    
                    // Mostrar mensaje de éxito
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
                    alertDiv.style.zIndex = '9999';
                    alertDiv.innerHTML = `
                        <i class="bi bi-check-circle me-2"></i>${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.body.appendChild(alertDiv);
                    setTimeout(() => alertDiv.remove(), 3000);
                } else {
                    alert('Error: ' + (data.message || 'No se pudo actualizar el riesgo'));
                    btnText.textContent = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar la solicitud');
                btnText.textContent = originalText;
            })
            .finally(() => {
                this.disabled = false;
            });
        });
    });
});
</script>

<?php include 'objects/footer.php'; ?>

