<?php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php";

// Aseguramos que la sesión y auth funcionen
$auth = new AuthController($conn);
$auth->checkAuth();

$modificacion_ruta = "../";
$page_title = "Modalidades";
include __DIR__ . "/../objects/header.php";
?>

<div class="container mt-3 mt-md-4">
    <!-- Header -->
    <div class="card shadow-sm mb-3 mb-md-4 crud-header-card">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-3">
                <div>
                    <h1 class="h4 h3-md mb-1 mb-md-2">
                        <i class="bi bi-person-video3 me-2 text-primary"></i>
                        Gestión de Modalidades
                    </h1>
                    <p class="text-muted small mb-0 d-none d-md-block">Administra las modalidades académicas</p>
                </div>
                <div class="d-flex flex-column flex-md-row gap-2 w-100 w-md-auto">
                    <button class="btn btn-primary btn-lg w-100 w-md-auto" id="btnNuevaModalidad">
                        <i class="bi bi-plus-circle me-2"></i>
                        <span class="d-none d-sm-inline">Agregar Modalidad</span>
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
                <input type="text" class="form-control" id="searchInput" placeholder="Buscar modalidades...">
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
                <table class="table table-hover mb-0 crud-table" id="tablaModalidades">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 80px;">ID</th>
                            <th>Nombre</th>
                            <th class="text-center" style="width: 150px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="modalidadesBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Cards Móvil -->
    <div class="d-md-none" id="modalidadesCardsContainer">
        <div id="modalidadesCardsBody"></div>
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
                    <nav aria-label="Paginación de modalidades">
                        <ul class="pagination justify-content-center justify-content-md-end mb-0 pagination-sm" id="paginationControls">
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalidadModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="modalLabel">
                    <i class="bi bi-person-video3 me-2"></i>Formulario de Modalidad
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 p-md-4">
                <form id="formModalidad">
                    <input type="hidden" id="id_modalidad" name="id">
                    <div class="mb-3">
                        <label for="nombre" class="form-label fw-semibold">
                            <i class="bi bi-tag me-1 text-primary"></i>Nombre de la Modalidad
                        </label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
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

<?php
include __DIR__ . '/../objects/footer.php';
?>

<script>
window.addEventListener('load', function() {
    const modalidadModalEl = document.getElementById('modalidadModal');
    const modalidadModal = modalidadModalEl ? new bootstrap.Modal(modalidadModalEl) : null;
    const form = document.getElementById('formModalidad');
    const modalidadesBody = document.getElementById('modalidadesBody');
    const paginationControls = document.getElementById('paginationControls');
    const paginationInfo = document.getElementById('paginationInfo');
    const searchInput = document.getElementById('searchInput');
    const itemsPerPageSelect = document.getElementById('itemsPerPage');
    const modalLabel = document.getElementById('modalLabel');

    let currentPage = 1;
    let itemsPerPage = 10;
    let totalItems = 0;
    let totalPages = 0;
    let searchTerm = '';
    let isLoading = false;

    const modalidadesCardsBody = document.getElementById('modalidadesCardsBody');
    
    // REEMPLAZO DE dom.setHTML
    const renderSpinner = () => {
        modalidadesBody.innerHTML = '<tr><td colspan="3" class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div><div class="mt-2 text-muted">Cargando modalidades...</div></td></tr>';
        if (modalidadesCardsBody) {
            modalidadesCardsBody.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div><div class="mt-2 text-muted">Cargando modalidades...</div></div>';
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

        // CORRECCIÓN DE CARACTERES DE PAGINACIÓN
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

    const renderModalidades = (modalidades) => {
        if (!modalidadesBody) return;
        
        // Limpiamos el contenido actual
        modalidadesBody.innerHTML = '';
        if (modalidadesCardsBody) modalidadesCardsBody.innerHTML = '';

        if (!modalidades.length) {
            modalidadesBody.innerHTML = '<tr><td colspan="3" class="text-center py-4 text-muted"><i class="bi bi-inbox me-2"></i>No se encontraron modalidades</td></tr>';
            if (modalidadesCardsBody) {
                modalidadesCardsBody.innerHTML = '<div class="alert alert-info mb-0"><i class="bi bi-inbox me-2"></i>No se encontraron modalidades</div>';
            }
            return;
        }

        // REEMPLAZO DE dom.appendHTML por insertAdjacentHTML
        modalidades.forEach((m) => {
            // Tabla desktop
            const row = `
                <tr class="align-middle">
                    <td class="text-center fw-bold text-primary">${m.id_modalidad}</td>
                    <td>${m.nombre}</td>
                    <td class="text-center">
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-warning btn-editar" data-id="${m.id_modalidad}" data-nombre="${m.nombre}" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar Modalidad">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-sm btn-danger btn-eliminar" data-id="${m.id_modalidad}" data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar Modalidad">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </div>
                    </td>
                </tr>`;
            modalidadesBody.insertAdjacentHTML('beforeend', row);
            
            // Cards móvil
            if (modalidadesCardsBody) {
                const card = `
                    <div class="card shadow-sm mb-3 crud-card-mobile">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-3 gap-2">
                                <div class="flex-grow-1 min-w-0">
                                    <h6 class="mb-1 fw-bold">${m.nombre}</h6>
                                    <small class="text-muted">ID: ${m.id_modalidad}</small>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <button class="btn btn-warning btn-editar w-100" data-id="${m.id_modalidad}" data-nombre="${m.nombre}">
                                    <i class="bi bi-pencil-square me-2"></i>Editar
                                </button>
                                <button class="btn btn-danger btn-eliminar w-100" data-id="${m.id_modalidad}">
                                    <i class="bi bi-trash-fill me-2"></i>Eliminar
                                </button>
                            </div>
                        </div>
                    </div>`;
                modalidadesCardsBody.insertAdjacentHTML('beforeend', card);
            }
        });

        // Inicializar tooltips de Bootstrap si existen
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    };

    const showError = (message) => {
        modalidadesBody.innerHTML = `<tr><td colspan="3" class="text-center text-danger py-4"><i class="bi bi-exclamation-triangle me-2"></i>${message}</td></tr>`;
        if (modalidadesCardsBody) {
            modalidadesCardsBody.innerHTML = `<div class="alert alert-danger mb-0"><i class="bi bi-exclamation-triangle me-2"></i>${message}</div>`;
        }
        if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'error', title: 'Error', text: message });
        }
    };

    // REEMPLAZO DE dom.fetchJSON por fetch nativo
    const cargarModalidades = async (page = 1, search = '') => {
        if (isLoading) return;
        isLoading = true;
        currentPage = page;
        searchTerm = search;
        renderSpinner();

        const params = new URLSearchParams({ action: 'paginated', page, limit: itemsPerPage, search });
        try {
            const response = await fetch(`/GESTACAD/controllers/modalidadesController.php?${params}`);
            
            if (!response.ok) throw new Error('Error en la respuesta del servidor');
            
            const data = await response.json();
            
            if (data.success) {
                totalItems = data.total;
                totalPages = data.totalPages;
                currentPage = data.currentPage;
                renderModalidades(data.modalidades);
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

    document.getElementById('btnNuevaModalidad')?.addEventListener('click', () => {
        form?.reset();
        const idField = document.getElementById('id_modalidad');
        if (idField) idField.value = '';
        if (modalLabel) modalLabel.textContent = 'Agregar Modalidad';
        modalidadModal?.show();
    });

    document.getElementById('btnSearch')?.addEventListener('click', () => {
        const search = searchInput?.value.trim() || '';
        cargarModalidades(1, search);
    });

    searchInput?.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            const search = searchInput.value.trim();
            cargarModalidades(1, search);
        }
    });

    document.getElementById('btnClear')?.addEventListener('click', () => {
        if (searchInput) searchInput.value = '';
        cargarModalidades(1, '');
    });

    itemsPerPageSelect?.addEventListener('change', () => {
        itemsPerPage = parseInt(itemsPerPageSelect.value, 10) || 10;
        cargarModalidades(1, searchTerm);
    });

    paginationControls?.addEventListener('click', (e) => {
        const link = e.target.closest('.page-link');
        if (!link) return;
        e.preventDefault();
        const page = parseInt(link.dataset.page, 10);
        if (!page || page === currentPage || page < 1 || page > totalPages) return;
        cargarModalidades(page, searchTerm);
    });

    document.addEventListener('click', (e) => {
        const editBtn = e.target.closest('.btn-editar');
        if (editBtn) {
            document.getElementById('id_modalidad').value = editBtn.dataset.id;
            document.getElementById('nombre').value = editBtn.dataset.nombre || '';
            if (modalLabel) modalLabel.textContent = 'Editar Modalidad';
            modalidadModal?.show();
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
                confirmButtonText: 'Sí, ¡elimínalo!',
                cancelButtonText: 'Cancelar'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        // REEMPLAZO DE dom.postForm para eliminar
                        const formData = new FormData();
                        formData.append('id', id);
                        
                        const response = await fetch('/GESTACAD/controllers/modalidadesController.php?action=delete', {
                            method: 'POST',
                            body: formData
                        });
                        
                        const data = await response.json();
                        
                        if(data.success || data === true) { // Ajusta según lo que devuelva tu PHP
                             Swal.fire('¡Eliminado!', 'La modalidad ha sido eliminada.', 'success');
                             cargarModalidades(currentPage, searchTerm);
                        } else {
                             throw new Error(data.message || 'Error al eliminar');
                        }

                    } catch (error) {
                        Swal.fire('Error', 'No se pudo eliminar la modalidad.', 'error');
                    }
                }
            });
        }
    });

    // REEMPLAZO DE dom.serializeForm y dom.postForm para guardar
    document.getElementById('btnGuardar')?.addEventListener('click', async () => {
        const idField = document.getElementById('id_modalidad');
        const isUpdate = idField && idField.value;
        const url = isUpdate ? '/GESTACAD/controllers/modalidadesController.php?action=update' : '/GESTACAD/controllers/modalidadesController.php?action=store';
        
        const formData = new FormData(form);
        
        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            // Asumimos que el backend devuelve un JSON con success: true
            if (response.ok) {
                modalidadModal?.hide();
                cargarModalidades(currentPage, searchTerm);
                Swal.fire({ icon: 'success', title: '¡Guardado!', text: 'La modalidad ha sido guardada correctamente.', timer: 1500, showConfirmButton: false });
            } else {
                 throw new Error(data.message || 'Error en el servidor');
            }
        } catch (error) {
            Swal.fire({ icon: 'error', title: 'Oops...', text: 'Error al guardar la modalidad. Revise los datos e intente de nuevo.' });
        }
    });

    modalidadModalEl?.addEventListener('hidden.bs.modal', () => {
        form?.reset();
    });

    cargarModalidades();
});
</script>