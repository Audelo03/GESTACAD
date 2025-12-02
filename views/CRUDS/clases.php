<?php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php";

$auth = new AuthController($conn);
$auth->checkAuth();
$page_title = "Gestión de Clases";
include __DIR__ . "/../objects/header.php";
?>

<div class="container mt-3 mt-md-4">
    <!-- Header -->
    <div class="card shadow-sm mb-3 mb-md-4 crud-header-card">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-3">
                <div>
                    <h1 class="h4 h3-md mb-1 mb-md-2">
                        <i class="bi bi-book me-2 text-primary"></i>
                        Gestión de Clases
                    </h1>
                    <p class="text-muted small mb-0 d-none d-md-block">Administra las clases académicas</p>
                </div>
                <div class="d-flex flex-column flex-md-row gap-2 w-100 w-md-auto">
                    <button class="btn btn-primary btn-lg w-100 w-md-auto" id="btnNuevaClase">
                        <i class="bi bi-plus-circle me-2"></i>
                        <span class="d-none d-sm-inline">Agregar Clase</span>
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
                <table class="table table-hover mb-0 crud-table" id="tablaClases">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 80px;">ID</th>
                            <th>Asignatura</th>
                            <th>Periodo</th>
                            <th>Docente</th>
                            <th>Sección</th>
                            <th>Modalidad</th>
                            <th>Aula</th>
                            <th class="text-center" style="width: 150px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="clasesBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Cards Móvil -->
    <div class="d-md-none" id="clasesCardsContainer">
        <div id="clasesCardsBody"></div>
    </div>
</div>

<div class="modal fade" id="claseModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="modalLabel">
                    <i class="bi bi-book me-2"></i>Formulario de Clase
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 p-md-4">
                <form id="formClase">
                    <input type="hidden" id="id" name="id">
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label for="asignatura_id" class="form-label fw-semibold">
                                <i class="bi bi-journal-bookmark me-1 text-primary"></i>Asignatura
                            </label>
                            <select id="asignatura_id" name="asignatura_id" class="form-select" required></select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="periodo_id" class="form-label fw-semibold">
                                <i class="bi bi-calendar-range me-1 text-primary"></i>Periodo
                            </label>
                            <select id="periodo_id" name="periodo_id" class="form-select" required></select>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label for="docente_usuario_id" class="form-label fw-semibold">
                                <i class="bi bi-person-badge me-1 text-primary"></i>Docente
                            </label>
                            <select id="docente_usuario_id" name="docente_usuario_id" class="form-select" required></select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="modalidad_id" class="form-label fw-semibold">
                                <i class="bi bi-person-video3 me-1 text-primary"></i>Modalidad
                            </label>
                            <select id="modalidad_id" name="modalidad_id" class="form-select" required></select>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-4">
                            <label for="seccion" class="form-label fw-semibold">
                                <i class="bi bi-hash me-1 text-primary"></i>Sección
                            </label>
                            <input type="text" id="seccion" name="seccion" class="form-control" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="cupo" class="form-label fw-semibold">
                                <i class="bi bi-people me-1 text-primary"></i>Cupo
                            </label>
                            <input type="number" id="cupo" name="cupo" class="form-control">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="aula" class="form-label fw-semibold">
                                <i class="bi bi-building me-1 text-primary"></i>Aula
                            </label>
                            <input type="text" id="aula" name="aula" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="grupo_referencia" class="form-label fw-semibold">
                            <i class="bi bi-people me-1 text-primary"></i>Grupo Referencia
                        </label>
                        <select id="grupo_referencia" name="grupo_referencia" class="form-select"></select>
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

<?php include __DIR__ . "/../objects/footer.php"; ?>

<script>
    window.addEventListener('load', function () {
        const modalEl = document.getElementById('claseModal');
        const modal = new bootstrap.Modal(modalEl);
        const form = document.getElementById('formClase');
        const tbody = document.getElementById('clasesBody');
        const btnGuardar = document.getElementById('btnGuardar');
        const modalLabel = document.getElementById('modalLabel');

        // Selects
        const asignaturaSelect = document.getElementById('asignatura_id');
        const periodoSelect = document.getElementById('periodo_id');
        const docenteSelect = document.getElementById('docente_usuario_id');
        const modalidadSelect = document.getElementById('modalidad_id');
        const grupoSelect = document.getElementById('grupo_referencia');

        const loadOptions = async () => {
            try {
                // Asignaturas
                const resAsig = await fetch('/GESTACAD/controllers/asignaturasController.php?action=index');
                const dataAsig = await resAsig.json();
                asignaturaSelect.innerHTML = '<option value="">Seleccione...</option>' + dataAsig.map(i => `<option value="${i.id}">${i.clave} - ${i.nombre}</option>`).join('');

                // Periodos
                const resPer = await fetch('/GESTACAD/controllers/periodosController.php?action=index');
                const dataPer = await resPer.json();
                periodoSelect.innerHTML = '<option value="">Seleccione...</option>' + dataPer.map(i => `<option value="${i.id}">${i.nombre}</option>`).join('');

                // Docentes (Usuarios) - Assuming we can get all users or filter by role later
                const resDoc = await fetch('/GESTACAD/controllers/usuarioController.php?action=listarUsuarios'); // Assuming this exists or similar
                // If listarUsuarios doesn't exist, we might need to use paginated or create a new method. 
                // Let's try to use the paginated one but with high limit or check if there's a simple list method.
                // Actually, let's try a simple fetch to a new endpoint if needed, but for now let's assume we can get a list.
                // If not, I'll fix it. Let's assume 'listarUsuarios' returns JSON array.
                // Wait, usuarioController had 'index' which calls 'getAll'.
                const resDoc2 = await fetch('/GESTACAD/controllers/usuarioController.php?action=index');
                // Wait, usuarioController index returns paginated structure? No, let's check.
                // usuarioController.php: index() -> echo json_encode($this->usuario->getAll());
                // So it returns array.
                const dataDoc = await resDoc2.json();
                // Filter for teachers if possible? Or just show all. Let's show all for now.
                docenteSelect.innerHTML = '<option value="">Seleccione...</option>' + dataDoc.map(i => `<option value="${i.id_usuario}">${i.nombre} ${i.apellido_paterno}</option>`).join('');

                // Modalidades
                const resMod = await fetch('/GESTACAD/controllers/modalidadesController.php?action=index');
                const dataMod = await resMod.json();
                modalidadSelect.innerHTML = '<option value="">Seleccione...</option>' + dataMod.map(i => `<option value="${i.id_modalidad}">${i.nombre}</option>`).join('');

                // Grupos
                const resGru = await fetch('/GESTACAD/controllers/gruposController.php?action=index');
                const dataGru = await resGru.json();
                grupoSelect.innerHTML = '<option value="">Seleccione...</option>' + dataGru.map(i => `<option value="${i.id_grupo}">${i.nombre}</option>`).join('');

            } catch (error) {
                console.error("Error loading options", error);
            }
        };

        const clasesCardsBody = document.getElementById('clasesCardsBody');
        
        const renderTable = (data) => {
            tbody.innerHTML = '';
            if (clasesCardsBody) clasesCardsBody.innerHTML = '';
            
            if (!data || data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted"><i class="bi bi-inbox me-2"></i>No hay registros</td></tr>';
                if (clasesCardsBody) {
                    clasesCardsBody.innerHTML = '<div class="alert alert-info mb-0"><i class="bi bi-inbox me-2"></i>No hay registros</div>';
                }
                return;
            }
            
            data.forEach(item => {
                // Tabla desktop
                tbody.innerHTML += `
                <tr class="align-middle">
                    <td class="text-center fw-bold text-primary">${item.id}</td>
                    <td><strong>${item.asignatura_clave}</strong><br><small class="text-muted">${item.asignatura_nombre}</small></td>
                    <td>${item.periodo_nombre}</td>
                    <td>${item.docente_nombre} ${item.docente_apellido}</td>
                    <td><span class="badge bg-info">${item.seccion}</span></td>
                    <td>${item.modalidad_nombre}</td>
                    <td>${item.aula || '-'}</td>
                    <td class="text-center">
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-warning btn-editar" 
                                data-id="${item.id}" 
                                data-asignatura="${item.asignatura_id}"
                                data-periodo="${item.periodo_id}"
                                data-docente="${item.docente_usuario_id}"
                                data-seccion="${item.seccion}"
                                data-modalidad="${item.modalidad_id}"
                                data-cupo="${item.cupo}"
                                data-grupo="${item.grupo_referencia}"
                                data-aula="${item.aula}"
                                data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                title="Editar">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-sm btn-danger btn-eliminar" 
                                data-id="${item.id}"
                                data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                title="Eliminar">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </div>
                    </td>
                </tr>`;
                
                // Cards móvil
                if (clasesCardsBody) {
                    clasesCardsBody.innerHTML += `
                    <div class="card shadow-sm mb-3 crud-card-mobile">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-3 gap-2">
                                <div class="flex-grow-1 min-w-0">
                                    <h6 class="mb-1 fw-bold text-truncate">${item.asignatura_clave} - ${item.asignatura_nombre}</h6>
                                    <small class="text-muted">ID: ${item.id}</small>
                                </div>
                                <div class="text-end flex-shrink-0 ms-2">
                                    <span class="badge bg-info">${item.seccion}</span>
                                </div>
                            </div>
                            <div class="border-top pt-2">
                                <div class="row g-2 small">
                                    <div class="col-12"><strong>Periodo:</strong> ${item.periodo_nombre}</div>
                                    <div class="col-12"><strong>Docente:</strong> ${item.docente_nombre} ${item.docente_apellido}</div>
                                    <div class="col-12"><strong>Modalidad:</strong> ${item.modalidad_nombre}</div>
                                    <div class="col-12"><strong>Aula:</strong> ${item.aula || '-'}</div>
                                </div>
                            </div>
                            <div class="d-grid gap-2 mt-3">
                                <button class="btn btn-warning btn-editar w-100" 
                                    data-id="${item.id}" 
                                    data-asignatura="${item.asignatura_id}"
                                    data-periodo="${item.periodo_id}"
                                    data-docente="${item.docente_usuario_id}"
                                    data-seccion="${item.seccion}"
                                    data-modalidad="${item.modalidad_id}"
                                    data-cupo="${item.cupo}"
                                    data-grupo="${item.grupo_referencia}"
                                    data-aula="${item.aula}">
                                    <i class="bi bi-pencil-square me-2"></i>Editar
                                </button>
                                <button class="btn btn-danger btn-eliminar w-100" data-id="${item.id}">
                                    <i class="bi bi-trash-fill me-2"></i>Eliminar
                                </button>
                            </div>
                        </div>
                    </div>`;
                }
            });
            
            // Inicializar tooltips
            if (typeof bootstrap !== 'undefined') {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }
        };

        const loadData = async () => {
            try {
                const res = await fetch('/GESTACAD/controllers/clasesController.php?action=index');
                const data = await res.json();
                renderTable(data);
            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'No se pudieron cargar los datos', 'error');
            }
        };

        document.getElementById('btnNuevaClase').addEventListener('click', () => {
            form.reset();
            document.getElementById('id').value = '';
            modalLabel.textContent = 'Agregar Clase';
            modal.show();
        });

        document.addEventListener('click', async (e) => {
            if (e.target.closest('.btn-editar')) {
                const btn = e.target.closest('.btn-editar');
                document.getElementById('id').value = btn.dataset.id;
                asignaturaSelect.value = btn.dataset.asignatura;
                periodoSelect.value = btn.dataset.periodo;
                docenteSelect.value = btn.dataset.docente;
                document.getElementById('seccion').value = btn.dataset.seccion;
                modalidadSelect.value = btn.dataset.modalidad;
                document.getElementById('cupo').value = btn.dataset.cupo;
                grupoSelect.value = btn.dataset.grupo;
                document.getElementById('aula').value = btn.dataset.aula;
                modalLabel.textContent = 'Editar Clase';
                modal.show();
            }

            if (e.target.closest('.btn-eliminar')) {
                const btn = e.target.closest('.btn-eliminar');
                const result = await Swal.fire({
                    title: '¿Eliminar?',
                    text: "No podrás revertir esto",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar'
                });

                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('id', btn.dataset.id);
                    const res = await fetch('/GESTACAD/controllers/clasesController.php?action=delete', { method: 'POST', body: formData });
                    const data = await res.json();
                    if (data.status === 'success') {
                        Swal.fire('Eliminado', data.message, 'success');
                        loadData();
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                }
            }
        });

        btnGuardar.addEventListener('click', async () => {
            const formData = new FormData(form);
            const id = document.getElementById('id').value;
            const action = id ? 'update' : 'store';

            try {
                const res = await fetch(`/GESTACAD/controllers/clasesController.php?action=${action}`, { method: 'POST', body: formData });
                const data = await res.json();
                if (data.status === 'ok') {
                    modal.hide();
                    Swal.fire('Éxito', 'Operación realizada correctamente', 'success');
                    loadData();
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'Error de conexión', 'error');
            }
        });

        loadOptions();
        loadData();
    });
</script>