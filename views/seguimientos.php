<?php
declare(strict_types=1);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../controllers/authController.php";
require_once __DIR__ . "/../controllers/seguimientoController.php";

$page_title = "Seguimientos";
include 'objects/header.php';




$auth = new AuthController($conn);
$auth->checkAuth();

$id_usuario_actual = $_SESSION['usuario_id'];
$id_nivel_usuario = $_SESSION['usuario_nivel'];

$seguimientoController = new SeguimientoController($conn);
$seguimientos = $seguimientoController->obtenerSeguimientosPorRol($id_usuario_actual, $id_nivel_usuario);

function getEstatusBadge(int $estatus): string {
    switch ($estatus) {
        case 1: return '<span class="badge bg-success">Abierto</span>';
        case 2: return '<span class="badge bg-warning text-dark">En Progreso</span>';
        case 3: return '<span class="badge bg-secondary">Cerrado</span>';
        default: return '<span class="badge bg-light text-dark">Desconocido</span>';
    }
}

function formatFecha(?string $fecha): string {
    if (empty($fecha)) return '<span class="text-muted">No definida</span>';
    return date("d/m/Y", strtotime($fecha));
}
?>
<style>

#tabla-seguimientos {
    border-radius: 0.5rem;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}

/* Encabezado con estilo */
#tabla-seguimientos thead th {
    background: #f8f9fa;
    font-weight: 600;
    text-align: center;
    vertical-align: middle;
}


#tabla-seguimientos tbody tr:hover {
    background: #f1f3f5;
    cursor: pointer;
    transition: background 0.2s ease-in-out;
}


#tabla-seguimientos td:last-child,
#tabla-seguimientos td .badge {
    text-align: center;
    vertical-align: middle;
}

</style>
<div class="container py-3 py-md-5">
    <div class="card shadow-sm mb-3 mb-md-4">
        <div class="card-header">
            <h1 class="h4 h3-md mb-0"><i class="bi bi-journal-text me-2"></i>Seguimientos</h1>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-2 p-md-3">
            <?php if (empty($seguimientos)): ?>
                <div class="text-center p-4">
                    <p class="mb-0">No hay seguimientos para mostrar.</p>
                </div>
            <?php else: ?>
                <!-- Vista de tabla para desktop -->
                <div class="table-responsive d-none d-md-block">
                    <table id="tabla-seguimientos" class="table table-striped table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Alumno</th>
                                <th>Matrícula</th>
                                <th>Carrera</th>
                                <th>Tipo</th>
                                <th>Estatus</th>
                                <th>Fecha Creación</th>
                                <th>Tutor</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($seguimientos as $s): ?>
                                <tr>
                                    <td><?= htmlspecialchars($s['nombre_alumno']) ?></td>
                                    <td><?= htmlspecialchars($s['matricula']) ?></td>
                                    <td><?= htmlspecialchars($s['nombre_carrera']) ?></td>
                                    <td>
                                        <span class="badge bg-info text-dark">
                                            <?= htmlspecialchars($s['tipo_seguimiento'] ?? 'N/A') ?>
                                        </span>
                                    </td>
                                    <td><?= getEstatusBadge((int)$s['estatus']) ?></td>
                                    <td><?= formatFecha($s['fecha_creacion']) ?></td>
                                    <td><?= htmlspecialchars($s['nombre_tutor']) ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="editar_seguimiento.php?id_seguimiento=<?= $s['id_seguimiento'] ?>" 
                                               class="btn btn-warning" 
                                               data-bs-toggle="tooltip" 
                                               data-bs-placement="top" 
                                               title="Editar este Seguimiento">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <a href="ver_seguimientos.php?id_alumno=<?= $s['id_alumno'] ?>" 
                                               class="btn btn-info" 
                                               data-bs-toggle="tooltip" 
                                               data-bs-placement="top" 
                                               title="Ver Seguimientos del Alumno">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Vista de cards para móvil -->
                <div class="d-md-none" id="cards-seguimientos">
                    <?php foreach ($seguimientos as $s): ?>
                        <div class="card mb-3 border-start border-primary border-3">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-1 fw-bold">
                                            <i class="bi bi-person me-2 text-primary"></i>
                                            <?= htmlspecialchars($s['nombre_alumno']) ?>
                                        </h6>
                                        <p class="card-text text-muted small mb-1">
                                            <i class="bi bi-person-badge me-1"></i>
                                            <strong>Matrícula:</strong> <?= htmlspecialchars($s['matricula']) ?>
                                        </p>
                                        <p class="card-text text-muted small mb-0">
                                            <i class="bi bi-mortarboard me-1"></i>
                                            <?= htmlspecialchars($s['nombre_carrera']) ?>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    <span class="badge bg-info text-dark">
                                        <?= htmlspecialchars($s['tipo_seguimiento'] ?? 'N/A') ?>
                                    </span>
                                    <?= getEstatusBadge((int)$s['estatus']) ?>
                                </div>

                                <div class="mb-2">
                                    <small class="text-muted d-block">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        <strong>Creación:</strong> <?= formatFecha($s['fecha_creacion']) ?>
                                    </small>
                                    <small class="text-muted d-block">
                                        <i class="bi bi-person-check me-1"></i>
                                        <strong>Tutor:</strong> <?= htmlspecialchars($s['nombre_tutor']) ?>
                                    </small>
                                </div>

                                <div class="d-grid gap-2 mt-3">
                                    <a href="editar_seguimiento.php?id_seguimiento=<?= $s['id_seguimiento'] ?>" 
                                       class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil-square me-2"></i>Editar
                                    </a>
                                    <a href="ver_seguimientos.php?id_alumno=<?= $s['id_alumno'] ?>" 
                                       class="btn btn-info btn-sm">
                                        <i class="bi bi-eye me-2"></i>Ver Seguimientos
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'objects/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabla = document.getElementById('tabla-seguimientos');
    if (!tabla) return;

    const dataTable = new simpleDatatables.DataTable(tabla, {
        searchable: true,
        fixedHeight: false,
        labels: {
            placeholder: "Buscar seguimiento...",
            perPage: "{select}",
            noRows: "No hay seguimientos para mostrar",
            info: "Mostrando {start} a {end} de {rows} registros"
        }
    });

    const wrapper = tabla.closest('.dataTable-wrapper');
    if (wrapper) {
        const top = wrapper.querySelector('.dataTable-top');
        if (top) {
            const actions = document.createElement('div');
            actions.className = 'd-flex flex-column flex-md-row align-items-stretch align-items-md-center gap-2';

            const exportBtn = document.createElement('button');
            exportBtn.className = 'btn btn-success btn-sm shadow-sm w-100 w-md-auto';
            exportBtn.innerHTML = '<i class="bi bi-file-earmark-arrow-down me-2"></i>Exportar CSV';
            exportBtn.addEventListener('click', () => {
                dataTable.export({
                    type: 'csv',
                    download: true,
                    filename: 'seguimientos'
                });
            });

            const searchInput = top.querySelector('input[type="search"]');
            if (searchInput) {
                searchInput.classList.add('form-control', 'form-control-sm', 'shadow-sm');
                searchInput.placeholder = 'Buscar seguimiento...';
            }

            actions.appendChild(exportBtn);
            top.appendChild(actions);
        }
    }

    // Buscador para cards móviles
    const cardsContainer = document.getElementById('cards-seguimientos');
    const searchInputMobile = document.createElement('input');
    searchInputMobile.type = 'text';
    searchInputMobile.className = 'form-control form-control-lg mb-3 d-md-none';
    searchInputMobile.placeholder = 'Buscar seguimiento...';
    
    if (cardsContainer && cardsContainer.parentElement) {
        cardsContainer.parentElement.insertBefore(searchInputMobile, cardsContainer);
        
        searchInputMobile.addEventListener('input', function() {
            const termino = this.value.toLowerCase().trim();
            const cards = cardsContainer.querySelectorAll('.card');
            
            cards.forEach(function(card) {
                const texto = card.textContent.toLowerCase();
                if (texto.includes(termino)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
});
</script>
