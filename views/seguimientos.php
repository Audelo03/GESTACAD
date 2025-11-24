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
<div class="container py-5">


    <div class="card shadow-sm">
        <div class="card-body">
            <?php if (empty($seguimientos)): ?>
                <div class="text-center p-4">
                    <p class="mb-0">No hay seguimientos para mostrar.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table id="tabla-seguimientos" class="table table-striped table-hover">
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
                                        <a href="editar_seguimiento.php?id_seguimiento=<?= $s['id_seguimiento'] ?>" 
                                           class="btn btn-sm btn-warning" 
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top" 
                                           title="Editar este Seguimiento">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="ver_seguimientos.php?id_alumno=<?= $s['id_alumno'] ?>" 
                                           class="btn btn-sm btn-info" 
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top" 
                                           title="Ver Seguimientos del Alumno">
                                            <i class="bi bi-eye"></i>
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
            actions.className = 'd-flex align-items-center gap-2';

            const exportBtn = document.createElement('button');
            exportBtn.className = 'btn btn-success btn-sm shadow-sm';
            exportBtn.innerHTML = '<i class="bi bi-file-earmark-arrow-down"></i> Exportar CSV';
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
});
</script>
