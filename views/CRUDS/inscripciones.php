<?php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php";

$auth = new AuthController($conn);
$auth->checkAuth();
$page_title = "Inscripciones";
include __DIR__ . "/../objects/header.php";
?>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col-md-6">
            <button class="btn btn-success" id="btnNuevaInscripcion">
                <i class="bi bi-plus-circle"></i> Inscribir Alumno
            </button>
        </div>
        <div class="col-md-6">
            <select id="filtroClase" class="form-select">
                <option value="">Filtrar por Clase...</option>
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Alumno</th>
                    <th>Clase</th>
                    <th>Estado</th>
                    <th>Calificaciones</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="inscripcionesBody"></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="inscripcionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Inscribir Alumno</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formInscripcion">
                    <div class="mb-3">
                        <label for="clase_id" class="form-label">Clase</label>
                        <select id="clase_id" name="clase_id" class="form-select" required></select>
                    </div>
                    <div class="mb-3">
                        <label for="alumno_id" class="form-label">Alumno</label>
                        <select id="alumno_id" name="alumno_id" class="form-select" required></select>
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

<div class="modal fade" id="calificacionesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Calificaciones</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formCalificaciones">
                    <input type="hidden" id="cal_id" name="id">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Parcial 1</label>
                            <input type="number" step="0.01" id="cal_parcial1" name="cal_parcial1" class="form-control">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Parcial 2</label>
                            <input type="number" step="0.01" id="cal_parcial2" name="cal_parcial2" class="form-control">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Parcial 3</label>
                            <input type="number" step="0.01" id="cal_parcial3" name="cal_parcial3" class="form-control">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Parcial 4</label>
                            <input type="number" step="0.01" id="cal_parcial4" name="cal_parcial4" class="form-control">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Final</label>
                            <input type="number" step="0.01" id="cal_final" name="cal_final" class="form-control">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarCalificaciones">Guardar</button>
            </div>
        </div>
    </div>
</div>

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

        const renderTable = (data) => {
            tbody.innerHTML = '';
            if (!data || data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center">No hay registros</td></tr>';
                return;
            }
            data.forEach(item => {
                tbody.innerHTML += `
                <tr>
                    <td>${item.id}</td>
                    <td>${item.alumno_nombre} ${item.alumno_apellido}</td>
                    <td>${item.asignatura_nombre} (${item.seccion})</td>
                    <td>${item.estado}</td>
                    <td>
                        P1: ${item.cal_parcial1 || '-'} | P2: ${item.cal_parcial2 || '-'} <br>
                        P3: ${item.cal_parcial3 || '-'} | P4: ${item.cal_parcial4 || '-'} <br>
                        Final: ${item.cal_final || '-'}
                    </td>
                    <td>
                        <button class="btn btn-sm btn-info btn-calificaciones" 
                            data-id="${item.id}" 
                            data-p1="${item.cal_parcial1}"
                            data-p2="${item.cal_parcial2}"
                            data-p3="${item.cal_parcial3}"
                            data-p4="${item.cal_parcial4}"
                            data-final="${item.cal_final}">
                            <i class="bi bi-journal-check"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-eliminar" data-id="${item.id}"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            `;
            });
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