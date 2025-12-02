<?php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php";

$auth = new AuthController($conn);
$auth->checkAuth();
$page_title = "Plan de Acción Tutorial (PAT) - Catálogo";
include __DIR__ . "/../objects/header.php";
?>

<div class="container mt-3 mt-md-4">
    <!-- Header -->
    <div class="card shadow-sm mb-3 mb-md-4 crud-header-card">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-3">
                <div>
                    <h1 class="h4 h3-md mb-1 mb-md-2">
                        <i class="bi bi-clipboard-data me-2 text-primary"></i>
                        Catálogo de Actividades PAT
                    </h1>
                    <p class="text-muted small mb-0 d-none d-md-block">Explora actividades de ejemplo y añádelas a tu PAT personal</p>
                    <p class="text-muted small mb-0 d-md-none">Actividades de ejemplo para tu PAT</p>
                </div>
                <div class="d-flex flex-column flex-md-row gap-2 w-100 w-md-auto">
                    <a href="/GESTACAD/tutorias/mi-pat" class="btn btn-success btn-lg w-100 w-md-auto">
                        <i class="bi bi-clipboard-check me-2"></i>
                        <span class="d-none d-sm-inline">Ver Mi PAT</span>
                        <span class="d-sm-none">Mi PAT</span>
                    </a>
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
                            <th>Parcial</th>
                            <th>Sesión</th>
                            <th>Carrera/Grupo</th>
                            <th class="text-center" style="width: 150px;">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="patBody">
                        <tr>
                            <td colspan="7" class="text-center py-5">
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

<?php include __DIR__ . '/../CRUDS/crud_helper_styles.php'; ?>

<?php include __DIR__ . "/../objects/footer.php"; ?>

<script>
    (function() {
        'use strict';
        
        const tbody = document.getElementById('patBody');
        const patCardsBody = document.getElementById('patCardsBody');

        // Cargar actividades
        function loadData() {
            // Spinner para tabla
            tbody.innerHTML = '<tr><td colspan="7" class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div><div class="mt-2 text-muted">Cargando actividades...</div></td></tr>';
            
            // Spinner para cards
            if (patCardsBody) {
                patCardsBody.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div><div class="mt-2 text-muted">Cargando actividades...</div></div>';
            }
            
            fetch('/GESTACAD/controllers/tutoriasController.php?action=getPatGeneralActividades')
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
                tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted"><i class="bi bi-inbox me-2"></i>No hay actividades disponibles</td></tr>';
                return;
            }
            
            data.forEach(item => {
                const carreraGrupo = item.carrera_nombre 
                    ? item.carrera_nombre 
                    : (item.grupo_nombre ? item.grupo_nombre : 'General');
                
                tbody.innerHTML += `
                    <tr class="align-middle">
                        <td class="text-center fw-bold text-primary">${item.id}</td>
                        <td><strong>${escapeHtml(item.nombre)}</strong></td>
                        <td>${escapeHtml(item.descripcion || 'Sin descripción')}</td>
                        <td><span class="badge bg-info">${item.parcial_numero || item.parcial_id}</span></td>
                        <td><span class="badge bg-secondary">${item.sesion_num}</span></td>
                        <td><small class="text-muted">${carreraGrupo}</small></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-success btn-anadir" 
                                data-id="${item.id}" 
                                data-nombre="${escapeHtml(item.nombre)}"
                                data-bs-toggle="tooltip" 
                                data-bs-placement="top" 
                                title="Añadir a mi PAT">
                                <i class="bi bi-plus-circle me-1"></i>Añadir
                            </button>
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
                patCardsBody.innerHTML = '<div class="alert alert-info mb-0"><i class="bi bi-inbox me-2"></i>No hay actividades disponibles</div>';
                return;
            }
            
            data.forEach(item => {
                const carreraGrupo = item.carrera_nombre 
                    ? item.carrera_nombre 
                    : (item.grupo_nombre ? item.grupo_nombre : 'General');
                
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
                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                        <span class="badge bg-info">Parcial ${item.parcial_numero || item.parcial_id}</span>
                                        <span class="badge bg-secondary">Sesión ${item.sesion_num}</span>
                                        <span class="badge bg-light text-dark">${carreraGrupo}</span>
                                    </div>
                                    <p class="text-muted small mb-0" style="word-wrap: break-word;">
                                        ${escapeHtml(item.descripcion || 'Sin descripción')}
                                    </p>
                                </div>
                            </div>
                            <div class="d-grid">
                                <button class="btn btn-success btn-anadir w-100" 
                                    data-id="${item.id}" 
                                    data-nombre="${escapeHtml(item.nombre)}">
                                    <i class="bi bi-plus-circle me-2"></i>Añadir a mi PAT
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
            tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-4"><i class="bi bi-exclamation-triangle me-2"></i>${message}</td></tr>`;
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

        // Añadir actividad a mi PAT
        document.addEventListener('click', (e) => {
            if (e.target.closest('.btn-anadir')) {
                const btn = e.target.closest('.btn-anadir');
                const actividadId = btn.dataset.id;
                const actividadNombre = btn.dataset.nombre;
                
                // Deshabilitar botón mientras se procesa
                btn.disabled = true;
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Añadiendo...';
                
                const formData = new FormData();
                formData.append('actividad_id', actividadId);
                
                fetch('/GESTACAD/controllers/tutoriasController.php?action=copiarPatATutor', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Actividad añadida!',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 2000
                            });
                        } else {
                            alert(data.message);
                        }
                        // Cambiar botón a estado de éxito
                        btn.classList.remove('btn-success');
                        btn.classList.add('btn-outline-success');
                        btn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Añadida';
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message
                            });
                        } else {
                            alert('Error: ' + data.message);
                        }
                        // Restaurar botón
                        btn.disabled = false;
                        btn.innerHTML = originalHTML;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error de conexión'
                        });
                    } else {
                        alert('Error de conexión');
                    }
                    // Restaurar botón
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                });
            }
        });

        // Cargar datos al iniciar
        loadData();
    })();
</script>
