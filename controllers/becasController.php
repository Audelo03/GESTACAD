<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Beca.php";
require_once __DIR__ . "/../models/Usuario.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

class BecasController
{

    public $beca;
    private $usuario;

    public function __construct($conn)
    {
        $this->beca = new Beca($conn);
        $this->usuario = new Usuario($conn);
    }

    public function index()
    {
        // Verificar el nivel del usuario
        $nivel = isset($_SESSION['usuario_nivel']) ? (int)$_SESSION['usuario_nivel'] : null;
        $usuario_id = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : null;

        // Si es tutor (nivel 3), mostrar solo las becas de sus alumnos
        if ($nivel == 3 && $usuario_id) {
            $grupos_ids = $this->usuario->getGruposIdByUsuarioId($usuario_id);
            $becas = $this->beca->getBecasByGrupos($grupos_ids);
            echo json_encode($becas);
        } else {
            // Administrador y Coordinador: mostrar catálogo de becas
            echo json_encode($this->beca->getAll());
        }
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

    /**
     * Obtener todas las becas activas para el select
     */
    public function getBecasActivas()
    {
        header('Content-Type: application/json');
        try {
            $becas = $this->beca->getBecasActivas();
            echo json_encode(['success' => true, 'data' => $becas]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Asignar una beca a un alumno
     */
    public function asignarBecaAlumno()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        try {
            // Validar campos requeridos
            if (!isset($_POST['alumno_id']) || !isset($_POST['beca_id']) || !isset($_POST['periodo_id'])) {
                echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
                return;
            }

            $data = [
                'alumno_id' => (int)$_POST['alumno_id'],
                'beca_id' => (int)$_POST['beca_id'],
                'periodo_id' => (int)$_POST['periodo_id'],
                'porcentaje' => isset($_POST['porcentaje']) ? (float)$_POST['porcentaje'] : 0.00,
                'monto' => isset($_POST['monto']) ? (float)$_POST['monto'] : 0.00
            ];

            $id = $this->beca->asignarBecaAlumno($data);

            if ($id) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Beca asignada exitosamente',
                    'id' => $id
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al asignar la beca']);
            }
        } catch (Exception $e) {
            error_log("Error in asignarBecaAlumno: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
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