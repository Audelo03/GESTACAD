<?php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php";

$auth = new AuthController($conn);
$auth->checkAuth();

$coordinadores = $conn->query("SELECT id_usuario, CONCAT(nombre, ' ', apellido_paterno) as nombre_completo FROM usuarios WHERE niveles_usuarios_id_nivel_usuario = 2 ORDER BY nombre_completo")->fetchAll(PDO::FETCH_ASSOC); 
$modificacion_ruta= "../";
$page_title = "Carreras";
include __DIR__ . "/../objects/header.php";
?>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col-md-6">
            <button class="btn btn-success" id="btnNuevaCarrera">
                <i class="bi bi-plus-circle"></i> Agregar Carrera
            </button>
        </div>
        <div class="col-md-6">
            <div class="input-group">
                <input type="text" class="form-control" id="searchInput" placeholder="Buscar carreras...">
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
        <table class="table table-bordered table-striped" id="tablaCarreras">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Coordinador</th>
                    <th>Fecha De Creaci√≥n</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="carrerasBody"></tbody>
        </table>
    </div>

    <!-- Controles de paginaci√≥n -->
    <nav aria-label="Paginaci√≥n de carreras" class="mt-3">
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
                    <!-- Los controles se generar√°n din√°micamente -->
                </ul>
            </div>
        </div>
    </nav>
</div>

<div class="modal fade" id="carreraModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Formulario de Carrera</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formCarrera">
                    <input type="hidden" id="id_carrera" name="id">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre de la Carrera</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="coordinador_id" class="form-label">Coordinador</label>
                        <select id="coordinador_id" name="usuario_id" class="form-select" required>
                            <option value="">Seleccione un coordinador</option>
                            <?php foreach ($coordinadores as $coordinador): ?>
                                <option value="<?= $coordinador['id_usuario'] ?>"><?= htmlspecialchars($coordinador['nombre_completo']) ?></option>
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

<?php include __DIR__ . "/../objects/footer.php";?>
<script>
window.addEventListener('load', function() {
    const carreraModalEl = document.getElementById('carreraModal');
    const carreraModal = carreraModalEl ? new bootstrap.Modal(carreraModalEl) : null;
    const form = document.getElementById('formCarrera');
    const carrerasBody = document.getElementById('carrerasBody');
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
        dom.setHTML(carrerasBody, '<tr><td colspan="5" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></td></tr>');
    };

    const updatePaginationInfo = () => {
        const start = ((currentPage - 1) * itemsPerPage) + 1;
        const end = Math.min(currentPage * itemsPerPage, totalItems);
        if (paginationInfo) {
            paginationInfo.textContent = `Mostrando ${start}-${end} de ${totalItems} registros`;
        }
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
            link.textContent = label;
            li.appendChild(link);
            paginationControls.appendChild(li);
        };

        addItem(currentPage - 1, '´ Anterior', currentPage === 1);

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

        addItem(currentPage + 1, 'Siguiente ª', currentPage === totalPages);
    };

    const renderCarreras = (carreras) => {
        if (!carrerasBody) return;
        if (!carreras.length) {
            dom.setHTML(carrerasBody, '<tr><td colspan="5" class="text-center text-muted">No se encontraron carreras</td></tr>');
            return;
        }

        carrerasBody.innerHTML = '';
        carreras.forEach((c) => {
            const coordinadorNombre = c.coordinador_nombre ? `${c.coordinador_nombre} ${c.coordinador_apellido_paterno || ''}`.trim() : 'N/A';
            const row = `
                <tr>
                    <td>${c.id_carrera}</td>
                    <td>${c.nombre}</td>
                    <td>${coordinadorNombre}</td>
                    <td>${c.fecha_creacion}</td>
                    <td>
                        <button class="btn btn-warning btn-sm btn-editar" data-id="${c.id_carrera}" data-nombre="${c.nombre}" data-coordinador="${c.coordinador_id ?? ''}" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar Carrera">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-danger btn-sm btn-eliminar" data-id="${c.id_carrera}" data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar Carrera">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </td>
                </tr>`;
            dom.appendHTML(carrerasBody, row);
        });

        if (typeof initTooltips === 'function') {
            initTooltips();
        }
    };

    const showError = (message) => {
        dom.setHTML(carrerasBody, `<tr><td colspan="5" class="text-center text-danger">${message}</td></tr>`);
        Swal.fire({ icon: 'error', title: 'Error', text: message });
    };

    const cargarCarreras = async (page = 1, search = '') => {
        if (isLoading) return;
        isLoading = true;
        currentPage = page;
        searchTerm = search;
        renderSpinner();

        const params = new URLSearchParams({ action: 'paginated', page, limit: itemsPerPage, search });

        try {
            const data = await dom.fetchJSON(`/GESTACAD/controllers/carrerasController.php?${params}`);
            if (data.success) {
                totalItems = data.total;
                totalPages = data.totalPages;
                currentPage = data.currentPage;
                renderCarreras(data.carreras);
                updatePaginationInfo();
                renderPaginationControls();
            } else {
                showError('Error al cargar los datos: ' + (data.message || 'Error desconocido'));
            }
        } catch (error) {
            showError('Error de conexiÛn: ' + error.message);
        } finally {
            isLoading = false;
        }
    };

    document.getElementById('btnNuevaCarrera')?.addEventListener('click', () => {
        form?.reset();
        const idField = document.getElementById('id_carrera');
        if (idField) idField.value = '';
        if (modalLabel) modalLabel.textContent = 'Agregar Carrera';
        carreraModal?.show();
    });

    document.getElementById('btnSearch')?.addEventListener('click', () => {
        const search = searchInput?.value.trim() || '';
        cargarCarreras(1, search);
    });

    searchInput?.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            const search = searchInput.value.trim();
            cargarCarreras(1, search);
        }
    });

    document.getElementById('btnClear')?.addEventListener('click', () => {
        if (searchInput) searchInput.value = '';
        cargarCarreras(1, '');
    });

    itemsPerPageSelect?.addEventListener('change', () => {
        itemsPerPage = parseInt(itemsPerPageSelect.value, 10) || 10;
        cargarCarreras(1, searchTerm);
    });

    paginationControls?.addEventListener('click', (e) => {
        const link = e.target.closest('.page-link');
        if (!link) return;
        e.preventDefault();
        const page = parseInt(link.dataset.page, 10);
        if (!page || page === currentPage || page < 1 || page > totalPages) return;
        cargarCarreras(page, searchTerm);
    });

    document.addEventListener('click', (e) => {
        const editBtn = e.target.closest('.btn-editar');
        if (editBtn) {
            const id = editBtn.dataset.id;
            const nombre = editBtn.dataset.nombre || '';
            const coordinador = editBtn.dataset.coordinador || '';
            document.getElementById('id_carrera').value = id;
            document.getElementById('nombre').value = nombre;
            document.getElementById('coordinador_id').value = coordinador;
            if (modalLabel) modalLabel.textContent = 'Editar Carrera';
            carreraModal?.show();
            return;
        }

        const deleteBtn = e.target.closest('.btn-eliminar');
        if (deleteBtn) {
            const id = deleteBtn.dataset.id;
            Swal.fire({
                title: 'øEst·s seguro?',
                text: '°No podr·s revertir esta acciÛn!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'SÌ, °elimÌnalo!',
                cancelButtonText: 'Cancelar'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        await dom.postForm('/GESTACAD/controllers/carrerasController.php?action=delete', { id });
                        Swal.fire('°Eliminado!', 'La carrera ha sido eliminada.', 'success');
                        cargarCarreras(currentPage, searchTerm);
                    } catch (error) {
                        Swal.fire('Error', 'No se pudo eliminar la carrera.', 'error');
                    }
                }
            });
        }
    });

    document.getElementById('btnGuardar')?.addEventListener('click', async () => {
        const idField = document.getElementById('id_carrera');
        const isUpdate = idField && idField.value;
        const url = isUpdate ? '/GESTACAD/controllers/carrerasController.php?action=update' : '/GESTACAD/controllers/carrerasController.php?action=store';
        const formData = dom.serializeForm(form);
        try {
            await dom.postForm(url, formData);
            carreraModal?.hide();
            cargarCarreras(currentPage, searchTerm);
            Swal.fire({ icon: 'success', title: '°Guardado!', text: 'La carrera ha sido guardada correctamente.', timer: 1500, showConfirmButton: false });
        } catch (error) {
            Swal.fire({ icon: 'error', title: 'Oops...', text: 'Error al guardar la carrera. Revise los datos e intente de nuevo.' });
        }
    });

    carreraModalEl?.addEventListener('hidden.bs.modal', () => {
        form?.reset();
        const idField = document.getElementById('id_carrera');
        if (idField) idField.value = '';
    });

    cargarCarreras();
});
</script>
