<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Division.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

class DivisionesController
{

    public $division;

    public function __construct($conn)
    {
        $this->division = new Division($conn);
    }

    public function index()
    {
        echo json_encode($this->division->getAll());
    }

    public function store()
    {
        $data = [
            'nombre' => $_POST['nombre']
        ];
        if ($this->division->create($data)) {
            echo json_encode(["status" => "ok"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al crear la división"]);
        }
    }

    public function update()
    {
        $id = $_POST['id'];
        $data = [
            'nombre' => $_POST['nombre']
        ];
        if ($this->division->update($id, $data)) {
            echo json_encode(["status" => "ok"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al actualizar la división"]);
        }
    }

    public function delete()
    {
        try {
            $this->division->delete($_POST['id']);
            echo json_encode(['status' => 'success', 'message' => 'La división ha sido eliminada correctamente.']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }
}

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $controller = new DivisionesController($conn);

    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        echo json_encode(["error" => "Método $action no encontrado"]);
    }
}
?>