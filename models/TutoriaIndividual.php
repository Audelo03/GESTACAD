<?php
class TutoriaIndividual
{
    private $conn;
    private $table = "tutorias_individuales";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Create a new individual tutoring session
     * @param array $data - Contains alumno_id, grupo_id, fecha, motivo, acciones, usuario_id
     * @return int|false - Returns the ID of the created session or false on failure
     */
    public function create($data)
    {
        $sql = "INSERT INTO " . $this->table . " 
                (alumno_id, grupo_id, fecha, motivo, acciones, usuario_id) 
                VALUES (:alumno_id, :grupo_id, :fecha, :motivo, :acciones, :usuario_id)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":alumno_id", $data['alumno_id'], PDO::PARAM_INT);
        $stmt->bindParam(":grupo_id", $data['grupo_id'], PDO::PARAM_INT);
        $stmt->bindParam(":fecha", $data['fecha']);
        $stmt->bindParam(":motivo", $data['motivo']);
        $stmt->bindParam(":acciones", $data['acciones']);
        $stmt->bindParam(":usuario_id", $data['usuario_id'], PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * Get all individual tutoring sessions for a specific student
     * @param int $alumno_id
     * @return array
     */
    public function getByAlumno($alumno_id)
    {
        $sql = "SELECT ti.*, 
                       a.nombre as alumno_nombre,
                       a.apellido_paterno as alumno_apellido_paterno,
                       a.apellido_materno as alumno_apellido_materno,
                       a.matricula,
                       g.nombre as grupo_nombre,
                       u.nombre as tutor_nombre,
                       u.apellido_paterno as tutor_apellido
                FROM " . $this->table . " ti
                JOIN alumnos a ON ti.alumno_id = a.id_alumno
                JOIN grupos g ON ti.grupo_id = g.id_grupo
                JOIN usuarios u ON ti.usuario_id = u.id_usuario
                WHERE ti.alumno_id = :alumno_id
                ORDER BY ti.fecha DESC, ti.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":alumno_id", $alumno_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all individual tutoring sessions for a specific group
     * @param int $grupo_id
     * @return array
     */
    public function getByGrupo($grupo_id)
    {
        $sql = "SELECT ti.*, 
                       a.nombre as alumno_nombre,
                       a.apellido_paterno as alumno_apellido_paterno,
                       a.apellido_materno as alumno_apellido_materno,
                       a.matricula,
                       u.nombre as tutor_nombre,
                       u.apellido_paterno as tutor_apellido
                FROM " . $this->table . " ti
                JOIN alumnos a ON ti.alumno_id = a.id_alumno
                JOIN usuarios u ON ti.usuario_id = u.id_usuario
                WHERE ti.grupo_id = :grupo_id
                ORDER BY ti.fecha DESC, ti.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":grupo_id", $grupo_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a specific individual tutoring session
     * @param int $id
     * @return array|false
     */
    public function getById($id)
    {
        $sql = "SELECT ti.*, 
                       a.nombre as alumno_nombre,
                       a.apellido_paterno as alumno_apellido_paterno,
                       a.apellido_materno as alumno_apellido_materno,
                       a.matricula,
                       g.nombre as grupo_nombre,
                       u.nombre as tutor_nombre,
                       u.apellido_paterno as tutor_apellido
                FROM " . $this->table . " ti
                JOIN alumnos a ON ti.alumno_id = a.id_alumno
                JOIN grupos g ON ti.grupo_id = g.id_grupo
                JOIN usuarios u ON ti.usuario_id = u.id_usuario
                WHERE ti.id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Delete an individual tutoring session
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
     * Update an individual tutoring session
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $sql = "UPDATE " . $this->table . " 
                SET motivo = :motivo, 
                    acciones = :acciones,
                    fecha = :fecha
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":motivo", $data['motivo']);
        $stmt->bindParam(":acciones", $data['acciones']);
        $stmt->bindParam(":fecha", $data['fecha']);

        return $stmt->execute();
    }
}
?>