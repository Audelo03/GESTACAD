<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Inscripcion.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

class InscripcionesController
{

    public $inscripcion;

    public function __construct($conn)
    {
        $this->inscripcion = new Inscripcion($conn);
    }

    public function index()
    {
        header('Content-Type: application/json');
        if (isset($_GET['alumno_id'])) {
            echo json_encode($this->inscripcion->getByAlumno($_GET['alumno_id']));
        } elseif (isset($_GET['clase_id'])) {
            echo json_encode($this->inscripcion->getByClase($_GET['clase_id']));
        } else {
            echo json_encode($this->inscripcion->getAll());
        }
        exit;
    }

    public function store()
    {
        header('Content-Type: application/json');
        try {
            if (!isset($_POST['alumno_id']) || !isset($_POST['clase_id'])) {
                echo json_encode(["status" => "error", "message" => "Datos incompletos"]);
                exit;
            }
            
            $data = [
                'alumno_id' => $_POST['alumno_id'],
                'clase_id' => $_POST['clase_id']
            ];
            if ($this->inscripcion->create($data)) {
                echo json_encode(["status" => "ok"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Error al inscribir al alumno"]);
            }
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        } catch (Error $e) {
            echo json_encode(["status" => "error", "message" => "Error del sistema: " . $e->getMessage()]);
        }
        exit;
    }

    public function updateEstados()
    {
        header('Content-Type: application/json');
        try {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);

            if (!isset($data['updates']) || !is_array($data['updates'])) {
                echo json_encode(["status" => "error", "message" => "Datos inválidos"]);
                exit;
            }

            $updates = [];
            foreach ($data['updates'] as $update) {
                if (!isset($update['id']) || !isset($update['estado']) || !isset($update['parcial'])) {
                    echo json_encode(["status" => "error", "message" => "Formato de actualización inválido. Se requiere: id, estado y parcial"]);
                    exit;
                }
                $updates[] = [
                    'id' => $update['id'],
                    'estado' => $update['estado'],
                    'parcial' => $update['parcial']
                ];
            }

            if ($this->inscripcion->updateEstadosMasivo($updates)) {
                echo json_encode(["status" => "ok"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Error al actualizar estados"]);
            }
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        } catch (Error $e) {
            echo json_encode(["status" => "error", "message" => "Error del sistema: " . $e->getMessage()]);
        }
        exit;
    }

    public function updateCalificaciones()
    {
        header('Content-Type: application/json');
        try {
            if (!isset($_POST['id'])) {
                echo json_encode(["status" => "error", "message" => "ID de inscripción requerido"]);
                exit;
            }
            
            $id = $_POST['id'];
            $data = [
                'cal_parcial1' => $_POST['cal_parcial1'] ?? null,
                'cal_parcial2' => $_POST['cal_parcial2'] ?? null,
                'cal_parcial3' => $_POST['cal_parcial3'] ?? null,
                'cal_parcial4' => $_POST['cal_parcial4'] ?? null,
                'cal_final' => $_POST['cal_final'] ?? null
            ];
            
            if ($this->inscripcion->updateCalificaciones($id, $data)) {
                echo json_encode(["status" => "ok", "message" => "Calificaciones actualizadas correctamente"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Error al actualizar calificaciones"]);
            }
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        } catch (Error $e) {
            echo json_encode(["status" => "error", "message" => "Error del sistema: " . $e->getMessage()]);
        }
        exit;
    }

    public function delete()
    {
        header('Content-Type: application/json');
        try {
            $this->inscripcion->delete($_POST['id']);
            echo json_encode(['status' => 'success', 'message' => 'La inscripción ha sido dada de baja.']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }
}

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $controller = new InscripcionesController($conn);

    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        echo json_encode(["error" => "Método $action no encontrado"]);
    }
}
?>