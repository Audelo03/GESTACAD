<?php
class CatalogoFaltas
{
    private $conn;
    private $table = "catalogos_faltas";

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

    public function getByType($tipo)
    {
        $sql = "SELECT * FROM " . $this->table . " WHERE tipo = :tipo AND activo = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":tipo", $tipo);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>