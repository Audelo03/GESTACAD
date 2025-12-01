<?php
// Remove session_start() as it's already started in index.php
require_once __DIR__ . '/../controllers/authController.php';

$is_component_mode = isset($_GET['modo']) && $_GET['modo'] === 'componente';

if (!$is_component_mode) {
    $auth = new AuthController($conn);
    $auth->checkAuth();
    $page_title = "Listado de Alumnos";
    include 'objects/header.php';


}

$niveles_autorizados = [1 => "Admin", 2 => "Coordinador", 3 => "Tutor", 4 => "Director"];
$nivel_nombre  = $niveles_autorizados[$_SESSION['usuario_nivel']] ?? "Desconocido"; 
$nombre = $_SESSION['usuario_nombre'] . ' ' . $_SESSION['usuario_apellido_paterno'] . ' ' . $_SESSION['usuario_apellido_materno'];
?>

<div class="container mt-3 mt-md-4">
    <!-- Tarjeta de usuario -->
    <div class="card shadow-lg border-0 rounded-3 mb-3 mb-md-4">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-column flex-md-row flex-wrap justify-content-between align-items-start align-items-md-center gap-2">
                <div>
                    <p class="mb-1 mb-md-2 text-muted small small-md">
                        <i class="bi bi-person-vcard me-1 text-secondary"></i>
                        <strong class="text-dark"><?= htmlspecialchars($nivel_nombre) ?></strong>
                    </p>
                    <p class="mb-0 text-muted small small-md">
                        <i class="bi bi-person-circle me-1 text-secondary"></i>
                        <strong class="text-dark"><?= htmlspecialchars($nombre) ?></strong>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Buscador -->
    <div class="card shadow-sm border-0 rounded-3 mb-3 mb-md-4">
        <div class="card-body p-3 p-md-4">
            <div class="input-group input-group-lg">
                <span class="input-group-text bg-primary text-white border-0">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="buscador" 
                       class="form-control border-0 shadow-none" 
                       placeholder="Buscar alumno por matrícula o nombre">
            </div>
        </div>
    </div>

    <!-- Contenedor alumnos -->
    <div id="contenedor-alumnos" class="row g-3"></div>

    <!-- Paginación -->
    <nav class="mt-3 mt-md-4" aria-label="Paginación de alumnos">
        <ul class="pagination pagination-sm pagination-md-lg justify-content-center flex-wrap" id="paginacion-controles"></ul>
    </nav>
</div>


<script id="listas-script">
(function () {
    if (window.listasScriptLoaded) return;
    window.listasScriptLoaded = true;

    const buscadorInput = document.getElementById('buscador');
    const contenedorAlumnos = document.getElementById('contenedor-alumnos');
    const paginacionControles = document.getElementById('paginacion-controles');
    let timeout = null;

    async function cargarAlumnos(page = 1, termino = '', modo_lista = false) {

        
        contenedorAlumnos.classList.add('loading');
        contenedorAlumnos.innerHTML = `<div class="d-flex justify-content-center p-5"><div class="spinner-border" role="status"><span class="visually-hidden">Cargando...</span></div></div>`;
        try {
            console.log(modo_lista);
            const response = await fetch(`/GESTACAD/alumnos-paginados?page=${page}&termino=${encodeURIComponent(termino)}&modo=${modo_lista}`);
            if (!response.ok) throw new Error('Error en la respuesta del servidor.');
            const data = await response.json();
            contenedorAlumnos.innerHTML = data.html;
             initTooltips(); 
            actualizarPaginacion(data.currentPage, data.totalPages, termino);
        } catch (error) {
            console.error('Error al cargar alumnos:', error);
            contenedorAlumnos.innerHTML = `<div class="alert alert-danger">No se pudieron cargar los datos. Intente de nuevo más tarde.</div>`;
        } finally {
            contenedorAlumnos.classList.remove('loading');
        }
    }

    function actualizarPaginacion(currentPage, totalPages, termino) {
        paginacionControles.innerHTML = '';
        if (totalPages <= 1) return;
        paginacionControles.innerHTML += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${currentPage - 1}" data-termino="${termino}">Anterior</a></li>`;
        for (let i = 1; i <= totalPages; i++) {
            paginacionControles.innerHTML += `<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}" data-termino="${termino}">${i}</a></li>`;
        }
        paginacionControles.innerHTML += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${currentPage + 1}" data-termino="${termino}">Siguiente</a></li>`;
    }

    function initTooltips() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    paginacionControles.addEventListener('click', function(e) {
        e.preventDefault();
        const target = e.target.closest('a');
        if (target && !target.parentElement.classList.contains('disabled')) {
            const page = parseInt(target.getAttribute('data-page'));
            const termino = target.getAttribute('data-termino');
            cargarAlumnos(page, termino);
        }
    });

    buscadorInput.addEventListener('keyup', function (e) {
        clearTimeout(timeout);
        const termino = e.target.value.trim();
        timeout = setTimeout(() => {
            if (termino != "")
                cargarAlumnos(1, termino, true);
            else
                cargarAlumnos(1, termino, false);
            


            
        }, 500);
    });
    cargarAlumnos(1); 
})();
</script>

<style>
/* Mejoras móviles para listas.php */
@media (max-width: 767.98px) {
    .pagination-md-lg {
        font-size: 0.875rem;
    }
    
    .pagination-md-lg .page-link {
        padding: 0.375rem 0.5rem;
    }
    
    .small-md {
        font-size: 0.875rem;
    }
    
    .accordion-button {
        font-size: 0.875rem;
        padding: 0.75rem 1rem;
    }
    
    .list-group-item {
        font-size: 0.875rem;
    }
}

@media (min-width: 768px) {
    .small-md {
        font-size: 1rem;
    }
}
</style>

<?php 
// Si no estamos en modo componente incluimos el footer
if (!$is_component_mode) {
    include 'objects/footer.php';
}
?>
