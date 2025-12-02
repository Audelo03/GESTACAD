<?php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php";

$auth = new AuthController($conn);
$auth->checkAuth();
$page_title = "Inscripciones";
include __DIR__ . "/../objects/header.php";
?>

<div class="container mt-3 mt-md-4">
    <!-- Header -->
    <div class="card shadow-sm mb-3 mb-md-4 crud-header-card">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-3">
                <div>
                    <h1 class="h4 h3-md mb-1 mb-md-2">
                        <i class="bi bi-clipboard-check me-2 text-primary"></i>
                        Gestión de Inscripciones
                    </h1>
                    <p class="text-muted small mb-0 d-none d-md-block">Administra las inscripciones de alumnos a clases</p>
                </div>
                <div class="d-flex flex-column flex-md-row gap-2 w-100 w-md-auto">
                    <button class="btn btn-primary btn-lg w-100 w-md-auto" id="btnNuevaInscripcion">
                        <i class="bi bi-plus-circle me-2"></i>
                        <span class="d-none d-sm-inline">Inscribir Alumno</span>
                        <span class="d-sm-none">Nuevo</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtro -->
    <div class="card shadow-sm mb-3 mb-md-4">
        <div class="card-body p-3 p-md-4">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label for="filtroClase" class="form-label fw-semibold">
                        <i class="bi bi-funnel me-1 text-primary"></i>Filtrar por Clase
                    </label>
                    <select id="filtroClase" class="form-select">
                        <option value="">Todas las clases</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla Desktop -->
    <div class="table-responsive d-none d-md-block">
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover mb-0 crud-table" id="tablaInscripciones">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 80px;">ID</th>
                            <th>Alumno</th>
                            <th>Clase</th>
                            <th class="text-center" style="width: 100px;">Estado</th>
                            <th>Calificaciones</th>
                            <th class="text-center" style="width: 150px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="inscripcionesBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Cards Móvil -->
    <div class="d-md-none" id="inscripcionesCardsContainer">
        <div id="inscripcionesCardsBody"></div>
    </div>
</div>

<div class="modal fade" id="inscripcionModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="modalLabel">
                    <i class="bi bi-clipboard-check me-2"></i>Inscribir Alumno
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 p-md-4">
                <form id="formInscripcion">
                    <div class="mb-3">
                        <label for="clase_id" class="form-label fw-semibold">
                            <i class="bi bi-book me-1 text-primary"></i>Clase
                        </label>
                        <select id="clase_id" name="clase_id" class="form-select" required></select>
                    </div>
                    <div class="mb-3">
                        <label for="alumno_id" class="form-label fw-semibold">
                            <i class="bi bi-person-badge me-1 text-primary"></i>Alumno
                        </label>
                        <select id="alumno_id" name="alumno_id" class="form-select" required></select>
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

<div class="modal fade" id="calificacionesModal" tabindex="-1" aria-labelledby="calModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title fw-bold" id="calModalLabel">
                    <i class="bi bi-journal-check me-2"></i>Calificaciones
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 p-md-4">
                <form id="formCalificaciones">
                    <input type="hidden" id="cal_id" name="id">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-1-circle me-1 text-primary"></i>Parcial 1
                            </label>
                            <input type="number" step="0.01" min="0" max="100" id="cal_parcial1" name="cal_parcial1" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-2-circle me-1 text-primary"></i>Parcial 2
                            </label>
                            <input type="number" step="0.01" min="0" max="100" id="cal_parcial2" name="cal_parcial2" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-3-circle me-1 text-primary"></i>Parcial 3
                            </label>
                            <input type="number" step="0.01" min="0" max="100" id="cal_parcial3" name="cal_parcial3" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-4-circle me-1 text-primary"></i>Parcial 4
                            </label>
                            <input type="number" step="0.01" min="0" max="100" id="cal_parcial4" name="cal_parcial4" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-star me-1 text-primary"></i>Calificación Final
                            </label>
                            <input type="number" step="0.01" min="0" max="100" id="cal_final" name="cal_final" class="form-control">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top p-3 p-md-4">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="btnGuardarCalificaciones">
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
        const modalEl = document.getElementById('inscripcionModal');
        const modal = new bootstrap.Modal(modalEl);
        const calModalEl = document.getElementById('calificacionesModal');
        const calModal = new bootstrap.Modal(calModalEl);

        const form = document.getElementById('formInscripcion');
        const formCal = document.getElementById('formCalificaciones');
        const tbody = document.getElementById('inscripcionesBody');
        const btnGuardar = document.getElementById('btnGuardar');
        const btnGuardarCal = document.getElementById('btnGuardarCalificaciones');

        const claseSelect = document.getElementById('clase_id');
        const alumnoSelect = document.getElementById('alumno_id');
        const filtroClase = document.getElementById('filtroClase');

        const loadOptions = async () => {
            try {
                // Clases
                const resClase = await fetch('/GESTACAD/controllers/clasesController.php?action=index');
                const dataClase = await resClase.json();
                const options = '<option value="">Seleccione...</option>' + dataClase.map(i => `<option value="${i.id}">${i.asignatura_clave} - ${i.seccion}</option>`).join('');
                claseSelect.innerHTML = options;
                filtroClase.innerHTML = '<option value="">Todas las clases</option>' + dataClase.map(i => `<option value="${i.id}">${i.asignatura_clave} - ${i.seccion}</option>`).join('');

                // Alumnos
                const resAlu = await fetch('/GESTACAD/controllers/alumnoController.php?action=listarAlumnos'); // Assuming this exists
                // If not, we might need to use paginated or create a method.
                // Let's assume 'listarAlumnos' exists or 'index' returns list.
                // Checking alumnoController... it has 'index' (paginated usually?) and 'listarAlumnos'.
                // Let's try 'listarAlumnos' or 'index'.
                // Actually, let's assume 'listarAlumnos' returns simple list.
                const resAlu2 = await fetch('/GESTACAD/controllers/alumnoController.php?action=listarAlumnos');
                const dataAlu = await resAlu2.json();
                // Assuming dataAlu is array of students
                alumnoSelect.innerHTML = '<option value="">Seleccione...</option>' + dataAlu.map(i => `<option value="${i.id_alumno}">${i.nombre} ${i.apellido_paterno} (${i.matricula})</option>`).join('');

            } catch (error) {
                console.error("Error loading options", error);
            }
        };

        const inscripcionesCardsBody = document.getElementById('inscripcionesCardsBody');
        
        const renderTable = (data) => {
            tbody.innerHTML = '';
            if (inscripcionesCardsBody) inscripcionesCardsBody.innerHTML = '';
            
            if (!data || data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted"><i class="bi bi-inbox me-2"></i>No hay registros</td></tr>';
                if (inscripcionesCardsBody) {
                    inscripcionesCardsBody.innerHTML = '<div class="alert alert-info mb-0"><i class="bi bi-inbox me-2"></i>No hay registros</div>';
                }
                return;
            }
            
            data.forEach(item => {
                const estadoBadge = item.estado === 'Activo' || item.estado === 'activo' 
                    ? '<span class="badge bg-success">Activo</span>' 
                    : '<span class="badge bg-secondary">' + (item.estado || 'N/A') + '</span>';
                
                // Tabla desktop
                tbody.innerHTML += `
                <tr class="align-middle">
                    <td class="text-center fw-bold text-primary">${item.id}</td>
                    <td><strong>${item.alumno_nombre} ${item.alumno_apellido}</strong></td>
                    <td>${item.asignatura_nombre}<br><small class="text-muted">Sección: ${item.seccion || 'N/A'}</small></td>
                    <td class="text-center">${estadoBadge}</td>
                    <td>
                        <small>
                            <strong>P1:</strong> ${item.cal_parcial1 || '-'} | 
                            <strong>P2:</strong> ${item.cal_parcial2 || '-'}<br>
                            <strong>P3:</strong> ${item.cal_parcial3 || '-'} | 
                            <strong>P4:</strong> ${item.cal_parcial4 || '-'}<br>
                            <strong>Final:</strong> <span class="fw-bold text-primary">${item.cal_final || '-'}</span>
                        </small>
                    </td>
                    <td class="text-center">
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-info btn-calificaciones" 
                                data-id="${item.id}" 
                                data-p1="${item.cal_parcial1 || ''}"
                                data-p2="${item.cal_parcial2 || ''}"
                                data-p3="${item.cal_parcial3 || ''}"
                                data-p4="${item.cal_parcial4 || ''}"
                                data-final="${item.cal_final || ''}"
                                data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                title="Calificaciones">
                                <i class="bi bi-journal-check"></i>
                            </button>
                            <button class="btn btn-sm btn-danger btn-eliminar" 
                                data-id="${item.id}"
                                data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                title="Dar de baja">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </div>
                    </td>
                </tr>`;
                
                // Cards móvil
                if (inscripcionesCardsBody) {
                    inscripcionesCardsBody.innerHTML += `
                    <div class="card shadow-sm mb-3 crud-card-mobile">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-3 gap-2">
                                <div class="flex-grow-1 min-w-0">
                                    <h6 class="mb-1 fw-bold text-truncate">${item.alumno_nombre} ${item.alumno_apellido}</h6>
                                    <small class="text-muted d-block">ID: ${item.id}</small>
                                    <small class="text-muted d-block">${item.asignatura_nombre}</small>
                                </div>
                                <div class="text-end flex-shrink-0 ms-2">
                                    ${estadoBadge}
                                </div>
                            </div>
                            <div class="border-top pt-2 mb-3">
                                <div class="row g-2 small">
                                    <div class="col-6"><strong>P1:</strong> ${item.cal_parcial1 || '-'}</div>
                                    <div class="col-6"><strong>P2:</strong> ${item.cal_parcial2 || '-'}</div>
                                    <div class="col-6"><strong>P3:</strong> ${item.cal_parcial3 || '-'}</div>
                                    <div class="col-6"><strong>P4:</strong> ${item.cal_parcial4 || '-'}</div>
                                    <div class="col-12"><strong>Final:</strong> <span class="fw-bold text-primary">${item.cal_final || '-'}</span></div>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <button class="btn btn-info btn-calificaciones w-100" 
                                    data-id="${item.id}" 
                                    data-p1="${item.cal_parcial1 || ''}"
                                    data-p2="${item.cal_parcial2 || ''}"
                                    data-p3="${item.cal_parcial3 || ''}"
                                    data-p4="${item.cal_parcial4 || ''}"
                                    data-final="${item.cal_final || ''}">
                                    <i class="bi bi-journal-check me-2"></i>Calificaciones
                                </button>
                                <button class="btn btn-danger btn-eliminar w-100" data-id="${item.id}">
                                    <i class="bi bi-trash-fill me-2"></i>Dar de Baja
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
                let url = '/GESTACAD/controllers/inscripcionesController.php?action=index';
                if (filtroClase.value) {
                    url += `&clase_id=${filtroClase.value}`;
                }
                const res = await fetch(url);
                const data = await res.json();
                renderTable(data);
            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'No se pudieron cargar los datos', 'error');
            }
        };

        filtroClase.addEventListener('change', loadData);

        document.getElementById('btnNuevaInscripcion').addEventListener('click', () => {
            form.reset();
            modal.show();
        });

        document.addEventListener('click', async (e) => {
            if (e.target.closest('.btn-calificaciones')) {
                const btn = e.target.closest('.btn-calificaciones');
                document.getElementById('cal_id').value = btn.dataset.id;
                document.getElementById('cal_parcial1').value = btn.dataset.p1;
                document.getElementById('cal_parcial2').value = btn.dataset.p2;
                document.getElementById('cal_parcial3').value = btn.dataset.p3;
                document.getElementById('cal_parcial4').value = btn.dataset.p4;
                document.getElementById('cal_final').value = btn.dataset.final;
                calModal.show();
            }

            if (e.target.closest('.btn-eliminar')) {
                const btn = e.target.closest('.btn-eliminar');
                const result = await Swal.fire({
                    title: '¿Dar de baja?',
                    text: "El alumno será dado de baja de la clase",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, dar de baja'
                });

                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('id', btn.dataset.id);
                    const res = await fetch('/GESTACAD/controllers/inscripcionesController.php?action=delete', { method: 'POST', body: formData });
                    const data = await res.json();
                    if (data.status === 'success') {
                        Swal.fire('Baja', data.message, 'success');
                        loadData();
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                }
            }
        });

        btnGuardar.addEventListener('click', async () => {
            const formData = new FormData(form);
            try {
                const res = await fetch(`/GESTACAD/controllers/inscripcionesController.php?action=store`, { method: 'POST', body: formData });
                const data = await res.json();
                if (data.status === 'ok') {
                    modal.hide();
                    Swal.fire('Éxito', 'Alumno inscrito correctamente', 'success');
                    loadData();
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'Error de conexión', 'error');
            }
        });

        btnGuardarCal.addEventListener('click', async () => {
            const formData = new FormData(formCal);
            try {
                const res = await fetch(`/GESTACAD/controllers/inscripcionesController.php?action=updateCalificaciones`, { method: 'POST', body: formData });
                const data = await res.json();
                if (data.status === 'ok') {
                    calModal.hide();
                    Swal.fire('Éxito', 'Calificaciones actualizadas', 'success');
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