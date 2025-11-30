<?php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php";

$auth = new AuthController($conn);
$auth->checkAuth();
$page_title = "Dashboard de Tutorías";
include __DIR__ . "/../objects/header.php";
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Plan de Acción Tutorial (PAT)</h5>
                    <p class="card-text">Gestionar actividades del PAT por parcial y sesión.</p>
                    <a href="/GESTACAD/tutorias/pat" class="btn btn-primary">Ir a PAT</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Canalización</h5>
                    <p class="card-text">Registrar y ver alumnos canalizados a otras áreas.</p>
                    <a href="/GESTACAD/tutorias/canalizacion" class="btn btn-primary">Ir a Canalización</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Riesgo de Deserción</h5>
                    <p class="card-text">Identificar y monitorear alumnos en riesgo.</p>
                    <a href="#" class="btn btn-secondary disabled">Próximamente</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . "/../objects/footer.php"; ?>