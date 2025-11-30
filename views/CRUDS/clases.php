<?php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php";

$auth = new AuthController($conn);
$auth->checkAuth();
$page_title = "Gestión de Clases";
include __DIR__ . "/../objects/header.php";
?>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col-md-6">
            <button class="btn btn-success" id="btnNuevaClase">
                <i class="bi bi-plus-circle"></i> Agregar Clase
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Asignatura</th>
                    <th>Periodo</th>
                    <th>Docente</th>
                    <th>Sección</th>
                    <th>Modalidad</th>
                    <th>Aula</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="clasesBody"></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="claseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Formulario de Clase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formClase">
                    <input type="hidden" id="id" name="id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="asignatura_id" class="form-label">Asignatura</label>
                            <select id="asignatura_id" name="asignatura_id" class="form-select" required></select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="periodo_id" class="form-label">Periodo</label>
                            <select id="periodo_id" name="periodo_id" class="form-select" required></select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="docente_usuario_id" class="form-label">Docente</label>
                            <select id="docente_usuario_id" name="docente_usuario_id" class="form-select"
                                required></select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="modalidad_id" class="form-label">Modalidad</label>
                            <select id="modalidad_id" name="modalidad_id" class="form-select" required></select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="seccion" class="form-label">Sección</label>
                            <input type="text" id="seccion" name="seccion" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="cupo" class="form-label">Cupo</label>
                            <input type="number" id="cupo" name="cupo" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="aula" class="form-label">Aula</label>
                            <input type="text" id="aula" name="aula" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="grupo_referencia" class="form-label">Grupo Referencia</label>
                        <select id="grupo_referencia" name="grupo_referencia" class="form-select"></select>
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

        const renderTable = (data) => {
            tbody.innerHTML = '';
            if (!data || data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center">No hay registros</td></tr>';
                return;
            }
            data.forEach(item => {
                tbody.innerHTML += `
                <tr>
                    <td>${item.id}</td>
                    <td>${item.asignatura_clave} - ${item.asignatura_nombre}</td>
                    <td>${item.periodo_nombre}</td>
                    <td>${item.docente_nombre} ${item.docente_apellido}</td>
                    <td>${item.seccion}</td>
                    <td>${item.modalidad_nombre}</td>
                    <td>${item.aula || ''}</td>
                    <td>
                        <button class="btn btn-sm btn-warning btn-editar" 
                            data-id="${item.id}" 
                            data-asignatura="${item.asignatura_id}"
                            data-periodo="${item.periodo_id}"
                            data-docente="${item.docente_usuario_id}"
                            data-seccion="${item.seccion}"
                            data-modalidad="${item.modalidad_id}"
                            data-cupo="${item.cupo}"
                            data-grupo="${item.grupo_referencia}"
                            data-aula="${item.aula}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-eliminar" data-id="${item.id}"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            `;
            });
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