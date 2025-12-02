<?php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php";

$auth = new AuthController($conn);
$auth->checkAuth();
$page_title = "Usuarios";
include __DIR__ . "/../objects/header.php";

?>

<div class="container mt-3 mt-md-4">
    <!-- Header con título y acciones -->
    <div class="card shadow-sm mb-3 mb-md-4 usuarios-header-card">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-3">
                <div>
                    <h1 class="h4 h3-md mb-1 mb-md-2">
                        <i class="bi bi-person-vcard me-2 text-primary"></i>
                        Gestión de Usuarios
                    </h1>
                    <p class="text-muted small mb-0 d-none d-md-block">Administra los usuarios del sistema</p>
                </div>
                <div class="d-flex flex-column flex-md-row gap-2 w-100 w-md-auto">
                    <button class="btn btn-primary btn-lg w-100 w-md-auto" id="btnNuevoUsuario">
                        <i class="bi bi-plus-circle me-2"></i>
                        <span class="d-none d-sm-inline">Agregar Usuario</span>
                        <span class="d-sm-none">Nuevo</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Barra de búsqueda -->
    <div class="card shadow-sm mb-3 mb-md-4">
        <div class="card-body p-3 p-md-4">
            <div class="input-group input-group-lg">
                <span class="input-group-text bg-primary text-white">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" class="form-control" id="searchInput" placeholder="Buscar por nombre, email o nivel...">
                <button class="btn btn-outline-primary" type="button" id="btnSearch">
                    <i class="bi bi-search me-1 d-none d-sm-inline"></i>
                    <span class="d-none d-sm-inline">Buscar</span>
                    <span class="d-sm-none">Buscar</span>
                </button>
                <button class="btn btn-outline-secondary" type="button" id="btnClear" title="Limpiar búsqueda">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Tabla Desktop -->
    <div class="table-responsive d-none d-md-block">
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover mb-0" id="tablaUsuarios">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 80px;">ID</th>
                            <th>Nombre Completo</th>
                            <th>Email</th>
                            <th class="text-center" style="width: 150px;">Nivel</th>
                            <th class="text-center" style="width: 150px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="usuariosBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Cards Móvil -->
    <div class="d-md-none" id="usuariosCardsContainer">
        <div id="usuariosCardsBody"></div>
    </div>

    <!-- Paginación -->
    <div class="card shadow-sm mt-3 mt-md-4">
        <div class="card-body p-3 p-md-4">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-6">
                    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-2">
                        <label for="itemsPerPage" class="form-label mb-0 small fw-semibold">Mostrar:</label>
                        <select class="form-select form-select-sm" id="itemsPerPage" style="width: auto; min-width: 80px;">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                        <span class="text-muted small" id="paginationInfo">Mostrando 0 de 0 registros</span>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <nav aria-label="Paginación de usuarios">
                        <ul class="pagination justify-content-center justify-content-md-end mb-0 pagination-sm" id="paginationControls">
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="usuarioModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="modalLabel">
                    <i class="bi bi-person-plus me-2"></i>Formulario de Usuario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 p-md-4">
                 <form id="formUsuario">
                    <input type="hidden" id="id_usuario" name="id_usuario">
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-4">
                            <label for="nombre" class="form-label fw-semibold">
                                <i class="bi bi-person me-1 text-primary"></i>Nombre(s)
                            </label>
                            <input type="text" id="nombre" name="nombre" class="form-control" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="apellido_paterno" class="form-label fw-semibold">
                                <i class="bi bi-person me-1 text-primary"></i>Apellido Paterno
                            </label>
                            <input type="text" id="apellido_paterno" name="apellido_paterno" class="form-control" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="apellido_materno" class="form-label fw-semibold">
                                <i class="bi bi-person me-1 text-primary"></i>Apellido Materno
                            </label>
                            <input type="text" id="apellido_materno" name="apellido_materno" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">
                            <i class="bi bi-envelope me-1 text-primary"></i>Email
                        </label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">
                            <i class="bi bi-lock me-1 text-primary"></i>Contraseña
                        </label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Dejar en blanco para no cambiar">
                        <small class="form-text text-muted">
                            <i class="bi bi-info-circle me-1"></i>La contraseña es requerida para nuevos usuarios.
                        </small>
                    </div>
                    <div class="mb-3">
                        <label for="niveles_usuarios_id_nivel_usuario" class="form-label fw-semibold">
                            <i class="bi bi-shield-check me-1 text-primary"></i>Nivel de Usuario
                        </label>
                        <select id="niveles_usuarios_id_nivel_usuario" name="niveles_usuarios_id_nivel_usuario" class="form-select" required>
                            <option value="">Seleccione un nivel</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top p-3 p-md-4">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="btnGuardarUsuario">
                    <i class="bi bi-check-circle me-2"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos personalizados para el CRUD de usuarios */
.usuarios-header-card {
    background: linear-gradient(135deg, rgba(91, 155, 213, 0.1), rgba(91, 155, 213, 0.05));
    border-left: 4px solid #5b9bd5;
}

.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #5b9bd5, #4a8bc2);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.avatar-circle-lg {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #5b9bd5, #4a8bc2);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(91, 155, 213, 0.3);
}

.usuario-card-mobile {
    transition: all 0.3s ease;
    border-left: 4px solid #5b9bd5;
    overflow: hidden; /* Evita que el contenido se salga */
}

.usuario-card-mobile:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(91, 155, 213, 0.2) !important;
}

.usuario-card-mobile .card-body {
    overflow: hidden; /* Evita desbordamiento */
    word-wrap: break-word; /* Permite que el texto largo se ajuste */
}

.usuario-card-mobile .badge {
    white-space: nowrap; /* Evita que el badge se parta */
    display: inline-block;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
}

#tablaUsuarios tbody tr {
    transition: all 0.2s ease;
}

#tablaUsuarios tbody tr:hover {
    background-color: rgba(91, 155, 213, 0.05);
    transform: scale(1.01);
}

#tablaUsuarios thead th {
    background: linear-gradient(180deg, var(--bg-surface-1), var(--bg-surface-2));
    color: #5b9bd5;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    border-bottom: 2px solid rgba(91, 155, 213, 0.2);
}

.btn-group .btn {
    border-radius: 0.375rem;
    margin: 0 2px;
}

.btn-group .btn:first-child {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

.btn-group .btn:last-child {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}

/* Mejoras móviles */
@media (max-width: 767.98px) {
    .usuario-card-mobile .btn {
        min-height: 44px;
        font-size: 0.95rem;
    }
    
    .usuario-card-mobile .card-body {
        padding: 1rem !important;
    }
    
    .usuario-card-mobile .d-flex {
        flex-wrap: wrap;
    }
    
    .usuario-card-mobile .text-end {
        width: 100%;
        text-align: left !important;
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        border-top: 1px solid var(--border-color);
    }
    
    .usuario-card-mobile .text-end .badge {
        display: inline-block;
        margin-bottom: 0.25rem;
    }
    
    .modal-dialog {
        margin: 0.5rem;
        max-width: calc(100% - 1rem);
    }
    
    .modal-body {
        padding: 1rem;
    }
    
    .form-control, .form-select {
        min-height: 48px;
        font-size: 16px;
    }
}

/* Animaciones */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.usuario-card-mobile {
    animation: fadeIn 0.3s ease;
}

#tablaUsuarios tbody tr {
    animation: fadeIn 0.3s ease;
}
</style>

<?php include __DIR__ . "/../objects/footer.php"; ?>

<script>
window.addEventListener('load', function() {
    const usuarioModalEl = document.getElementById('usuarioModal');
    const usuarioModal = usuarioModalEl ? new bootstrap.Modal(usuarioModalEl) : null;
    const form = document.getElementById('formUsuario');
    const usuariosBody = document.getElementById('usuariosBody');
    const paginationControls = document.getElementById('paginationControls');
    const paginationInfo = document.getElementById('paginationInfo');
    const searchInput = document.getElementById('searchInput');
    const itemsPerPageSelect = document.getElementById('itemsPerPage');
    const modalLabel = document.getElementById('modalLabel');
    const passwordInput = document.getElementById('password');
    const nivelSelect = document.getElementById('niveles_usuarios_id_nivel_usuario');
    const guardarBtn = document.getElementById('btnGuardarUsuario');

    let currentPage = 1;
    let itemsPerPage = 10;
    let totalItems = 0;
    let totalPages = 0;
    let searchTerm = '';
    let isLoading = false;

    // Reemplazo de dom.setHTML
    const renderSpinner = () => {
        usuariosBody.innerHTML = '<tr><td colspan="5" class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div><div class="mt-2 text-muted">Cargando usuarios...</div></td></tr>';
        const usuariosCardsBody = document.getElementById('usuariosCardsBody');
        if (usuariosCardsBody) {
            usuariosCardsBody.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div><div class="mt-2 text-muted">Cargando usuarios...</div></div>';
        }
    };

    const updatePaginationInfo = () => {
        const start = ((currentPage - 1) * itemsPerPage) + 1;
        const end = Math.min(currentPage * itemsPerPage, totalItems);
        if (paginationInfo) paginationInfo.textContent = `Mostrando ${start}-${end} de ${totalItems} registros`;
    };

    const renderPaginationControls = () => {
        if (!paginationControls) return;
        paginationControls.innerHTML = '';
        if (totalPages <= 1) return;

        const addItem = (page, label, disabled = false, active = false) => {
            const li = document.createElement('li');
            li.className = `page-item ${disabled ? 'disabled' : ''} ${active ? 'active' : ''}`.trim();
            const link = document.createElement('a');
            link.className = 'page-link';
            link.href = '#';
            link.dataset.page = page;
            link.innerHTML = label; // Usamos innerHTML para permitir entidades HTML
            li.appendChild(link);
            paginationControls.appendChild(li);
        };

        // Corrección de caracteres
        addItem(currentPage - 1, '&laquo; Anterior', currentPage === 1);
        
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(totalPages, currentPage + 2);

        if (startPage > 1) {
            addItem(1, '1');
            if (startPage > 2) {
                const dots = document.createElement('li');
                dots.className = 'page-item disabled';
                dots.innerHTML = '<span class="page-link">...</span>';
                paginationControls.appendChild(dots);
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            addItem(i, String(i), false, i === currentPage);
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                const dots = document.createElement('li');
                dots.className = 'page-item disabled';
                dots.innerHTML = '<span class="page-link">...</span>';
                paginationControls.appendChild(dots);
            }
            addItem(totalPages, String(totalPages));
        }

        addItem(currentPage + 1, 'Siguiente &raquo;', currentPage === totalPages);
    };

    const renderUsuarios = (usuarios) => {
        if (!usuariosBody) return;
        
        const usuariosCardsBody = document.getElementById('usuariosCardsBody');
        
        // Limpiamos contenido
        usuariosBody.innerHTML = '';
        if (usuariosCardsBody) usuariosCardsBody.innerHTML = '';

        // Validación para evitar error de .length en undefined
        if (!usuarios || !Array.isArray(usuarios) || usuarios.length === 0) {
            usuariosBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4"><i class="bi bi-inbox me-2"></i>No se encontraron usuarios</td></tr>';
            if (usuariosCardsBody) {
                usuariosCardsBody.innerHTML = '<div class="alert alert-info mb-0"><i class="bi bi-inbox me-2"></i>No se encontraron usuarios</div>';
            }
            return;
        }

        // Función para obtener badge de nivel
        const getNivelBadge = (nivel) => {
            const niveles = {
                'Administrador': 'bg-danger',
                'Coordinador': 'bg-warning',
                'Tutor': 'bg-info',
                'Usuario': 'bg-secondary'
            };
            const badgeClass = niveles[nivel] || 'bg-secondary';
            return `<span class="badge ${badgeClass} text-white">${nivel || 'N/A'}</span>`;
        };

        // Renderizar tabla desktop
        usuarios.forEach((u) => {
            const nombreCompleto = `${u.nombre} ${u.apellido_paterno} ${u.apellido_materno ?? ''}`.trim();
            const row = `
                <tr class="align-middle">
                    <td class="text-center fw-bold text-primary">${u.id_usuario}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle me-2">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">${nombreCompleto}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <i class="bi bi-envelope me-2 text-muted"></i>
                        <a href="mailto:${u.email}" class="text-decoration-none">${u.email}</a>
                    </td>
                    <td class="text-center">${getNivelBadge(u.nivel_usuario)}</td>
                    <td class="text-center">
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-warning btn-editar" 
                                data-id="${u.id_usuario}" 
                                data-nombre="${u.nombre}" 
                                data-apellido-paterno="${u.apellido_paterno}" 
                                data-apellido-materno="${u.apellido_materno ?? ''}" 
                                data-email="${u.email}" 
                                data-nivel="${u.niveles_usuarios_id_nivel_usuario ?? ''}" 
                                data-bs-toggle="tooltip" 
                                data-bs-placement="top" 
                                title="Editar Usuario">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-sm btn-danger btn-eliminar" 
                                data-id="${u.id_usuario}" 
                                data-bs-toggle="tooltip" 
                                data-bs-placement="top" 
                                title="Eliminar Usuario">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </div>
                    </td>
                </tr>`;
            usuariosBody.insertAdjacentHTML('beforeend', row);
        });

        // Renderizar cards móvil
        if (usuariosCardsBody) {
            usuarios.forEach((u) => {
                const nombreCompleto = `${u.nombre} ${u.apellido_paterno} ${u.apellido_materno ?? ''}`.trim();
                const card = `
                    <div class="card shadow-sm mb-3 usuario-card-mobile">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-3 gap-2">
                                <div class="d-flex align-items-center flex-grow-1 min-w-0">
                                    <div class="avatar-circle-lg me-3 flex-shrink-0">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <h6 class="mb-1 fw-bold text-truncate">${nombreCompleto}</h6>
                                        <small class="text-muted d-block text-truncate">
                                            <i class="bi bi-envelope me-1"></i>
                                            <a href="mailto:${u.email}" class="text-decoration-none">${u.email}</a>
                                        </small>
                                    </div>
                                </div>
                                <div class="text-end flex-shrink-0 ms-2">
                                    <div class="mb-2">${getNivelBadge(u.nivel_usuario)}</div>
                                    <small class="text-muted d-block">ID: ${u.id_usuario}</small>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <button class="btn btn-warning btn-editar w-100" 
                                    data-id="${u.id_usuario}" 
                                    data-nombre="${u.nombre}" 
                                    data-apellido-paterno="${u.apellido_paterno}" 
                                    data-apellido-materno="${u.apellido_materno ?? ''}" 
                                    data-email="${u.email}" 
                                    data-nivel="${u.niveles_usuarios_id_nivel_usuario ?? ''}">
                                    <i class="bi bi-pencil-square me-2"></i>Editar
                                </button>
                                <button class="btn btn-danger btn-eliminar w-100" 
                                    data-id="${u.id_usuario}">
                                    <i class="bi bi-trash-fill me-2"></i>Eliminar
                                </button>
                            </div>
                        </div>
                    </div>`;
                usuariosCardsBody.insertAdjacentHTML('beforeend', card);
            });
        }

        // Inicializar tooltips
        if (typeof bootstrap !== 'undefined') {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        }
    };

    const showError = (message) => {
        usuariosBody.innerHTML = `<tr><td colspan="5" class="text-center text-danger py-4"><i class="bi bi-exclamation-triangle me-2"></i>${message}</td></tr>`;
        const usuariosCardsBody = document.getElementById('usuariosCardsBody');
        if (usuariosCardsBody) {
            usuariosCardsBody.innerHTML = `<div class="alert alert-danger mb-0"><i class="bi bi-exclamation-triangle me-2"></i>${message}</div>`;
        }
        if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'error', title: 'Error', text: message });
        }
    };

    // Reemplazo de dom.fetchJSON
    const cargarUsuarios = async (page = 1, search = '') => {
        if (isLoading) return;
        isLoading = true;
        currentPage = page;
        searchTerm = search;
        renderSpinner();

        const params = new URLSearchParams({ action: 'paginated', page, limit: itemsPerPage, search });
        try {
            const response = await fetch(`/GESTACAD/controllers/usuarioController.php?${params}`);
            
            if (!response.ok) throw new Error('Error en la respuesta del servidor');

            const data = await response.json();
            
            if (data.success) {
                totalItems = data.total;
                totalPages = data.totalPages;
                currentPage = data.currentPage;
                // Aseguramos que se pase un array aunque la clave sea nula o diferente
                renderUsuarios(data.usuarios || []);
                updatePaginationInfo();
                renderPaginationControls();
            } else {
                showError('Error al cargar los datos: ' + (data.message || 'Error desconocido'));
            }
        } catch (error) {
            console.error(error);
            showError('Error de conexión: ' + error.message);
        } finally {
            isLoading = false;
        }
    };

    // Reemplazo de dom.fetchJSON para niveles
    const cargarNiveles = async () => {
        if (!nivelSelect) return;
        try {
            const response = await fetch('/GESTACAD/controllers/usuarioController.php?action=getLevels');
            
            // Asumimos que getLevels devuelve un array directo o un objeto con data
            let niveles = [];
            if(response.ok) {
                const data = await response.json();
                // Verificamos si es array directo o viene dentro de una propiedad
                niveles = Array.isArray(data) ? data : (data.data || []);
            }

            let options = '<option value="">Seleccione un nivel</option>';
            niveles.forEach((level) => {
                options += `<option value="${level.id_nivel_usuario}">${level.nombre}</option>`;
            });
            nivelSelect.innerHTML = options;
        } catch (error) {
            console.error('No se pudieron cargar los niveles', error);
        }
    };

    const resetForm = (isNew = true) => {
        form?.reset();
        const idField = document.getElementById('id_usuario');
        if (idField) idField.value = '';
        if (passwordInput) {
            passwordInput.value = '';
            passwordInput.required = isNew;
        }
        if (modalLabel) modalLabel.textContent = isNew ? 'Agregar Usuario' : 'Editar Usuario';
        if (nivelSelect) nivelSelect.value = '';
    };

    document.getElementById('btnNuevoUsuario')?.addEventListener('click', async () => {
        resetForm(true);
        // Asegurar que los niveles estén cargados antes de abrir el modal
        if (nivelSelect && nivelSelect.options.length <= 1) {
            await cargarNiveles();
        }
        usuarioModal?.show();
    });

    document.getElementById('btnSearch')?.addEventListener('click', () => {
        const search = searchInput?.value.trim() || '';
        cargarUsuarios(1, search);
    });

    searchInput?.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            cargarUsuarios(1, searchInput.value.trim());
        }
    });

    document.getElementById('btnClear')?.addEventListener('click', () => {
        if (searchInput) searchInput.value = '';
        cargarUsuarios(1, '');
    });

    itemsPerPageSelect?.addEventListener('change', () => {
        itemsPerPage = parseInt(itemsPerPageSelect.value, 10) || 10;
        cargarUsuarios(1, searchTerm);
    });

    paginationControls?.addEventListener('click', (e) => {
        const link = e.target.closest('.page-link');
        if (!link) return;
        e.preventDefault();
        const page = parseInt(link.dataset.page, 10);
        if (!page || page === currentPage || page < 1 || page > totalPages) return;
        cargarUsuarios(page, searchTerm);
    });

    document.addEventListener('click', async (e) => {
        const editBtn = e.target.closest('.btn-editar');
        if (editBtn) {
            resetForm(false);
            // Asegurar que los niveles estén cargados antes de establecer el valor
            if (nivelSelect && nivelSelect.options.length <= 1) {
                await cargarNiveles();
            }
            document.getElementById('id_usuario').value = editBtn.dataset.id;
            document.getElementById('nombre').value = editBtn.dataset.nombre || '';
            document.getElementById('apellido_paterno').value = editBtn.dataset.apellidoPaterno || '';
            document.getElementById('apellido_materno').value = editBtn.dataset.apellidoMaterno || '';
            document.getElementById('email').value = editBtn.dataset.email || '';
            if (nivelSelect) nivelSelect.value = editBtn.dataset.nivel || '';
            if (passwordInput) passwordInput.required = false;
            if (modalLabel) modalLabel.textContent = 'Editar Usuario';
            usuarioModal?.show();
            return;
        }

        const deleteBtn = e.target.closest('.btn-eliminar');
        if (deleteBtn) {
            const id = deleteBtn.dataset.id;
            Swal.fire({
                title: '¿Estás seguro?',
                text: '¡No podrás revertir esta acción!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        // Reemplazo de dom.postForm para eliminar
                        const formData = new FormData();
                        formData.append('id_usuario', id);

                        const response = await fetch('/GESTACAD/controllers/usuarioController.php?action=delete', {
                            method: 'POST',
                            body: formData
                        });

                        const data = await response.json();

                        if (data.success || data.status === 'ok' || data === true) {
                            usuarioModal?.hide();
                            cargarUsuarios(currentPage, searchTerm);
                            Swal.fire('¡Eliminado!', 'El usuario ha sido eliminado.', 'success');
                        } else {
                            throw new Error(data.message || 'Error al eliminar');
                        }
                    } catch (error) {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo eliminar el usuario. Verifique la consola o dependencias.' });
                    }
                }
            });
        }
    });

    // Reemplazo de dom.postForm para guardar
    const guardarUsuario = async () => {
        const idField = document.getElementById('id_usuario');
        const isUpdate = Boolean(idField && idField.value);
        if (!isUpdate && passwordInput && !passwordInput.value) {
            Swal.fire({ icon: 'error', title: 'Campo requerido', text: 'La contraseña es obligatoria para los nuevos usuarios.' });
            return;
        }

        const url = isUpdate ? '/GESTACAD/controllers/usuarioController.php?action=update' : '/GESTACAD/controllers/usuarioController.php?action=store';
        
        const formData = new FormData(form);

        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            
            // Verificamos success (ajusta según tu API si devuelve "status" o "success")
            if (response.ok && (data.success || data.status === 'ok')) {
                Swal.fire({ icon: 'success', title: 'Éxito', text: data.message || 'Operación completada', timer: 2000, showConfirmButton: false });
                usuarioModal?.hide();
                form?.reset();
                cargarUsuarios(currentPage, searchTerm);
            } else {
                throw new Error(data.message || 'Error en la operación');
            }
        } catch (error) {
            console.error(error);
            Swal.fire({ icon: 'error', title: 'Ocurrió un error', text: error.message || 'No se pudo conectar con el servidor.' });
        }
    };

    guardarBtn?.addEventListener('click', guardarUsuario);

    usuarioModalEl?.addEventListener('hidden.bs.modal', () => {
        resetForm(true);
    });

    cargarNiveles();
    cargarUsuarios();
});
</script>