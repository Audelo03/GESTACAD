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

// Obtener tutorías grupales e individuales
$tutoriaGrupal = new TutoriaGrupal($conn);
$tutoriaIndividual = new TutoriaIndividual($conn);
$tutoriasGrupales = $tutoriaGrupal->getByGrupo($id_grupo);
$tutoriasIndividuales = $tutoriaIndividual->getByGrupo($id_grupo);

$page_title = "Gestionar Asistencias: " . htmlspecialchars($nombre_grupo);
include 'objects/header.php';
include 'tutorias/tutorias_modals.php';
?>

<div class="container mt-5">
    <div class="card shadow-sm mb-4"> <div class="card-body">
<h1 class="h3 mb-1">Gestionar Asistencias del Grupo</h1>
 <h2 class="h5 text-muted"><?= htmlspecialchars($nombre_grupo) ?></h2>
 </div>
 </div>
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <button type="button" 
                    class="btn btn-primary btn-lg btn-tutoria-grupal-today" 
                    data-grupo-id="<?= htmlspecialchars($id_grupo) ?>"
                    data-grupo-nombre="<?= htmlspecialchars($nombre_grupo) ?>"
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    title="Tomar Asistencia Grupal para Hoy">
                <i class="bi bi-calendar-plus-fill me-2"></i>
                Pasar lista / Editar la de hoy (<?= date('d/m/Y') ?>)
            </button>
            
            <div class="d-flex gap-2">
                <button type="button" 
                        class="btn btn-outline-primary btn-lg btn-tutoria-grupal" 
                        data-grupo-id="<?= htmlspecialchars($id_grupo) ?>"
                        data-grupo-nombre="<?= htmlspecialchars($nombre_grupo) ?>"
                        data-bs-toggle="tooltip" 
                        data-bs-placement="top" 
                        title="Tutoría Grupal">
                    <i class="bi bi-people-fill me-2"></i>Grupal
                </button>
                
                <button type="button" 
                        class="btn btn-outline-info btn-lg btn-tutoria-individual" 
                        data-grupo-id="<?= htmlspecialchars($id_grupo) ?>"
                        data-grupo-nombre="<?= htmlspecialchars($nombre_grupo) ?>"
                        data-bs-toggle="tooltip" 
                        data-bs-placement="top" 
                        title="Tutoría Individual">
                    <i class="bi bi-person-fill me-2"></i>Individual
                </button>
                
                <a href="ver-alumnos-grupo?id_grupo=<?= htmlspecialchars($id_grupo) ?>" 
                   class="btn btn-outline-secondary btn-lg"
                   data-bs-toggle="tooltip" 
                   data-bs-placement="top" 
                   title="Ver Alumnos del Grupo">
                    <i class="bi bi-gear-fill me-2"></i>Ver Alumnos
                </a>
            </div>
        </div>
    </div>
</div>


    <!-- Tutorías Grupales -->
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h3 class="h5 mb-0"><i class="bi bi-people-fill me-2"></i> Tutorías Grupales</h3>
        </div>
        <div class="card-body">
            <?php if (!empty($tutoriasGrupales)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
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
            <?php else: ?>
                <div class="alert alert-info">No hay tutorías grupales registradas para este grupo.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tutorías Individuales -->
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h3 class="h5 mb-0"><i class="bi bi-person-fill me-2"></i> Tutorías Individuales</h3>
        </div>
        <div class="card-body">
            <?php if (!empty($tutoriasIndividuales)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
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
            <?php else: ?>
                <div class="alert alert-info">No hay tutorías individuales registradas para este grupo.</div>
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
            tutoriaIdInput.name = 'tutoria_id';
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
