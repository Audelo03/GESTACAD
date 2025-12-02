<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/authController.php';
require_once __DIR__ . '/../controllers/reportesController.php';

$auth = new AuthController($conn);
$auth->checkAuth();

$page_title = 'Reportes y Estadísticas';
include 'objects/header.php';

$controller = new ReportesController($conn);

// Obtener datos para los filtros
$periodos = $controller->obtenerPeriodos();
$carreras = $controller->obtenerCarreras();

// Obtener período y parcial activos por defecto
$periodo_activo = null;
$parcial_activo = null;
$grupos = [];

if (!empty($periodos)) {
    $periodo_activo = $periodos[0];
    $parciales = $controller->obtenerParciales($periodo_activo['id']);
    if (!empty($parciales)) {
        $parcial_activo = $parciales[0];
    }
    
    // Obtener grupos según el rol
    $carrera_id = null;
    if (isset($_SESSION["usuario_nivel"]) && $_SESSION["usuario_nivel"] == 2) {
        require_once __DIR__ . '/../models/Usuario.php';
        $usuario = new Usuario($conn);
        $carrera_id = $usuario->getCarrreraIdByUsuarioId($_SESSION["usuario_id"]);
    }
    $grupos = $controller->obtenerGrupos($periodo_activo['id'], $carrera_id);
}

// Obtener reporte inicial si hay datos o si vienen por GET
$reporte = null;
$grupo_seleccionado = null;

// Si vienen parámetros por GET, usarlos
if (isset($_GET['parcial_id']) && isset($_GET['grupo_id'])) {
    $parcial_id_get = (int)$_GET['parcial_id'];
    $grupo_id_get = (int)$_GET['grupo_id'];
    
    // Obtener parciales para encontrar el seleccionado
    $parciales_all = [];
    foreach ($periodos as $p) {
        $parcs = $controller->obtenerParciales($p['id']);
        foreach ($parcs as $parc) {
            if ($parc['id'] == $parcial_id_get) {
                $parcial_activo = $parc;
                $periodo_activo = $p;
                $parciales_all = $parcs;
                break 2;
            }
        }
    }
    
    if ($parcial_activo) {
        // Obtener grupos nuevamente para el select
        $carrera_id = null;
        if (isset($_SESSION["usuario_nivel"]) && $_SESSION["usuario_nivel"] == 2) {
            require_once __DIR__ . '/../models/Usuario.php';
            $usuario = new Usuario($conn);
            $carrera_id = $usuario->getCarrreraIdByUsuarioId($_SESSION["usuario_id"]);
        }
        $grupos = $controller->obtenerGrupos($periodo_activo['id'], $carrera_id);
        
        // Buscar el grupo seleccionado
        foreach ($grupos as $g) {
            if ($g['id_grupo'] == $grupo_id_get) {
                $grupo_seleccionado = $g;
                break;
            }
        }
        
        $reporte = $controller->generarReporte($parcial_id_get, $grupo_id_get);
    }
} elseif ($parcial_activo && !empty($grupos)) {
    $grupo_seleccionado = $grupos[0];
    $reporte = $controller->generarReporte($parcial_activo['id'], $grupo_seleccionado['id_grupo']);
}
?>

<div class="container-fluid py-2 py-md-4 px-2 px-md-4" style="padding-bottom: 100px !important;">
    <div class="row mb-3 mb-md-4">
        <div class="col-12">
            <h2 class="h4 mb-2 mb-md-3 d-md-none">
                <i class="bi bi-file-earmark-bar-graph me-2"></i>
                <span>Reportes</span>
            </h2>
            <h2 class="h3 mb-3 d-none d-md-block">
                <i class="bi bi-file-earmark-bar-graph me-2"></i>
                Reportes y Estadísticas por Parcial y Grupo
            </h2>
            
            <!-- Filtros -->
            <div class="card mb-3 mb-md-4 shadow-sm">
                <div class="card-header bg-primary text-white py-2 py-md-3">
                    <i class="bi bi-funnel me-2"></i>
                    <span class="small d-sm-none">Filtros</span>
                    <span class="d-none d-sm-inline">Filtros</span>
                </div>
                <div class="card-body p-2 p-md-3">
                    <form id="formFiltros" class="row g-2 g-md-3">
                        <div class="col-12 col-sm-6 col-md-3">
                            <label for="periodo_id" class="form-label small mb-1 mb-md-2">Período</label>
                            <select class="form-select form-select-sm" id="periodo_id" name="periodo_id" required>
                                <option value="">Seleccione un período</option>
                                <?php foreach ($periodos as $p): ?>
                                    <option value="<?= $p['id'] ?>" <?= ($periodo_activo && $p['id'] == $periodo_activo['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($p['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <?php if (isset($_SESSION["usuario_nivel"]) && $_SESSION["usuario_nivel"] == 1): ?>
                        <div class="col-12 col-sm-6 col-md-3">
                            <label for="carrera_id" class="form-label small mb-1 mb-md-2">Carrera</label>
                            <select class="form-select form-select-sm" id="carrera_id" name="carrera_id">
                                <option value="">Todas las carreras</option>
                                <?php foreach ($carreras as $c): ?>
                                    <option value="<?= $c['id_carrera'] ?>">
                                        <?= htmlspecialchars($c['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        
                        <div class="col-12 col-sm-6 col-md-3">
                            <label for="parcial_id" class="form-label small mb-1 mb-md-2">Parcial</label>
                            <select class="form-select form-select-sm" id="parcial_id" name="parcial_id" required>
                                <option value="">Seleccione un parcial</option>
                                <?php 
                                $parciales_display = [];
                                if ($periodo_activo) {
                                    $parciales_display = $controller->obtenerParciales($periodo_activo['id']);
                                }
                                foreach ($parciales_display as $p): 
                                ?>
                                    <option value="<?= $p['id'] ?>" <?= ($parcial_activo && $p['id'] == $parcial_activo['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($p['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-12 col-sm-6 col-md-3">
                            <label for="grupo_id" class="form-label small mb-1 mb-md-2">Grupo</label>
                            <select class="form-select form-select-sm" id="grupo_id" name="grupo_id" required>
                                <option value="">Seleccione un grupo</option>
                                <?php foreach ($grupos as $g): ?>
                                    <option value="<?= $g['id_grupo'] ?>" <?= (isset($grupo_seleccionado) && $g['id_grupo'] == $grupo_seleccionado['id_grupo']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($g['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-12 mt-2">
                            <button type="submit" class="btn btn-primary btn-sm w-100 w-md-auto">
                                <i class="bi bi-search me-2"></i>
                                <span class="d-sm-none">Generar</span>
                                <span class="d-none d-sm-inline">Generar Reporte</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Contenedor del reporte -->
    <div id="contenedorReporte">
        <?php if ($reporte && !isset($reporte['error'])): ?>
            <?php 
            // Renderizar el contenido del reporte
            $data = $reporte;
            include 'reportes/contenido_reporte.php'; 
            ?>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Seleccione un período, parcial y grupo para generar el reporte.
                <?php if (isset($reporte['error'])): ?>
                    <br><strong>Error:</strong> <?= htmlspecialchars($reporte['error']) ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const formFiltros = document.getElementById('formFiltros');
    const periodoSelect = document.getElementById('periodo_id');
    const carreraSelect = document.getElementById('carrera_id');
    const parcialSelect = document.getElementById('parcial_id');
    const grupoSelect = document.getElementById('grupo_id');
    const contenedorReporte = document.getElementById('contenedorReporte');
    
    // Cargar parciales cuando cambia el período
    periodoSelect.addEventListener('change', function() {
        const periodoId = this.value;
        parcialSelect.innerHTML = '<option value="">Cargando...</option>';
        grupoSelect.innerHTML = '<option value="">Cargando...</option>';
        
        if (periodoId) {
            fetch(`/GESTACAD/controllers/reportesController.php?action=parciales&periodo_id=${periodoId}`)
                .then(response => response.json())
                .then(parciales => {
                    parcialSelect.innerHTML = '<option value="">Seleccione un parcial</option>';
                    parciales.forEach(p => {
                        parcialSelect.innerHTML += `<option value="${p.id}">${p.nombre}</option>`;
                    });
                })
                .catch(error => {
                    console.error('Error al cargar parciales:', error);
                    parcialSelect.innerHTML = '<option value="">Error al cargar</option>';
                });
            
            // Cargar grupos
            let url = `/GESTACAD/controllers/reportesController.php?action=grupos&periodo_id=${periodoId}`;
            if (carreraSelect && carreraSelect.value) {
                url += `&carrera_id=${carreraSelect.value}`;
            }
            
            fetch(url)
                .then(response => response.json())
                .then(grupos => {
                    grupoSelect.innerHTML = '<option value="">Seleccione un grupo</option>';
                    grupos.forEach(g => {
                        grupoSelect.innerHTML += `<option value="${g.id_grupo}">${g.nombre}</option>`;
                    });
                })
                .catch(error => {
                    console.error('Error al cargar grupos:', error);
                    grupoSelect.innerHTML = '<option value="">Error al cargar</option>';
                });
        }
    });
    
    // Cargar grupos cuando cambia la carrera (solo admin)
    if (carreraSelect) {
        carreraSelect.addEventListener('change', function() {
            const periodoId = periodoSelect.value;
            const carreraId = this.value;
            
            if (periodoId) {
                grupoSelect.innerHTML = '<option value="">Cargando...</option>';
                let url = `/GESTACAD/controllers/reportesController.php?action=grupos&periodo_id=${periodoId}`;
                if (carreraId) {
                    url += `&carrera_id=${carreraId}`;
                }
                
                fetch(url)
                    .then(response => response.json())
                    .then(grupos => {
                        grupoSelect.innerHTML = '<option value="">Seleccione un grupo</option>';
                        grupos.forEach(g => {
                            grupoSelect.innerHTML += `<option value="${g.id_grupo}">${g.nombre}</option>`;
                        });
                    })
                    .catch(error => {
                        console.error('Error al cargar grupos:', error);
                        grupoSelect.innerHTML = '<option value="">Error al cargar</option>';
                    });
            }
        });
    }
    
    // Generar reporte cuando se envía el formulario
    formFiltros.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const parcialId = parcialSelect.value;
        const grupoId = grupoSelect.value;
        
        if (!parcialId || !grupoId) {
            alert('Por favor seleccione un parcial y un grupo');
            return;
        }
        
        contenedorReporte.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div><p class="mt-3">Generando reporte...</p></div>';
        
        // Redirigir a la misma página con parámetros GET para generar el reporte
        const url = new URL(window.location.href);
        url.searchParams.set('parcial_id', parcialId);
        url.searchParams.set('grupo_id', grupoId);
        window.location.href = url.toString();
    });
});
</script>

<style>
/* Estilos mejorados para móvil en la página de reportes */
@media (max-width: 767.98px) {
    .container-fluid {
        padding-left: 0.5rem !important;
        padding-right: 0.5rem !important;
        padding-bottom: 120px !important; /* Espacio para la barra de navegación inferior */
    }
    
    /* Títulos más compactos */
    h2 {
        font-size: 1.25rem !important;
        margin-bottom: 0.75rem !important;
        line-height: 1.3;
    }
    
    /* Filtros más compactos */
    #formFiltros .form-select {
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
    }
    
    #formFiltros .form-label {
        font-size: 0.8rem;
        margin-bottom: 0.25rem;
        font-weight: 500;
    }
    
    /* Botones más compactos */
    .btn {
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
    }
    
    /* Cards más compactos */
    .card {
        margin-bottom: 1rem !important;
        border-radius: 0.5rem;
    }
    
    .card-header {
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem !important;
    }
    
    .card-body {
        padding: 0.75rem !important;
    }
    
    /* Espaciado mejorado */
    .mb-3, .mb-4 {
        margin-bottom: 1rem !important;
    }
    
    /* Alerts más compactos */
    .alert {
        font-size: 0.875rem;
        padding: 0.75rem;
    }
}

/* Mejorar scroll en tablas en móvil */
@media (max-width: 575.98px) {
    .table-responsive {
        -webkit-overflow-scrolling: touch;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
    }
    
    /* Scroll suave y visible */
    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }
    
    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .table-responsive::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }
    
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    
    /* Ajustar contenedor principal */
    .container-fluid {
        padding-left: 0.25rem !important;
        padding-right: 0.25rem !important;
    }
    
    /* Mejorar espaciado entre secciones */
    .reporte-container > .card {
        margin-bottom: 0.75rem !important;
    }
}

/* Mejoras adicionales para pantallas muy pequeñas */
@media (max-width: 374.98px) {
    h2 {
        font-size: 1.1rem !important;
    }
    
    .card-header {
        font-size: 0.8rem !important;
    }
    
    .card-body {
        padding: 0.5rem !important;
    }
    
    #formFiltros .form-select,
    #formFiltros .form-label {
        font-size: 0.8rem;
    }
}
</style>

<?php include 'objects/footer.php'; ?>
