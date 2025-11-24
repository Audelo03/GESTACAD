<?php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php";

$auth = new AuthController($conn);
$auth->checkAuth();
$page_title = "Usuarios";
include __DIR__ . "/../objects/header.php";

?>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col-md-6">
            <button class="btn btn-success" id="btnNuevoUsuario">
                <i class="bi bi-plus-circle"></i> Agregar Usuario
            </button>
        </div>
        <div class="col-md-6">
            <div class="input-group">
                <input type="text" class="form-control" id="searchInput" placeholder="Buscar usuarios...">
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
        <table class="table table-bordered table-striped table-hover" id="tablaUsuarios">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre Completo</th>
                    <th>Email</th>
                    <th>Nivel</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="usuariosBody"></tbody>
        </table>
    </div>

    <nav aria-label="Paginación de usuarios" class="mt-3">
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

<div class="modal fade" id="usuarioModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Formulario de Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                 <form id="formUsuario">
                    <input type="hidden" id="id_usuario" name="id_usuario">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="nombre" class="form-label">Nombre(s)</label>
                            <input type="text" id="nombre" name="nombre" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="apellido_paterno" class="form-label">Apellido Paterno</label>
                            <input type="text" id="apellido_paterno" name="apellido_paterno" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="apellido_materno" class="form-label">Apellido Materno</label>
                            <input type="text" id="apellido_materno" name="apellido_materno" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Dejar en blanco para no cambiar">
                        <small class="form-text text-muted">La contraseña es requerida para nuevos usuarios.</small>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="niveles_usuarios_id_nivel_usuario" class="form-label">Nivel de Usuario</label>
                            <select id="niveles_usuarios_id_nivel_usuario" name="niveles_usuarios_id_nivel_usuario" class="form-select">
                                <option value="">Seleccione un nivel</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarUsuario">Guardar</button>
            </div>
        </div>
    </div>
</div>

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
        usuariosBody.innerHTML = '<tr><td colspan="5" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></td></tr>';
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
        
        // Limpiamos contenido
        usuariosBody.innerHTML = '';

        // Validación para evitar error de .length en undefined
        if (!usuarios || !Array.isArray(usuarios) || usuarios.length === 0) {
            usuariosBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No se encontraron usuarios</td></tr>';
            return;
        }

        // Reemplazo de dom.appendHTML
        usuarios.forEach((u) => {
            const nombreCompleto = `${u.nombre} ${u.apellido_paterno} ${u.apellido_materno ?? ''}`.trim();
            const row = `
                <tr>
                    <td>${u.id_usuario}</td>
                    <td>${nombreCompleto}</td>
                    <td>${u.email}</td>
                    <td>${u.nivel_nombre ?? ''}</td>
                    <td>
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
                    </td>
                </tr>`;
            usuariosBody.insertAdjacentHTML('beforeend', row);
        });

        // Inicializar tooltips
        if (typeof bootstrap !== 'undefined') {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        }
    };

    const showError = (message) => {
        usuariosBody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">${message}</td></tr>`;
        Swal.fire({ icon: 'error', title: 'Error', text: message });
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
            const response = await fetch(`/GORA/controllers/usuarioController.php?${params}`);
            
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
            const response = await fetch('/GORA/controllers/usuarioController.php?action=getLevels');
            
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

    document.getElementById('btnNuevoUsuario')?.addEventListener('click', () => {
        resetForm(true);
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

    document.addEventListener('click', (e) => {
        const editBtn = e.target.closest('.btn-editar');
        if (editBtn) {
            resetForm(false);
            document.getElementById('id_usuario').value = editBtn.dataset.id;
            document.getElementById('nombre').value = editBtn.dataset.nombre || '';
            document.getElementById('apellido_paterno').value = editBtn.dataset.apellidoPaterno || ''
            document.getElementById('apellido_materno').value = editBtn.dataset.apellidoMaterno || ''
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

                        const response = await fetch('/GORA/controllers/usuarioController.php?action=delete', {
                            method: 'POST',
                            body: formData
                        });

                        const data = await response.json();

                        if (data.success || data === true) {
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

        const url = isUpdate ? '/GORA/controllers/usuarioController.php?action=update' : '/GORA/controllers/usuarioController.php?action=store';
        
        const formData = new FormData(form);

        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            
            // Verificamos success (ajusta según tu API si devuelve "status" o "success")
            if (response.ok && data.success) {
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