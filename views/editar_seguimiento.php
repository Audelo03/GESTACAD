<?php
declare(strict_types=1);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../controllers/authController.php";
require_once __DIR__ . "/../controllers/alumnoController.php";
require_once __DIR__ . "/../controllers/seguimientoController.php";

$auth = new AuthController($conn);
$auth->checkAuth();

$id_seguimiento = filter_input(INPUT_GET, 'id_seguimiento', FILTER_VALIDATE_INT);
if (!$id_seguimiento) {
    header("Location: /GORA/listas?error=invalid_id");
    exit;
}

$seguimientoController = new SeguimientoController($conn);
$seguimiento = $seguimientoController->obtenerPorId($id_seguimiento);

if (!$seguimiento) {
    header("Location: /GORA/listas?error=seguimiento_not_found");
    exit;
}

$alumnoController = new AlumnoController($conn);
$alumno = $alumnoController->obtenerAlumnoPorId((int)$seguimiento['alumnos_id_alumno']);
$tipos_seguimiento = $seguimientoController->obtenerTiposSeguimiento();

$errors = [];
$descripcion = $seguimiento['descripcion'];
$estatus = (int)$seguimiento['estatus'];
$fecha_compromiso = $seguimiento['fecha_compromiso'] ?? "";
$tipo_seguimiento_id = (int)$seguimiento['tipo_seguimiento_id'];
$id_alumno = (int)$seguimiento['alumnos_id_alumno'];

// --- PROCESAR FORMULARIO ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $descripcion = trim($_POST['descripcion'] ?? '');
    $estatus = filter_input(INPUT_POST, 'estatus', FILTER_VALIDATE_INT);
    $fecha_compromiso = $_POST['fecha_compromiso'] ?? null;
    $tipo_seguimiento_id = filter_input(INPUT_POST, 'tipo_seguimiento_id', FILTER_VALIDATE_INT);

    if (empty($descripcion)) $errors['descripcion'] = "La descripción es obligatoria.";
    if ($estatus === false) $errors['estatus'] = "Selecciona un estatus válido.";
    if (empty($tipo_seguimiento_id)) $errors['tipo_seguimiento_id'] = "Selecciona un tipo de seguimiento.";

    if (empty($errors)) {
        $resultado = $seguimientoController->actualizar(
            $id_seguimiento,
            $descripcion,
            $estatus,
            $fecha_compromiso,
            $tipo_seguimiento_id
        );

        if ($resultado) {
            header("Location: /GORA/ver_seguimientos.php?id_alumno=$id_alumno&success=edited");
            exit;
        } else {
            $errors['general'] = "Hubo un error al actualizar el seguimiento.";
        }
    }
}

$page_title = "Editar Seguimiento";
include 'objects/header.php';

?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-0">
                <div class="card-body p-4">

                    <h3 class="mb-4">Editar Seguimiento</h3>

                    <div class="mb-4 p-3 rounded">
                        <h5 class="mb-1 "><?= htmlspecialchars($alumno['nombre'] ?? '') ?></h5>
                        <small class="text-muted">Matrícula: <?= htmlspecialchars($alumno['matricula'] ?? '') ?></small>
                    </div>

                    <?php if (!empty($errors['general'])): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control <?= isset($errors['descripcion']) ? 'is-invalid' : '' ?>"
                                      id="descripcion" name="descripcion" rows="4"><?= htmlspecialchars($descripcion) ?></textarea>
                            <?php if (isset($errors['descripcion'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['descripcion']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="estatus" class="form-label">Estatus</label>
                                <select class="form-select <?= isset($errors['estatus']) ? 'is-invalid' : '' ?>" id="estatus" name="estatus">
                                    <option value="1" <?= $estatus == 1 ? 'selected' : '' ?>>Abierto</option>
                                    <option value="2" <?= $estatus == 2 ? 'selected' : '' ?>>En Progreso</option>
                                    <option value="3" <?= $estatus == 3 ? 'selected' : '' ?>>Cerrado</option>
                                </select>
                                <?php if (isset($errors['estatus'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['estatus']) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tipo_seguimiento_id" class="form-label">Tipo de Seguimiento</label>
                                <select class="form-select <?= isset($errors['tipo_seguimiento_id']) ? 'is-invalid' : '' ?>" id="tipo_seguimiento_id" name="tipo_seguimiento_id">
                                    <option value="">Selecciona un tipo...</option>
                                    <?php foreach ($tipos_seguimiento as $tipo): ?>
                                        <option value="<?= $tipo['id_tipo_seguimiento'] ?>" <?= ($tipo_seguimiento_id == $tipo['id_tipo_seguimiento']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($tipo['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['tipo_seguimiento_id'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['tipo_seguimiento_id']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="fecha_compromiso" class="form-label">Fecha Compromiso</label>
                            <input type="date" class="form-control" id="fecha_compromiso" name="fecha_compromiso" value="<?= htmlspecialchars($fecha_compromiso) ?>">
                        </div>
                        

                        <div class="d-flex justify-content-end">
                            <a href="ver_seguimientos.php?id_alumno=<?= $id_alumno ?>" 
                               class="btn btn-outline-secondary me-2">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="btn btn-primary">
                                Actualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'objects/footer.php'; ?>
