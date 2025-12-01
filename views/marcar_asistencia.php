<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/AsistenciaToken.php';
require_once __DIR__ . '/../models/TutoriaGrupal.php';
require_once __DIR__ . '/../controllers/alumnoController.php';

$page_title = "Marcar Asistencia";

// Obtener token de la URL
$token = isset($_GET['token']) ? trim($_GET['token']) : '';

if (empty($token)) {
    http_response_code(400);
    die("Token no proporcionado");
}

// Validar token
$tokenModel = new AsistenciaToken($conn);
$tokenInfo = $tokenModel->obtenerInfoCompleta($token);

if (!$tokenInfo) {
    http_response_code(404);
    die("Token inválido o expirado. Por favor, solicita un nuevo código QR al docente.");
}

// Obtener alumnos del grupo
$alumnoController = new AlumnoController($conn);
$alumnos = $alumnoController->getAlumnosByGrupo($tokenInfo['grupo_id']);

// Verificar si ya existe una tutoría grupal para esta fecha
$tutoriaGrupal = new TutoriaGrupal($conn);
$tutoriaExistente = null;

if ($tokenInfo['tutoria_grupal_id']) {
    // Si hay una tutoría específica, obtenerla
    $tutoriaData = $tutoriaGrupal->getById($tokenInfo['tutoria_grupal_id']);
    if ($tutoriaData) {
        $tutoriaExistente = $tutoriaData;
    }
}

// Obtener asistencias ya registradas si existe la tutoría
$asistenciasRegistradas = [];
if ($tutoriaExistente && isset($tutoriaExistente['asistencia'])) {
    foreach ($tutoriaExistente['asistencia'] as $asist) {
        if ($asist['presente'] == 1) {
            $asistenciasRegistradas[] = $asist['alumno_id'];
        }
    }
}

include 'objects/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h3 class="mb-0">
                        <i class="bi bi-check-circle me-2"></i>
                        Marcar Asistencia
                    </h3>
                </div>
                <div class="card-body p-4">
                    <!-- Información del grupo -->
                    <div class="alert alert-info mb-4">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-info-circle fs-4 me-3"></i>
                            <div>
                                <strong><?= htmlspecialchars($tokenInfo['grupo_nombre']) ?></strong><br>
                                <small>Fecha: <?= htmlspecialchars(date('d/m/Y', strtotime($tokenInfo['fecha']))) ?></small><br>
                                <small class="text-danger">
                                    <i class="bi bi-clock me-1"></i>
                                    Este código expira en: <?= date('H:i', strtotime($tokenInfo['expira_en'])) ?>
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Buscador de alumno -->
                    <div class="mb-4">
                        <label for="buscadorAlumno" class="form-label fw-bold">
                            <i class="bi bi-search me-2"></i>
                            Busca tu matrícula o nombre:
                        </label>
                        <input type="text" 
                               class="form-control form-control-lg" 
                               id="buscadorAlumno" 
                               placeholder="Escribe tu matrícula o nombre completo..."
                               autocomplete="off">
                        <div id="resultadosBusqueda" class="mt-3"></div>
                    </div>

                    <!-- Lista de alumnos (oculta inicialmente) -->
                    <div id="listaAlumnos" class="d-none">
                        <h5 class="mb-3">Selecciona tu nombre:</h5>
                        <div class="list-group" id="listaAlumnosContainer"></div>
                    </div>

                    <!-- Mensaje de éxito/error -->
                    <div id="mensajeResultado" class="mt-4"></div>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="card mt-3 border-0 bg-light">
                <div class="card-body text-center">
                    <small class="text-muted">
                        <i class="bi bi-shield-check me-1"></i>
                        Tu asistencia se registrará de forma segura. Solo puedes marcar tu propia asistencia.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const token = <?= json_encode($token) ?>;
    const alumnos = <?= json_encode($alumnos) ?>;
    const asistenciasRegistradas = <?= json_encode($asistenciasRegistradas) ?>;
    const tutoriaGrupalId = <?= json_encode($tokenInfo['tutoria_grupal_id']) ?>;
    const grupoId = <?= json_encode($tokenInfo['grupo_id']) ?>;
    const fecha = <?= json_encode($tokenInfo['fecha']) ?>;
    
    const buscador = document.getElementById('buscadorAlumno');
    const resultados = document.getElementById('resultadosBusqueda');
    const listaAlumnos = document.getElementById('listaAlumnos');
    const listaAlumnosContainer = document.getElementById('listaAlumnosContainer');
    const mensajeResultado = document.getElementById('mensajeResultado');
    
    let timeout = null;
    
    // Función para buscar alumnos
    function buscarAlumnos(termino) {
        if (termino.length < 2) {
            resultados.innerHTML = '';
            listaAlumnos.classList.add('d-none');
            return;
        }
        
        const terminoLower = termino.toLowerCase();
        const coincidencias = alumnos.filter(alumno => {
            const nombreCompleto = `${alumno.nombre_completo} ${alumno.matricula}`.toLowerCase();
            return nombreCompleto.includes(terminoLower) || 
                   alumno.matricula.toLowerCase().includes(terminoLower);
        });
        
        if (coincidencias.length === 0) {
            resultados.innerHTML = '<div class="alert alert-warning">No se encontraron coincidencias</div>';
            listaAlumnos.classList.add('d-none');
            return;
        }
        
        // Mostrar lista de alumnos
        listaAlumnosContainer.innerHTML = '';
        coincidencias.forEach(alumno => {
            const yaRegistrado = asistenciasRegistradas.includes(parseInt(alumno.id_alumno));
            const listItem = document.createElement('button');
            listItem.type = 'button';
            listItem.className = `list-group-item list-group-item-action ${yaRegistrado ? 'list-group-item-success' : ''}`;
            listItem.innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>${alumno.nombre_completo}</strong><br>
                        <small class="text-muted">Matrícula: ${alumno.matricula}</small>
                    </div>
                    ${yaRegistrado ? '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Ya registrado</span>' : '<i class="bi bi-arrow-right-circle text-primary"></i>'}
                </div>
            `;
            
            if (!yaRegistrado) {
                listItem.addEventListener('click', () => marcarAsistencia(alumno));
            }
            
            listaAlumnosContainer.appendChild(listItem);
        });
        
        listaAlumnos.classList.remove('d-none');
        resultados.innerHTML = '';
    }
    
    // Event listener para el buscador
    buscador.addEventListener('input', function(e) {
        clearTimeout(timeout);
        const termino = e.target.value.trim();
        timeout = setTimeout(() => {
            buscarAlumnos(termino);
        }, 300);
    });
    
    // Función para verificar bloqueo local
    function verificarBloqueoLocal() {
        const bloqueoKey = 'asistencia_bloqueo';
        const bloqueoData = localStorage.getItem(bloqueoKey);
        
        if (bloqueoData) {
            const bloqueo = JSON.parse(bloqueoData);
            const ahora = Date.now();
            const tiempoRestante = bloqueo.expiraEn - ahora;
            
            if (tiempoRestante > 0) {
                return tiempoRestante;
            } else {
                // Bloqueo expirado, limpiar
                localStorage.removeItem(bloqueoKey);
            }
        }
        
        return 0;
    }
    
    // Función para establecer bloqueo local
    function establecerBloqueoLocal() {
        const bloqueoKey = 'asistencia_bloqueo';
        const expiraEn = Date.now() + (5 * 60 * 1000); // 5 minutos
        localStorage.setItem(bloqueoKey, JSON.stringify({ expiraEn: expiraEn }));
    }
    
    // Función para marcar asistencia
    async function marcarAsistencia(alumno) {
        // Verificar bloqueo local primero
        const tiempoRestanteLocal = verificarBloqueoLocal();
        if (tiempoRestanteLocal > 0) {
            const minutos = Math.floor(tiempoRestanteLocal / 60000);
            const segundos = Math.floor((tiempoRestanteLocal % 60000) / 1000);
            await Swal.fire({
                title: 'Espera un momento',
                html: `
                    <div class="text-center">
                        <i class="bi bi-clock-history text-warning" style="font-size: 3rem;"></i>
                        <p class="mt-3 mb-0">Debes esperar antes de registrar otra asistencia.</p>
                        <p class="mt-2 fw-bold text-primary">Tiempo restante: ${minutos}:${segundos.toString().padStart(2, '0')}</p>
                    </div>
                `,
                icon: 'warning',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Entendido'
            });
            return;
        }
        
        // Confirmar con SweetAlert
        const result = await Swal.fire({
            title: '¿Confirmas tu identidad?',
            html: `
                <div class="text-center">
                    <p class="mb-2">¿Confirmas que eres:</p>
                    <p class="fw-bold text-primary mb-1">${alumno.nombre_completo}</p>
                    <p class="text-muted small">Matrícula: ${alumno.matricula}</p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-check-circle me-1"></i> Sí, soy yo',
            cancelButtonText: '<i class="bi bi-x-circle me-1"></i> Cancelar',
            reverseButtons: true
        });
        
        if (!result.isConfirmed) {
            return;
        }
        
        // Deshabilitar botones
        const botones = listaAlumnosContainer.querySelectorAll('button');
        botones.forEach(btn => btn.disabled = true);
        
        // Mostrar loading con SweetAlert
        Swal.fire({
            title: 'Registrando asistencia...',
            text: 'Por favor espera un momento',
            icon: 'info',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        try {
            const formData = new FormData();
            formData.append('token', token);
            formData.append('alumno_id', alumno.id_alumno);
            formData.append('grupo_id', grupoId);
            formData.append('fecha', fecha);
            if (tutoriaGrupalId) {
                formData.append('tutoria_grupal_id', tutoriaGrupalId);
            }
            
            const response = await fetch('/GESTACAD/controllers/asistenciaTokenController.php?action=marcarAsistencia', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            // Cerrar el loading
            Swal.close();
            
            if (result.success) {
                // Establecer bloqueo local
                establecerBloqueoLocal();
                
                // Mostrar éxito con SweetAlert
                await Swal.fire({
                    title: '¡Asistencia registrada!',
                    html: `
                        <div class="text-center">
                            <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                            <p class="mt-3 mb-0">Tu asistencia ha sido registrada correctamente.</p>
                            <p class="mt-2 text-muted small">Debes esperar 5 minutos antes de registrar otra asistencia.</p>
                        </div>
                    `,
                    icon: 'success',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Aceptar',
                    timer: 3000,
                    timerProgressBar: true
                });
                
                // Marcar como registrado en la lista
                asistenciasRegistradas.push(parseInt(alumno.id_alumno));
                
                // Actualizar la lista
                buscarAlumnos(buscador.value);
                
                // Limpiar buscador después de 2 segundos
                setTimeout(() => {
                    buscador.value = '';
                    resultados.innerHTML = '';
                    listaAlumnos.classList.add('d-none');
                }, 2000);
            } else {
                // Verificar si es un error de bloqueo
                if (result.bloqueado && result.tiempo_restante) {
                    const minutos = Math.floor(result.tiempo_restante / 60);
                    const segundos = result.tiempo_restante % 60;
                    
                    // Actualizar bloqueo local
                    establecerBloqueoLocal();
                    
                    await Swal.fire({
                        title: 'Espera un momento',
                        html: `
                            <div class="text-center">
                                <i class="bi bi-clock-history text-warning" style="font-size: 3rem;"></i>
                                <p class="mt-3 mb-0">${result.error}</p>
                                <p class="mt-2 fw-bold text-primary">Tiempo restante: ${minutos}:${segundos.toString().padStart(2, '0')}</p>
                            </div>
                        `,
                        icon: 'warning',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Entendido'
                    });
                } else {
                    // Mostrar error con SweetAlert
                    await Swal.fire({
                        title: 'Error',
                        text: result.error || 'No se pudo registrar la asistencia. Intenta nuevamente.',
                        icon: 'error',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Aceptar'
                    });
                }
                
                // Rehabilitar botones
                botones.forEach(btn => btn.disabled = false);
            }
        } catch (error) {
            console.error('Error:', error);
            
            // Cerrar el loading si está abierto
            Swal.close();
            
            // Mostrar error de conexión con SweetAlert
            await Swal.fire({
                title: 'Error de conexión',
                text: 'No se pudo conectar con el servidor. Verifica tu conexión e intenta nuevamente.',
                icon: 'error',
                confirmButtonColor: '#d33',
                confirmButtonText: 'Aceptar'
            });
            
            // Rehabilitar botones
            botones.forEach(btn => btn.disabled = false);
        }
    }
    
    // Verificar expiración del token cada minuto
    setInterval(() => {
        const expiraEn = new Date(<?= json_encode($tokenInfo['expira_en']) ?>);
        const ahora = new Date();
        
        if (ahora >= expiraEn) {
            mensajeResultado.innerHTML = `
                <div class="alert alert-danger">
                    <h5><i class="bi bi-clock-history me-2"></i>Token expirado</h5>
                    <p class="mb-0">Este código QR ha expirado. Por favor, solicita un nuevo código al docente.</p>
                </div>
            `;
            buscador.disabled = true;
            listaAlumnos.classList.add('d-none');
        }
    }, 60000); // Verificar cada minuto
});
</script>

<?php include 'objects/footer.php'; ?>

