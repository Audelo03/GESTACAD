<?php
class Beca
{
    private $conn;
    private $table = "cat_becas";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Obtiene todas las becas del catálogo (para administradores y coordinadores)
     */
    public function getAll()
    {
        $sql = "SELECT * FROM " . $this->table . " WHERE activo = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene las becas asignadas a alumnos de grupos específicos (para tutores)
     * @param array $grupos_ids Array de IDs de grupos
     * @return array Becas de alumnos con información del alumno y la beca
     */
    public function getBecasByGrupos($grupos_ids)
    {
        if (empty($grupos_ids)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($grupos_ids), '?'));
        
        $sql = "SELECT 
                    ab.id,
                    ab.alumno_id,
                    ab.beca_id,
                    ab.periodo_id,
                    ab.porcentaje,
                    ab.monto,
                    ab.fecha_asignacion,
                    a.matricula,
                    a.nombre as alumno_nombre,
                    a.apellido_paterno as alumno_apellido_paterno,
                    a.apellido_materno as alumno_apellido_materno,
                    cb.clave as beca_clave,
                    cb.nombre as beca_nombre,
                    pe.nombre as periodo_nombre,
                    g.nombre as grupo_nombre
                FROM alumno_beca ab
                INNER JOIN alumnos a ON ab.alumno_id = a.id_alumno
                INNER JOIN cat_becas cb ON ab.beca_id = cb.id
                INNER JOIN periodos_escolares pe ON ab.periodo_id = pe.id
                INNER JOIN grupos g ON a.grupos_id_grupo = g.id_grupo
                WHERE a.grupos_id_grupo IN ($placeholders)
                AND cb.activo = 1
                ORDER BY ab.fecha_asignacion DESC, a.apellido_paterno, a.apellido_materno, a.nombre";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($grupos_ids);
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

    /**
     * Asignar una beca a un alumno
     * @param array $data - Contiene alumno_id, beca_id, periodo_id, porcentaje, monto
     * @return int|false - Retorna el ID de la asignación o false en caso de error
     */
    public function asignarBecaAlumno($data)
    {
        try {
            $sql = "INSERT INTO alumno_beca (alumno_id, beca_id, periodo_id, porcentaje, monto, fecha_asignacion) 
                    VALUES (:alumno_id, :beca_id, :periodo_id, :porcentaje, :monto, CURDATE())";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":alumno_id", $data['alumno_id'], PDO::PARAM_INT);
            $stmt->bindParam(":beca_id", $data['beca_id'], PDO::PARAM_INT);
            $stmt->bindParam(":periodo_id", $data['periodo_id'], PDO::PARAM_INT);
            $stmt->bindParam(":porcentaje", $data['porcentaje']);
            $stmt->bindParam(":monto", $data['monto']);
            
            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            return false;
        } catch (Exception $e) {
            error_log("Error asignando beca: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener todas las becas activas del catálogo
     */
    public function getBecasActivas()
    {
        $sql = "SELECT * FROM " . $this->table . " WHERE activo = 1 ORDER BY nombre";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>