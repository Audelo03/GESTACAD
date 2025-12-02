<?php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php";

$auth = new AuthController($conn);
$auth->checkAuth();
$page_title = "Periodos Escolares";
include __DIR__ . "/../objects/header.php";
?>

<div class="container mt-3 mt-md-4">
    <!-- Header -->
    <div class="card shadow-sm mb-3 mb-md-4 crud-header-card">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-3">
                <div>
                    <h1 class="h4 h3-md mb-1 mb-md-2">
                        <i class="bi bi-calendar-range me-2 text-primary"></i>
                        Gestión de Periodos Escolares
                    </h1>
                    <p class="text-muted small mb-0 d-none d-md-block">Administra los periodos escolares</p>
                </div>
                <div class="d-flex flex-column flex-md-row gap-2 w-100 w-md-auto">
                    <button class="btn btn-primary btn-lg w-100 w-md-auto" id="btnNuevoPeriodo">
                        <i class="bi bi-plus-circle me-2"></i>
                        <span class="d-none d-sm-inline">Agregar Periodo</span>
                        <span class="d-sm-none">Nuevo</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla Desktop -->
    <div class="table-responsive d-none d-md-block">
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover mb-0 crud-table" id="tablaPeriodos">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 80px;">ID</th>
                            <th>Nombre</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th class="text-center" style="width: 100px;">Activo</th>
                            <th class="text-center" style="width: 150px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="periodosBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Cards Móvil -->
    <div class="d-md-none" id="periodosCardsContainer">
        <div id="periodosCardsBody"></div>
    </div>
</div>

<div class="modal fade" id="periodoModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="modalLabel">
                    <i class="bi bi-calendar-range me-2"></i>Formulario de Periodo
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 p-md-4">
                <form id="formPeriodo">
                    <input type="hidden" id="id" name="id">
                    <div class="mb-3">
                        <label for="nombre" class="form-label fw-semibold">
                            <i class="bi bi-tag me-1 text-primary"></i>Nombre
                        </label>
                        <input type="text" id="nombre" name="nombre" class="form-control" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="fecha_inicio" class="form-label fw-semibold">
                                <i class="bi bi-calendar-event me-1 text-primary"></i>Fecha Inicio
                            </label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="fecha_fin" class="form-label fw-semibold">
                                <i class="bi bi-calendar-x me-1 text-primary"></i>Fecha Fin
                            </label>
                            <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="activo" name="activo" checked>
                            <label class="form-check-label fw-semibold" for="activo">
                                <i class="bi bi-check-circle me-1 text-primary"></i>Periodo Activo
                            </label>
                        </div>
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

<?php include __DIR__ . '/crud_helper_styles.php'; ?>

<?php include __DIR__ . "/../objects/footer.php"; ?>

<script>
    window.addEventListener('load', function () {
        const modalEl = document.getElementById('periodoModal');
        const modal = new bootstrap.Modal(modalEl);
        const form = document.getElementById('formPeriodo');
        const tbody = document.getElementById('periodosBody');
        const btnGuardar = document.getElementById('btnGuardar');
        const modalLabel = document.getElementById('modalLabel');

        const periodosCardsBody = document.getElementById('periodosCardsBody');
        
        const renderTable = (data) => {
            tbody.innerHTML = '';
            if (periodosCardsBody) periodosCardsBody.innerHTML = '';
            
            if (!data || data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted"><i class="bi bi-inbox me-2"></i>No hay registros</td></tr>';
                if (periodosCardsBody) {
                    periodosCardsBody.innerHTML = '<div class="alert alert-info mb-0"><i class="bi bi-inbox me-2"></i>No hay registros</div>';
                }
                return;
            }
            
            data.forEach(item => {
                const fechaInicio = item.fecha_inicio ? new Date(item.fecha_inicio).toLocaleDateString('es-MX') : 'N/A';
                const fechaFin = item.fecha_fin ? new Date(item.fecha_fin).toLocaleDateString('es-MX') : 'N/A';
                const activoBadge = item.activo == 1 ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>';
                
                // Tabla desktop
                tbody.innerHTML += `
                <tr class="align-middle">
                    <td class="text-center fw-bold text-primary">${item.id}</td>
                    <td><strong>${item.nombre}</strong></td>
                    <td>${fechaInicio}</td>
                    <td>${fechaFin}</td>
                    <td class="text-center">${activoBadge}</td>
                    <td class="text-center">
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-warning btn-editar" 
                                data-id="${item.id}" 
                                data-nombre="${item.nombre}"
                                data-inicio="${item.fecha_inicio}"
                                data-fin="${item.fecha_fin}"
                                data-activo="${item.activo}"
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
                if (periodosCardsBody) {
                    periodosCardsBody.innerHTML += `
                    <div class="card shadow-sm mb-3 crud-card-mobile">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-3 gap-2">
                                <div class="flex-grow-1 min-w-0">
                                    <h6 class="mb-1 fw-bold">${item.nombre}</h6>
                                    <small class="text-muted">ID: ${item.id}</small>
                                </div>
                                <div class="text-end flex-shrink-0 ms-2">
                                    ${activoBadge}
                                </div>
                            </div>
                            <div class="border-top pt-2">
                                <div class="row g-2 small">
                                    <div class="col-6"><strong>Inicio:</strong><br>${fechaInicio}</div>
                                    <div class="col-6"><strong>Fin:</strong><br>${fechaFin}</div>
                                </div>
                            </div>
                            <div class="d-grid gap-2 mt-3">
                                <button class="btn btn-warning btn-editar w-100" 
                                    data-id="${item.id}" 
                                    data-nombre="${item.nombre}"
                                    data-inicio="${item.fecha_inicio}"
                                    data-fin="${item.fecha_fin}"
                                    data-activo="${item.activo}">
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
        };

        const loadData = async () => {
            try {
                const res = await fetch('/GESTACAD/controllers/periodosController.php?action=index');
                const data = await res.json();
                renderTable(data);
            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'No se pudieron cargar los datos', 'error');
            }
        };

        document.getElementById('btnNuevoPeriodo').addEventListener('click', () => {
            form.reset();
            document.getElementById('id').value = '';
            document.getElementById('activo').checked = true;
            modalLabel.textContent = 'Agregar Periodo';
            modal.show();
        });

        document.addEventListener('click', async (e) => {
            if (e.target.closest('.btn-editar')) {
                const btn = e.target.closest('.btn-editar');
                document.getElementById('id').value = btn.dataset.id;
                document.getElementById('nombre').value = btn.dataset.nombre;
                document.getElementById('fecha_inicio').value = btn.dataset.inicio;
                document.getElementById('fecha_fin').value = btn.dataset.fin;
                document.getElementById('activo').checked = btn.dataset.activo == 1;
                modalLabel.textContent = 'Editar Periodo';
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
                    const res = await fetch('/GESTACAD/controllers/periodosController.php?action=delete', { method: 'POST', body: formData });
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

        btnGuardar.addEventListener('click', async () => {
            const formData = new FormData(form);
            const id = document.getElementById('id').value;
            const action = id ? 'update' : 'store';

            try {
                const res = await fetch(`/GESTACAD/controllers/periodosController.php?action=${action}`, { method: 'POST', body: formData });
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

        loadData();
    });
</script>