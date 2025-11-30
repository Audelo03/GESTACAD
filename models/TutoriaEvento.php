<?php
class TutoriaEvento
{
    private $conn;
    private $table = "tutorias_eventos";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAll()
    {
        $sql = "SELECT t.*, g.nombre as grupo_nombre, p.numero as parcial_numero 
                FROM " . $this->table . " t
                LEFT JOIN grupos g ON t.grupo_id = g.id_grupo
                LEFT JOIN parciales p ON t.parcial_id = p.id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $sql = "INSERT INTO " . $this->table . " (grupo_id, parcial_id, sesion_num, fecha, tipo, actividad_id, actividad_nombre, actividad_descripcion) 
                VALUES (:grupo_id, :parcial_id, :sesion_num, :fecha, :tipo, :actividad_id, :actividad_nombre, :actividad_descripcion)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":grupo_id", $data['grupo_id']);
        $stmt->bindParam(":parcial_id", $data['parcial_id']);
        $stmt->bindParam(":sesion_num", $data['sesion_num']);
        $stmt->bindParam(":fecha", $data['fecha']);
        $stmt->bindParam(":tipo", $data['tipo']);
        $stmt->bindParam(":actividad_id", $data['actividad_id']);
        $stmt->bindParam(":actividad_nombre", $data['actividad_nombre']);
        $stmt->bindParam(":actividad_descripcion", $data['actividad_descripcion']);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $sql = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>