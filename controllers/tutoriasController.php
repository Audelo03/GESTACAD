<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/ActividadPAT.php";
require_once __DIR__ . "/../models/TutoriaEvento.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

class TutoriasController
{

    public $pat;
    public $evento;

    public function __construct($conn)
    {
        $this->pat = new ActividadPAT($conn);
        $this->evento = new TutoriaEvento($conn);
    }

    // PAT Methods
    public function indexPAT()
    {
        echo json_encode($this->pat->getAll());
    }

    public function storePAT()
    {
        $data = [
            'carrera_id' => $_POST['carrera_id'] ?: null,
            'grupo_id' => $_POST['grupo_id'] ?: null,
            'parcial_id' => $_POST['parcial_id'],
            'sesion_num' => $_POST['sesion_num'],
            'nombre' => $_POST['nombre'],
            'descripcion' => $_POST['descripcion']
        ];
        if ($this->pat->create($data)) {
            echo json_encode(["status" => "ok"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al crear actividad PAT"]);
        }
    }

    public function updatePAT()
    {
        $id = $_POST['id'];
        $data = [
            'carrera_id' => $_POST['carrera_id'] ?: null,
            'grupo_id' => $_POST['grupo_id'] ?: null,
            'parcial_id' => $_POST['parcial_id'],
            'sesion_num' => $_POST['sesion_num'],
            'nombre' => $_POST['nombre'],
            'descripcion' => $_POST['descripcion']
        ];
        if ($this->pat->update($id, $data)) {
            echo json_encode(["status" => "ok"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al actualizar actividad PAT"]);
        }
    }

    public function deletePAT()
    {
        try {
            $this->pat->delete($_POST['id']);
            echo json_encode(['status' => 'success', 'message' => 'Actividad PAT eliminada.']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // Evento Methods
    public function indexEventos()
    {
        echo json_encode($this->evento->getAll());
    }

    public function storeEvento()
    {
        $data = [
            'grupo_id' => $_POST['grupo_id'],
            'parcial_id' => $_POST['parcial_id'],
            'sesion_num' => $_POST['sesion_num'],
            'fecha' => $_POST['fecha'],
            'tipo' => $_POST['tipo'],
            'actividad_id' => $_POST['actividad_id'] ?: null,
            'actividad_nombre' => $_POST['actividad_nombre'],
            'actividad_descripcion' => $_POST['actividad_descripcion']
        ];
        if ($this->evento->create($data)) {
            echo json_encode(["status" => "ok"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al crear evento de tutoría"]);
        }
    }

    public function deleteEvento()
    {
        try {
            $this->evento->delete($_POST['id']);
            echo json_encode(['status' => 'success', 'message' => 'Evento eliminado.']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $controller = new TutoriasController($conn);

    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        echo json_encode(["error" => "Método $action no encontrado"]);
    }
}
?>