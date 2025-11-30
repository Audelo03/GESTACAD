<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Asignatura.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

class AsignaturasController
{

    public $asignatura;

    public function __construct($conn)
    {
        $this->asignatura = new Asignatura($conn);
    }

    public function index()
    {
        echo json_encode($this->asignatura->getAll());
    }

    public function store()
    {
        $data = [
            'clave' => $_POST['clave'],
            'nombre' => $_POST['nombre'],
            'creditos' => $_POST['creditos'],
            'horas_semana' => $_POST['horas_semana'],
            'area' => $_POST['area']
        ];
        if ($this->asignatura->create($data)) {
            echo json_encode(["status" => "ok"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al crear la asignatura"]);
        }
    }

    public function update()
    {
        $id = $_POST['id'];
        $data = [
            'clave' => $_POST['clave'],
            'nombre' => $_POST['nombre'],
            'creditos' => $_POST['creditos'],
            'horas_semana' => $_POST['horas_semana'],
            'area' => $_POST['area']
        ];
        if ($this->asignatura->update($id, $data)) {
            echo json_encode(["status" => "ok"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al actualizar la asignatura"]);
        }
    }

    public function delete()
    {
        try {
            $this->asignatura->delete($_POST['id']);
            echo json_encode(['status' => 'success', 'message' => 'La asignatura ha sido eliminada correctamente.']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }
}

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $controller = new AsignaturasController($conn);

    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        echo json_encode(["error" => "Método $action no encontrado"]);
    }
}
?>