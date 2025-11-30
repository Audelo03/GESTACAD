<?php
require_once __DIR__ . "/../controllers/usuarioController.php";
require_once __DIR__ . "/../controllers/authController.php";

$page_title = "Perfil";

$userId = $_SESSION['usuario_id'] ?? null;
if (!$userId) {
    header("Location: /GESTACAD/login");
    exit;
}

$userController = new UsuarioController($conn);
$usuario = $userController->obtenerUsuarioPorId($userId);

$niveles_autorizados = [1 => "Admin", 2 => "Coordinador", 3 => "Tutor", 4 => "Director"];
$nivel_nombre = $niveles_autorizados[$_SESSION['usuario_nivel']] ?? "Desconocido";

if (!$usuario) {
    header("Location: /GESTACAD/login?error=usernotfound");
    exit;
}

$nombreCompleto = trim(
    htmlspecialchars($usuario['nombre']) . " " .
    htmlspecialchars($usuario["apellido_paterno"]) . " " .
    htmlspecialchars($usuario["apellido_materno"])
);
$emailUsuario = htmlspecialchars($usuario['email']);
$idUsuario = htmlspecialchars($usuario['id_usuario']);


include 'objects/header.php';


?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">


            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div
                            class="col-md-4 bg-light d-flex flex-column align-items-center justify-content-center p-4 text-center">
                            <i class="bi bi-person-circle text-primary" style="font-size: 8rem;"></i>

                        </div>

                        <div class="col-md-8 p-4 p-lg-5">
                            <h4 class="card-title mb-4 fw-light text-primary border-bottom pb-2">
                                Información de la Cuenta
                            </h4>

                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-person-vcard fs-4 text-muted me-3"></i>

                                <div>
                                    <strong class="d-block">Nombre completo</strong>
                                    <span><?php echo $nombreCompleto; ?></span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4">
                                <i class="bi bi-envelope-fill fs-4 text-muted me-3"></i>
                                <div>
                                    <strong class="d-block">Correo electrónico</strong>
                                    <span><?php echo $emailUsuario; ?></span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4">
                                <i class="bi bi-person-vcard fs-4 text-muted me-3"></i>
                                <div>
                                    <strong class="d-block">Nivel de usuario</strong>
                                    <span><?php echo $nivel_nombre; ?></span>
                                </div>
                            </div>

                            <hr>


                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
</div>

<?php include 'objects/footer.php'; ?>