<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/RiesgoDesercion.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

class RiesgoController
{

    public $riesgo;

    public function __construct($conn)
    {
        $this->riesgo = new RiesgoDesercion($conn);
    }

    public function index()
    {
        echo json_encode($this->riesgo->getAll());
    }

    public function store()
    {
        $data = [
            'alumno_id' => $_POST['alumno_id'],
            'periodo_id' => $_POST['periodo_id'],
            'posible' => isset($_POST['posible']) ? 1 : 0,
            'nivel' => $_POST['nivel'],
            'motivo' => $_POST['motivo'],
            'fuente' => $_POST['fuente']
        ];
        if ($this->riesgo->create($data)) {
            echo json_encode(["status" => "ok"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al registrar riesgo"]);
        }
    }
}

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $controller = new RiesgoController($conn);

    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        echo json_encode(["error" => "Método $action no encontrado"]);
    }
}
?>