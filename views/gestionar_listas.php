<?php
// Remove session_start() as it's already started in index.php
require_once __DIR__ . '/../controllers/authController.php';
require_once __DIR__ . '/../controllers/alumnoController.php';
require_once __DIR__ . '/../models/TutoriaGrupal.php';
require_once __DIR__ . '/../models/TutoriaIndividual.php';
require_once __DIR__ . '/../config/db.php';

$auth = new AuthController($conn);
$auth->checkAuth();

$id_grupo = isset($_GET['id_grupo']) ? (int)$_GET['id_grupo'] : 0;
if ($id_grupo === 0) {
    header('Location: /GESTACAD/dashboard');
    exit();
}

$fecha = date('Y-m-d'); 
$alumnoController = new AlumnoController($conn);

$nombre_grupo = $alumnoController->getNombreGrupo($id_grupo);

// Contar alumnos del grupo
$totalAlumnos = $alumnoController->contarTotalAlumnosPorGrupo($id_grupo);

// Obtener tutorías grupales e individuales
$tutoriaGrupal = new TutoriaGrupal($conn);
$tutoriaIndividual = new TutoriaIndividual($conn);
$tutoriasGrupales = $tutoriaGrupal->getByGrupo($id_grupo);
$tutoriasIndividuales = $tutoriaIndividual->getByGrupo($id_grupo);

$page_title = "Gestionar Asistencias: " . htmlspecialchars($nombre_grupo);
include 'objects/header.php';
include 'tutorias/tutorias_modals.php';
?>

<div class="container mt-3 mt-md-5">
    <div class="card shadow-sm mb-3 mb-md-4">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
            <h1 class="h4 h3-md mb-1">Gestionar Asistencias del Grupo</h1>
            <h2 class="h6 h5-md text-muted mb-0"><?= htmlspecialchars($nombre_grupo) ?></h2>
                </div>
                <button class="btn btn-info" 
                        id="btnGenerarQR"
                        data-grupo-id="<?= htmlspecialchars($id_grupo) ?>"
                        data-fecha="<?= htmlspecialchars($fecha) ?>"
                        title="Generar código QR para que los alumnos marquen su asistencia (válido por 5 minutos)">
                    <i class="bi bi-qr-code me-1"></i>
                    <span class="d-none d-md-inline">Generar QR</span>
                    <span class="d-md-none">QR</span>
                </button>
            </div>
        </div>
    </div>
    
    <?php if ($totalAlumnos > 0): ?>
    <div class="card shadow-sm mb-3 mb-md-4">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-3">
                <button type="button" 
                        class="btn btn-primary w-100 w-md-auto btn-lg btn-tutoria-grupal-today" 
                        data-grupo-id="<?= htmlspecialchars($id_grupo) ?>"
                        data-grupo-nombre="<?= htmlspecialchars($nombre_grupo) ?>"
                        data-bs-toggle="tooltip" 
                        data-bs-placement="top" 
                        title="Tomar Asistencia Grupal para Hoy">
                    <i class="bi bi-calendar-plus-fill me-2"></i>
                    <span class="d-none d-sm-inline">Pasar lista / Editar la de hoy</span>
                    <span class="d-sm-none">Pasar lista grupal</span>
                    <small class="d-block d-sm-inline d-md-none mt-1" id="fecha-boton-mobile">(<?= date('d/m/Y') ?>)</small>
                    <span class="d-none d-md-inline" id="fecha-boton-desktop"> (<?= date('d/m/Y') ?>)</span>
                </button>
                
                <div class="d-grid d-md-flex gap-2 w-100 w-md-auto">
                
                    
                    <button type="button" 
                            class="btn btn-outline-info btn-lg btn-tutoria-individual flex-fill flex-md-none" 
                            data-grupo-id="<?= htmlspecialchars($id_grupo) ?>"
                            data-grupo-nombre="<?= htmlspecialchars($nombre_grupo) ?>"
                            data-bs-toggle="tooltip" 
                            data-bs-placement="top" 
                            title="Tomar Lista Individual">
                        <i class="bi bi-person-fill me-2"></i>
                        <span class="d-none d-md-inline">Individual</span>
                        <span class="d-md-none">Tomar Lista Individual</span>
                    </button>
                    
                    <a href="ver-alumnos-grupo?id_grupo=<?= htmlspecialchars($id_grupo) ?>" 
                       class="btn btn-outline-secondary btn-lg flex-fill flex-md-none"
                       data-bs-toggle="tooltip" 
                       data-bs-placement="top" 
                       title="Ver Alumnos del Grupo">
                        <i class="bi bi-gear-fill me-2"></i><span class="d-none d-sm-inline">Ver Alumnos</span><span class="d-sm-none">Alumnos</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="card shadow-sm mb-3 mb-md-4">
        <div class="card-body p-3 p-md-4">
            <div class="alert alert-secondary py-2 mb-0">
                <i class="bi bi-info-circle me-1"></i> No hay alumnos en este grupo. No se pueden gestionar listas.
            </div>
        </div>
    </div>
    <?php endif; ?>


    <!-- Tutorías Grupales -->
    <div class="card shadow-sm mb-3 mb-md-4">
        <div class="card-header">
            <h3 class="h5 h6-md mb-0"><i class="bi bi-people-fill me-2"></i> Tutorías Grupales</h3>
        </div>
        <div class="card-body p-2 p-md-3">
            <?php if (!empty($tutoriasGrupales)): ?>
                <!-- Vista de tabla para desktop -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Actividad</th>
                                <th>Asistencia</th>
                                <th>Tutor</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tutoriasGrupales as $tutoria): ?>
                                <tr>
                                    <td><?= htmlspecialchars(date("d/m/Y", strtotime($tutoria['fecha']))) ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($tutoria['actividad_nombre'] ?? 'Sin actividad') ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?= $tutoria['total_presentes'] ?? 0 ?> / <?= $tutoria['total_alumnos'] ?? 0 ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars(($tutoria['tutor_nombre'] ?? '') . ' ' . ($tutoria['tutor_apellido'] ?? '')) ?></td>
                                    <td>
                                        <div class="d-flex justify-content-start">
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-primary btn-editar-tutoria-grupal"
                                                    data-tutoria-id="<?= $tutoria['id'] ?>"
                                                    data-grupo-id="<?= htmlspecialchars($id_grupo) ?>"
                                                    data-grupo-nombre="<?= htmlspecialchars($nombre_grupo) ?>"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    title="Editar esta tutoría y asistencia">
                                                <i class="bi bi-pencil-square me-1"></i> Editar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Vista de cards para móvil -->
                <div class="d-md-none">
                    <?php foreach ($tutoriasGrupales as $tutoria): ?>
                        <div class="card mb-3 border-start border-primary border-3">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-1 fw-bold">
                                            <i class="bi bi-calendar3 me-2 text-primary"></i>
                                            <?= htmlspecialchars(date("d/m/Y", strtotime($tutoria['fecha']))) ?>
                                        </h6>
                                        <?php if (!empty($tutoria['actividad_nombre'])): ?>
                                        <p class="card-text mb-1">
                                            <i class="bi bi-clipboard-check me-1 text-success"></i>
                                            <strong><?= htmlspecialchars($tutoria['actividad_nombre']) ?></strong>
                                        </p>
                                        <?php endif; ?>
                                        <p class="card-text text-muted small mb-2">
                                            <i class="bi bi-person-badge me-1"></i>
                                            <?= htmlspecialchars(($tutoria['tutor_nombre'] ?? '') . ' ' . ($tutoria['tutor_apellido'] ?? '')) ?>
                                        </p>
                                    </div>
                                    <span class="badge bg-info fs-6">
                                        <?= $tutoria['total_presentes'] ?? 0 ?> / <?= $tutoria['total_alumnos'] ?? 0 ?>
                                    </span>
                                </div>
                                <button type="button"
                                        class="btn btn-outline-primary w-100 btn-editar-tutoria-grupal"
                                        data-tutoria-id="<?= $tutoria['id'] ?>"
                                        data-grupo-id="<?= htmlspecialchars($id_grupo) ?>"
                                        data-grupo-nombre="<?= htmlspecialchars($nombre_grupo) ?>">
                                    <i class="bi bi-pencil-square me-2"></i> Editar
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info mb-0">No hay tutorías grupales registradas para este grupo.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tutorías Individuales -->
    <div class="card shadow-sm mb-3 mb-md-4">
        <div class="card-header">
            <h3 class="h5 h6-md mb-0"><i class="bi bi-person-fill me-2"></i> Tutorías Individuales</h3>
        </div>
        <div class="card-body p-2 p-md-3">
            <?php if (!empty($tutoriasIndividuales)): ?>
                <!-- Vista de tabla para desktop -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Alumno</th>
                                <th>Matrícula</th>
                                <th>Motivo</th>
                                <th>Tutor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tutoriasIndividuales as $tutoria): ?>
                                <tr>
                                    <td><?= htmlspecialchars(date("d/m/Y", strtotime($tutoria['fecha']))) ?></td>
                                    <td>
                                        <?= htmlspecialchars(($tutoria['alumno_nombre'] ?? '') . ' ' . ($tutoria['alumno_apellido_paterno'] ?? '') . ' ' . ($tutoria['alumno_apellido_materno'] ?? '')) ?>
                                    </td>
                                    <td><?= htmlspecialchars($tutoria['matricula'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($tutoria['motivo'] ?? '') ?></td>
                                    <td><?= htmlspecialchars(($tutoria['tutor_nombre'] ?? '') . ' ' . ($tutoria['tutor_apellido'] ?? '')) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Vista de cards para móvil -->
                <div class="d-md-none">
                    <?php foreach ($tutoriasIndividuales as $tutoria): ?>
                        <div class="card mb-3 border-start border-info border-3">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-1 fw-bold">
                                            <i class="bi bi-person me-2 text-info"></i>
                                            <?= htmlspecialchars(($tutoria['alumno_nombre'] ?? '') . ' ' . ($tutoria['alumno_apellido_paterno'] ?? '') . ' ' . ($tutoria['alumno_apellido_materno'] ?? '')) ?>
                                        </h6>
                                        <p class="card-text text-muted small mb-1">
                                            <i class="bi bi-person-badge me-1"></i>
                                            <strong>Matrícula:</strong> <?= htmlspecialchars($tutoria['matricula'] ?? '') ?>
                                        </p>
                                        <p class="card-text text-muted small mb-0">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            <?= htmlspecialchars(date("d/m/Y", strtotime($tutoria['fecha']))) ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted d-block mb-1"><strong>Motivo:</strong></small>
                                    <p class="card-text small mb-0"><?= htmlspecialchars($tutoria['motivo'] ?? '') ?></p>
                                </div>
                                <div class="mt-2 pt-2 border-top">
                                    <small class="text-muted">
                                        <i class="bi bi-person-check me-1"></i>
                                        <strong>Tutor:</strong> <?= htmlspecialchars(($tutoria['tutor_nombre'] ?? '') . ' ' . ($tutoria['tutor_apellido'] ?? '')) ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info mb-0">No hay tutorías individuales registradas para este grupo.</div>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- Modal para mostrar el código QR -->
<div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrModalLabel">
                    <i class="bi bi-qr-code me-2"></i>
                    Código QR - Asistencia de Alumnos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div id="qrLoading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Generando código QR...</span>
                    </div>
                    <p class="mt-3 text-muted">Generando código QR seguro...</p>
                </div>
                <div id="qrContent" class="d-none">
                    <p class="text-muted mb-3">
                        <i class="bi bi-shield-check me-2 text-success"></i>
                        Escanea este código para marcar tu asistencia. <strong>Válido por 5 minutos</strong>
                    </p>
                    <div class="mb-3">
                        <strong><?= htmlspecialchars($nombre_grupo) ?></strong><br>
                        <small class="text-muted" id="qrFechaDisplay">Fecha: <?= htmlspecialchars(date('d/m/Y', strtotime($fecha))) ?></small><br>
                        <small class="text-danger" id="qrExpiraEn">
                            <i class="bi bi-clock me-1"></i>
                            Expira en: <span id="qrTiempoRestante">5:00</span>
                        </small>
                    </div>
                    <div id="qrcode" class="d-flex justify-content-center mb-3"></div>
                    <div class="d-flex gap-2 justify-content-center">
                        <button class="btn btn-primary" onclick="descargarQR()">
                            <i class="bi bi-download me-1"></i>
                            Descargar QR
                        </button>
                        <button class="btn btn-secondary" onclick="copiarEnlace()">
                            <i class="bi bi-link-45deg me-1"></i>
                            Copiar Enlace
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Librería para generar códigos QR -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js" integrity="sha512-CNgIRecGo7nphbeZ04Sc13ka07paqdeTu0WR1IM4kNcpmBAUSHSQX0FslNhTDadL4O5SAGapGt4FodqL8My0mA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<?php
include 'objects/footer.php';
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para obtener fecha local en formato YYYY-MM-DD (no UTC)
    function getLocalDateString() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }
    
    // Función para obtener fecha local en formato d/m/Y para mostrar
    function getLocalDateDisplayString() {
        const now = new Date();
        const day = String(now.getDate()).padStart(2, '0');
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const year = now.getFullYear();
        return `${day}/${month}/${year}`;
    }
    
    // Variables globales para QR
    const idGrupo = <?= json_encode($id_grupo) ?>;
    const nombreGrupo = <?= json_encode($nombre_grupo) ?>;
    // Usar fecha local del navegador en lugar de la fecha del servidor
    const fecha = getLocalDateString();
    let urlQRBase = '';
    let qrCodeInstance = null;
    let qrExpiraEn = null;
    let countdownInterval = null;
    
    // Botón para generar QR
    const btnGenerarQR = document.getElementById('btnGenerarQR');
    if (btnGenerarQR) {
        btnGenerarQR.addEventListener('click', function() {
            generarNuevoToken();
        });
    }
    
    // Función para generar nuevo token
    async function generarNuevoToken() {
        // Asegurarse de usar la fecha local actual al generar el QR
        const fechaLocal = getLocalDateString();
        
        const qrModal = new bootstrap.Modal(document.getElementById('qrModal'));
        qrModal.show();
        
        // Mostrar loading
        document.getElementById('qrLoading').classList.remove('d-none');
        document.getElementById('qrContent').classList.add('d-none');
        
        try {
            const formData = new FormData();
            formData.append('grupo_id', idGrupo);
            formData.append('fecha', fechaLocal);
            
            const response = await fetch('/GESTACAD/controllers/asistenciaTokenController.php?action=generarToken', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                urlQRBase = result.url;
                // Calcular la fecha de expiración directamente: 5 minutos desde ahora
                qrExpiraEn = new Date(Date.now() + 5 * 60 * 1000);
                
                // Actualizar la fecha mostrada en el modal con la fecha local
                const fechaDisplayElement = document.getElementById('qrFechaDisplay');
                if (fechaDisplayElement) {
                    fechaDisplayElement.textContent = `Fecha: ${getLocalDateDisplayString()}`;
                }
                
                // Generar QR
                const qrContainer = document.getElementById('qrcode');
                qrContainer.innerHTML = '';
                
                if (typeof QRCode !== 'undefined') {
                    qrCodeInstance = new QRCode(qrContainer, {
                        text: urlQRBase,
                        width: 256,
                        height: 256,
                        colorDark: "#000000",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.H
                    });
                } else {
                    qrContainer.innerHTML = `
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=256x256&data=${encodeURIComponent(urlQRBase)}" 
                             alt="Código QR" 
                             class="img-fluid">
                    `;
                }
                
                // Ocultar loading y mostrar contenido
                document.getElementById('qrLoading').classList.add('d-none');
                document.getElementById('qrContent').classList.remove('d-none');
                
                // Iniciar countdown inmediatamente y luego cada segundo
                iniciarCountdown();
            } else {
                alert('Error al generar el código QR: ' + (result.error || 'Error desconocido'));
                qrModal.hide();
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al generar el código QR. Intenta nuevamente.');
            qrModal.hide();
        }
    }
    
    // Función para iniciar countdown
    function iniciarCountdown() {
        if (countdownInterval) {
            clearInterval(countdownInterval);
        }
        
        const tiempoRestanteEl = document.getElementById('qrTiempoRestante');
        
        // Función para actualizar el tiempo
        function actualizarTiempo() {
            if (!qrExpiraEn) return;
            
            const ahora = Date.now();
            const expiraEn = qrExpiraEn.getTime();
            const diferencia = expiraEn - ahora;
            
            if (diferencia <= 0) {
                tiempoRestanteEl.textContent = '0:00';
                tiempoRestanteEl.parentElement.classList.add('text-danger');
                clearInterval(countdownInterval);
                return;
            }
            
            // Calcular minutos y segundos correctamente
            // diferencia está en milisegundos
            const totalSegundos = Math.floor(diferencia / 1000);
            const minutos = Math.floor(totalSegundos / 60);
            const segundos = totalSegundos % 60;
            
            // Asegurar que no sea negativo
            const minutosFinal = Math.max(0, minutos);
            const segundosFinal = Math.max(0, segundos);
            
            tiempoRestanteEl.textContent = `${minutosFinal}:${segundosFinal.toString().padStart(2, '0')}`;
        }
        
        // Actualizar inmediatamente
        actualizarTiempo();
        
        // Actualizar cada segundo
        countdownInterval = setInterval(actualizarTiempo, 1000);
    }
    
    // Limpiar interval cuando se cierre el modal
    const qrModal = document.getElementById('qrModal');
    if (qrModal) {
        qrModal.addEventListener('hidden.bs.modal', function () {
            if (countdownInterval) {
                clearInterval(countdownInterval);
                countdownInterval = null;
            }
        });
    }
    
    // Función para descargar el QR
    window.descargarQR = function() {
        if (!urlQRBase) {
            alert('Primero genera un código QR');
            return;
        }
        const canvas = document.querySelector('#qrcode canvas');
        if (canvas) {
            const url = canvas.toDataURL('image/png');
            const link = document.createElement('a');
            // Usar fecha local para el nombre del archivo
            const fechaLocal = getLocalDateString();
            link.download = `QR_Asistencia_${nombreGrupo}_${fechaLocal}.png`;
            link.href = url;
            link.click();
        }
    };
    
    // Función para copiar el enlace
    window.copiarEnlace = function() {
        if (!urlQRBase) {
            alert('Primero genera un código QR');
            return;
        }
        navigator.clipboard.writeText(urlQRBase).then(function() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: '¡Enlace copiado!',
                    text: 'El enlace ha sido copiado al portapapeles',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                alert('Enlace copiado al portapapeles');
            }
        }).catch(function(err) {
            console.error('Error al copiar:', err);
            alert('Error al copiar el enlace');
        });
    };
    
    // Event listener para botón de editar tutoría grupal
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-editar-tutoria-grupal');
        if (btn) {
            e.preventDefault();
            const tutoriaId = btn.getAttribute('data-tutoria-id');
            const grupoId = btn.getAttribute('data-grupo-id');
            const grupoNombre = btn.getAttribute('data-grupo-nombre');
            
            // Cargar datos de la tutoría
            fetch(`/GESTACAD/controllers/tutoriasController.php?action=getGrupalById&id=${tutoriaId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data) {
                        const tutoria = data.data;
                        openModalTutoriaGrupalEdit(tutoria, grupoId, grupoNombre);
                    } else {
                        alert('Error al cargar los datos de la tutoría');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar los datos de la tutoría');
                });
        }
    });
    
    function openModalTutoriaGrupalEdit(tutoria, grupoId, grupoNombre) {
        // Llenar el formulario con los datos de la tutoría
        document.getElementById('grupal-grupo-id').value = grupoId;
        document.getElementById('grupal-grupo-nombre').textContent = grupoNombre;
        document.getElementById('grupal-fecha').value = tutoria.fecha;
        document.getElementById('grupal-fecha').setAttribute('readonly', 'readonly');
        document.getElementById('grupal-actividad-nombre').value = tutoria.actividad_nombre || '';
        document.getElementById('grupal-actividad-descripcion').value = tutoria.actividad_descripcion || '';
        
        // Si hay un campo hidden para el ID de la tutoría, agregarlo
        let tutoriaIdInput = document.getElementById('grupal-tutoria-id');
        if (!tutoriaIdInput) {
            tutoriaIdInput = document.createElement('input');
            tutoriaIdInput.type = 'hidden';
            tutoriaIdInput.id = 'grupal-tutoria-id';
            tutoriaIdInput.name = 'tutoria_id';
            document.getElementById('formTutoriaGrupal').appendChild(tutoriaIdInput);
        }
        tutoriaIdInput.value = tutoria.id;
        
        // Cargar alumnos y marcar los presentes
        loadAlumnosForGrupalEdit(grupoId, tutoria.asistencia || []);
        
        // Mostrar modal
        const modal = new bootstrap.Modal(document.getElementById('modalTutoriaGrupal'));
        modal.show();
    }
    
    function loadAlumnosForGrupalEdit(grupoId, asistencia) {
        const container = document.getElementById('grupal-lista-alumnos');
        container.innerHTML = '<div class="text-center text-muted"><div class="spinner-border spinner-border-sm me-2" role="status"><span class="visually-hidden">Cargando...</span></div>Cargando alumnos...</div>';

        fetch(`/GESTACAD/controllers/tutoriasController.php?action=getAlumnosByGrupo&grupo_id=${grupoId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    // Crear un mapa de alumnos presentes
                    const presentesMap = {};
                    if (asistencia && asistencia.length > 0) {
                        asistencia.forEach(function(a) {
                            if (a.presente == 1) {
                                presentesMap[a.alumno_id] = true;
                            }
                        });
                    }
                    
                    let html = '<div class="list-group list-group-flush">';
                    data.data.forEach(alumno => {
                        const checked = presentesMap[alumno.id_alumno] ? 'checked' : '';
                        html += `
                            <div class="list-group-item">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="asistencia[${alumno.id_alumno}]" value="1" id="asist_${alumno.id_alumno}" ${checked}>
                                    <label class="form-check-label" for="asist_${alumno.id_alumno}">
                                        ${alumno.nombre} ${alumno.apellido_paterno} ${alumno.apellido_materno}
                                        <small class="text-muted d-block">${alumno.matricula}</small>
                                    </label>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                    container.innerHTML = html;
                } else {
                    container.innerHTML = '<div class="alert alert-warning mb-0">No hay alumnos en este grupo</div>';
                }
            })
            .catch(error => {
                console.error('Error loading students:', error);
                container.innerHTML = '<div class="alert alert-danger mb-0">Error al cargar los alumnos</div>';
            });
    }
});
</script>
