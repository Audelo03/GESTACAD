<?php
// Remove session_start() as it's already started in index.php
require_once __DIR__ . '/../controllers/authController.php';
require_once __DIR__ . '/../controllers/alumnoController.php';
require_once __DIR__ . '/../models/TutoriaGrupal.php';
require_once __DIR__ . '/../models/TutoriaIndividual.php';
require_once __DIR__ . '/../config/db.php';

$auth = new AuthController($conn);
$auth->checkAuth();

$id_grupo = isset($_GET['id_grupo']) ? (int)$_GET['id_grupo'] : 0;
if ($id_grupo === 0) {
    header('Location: /GESTACAD/dashboard');
    exit();
}

$fecha = date('Y-m-d'); 
$alumnoController = new AlumnoController($conn);

$nombre_grupo = $alumnoController->getNombreGrupo($id_grupo);

// Contar alumnos del grupo
$totalAlumnos = $alumnoController->contarTotalAlumnosPorGrupo($id_grupo);

// Obtener tutorías grupales e individuales
$tutoriaGrupal = new TutoriaGrupal($conn);
$tutoriaIndividual = new TutoriaIndividual($conn);
$tutoriasGrupales = $tutoriaGrupal->getByGrupo($id_grupo);
$tutoriasIndividuales = $tutoriaIndividual->getByGrupo($id_grupo);

$page_title = "Gestionar Asistencias: " . htmlspecialchars($nombre_grupo);
include 'objects/header.php';
include 'tutorias/tutorias_modals.php';
?>

<div class="container mt-3 mt-md-5">
    <div class="card shadow-sm mb-3 mb-md-4">
        <div class="card-body p-3 p-md-4">
            <h1 class="h4 h3-md mb-1">Gestionar Asistencias del Grupo</h1>
            <h2 class="h6 h5-md text-muted mb-0"><?= htmlspecialchars($nombre_grupo) ?></h2>
        </div>
    </div>
    
    <?php if ($totalAlumnos > 0): ?>
    <div class="card shadow-sm mb-3 mb-md-4">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-3">
                <button type="button" 
                        class="btn btn-primary w-100 w-md-auto btn-lg btn-tutoria-grupal-today" 
                        data-grupo-id="<?= htmlspecialchars($id_grupo) ?>"
                        data-grupo-nombre="<?= htmlspecialchars($nombre_grupo) ?>"
                        data-bs-toggle="tooltip" 
                        data-bs-placement="top" 
                        title="Tomar Asistencia Grupal para Hoy">
                    <i class="bi bi-calendar-plus-fill me-2"></i>
                    <span class="d-none d-sm-inline">Pasar lista / Editar la de hoy</span>
                    <span class="d-sm-none">Pasar lista</span>
                    <small class="d-block d-sm-inline d-md-none mt-1">(<?= date('d/m/Y') ?>)</small>
                    <span class="d-none d-md-inline"> (<?= date('d/m/Y') ?>)</span>
                </button>
                
                <div class="d-grid d-md-flex gap-2 w-100 w-md-auto">
                    <button type="button" 
                            class="btn btn-outline-primary btn-lg btn-tutoria-grupal flex-fill flex-md-none" 
                            data-grupo-id="<?= htmlspecialchars($id_grupo) ?>"
                            data-grupo-nombre="<?= htmlspecialchars($nombre_grupo) ?>"
                            data-bs-toggle="tooltip" 
                            data-bs-placement="top" 
                            title="Tomar Lista Grupal">
                        <i class="bi bi-people-fill me-2"></i>
                        <span class="d-none d-md-inline">Grupal</span>
                        <span class="d-md-none">Tomar Lista Grupal</span>
                    </button>
                    
                    <button type="button" 
                            class="btn btn-outline-info btn-lg btn-tutoria-individual flex-fill flex-md-none" 
                            data-grupo-id="<?= htmlspecialchars($id_grupo) ?>"
                            data-grupo-nombre="<?= htmlspecialchars($nombre_grupo) ?>"
                            data-bs-toggle="tooltip" 
                            data-bs-placement="top" 
                            title="Tomar Lista Individual">
                        <i class="bi bi-person-fill me-2"></i>
                        <span class="d-none d-md-inline">Individual</span>
                        <span class="d-md-none">Tomar Lista Individual</span>
                    </button>
                    
                    <a href="ver-alumnos-grupo?id_grupo=<?= htmlspecialchars($id_grupo) ?>" 
                       class="btn btn-outline-secondary btn-lg flex-fill flex-md-none"
                       data-bs-toggle="tooltip" 
                       data-bs-placement="top" 
                       title="Ver Alumnos del Grupo">
                        <i class="bi bi-gear-fill me-2"></i><span class="d-none d-sm-inline">Ver Alumnos</span><span class="d-sm-none">Alumnos</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="card shadow-sm mb-3 mb-md-4">
        <div class="card-body p-3 p-md-4">
            <div class="alert alert-secondary py-2 mb-0">
                <i class="bi bi-info-circle me-1"></i> No hay alumnos en este grupo. No se pueden gestionar listas.
            </div>
        </div>
    </div>
    <?php endif; ?>


    <!-- Tutorías Grupales -->
    <div class="card shadow-sm mb-3 mb-md-4">
        <div class="card-header">
            <h3 class="h5 h6-md mb-0"><i class="bi bi-people-fill me-2"></i> Tutorías Grupales</h3>
        </div>
        <div class="card-body p-2 p-md-3">
            <?php if (!empty($tutoriasGrupales)): ?>
                <!-- Vista de tabla para desktop -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Asistencia</th>
                                <th>Tutor</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tutoriasGrupales as $tutoria): ?>
                                <tr>
                                    <td><?= htmlspecialchars(date("d/m/Y", strtotime($tutoria['fecha']))) ?></td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?= $tutoria['total_presentes'] ?? 0 ?> / <?= $tutoria['total_alumnos'] ?? 0 ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars(($tutoria['tutor_nombre'] ?? '') . ' ' . ($tutoria['tutor_apellido'] ?? '')) ?></td>
                                    <td>
                                        <div class="d-flex justify-content-start">
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-primary btn-editar-tutoria-grupal"
                                                    data-tutoria-id="<?= $tutoria['id'] ?>"
                                                    data-grupo-id="<?= htmlspecialchars($id_grupo) ?>"
                                                    data-grupo-nombre="<?= htmlspecialchars($nombre_grupo) ?>"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    title="Editar esta tutoría y asistencia">
                                                <i class="bi bi-pencil-square me-1"></i> Editar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Vista de cards para móvil -->
                <div class="d-md-none">
                    <?php foreach ($tutoriasGrupales as $tutoria): ?>
                        <div class="card mb-3 border-start border-primary border-3">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="card-title mb-1 fw-bold">
                                            <i class="bi bi-calendar3 me-2 text-primary"></i>
                                            <?= htmlspecialchars(date("d/m/Y", strtotime($tutoria['fecha']))) ?>
                                        </h6>
                                        <p class="card-text text-muted small mb-2">
                                            <i class="bi bi-person-badge me-1"></i>
                                            <?= htmlspecialchars(($tutoria['tutor_nombre'] ?? '') . ' ' . ($tutoria['tutor_apellido'] ?? '')) ?>
                                        </p>
                                    </div>
                                    <span class="badge bg-info fs-6">
                                        <?= $tutoria['total_presentes'] ?? 0 ?> / <?= $tutoria['total_alumnos'] ?? 0 ?>
                                    </span>
                                </div>
                                <button type="button"
                                        class="btn btn-outline-primary w-100 btn-editar-tutoria-grupal"
                                        data-tutoria-id="<?= $tutoria['id'] ?>"
                                        data-grupo-id="<?= htmlspecialchars($id_grupo) ?>"
                                        data-grupo-nombre="<?= htmlspecialchars($nombre_grupo) ?>">
                                    <i class="bi bi-pencil-square me-2"></i> Editar
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info mb-0">No hay tutorías grupales registradas para este grupo.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tutorías Individuales -->
    <div class="card shadow-sm mb-3 mb-md-4">
        <div class="card-header">
            <h3 class="h5 h6-md mb-0"><i class="bi bi-person-fill me-2"></i> Tutorías Individuales</h3>
        </div>
        <div class="card-body p-2 p-md-3">
            <?php if (!empty($tutoriasIndividuales)): ?>
                <!-- Vista de tabla para desktop -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Alumno</th>
                                <th>Matrícula</th>
                                <th>Motivo</th>
                                <th>Tutor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tutoriasIndividuales as $tutoria): ?>
                                <tr>
                                    <td><?= htmlspecialchars(date("d/m/Y", strtotime($tutoria['fecha']))) ?></td>
                                    <td>
                                        <?= htmlspecialchars(($tutoria['alumno_nombre'] ?? '') . ' ' . ($tutoria['alumno_apellido_paterno'] ?? '') . ' ' . ($tutoria['alumno_apellido_materno'] ?? '')) ?>
                                    </td>
                                    <td><?= htmlspecialchars($tutoria['matricula'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($tutoria['motivo'] ?? '') ?></td>
                                    <td><?= htmlspecialchars(($tutoria['tutor_nombre'] ?? '') . ' ' . ($tutoria['tutor_apellido'] ?? '')) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Vista de cards para móvil -->
                <div class="d-md-none">
                    <?php foreach ($tutoriasIndividuales as $tutoria): ?>
                        <div class="card mb-3 border-start border-info border-3">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-1 fw-bold">
                                            <i class="bi bi-person me-2 text-info"></i>
                                            <?= htmlspecialchars(($tutoria['alumno_nombre'] ?? '') . ' ' . ($tutoria['alumno_apellido_paterno'] ?? '') . ' ' . ($tutoria['alumno_apellido_materno'] ?? '')) ?>
                                        </h6>
                                        <p class="card-text text-muted small mb-1">
                                            <i class="bi bi-person-badge me-1"></i>
                                            <strong>Matrícula:</strong> <?= htmlspecialchars($tutoria['matricula'] ?? '') ?>
                                        </p>
                                        <p class="card-text text-muted small mb-0">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            <?= htmlspecialchars(date("d/m/Y", strtotime($tutoria['fecha']))) ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted d-block mb-1"><strong>Motivo:</strong></small>
                                    <p class="card-text small mb-0"><?= htmlspecialchars($tutoria['motivo'] ?? '') ?></p>
                                </div>
                                <div class="mt-2 pt-2 border-top">
                                    <small class="text-muted">
                                        <i class="bi bi-person-check me-1"></i>
                                        <strong>Tutor:</strong> <?= htmlspecialchars(($tutoria['tutor_nombre'] ?? '') . ' ' . ($tutoria['tutor_apellido'] ?? '')) ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info mb-0">No hay tutorías individuales registradas para este grupo.</div>
            <?php endif; ?>
        </div>
    </div>

</div>

<?php
include 'objects/footer.php';
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Event listener para botón de editar tutoría grupal
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-editar-tutoria-grupal');
        if (btn) {
            e.preventDefault();
            const tutoriaId = btn.getAttribute('data-tutoria-id');
            const grupoId = btn.getAttribute('data-grupo-id');
            const grupoNombre = btn.getAttribute('data-grupo-nombre');
            
            // Cargar datos de la tutoría
            fetch(`/GESTACAD/controllers/tutoriasController.php?action=getGrupalById&id=${tutoriaId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data) {
                        const tutoria = data.data;
                        openModalTutoriaGrupalEdit(tutoria, grupoId, grupoNombre);
                    } else {
                        alert('Error al cargar los datos de la tutoría');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar los datos de la tutoría');
                });
        }
    });
    
    function openModalTutoriaGrupalEdit(tutoria, grupoId, grupoNombre) {
        // Llenar el formulario con los datos de la tutoría
        document.getElementById('grupal-grupo-id').value = grupoId;
        document.getElementById('grupal-grupo-nombre').textContent = grupoNombre;
        document.getElementById('grupal-fecha').value = tutoria.fecha;
        document.getElementById('grupal-fecha').setAttribute('readonly', 'readonly');
        document.getElementById('grupal-actividad-nombre').value = tutoria.actividad_nombre || '';
        document.getElementById('grupal-actividad-descripcion').value = tutoria.actividad_descripcion || '';
        
        // Si hay un campo hidden para el ID de la tutoría, agregarlo
        let tutoriaIdInput = document.getElementById('grupal-tutoria-id');
        if (!tutoriaIdInput) {
            tutoriaIdInput = document.createElement('input');
            tutoriaIdInput.type = 'hidden';
            tutoriaIdInput.id = 'grupal-tutoria-id';
            tutoriaIdInput.name = 'id';
            document.getElementById('formTutoriaGrupal').appendChild(tutoriaIdInput);
        }
        tutoriaIdInput.value = tutoria.id;
        
        // Cargar alumnos y marcar los presentes
        loadAlumnosForGrupalEdit(grupoId, tutoria.asistencia || []);
        
        // Mostrar modal
        const modal = new bootstrap.Modal(document.getElementById('modalTutoriaGrupal'));
        modal.show();
    }
    
    function loadAlumnosForGrupalEdit(grupoId, asistencia) {
        const container = document.getElementById('grupal-lista-alumnos');
        container.innerHTML = '<div class="text-center text-muted"><div class="spinner-border spinner-border-sm me-2" role="status"><span class="visually-hidden">Cargando...</span></div>Cargando alumnos...</div>';

        fetch(`/GESTACAD/controllers/tutoriasController.php?action=getAlumnosByGrupo&grupo_id=${grupoId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    // Crear un mapa de alumnos presentes
                    const presentesMap = {};
                    if (asistencia && asistencia.length > 0) {
                        asistencia.forEach(function(a) {
                            if (a.presente == 1) {
                                presentesMap[a.alumno_id] = true;
                            }
                        });
                    }
                    
                    let html = '<div class="list-group list-group-flush">';
                    data.data.forEach(alumno => {
                        const checked = presentesMap[alumno.id_alumno] ? 'checked' : '';
                        html += `
                            <div class="list-group-item">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="asistencia[${alumno.id_alumno}]" value="1" id="asist_${alumno.id_alumno}" ${checked}>
                                    <label class="form-check-label" for="asist_${alumno.id_alumno}">
                                        ${alumno.nombre} ${alumno.apellido_paterno} ${alumno.apellido_materno}
                                        <small class="text-muted d-block">${alumno.matricula}</small>
                                    </label>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                    container.innerHTML = html;
                } else {
                    container.innerHTML = '<div class="alert alert-warning mb-0">No hay alumnos en este grupo</div>';
                }
            })
            .catch(error => {
                console.error('Error loading students:', error);
                container.innerHTML = '<div class="alert alert-danger mb-0">Error al cargar los alumnos</div>';
            });
    }
});
</script>
