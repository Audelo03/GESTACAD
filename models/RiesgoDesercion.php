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

    public function getByAlumnoAndPeriodo($alumno_id, $periodo_id)
    {
        $sql = "SELECT * FROM " . $this->table . " 
                WHERE alumno_id = :alumno_id AND periodo_id = :periodo_id 
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":alumno_id", $alumno_id, PDO::PARAM_INT);
        $stmt->bindParam(":periodo_id", $periodo_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function toggleRiesgo($alumno_id, $periodo_id, $nivel = 'MEDIO', $motivo = null, $fuente = 'Manual')
    {
        $existente = $this->getByAlumnoAndPeriodo($alumno_id, $periodo_id);
        
        if ($existente) {
            // Si existe, actualizar: cambiar posible a 0 si estaba en 1, o a 1 si estaba en 0
            $nuevo_posible = $existente['posible'] ? 0 : 1;
            $sql = "UPDATE " . $this->table . " 
                    SET posible = :posible, nivel = :nivel, motivo = :motivo, fuente = :fuente, fecha_detectado = NOW()
                    WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":posible", $nuevo_posible, PDO::PARAM_INT);
            $stmt->bindParam(":nivel", $nivel);
            $stmt->bindParam(":motivo", $motivo);
            $stmt->bindParam(":fuente", $fuente);
            $stmt->bindParam(":id", $existente['id'], PDO::PARAM_INT);
            return $stmt->execute();
        } else {
            // Si no existe, crear nuevo registro
            $sql = "INSERT INTO " . $this->table . " (alumno_id, periodo_id, posible, nivel, motivo, fuente) 
                    VALUES (:alumno_id, :periodo_id, 1, :nivel, :motivo, :fuente)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":alumno_id", $alumno_id, PDO::PARAM_INT);
            $stmt->bindParam(":periodo_id", $periodo_id, PDO::PARAM_INT);
            $stmt->bindParam(":nivel", $nivel);
            $stmt->bindParam(":motivo", $motivo);
            $stmt->bindParam(":fuente", $fuente);
            return $stmt->execute();
        }
    }

    public function estaMarcado($alumno_id, $periodo_id)
    {
        $registro = $this->getByAlumnoAndPeriodo($alumno_id, $periodo_id);
        return $registro && $registro['posible'] == 1;
    }
}
?>