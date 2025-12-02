<?php
class Inscripcion
{
    private $conn;
    private $table = "inscripciones";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAll()
    {
        $sql = "SELECT i.*,
                       c.seccion, c.aula,
                       asig.nombre as asignatura_nombre, asig.clave as asignatura_clave,
                       u.nombre as docente_nombre, u.apellido_paterno as docente_apellido,
                       p.nombre as periodo_nombre,
                       m.nombre as modalidad_nombre,
                       a.nombre as alumno_nombre, 
                       a.apellido_paterno as alumno_apellido,
                       a.apellido_materno as alumno_apellido_materno,
                       a.matricula,
                       i.estado_parcial1, i.estado_parcial2, i.estado_parcial3, i.estado_parcial4
                FROM " . $this->table . " i
                LEFT JOIN clases c ON i.clase_id = c.id
                LEFT JOIN asignaturas asig ON c.asignatura_id = asig.id
                LEFT JOIN usuarios u ON c.docente_usuario_id = u.id_usuario
                LEFT JOIN periodos_escolares p ON c.periodo_id = p.id
                LEFT JOIN modalidades m ON c.modalidad_id = m.id_modalidad
                LEFT JOIN alumnos a ON i.alumno_id = a.id_alumno
                ORDER BY i.fecha_alta DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByClase($clase_id)
    {
        $sql = "SELECT i.*,
                       c.seccion, c.aula,
                       asig.nombre as asignatura_nombre, asig.clave as asignatura_clave,
                       u.nombre as docente_nombre, u.apellido_paterno as docente_apellido,
                       p.nombre as periodo_nombre,
                       m.nombre as modalidad_nombre,
                       a.nombre as alumno_nombre, 
                       a.apellido_paterno as alumno_apellido,
                       a.apellido_materno as alumno_apellido_materno,
                       a.matricula,
                       i.estado_parcial1, i.estado_parcial2, i.estado_parcial3, i.estado_parcial4
                FROM " . $this->table . " i
                LEFT JOIN clases c ON i.clase_id = c.id
                LEFT JOIN asignaturas asig ON c.asignatura_id = asig.id
                LEFT JOIN usuarios u ON c.docente_usuario_id = u.id_usuario
                LEFT JOIN periodos_escolares p ON c.periodo_id = p.id
                LEFT JOIN modalidades m ON c.modalidad_id = m.id_modalidad
                LEFT JOIN alumnos a ON i.alumno_id = a.id_alumno
                WHERE i.clase_id = :clase_id
                ORDER BY i.fecha_alta DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":clase_id", $clase_id, PDO::PARAM_INT);
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

    public function getByAlumno($alumno_id)
    {
        $sql = "SELECT i.*,
                       c.seccion, c.aula,
                       asig.nombre as asignatura_nombre, asig.clave as asignatura_clave,
                       u.nombre as docente_nombre, u.apellido_paterno as docente_apellido,
                       p.nombre as periodo_nombre,
                       m.nombre as modalidad_nombre,
                       a.nombre as alumno_nombre, 
                       a.apellido_paterno as alumno_apellido,
                       a.apellido_materno as alumno_apellido_materno,
                       a.matricula,
                       i.estado_parcial1, i.estado_parcial2, i.estado_parcial3, i.estado_parcial4
                FROM " . $this->table . " i
                LEFT JOIN clases c ON i.clase_id = c.id
                LEFT JOIN asignaturas asig ON c.asignatura_id = asig.id
                LEFT JOIN usuarios u ON c.docente_usuario_id = u.id_usuario
                LEFT JOIN periodos_escolares p ON c.periodo_id = p.id
                LEFT JOIN modalidades m ON c.modalidad_id = m.id_modalidad
                LEFT JOIN alumnos a ON i.alumno_id = a.id_alumno
                WHERE i.alumno_id = :alumno_id
                ORDER BY i.fecha_alta DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":alumno_id", $alumno_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        // Verificar si ya existe una inscripción activa para este alumno en esta clase
        $checkSql = "SELECT id FROM " . $this->table . "
                     WHERE alumno_id = :alumno_id AND clase_id = :clase_id AND estado = 'CURSANDO'";
        $checkStmt = $this->conn->prepare($checkSql);
        $checkStmt->bindParam(":alumno_id", $data['alumno_id']);
        $checkStmt->bindParam(":clase_id", $data['clase_id']);
        $checkStmt->execute();

        if ($checkStmt->fetch()) {
            throw new Exception("El alumno ya está inscrito en esta clase.");
        }

        $sql = "INSERT INTO " . $this->table . " 
                (alumno_id, clase_id, estado, fecha_alta) 
                VALUES (:alumno_id, :clase_id, 'CURSANDO', CURDATE())";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":alumno_id", $data['alumno_id']);
        $stmt->bindParam(":clase_id", $data['clase_id']);
        return $stmt->execute();
    }

    public function updateEstado($id, $estado)
    {
        $sql = "UPDATE " . $this->table . " SET estado = :estado WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":estado", $estado);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateEstadosMasivo($updates)
    {
        if (empty($updates)) {
            return true;
        }

        $this->conn->beginTransaction();
        try {
            // Agrupar actualizaciones por inscripción_id para actualizar múltiples parciales en una sola query
            $updatesByInscripcion = [];
            foreach ($updates as $update) {
                $inscripcionId = $update['id'];
                $parcial = $update['parcial'] ?? 1;
                $estado = $update['estado'];
                
                if (!isset($updatesByInscripcion[$inscripcionId])) {
                    $updatesByInscripcion[$inscripcionId] = [];
                }
                $updatesByInscripcion[$inscripcionId][$parcial] = $estado;
            }

            // Construir y ejecutar UPDATE para cada inscripción
            foreach ($updatesByInscripcion as $inscripcionId => $parciales) {
                $setParts = [];
                $params = [':id' => $inscripcionId];
                
                foreach ($parciales as $parcial => $estado) {
                    $setParts[] = "estado_parcial{$parcial} = :estado_p{$parcial}";
                    $params[":estado_p{$parcial}"] = $estado;
                }
                
                $sql = "UPDATE " . $this->table . " SET " . implode(", ", $setParts) . " WHERE id = :id";
                $stmt = $this->conn->prepare($sql);
                
                foreach ($params as $key => $value) {
                    $stmt->bindValue($key, $value);
                }
                
                $stmt->execute();
            }
            
            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            throw new Exception("Error al actualizar estados masivamente: " . $e->getMessage());
        }
    }

    public function updateCalificaciones($id, $data)
    {
        $sql = "UPDATE " . $this->table . " 
                SET cal_parcial1 = :cal_parcial1,
                    cal_parcial2 = :cal_parcial2,
                    cal_parcial3 = :cal_parcial3,
                    cal_parcial4 = :cal_parcial4,
                    cal_final = :cal_final
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":cal_parcial1", !empty($data['cal_parcial1']) ? $data['cal_parcial1'] : null);
        $stmt->bindValue(":cal_parcial2", !empty($data['cal_parcial2']) ? $data['cal_parcial2'] : null);
        $stmt->bindValue(":cal_parcial3", !empty($data['cal_parcial3']) ? $data['cal_parcial3'] : null);
        $stmt->bindValue(":cal_parcial4", !empty($data['cal_parcial4']) ? $data['cal_parcial4'] : null);
        $stmt->bindValue(":cal_final", !empty($data['cal_final']) ? $data['cal_final'] : null);
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