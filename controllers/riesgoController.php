<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/RiesgoDesercion.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

class RiesgoController
{
    public $riesgo;
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
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

    public function toggle()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(["status" => "error", "message" => "Usuario no autenticado"]);
            return;
        }
        
        $alumno_id = isset($_POST['alumno_id']) ? (int)$_POST['alumno_id'] : 0;
        $periodo_id = isset($_POST['periodo_id']) ? (int)$_POST['periodo_id'] : null;
        $nivel = isset($_POST['nivel']) ? $_POST['nivel'] : 'MEDIO';
        $motivo = isset($_POST['motivo']) ? trim($_POST['motivo']) : null;
        $fuente = isset($_POST['fuente']) ? trim($_POST['fuente']) : 'Manual';
        
        if ($alumno_id === 0) {
            echo json_encode(["status" => "error", "message" => "ID de alumno no válido"]);
            return;
        }
        
        // Si no se proporciona periodo_id, obtener el periodo activo
        if ($periodo_id === null) {
            $sql = "SELECT id FROM periodos_escolares WHERE activo = 1 LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $periodo = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$periodo) {
                echo json_encode(["status" => "error", "message" => "No hay período activo"]);
                return;
            }
            $periodo_id = $periodo['id'];
        }
        
        // Obtener estado actual antes de toggle
        $estaMarcado = $this->riesgo->estaMarcado($alumno_id, $periodo_id);
        
        if ($this->riesgo->toggleRiesgo($alumno_id, $periodo_id, $nivel, $motivo, $fuente)) {
            $nuevoEstado = !$estaMarcado;
            echo json_encode([
                "status" => "ok", 
                "message" => $nuevoEstado ? "Alumno marcado como en riesgo" : "Alumno desmarcado de riesgo",
                "marcado" => $nuevoEstado
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al actualizar el riesgo"]);
        }
    }

    public function check()
    {
        header('Content-Type: application/json');
        
        $alumno_id = isset($_GET['alumno_id']) ? (int)$_GET['alumno_id'] : 0;
        $periodo_id = isset($_GET['periodo_id']) ? (int)$_GET['periodo_id'] : null;
        
        if ($alumno_id === 0) {
            echo json_encode(["status" => "error", "message" => "ID de alumno no válido"]);
            return;
        }
        
        // Si no se proporciona periodo_id, obtener el periodo activo
        if ($periodo_id === null) {
            $sql = "SELECT id FROM periodos_escolares WHERE activo = 1 LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $periodo = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($periodo) {
                $periodo_id = $periodo['id'];
            } else {
                echo json_encode(["status" => "error", "message" => "No hay período activo"]);
                return;
            }
        }
        
        $estaMarcado = $this->riesgo->estaMarcado($alumno_id, $periodo_id);
        echo json_encode(["status" => "ok", "marcado" => $estaMarcado]);
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