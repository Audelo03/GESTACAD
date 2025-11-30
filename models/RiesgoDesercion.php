<?php
class RiesgoDesercion
{
    private $conn;
    private $table = "alumno_riesgo_desercion";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAll()
    {
        $sql = "SELECT r.*, a.nombre as alumno_nombre, a.apellido_paterno as alumno_apellido, p.nombre as periodo_nombre
                FROM " . $this->table . " r
                LEFT JOIN alumnos al ON r.alumno_id = al.id_alumno
                LEFT JOIN usuarios a ON al.usuarios_id_usuario = a.id_usuario
                LEFT JOIN periodos_escolares p ON r.periodo_id = p.id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $sql = "INSERT INTO " . $this->table . " (alumno_id, periodo_id, posible, nivel, motivo, fuente) 
                VALUES (:alumno_id, :periodo_id, :posible, :nivel, :motivo, :fuente)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":alumno_id", $data['alumno_id']);
        $stmt->bindParam(":periodo_id", $data['periodo_id']);
        $stmt->bindParam(":posible", $data['posible'], PDO::PARAM_BOOL);
        $stmt->bindParam(":nivel", $data['nivel']);
        $stmt->bindParam(":motivo", $data['motivo']);
        $stmt->bindParam(":fuente", $data['fuente']);
        return $stmt->execute();
    }
}
?>