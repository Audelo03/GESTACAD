<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Periodo.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

class PeriodosController
{

    public $periodo;

    public function __construct($conn)
    {
        $this->periodo = new Periodo($conn);
    }

    public function index()
    {
        echo json_encode($this->periodo->getAll());
    }

    public function store()
    {
        $data = [
            'nombre' => $_POST['nombre'],
            'fecha_inicio' => $_POST['fecha_inicio'],
            'fecha_fin' => $_POST['fecha_fin'],
            'activo' => isset($_POST['activo']) ? 1 : 0
        ];
        if ($this->periodo->create($data)) {
            echo json_encode(["status" => "ok"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al crear el periodo"]);
        }
    }

    public function update()
    {
        $id = $_POST['id'];
        $data = [
            'nombre' => $_POST['nombre'],
            'fecha_inicio' => $_POST['fecha_inicio'],
            'fecha_fin' => $_POST['fecha_fin'],
            'activo' => isset($_POST['activo']) ? 1 : 0
        ];
        if ($this->periodo->update($id, $data)) {
            echo json_encode(["status" => "ok"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al actualizar el periodo"]);
        }
    }

    public function delete()
    {
        try {
            $this->periodo->delete($_POST['id']);
            echo json_encode(['status' => 'success', 'message' => 'El periodo ha sido eliminado correctamente.']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }
}

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $controller = new PeriodosController($conn);

    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        echo json_encode(["error" => "Método $action no encontrado"]);
    }
}
?>