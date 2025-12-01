<?php
// Desactivar mostrar errores en pantalla para evitar que se muestren antes del JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once __DIR__ . '/../models/AsistenciaToken.php';
require_once __DIR__ . '/../config/db.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

class AsistenciaTokenController
{
    private $tokenModel;
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
        $this->tokenModel = new AsistenciaToken($db);
    }

    /**
     * Genera un nuevo token de asistencia
     */
    public function generarToken()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(['success' => false, 'error' => 'No autorizado']);
            return;
        }

        $grupo_id = isset($_POST['grupo_id']) ? (int)$_POST['grupo_id'] : 0;
        $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : date('Y-m-d');
        $tutoria_grupal_id = isset($_POST['tutoria_grupal_id']) ? (int)$_POST['tutoria_grupal_id'] : null;
        $usuario_id = $_SESSION['usuario_id'];

        if ($grupo_id === 0) {
            echo json_encode(['success' => false, 'error' => 'ID de grupo inválido']);
            return;
        }

        // Validar formato de fecha
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            echo json_encode(['success' => false, 'error' => 'Formato de fecha inválido']);
            return;
        }

        $token = $this->tokenModel->crearToken($grupo_id, $fecha, $usuario_id, $tutoria_grupal_id);

        if ($token) {
            // Construir URL completa
            $protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            $basePath = '/GESTACAD';
            $url = $protocolo . '://' . $host . $basePath . '/marcar-asistencia?token=' . $token;

            echo json_encode([
                'success' => true,
                'token' => $token,
                'url' => $url,
                'expira_en' => date('Y-m-d H:i:s', strtotime('+5 minutes'))
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al generar el token']);
        }
    }

    /**
     * Valida un token
     */
    public function validarToken()
    {
        header('Content-Type: application/json');

        $token = isset($_GET['token']) ? $_GET['token'] : '';

        if (empty($token)) {
            echo json_encode(['success' => false, 'error' => 'Token no proporcionado']);
            return;
        }

        $info = $this->tokenModel->obtenerInfoCompleta($token);

        if ($info) {
            echo json_encode([
                'success' => true,
                'data' => $info
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Token inválido o expirado'
            ]);
        }
    }

    /**
     * Marca la asistencia de un alumno
     */
    public function marcarAsistencia()
    {
        // Iniciar output buffering para capturar cualquier salida no deseada
        ob_start();
        
        try {
            header('Content-Type: application/json');

            $token = isset($_POST['token']) ? trim($_POST['token']) : '';
            $alumno_id = isset($_POST['alumno_id']) ? (int)$_POST['alumno_id'] : 0;
            $grupo_id = isset($_POST['grupo_id']) ? (int)$_POST['grupo_id'] : 0;
            $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : '';
            $tutoria_grupal_id = isset($_POST['tutoria_grupal_id']) ? (int)$_POST['tutoria_grupal_id'] : null;

            if (empty($token) || $alumno_id === 0 || $grupo_id === 0 || empty($fecha)) {
                ob_clean();
                echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
                return;
            }

            // Obtener IP del cliente
            $ip_address = AsistenciaToken::obtenerIP();

            // Verificar si la IP está bloqueada
            $ipBloqueada = $this->tokenModel->verificarIPBloqueada($ip_address);
            if ($ipBloqueada) {
                $tiempoRestante = $this->tokenModel->obtenerTiempoRestanteBloqueo($ip_address);
                $minutos = floor($tiempoRestante / 60);
                $segundos = $tiempoRestante % 60;
                ob_clean();
                echo json_encode([
                    'success' => false, 
                    'error' => 'Debes esperar antes de registrar otra asistencia. Tiempo restante: ' . $minutos . ':' . str_pad($segundos, 2, '0', STR_PAD_LEFT),
                    'bloqueado' => true,
                    'tiempo_restante' => $tiempoRestante
                ]);
                return;
            }

            // Validar token
            $tokenInfo = $this->tokenModel->validarToken($token);
            if (!$tokenInfo) {
                ob_clean();
                echo json_encode(['success' => false, 'error' => 'Token inválido o expirado']);
                return;
            }

            // Verificar que el alumno pertenece al grupo del token
            if ($tokenInfo['grupo_id'] != $grupo_id) {
                ob_clean();
                echo json_encode(['success' => false, 'error' => 'El alumno no pertenece a este grupo']);
                return;
            }

            // Verificar que el alumno existe y pertenece al grupo
            @require_once __DIR__ . '/../controllers/alumnoController.php';
            if (!class_exists('AlumnoController')) {
                ob_clean();
                echo json_encode(['success' => false, 'error' => 'Error al cargar el controlador de alumnos']);
                return;
            }
            
            $alumnoController = new AlumnoController($this->conn);
            $alumnos = $alumnoController->getAlumnosByGrupo($grupo_id);
            
            if (!is_array($alumnos)) {
                ob_clean();
                echo json_encode(['success' => false, 'error' => 'Error al obtener la lista de alumnos']);
                return;
            }
            
            $alumnoExiste = false;
            foreach ($alumnos as $alumno) {
                if (isset($alumno['id_alumno']) && $alumno['id_alumno'] == $alumno_id) {
                    $alumnoExiste = true;
                    break;
                }
            }

            if (!$alumnoExiste) {
                ob_clean();
                echo json_encode(['success' => false, 'error' => 'Alumno no encontrado en este grupo']);
                return;
            }

            @require_once __DIR__ . '/../models/TutoriaGrupal.php';
            if (!class_exists('TutoriaGrupal')) {
                ob_clean();
                echo json_encode(['success' => false, 'error' => 'Error al cargar el modelo de tutoría']);
                return;
            }
            
            $tutoriaGrupal = new TutoriaGrupal($this->conn);

            // Si hay una tutoría específica, actualizar su asistencia
            if ($tutoria_grupal_id) {
                // Obtener la tutoría
                $tutoria = $tutoriaGrupal->getById($tutoria_grupal_id);
                if (!$tutoria || !is_array($tutoria)) {
                    ob_clean();
                    echo json_encode(['success' => false, 'error' => 'Tutoría no encontrada']);
                    return;
                }

                // Verificar si ya tiene asistencia registrada
                $asistenciaActual = [];
                if (isset($tutoria['asistencia']) && is_array($tutoria['asistencia'])) {
                    foreach ($tutoria['asistencia'] as $asist) {
                        if (isset($asist['alumno_id']) && isset($asist['presente'])) {
                            $asistenciaActual[$asist['alumno_id']] = $asist['presente'];
                        }
                    }
                }

                // Agregar/actualizar la asistencia del alumno
                $asistenciaActual[$alumno_id] = 1;

                // Actualizar la tutoría con la nueva asistencia
                $data = [
                    'grupo_id' => $tutoria['grupo_id'],
                    'parcial_id' => $tutoria['parcial_id'],
                    'fecha' => $tutoria['fecha'],
                    'actividad_nombre' => $tutoria['actividad_nombre'],
                    'actividad_descripcion' => $tutoria['actividad_descripcion'] ?? '',
                    'evidencia_foto_id' => $tutoria['evidencia_foto_id'] ?? null,
                    'usuario_id' => $tutoria['usuario_id']
                ];

                $resultado = $tutoriaGrupal->update($tutoria_grupal_id, $data, $asistenciaActual);

                if ($resultado) {
                    // Bloquear IP por 1 minuto después de registro exitoso
                    $this->tokenModel->bloquearIP($ip_address, $token);
                    
                    ob_clean();
                    echo json_encode(['success' => true, 'message' => 'Asistencia registrada correctamente']);
                } else {
                    ob_clean();
                    echo json_encode(['success' => false, 'error' => 'Error al registrar la asistencia']);
                }
            } else {
                // Si no hay tutoría específica, crear una nueva o actualizar la del día
                $tutoriaExistente = $tutoriaGrupal->getByGrupoAndDate($grupo_id, $fecha);

                if ($tutoriaExistente && is_array($tutoriaExistente)) {
                    // Actualizar tutoría existente
                    $asistenciaActual = [];
                    if (isset($tutoriaExistente['asistencia']) && is_array($tutoriaExistente['asistencia'])) {
                        foreach ($tutoriaExistente['asistencia'] as $asist) {
                            if (isset($asist['alumno_id']) && isset($asist['presente'])) {
                                $asistenciaActual[$asist['alumno_id']] = $asist['presente'];
                            }
                        }
                    }
                    $asistenciaActual[$alumno_id] = 1;

                    $data = [
                        'grupo_id' => $tutoriaExistente['grupo_id'],
                        'parcial_id' => $tutoriaExistente['parcial_id'],
                        'fecha' => $tutoriaExistente['fecha'],
                        'actividad_nombre' => $tutoriaExistente['actividad_nombre'],
                        'actividad_descripcion' => $tutoriaExistente['actividad_descripcion'] ?? '',
                        'evidencia_foto_id' => $tutoriaExistente['evidencia_foto_id'] ?? null,
                        'usuario_id' => $tutoriaExistente['usuario_id']
                    ];

                    $resultado = $tutoriaGrupal->update($tutoriaExistente['id'], $data, $asistenciaActual);
                } else {
                    // Crear nueva tutoría (necesitamos obtener el parcial activo)
                    // Obtener el parcial activo del periodo actual
                    $parcial_id = 1; // Default
                    try {
                        $sql_parcial = "SELECT id FROM parciales WHERE periodo_id IN (SELECT id FROM periodos_escolares WHERE activo = 1) ORDER BY numero DESC LIMIT 1";
                        $stmt_parcial = $this->conn->prepare($sql_parcial);
                        $stmt_parcial->execute();
                        $parcial_result = $stmt_parcial->fetch(PDO::FETCH_ASSOC);
                        if ($parcial_result) {
                            $parcial_id = $parcial_result['id'];
                        }
                    } catch (Exception $e) {
                        error_log("Error obteniendo parcial activo: " . $e->getMessage());
                        // Usar default de 1
                    }

                    $data = [
                        'grupo_id' => $grupo_id,
                        'parcial_id' => $parcial_id,
                        'fecha' => $fecha,
                        'actividad_nombre' => 'Asistencia registrada por QR',
                        'actividad_descripcion' => 'Asistencia registrada automáticamente mediante código QR',
                        'evidencia_foto_id' => null,
                        'usuario_id' => $tokenInfo['usuario_id']
                    ];

                    $asistencia = [$alumno_id => 1];
                    $tutoria_id = $tutoriaGrupal->create($data, $asistencia);
                    $resultado = $tutoria_id !== false;
                }

                if ($resultado) {
                    // Bloquear IP por 1 minuto después de registro exitoso
                    $this->tokenModel->bloquearIP($ip_address, $token);
                    
                    ob_clean();
                    echo json_encode(['success' => true, 'message' => 'Asistencia registrada correctamente']);
                } else {
                    ob_clean();
                    echo json_encode(['success' => false, 'error' => 'Error al registrar la asistencia']);
                }
            }
        } catch (Exception $e) {
            ob_clean();
            error_log("Error marking attendance: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            echo json_encode(['success' => false, 'error' => 'Error al procesar la solicitud: ' . $e->getMessage()]);
        } catch (Error $e) {
            ob_clean();
            error_log("Fatal error marking attendance: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            echo json_encode(['success' => false, 'error' => 'Error fatal al procesar la solicitud']);
        } finally {
            ob_end_flush();
        }
    }
}

// Procesar acciones
if (isset($_GET['action'])) {
    try {
        $action = $_GET['action'];
        $controller = new AsistenciaTokenController($conn);

        if (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Acción no encontrada']);
        }
    } catch (Exception $e) {
        header('Content-Type: application/json');
        error_log("Error in asistenciaTokenController: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Error al procesar la solicitud: ' . $e->getMessage()]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'No se especificó una acción']);
}

