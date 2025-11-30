<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Beca.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

class BecasController
{

    public $beca;

    public function __construct($conn)
    {
        $this->beca = new Beca($conn);
    }

    public function index()
    {
        echo json_encode($this->beca->getAll());
    }

    public function store()
    {
        $data = [
            'clave' => $_POST['clave'],
            'nombre' => $_POST['nombre']
        ];
        if ($this->beca->create($data)) {
            echo json_encode(["status" => "ok"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al crear la beca"]);
        }
    }

    public function update()
    {
        $id = $_POST['id'];
        $data = [
            'clave' => $_POST['clave'],
            'nombre' => $_POST['nombre']
        ];
        if ($this->beca->update($id, $data)) {
            echo json_encode(["status" => "ok"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al actualizar la beca"]);
        }
    }

    public function delete()
    {
        try {
            $this->beca->delete($_POST['id']);
            echo json_encode(['status' => 'success', 'message' => 'La beca ha sido eliminada correctamente.']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }
}

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $controller = new BecasController($conn);

    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        echo json_encode(["error" => "Método $action no encontrado"]);
    }
}
?>