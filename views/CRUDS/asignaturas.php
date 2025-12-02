<?php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php";

$auth = new AuthController($conn);
$auth->checkAuth();
$page_title = "Asignaturas";
include __DIR__ . "/../objects/header.php";
?>

<div class="container mt-3 mt-md-4">
    <!-- Header -->
    <div class="card shadow-sm mb-3 mb-md-4 crud-header-card">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-3">
                <div>
                    <h1 class="h4 h3-md mb-1 mb-md-2">
                        <i class="bi bi-journal-bookmark me-2 text-primary"></i>
                        Gestión de Asignaturas
                    </h1>
                    <p class="text-muted small mb-0 d-none d-md-block">Administra las asignaturas académicas</p>
                </div>
                <div class="d-flex flex-column flex-md-row gap-2 w-100 w-md-auto">
                    <button class="btn btn-primary btn-lg w-100 w-md-auto" id="btnNuevaAsignatura">
                        <i class="bi bi-plus-circle me-2"></i>
                        <span class="d-none d-sm-inline">Agregar Asignatura</span>
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
                <table class="table table-hover mb-0 crud-table" id="tablaAsignaturas">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 80px;">ID</th>
                            <th>Clave</th>
                            <th>Nombre</th>
                            <th class="text-center" style="width: 100px;">Créditos</th>
                            <th class="text-center" style="width: 120px;">Horas/Sem</th>
                            <th>Área</th>
                            <th class="text-center" style="width: 150px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="asignaturasBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Cards Móvil -->
    <div class="d-md-none" id="asignaturasCardsContainer">
        <div id="asignaturasCardsBody"></div>
    </div>
</div>

<div class="modal fade" id="asignaturaModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="modalLabel">
                    <i class="bi bi-journal-bookmark me-2"></i>Formulario de Asignatura
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 p-md-4">
                <form id="formAsignatura">
                    <input type="hidden" id="id" name="id">
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-4">
                            <label for="clave" class="form-label fw-semibold">
                                <i class="bi bi-key me-1 text-primary"></i>Clave
                            </label>
                            <input type="text" id="clave" name="clave" class="form-control" required>
                        </div>
                        <div class="col-12 col-md-8">
                            <label for="nombre" class="form-label fw-semibold">
                                <i class="bi bi-tag me-1 text-primary"></i>Nombre
                            </label>
                            <input type="text" id="nombre" name="nombre" class="form-control" required>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label for="creditos" class="form-label fw-semibold">
                                <i class="bi bi-star me-1 text-primary"></i>Créditos
                            </label>
                            <input type="number" id="creditos" name="creditos" class="form-control">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="horas_semana" class="form-label fw-semibold">
                                <i class="bi bi-clock me-1 text-primary"></i>Horas/Semana
                            </label>
                            <input type="number" id="horas_semana" name="horas_semana" class="form-control">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="area" class="form-label fw-semibold">
                                <i class="bi bi-folder me-1 text-primary"></i>Área
                            </label>
                            <input type="text" id="area" name="area" class="form-control">
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
        const modalEl = document.getElementById('asignaturaModal');
        const modal = new bootstrap.Modal(modalEl);
        const form = document.getElementById('formAsignatura');
        const tbody = document.getElementById('asignaturasBody');
        const btnGuardar = document.getElementById('btnGuardar');
        const modalLabel = document.getElementById('modalLabel');

        const asignaturasCardsBody = document.getElementById('asignaturasCardsBody');
        
        const renderTable = (data) => {
            tbody.innerHTML = '';
            if (asignaturasCardsBody) asignaturasCardsBody.innerHTML = '';
            
            if (!data || data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted"><i class="bi bi-inbox me-2"></i>No hay registros</td></tr>';
                if (asignaturasCardsBody) {
                    asignaturasCardsBody.innerHTML = '<div class="alert alert-info mb-0"><i class="bi bi-inbox me-2"></i>No hay registros</div>';
                }
                return;
            }
            
            data.forEach(item => {
                // Tabla desktop
                tbody.innerHTML += `
                <tr class="align-middle">
                    <td class="text-center fw-bold text-primary">${item.id}</td>
                    <td><span class="badge bg-primary">${item.clave}</span></td>
                    <td>${item.nombre}</td>
                    <td class="text-center">${item.creditos || '-'}</td>
                    <td class="text-center">${item.horas_semana || '-'}</td>
                    <td>${item.area || '-'}</td>
                    <td class="text-center">
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-warning btn-editar" 
                                data-id="${item.id}" 
                                data-clave="${item.clave}"
                                data-nombre="${item.nombre}"
                                data-creditos="${item.creditos}"
                                data-horas="${item.horas_semana}"
                                data-area="${item.area}"
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
                if (asignaturasCardsBody) {
                    asignaturasCardsBody.innerHTML += `
                    <div class="card shadow-sm mb-3 crud-card-mobile">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-3 gap-2">
                                <div class="flex-grow-1 min-w-0">
                                    <h6 class="mb-1 fw-bold text-truncate">${item.nombre}</h6>
                                    <small class="text-muted">ID: ${item.id}</small>
                                </div>
                                <div class="text-end flex-shrink-0 ms-2">
                                    <span class="badge bg-primary">${item.clave}</span>
                                </div>
                            </div>
                            <div class="border-top pt-2">
                                <div class="row g-2 small">
                                    <div class="col-4"><strong>Créditos:</strong><br>${item.creditos || '-'}</div>
                                    <div class="col-4"><strong>Horas/Sem:</strong><br>${item.horas_semana || '-'}</div>
                                    <div class="col-4"><strong>Área:</strong><br>${item.area || '-'}</div>
                                </div>
                            </div>
                            <div class="d-grid gap-2 mt-3">
                                <button class="btn btn-warning btn-editar w-100" 
                                    data-id="${item.id}" 
                                    data-clave="${item.clave}"
                                    data-nombre="${item.nombre}"
                                    data-creditos="${item.creditos}"
                                    data-horas="${item.horas_semana}"
                                    data-area="${item.area}">
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
                const res = await fetch('/GESTACAD/controllers/asignaturasController.php?action=index');
                const data = await res.json();
                renderTable(data);
            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'No se pudieron cargar los datos', 'error');
            }
        };

        document.getElementById('btnNuevaAsignatura').addEventListener('click', () => {
            form.reset();
            document.getElementById('id').value = '';
            modalLabel.textContent = 'Agregar Asignatura';
            modal.show();
        });

        document.addEventListener('click', async (e) => {
            if (e.target.closest('.btn-editar')) {
                const btn = e.target.closest('.btn-editar');
                document.getElementById('id').value = btn.dataset.id;
                document.getElementById('clave').value = btn.dataset.clave;
                document.getElementById('nombre').value = btn.dataset.nombre;
                document.getElementById('creditos').value = btn.dataset.creditos;
                document.getElementById('horas_semana').value = btn.dataset.horas;
                document.getElementById('area').value = btn.dataset.area;
                modalLabel.textContent = 'Editar Asignatura';
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
                    const res = await fetch('/GESTACAD/controllers/asignaturasController.php?action=delete', { method: 'POST', body: formData });
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
                const res = await fetch(`/GESTACAD/controllers/asignaturasController.php?action=${action}`, { method: 'POST', body: formData });
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