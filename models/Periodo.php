<?php
class Periodo
{
    private $conn;
    private $table = "periodos_escolares";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAll()
    {
        $sql = "SELECT * FROM " . $this->table . " WHERE activo = 1 ORDER BY fecha_inicio DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $sql = "INSERT INTO " . $this->table . " (nombre, fecha_inicio, fecha_fin, activo) VALUES (:nombre, :fecha_inicio, :fecha_fin, :activo)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":nombre", $data['nombre']);
        $stmt->bindParam(":fecha_inicio", $data['fecha_inicio']);
        $stmt->bindParam(":fecha_fin", $data['fecha_fin']);
        $activo = isset($data['activo']) ? $data['activo'] : 0; // Default to inactive if not specified, though SQL default is false
        $stmt->bindParam(":activo", $activo, PDO::PARAM_BOOL);
        return $stmt->execute();
    }

    public function update($id, $data)
    {
        $sql = "UPDATE " . $this->table . " SET nombre = :nombre, fecha_inicio = :fecha_inicio, fecha_fin = :fecha_fin, activo = :activo WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":nombre", $data['nombre']);
        $stmt->bindParam(":fecha_inicio", $data['fecha_inicio']);
        $stmt->bindParam(":fecha_fin", $data['fecha_fin']);
        $stmt->bindParam(":activo", $data['activo'], PDO::PARAM_BOOL);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete($id)
    {
        // Hard delete or soft delete? SQL says 'activo' default false. Let's assume we toggle active state or use it as soft delete.
        // For consistency with other models, let's assume soft delete means setting active = 0, but since 'activo' is used for current period logic, maybe we shouldn't delete periods easily.
        // Let's implement soft delete by setting activo = 0.
        $sql = "UPDATE " . $this->table . " SET activo = 0 WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getActive()
    {
        $sql = "SELECT * FROM " . $this->table . " WHERE activo = 1 LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>