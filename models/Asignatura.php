<?php
class Asignatura
{
    private $conn;
    private $table = "asignaturas";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAll()
    {
        $sql = "SELECT * FROM " . $this->table . " WHERE activo = 1 ORDER BY nombre ASC";
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
        $sql = "INSERT INTO " . $this->table . " (clave, nombre, creditos, horas_semana, area, activo) VALUES (:clave, :nombre, :creditos, :horas_semana, :area, 1)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":clave", $data['clave']);
        $stmt->bindParam(":nombre", $data['nombre']);
        $stmt->bindParam(":creditos", $data['creditos']);
        $stmt->bindParam(":horas_semana", $data['horas_semana']);
        $stmt->bindParam(":area", $data['area']);
        return $stmt->execute();
    }

    public function update($id, $data)
    {
        $sql = "UPDATE " . $this->table . " SET clave = :clave, nombre = :nombre, creditos = :creditos, horas_semana = :horas_semana, area = :area WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":clave", $data['clave']);
        $stmt->bindParam(":nombre", $data['nombre']);
        $stmt->bindParam(":creditos", $data['creditos']);
        $stmt->bindParam(":horas_semana", $data['horas_semana']);
        $stmt->bindParam(":area", $data['area']);
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