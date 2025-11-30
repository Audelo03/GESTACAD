<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Canalizacion.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

class CanalizacionController
{

    public $canalizacion;

    public function __construct($conn)
    {
        $this->canalizacion = new Canalizacion($conn);
    }

    public function index()
    {
        echo json_encode($this->canalizacion->getAll());
    }

    public function store()
    {
        $data = [
            'alumno_id' => $_POST['alumno_id'],
            'periodo_id' => $_POST['periodo_id'],
            'area_id' => $_POST['area_id'],
            'usuario_id' => $_SESSION['usuario_id'], // Assuming logged in user creates it
            'observacion' => $_POST['observacion']
        ];
        if ($this->canalizacion->create($data)) {
            echo json_encode(["status" => "ok"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al crear canalización"]);
        }
    }
}

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $controller = new CanalizacionController($conn);

    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        echo json_encode(["error" => "Método $action no encontrado"]);
    }
}
?>