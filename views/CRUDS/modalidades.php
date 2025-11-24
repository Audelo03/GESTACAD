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

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col-md-6">
            <button class="btn btn-success" id="btnNuevaModalidad">
                <i class="bi bi-plus-circle"></i> Agregar Modalidad
            </button>
        </div>
        <div class="col-md-6">
            <div class="input-group">
                <input type="text" class="form-control" id="searchInput" placeholder="Buscar modalidades...">
                <button class="btn btn-outline-secondary" type="button" id="btnSearch">
                    <i class="bi bi-search"></i>
                </button>
                <button class="btn btn-outline-secondary" type="button" id="btnClear">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="modalidadesBody"></tbody>
        </table>
    </div>

    <nav aria-label="Paginación de modalidades" class="mt-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    <label for="itemsPerPage" class="form-label me-2 mb-0">Mostrar:</label>
                    <select class="form-select form-select-sm" id="itemsPerPage" style="width: auto;">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span class="ms-2 text-muted" id="paginationInfo">Mostrando 0 de 0 registros</span>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="pagination justify-content-end mb-0" id="paginationControls">
                    </ul>
            </div>
        </div>
    </nav>
</div>

<div class="modal fade" id="modalidadModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Formulario de Modalidad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formModalidad">
                    <input type="hidden" id="id_modalidad" name="id">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre de la Modalidad</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardar">Guardar</button>
            </div>
        </div>
    </div>
</div>

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

    // REEMPLAZO DE dom.setHTML
    const renderSpinner = () => {
        modalidadesBody.innerHTML = '<tr><td colspan="3" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></td></tr>';
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

        if (!modalidades.length) {
            modalidadesBody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">No se encontraron modalidades</td></tr>';
            return;
        }

        // REEMPLAZO DE dom.appendHTML por insertAdjacentHTML
        modalidades.forEach((m) => {
            const row = `
                <tr>
                    <td>${m.id_modalidad}</td>
                    <td>${m.nombre}</td>
                    <td>
                        <button class="btn btn-warning btn-sm btn-editar" data-id="${m.id_modalidad}" data-nombre="${m.nombre}" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar Modalidad">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-danger btn-sm btn-eliminar" data-id="${m.id_modalidad}" data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar Modalidad">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </td>
                </tr>`;
            modalidadesBody.insertAdjacentHTML('beforeend', row);
        });

        // Inicializar tooltips de Bootstrap si existen
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    };

    const showError = (message) => {
        modalidadesBody.innerHTML = `<tr><td colspan="3" class="text-center text-danger">${message}</td></tr>`;
        Swal.fire({ icon: 'error', title: 'Error', text: message });
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
            const response = await fetch(`/GORA/controllers/modalidadesController.php?${params}`);
            
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
                        
                        const response = await fetch('/GORA/controllers/modalidadesController.php?action=delete', {
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
        const url = isUpdate ? '/GORA/controllers/modalidadesController.php?action=update' : '/GORA/controllers/modalidadesController.php?action=store';
        
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