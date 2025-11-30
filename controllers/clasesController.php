<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Clase.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

class ClasesController
{

    public $clase;

    public function __construct($conn)
    {
        $this->clase = new Clase($conn);
    }

    public function index()
    {
        echo json_encode($this->clase->getAll());
    }

    public function store()
    {
        $data = [
            'asignatura_id' => $_POST['asignatura_id'],
            'periodo_id' => $_POST['periodo_id'],
            'docente_usuario_id' => $_POST['docente_usuario_id'],
            'seccion' => $_POST['seccion'],
            'modalidad_id' => $_POST['modalidad_id'],
            'cupo' => $_POST['cupo'],
            'grupo_referencia' => $_POST['grupo_referencia'],
            'aula' => $_POST['aula']
        ];
        if ($this->clase->create($data)) {
            echo json_encode(["status" => "ok"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al crear la clase"]);
        }
    }

    public function update()
    {
        $id = $_POST['id'];
        $data = [
            'asignatura_id' => $_POST['asignatura_id'],
            'periodo_id' => $_POST['periodo_id'],
            'docente_usuario_id' => $_POST['docente_usuario_id'],
            'seccion' => $_POST['seccion'],
            'modalidad_id' => $_POST['modalidad_id'],
            'cupo' => $_POST['cupo'],
            'grupo_referencia' => $_POST['grupo_referencia'],
            'aula' => $_POST['aula']
        ];
        if ($this->clase->update($id, $data)) {
            echo json_encode(["status" => "ok"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al actualizar la clase"]);
        }
    }

    public function delete()
    {
        try {
            $this->clase->delete($_POST['id']);
            echo json_encode(['status' => 'success', 'message' => 'La clase ha sido eliminada correctamente.']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }
}

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $controller = new ClasesController($conn);

    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        echo json_encode(["error" => "Método $action no encontrado"]);
    }
}
?>