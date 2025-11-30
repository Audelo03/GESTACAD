<?php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php";

// Verificación de sesión
$auth = new AuthController($conn);
$auth->checkAuth();

// Obtener listas para los selects
$carreras = $conn->query("SELECT id_carrera, nombre FROM carreras ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$grupos = $conn->query("SELECT id_grupo, nombre FROM grupos ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

$modificacion_ruta = "../";
$page_title = "Alumnos";
include __DIR__ . "/../objects/header.php"; // Se agregó el punto y coma faltante aquí
?>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col-md-6">
            <button class="btn btn-success" id="btnNuevoAlumno">
                <i class="bi bi-plus-circle"></i> Agregar Alumno
            </button>
        </div>
        <div class="col-md-6">
            <div class="input-group">
                <input type="text" class="form-control" id="searchInput" placeholder="Buscar alumnos...">
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
        <table id="alumnosTable" class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Matrícula</th>
                    <th>Nombre Completo</th>
                    <th>Carrera</th>
                    <th>Grupo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="alumnosBody"></tbody>
        </table>
    </div>

    <nav aria-label="Paginación de alumnos" class="mt-3">
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

<div class="modal fade" id="alumnoModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Formulario de Alumno</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formAlumno">
                    <input type="hidden" id="id_alumno" name="id_alumno">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="matricula" class="form-label">Matrícula</label>
                            <input type="text" id="matricula" name="matricula" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="nombre" class="form-label">Nombre(s)</label>
                            <input type="text" id="nombre" name="nombre" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="apellido_paterno" class="form-label">Apellido Paterno</label>
                            <input type="text" id="apellido_paterno" name="apellido_paterno" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="apellido_materno" class="form-label">Apellido Materno</label>
                            <input type="text" id="apellido_materno" name="apellido_materno" class="form-control">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="carreras_id_carrera" class="form-label">Carrera</label>
                            <select id="carreras_id_carrera" name="carreras_id_carrera" class="form-select" required>
                                <option value="">Seleccione una carrera</option>
                                <?php foreach ($carreras as $carrera): ?>
                                    <option value="<?= $carrera['id_carrera'] ?>"><?= htmlspecialchars($carrera['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="grupos_id_grupo" class="form-label">Grupo</label>
                            <select id="grupos_id_grupo" name="grupos_id_grupo" class="form-select" required>
                                <option value="">Seleccione un grupo</option>
                                <?php foreach ($grupos as $grupo): ?>
                                    <option value="<?= $grupo['id_grupo'] ?>"><?= htmlspecialchars($grupo['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardar">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>


<?php include __DIR__ . "/../objects/footer.php"; ?>

<script>
window.addEventListener('load', function() {
    const alumnoModalEl = document.getElementById('alumnoModal');
    const alumnoModal = alumnoModalEl ? new bootstrap.Modal(alumnoModalEl) : null;
    const form = document.getElementById('formAlumno');
    const alumnosBody = document.getElementById('alumnosBody');
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

    // Renderizado estándar sin objeto 'dom'
    const renderSpinner = () => {
        alumnosBody.innerHTML = '<tr><td colspan="6" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></td></tr>';
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
            link.innerHTML = label; // Usamos innerHTML para permitir entidades HTML
            li.appendChild(link);
            paginationControls.appendChild(li);
        };

        // Entidades HTML corregidas para flechas
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

    const renderAlumnos = (alumnos) => {
        if (!alumnosBody) return;
        alumnosBody.innerHTML = ''; // Limpiar contenido previo

        if (!alumnos.length) {
            alumnosBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No se encontraron alumnos</td></tr>';
            return;
        }

        alumnos.forEach((a) => {
            const nombreCompleto = `${a.nombre} ${a.apellido_paterno} ${a.apellido_materno ?? ''}`.trim();
            const row = `
                <tr>
                    <td>${a.id_alumno}</td>
                    <td>${a.matricula}</td>
                    <td>${nombreCompleto}</td>
                    <td>${a.carrera_nombre ?? ''}</td>
                    <td>${a.grupo_nombre ?? ''}</td>
                    <td>
                        <button class="btn btn-warning btn-sm btn-editar" data-id="${a.id_alumno}" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar Alumno">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-danger btn-sm btn-eliminar" data-id="${a.id_alumno}" data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar Alumno">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </td>
                </tr>`;
            alumnosBody.insertAdjacentHTML('beforeend', row);
        });

        if (typeof bootstrap !== 'undefined') {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        }
    };

    const showError = (message) => {
        alumnosBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">${message}</td></tr>`;
        Swal.fire({ icon: 'error', title: 'Error', text: message });
    };

    // Fetch estándar en lugar de dom.fetchJSON
    const cargarAlumnos = async (page = 1, search = '') => {
        if (isLoading) return;
        isLoading = true;
        currentPage = page;
        searchTerm = search;
        renderSpinner();

        const params = new URLSearchParams({
            action: 'paginated',
            page,
            limit: itemsPerPage,
            search
        });

        try {
            const response = await fetch(`/GESTACAD/controllers/alumnoController.php?${params}`);
            
            if(!response.ok) throw new Error('Error en la respuesta del servidor');
            
            const data = await response.json();
            
            if (data.success) {
                totalItems = data.total;
                totalPages = data.totalPages;
                currentPage = data.currentPage;
                renderAlumnos(data.alumnos);
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

    document.getElementById('btnNuevoAlumno')?.addEventListener('click', () => {
        form?.reset();
        const idField = document.getElementById('id_alumno');
        if (idField) idField.value = '';
        if (modalLabel) modalLabel.textContent = 'Agregar Alumno';
        alumnoModal?.show();
    });

    document.getElementById('btnSearch')?.addEventListener('click', () => {
        const search = searchInput?.value.trim() || '';
        cargarAlumnos(1, search);
    });

    searchInput?.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            const search = searchInput.value.trim();
            cargarAlumnos(1, search);
        }
    });

    document.getElementById('btnClear')?.addEventListener('click', () => {
        if (searchInput) searchInput.value = '';
        cargarAlumnos(1, '');
    });

    itemsPerPageSelect?.addEventListener('change', () => {
        itemsPerPage = parseInt(itemsPerPageSelect.value, 10) || 10;
        cargarAlumnos(1, searchTerm);
    });

    paginationControls?.addEventListener('click', (e) => {
        const link = e.target.closest('.page-link');
        if (!link) return;
        e.preventDefault();
        const page = parseInt(link.dataset.page, 10);
        if (!page || page === currentPage || page < 1 || page > totalPages) return;
        cargarAlumnos(page, searchTerm);
    });

    document.addEventListener('click', async (e) => {
        // Logica para Editar
        const editBtn = e.target.closest('.btn-editar');
        if (editBtn) {
            const id = editBtn.dataset.id;
            try {
                const response = await fetch(`/GESTACAD/controllers/alumnoController.php?action=show&id=${id}`);
                const alumno = await response.json();
                
                if(!alumno) throw new Error("No se recibieron datos");

                document.getElementById('id_alumno').value = alumno.id_alumno;
                document.getElementById('matricula').value = alumno.matricula;
                document.getElementById('nombre').value = alumno.nombre;
                document.getElementById('apellido_paterno').value = alumno.apellido_paterno;
                document.getElementById('apellido_materno').value = alumno.apellido_materno ?? '';
                document.getElementById('carreras_id_carrera').value = alumno.carreras_id_carrera;
                document.getElementById('grupos_id_grupo').value = alumno.grupos_id_grupo;
                
                if (modalLabel) modalLabel.textContent = 'Editar Alumno';
                alumnoModal?.show();
            } catch (error) {
                console.error(error);
                Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo obtener la información del alumno.' });
            }
            return;
        }

        // Lógica para Eliminar
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

                        const response = await fetch(`/GESTACAD/controllers/alumnoController.php?action=delete`, {
                            method: 'POST',
                            body: formData
                        });
                        const data = await response.json();

                        if (data.success || data === true) {
                            Swal.fire('¡Eliminado!', 'El alumno ha sido eliminado.', 'success');
                            cargarAlumnos(currentPage, searchTerm);
                        } else {
                            throw new Error(data.message || 'Error desconocido');
                        }
                    } catch (error) {
                        Swal.fire('Error', 'No se pudo eliminar el alumno.', 'error');
                    }
                }
            });
        }
    });

    // Guardar (Crear o Actualizar)
    document.getElementById('btnGuardar')?.addEventListener('click', async () => {
        const idField = document.getElementById('id_alumno');
        const isUpdate = idField && idField.value;
        const url = isUpdate ? `/GESTACAD/controllers/alumnoController.php?action=update` : `/GESTACAD/controllers/alumnoController.php?action=store`;
        
        const formData = new FormData(form);

        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();

            if(response.ok) {
                alumnoModal?.hide();
                cargarAlumnos(currentPage, searchTerm);
                Swal.fire({
                    icon: 'success',
                    title: '¡Guardado!',
                    text: 'El alumno ha sido guardado correctamente.',
                    timer: 1500,
                    showConfirmButton: false
                });
            } else {
                 throw new Error(data.message || 'Error del servidor');
            }
        } catch (error) {
            Swal.fire({ icon: 'error', title: 'Oops...', text: 'Error al guardar el alumno. Revise los datos e intente de nuevo.' });
        }
    });

    alumnoModalEl?.addEventListener('hidden.bs.modal', () => {
        form?.reset();
    });

    cargarAlumnos();
});
</script>