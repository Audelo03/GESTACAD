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

<div class="container py-5">
    <div class="row">
        <div class="col-12">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="text-muted fw-normal">
                        <?= htmlspecialchars($alumno['nombre_completo'] ?? $alumno['nombre']) ?>
                    </h4>
                    <p class="mb-0">Matrícula: <?= htmlspecialchars($alumno['matricula']) ?></p>
                </div>
                <a href="crear_seguimiento.php?id_alumno=<?= $id_alumno ?>" class="btn btn-primary"
                    data-bs-toggle="tooltip" data-bs-placement="top" title="Crear Nuevo Seguimiento">
                    <i class="bi bi-plus-circle"></i>
                </a>
            </div>

            <?php if (isset($_GET['success']) && $_GET['success'] === 'created'): ?>
                <div class="alert alert-success">Seguimiento creado exitosamente.</div>
            <?php endif; ?>
            <?php if (isset($_GET['success']) && $_GET['success'] === 'edited'): ?>
                <div class="alert alert-success">Seguimiento editado exitosamente.</div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-body">
                    <?php if (empty($seguimientos)): ?>
                        <div class="text-center p-4">
                            <p class="mb-0">Aún no hay seguimientos para este alumno.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
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
                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-4 text-end">
                <a href="listas.php" class="btn btn-outline-secondary" data-bs-toggle="tooltip" data-bs-placement="top"
                    title="Volver a la Lista de Alumnos">
                    <i class="bi bi-arrow-left"></i>Volver a la Lista
                </a>
            </div>

        </div>
    </div>
</div>

<?php include 'objects/footer.php'; ?>