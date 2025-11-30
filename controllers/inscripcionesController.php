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
        if (isset($_GET['clase_id'])) {
            echo json_encode($this->inscripcion->getByClase($_GET['clase_id']));
        } else {
            echo json_encode($this->inscripcion->getAll());
        }
    }

    public function store()
    {
        $data = [
            'alumno_id' => $_POST['alumno_id'],
            'clase_id' => $_POST['clase_id']
        ];
        if ($this->inscripcion->create($data)) {
            echo json_encode(["status" => "ok"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al inscribir al alumno"]);
        }
    }

    public function updateCalificaciones()
    {
        $id = $_POST['id'];
        $data = [
            'cal_parcial1' => $_POST['cal_parcial1'],
            'cal_parcial2' => $_POST['cal_parcial2'],
            'cal_parcial3' => $_POST['cal_parcial3'],
            'cal_parcial4' => $_POST['cal_parcial4'],
            'cal_final' => $_POST['cal_final']
        ];
        if ($this->inscripcion->updateCalificaciones($id, $data)) {
            echo json_encode(["status" => "ok"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al actualizar calificaciones"]);
        }
    }

    public function delete()
    {
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