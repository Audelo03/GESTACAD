<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/TutoriaGrupal.php";
require_once __DIR__ . "/../models/TutoriaIndividual.php";
require_once __DIR__ . "/../models/Alumno.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

class TutoriasController
{
    private $tutoriaGrupal;
    private $tutoriaIndividual;
    private $alumno;
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->tutoriaGrupal = new TutoriaGrupal($conn);
        $this->tutoriaIndividual = new TutoriaIndividual($conn);
        $this->alumno = new Alumno($conn);
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
                'motivo' => $_POST['motivo'],
                'acciones' => $_POST['acciones'],
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