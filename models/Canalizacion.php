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
        $sql = "SELECT c.*, 
                       al.nombre as alumno_nombre, 
                       al.apellido_paterno as alumno_apellido_paterno,
                       al.apellido_materno as alumno_apellido_materno,
                       al.matricula as alumno_matricula,
                       CONCAT(al.nombre, ' ', al.apellido_paterno, ' ', COALESCE(al.apellido_materno, '')) as alumno_nombre_completo,
                       p.nombre as periodo_nombre, 
                       ar.nombre as area_nombre, 
                       u.nombre as usuario_nombre,
                       u.apellido_paterno as usuario_apellido
                FROM " . $this->table . " c
                LEFT JOIN alumnos al ON c.alumno_id = al.id_alumno
                LEFT JOIN periodos_escolares p ON c.periodo_id = p.id
                LEFT JOIN cat_areas_canalizacion ar ON c.area_id = ar.id
                LEFT JOIN usuarios u ON c.usuario_id = u.id_usuario
                ORDER BY c.fecha_solicitud DESC, c.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAreas()
    {
        $sql = "SELECT * FROM cat_areas_canalizacion WHERE activo = 1 ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getByAlumno($alumno_id)
    {
        $sql = "SELECT c.*, 
                       p.nombre as periodo_nombre, 
                       ar.nombre as area_nombre, 
                       u.nombre as usuario_nombre,
                       u.apellido_paterno as usuario_apellido
                FROM " . $this->table . " c
                LEFT JOIN periodos_escolares p ON c.periodo_id = p.id
                LEFT JOIN cat_areas_canalizacion ar ON c.area_id = ar.id
                LEFT JOIN usuarios u ON c.usuario_id = u.id_usuario
                WHERE c.alumno_id = :alumno_id
                ORDER BY c.fecha_solicitud DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":alumno_id", $alumno_id, PDO::PARAM_INT);
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