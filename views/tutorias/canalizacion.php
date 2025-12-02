<?php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php";

$auth = new AuthController($conn);
$auth->checkAuth();
$page_title = "Canalización de Alumnos";
include __DIR__ . "/../objects/header.php";
?>

<div class="container mt-3 mt-md-5">
    <div class="card shadow-sm mb-3 mb-md-4">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div>
                    <h1 class="h4 h3-md mb-1">
                        <i class="bi bi-arrow-right-circle-fill me-2 text-primary"></i>
                        Canalización de Alumnos
                    </h1>
                    <p class="text-muted mb-0 small">Registra y gestiona las canalizaciones de alumnos a diferentes áreas</p>
                </div>
                <button class="btn btn-primary btn-lg" id="btnNuevaCanalizacion">
                    <i class="bi bi-plus-circle me-2"></i>
                    <span class="d-none d-sm-inline">Nueva Canalización</span>
                    <span class="d-sm-none">Nueva</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Filtros y búsqueda -->
    <div class="card shadow-sm mb-3 mb-md-4">
        <div class="card-body p-3 p-md-4">
            <div class="row g-3">
                <div class="col-12 col-md-6 col-lg-4">
                    <label for="filtroBusqueda" class="form-label fw-bold">
                        <i class="bi bi-search me-1"></i>Buscar
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="filtroBusqueda" 
                           placeholder="Buscar por alumno, matrícula o área...">
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <label for="filtroArea" class="form-label fw-bold">
                        <i class="bi bi-funnel me-1"></i>Área
                    </label>
                    <select class="form-select" id="filtroArea">
                        <option value="">Todas las áreas</option>
                    </select>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <label for="filtroPeriodo" class="form-label fw-bold">
                        <i class="bi bi-calendar me-1"></i>Periodo
                    </label>
                    <select class="form-select" id="filtroPeriodo">
                        <option value="">Todos los periodos</option>
                    </select>
                </div>
                <div class="col-12 col-md-6 col-lg-2 d-flex align-items-end">
                    <button class="btn btn-outline-secondary w-100" id="btnLimpiarFiltros">
                        <i class="bi bi-x-circle me-1"></i>Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de canalizaciones (Desktop) -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="h5 h6-md mb-0">
                    <i class="bi bi-list-ul me-2"></i>Registros de Canalización
                </h3>
                <span class="badge bg-primary" id="contadorResultados" style="display: none;">
                    <span id="numResultados">0</span> resultado(s)
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Alumno</th>
                            <th>Matrícula</th>
                            <th>Área</th>
                            <th>Periodo</th>
                            <th>Fecha</th>
                            <th>Observación</th>
                            <th>Canalizado Por</th>
                            <th style="width: 100px;">Estado</th>
                        </tr>
                    </thead>
                    <tbody id="canalizacionBody">
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <p class="text-muted mt-2 mb-0">Cargando canalizaciones...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Vista de cards para móvil -->
            <div class="d-md-none" id="canalizacionCardsBody" style="max-height: calc(100vh - 400px); overflow-y: auto;">
                <div class="p-3">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="text-muted mt-2 mb-0">Cargando canalizaciones...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Canalización -->
<div class="modal fade" id="canalizacionModal" tabindex="-1" aria-labelledby="modalCanalizacionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-md-down">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalCanalizacionLabel">
                    <i class="bi bi-arrow-right-circle me-2"></i>
                    Nueva Canalización
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formCanalizacion">
                    <input type="hidden" id="canalizacion_id" name="canalizacion_id">
                    
                    <div class="mb-3">
                        <label for="alumno_id" class="form-label fw-bold">
                            Alumno <span class="text-danger">*</span>
                        </label>
                        <select id="alumno_id" name="alumno_id" class="form-select" required>
                            <option value="">Seleccione un alumno...</option>
                        </select>
                        <small class="text-muted">Busca y selecciona el alumno a canalizar</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="periodo_id" class="form-label fw-bold">
                                Periodo <span class="text-danger">*</span>
                            </label>
                            <select id="periodo_id" name="periodo_id" class="form-select" required>
                                <option value="">Seleccione un periodo...</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="area_id" class="form-label fw-bold">
                                Área de Canalización <span class="text-danger">*</span>
                            </label>
                            <select id="area_id" name="area_id" class="form-select" required>
                                <option value="">Seleccione un área...</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="observacion" class="form-label fw-bold">
                            Observación / Motivo <span class="text-danger">*</span>
                        </label>
                        <textarea id="observacion" 
                                  name="observacion" 
                                  class="form-control" 
                                  rows="4" 
                                  required
                                  placeholder="Describe el motivo de la canalización, situación del alumno, y cualquier información relevante..."></textarea>
                        <small class="text-muted">Proporciona información detallada sobre la situación que requiere la canalización</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="btnGuardar">
                    <i class="bi bi-check-circle me-1"></i>Guardar Canalización
                </button>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . "/../objects/footer.php"; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalEl = document.getElementById('canalizacionModal');
    const modal = new bootstrap.Modal(modalEl);
    const form = document.getElementById('formCanalizacion');
    const tbody = document.getElementById('canalizacionBody');
    const cardsBody = document.getElementById('canalizacionCardsBody');
    const btnGuardar = document.getElementById('btnGuardar');
    const btnNuevaCanalizacion = document.getElementById('btnNuevaCanalizacion');
    
    const alumnoSelect = document.getElementById('alumno_id');
    const periodoSelect = document.getElementById('periodo_id');
    const areaSelect = document.getElementById('area_id');
    const observacionTextarea = document.getElementById('observacion');
    
    const filtroBusqueda = document.getElementById('filtroBusqueda');
    const filtroArea = document.getElementById('filtroArea');
    const filtroPeriodo = document.getElementById('filtroPeriodo');
    const btnLimpiarFiltros = document.getElementById('btnLimpiarFiltros');
    
    let canalizacionesData = [];
    let alumnosData = [];
    
    // Cargar opciones para los selects
    async function loadOptions() {
        try {
            // Cargar áreas
            const resAreas = await fetch('/GESTACAD/controllers/canalizacionController.php?action=getAreas');
            const areas = await resAreas.json();
            areaSelect.innerHTML = '<option value="">Seleccione un área...</option>';
            filtroArea.innerHTML = '<option value="">Todas las áreas</option>';
            areas.forEach(area => {
                areaSelect.innerHTML += `<option value="${area.id}">${area.nombre}</option>`;
                filtroArea.innerHTML += `<option value="${area.id}">${area.nombre}</option>`;
            });
            
            // Cargar periodos
            const resPer = await fetch('/GESTACAD/controllers/periodosController.php?action=index');
            const periodos = await resPer.json();
            periodoSelect.innerHTML = '<option value="">Seleccione un periodo...</option>';
            filtroPeriodo.innerHTML = '<option value="">Todos los periodos</option>';
            periodos.forEach(periodo => {
                periodoSelect.innerHTML += `<option value="${periodo.id}">${periodo.nombre}</option>`;
                filtroPeriodo.innerHTML += `<option value="${periodo.id}">${periodo.nombre}</option>`;
            });
            
            // Cargar alumnos
            const resAlu = await fetch('/GESTACAD/controllers/alumnoController.php?action=listarAlumnos');
            alumnosData = await resAlu.json();
            alumnoSelect.innerHTML = '<option value="">Seleccione un alumno...</option>';
            alumnosData.forEach(alumno => {
                const nombreCompleto = `${alumno.nombre} ${alumno.apellido_paterno} ${alumno.apellido_materno || ''}`.trim();
                alumnoSelect.innerHTML += `<option value="${alumno.id_alumno}" data-matricula="${alumno.matricula || ''}">${nombreCompleto} - ${alumno.matricula || 'Sin matrícula'}</option>`;
            });
            
            // Inicializar Select2 para alumnos si está disponible
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $(alumnoSelect).select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Seleccione un alumno...',
                    allowClear: true,
                    dropdownParent: $(modalEl),
                    language: {
                        noResults: function() {
                            return "No se encontraron alumnos";
                        },
                        searching: function() {
                            return "Buscando...";
                        }
                    }
                });
            }
        } catch (error) {
            console.error("Error loading options", error);
            Swal.fire('Error', 'Error al cargar las opciones', 'error');
        }
    }
    
    // Determinar color del badge según el estatus
    function getEstatusBadgeClass(estatus) {
        const estatusTexto = estatus || 'PENDIENTE';
        if (estatusTexto === 'ATENDIDO') {
            return 'bg-success';
        } else if (estatusTexto === 'CANCELADO') {
            return 'bg-danger';
        } else {
            return 'bg-warning';
        }
    }
    
    // Renderizar tabla (Desktop)
    function renderTable(data) {
        tbody.innerHTML = '';
        
        if (!data || data.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-5">
                        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3 mb-0">No hay canalizaciones registradas</p>
                    </td>
                </tr>
            `;
            return;
        }
        
        data.forEach(item => {
            const nombreCompleto = item.alumno_nombre_completo || 
                                 `${item.alumno_nombre || ''} ${item.alumno_apellido_paterno || ''} ${item.alumno_apellido_materno || ''}`.trim();
            const fecha = item.fecha_solicitud ? new Date(item.fecha_solicitud).toLocaleDateString('es-MX') : '-';
            const canalizadoPor = `${item.usuario_nombre || ''} ${item.usuario_apellido || ''}`.trim();
            const observacionCorta = item.observacion ? 
                (item.observacion.length > 50 ? item.observacion.substring(0, 50) + '...' : item.observacion) : '-';
            
            const estatusTexto = item.estatus || 'PENDIENTE';
            const estatusBadge = getEstatusBadgeClass(item.estatus);
            
            tbody.innerHTML += `
                <tr>
                    <td><strong>${item.id}</strong></td>
                    <td>
                        <div class="fw-bold">${nombreCompleto || 'N/A'}</div>
                    </td>
                    <td><span class="badge bg-secondary">${item.alumno_matricula || 'N/A'}</span></td>
                    <td>
                        <span class="badge bg-info">${item.area_nombre || 'N/A'}</span>
                    </td>
                    <td>${item.periodo_nombre || 'N/A'}</td>
                    <td>
                        <small>${fecha}</small>
                    </td>
                    <td>
                        <span title="${item.observacion || ''}">${observacionCorta}</span>
                    </td>
                    <td><small>${canalizadoPor || 'N/A'}</small></td>
                    <td>
                        <span class="badge ${estatusBadge}">${estatusTexto}</span>
                    </td>
                </tr>
            `;
        });
    }
    
    // Función para escapar HTML
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Renderizar cards (Móvil)
    function renderCards(data) {
        if (!cardsBody) return;
        
        // Crear contenedor completo
        let htmlContent = '';
        
        if (!data || data.length === 0) {
            htmlContent = `
                <div class="p-3">
                    <div class="text-center py-5">
                        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3 mb-0">No hay canalizaciones registradas</p>
                    </div>
                </div>
            `;
            cardsBody.innerHTML = htmlContent;
            return;
        }
        
        let cardsHTML = '';
        data.forEach(item => {
            const nombreCompleto = item.alumno_nombre_completo || 
                                 `${item.alumno_nombre || ''} ${item.alumno_apellido_paterno || ''} ${item.alumno_apellido_materno || ''}`.trim();
            const fecha = item.fecha_solicitud ? new Date(item.fecha_solicitud).toLocaleDateString('es-MX', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            }) : '-';
            const canalizadoPor = `${item.usuario_nombre || ''} ${item.usuario_apellido || ''}`.trim();
            const estatusTexto = item.estatus || 'PENDIENTE';
            const estatusBadge = getEstatusBadgeClass(item.estatus);
            
            // Escapar HTML para seguridad
            const nombreEscapado = escapeHtml(nombreCompleto || 'N/A');
            const matriculaEscapada = escapeHtml(item.alumno_matricula || 'N/A');
            const areaEscapada = escapeHtml(item.area_nombre || 'N/A');
            const periodoEscapado = escapeHtml(item.periodo_nombre || 'N/A');
            const observacionEscapada = escapeHtml(item.observacion || 'Sin observación');
            const canalizadoPorEscapado = escapeHtml(canalizadoPor || 'N/A');
            
            cardsHTML += `
                <div class="card mb-3 border-start border-primary border-3 shadow-sm canalizacion-card" 
                     data-nombre="${escapeHtml(nombreCompleto.toLowerCase())}"
                     data-matricula="${escapeHtml((item.alumno_matricula || '').toLowerCase())}"
                     data-area="${escapeHtml((item.area_nombre || '').toLowerCase())}"
                     data-periodo="${escapeHtml((item.periodo_nombre || '').toLowerCase())}"
                     data-observacion="${escapeHtml((item.observacion || '').toLowerCase())}">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="flex-grow-1">
                                <h6 class="card-title mb-1 fw-bold">
                                    <i class="bi bi-person-circle me-2 text-primary"></i>
                                    ${nombreEscapado}
                                </h6>
                                <p class="text-muted small mb-1">
                                    <i class="bi bi-person-badge me-1"></i>
                                    <strong>Matrícula:</strong> ${matriculaEscapada}
                                </p>
                                <p class="text-muted small mb-1">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    <strong>Fecha:</strong> ${fecha}
                                </p>
                            </div>
                            <span class="badge ${estatusBadge} align-self-start">${estatusTexto}</span>
                        </div>
                        
                        <div class="mb-2">
                            <span class="badge bg-info">
                                <i class="bi bi-building me-1"></i>${areaEscapada}
                            </span>
                            <span class="badge bg-secondary ms-1">
                                <i class="bi bi-calendar-range me-1"></i>${periodoEscapado}
                            </span>
                        </div>
                        
                        <div class="mb-2">
                            <p class="small mb-1 fw-bold">
                                <i class="bi bi-chat-left-text me-1"></i>Observación:
                            </p>
                            <p class="small text-muted mb-0" style="white-space: pre-wrap; word-wrap: break-word;">${observacionEscapada}</p>
                        </div>
                        
                        <div class="pt-2 border-top">
                            <p class="small text-muted mb-1">
                                <i class="bi bi-person-check me-1"></i>
                                <strong>Canalizado por:</strong> ${canalizadoPorEscapado}
                            </p>
                            <p class="small text-muted mb-0">
                                <i class="bi bi-hash me-1"></i>
                                <strong>ID:</strong> #${item.id}
                            </p>
                        </div>
                    </div>
                </div>
            `;
        });
        
        htmlContent = `<div class="p-3">${cardsHTML}</div>`;
        cardsBody.innerHTML = htmlContent;
    }
    
    // Renderizar ambas vistas
    function renderViews(data) {
        renderTable(data);
        renderCards(data);
    }
    
    // Cargar datos
    async function loadData() {
        try {
            // Mostrar loading en ambas vistas
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-3">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </td>
                </tr>
            `;
            if (cardsBody) {
                cardsBody.innerHTML = `
                    <div class="p-3">
                        <div class="text-center py-3">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="text-muted mt-2 mb-0 small">Cargando canalizaciones...</p>
                        </div>
                    </div>
                `;
            }
            
            const res = await fetch('/GESTACAD/controllers/canalizacionController.php?action=index');
            canalizacionesData = await res.json();
            aplicarFiltros();
        } catch (error) {
            console.error(error);
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-5">
                        <i class="bi bi-exclamation-triangle text-danger" style="font-size: 2rem;"></i>
                        <p class="text-danger mt-3 mb-0">Error al cargar los datos</p>
                    </td>
                </tr>
            `;
            if (cardsBody) {
                cardsBody.innerHTML = `
                    <div class="p-3">
                        <div class="text-center py-5">
                            <i class="bi bi-exclamation-triangle text-danger" style="font-size: 2rem;"></i>
                            <p class="text-danger mt-3 mb-0">Error al cargar los datos</p>
                        </div>
                    </div>
                `;
            }
        }
    }
    
    // Aplicar filtros
    function aplicarFiltros() {
        let datosFiltrados = [...canalizacionesData];
        
        // Filtro de búsqueda
        const busqueda = filtroBusqueda.value.toLowerCase().trim();
        if (busqueda) {
            datosFiltrados = datosFiltrados.filter(item => {
                const nombre = (item.alumno_nombre_completo || '').toLowerCase();
                const matricula = (item.alumno_matricula || '').toLowerCase();
                const area = (item.area_nombre || '').toLowerCase();
                const observacion = (item.observacion || '').toLowerCase();
                return nombre.includes(busqueda) || 
                       matricula.includes(busqueda) || 
                       area.includes(busqueda) ||
                       observacion.includes(busqueda);
            });
        }
        
        // Filtro de área
        const areaId = filtroArea.value;
        if (areaId) {
            datosFiltrados = datosFiltrados.filter(item => item.area_id == areaId);
        }
        
        // Filtro de periodo
        const periodoId = filtroPeriodo.value;
        if (periodoId) {
            datosFiltrados = datosFiltrados.filter(item => item.periodo_id == periodoId);
        }
        
        // Actualizar contador
        const contadorEl = document.getElementById('contadorResultados');
        const numResultadosEl = document.getElementById('numResultados');
        if (contadorEl && numResultadosEl) {
            const total = datosFiltrados.length;
            numResultadosEl.textContent = total;
            if (total > 0 || filtroBusqueda.value || filtroArea.value || filtroPeriodo.value) {
                contadorEl.style.display = 'inline-block';
            } else {
                contadorEl.style.display = 'none';
            }
        }
        
        renderViews(datosFiltrados);
    }
    
    // Event listeners
    btnNuevaCanalizacion.addEventListener('click', () => {
        form.reset();
        document.getElementById('canalizacion_id').value = '';
        document.getElementById('modalCanalizacionLabel').innerHTML = '<i class="bi bi-arrow-right-circle me-2"></i>Nueva Canalización';
        modal.show();
    });
    
    btnGuardar.addEventListener('click', async () => {
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        const formData = new FormData(form);
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';
        
        try {
            const res = await fetch('/GESTACAD/controllers/canalizacionController.php?action=store', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            
            if (data.status === 'ok') {
                modal.hide();
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: data.message || 'Canalización registrada exitosamente',
                    timer: 2000,
                    showConfirmButton: false
                });
                loadData();
            } else {
                Swal.fire('Error', data.message || 'Error al registrar la canalización', 'error');
            }
        } catch (error) {
            console.error(error);
            Swal.fire('Error', 'Error de conexión. Intenta nuevamente.', 'error');
        } finally {
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = '<i class="bi bi-check-circle me-1"></i>Guardar Canalización';
        }
    });
    
    // Filtros
    filtroBusqueda.addEventListener('input', aplicarFiltros);
    filtroArea.addEventListener('change', aplicarFiltros);
    filtroPeriodo.addEventListener('change', aplicarFiltros);
    
    btnLimpiarFiltros.addEventListener('click', () => {
        filtroBusqueda.value = '';
        filtroArea.value = '';
        filtroPeriodo.value = '';
        aplicarFiltros();
    });
    
    // Limpiar Select2 al cerrar modal
    modalEl.addEventListener('hidden.bs.modal', function() {
        if (typeof $ !== 'undefined' && $.fn.select2 && $(alumnoSelect).hasClass('select2-hidden-accessible')) {
            $(alumnoSelect).val(null).trigger('change');
        }
    });
    
    // Inicializar
    loadOptions();
    loadData();
});
</script>
