<?php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php";

$auth = new AuthController($conn);
$auth->checkAuth();
$page_title = "Canalización de Alumnos";
include __DIR__ . "/../objects/header.php";
?>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col-md-6">
            <button class="btn btn-success" id="btnNuevaCanalizacion">
                <i class="bi bi-plus-circle"></i> Nueva Canalización
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Alumno</th>
                    <th>Periodo</th>
                    <th>Área</th>
                    <th>Observación</th>
                    <th>Canalizado Por</th>
                </tr>
            </thead>
            <tbody id="canalizacionBody"></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="canalizacionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Formulario de Canalización</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formCanalizacion">
                    <div class="mb-3">
                        <label for="alumno_id" class="form-label">Alumno</label>
                        <select id="alumno_id" name="alumno_id" class="form-select" required></select>
                    </div>
                    <div class="mb-3">
                        <label for="periodo_id" class="form-label">Periodo</label>
                        <select id="periodo_id" name="periodo_id" class="form-select" required></select>
                    </div>
                    <div class="mb-3">
                        <label for="area_id" class="form-label">Área de Canalización</label>
                        <select id="area_id" name="area_id" class="form-select" required>
                            <!-- TODO: Load areas dynamically if controller exists, else hardcode or create controller -->
                            <option value="1">Psicología</option>
                            <option value="2">Pedagogía</option>
                            <option value="3">Nutrición</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="observacion" class="form-label">Observación</label>
                        <textarea id="observacion" name="observacion" class="form-control" required></textarea>
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
    window.addEventListener('load', function () {
        const modalEl = document.getElementById('canalizacionModal');
        const modal = new bootstrap.Modal(modalEl);
        const form = document.getElementById('formCanalizacion');
        const tbody = document.getElementById('canalizacionBody');
        const btnGuardar = document.getElementById('btnGuardar');

        const alumnoSelect = document.getElementById('alumno_id');
        const periodoSelect = document.getElementById('periodo_id');

        const loadOptions = async () => {
            try {
                // Alumnos
                const resAlu = await fetch('/GESTACAD/controllers/alumnoController.php?action=listarAlumnos');
                const dataAlu = await resAlu.json();
                alumnoSelect.innerHTML = '<option value="">Seleccione...</option>' + dataAlu.map(i => `<option value="${i.id_alumno}">${i.nombre} ${i.apellido_paterno}</option>`).join('');

                // Periodos
                const resPer = await fetch('/GESTACAD/controllers/periodosController.php?action=index');
                const dataPer = await resPer.json();
                periodoSelect.innerHTML = '<option value="">Seleccione...</option>' + dataPer.map(i => `<option value="${i.id}">${i.nombre}</option>`).join('');

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
                    <td>${item.periodo_nombre}</td>
                    <td>${item.area_nombre || item.area_id}</td>
                    <td>${item.observacion}</td>
                    <td>${item.usuario_nombre}</td>
                </tr>
            `;
            });
        };

        const loadData = async () => {
            try {
                const res = await fetch('/GESTACAD/controllers/canalizacionController.php?action=index');
                const data = await res.json();
                renderTable(data);
            } catch (error) {
                console.error(error);
            }
        };

        document.getElementById('btnNuevaCanalizacion').addEventListener('click', () => {
            form.reset();
            modal.show();
        });

        btnGuardar.addEventListener('click', async () => {
            const formData = new FormData(form);
            try {
                const res = await fetch(`/GESTACAD/controllers/canalizacionController.php?action=store`, { method: 'POST', body: formData });
                const data = await res.json();
                if (data.status === 'ok') {
                    modal.hide();
                    Swal.fire('Éxito', 'Canalización registrada', 'success');
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