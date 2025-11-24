<?php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php"; 

$auth = new AuthController($conn);
$auth->checkAuth();

// Obtener datos para los selects
$carreras = $conn->query("SELECT id_carrera, nombre FROM carreras ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
// Asumiendo que 3 es el ID para tutores, ajusta si es necesario
$tutores = $conn->query("SELECT id_usuario, CONCAT(nombre, ' ', apellido_paterno) as nombre_completo FROM usuarios WHERE niveles_usuarios_id_nivel_usuario = 3 ORDER BY nombre_completo")->fetchAll(PDO::FETCH_ASSOC); 
$modalidades = $conn->query("SELECT id_modalidad, nombre FROM modalidades ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

$modificacion_ruta = "../";
$page_title = "Grupos";
include __DIR__ . "/../objects/header.php";
?>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col-md-6">
            <button class="btn btn-success" id="btnNuevoGrupo">
                <i class="bi bi-plus-circle"></i> Agregar Grupo
            </button>
        </div>
        <div class="col-md-6">
            <div class="input-group">
                <input type="text" class="form-control" id="searchInput" placeholder="Buscar grupos...">
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
                    <th>Tutor</th>
                    <th>Carrera</th>
                    <th>Modalidad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="gruposBody"></tbody>
        </table>
    </div>

    <nav aria-label="Paginación de grupos" class="mt-3">
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

<div class="modal fade" id="grupoModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Formulario de Grupo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formGrupo">
                    <input type="hidden" id="id_grupo" name="id_grupo">

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del Grupo</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>

                    <div class="mb-3">
                        <label for="usuarios_id_usuario_tutor" class="form-label">Tutor Asignado</label>
                        <select class="form-select" id="usuarios_id_usuario_tutor" name="usuarios_id_usuario_tutor" required>
                            <option value="">Seleccione un tutor</option>
                            <?php foreach ($tutores as $tutor) : ?>
                                <option value="<?= $tutor['id_usuario'] ?>"><?= htmlspecialchars($tutor['nombre_completo']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="carreras_id_carrera" class="form-label">Carrera</label>
                        <select class="form-select" id="carreras_id_carrera" name="carreras_id_carrera" required>
                            <option value="">Seleccione una carrera</option>
                            <?php foreach ($carreras as $carrera) : ?>
                                <option value="<?= $carrera['id_carrera'] ?>"><?= htmlspecialchars($carrera['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="modalidades_id_modalidad" class="form-label">Modalidad</label>
                        <select class="form-select" id="modalidades_id_modalidad" name="modalidades_id_modalidad" required>
                            <option value="">Seleccione una modalidad</option>
                            <?php foreach ($modalidades as $modalidad) : ?>
                                <option value="<?= $modalidad['id_modalidad'] ?>"><?= htmlspecialchars($modalidad['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
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
    const grupoModalEl = document.getElementById('grupoModal');
    const grupoModal = grupoModalEl ? new bootstrap.Modal(grupoModalEl) : null;
    const form = document.getElementById('formGrupo');
    const gruposBody = document.getElementById('gruposBody');
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
        gruposBody.innerHTML = '<tr><td colspan="6" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></td></tr>';
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
            link.innerHTML = label; // Usamos innerHTML para permitir caracteres HTML
            li.appendChild(link);
            paginationControls.appendChild(li);
        };

        // CORRECCIÓN DE CARACTERES
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

    const renderGrupos = (grupos) => {
        if (!gruposBody) return;
        
        // Limpiamos contenido previo
        gruposBody.innerHTML = '';

        if (!grupos.length) {
            gruposBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No se encontraron grupos</td></tr>';
            return;
        }

        // REEMPLAZO DE dom.appendHTML
        grupos.forEach((g) => {
            const tutorNombre = g.tutor_nombre ? `${g.tutor_nombre} ${g.tutor_apellido_paterno || ''}`.trim() : 'N/A';
            const row = `
                <tr>
                    <td>${g.id_grupo}</td>
                    <td>${g.nombre}</td>
                    <td>${tutorNombre}</td>
                    <td>${g.carrera_nombre ?? ''}</td>
                    <td>${g.modalidad_nombre ?? ''}</td>
                    <td>
                        <button class="btn btn-warning btn-sm btn-editar" 
                            data-id="${g.id_grupo}" 
                            data-nombre="${g.nombre}" 
                            data-tutor="${g.usuarios_id_usuario_tutor ?? ''}" 
                            data-carrera="${g.carreras_id_carrera ?? ''}" 
                            data-modalidad="${g.modalidades_id_modalidad ?? ''}" 
                            data-bs-toggle="tooltip" 
                            data-bs-placement="top" 
                            title="Editar Grupo">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-danger btn-sm btn-eliminar" 
                            data-id="${g.id_grupo}" 
                            data-bs-toggle="tooltip" 
                            data-bs-placement="top" 
                            title="Eliminar Grupo">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </td>
                </tr>`;
            gruposBody.insertAdjacentHTML('beforeend', row);
        });

        if (typeof bootstrap !== 'undefined') {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        }
    };

    const showError = (message) => {
        gruposBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">${message}</td></tr>`;
        Swal.fire({ icon: 'error', title: 'Error', text: message });
    };

    // REEMPLAZO DE dom.fetchJSON
    const cargarGrupos = async (page = 1, search = '') => {
        if (isLoading) return;
        isLoading = true;
        currentPage = page;
        searchTerm = search;
        renderSpinner();

        const params = new URLSearchParams({ action: 'paginated', page, limit: itemsPerPage, search });
        
        try {
            const response = await fetch(`/GORA/controllers/gruposController.php?${params}`);
            
            if(!response.ok) throw new Error('Error en la respuesta del servidor');

            const data = await response.json();
            
            if (data.success) {
                totalItems = data.total;
                totalPages = data.totalPages;
                currentPage = data.currentPage;
                renderGrupos(data.grupos);
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

    document.getElementById('btnNuevoGrupo')?.addEventListener('click', () => {
        form?.reset();
        const idField = document.getElementById('id_grupo');
        if (idField) idField.value = '';
        if (modalLabel) modalLabel.textContent = 'Agregar Grupo';
        grupoModal?.show();
    });

    document.getElementById('btnSearch')?.addEventListener('click', () => {
        const search = searchInput?.value.trim() || '';
        cargarGrupos(1, search);
    });

    searchInput?.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            const search = searchInput.value.trim();
            cargarGrupos(1, search);
        }
    });

    document.getElementById('btnClear')?.addEventListener('click', () => {
        if (searchInput) searchInput.value = '';
        cargarGrupos(1, '');
    });

    itemsPerPageSelect?.addEventListener('change', () => {
        itemsPerPage = parseInt(itemsPerPageSelect.value, 10) || 10;
        cargarGrupos(1, searchTerm);
    });

    paginationControls?.addEventListener('click', (e) => {
        const link = e.target.closest('.page-link');
        if (!link) return;
        e.preventDefault();
        const page = parseInt(link.dataset.page, 10);
        if (!page || page === currentPage || page < 1 || page > totalPages) return;
        cargarGrupos(page, searchTerm);
    });

    document.addEventListener('click', (e) => {
        const editBtn = e.target.closest('.btn-editar');
        if (editBtn) {
            document.getElementById('id_grupo').value = editBtn.dataset.id;
            document.getElementById('nombre').value = editBtn.dataset.nombre || '';
            document.getElementById('usuarios_id_usuario_tutor').value = editBtn.dataset.tutor || '';
            document.getElementById('carreras_id_carrera').value = editBtn.dataset.carrera || '';
            document.getElementById('modalidades_id_modalidad').value = editBtn.dataset.modalidad || '';
            
            if (modalLabel) modalLabel.textContent = 'Editar Grupo';
            grupoModal?.show();
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

                        const response = await fetch('/GORA/controllers/gruposController.php?action=delete', {
                            method: 'POST',
                            body: formData
                        });

                        const data = await response.json();

                        if(data.success || data === true) {
                            Swal.fire('¡Eliminado!', 'El grupo ha sido eliminado.', 'success');
                            cargarGrupos(currentPage, searchTerm);
                        } else {
                            throw new Error(data.message || 'Error al eliminar');
                        }
                    } catch (error) {
                        Swal.fire('Error', 'No se pudo eliminar el grupo.', 'error');
                    }
                }
            });
        }
    });

    // REEMPLAZO DE dom.postForm para guardar
    document.getElementById('btnGuardar')?.addEventListener('click', async () => {
        const idField = document.getElementById('id_grupo');
        const isUpdate = idField && idField.value;
        const url = isUpdate ? '/GORA/controllers/gruposController.php?action=update' : '/GORA/controllers/gruposController.php?action=store';
        
        const formData = new FormData(form);

        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (response.ok) {
                grupoModal?.hide();
                cargarGrupos(currentPage, searchTerm);
                Swal.fire({ icon: 'success', title: '¡Guardado!', text: 'El grupo ha sido guardado correctamente.', timer: 1500, showConfirmButton: false });
            } else {
                throw new Error(data.message || 'Error del servidor');
            }
        } catch (error) {
            Swal.fire({ icon: 'error', title: 'Oops...', text: 'Error al guardar el grupo. Revise los datos e intente de nuevo.' });
        }
    });

    grupoModalEl?.addEventListener('hidden.bs.modal', () => {
        form?.reset();
    });

    cargarGrupos();
});
</script>