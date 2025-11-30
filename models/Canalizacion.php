<?php
class Canalizacion
{
    private $conn;
    private $table = "canalizacion";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAll()
    {
        $sql = "SELECT c.*, a.nombre as alumno_nombre, a.apellido_paterno as alumno_apellido, 
                       p.nombre as periodo_nombre, ar.nombre as area_nombre, u.nombre as usuario_nombre
                FROM " . $this->table . " c
                LEFT JOIN alumnos al ON c.alumno_id = al.id_alumno
                LEFT JOIN usuarios a ON al.usuarios_id_usuario = a.id_usuario
                LEFT JOIN periodos_escolares p ON c.periodo_id = p.id
                LEFT JOIN cat_areas_canalizacion ar ON c.area_id = ar.id
                LEFT JOIN usuarios u ON c.usuario_id = u.id_usuario";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $sql = "INSERT INTO " . $this->table . " (alumno_id, periodo_id, area_id, usuario_id, observacion) 
                VALUES (:alumno_id, :periodo_id, :area_id, :usuario_id, :observacion)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":alumno_id", $data['alumno_id']);
        $stmt->bindParam(":periodo_id", $data['periodo_id']);
        $stmt->bindParam(":area_id", $data['area_id']);
        $stmt->bindParam(":usuario_id", $data['usuario_id']);
        $stmt->bindParam(":observacion", $data['observacion']);
        return $stmt->execute();
    }
}
?>