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

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario_movimiento = $_SESSION['usuario_id'];
    $descripcion = trim($_POST['descripcion'] ?? '');
    $estatus = filter_input(INPUT_POST, 'estatus', FILTER_VALIDATE_INT);
    $fecha_movimiento = $_POST['fecha_movimiento'] ?? date('Y-m-d');
    $fecha_compromiso = $_POST['fecha_compromiso'] ?? null;
    $tipo_seguimiento_id = filter_input(INPUT_POST, 'tipo_seguimiento_id', FILTER_VALIDATE_INT);

    // Validaciones
    if (empty($descripcion)) {
        $errors['descripcion'] = "La descripción es obligatoria.";
    }
    if ($estatus === false || !in_array($estatus, [1, 2, 3])) {
        $errors['estatus'] = "Selecciona un estatus válido.";
    }
    if (empty($fecha_movimiento)) {
        $errors['fecha_movimiento'] = "La fecha de movimiento es obligatoria.";
    }
    if (empty($tipo_seguimiento_id)) {
        $errors['tipo_seguimiento_id'] = "Selecciona un tipo de seguimiento.";
    }

    // Manejar tutor_id según el nivel del usuario
    $tutor_id = null;
    if ($_SESSION['usuario_nivel'] == 3) { 
        $tutor_id = $id_usuario_movimiento;
    }

    if (empty($errors)) {
        $seguimientoController = new SeguimientoController($conn);
        $resultado = $seguimientoController->crear(
            $id_alumno,
            $id_usuario_movimiento,
            $descripcion,
            (int)$estatus,
            $fecha_movimiento,
            $fecha_compromiso,
            (int)$tipo_seguimiento_id,
            $tutor_id
        );

        if ($resultado) {
            header("Location: /GESTACAD/ver_seguimientos.php?id_alumno=$id_alumno&success=created");
            exit;
        } else {
            $errors['general'] = "Hubo un error al guardar el seguimiento.";
        }
    }
}

$page_title = "Nuevo Seguimiento";
include 'objects/header.php';

$seguimientoController = new SeguimientoController($conn);
$tipos_seguimiento = $seguimientoController->obtenerTiposSeguimiento();

// Inicializar variables para el formulario (usar valores del POST si hay errores)
$descripcion = $_POST['descripcion'] ?? "";
$estatus = $_POST['estatus'] ?? 1;
$fecha_movimiento = $_POST['fecha_movimiento'] ?? date('Y-m-d');
$fecha_compromiso = $_POST['fecha_compromiso'] ?? "";
$tipo_seguimiento_id = $_POST['tipo_seguimiento_id'] ?? null;


?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card shadow border-0">
                <div class="card-body p-4">

                

                    <div class="mb-4 p-3 rounded">
                        <h5 class="mb-1"><?= htmlspecialchars($alumno['nombre']) ?></h5>
                        <small class="mb-0">Matrícula: <?= htmlspecialchars($alumno['matricula']) ?></small>
                    </div>

                    <?php if (!empty($errors['general'])): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
                    <?php endif; ?>

                    <form method="POST">

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control <?= isset($errors['descripcion']) ? 'is-invalid' : '' ?>"
                                      id="descripcion" name="descripcion" rows="4"
                                      placeholder="Describe el seguimiento..."><?= htmlspecialchars($descripcion) ?></textarea>
                            <?php if (isset($errors['descripcion'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['descripcion']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="estatus" class="form-label">Estatus</label>
                                <select class="form-select <?= isset($errors['estatus']) ? 'is-invalid' : '' ?>"
                                        id="estatus" name="estatus">
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
                                <select class="form-select <?= isset($errors['tipo_seguimiento_id']) ? 'is-invalid' : '' ?>"
                                        id="tipo_seguimiento_id" name="tipo_seguimiento_id">
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


                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fecha_movimiento" class="form-label" >Fecha de Creación</label>
                                <input type="date" class="form-control" id="fecha_movimiento"
                                       name="fecha_movimiento" disabled value="<?= htmlspecialchars($fecha_movimiento) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="fecha_compromiso" class="form-label">Fecha Compromiso</label>
                                <input type="date" class="form-control" id="fecha_compromiso"
                                       name="fecha_compromiso" value="<?= htmlspecialchars($fecha_compromiso) ?>">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="javascript:window.history.back();" 
                               class="btn btn-outline-secondary me-2">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="btn btn-primary">
                                Guardar
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'objects/footer.php'; ?>
