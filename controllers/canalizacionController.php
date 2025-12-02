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
        header('Content-Type: application/json');
        echo json_encode($this->canalizacion->getAll());
    }

    public function store()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(["status" => "error", "message" => "Usuario no autenticado"]);
            return;
        }
        
        $data = [
            'alumno_id' => $_POST['alumno_id'] ?? null,
            'periodo_id' => $_POST['periodo_id'] ?? null,
            'area_id' => $_POST['area_id'] ?? null,
            'usuario_id' => $_SESSION['usuario_id'],
            'observacion' => $_POST['observacion'] ?? ''
        ];
        
        // Validar datos requeridos
        if (empty($data['alumno_id']) || empty($data['periodo_id']) || empty($data['area_id']) || empty($data['observacion'])) {
            echo json_encode(["status" => "error", "message" => "Todos los campos son requeridos"]);
            return;
        }
        
        if ($this->canalizacion->create($data)) {
            echo json_encode(["status" => "ok", "message" => "Canalización registrada exitosamente"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al crear canalización"]);
        }
    }
    
    public function getAreas()
    {
        header('Content-Type: application/json');
        echo json_encode($this->canalizacion->getAreas());
    }
    
    public function getByAlumno()
    {
        header('Content-Type: application/json');
        $alumno_id = isset($_GET['alumno_id']) ? (int)$_GET['alumno_id'] : 0;
        if ($alumno_id > 0) {
            echo json_encode($this->canalizacion->getByAlumno($alumno_id));
        } else {
            echo json_encode([]);
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