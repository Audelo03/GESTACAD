<?php
// Remove session_start() as it's already started in index.php
require_once __DIR__ . '/../controllers/authController.php';
require_once __DIR__ . '/../config/db.php';

$auth = new AuthController($conn);
$auth->checkAuth();

$page_title = "Dashboard";
include 'objects/header.php';

?>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8" id="estadisticas-container">
            <div class="d-flex justify-content-center p-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>
        </div>

        <div class="col-lg-4" id="lista-alumnos-container">
            <div class="d-flex justify-content-center p-5">
                <div class="spinner-border text-success" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    function loadAndExecuteScript(containerId, scriptId) {
        const scriptTag = document.getElementById(scriptId);
        if (scriptTag) {
            const scriptElement = document.createElement('script');
            scriptElement.innerHTML = scriptTag.innerHTML;
            document.body.appendChild(scriptElement);
            scriptTag.remove(); // Limpiamos la etiqueta original
        }
    }

  
    fetch('/GESTACAD/estadisticas?modo=componente')
        .then(response => response.text())
        .then(html => {
            document.getElementById('estadisticas-container').innerHTML = html;
            loadAndExecuteScript('estadisticas-container', 'estadisticas-script');
        })
        .catch(error => {
            console.error('Error al cargar las estadísticas:', error);
            document.getElementById('estadisticas-container').innerHTML = `<div class="alert alert-danger">No se pudieron cargar las estadísticas.</div>`;
        });

    // Cargar la lista de alumnos (añadimos ?modo=componente)
    fetch('/GESTACAD/listas?modo=componente')
        .then(response => response.text())
        .then(html => {
            document.getElementById('lista-alumnos-container').innerHTML = html;
            loadAndExecuteScript('lista-alumnos-container', 'listas-script');
        })
        .catch(error => {
            console.error('Error al cargar la lista de alumnos:', error);
            document.getElementById('lista-alumnos-container').innerHTML = `<div class="alert alert-danger">No se pudo cargar la lista de alumnos.</div>`;
        });
});
</script>

<?php
include 'objects/footer.php';
?>
