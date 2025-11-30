<?php
class Beca
{
    private $conn;
    private $table = "cat_becas";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAll()
    {
        $sql = "SELECT * FROM " . $this->table . " WHERE activo = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $sql = "INSERT INTO " . $this->table . " (clave, nombre, activo) VALUES (:clave, :nombre, 1)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":clave", $data['clave']);
        $stmt->bindParam(":nombre", $data['nombre']);
        return $stmt->execute();
    }

    public function update($id, $data)
    {
        $sql = "UPDATE " . $this->table . " SET clave = :clave, nombre = :nombre WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":clave", $data['clave']);
        $stmt->bindParam(":nombre", $data['nombre']);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $sql = "UPDATE " . $this->table . " SET activo = 0 WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>