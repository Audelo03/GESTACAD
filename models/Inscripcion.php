<?php
class Inscripcion
{
    private $conn;
    private $table = "inscripciones";
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $sql = "INSERT INTO " . $this->table . " 
                (alumno_id, clase_id, estado, fecha_alta) 
                VALUES (:alumno_id, :clase_id, 'CURSANDO', CURDATE())";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":alumno_id", $data['alumno_id']);
        $stmt->bindParam(":clase_id", $data['clase_id']);
        return $stmt->execute();
    }

    public function updateCalificaciones($id, $data)
    {
        $sql = "UPDATE " . $this->table . " 
                SET cal_parcial1 = :p1, cal_parcial2 = :p2, cal_parcial3 = :p3, cal_parcial4 = :p4, cal_final = :final
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":p1", $data['cal_parcial1']);
        $stmt->bindParam(":p2", $data['cal_parcial2']);
        $stmt->bindParam(":p3", $data['cal_parcial3']);
        $stmt->bindParam(":p4", $data['cal_parcial4']);
        $stmt->bindParam(":final", $data['cal_final']);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete($id)
    {
        // Hard delete or status change? Usually enrollment records are kept.
        // Let's assume 'BAJA' status update instead of delete, or hard delete if it was a mistake.
        // For CRUD simplicity, let's do hard delete for now, or update status to BAJA.
        // The SQL has 'estado' ENUM.
        $sql = "UPDATE " . $this->table . " SET estado = 'BAJA', fecha_baja = CURDATE() WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>