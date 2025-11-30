<?php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php";

$auth = new AuthController($conn);
$auth->checkAuth();
$page_title = "Asignaturas";
include __DIR__ . "/../objects/header.php";
?>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col-md-6">
            <button class="btn btn-success" id="btnNuevaAsignatura">
                <i class="bi bi-plus-circle"></i> Agregar Asignatura
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Clave</th>
                    <th>Nombre</th>
                    <th>Créditos</th>
                    <th>Horas/Semana</th>
                    <th>Área</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="asignaturasBody"></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="asignaturaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Formulario de Asignatura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formAsignatura">
                    <input type="hidden" id="id" name="id">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="clave" class="form-label">Clave</label>
                            <input type="text" id="clave" name="clave" class="form-control" required>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" id="nombre" name="nombre" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="creditos" class="form-label">Créditos</label>
                            <input type="number" id="creditos" name="creditos" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="horas_semana" class="form-label">Horas/Semana</label>
                            <input type="number" id="horas_semana" name="horas_semana" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="area" class="form-label">Área</label>
                            <input type="text" id="area" name="area" class="form-control">
                        </div>
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
        const modalEl = document.getElementById('asignaturaModal');
        const modal = new bootstrap.Modal(modalEl);
        const form = document.getElementById('formAsignatura');
        const tbody = document.getElementById('asignaturasBody');
        const btnGuardar = document.getElementById('btnGuardar');
        const modalLabel = document.getElementById('modalLabel');

        const renderTable = (data) => {
            tbody.innerHTML = '';
            if (!data || data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center">No hay registros</td></tr>';
                return;
            }
            data.forEach(item => {
                tbody.innerHTML += `
                <tr>
                    <td>${item.id}</td>
                    <td>${item.clave}</td>
                    <td>${item.nombre}</td>
                    <td>${item.creditos || ''}</td>
                    <td>${item.horas_semana || ''}</td>
                    <td>${item.area || ''}</td>
                    <td>
                        <button class="btn btn-sm btn-warning btn-editar" 
                            data-id="${item.id}" 
                            data-clave="${item.clave}"
                            data-nombre="${item.nombre}"
                            data-creditos="${item.creditos}"
                            data-horas="${item.horas_semana}"
                            data-area="${item.area}">
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
                const res = await fetch('/GESTACAD/controllers/asignaturasController.php?action=index');
                const data = await res.json();
                renderTable(data);
            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'No se pudieron cargar los datos', 'error');
            }
        };

        document.getElementById('btnNuevaAsignatura').addEventListener('click', () => {
            form.reset();
            document.getElementById('id').value = '';
            modalLabel.textContent = 'Agregar Asignatura';
            modal.show();
        });

        document.addEventListener('click', async (e) => {
            if (e.target.closest('.btn-editar')) {
                const btn = e.target.closest('.btn-editar');
                document.getElementById('id').value = btn.dataset.id;
                document.getElementById('clave').value = btn.dataset.clave;
                document.getElementById('nombre').value = btn.dataset.nombre;
                document.getElementById('creditos').value = btn.dataset.creditos;
                document.getElementById('horas_semana').value = btn.dataset.horas;
                document.getElementById('area').value = btn.dataset.area;
                modalLabel.textContent = 'Editar Asignatura';
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
                    const res = await fetch('/GESTACAD/controllers/asignaturasController.php?action=delete', { method: 'POST', body: formData });
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
                const res = await fetch(`/GESTACAD/controllers/asignaturasController.php?action=${action}`, { method: 'POST', body: formData });
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

        loadData();
    });
</script>