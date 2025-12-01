<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . "/../config/db.php";
class Alumno
{
    public $conn;
    private $table = "alumnos";

    public $id_alumno;
    public $matricula;
    public $nombre;
    public $apellido_paterno;
    public $apellido_materno;
    public $estatus;
    public $carreras_id_carrera;
    public $grupos_id_grupo;

    public function __construct($db = null)
    {
        $this->conn = $db;
    }

    public function getAll()
    {
        $sql = "SELECT a.*, c.nombre AS carrera, g.nombre AS grupo, g.usuarios_id_usuario_tutor AS id_tutor
                FROM alumnos a
                LEFT JOIN carreras c ON a.carreras_id_carrera = c.id_carrera
                LEFT JOIN grupos g ON a.grupos_id_grupo = g.id_grupo
                WHERE a.estatus = 1
                ORDER BY a.nombre ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllPaginated($offset, $limit, $search = '')
    {
        $sql = "SELECT a.*, c.nombre AS carrera, g.nombre AS grupo, g.usuarios_id_usuario_tutor AS id_tutor
                FROM alumnos a
                LEFT JOIN carreras c ON a.carreras_id_carrera = c.id_carrera
                LEFT JOIN grupos g ON a.grupos_id_grupo = g.id_grupo
                WHERE a.estatus = 1";

        $params = [];
        if (!empty($search)) {
            $sql .= " AND (LOWER(a.nombre) LIKE LOWER(:search) 
                     OR LOWER(a.apellido_paterno) LIKE LOWER(:search) 
                     OR LOWER(a.apellido_materno) LIKE LOWER(:search) 
                     OR LOWER(a.matricula) LIKE LOWER(:search)
                     OR LOWER(c.nombre) LIKE LOWER(:search)
                     OR LOWER(g.nombre) LIKE LOWER(:search))";
            $params[':search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY a.nombre ASC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAll($search = '')
    {
        $sql = "SELECT COUNT(*) FROM alumnos a
                LEFT JOIN carreras c ON a.carreras_id_carrera = c.id_carrera
                LEFT JOIN grupos g ON a.grupos_id_grupo = g.id_grupo
                WHERE a.estatus = 1";

        $params = [];
        if (!empty($search)) {
            $sql .= " AND (LOWER(a.nombre) LIKE LOWER(:search) 
                     OR LOWER(a.apellido_paterno) LIKE LOWER(:search) 
                     OR LOWER(a.apellido_materno) LIKE LOWER(:search) 
                     OR LOWER(a.matricula) LIKE LOWER(:search)
                     OR LOWER(c.nombre) LIKE LOWER(:search)
                     OR LOWER(g.nombre) LIKE LOWER(:search))";
            $params[':search'] = '%' . $search . '%';
        }

        $stmt = $this->conn->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM " . $this->table . " WHERE id_alumno = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        // Validaciones básicas de entrada
        if (empty($data['nombre']) || empty($data['apellido_paterno']) || empty($data['matricula'])) {
            throw new InvalidArgumentException("Los campos nombre, apellido paterno y matrícula son obligatorios.");
        }

        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("El formato del email no es válido.");
        }

        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ":$col", $columns);

        $sql = "INSERT INTO " . $this->table . " (" . implode(", ", $columns) . ") 
                VALUES (" . implode(", ", $placeholders) . ")";
        $stmt = $this->conn->prepare($sql);

        foreach ($data as $key => $value) {
            // Sanitización mejorada por tipo de campo
            if (in_array($key, ['nombre', 'apellido_paterno', 'apellido_materno'])) {
                $clean_value = htmlspecialchars(strip_tags(trim((string) $value)), ENT_QUOTES, 'UTF-8');
            } elseif ($key === 'email' && !empty($value)) {
                $clean_value = filter_var(trim($value), FILTER_SANITIZE_EMAIL);
            } elseif (in_array($key, ['carreras_id_carrera', 'grupos_id_grupo', 'estatus'])) {
                $clean_value = (int) $value;
            } else {
                $clean_value = htmlspecialchars(strip_tags(trim((string) $value)), ENT_QUOTES, 'UTF-8');
            }
            $stmt->bindValue(":$key", $clean_value);
        }

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        error_log("Error en Alumno::create: " . implode(', ', $stmt->errorInfo()));
        return false;
    }

    public function update($id, $data)
    {
        // Validar ID
        $id = (int) $id;
        if ($id <= 0) {
            throw new InvalidArgumentException("ID de alumno no válido.");
        }

        // Validaciones de entrada
        if (isset($data['email']) && !empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("El formato del email no es válido.");
        }

        $set_parts = [];
        foreach ($data as $key => $value) {
            $set_parts[] = "$key = :$key";
        }

        $set_clause = implode(", ", $set_parts);
        $query = "UPDATE " . $this->table . " SET " . $set_clause . " WHERE id_alumno = :id_alumno";
        $stmt = $this->conn->prepare($query);

        foreach ($data as $key => $value) {
            // Sanitización mejorada por tipo de campo
            if (in_array($key, ['nombre', 'apellido_paterno', 'apellido_materno'])) {
                $clean_value = htmlspecialchars(strip_tags(trim((string) $value)), ENT_QUOTES, 'UTF-8');
            } elseif ($key === 'email' && !empty($value)) {
                $clean_value = filter_var(trim($value), FILTER_SANITIZE_EMAIL);
            } elseif (in_array($key, ['carreras_id_carrera', 'grupos_id_grupo', 'estatus'])) {
                $clean_value = (int) $value;
            } else {
                $clean_value = htmlspecialchars(strip_tags(trim((string) $value)), ENT_QUOTES, 'UTF-8');
            }
            $stmt->bindValue(":$key", $clean_value);
        }

        $stmt->bindParam(":id_alumno", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $stmt->rowCount() > 0;
        }

        error_log("Error en Alumno::update: " . implode(', ', $stmt->errorInfo()));
        return false;
    }

    public function delete($id)
    {
        $sql = "UPDATE $this->table SET estatus = 0 WHERE id_alumno = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getByMatricula($matricula)
    {
        $sql = "SELECT * FROM " . $this->table . " WHERE matricula = :matricula LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":matricula", $matricula);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * [MEJORADO] Obtiene todos los alumnos de todos los grupos asignados a un tutor.
     * Utiliza un JOIN para ser más eficiente y correcto.
     */
    public function getByTutorId($tutor_id)
    {
        $sql = "SELECT a.* FROM " . $this->table . " a
                JOIN grupos g ON a.grupos_id_grupo = g.id_grupo
                WHERE g.usuarios_id_usuario_tutor = :tutor_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":tutor_id", $tutor_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listByGroupId($group_id, $limit = null, $offset = 0)
    {
        $sql = "SELECT a.*, c.nombre AS carrera_nombre, g.nombre AS grupo_nombre 
                FROM " . $this->table . " a
                LEFT JOIN carreras c ON a.carreras_id_carrera = c.id_carrera
                LEFT JOIN grupos g ON a.grupos_id_grupo = g.id_grupo
                WHERE a.grupos_id_grupo = :group_id
                ORDER BY a.nombre ASC";

        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":group_id", $group_id, PDO::PARAM_INT);

        if ($limit) {
            $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
            $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countByGroupId($group_id)
    {
        $sql = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE grupos_id_grupo = :group_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":group_id", $group_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }

    public function listByCarreraId($career_id)
    {
        $sql = "SELECT * FROM " . $this->table . " WHERE carreras_id_carrera = :career_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":career_id", $career_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- NUEVOS MÉTODOS PARA ELIMINAR CONSULTAS N+1 ---

    /**
     * [NUEVO] Obtiene los alumnos de un arreglo de IDs de grupo.
     * Ideal para la vista de Tutor, para traer todos sus alumnos en una sola consulta.
     * @param array $groupIds Arreglo con los IDs de los grupos.
     * @param string $search Término de búsqueda opcional.
     * @return array Lista de alumnos.
     */
    public function listByGroupIds(array $groupIds, $search = '')
    {
        if (empty($groupIds)) {
            return [];
        }
        // Crea los placeholders (?, ?, ?) para la cláusula IN
        $placeholders = implode(',', array_fill(0, count($groupIds), '?'));

        $sql = "SELECT * FROM " . $this->table . " WHERE grupos_id_grupo IN ($placeholders)";

        // Add search condition if provided
        if (!empty($search)) {
            $sql .= " AND (LOWER(nombre) LIKE LOWER(?) 
                     OR LOWER(apellido_paterno) LIKE LOWER(?) 
                     OR LOWER(apellido_materno) LIKE LOWER(?) 
                     OR LOWER(matricula) LIKE LOWER(?))";
        }

        $sql .= " ORDER BY apellido_paterno, apellido_materno, nombre";

        $stmt = $this->conn->prepare($sql);

        // Merge group IDs with search params if needed
        $params = $groupIds;
        if (!empty($search)) {
            $searchTerm = '%' . $search . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listByCarreraIdConTutorYGrupo($carrera_id)
    {
        $sql = "SELECT 
                    a.nombre, a.apellido_paterno, a.apellido_materno,
                    g.nombre AS nombre_grupo,
                    CONCAT(t.nombre, ' ', t.apellido_paterno, ' ', t.apellido_materno) AS nombre_tutor
                FROM " . $this->table . " a
                LEFT JOIN grupos g ON a.grupos_id_grupo = g.id_grupo
                LEFT JOIN usuarios t ON g.usuarios_id_usuario_tutor = t.id_usuario
                WHERE a.carreras_id_carrera = :carrera_id
                ORDER BY g.nombre, nombre_tutor, a.apellido_paterno";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':carrera_id', $carrera_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>