<?php
class PatTutorActividad
{
    private $conn;
    private $table = "pat_tutor_actividades";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Create a new PAT activity for a tutor
     * @param array $data - Contains usuario_id, nombre, descripcion
     * @return int|false - Returns the ID of the created activity or false on failure
     */
    public function create($data)
    {
        try {
            $sql = "INSERT INTO " . $this->table . " 
                    (usuario_id, nombre, descripcion) 
                    VALUES (:usuario_id, :nombre, :descripcion)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":usuario_id", $data['usuario_id'], PDO::PARAM_INT);
            $stmt->bindParam(":nombre", $data['nombre']);
            $stmt->bindParam(":descripcion", $data['descripcion']);

            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            return false;
        } catch (Exception $e) {
            error_log("Error creating PAT activity: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update an existing PAT activity
     * @param int $id
     * @param array $data - Contains nombre, descripcion
     * @return bool
     */
    public function update($id, $data)
    {
        try {
            $sql = "UPDATE " . $this->table . " 
                    SET nombre = :nombre,
                        descripcion = :descripcion
                    WHERE id = :id AND usuario_id = :usuario_id";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->bindParam(":usuario_id", $data['usuario_id'], PDO::PARAM_INT);
            $stmt->bindParam(":nombre", $data['nombre']);
            $stmt->bindParam(":descripcion", $data['descripcion']);

            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error updating PAT activity: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all PAT activities for a specific tutor
     * @param int $usuario_id
     * @return array
     */
    public function getByUsuario($usuario_id)
    {
        $sql = "SELECT * FROM " . $this->table . " 
                WHERE usuario_id = :usuario_id 
                ORDER BY nombre ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":usuario_id", $usuario_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a specific PAT activity by ID
     * @param int $id
     * @param int $usuario_id - To ensure the tutor owns this activity
     * @return array|false
     */
    public function getById($id, $usuario_id)
    {
        $sql = "SELECT * FROM " . $this->table . " 
                WHERE id = :id AND usuario_id = :usuario_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":usuario_id", $usuario_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Delete a PAT activity
     * @param int $id
     * @param int $usuario_id - To ensure the tutor owns this activity
     * @return bool
     */
    public function delete($id, $usuario_id)
    {
        $sql = "DELETE FROM " . $this->table . " 
                WHERE id = :id AND usuario_id = :usuario_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":usuario_id", $usuario_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>

