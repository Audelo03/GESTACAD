<?php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php";

$auth = new AuthController($conn);
$auth->checkAuth();

$nivel = isset($_SESSION['usuario_nivel']) ? (int)$_SESSION['usuario_nivel'] : null;
$es_tutor = ($nivel == 3);

$page_title = $es_tutor ? "Becas de Mis Alumnos" : "Becas";
include __DIR__ . "/../objects/header.php";
?>

<div class="container mt-3 mt-md-4">
    <!-- Header -->
    <div class="card shadow-sm mb-3 mb-md-4 crud-header-card">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-3">
                <div>
                    <h1 class="h4 h3-md mb-1 mb-md-2">
                        <i class="bi bi-award me-2 text-primary"></i>
                        <?= $es_tutor ? "Becas de Mis Alumnos" : "Gestión de Becas" ?>
                    </h1>
                    <p class="text-muted small mb-0 d-none d-md-block">
                        <?= $es_tutor ? "Becas asignadas a los alumnos de tus grupos" : "Administra el catálogo de becas" ?>
                    </p>
                </div>
                <?php if (!$es_tutor): ?>
                <div class="d-flex flex-column flex-md-row gap-2 w-100 w-md-auto">
                    <button class="btn btn-primary btn-lg w-100 w-md-auto" id="btnNuevaBeca">
                        <i class="bi bi-plus-circle me-2"></i>
                        <span class="d-none d-sm-inline">Agregar Beca</span>
                        <span class="d-sm-none">Nuevo</span>
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($es_tutor): ?>
    <div class="alert alert-info mb-3 mb-md-4">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Becas de tus alumnos:</strong> Aquí puedes ver las becas asignadas a los alumnos de tus grupos.
    </div>
    <?php endif; ?>

    <!-- Tabla Desktop -->
    <div class="table-responsive d-none d-md-block">
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover mb-0 crud-table" id="tablaBecas">
                    <thead>
                        <?php if ($es_tutor): ?>
                        <tr>
                            <th>Alumno</th>
                            <th>Matrícula</th>
                            <th>Grupo</th>
                            <th>Beca</th>
                            <th>Periodo</th>
                            <th class="text-center">Porcentaje</th>
                            <th class="text-center">Monto</th>
                            <th>Fecha Asignación</th>
                        </tr>
                        <?php else: ?>
                        <tr>
                            <th class="text-center" style="width: 80px;">ID</th>
                            <th>Clave</th>
                            <th>Nombre</th>
                            <th class="text-center" style="width: 150px;">Acciones</th>
                        </tr>
                        <?php endif; ?>
                    </thead>
                    <tbody id="becasBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Cards Móvil -->
    <div class="d-md-none" id="becasCardsContainer">
        <div id="becasCardsBody"></div>
    </div>
</div>

<?php if (!$es_tutor): ?>
<div class="modal fade" id="becaModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="modalLabel">
                    <i class="bi bi-award me-2"></i>Formulario de Beca
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 p-md-4">
                <form id="formBeca">
                    <input type="hidden" id="id" name="id">
                    <div class="mb-3">
                        <label for="clave" class="form-label fw-semibold">
                            <i class="bi bi-key me-1 text-primary"></i>Clave
                        </label>
                        <input type="text" id="clave" name="clave" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="nombre" class="form-label fw-semibold">
                            <i class="bi bi-tag me-1 text-primary"></i>Nombre
                        </label>
                        <input type="text" id="nombre" name="nombre" class="form-control" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top p-3 p-md-4">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="btnGuardar">
                    <i class="bi bi-check-circle me-2"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/crud_helper_styles.php'; ?>

<?php include __DIR__ . "/../objects/footer.php"; ?>

<script>
    window.addEventListener('load', function () {
        const modalEl = document.getElementById('becaModal');
        const modal = modalEl ? new bootstrap.Modal(modalEl) : null;
        const form = document.getElementById('formBeca');
        const tbody = document.getElementById('becasBody');
        const btnGuardar = document.getElementById('btnGuardar');
        const modalLabel = document.getElementById('modalLabel');

        const esTutor = <?= $es_tutor ? 'true' : 'false' ?>;
        
        const becasCardsBody = document.getElementById('becasCardsBody');
        
        const renderTable = (data) => {
            tbody.innerHTML = '';
            if (becasCardsBody) becasCardsBody.innerHTML = '';
            
            if (!data || data.length === 0) {
                const colspan = esTutor ? 8 : 4;
                tbody.innerHTML = `<tr><td colspan="${colspan}" class="text-center py-4 text-muted"><i class="bi bi-inbox me-2"></i>No hay registros</td></tr>`;
                if (becasCardsBody) {
                    becasCardsBody.innerHTML = '<div class="alert alert-info mb-0"><i class="bi bi-inbox me-2"></i>No hay registros</div>';
                }
                return;
            }
            
            if (esTutor) {
                // Vista para tutor: mostrar becas de alumnos
                data.forEach(item => {
                    const nombreCompleto = `${item.alumno_nombre || ''} ${item.alumno_apellido_paterno || ''} ${item.alumno_apellido_materno || ''}`.trim();
                    const porcentaje = item.porcentaje ? parseFloat(item.porcentaje).toFixed(2) + '%' : 'N/A';
                    const monto = item.monto ? '$' + parseFloat(item.monto).toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : 'N/A';
                    const fecha = item.fecha_asignacion ? new Date(item.fecha_asignacion).toLocaleDateString('es-MX') : 'N/A';
                    
                    // Tabla desktop
                    tbody.innerHTML += `
                    <tr class="align-middle">
                        <td>${nombreCompleto || 'N/A'}</td>
                        <td><span class="badge bg-secondary">${item.matricula || 'N/A'}</span></td>
                        <td>${item.grupo_nombre || 'N/A'}</td>
                        <td><span class="badge bg-primary">${item.beca_clave || ''}</span> ${item.beca_nombre || 'N/A'}</td>
                        <td>${item.periodo_nombre || 'N/A'}</td>
                        <td class="text-center"><span class="badge bg-info">${porcentaje}</span></td>
                        <td class="text-center fw-semibold text-success">${monto}</td>
                        <td>${fecha}</td>
                    </tr>`;
                    
                    // Cards móvil
                    if (becasCardsBody) {
                        becasCardsBody.innerHTML += `
                        <div class="card shadow-sm mb-3 crud-card-mobile">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-3 gap-2">
                                    <div class="flex-grow-1 min-w-0">
                                        <h6 class="mb-1 fw-bold text-truncate">${nombreCompleto || 'N/A'}</h6>
                                        <small class="text-muted d-block"><i class="bi bi-person-badge me-1"></i>${item.matricula || 'N/A'}</small>
                                        <small class="text-muted d-block"><i class="bi bi-people me-1"></i>${item.grupo_nombre || 'N/A'}</small>
                                    </div>
                                    <div class="text-end flex-shrink-0 ms-2">
                                        <span class="badge bg-primary mb-1">${item.beca_clave || ''}</span>
                                        <div class="badge bg-info">${porcentaje}</div>
                                    </div>
                                </div>
                                <div class="border-top pt-2">
                                    <div class="row g-2 small">
                                        <div class="col-6"><strong>Periodo:</strong> ${item.periodo_nombre || 'N/A'}</div>
                                        <div class="col-6 text-end"><strong>Monto:</strong> <span class="text-success fw-bold">${monto}</span></div>
                                        <div class="col-12"><strong>Fecha:</strong> ${fecha}</div>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    }
                });
            } else {
                // Vista para administrador/coordinador: mostrar catálogo de becas
                data.forEach(item => {
                    // Tabla desktop
                    tbody.innerHTML += `
                    <tr class="align-middle">
                        <td class="text-center fw-bold text-primary">${item.id}</td>
                        <td><span class="badge bg-primary">${item.clave}</span></td>
                        <td>${item.nombre}</td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-warning btn-editar" 
                                    data-id="${item.id}" 
                                    data-clave="${item.clave}"
                                    data-nombre="${item.nombre}"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Editar">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btn-eliminar" 
                                    data-id="${item.id}"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Eliminar">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </div>
                        </td>
                    </tr>`;
                    
                    // Cards móvil
                    if (becasCardsBody) {
                        becasCardsBody.innerHTML += `
                        <div class="card shadow-sm mb-3 crud-card-mobile">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-3 gap-2">
                                    <div class="flex-grow-1 min-w-0">
                                        <h6 class="mb-1 fw-bold">${item.nombre}</h6>
                                        <small class="text-muted">ID: ${item.id}</small>
                                    </div>
                                    <div class="text-end flex-shrink-0 ms-2">
                                        <span class="badge bg-primary">${item.clave}</span>
                                    </div>
                                </div>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-warning btn-editar w-100" 
                                        data-id="${item.id}" 
                                        data-clave="${item.clave}"
                                        data-nombre="${item.nombre}">
                                        <i class="bi bi-pencil-square me-2"></i>Editar
                                    </button>
                                    <button class="btn btn-danger btn-eliminar w-100" data-id="${item.id}">
                                        <i class="bi bi-trash-fill me-2"></i>Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>`;
                    }
                });
                
                // Inicializar tooltips
                if (typeof bootstrap !== 'undefined') {
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });
                }
            }
        };

        const loadData = async () => {
            try {
                const res = await fetch('/GESTACAD/controllers/becasController.php?action=index');
                const data = await res.json();
                renderTable(data);
            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'No se pudieron cargar los datos', 'error');
            }
        };

        const btnNuevaBeca = document.getElementById('btnNuevaBeca');
        if (btnNuevaBeca) {
            btnNuevaBeca.addEventListener('click', () => {
                form.reset();
                document.getElementById('id').value = '';
                modalLabel.textContent = 'Agregar Beca';
                modal.show();
            });
        }

        if (!esTutor) {
            // Solo para administradores y coordinadores
            document.addEventListener('click', async (e) => {
                if (e.target.closest('.btn-editar')) {
                    const btn = e.target.closest('.btn-editar');
                    document.getElementById('id').value = btn.dataset.id;
                    document.getElementById('clave').value = btn.dataset.clave;
                    document.getElementById('nombre').value = btn.dataset.nombre;
                    modalLabel.textContent = 'Editar Beca';
                    modal.show();
                }

                if (e.target.closest('.btn-eliminar')) {
                    const btn = e.target.closest('.btn-eliminar');
                    const result = await Swal.fire({
                        title: '¿Eliminar?',
                        text: "No podrás revertir esto",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar'
                    });

                    if (result.isConfirmed) {
                        const formData = new FormData();
                        formData.append('id', btn.dataset.id);
                        const res = await fetch('/GESTACAD/controllers/becasController.php?action=delete', { method: 'POST', body: formData });
                        const data = await res.json();
                        if (data.status === 'success') {
                            Swal.fire('Eliminado', data.message, 'success');
                            loadData();
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    }
                }
            });

            if (btnGuardar) {
                btnGuardar.addEventListener('click', async () => {
                    const formData = new FormData(form);
                    const id = document.getElementById('id').value;
                    const action = id ? 'update' : 'store';

                    try {
                        const res = await fetch(`/GESTACAD/controllers/becasController.php?action=${action}`, { method: 'POST', body: formData });
                        const data = await res.json();
                        if (data.status === 'ok') {
                            modal.hide();
                            Swal.fire('Éxito', 'Operación realizada correctamente', 'success');
                            loadData();
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    } catch (error) {
                        console.error(error);
                        Swal.fire('Error', 'Error de conexión', 'error');
                    }
                });
            }
        }

        loadData();
    });
</script>