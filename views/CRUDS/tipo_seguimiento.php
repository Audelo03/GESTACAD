<?php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php";

$auth = new AuthController($conn);
$auth->checkAuth();

$page_title = "Seguimientos";
$modificacion_ruta = "../";
include __DIR__ . "/../objects/header.php";
?>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col-md-6">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#tipoSeguimientoModal" id="btnNuevo">
                <i class="bi bi-plus-circle"></i> Agregar Tipo
            </button>
        </div>
        <div class="col-md-6">
            <div class="input-group">
                <input type="text" class="form-control" id="searchInput" placeholder="Buscar tipos de seguimiento...">
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
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tiposBody"></tbody>
        </table>
    </div>

    <nav aria-label="Paginación de tipos de seguimiento" class="mt-3">
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

<div class="modal fade" id="tipoSeguimientoModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Formulario de Tipo de Seguimiento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formTipo">
                    <input type="hidden" id="id_tipo_seguimiento" name="id">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
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

<?php include __DIR__ . "/../objects/footer.php"; ?>

<script>
window.addEventListener('load', function() {
    const tipoModalEl = document.getElementById('tipoSeguimientoModal');
    const tipoModal = tipoModalEl ? new bootstrap.Modal(tipoModalEl) : null;
    const form = document.getElementById('formTipo');
    const tiposBody = document.getElementById('tiposBody');
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

    const renderSpinner = () => {
        tiposBody.innerHTML = '<tr><td colspan="3" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></td></tr>';
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
            link.innerHTML = label;
            li.appendChild(link);
            paginationControls.appendChild(li);
        };

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

    // --- CORRECCIÓN AQUÍ: Validación de datos ---
    const renderTipos = (tipos) => {
        if (!tiposBody) return;
        
        tiposBody.innerHTML = '';

        // Validamos si 'tipos' existe y si es un array antes de leer .length
        if (!tipos || !Array.isArray(tipos) || tipos.length === 0) {
            tiposBody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">No se encontraron tipos de seguimiento</td></tr>';
            return;
        }

        tipos.forEach((t) => {
            const row = `
                <tr>
                    <td>${t.id_tipo_seguimiento}</td>
                    <td>${t.nombre}</td>
                    <td>
                        <button class="btn btn-warning btn-sm btn-editar" data-id="${t.id_tipo_seguimiento}" data-nombre="${t.nombre}" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar Tipo">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-danger btn-sm btn-eliminar" data-id="${t.id_tipo_seguimiento}" data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar Tipo">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </td>
                </tr>`;
            tiposBody.insertAdjacentHTML('beforeend', row);
        });

        if (typeof bootstrap !== 'undefined') {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        }
    };

    const showError = (message) => {
        tiposBody.innerHTML = `<tr><td colspan="3" class="text-center text-danger">${message}</td></tr>`;
        Swal.fire({ icon: 'error', title: 'Error', text: message });
    };

    const cargarTipos = async (page = 1, search = '') => {
        if (isLoading) return;
        isLoading = true;
        currentPage = page;
        searchTerm = search;
        renderSpinner();

        const params = new URLSearchParams({ action: 'paginated', page, limit: itemsPerPage, search });
        try {
            const response = await fetch(`/GESTACAD/controllers/tipoSeguimientoController.php?${params}`);
            
            if (!response.ok) throw new Error('Error en la respuesta del servidor');

            const data = await response.json();
            
            // Debug: Mira en la consola qué devuelve el servidor
            console.log("Datos recibidos:", data); 

            if (data.success) {
                totalItems = data.total;
                totalPages = data.totalPages;
                currentPage = data.currentPage;
                
                // --- CORRECCIÓN AQUÍ: Forzar un array vacío si data.tipos no existe ---
                // Si tu PHP devuelve data.data o data.seguimientos, cambia data.tipos por eso.
                renderTipos(data.tiposSeguimiento || []); 
                
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

    document.getElementById('btnNuevo')?.addEventListener('click', () => {
        form?.reset();
        const idField = document.getElementById('id_tipo_seguimiento');
        if (idField) idField.value = '';
        if (modalLabel) modalLabel.textContent = 'Agregar Tipo de Seguimiento';
        tipoModal?.show();
    });

    document.getElementById('btnSearch')?.addEventListener('click', () => {
        const search = searchInput?.value.trim() || '';
        cargarTipos(1, search);
    });

    searchInput?.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            const search = searchInput.value.trim();
            cargarTipos(1, search);
        }
    });

    document.getElementById('btnClear')?.addEventListener('click', () => {
        if (searchInput) searchInput.value = '';
        cargarTipos(1, '');
    });

    itemsPerPageSelect?.addEventListener('change', () => {
        itemsPerPage = parseInt(itemsPerPageSelect.value, 10) || 10;
        cargarTipos(1, searchTerm);
    });

    paginationControls?.addEventListener('click', (e) => {
        const link = e.target.closest('.page-link');
        if (!link) return;
        e.preventDefault();
        const page = parseInt(link.dataset.page, 10);
        if (!page || page === currentPage || page < 1 || page > totalPages) return;
        cargarTipos(page, searchTerm);
    });

    document.addEventListener('click', (e) => {
        const editBtn = e.target.closest('.btn-editar');
        if (editBtn) {
            document.getElementById('id_tipo_seguimiento').value = editBtn.dataset.id;
            document.getElementById('nombre').value = editBtn.dataset.nombre || '';
            if (modalLabel) modalLabel.textContent = 'Editar Tipo de Seguimiento';
            tipoModal?.show();
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
                        const formData = new FormData();
                        formData.append('id', id);

                        const response = await fetch('/GESTACAD/controllers/tipoSeguimientoController.php?action=delete', {
                            method: 'POST',
                            body: formData
                        });

                        const data = await response.json();

                        if(data.success || data === true) {
                            Swal.fire('¡Eliminado!', 'El tipo de seguimiento ha sido eliminado.', 'success');
                            cargarTipos(currentPage, searchTerm);
                        } else {
                            throw new Error(data.message || 'Error al eliminar');
                        }
                    } catch (error) {
                        Swal.fire('Error', 'No se pudo eliminar el tipo de seguimiento.', 'error');
                    }
                }
            });
        }
    });

    document.getElementById('btnGuardar')?.addEventListener('click', async () => {
        const idField = document.getElementById('id_tipo_seguimiento');
        const isUpdate = idField && idField.value;
        const url = isUpdate ? '/GESTACAD/controllers/tipoSeguimientoController.php?action=update' : '/GESTACAD/controllers/tipoSeguimientoController.php?action=store';
        
        const formData = new FormData(form);

        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (response.ok) {
                tipoModal?.hide();
                cargarTipos(currentPage, searchTerm);
                Swal.fire({ icon: 'success', title: '¡Guardado!', text: 'El tipo de seguimiento ha sido guardado correctamente.', timer: 1500, showConfirmButton: false });
            } else {
                 throw new Error(data.message || 'Error del servidor');
            }
        } catch (error) {
            Swal.fire({ icon: 'error', title: 'Oops...', text: 'Error al guardar el tipo de seguimiento. Revise los datos e intente de nuevo.' });
        }
    });

    tipoModalEl?.addEventListener('hidden.bs.modal', () => {
        form?.reset();
    });

    cargarTipos();
});
</script>