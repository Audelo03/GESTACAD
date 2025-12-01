<?php
class TutoriaGrupal
{
    private $conn;
    private $table = "tutorias_grupales";
    private $table_asistencia = "tutorias_grupales_asistencia";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Create a new group tutoring session with attendance
     * @param array $data - Contains grupo_id, parcial_id, fecha, actividad_nombre, actividad_descripcion, evidencia_foto_id, usuario_id
     * @param array $asistencia - Array of alumno_id => presente (boolean)
     * @return int|false - Returns the ID of the created session or false on failure
     */
    public function create($data, $asistencia = [])
    {
        try {
            $this->conn->beginTransaction();

            // Insert the group tutoring session
            $sql = "INSERT INTO " . $this->table . " 
                    (grupo_id, parcial_id, fecha, actividad_nombre, actividad_descripcion, evidencia_foto_id, usuario_id) 
                    VALUES (:grupo_id, :parcial_id, :fecha, :actividad_nombre, :actividad_descripcion, :evidencia_foto_id, :usuario_id)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":grupo_id", $data['grupo_id'], PDO::PARAM_INT);
            $stmt->bindParam(":parcial_id", $data['parcial_id'], PDO::PARAM_INT);
            $stmt->bindParam(":fecha", $data['fecha']);
            $stmt->bindParam(":actividad_nombre", $data['actividad_nombre']);
            $stmt->bindParam(":actividad_descripcion", $data['actividad_descripcion']);
            $stmt->bindParam(":evidencia_foto_id", $data['evidencia_foto_id'], PDO::PARAM_INT);
            $stmt->bindParam(":usuario_id", $data['usuario_id'], PDO::PARAM_INT);

            if (!$stmt->execute()) {
                $this->conn->rollBack();
                return false;
            }

            $tutoria_id = $this->conn->lastInsertId();

            // Insert attendance records
            if (!empty($asistencia)) {
                $sql_asistencia = "INSERT INTO " . $this->table_asistencia . " 
                                   (tutoria_grupal_id, alumno_id, presente) 
                                   VALUES (:tutoria_grupal_id, :alumno_id, :presente)";
                $stmt_asistencia = $this->conn->prepare($sql_asistencia);

                foreach ($asistencia as $alumno_id => $presente) {
                    $stmt_asistencia->bindParam(":tutoria_grupal_id", $tutoria_id, PDO::PARAM_INT);
                    $stmt_asistencia->bindParam(":alumno_id", $alumno_id, PDO::PARAM_INT);
                    $stmt_asistencia->bindParam(":presente", $presente, PDO::PARAM_INT);

                    if (!$stmt_asistencia->execute()) {
                        $this->conn->rollBack();
                        return false;
                    }
                }
            }

            $this->conn->commit();
            return $tutoria_id;

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error creating group tutoring: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update an existing group tutoring session with attendance
     * @param int $id
     * @param array $data - Contains grupo_id, parcial_id, fecha, actividad_nombre, actividad_descripcion, evidencia_foto_id, usuario_id
     * @param array $asistencia - Array of alumno_id => presente (boolean)
     * @return bool
     */
    public function update($id, $data, $asistencia = [])
    {
        try {
            $this->conn->beginTransaction();

            // Update the group tutoring session
            $sql = "UPDATE " . $this->table . " 
                    SET grupo_id = :grupo_id,
                        parcial_id = :parcial_id,
                        fecha = :fecha,
                        actividad_nombre = :actividad_nombre,
                        actividad_descripcion = :actividad_descripcion,
                        evidencia_foto_id = :evidencia_foto_id,
                        usuario_id = :usuario_id
                    WHERE id = :id";

            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                $this->conn->rollBack();
                $errorInfo = $this->conn->errorInfo();
                error_log("Error preparing update statement: " . print_r($errorInfo, true));
                return false;
            }
            
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->bindParam(":grupo_id", $data['grupo_id'], PDO::PARAM_INT);
            $stmt->bindParam(":parcial_id", $data['parcial_id'], PDO::PARAM_INT);
            $stmt->bindParam(":fecha", $data['fecha']);
            $stmt->bindParam(":actividad_nombre", $data['actividad_nombre']);
            $stmt->bindParam(":actividad_descripcion", $data['actividad_descripcion']);
            // Handle null evidencia_foto_id properly
            if ($data['evidencia_foto_id'] === null || $data['evidencia_foto_id'] === '') {
                $stmt->bindValue(":evidencia_foto_id", null, PDO::PARAM_NULL);
            } else {
                $stmt->bindParam(":evidencia_foto_id", $data['evidencia_foto_id'], PDO::PARAM_INT);
            }
            $stmt->bindParam(":usuario_id", $data['usuario_id'], PDO::PARAM_INT);

            if (!$stmt->execute()) {
                $this->conn->rollBack();
                $errorInfo = $stmt->errorInfo();
                error_log("Error executing update statement: " . print_r($errorInfo, true));
                error_log("Update data: " . print_r($data, true));
                error_log("Update ID: " . $id);
                return false;
            }

            // Delete existing attendance records
            $sql_delete = "DELETE FROM " . $this->table_asistencia . " WHERE tutoria_grupal_id = :tutoria_id";
            $stmt_delete = $this->conn->prepare($sql_delete);
            if (!$stmt_delete) {
                $this->conn->rollBack();
                $errorInfo = $this->conn->errorInfo();
                error_log("Error preparing delete attendance statement: " . print_r($errorInfo, true));
                return false;
            }
            $stmt_delete->bindParam(":tutoria_id", $id, PDO::PARAM_INT);
            if (!$stmt_delete->execute()) {
                $this->conn->rollBack();
                $errorInfo = $stmt_delete->errorInfo();
                error_log("Error deleting attendance records: " . print_r($errorInfo, true));
                return false;
            }

            // Insert new attendance records
            if (!empty($asistencia)) {
                $sql_asistencia = "INSERT INTO " . $this->table_asistencia . " (tutoria_grupal_id, alumno_id, presente) VALUES (:tutoria_id, :alumno_id, :presente)";
                $stmt_asistencia = $this->conn->prepare($sql_asistencia);
                
                if (!$stmt_asistencia) {
                    $this->conn->rollBack();
                    $errorInfo = $this->conn->errorInfo();
                    error_log("Error preparing insert attendance statement: " . print_r($errorInfo, true));
                    return false;
                }

                foreach ($asistencia as $alumno_id => $presente) {
                    // Use bindValue instead of bindParam to avoid reference issues in loops
                    $stmt_asistencia->bindValue(":tutoria_id", $id, PDO::PARAM_INT);
                    $stmt_asistencia->bindValue(":alumno_id", (int)$alumno_id, PDO::PARAM_INT);
                    $stmt_asistencia->bindValue(":presente", (int)$presente, PDO::PARAM_INT);
                    
                    if (!$stmt_asistencia->execute()) {
                        $this->conn->rollBack();
                        $errorInfo = $stmt_asistencia->errorInfo();
                        error_log("Error inserting attendance record for alumno_id $alumno_id: " . print_r($errorInfo, true));
                        return false;
                    }
                }
            }

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error updating group tutoring: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Get all group tutoring sessions for a specific group
     * @param int $grupo_id
     * @return array
     */
    public function getByGrupo($grupo_id)
    {
        $sql = "SELECT tg.*, 
                       u.nombre as tutor_nombre, 
                       u.apellido_paterno as tutor_apellido,
                       p.numero as parcial_numero,
                       f.ruta as evidencia_ruta,
                       (SELECT COUNT(*) FROM alumnos WHERE grupos_id_grupo = tg.grupo_id) as total_alumnos,
                       SUM(CASE WHEN tga.presente = 1 THEN 1 ELSE 0 END) as total_presentes
                FROM " . $this->table . " tg
                LEFT JOIN usuarios u ON tg.usuario_id = u.id_usuario
                LEFT JOIN parciales p ON tg.parcial_id = p.id
                LEFT JOIN files f ON tg.evidencia_foto_id = f.id
                LEFT JOIN " . $this->table_asistencia . " tga ON tg.id = tga.tutoria_grupal_id
                WHERE tg.grupo_id = :grupo_id
                GROUP BY tg.id
                ORDER BY tg.fecha DESC, tg.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":grupo_id", $grupo_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a specific group tutoring session with attendance details
     * @param int $id
     * @return array|false
     */
    public function getById($id)
    {
        $sql = "SELECT tg.*, 
                       u.nombre as tutor_nombre, 
                       u.apellido_paterno as tutor_apellido,
                       p.numero as parcial_numero,
                       g.nombre as grupo_nombre,
                       f.ruta as evidencia_ruta
                FROM " . $this->table . " tg
                LEFT JOIN usuarios u ON tg.usuario_id = u.id_usuario
                LEFT JOIN parciales p ON tg.parcial_id = p.id
                LEFT JOIN grupos g ON tg.grupo_id = g.id_grupo
                LEFT JOIN files f ON tg.evidencia_foto_id = f.id
                WHERE tg.id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $tutoria = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($tutoria) {
            // Get attendance details
            $sql_asistencia = "SELECT tga.*, 
                                      a.nombre, 
                                      a.apellido_paterno, 
                                      a.apellido_materno,
                                      a.matricula
                               FROM " . $this->table_asistencia . " tga
                               JOIN alumnos a ON tga.alumno_id = a.id_alumno
                               WHERE tga.tutoria_grupal_id = :tutoria_id
                               ORDER BY a.apellido_paterno, a.apellido_materno, a.nombre";

            $stmt_asistencia = $this->conn->prepare($sql_asistencia);
            $stmt_asistencia->bindParam(":tutoria_id", $id, PDO::PARAM_INT);
            $stmt_asistencia->execute();
            $tutoria['asistencia'] = $stmt_asistencia->fetchAll(PDO::FETCH_ASSOC);
        }

        return $tutoria;
    }

    /**
     * Delete a group tutoring session (cascade will delete attendance records)
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $sql = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Get a group tutoring session by grupo_id and fecha
     * @param int $grupo_id
     * @param string $fecha
     * @return array|false
     */
    public function getByGrupoAndDate($grupo_id, $fecha)
    {
        $sql = "SELECT tg.*, 
                       u.nombre as tutor_nombre, 
                       u.apellido_paterno as tutor_apellido,
                       p.numero as parcial_numero,
                       g.nombre as grupo_nombre,
                       f.ruta as evidencia_ruta
                FROM " . $this->table . " tg
                LEFT JOIN usuarios u ON tg.usuario_id = u.id_usuario
                LEFT JOIN parciales p ON tg.parcial_id = p.id
                LEFT JOIN grupos g ON tg.grupo_id = g.id_grupo
                LEFT JOIN files f ON tg.evidencia_foto_id = f.id
                WHERE tg.grupo_id = :grupo_id AND tg.fecha = :fecha
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":grupo_id", $grupo_id, PDO::PARAM_INT);
        $stmt->bindParam(":fecha", $fecha);
        $stmt->execute();
        $tutoria = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($tutoria) {
            // Get attendance details
            $sql_asistencia = "SELECT tga.*, 
                                      a.nombre, 
                                      a.apellido_paterno, 
                                      a.apellido_materno,
                                      a.matricula
                               FROM " . $this->table_asistencia . " tga
                               JOIN alumnos a ON tga.alumno_id = a.id_alumno
                               WHERE tga.tutoria_grupal_id = :tutoria_id
                               ORDER BY a.apellido_paterno, a.apellido_materno, a.nombre";

            $stmt_asistencia = $this->conn->prepare($sql_asistencia);
            $stmt_asistencia->bindParam(":tutoria_id", $tutoria['id'], PDO::PARAM_INT);
            $stmt_asistencia->execute();
            $tutoria['asistencia'] = $stmt_asistencia->fetchAll(PDO::FETCH_ASSOC);
        }

        return $tutoria;
    }

    /**
     * Get all group tutoring sessions for a specific parcial
     * @param int $parcial_id
     * @return array
     */
    public function getByParcial($parcial_id)
    {
        $sql = "SELECT tg.*, 
                       g.nombre as grupo_nombre,
                       u.nombre as tutor_nombre, 
                       u.apellido_paterno as tutor_apellido,
                       COUNT(DISTINCT tga.id) as total_alumnos,
                       SUM(CASE WHEN tga.presente = 1 THEN 1 ELSE 0 END) as total_presentes
                FROM " . $this->table . " tg
                LEFT JOIN grupos g ON tg.grupo_id = g.id_grupo
                LEFT JOIN usuarios u ON tg.usuario_id = u.id_usuario
                LEFT JOIN " . $this->table_asistencia . " tga ON tg.id = tga.tutoria_grupal_id
                WHERE tg.parcial_id = :parcial_id
                GROUP BY tg.id
                ORDER BY tg.fecha DESC, tg.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":parcial_id", $parcial_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>