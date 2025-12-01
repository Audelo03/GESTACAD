<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Usuario.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

class UsuarioController {
    
    public $usuario;
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->usuario = new Usuario($conn);
    }

    public function index() {
        echo json_encode($this->usuario->getAll());
    }

    public function searchTutores() {
        return $this->searchUsersByLevel(3);
    }

    public function searchCoordinadores() {
        return $this->searchUsersByLevel(2);
    }

    private function searchUsersByLevel($level) {
        try {
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 20; // Límite para Select2
            
            $offset = ($page - 1) * $limit;
            
            $sql = "SELECT id_usuario as id, 
                           CONCAT(nombre, ' ', apellido_paterno, ' ', COALESCE(apellido_materno, '')) as text
                    FROM usuarios 
                    WHERE niveles_usuarios_id_nivel_usuario = :level";
            
            $params = [':level' => $level];
            
            if (!empty($search)) {
                $sql .= " AND (nombre LIKE :search OR apellido_paterno LIKE :search OR apellido_materno LIKE :search)";
                $params[':search'] = "%{$search}%";
            }
            
            $sql .= " ORDER BY nombre, apellido_paterno LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Contar total para paginación
            $countSql = "SELECT COUNT(*) as total FROM usuarios WHERE niveles_usuarios_id_nivel_usuario = :level";
            if (!empty($search)) {
                $countSql .= " AND (nombre LIKE :search OR apellido_paterno LIKE :search OR apellido_materno LIKE :search)";
            }
            
            $countStmt = $this->conn->prepare($countSql);
            $countStmt->bindValue(':level', $level, PDO::PARAM_INT);
            if (!empty($search)) {
                $countStmt->bindValue(':search', "%{$search}%");
            }
            $countStmt->execute();
            $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            $response = [
                'results' => $results,
                'pagination' => [
                    'more' => ($offset + $limit) < $total
                ]
            ];
            
            header('Content-Type: application/json');
            echo json_encode($response);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error en la búsqueda: ' . $e->getMessage()]);
        }
    }

    public function paginated() {
        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            
            // Validar parámetros
            if ($page < 1) $page = 1;
            if ($limit < 1 || $limit > 100) $limit = 10;
            
            $offset = ($page - 1) * $limit;
            
            // Obtener total de registros
            $total = $this->usuario->countAll($search);
            $totalPages = ceil($total / $limit);
            
            // Obtener usuarios paginados
            $usuarios = $this->usuario->getAllPaginated($offset, $limit, $search);
            
            echo json_encode([
                'success' => true,
                'usuarios' => $usuarios,
                'total' => $total,
                'totalPages' => $totalPages,
                'currentPage' => $page,
                'limit' => $limit
            ]);
            
        } catch (Exception $e) {
            error_log("Error en paginated: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error al cargar los datos',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function listarUsuarios() {
        return $this->usuario->getAll();
    }

    public function listarCoordinadores() {
        return $this->usuario->fetchCoordinadores('coordinador');
    }

    public function verUsuario($id) {
        return $this->usuario->getById($id);
    }

    public function store() {
        $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $data = [
            'nombre' => $_POST['nombre'],
            'apellido_paterno' => $_POST['apellido_paterno'],
            'apellido_materno' => $_POST['apellido_materno'] ?? null,
            'email' => $_POST['email'],
            'password' => $hashedPassword,
            'estatus' => 1, // Siempre activo al crear
            'niveles_usuarios_id_nivel_usuario' => $_POST['niveles_usuarios_id_nivel_usuario'],
            'usuarios_id_usuario_movimiento' => $_SESSION['usuario_id'] ?? null 
        ];
        
        $this->usuario->create($data);
        echo json_encode(["status" => "ok", "message" => "Usuario creado exitosamente"]);
    }

    public function update() {

        
        $id = $_POST['id_usuario'];
    
        $data = [
            'nombre' => $_POST['nombre'],
            'apellido_paterno' => $_POST['apellido_paterno'],
            'apellido_materno' => $_POST['apellido_materno'] ?? null,
            'email' => $_POST['email'],
            'estatus' => 1, // Mantener activo al actualizar
            'niveles_usuarios_id_nivel_usuario' => $_POST['niveles_usuarios_id_nivel_usuario'],
            'usuarios_id_usuario_movimiento' => $_SESSION['usuario_id'] ?? null
        ];
        if (!empty($_POST['password'])) {
            $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }
        $this->usuario->update($id, $data);
        echo json_encode(["status" => "ok", "message" => "Usuario actualizado exitosamente"]);
    }

    public function delete() {
        $id = $_POST['id_usuario'];
        $this->usuario->delete($id);
        echo json_encode(["status" => "ok", "message" => "Usuario eliminado exitosamente"]);
    }
    public function obtenerUsuarioPorId($user_id) {
        
        return $this->usuario->getById($user_id);
        
    }

    public function getLevels() {
        try {
            $sql = "SELECT id_nivel_usuario, nombre FROM niveles_usuarios WHERE estatus = 1 ORDER BY id_nivel_usuario";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $niveles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            header('Content-Type: application/json');
            echo json_encode($niveles);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al cargar los niveles: ' . $e->getMessage()]);
        }
    }
}


if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $controller = new UsuarioController($conn);

    // Mapear acciones
    $actionMap = [
        'search_tutores' => 'searchTutores',
        'search_coordinadores' => 'searchCoordinadores',
        'getLevels' => 'getLevels'
    ];

    $method = $actionMap[$action] ?? $action;

    if (method_exists($controller, $method)) {
        $controller->$method();
    } else {
        echo json_encode(["error" => "Método $action no encontrado"]);
    }
}
?>