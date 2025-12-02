<?php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php";

$auth = new AuthController($conn);
$auth->checkAuth();
$page_title = "Mi Plan de Acción Tutorial (PAT)";
include __DIR__ . "/../objects/header.php";
?>

<div class="container mt-3 mt-md-4">
    <!-- Header -->
    <div class="card shadow-sm mb-3 mb-md-4 crud-header-card">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-3">
                <div>
                    <h1 class="h4 h3-md mb-1 mb-md-2">
                        <i class="bi bi-clipboard-check me-2 text-primary"></i>
                        Mi Plan de Acción Tutorial
                    </h1>
                    <p class="text-muted small mb-0 d-none d-md-block">Gestiona tus actividades personalizadas del PAT</p>
                </div>
                <div class="d-flex flex-column flex-md-row gap-2 w-100 w-md-auto">
                    <button class="btn btn-success btn-lg w-100 w-md-auto" id="btnNuevoPAT">
                        <i class="bi bi-plus-circle me-2"></i>
                        <span class="d-none d-sm-inline">Nueva Actividad</span>
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
                <table class="table table-hover mb-0 crud-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 80px;">ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Fecha de Creación</th>
                            <th class="text-center" style="width: 150px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="patBody">
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <div class="mt-2 text-muted">Cargando actividades...</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Cards Móvil -->
    <div class="d-md-none" id="patCardsContainer">
        <div id="patCardsBody">
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <div class="mt-2 text-muted">Cargando actividades...</div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear/editar actividad PAT -->
<div class="modal fade" id="patModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold" id="modalLabel">
                    <i class="bi bi-clipboard-check me-2"></i>Actividad PAT
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 p-md-4">
                <form id="formPAT">
                    <input type="hidden" id="pat-id" name="id">
                    <div class="mb-3">
                        <label for="pat-nombre" class="form-label fw-semibold">
                            <i class="bi bi-tag me-1 text-success"></i>Nombre de la Actividad <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="pat-nombre" name="nombre" class="form-control" required maxlength="200" placeholder="Ej: Bienvenida e Inducción">
                    </div>
                    <div class="mb-3">
                        <label for="pat-descripcion" class="form-label fw-semibold">
                            <i class="bi bi-card-text me-1 text-success"></i>Descripción
                        </label>
                        <textarea id="pat-descripcion" name="descripcion" class="form-control" rows="4" placeholder="Describe la actividad..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top p-3 p-md-4">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-success" id="btnGuardarPAT">
                    <i class="bi bi-check-circle me-2"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../CRUDS/crud_helper_styles.php'; ?>

<?php include __DIR__ . "/../objects/footer.php"; ?>

<script>
    (function() {
        'use strict';
        
        const modalEl = document.getElementById('patModal');
        const modal = new bootstrap.Modal(modalEl);
        const form = document.getElementById('formPAT');
        const tbody = document.getElementById('patBody');
        const patCardsBody = document.getElementById('patCardsBody');
        const btnGuardar = document.getElementById('btnGuardarPAT');
        const modalLabel = document.getElementById('modalLabel');

        // Cargar actividades
        function loadData() {
            // Spinner para tabla
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div><div class="mt-2 text-muted">Cargando actividades...</div></td></tr>';
            
            // Spinner para cards
            if (patCardsBody) {
                patCardsBody.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div><div class="mt-2 text-muted">Cargando actividades...</div></div>';
            }
            
            fetch('/GESTACAD/controllers/tutoriasController.php?action=getPatTutorActividades')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderTable(data.data);
                        renderCards(data.data);
                    } else {
                        showError('Error al cargar las actividades');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Error de conexión');
                });
        }

        // Renderizar tabla desktop
        function renderTable(data) {
            tbody.innerHTML = '';
            if (!data || data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted"><i class="bi bi-inbox me-2"></i>No hay actividades registradas. Crea tu primera actividad.</td></tr>';
                return;
            }
            
            data.forEach(item => {
                const fecha = new Date(item.created_at).toLocaleDateString('es-MX', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
                
                tbody.innerHTML += `
                    <tr class="align-middle">
                        <td class="text-center fw-bold text-primary">${item.id}</td>
                        <td><strong>${escapeHtml(item.nombre)}</strong></td>
                        <td>${escapeHtml(item.descripcion || 'Sin descripción')}</td>
                        <td><small class="text-muted">${fecha}</small></td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-warning btn-editar" 
                                    data-id="${item.id}" 
                                    data-nombre="${escapeHtml(item.nombre)}"
                                    data-descripcion="${escapeHtml(item.descripcion || '')}"
                                    data-bs-toggle="tooltip" 
                                    data-bs-placement="top" 
                                    title="Editar Actividad">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btn-eliminar" 
                                    data-id="${item.id}"
                                    data-bs-toggle="tooltip" 
                                    data-bs-placement="top" 
                                    title="Eliminar Actividad">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });

            // Inicializar tooltips
            if (typeof bootstrap !== 'undefined') {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }
        }

        // Renderizar cards móvil
        function renderCards(data) {
            if (!patCardsBody) return;
            
            patCardsBody.innerHTML = '';
            if (!data || data.length === 0) {
                patCardsBody.innerHTML = '<div class="alert alert-info mb-0"><i class="bi bi-inbox me-2"></i>No hay actividades registradas. Crea tu primera actividad.</div>';
                return;
            }
            
            data.forEach(item => {
                const fecha = new Date(item.created_at).toLocaleDateString('es-MX', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
                
                // Obtener iniciales del nombre
                const palabras = item.nombre.split(' ');
                const iniciales = palabras.length > 0 
                    ? palabras.map(p => p[0]).join('').substring(0, 2).toUpperCase()
                    : 'PA';
                
                const card = `
                    <div class="card shadow-sm mb-3 crud-card-mobile">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-start mb-3 gap-3">
                                <div class="user-avatar flex-shrink-0">
                                    <span class="avatar-initials">${iniciales}</span>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <h6 class="mb-1 fw-bold text-truncate">${escapeHtml(item.nombre)}</h6>
                                    <small class="text-muted d-block mb-2">
                                        <i class="bi bi-calendar3 me-1"></i>${fecha}
                                    </small>
                                    <p class="text-muted small mb-0" style="word-wrap: break-word;">
                                        ${escapeHtml(item.descripcion || 'Sin descripción')}
                                    </p>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <button class="btn btn-warning btn-editar w-100" 
                                    data-id="${item.id}" 
                                    data-nombre="${escapeHtml(item.nombre)}"
                                    data-descripcion="${escapeHtml(item.descripcion || '')}">
                                    <i class="bi bi-pencil-square me-2"></i>Editar
                                </button>
                                <button class="btn btn-danger btn-eliminar w-100" 
                                    data-id="${item.id}">
                                    <i class="bi bi-trash-fill me-2"></i>Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                patCardsBody.insertAdjacentHTML('beforeend', card);
            });
        }

        // Mostrar error
        function showError(message) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger py-4"><i class="bi bi-exclamation-triangle me-2"></i>${message}</td></tr>`;
            if (patCardsBody) {
                patCardsBody.innerHTML = `<div class="alert alert-danger mb-0"><i class="bi bi-exclamation-triangle me-2"></i>${message}</div>`;
            }
        }

        // Escapar HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Botón nuevo
        document.getElementById('btnNuevoPAT').addEventListener('click', () => {
            form.reset();
            document.getElementById('pat-id').value = '';
            modalLabel.textContent = 'Nueva Actividad PAT';
            modal.show();
        });

        // Editar
        document.addEventListener('click', (e) => {
            if (e.target.closest('.btn-editar')) {
                const btn = e.target.closest('.btn-editar');
                document.getElementById('pat-id').value = btn.dataset.id;
                document.getElementById('pat-nombre').value = btn.dataset.nombre;
                document.getElementById('pat-descripcion').value = btn.dataset.descripcion;
                modalLabel.textContent = 'Editar Actividad PAT';
                modal.show();
            }

            if (e.target.closest('.btn-eliminar')) {
                const btn = e.target.closest('.btn-eliminar');
                const actividadNombre = btn.dataset.nombre || 'esta actividad';
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '¿Eliminar actividad?',
                        text: `¿Estás seguro de eliminar "${actividadNombre}"?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            eliminarActividad(btn.dataset.id);
                        }
                    });
                } else {
                    if (confirm(`¿Estás seguro de eliminar "${actividadNombre}"?`)) {
                        eliminarActividad(btn.dataset.id);
                    }
                }
            }
        });

        // Función para eliminar actividad
        function eliminarActividad(id) {
            const formData = new FormData();
            formData.append('id', id);
            
            fetch('/GESTACAD/controllers/tutoriasController.php?action=deletePatTutorActividad', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadData();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Eliminado', data.message, 'success');
                    } else {
                        alert(data.message);
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Error', data.message, 'error');
                    } else {
                        alert('Error: ' + data.message);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error', 'Error de conexión', 'error');
                } else {
                    alert('Error de conexión');
                }
            });
        }

        // Guardar
        btnGuardar.addEventListener('click', () => {
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            const id = document.getElementById('pat-id').value;
            const action = id ? 'updatePatTutorActividad' : 'createPatTutorActividad';

            btnGuardar.disabled = true;
            btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';

            fetch(`/GESTACAD/controllers/tutoriasController.php?action=${action}`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    modal.hide();
                    loadData();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Éxito', data.message, 'success');
                    } else {
                        alert(data.message);
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Error', data.message, 'error');
                    } else {
                        alert('Error: ' + data.message);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error', 'Error de conexión', 'error');
                } else {
                    alert('Error de conexión');
                }
            })
            .finally(() => {
                btnGuardar.disabled = false;
                btnGuardar.innerHTML = '<i class="bi bi-check-circle me-2"></i>Guardar';
            });
        });

        // Cargar datos al iniciar
        loadData();
    })();
</script>
