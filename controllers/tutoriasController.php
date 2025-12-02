<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/TutoriaGrupal.php";
require_once __DIR__ . "/../models/TutoriaIndividual.php";
require_once __DIR__ . "/../models/Alumno.php";
require_once __DIR__ . "/../models/PatTutorActividad.php";
require_once __DIR__ . "/../models/ActividadPAT.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

class TutoriasController
{
    private $tutoriaGrupal;
    private $tutoriaIndividual;
    private $alumno;
    private $patTutorActividad;
    private $actividadPAT;
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->tutoriaGrupal = new TutoriaGrupal($conn);
        $this->tutoriaIndividual = new TutoriaIndividual($conn);
        $this->alumno = new Alumno($conn);
        $this->patTutorActividad = new PatTutorActividad($conn);
        $this->actividadPAT = new ActividadPAT($conn);
    }

    /**
     * Create a new group tutoring session
     */
    public function createGrupal()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        try {
            // Handle file upload if present
            $evidencia_foto_id = null;
            if (isset($_FILES['evidencia_foto']) && $_FILES['evidencia_foto']['error'] === UPLOAD_ERR_OK) {
                $evidencia_foto_id = $this->uploadFile($_FILES['evidencia_foto']);
                if (!$evidencia_foto_id) {
                    echo json_encode(['success' => false, 'message' => 'Error al subir la foto de evidencia']);
                    return;
                }
            }

            // Prepare tutoring data
            $data = [
                'grupo_id' => $_POST['grupo_id'],
                'parcial_id' => $_POST['parcial_id'],
                'fecha' => $_POST['fecha'] ?? date('Y-m-d'),
                'actividad_nombre' => $_POST['actividad_nombre'],
                'actividad_descripcion' => $_POST['actividad_descripcion'] ?? '',
                'evidencia_foto_id' => $evidencia_foto_id,
                'usuario_id' => $_SESSION['usuario_id']
            ];

            // Prepare attendance data
            $asistencia = [];
            if (isset($_POST['asistencia']) && is_array($_POST['asistencia'])) {
                foreach ($_POST['asistencia'] as $alumno_id => $presente) {
                    $asistencia[$alumno_id] = (int) $presente;
                }
            }

            // Create the tutoring session
            $tutoria_id = $this->tutoriaGrupal->create($data, $asistencia);

            if ($tutoria_id) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Tutoría grupal creada exitosamente',
                    'tutoria_id' => $tutoria_id
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al crear la tutoría grupal']);
            }

        } catch (Exception $e) {
            error_log("Error in createGrupal: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Update an existing group tutoring session
     */
    public function updateGrupal()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        if (!isset($_POST['id'])) {
            echo json_encode(['success' => false, 'message' => 'ID de tutoría no proporcionado']);
            return;
        }

        try {
            // Validate required fields
            if (!isset($_POST['grupo_id']) || empty($_POST['grupo_id'])) {
                echo json_encode(['success' => false, 'message' => 'ID de grupo no proporcionado']);
                return;
            }
            
            if (!isset($_POST['parcial_id']) || empty($_POST['parcial_id'])) {
                echo json_encode(['success' => false, 'message' => 'ID de parcial no proporcionado']);
                return;
            }
            
            if (!isset($_POST['actividad_nombre']) || empty(trim($_POST['actividad_nombre']))) {
                echo json_encode(['success' => false, 'message' => 'El nombre de la actividad es requerido']);
                return;
            }
            
            if (!isset($_SESSION['usuario_id'])) {
                echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
                return;
            }
            
            // Get existing tutoring session to preserve evidencia_foto_id if not changed
            $existing_tutoria = $this->tutoriaGrupal->getById($_POST['id']);
            if (!$existing_tutoria) {
                echo json_encode(['success' => false, 'message' => 'Tutoría no encontrada']);
                return;
            }
            
            // Handle file upload if provided
            // Preserve existing evidencia_foto_id by default (can be null)
            $evidencia_foto_id = isset($existing_tutoria['evidencia_foto_id']) && $existing_tutoria['evidencia_foto_id'] !== '' 
                ? $existing_tutoria['evidencia_foto_id'] 
                : null;
            
            if (isset($_POST['evidencia_foto_id']) && !empty($_POST['evidencia_foto_id'])) {
                $evidencia_foto_id = (int)$_POST['evidencia_foto_id'];
            }
            
            // If a new file is uploaded, process it
            if (isset($_FILES['evidencia_foto']) && $_FILES['evidencia_foto']['error'] === UPLOAD_ERR_OK) {
                $new_file_id = $this->uploadFile($_FILES['evidencia_foto']);
                if ($new_file_id) {
                    $evidencia_foto_id = $new_file_id;
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al subir la foto de evidencia']);
                    return;
                }
            }

            // Prepare tutoring data
            $data = [
                'grupo_id' => (int)$_POST['grupo_id'],
                'parcial_id' => (int)$_POST['parcial_id'],
                'fecha' => $_POST['fecha'] ?? date('Y-m-d'),
                'actividad_nombre' => trim($_POST['actividad_nombre']),
                'actividad_descripcion' => isset($_POST['actividad_descripcion']) ? trim($_POST['actividad_descripcion']) : '',
                'evidencia_foto_id' => $evidencia_foto_id,
                'usuario_id' => (int)$_SESSION['usuario_id']
            ];

            // Prepare attendance data
            $asistencia = [];
            if (isset($_POST['asistencia']) && is_array($_POST['asistencia'])) {
                foreach ($_POST['asistencia'] as $alumno_id => $presente) {
                    $asistencia[$alumno_id] = (int) $presente;
                }
            }

            // Update the tutoring session
            $success = $this->tutoriaGrupal->update($_POST['id'], $data, $asistencia);

            if ($success) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Tutoría grupal actualizada exitosamente'
                ]);
            } else {
                // Get last error from database
                $errorInfo = $this->conn->errorInfo();
                $errorMessage = 'Error al actualizar la tutoría grupal';
                if ($errorInfo && isset($errorInfo[2]) && !empty($errorInfo[2])) {
                    $errorMessage .= ': ' . $errorInfo[2];
                }
                error_log("Update failed. ID: " . $_POST['id']);
                error_log("Update failed. Data: " . print_r($data, true));
                error_log("Update failed. Attendance: " . print_r($asistencia, true));
                error_log("Update failed. Error info: " . print_r($errorInfo, true));
                echo json_encode(['success' => false, 'message' => $errorMessage]);
            }

        } catch (Exception $e) {
            error_log("Error in updateGrupal: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Create a new individual tutoring session
     */
    public function createIndividual()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        try {
            $data = [
                'alumno_id' => $_POST['alumno_id'],
                'grupo_id' => $_POST['grupo_id'],
                'fecha' => $_POST['fecha'] ?? date('Y-m-d'),
                'motivo' => $_POST['motivo'] ?? '',
                'acciones' => $_POST['acciones'] ?? '',
                'usuario_id' => $_SESSION['usuario_id']
            ];

            $tutoria_id = $this->tutoriaIndividual->create($data);

            if ($tutoria_id) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Tutoría individual creada exitosamente',
                    'tutoria_id' => $tutoria_id
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al crear la tutoría individual']);
            }

        } catch (Exception $e) {
            error_log("Error in createIndividual: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Get group tutoring sessions by group
     */
    public function getGrupalesByGrupo()
    {
        header('Content-Type: application/json');

        if (!isset($_GET['grupo_id'])) {
            echo json_encode(['success' => false, 'message' => 'ID de grupo no proporcionado']);
            return;
        }

        try {
            $tutorias = $this->tutoriaGrupal->getByGrupo($_GET['grupo_id']);
            echo json_encode(['success' => true, 'data' => $tutorias]);
        } catch (Exception $e) {
            error_log("Error in getGrupalesByGrupo: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Get individual tutoring sessions by group
     */
    public function getIndividualesByGrupo()
    {
        header('Content-Type: application/json');

        if (!isset($_GET['grupo_id'])) {
            echo json_encode(['success' => false, 'message' => 'ID de grupo no proporcionado']);
            return;
        }

        try {
            $tutorias = $this->tutoriaIndividual->getByGrupo($_GET['grupo_id']);
            echo json_encode(['success' => true, 'data' => $tutorias]);
        } catch (Exception $e) {
            error_log("Error in getIndividualesByGrupo: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Get a specific group tutoring session by ID
     */
    public function getGrupalById()
    {
        header('Content-Type: application/json');

        if (!isset($_GET['id'])) {
            echo json_encode(['success' => false, 'message' => 'ID de tutoría no proporcionado']);
            return;
        }

        try {
            $tutoria = $this->tutoriaGrupal->getById($_GET['id']);
            if ($tutoria) {
                echo json_encode(['success' => true, 'data' => $tutoria]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Tutoría no encontrada']);
            }
        } catch (Exception $e) {
            error_log("Error in getGrupalById: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Get a group tutoring session by grupo_id and fecha
     */
    public function getGrupalByGrupoAndDate()
    {
        header('Content-Type: application/json');

        if (!isset($_GET['grupo_id']) || !isset($_GET['fecha'])) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }

        try {
            $tutoria = $this->tutoriaGrupal->getByGrupoAndDate($_GET['grupo_id'], $_GET['fecha']);
            if ($tutoria) {
                echo json_encode(['success' => true, 'data' => $tutoria]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se encontró tutoría para esta fecha']);
            }
        } catch (Exception $e) {
            error_log("Error in getGrupalByGrupoAndDate: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Get students by group for individual tutoring
     */
    public function getAlumnosByGrupo()
    {
        header('Content-Type: application/json');

        if (!isset($_GET['grupo_id'])) {
            echo json_encode(['success' => false, 'message' => 'ID de grupo no proporcionado']);
            return;
        }

        try {
            $sql = "SELECT id_alumno, matricula, nombre, apellido_paterno, apellido_materno 
                    FROM alumnos 
                    WHERE grupos_id_grupo = :grupo_id 
                    ORDER BY apellido_paterno, apellido_materno, nombre";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':grupo_id', $_GET['grupo_id'], PDO::PARAM_INT);
            $stmt->execute();
            $alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'data' => $alumnos]);
        } catch (Exception $e) {
            error_log("Error in getAlumnosByGrupo: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a group tutoring session
     */
    public function deleteGrupal()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }

        try {
            if ($this->tutoriaGrupal->delete($_POST['id'])) {
                echo json_encode(['success' => true, 'message' => 'Tutoría grupal eliminada exitosamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al eliminar la tutoría grupal']);
            }
        } catch (Exception $e) {
            error_log("Error in deleteGrupal: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete an individual tutoring session
     */
    public function deleteIndividual()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }

        try {
            if ($this->tutoriaIndividual->delete($_POST['id'])) {
                echo json_encode(['success' => true, 'message' => 'Tutoría individual eliminada exitosamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al eliminar la tutoría individual']);
            }
        } catch (Exception $e) {
            error_log("Error in deleteIndividual: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Upload a file and return its ID
     * @param array $file - The uploaded file from $_FILES
     * @return int|false - Returns the file ID or false on failure
     */
    private function uploadFile($file)
    {
        try {
            // Validate file type (only images)
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($file['type'], $allowed_types)) {
                return false;
            }

            // Validate file size (max 5MB)
            if ($file['size'] > 5 * 1024 * 1024) {
                return false;
            }

            // Create upload directory if it doesn't exist
            $upload_dir = __DIR__ . '/../uploads/tutorias/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid('tutoria_', true) . '.' . $extension;
            $filepath = $upload_dir . $filename;

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                return false;
            }

            // Insert file record into database
            $sql = "INSERT INTO files (ruta, tipo_mime, tamano, hash) 
                    VALUES (:ruta, :tipo_mime, :tamano, :hash)";

            $stmt = $this->conn->prepare($sql);
            $ruta = 'uploads/tutorias/' . $filename;
            $hash = hash_file('sha256', $filepath);

            $stmt->bindParam(':ruta', $ruta);
            $stmt->bindParam(':tipo_mime', $file['type']);
            $stmt->bindParam(':tamano', $file['size'], PDO::PARAM_INT);
            $stmt->bindParam(':hash', $hash);

            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            }

            return false;

        } catch (Exception $e) {
            error_log("Error uploading file: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all PAT activities for the current tutor
     */
    public function getPatTutorActividades()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
            return;
        }

        try {
            $actividades = $this->patTutorActividad->getByUsuario($_SESSION['usuario_id']);
            echo json_encode(['success' => true, 'data' => $actividades]);
        } catch (Exception $e) {
            error_log("Error in getPatTutorActividades: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Create a new PAT activity for the current tutor
     */
    public function createPatTutorActividad()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
            return;
        }

        try {
            if (!isset($_POST['nombre']) || empty(trim($_POST['nombre']))) {
                echo json_encode(['success' => false, 'message' => 'El nombre de la actividad es requerido']);
                return;
            }

            $data = [
                'usuario_id' => $_SESSION['usuario_id'],
                'nombre' => trim($_POST['nombre']),
                'descripcion' => isset($_POST['descripcion']) ? trim($_POST['descripcion']) : ''
            ];

            $id = $this->patTutorActividad->create($data);

            if ($id) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Actividad PAT creada exitosamente',
                    'id' => $id
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al crear la actividad PAT']);
            }
        } catch (Exception $e) {
            error_log("Error in createPatTutorActividad: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Update an existing PAT activity
     */
    public function updatePatTutorActividad()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
            return;
        }

        if (!isset($_POST['id'])) {
            echo json_encode(['success' => false, 'message' => 'ID de actividad no proporcionado']);
            return;
        }

        try {
            if (!isset($_POST['nombre']) || empty(trim($_POST['nombre']))) {
                echo json_encode(['success' => false, 'message' => 'El nombre de la actividad es requerido']);
                return;
            }

            $data = [
                'usuario_id' => $_SESSION['usuario_id'],
                'nombre' => trim($_POST['nombre']),
                'descripcion' => isset($_POST['descripcion']) ? trim($_POST['descripcion']) : ''
            ];

            $success = $this->patTutorActividad->update($_POST['id'], $data);

            if ($success) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Actividad PAT actualizada exitosamente'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar la actividad PAT']);
            }
        } catch (Exception $e) {
            error_log("Error in updatePatTutorActividad: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a PAT activity
     */
    public function deletePatTutorActividad()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
            return;
        }

        if (!isset($_POST['id'])) {
            echo json_encode(['success' => false, 'message' => 'ID de actividad no proporcionado']);
            return;
        }

        try {
            $success = $this->patTutorActividad->delete($_POST['id'], $_SESSION['usuario_id']);

            if ($success) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Actividad PAT eliminada exitosamente'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al eliminar la actividad PAT']);
            }
        } catch (Exception $e) {
            error_log("Error in deletePatTutorActividad: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Get all general PAT activities (catalog)
     */
    public function getPatGeneralActividades()
    {
        header('Content-Type: application/json');

        try {
            $actividades = $this->actividadPAT->getAll();
            echo json_encode(['success' => true, 'data' => $actividades]);
        } catch (Exception $e) {
            error_log("Error in getPatGeneralActividades: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Copy a general PAT activity to tutor's personal PAT
     */
    public function copiarPatATutor()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
            return;
        }

        if (!isset($_POST['actividad_id'])) {
            echo json_encode(['success' => false, 'message' => 'ID de actividad no proporcionado']);
            return;
        }

        try {
            // Get the general PAT activity
            $actividades = $this->actividadPAT->getAll();
            $actividad = null;
            foreach ($actividades as $act) {
                if ($act['id'] == $_POST['actividad_id']) {
                    $actividad = $act;
                    break;
                }
            }

            if (!$actividad) {
                echo json_encode(['success' => false, 'message' => 'Actividad no encontrada']);
                return;
            }

            // Check if tutor already has this activity
            $tutorActividades = $this->patTutorActividad->getByUsuario($_SESSION['usuario_id']);
            foreach ($tutorActividades as $tutorAct) {
                if ($tutorAct['nombre'] === $actividad['nombre']) {
                    echo json_encode(['success' => false, 'message' => 'Ya tienes esta actividad en tu PAT']);
                    return;
                }
            }

            // Copy to tutor's PAT
            $data = [
                'usuario_id' => $_SESSION['usuario_id'],
                'nombre' => $actividad['nombre'],
                'descripcion' => $actividad['descripcion'] ?? ''
            ];

            $id = $this->patTutorActividad->create($data);

            if ($id) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Actividad añadida a tu PAT exitosamente',
                    'id' => $id
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al añadir la actividad']);
            }
        } catch (Exception $e) {
            error_log("Error in copiarPatATutor: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}

// Handle requests
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $controller = new TutoriasController($conn);

    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        echo json_encode(['success' => false, 'message' => "Método $action no encontrado"]);
    }
}
?>