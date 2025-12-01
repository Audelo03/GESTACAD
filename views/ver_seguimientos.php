<?php
declare(strict_types=1);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../controllers/authController.php";
require_once __DIR__ . "/../controllers/alumnoController.php";
require_once __DIR__ . "/../models/Seguimiento.php";

$auth = new AuthController($conn);
$auth->checkAuth();

$id_alumno = filter_input(INPUT_GET, 'id_alumno', FILTER_VALIDATE_INT);
if (!$id_alumno) {
    header("Location: /GESTACAD/listas?error=invalid_id");
    exit;
}

$alumnoController = new AlumnoController($conn);
$alumno = $alumnoController->obtenerAlumnoPorId($id_alumno);

if (!$alumno) {
    header("Location: /GESTACAD/listas?error=alumno_not_found");
    exit;
}

$page_title = "Historial de Seguimientos";
include 'objects/header.php';

$seguimientoModel = new Seguimiento($conn);
$seguimientos = $seguimientoModel->getByAlumno($id_alumno);


function getEstatus(int $estatus): string
{
    switch ($estatus) {
        case 1:
            return '<span class="badge bg-success">Abierto</span>';
        case 2:
            return '<span class="badge bg-warning text-dark">En Progreso</span>';
        case 3:
            return '<span class="badge bg-secondary">Cerrado</span>';
        default:
            return '<span class="badge bg-light text-dark">Desconocido</span>';
    }
}

function formatFecha(?string $fecha): string
{
    if (empty($fecha)) {
        return '<span class="text-muted">No definida</span>';
    }
    return date("d/m/Y", strtotime($fecha));
}

?>

<div class="container py-3 py-md-5">
    <div class="row">
        <div class="col-12">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 mb-md-4 gap-3">
                <div>
                    <h4 class="h5 h4-md text-muted fw-normal mb-1 mb-md-2">
                        <?= htmlspecialchars($alumno['nombre_completo'] ?? $alumno['nombre']) ?>
                    </h4>
                    <p class="mb-0 small">Matrícula: <?= htmlspecialchars($alumno['matricula']) ?></p>
                </div>
                <a href="crear_seguimiento.php?id_alumno=<?= $id_alumno ?>" class="btn btn-primary w-100 w-md-auto"
                    data-bs-toggle="tooltip" data-bs-placement="top" title="Crear Nuevo Seguimiento">
                    <i class="bi bi-plus-circle me-2"></i>Nuevo Seguimiento
                </a>
            </div>

            <?php if (isset($_GET['success']) && $_GET['success'] === 'created'): ?>
                <div class="alert alert-success">Seguimiento creado exitosamente.</div>
            <?php endif; ?>
            <?php if (isset($_GET['success']) && $_GET['success'] === 'edited'): ?>
                <div class="alert alert-success">Seguimiento editado exitosamente.</div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-body p-2 p-md-3">
                    <?php if (empty($seguimientos)): ?>
                        <div class="text-center p-4">
                            <p class="mb-0">Aún no hay seguimientos para este alumno.</p>
                        </div>
                    <?php else: ?>
                        <!-- Vista de tabla para desktop -->
                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Descripción</th>
                                        <th scope="col">Tipo</th>
                                        <th scope="col">Estatus</th>
                                        <th scope="col">Fecha Creación</th>
                                        <th scope="col">Fecha Compromiso</th>
                                        <th scope="col">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($seguimientos as $seguimiento): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($seguimiento['descripcion']) ?></td>
                                            <td>
                                                <span class="badge bg-info text-dark">
                                                    <?= htmlspecialchars($seguimiento['tipo_seguimiento_nombre'] ?? 'No asignado') ?>
                                                </span>
                                            </td>
                                            <td><?= getEstatus((int) $seguimiento['estatus']) ?></td>
                                            <td><?= formatFecha($seguimiento['fecha_creacion']) ?></td>
                                            <td><?= formatFecha($seguimiento['fecha_compromiso']) ?></td>
                                            <td>
                                                <a href="editar_seguimiento.php?id_seguimiento=<?= $seguimiento['id_seguimiento'] ?>"
                                                    class="btn btn-sm btn-warning" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="Editar Seguimiento">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Vista de cards para móvil -->
                        <div class="d-md-none">
                            <?php foreach ($seguimientos as $seguimiento): ?>
                                <div class="card mb-3 border-start border-primary border-3">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="flex-grow-1">
                                                <h6 class="card-title mb-2 fw-bold small">
                                                    <?= htmlspecialchars($seguimiento['descripcion']) ?>
                                                </h6>
                                                <div class="d-flex flex-wrap gap-2 mb-2">
                                                    <span class="badge bg-info text-dark">
                                                        <?= htmlspecialchars($seguimiento['tipo_seguimiento_nombre'] ?? 'No asignado') ?>
                                                    </span>
                                                    <?= getEstatus((int) $seguimiento['estatus']) ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted d-block">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                <strong>Creación:</strong> <?= formatFecha($seguimiento['fecha_creacion']) ?>
                                            </small>
                                            <small class="text-muted d-block">
                                                <i class="bi bi-calendar-check me-1"></i>
                                                <strong>Compromiso:</strong> <?= formatFecha($seguimiento['fecha_compromiso']) ?>
                                            </small>
                                        </div>
                                        <a href="editar_seguimiento.php?id_seguimiento=<?= $seguimiento['id_seguimiento'] ?>"
                                            class="btn btn-warning btn-sm w-100">
                                            <i class="bi bi-pencil-square me-2"></i>Editar
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-3 mt-md-4 text-center text-md-end">
                <a href="listas.php" class="btn btn-outline-secondary w-100 w-md-auto" data-bs-toggle="tooltip" data-bs-placement="top"
                    title="Volver a la Lista de Alumnos">
                    <i class="bi bi-arrow-left me-2"></i>Volver a la Lista
                </a>
            </div>

        </div>
    </div>
</div>

<?php include 'objects/footer.php'; ?>