<?php
class File
{
    private $conn;
    private $table = "files";

    public function __construct($db)
    {
        $this->conn = $db;
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
        $sql = "INSERT INTO " . $this->table . " (ruta, tipo_mime, tamano, hash) VALUES (:ruta, :tipo_mime, :tamano, :hash)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":ruta", $data['ruta']);
        $stmt->bindParam(":tipo_mime", $data['tipo_mime']);
        $stmt->bindParam(":tamano", $data['tamano']);
        $stmt->bindParam(":hash", $data['hash']);
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
}
?>