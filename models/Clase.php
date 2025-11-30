<?php
class Clase
{
    private $conn;
    private $table = "clases";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAll()
    {
        $sql = "SELECT c.*, 
                       a.nombre as asignatura_nombre, a.clave as asignatura_clave,
                       p.nombre as periodo_nombre,
                       u.nombre as docente_nombre, u.apellido_paterno as docente_apellido,
                       m.nombre as modalidad_nombre,
                       g.nombre as grupo_nombre
                FROM " . $this->table . " c
                LEFT JOIN asignaturas a ON c.asignatura_id = a.id
                LEFT JOIN periodos_escolares p ON c.periodo_id = p.id
                LEFT JOIN usuarios u ON c.docente_usuario_id = u.id_usuario
                LEFT JOIN modalidades m ON c.modalidad_id = m.id_modalidad
                LEFT JOIN grupos g ON c.grupo_referencia = g.id_grupo
                WHERE c.activo = 1";
        $stmt = $this->conn->prepare($sql);
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

    public function create($data)
    {
        $sql = "INSERT INTO " . $this->table . " 
                (asignatura_id, periodo_id, docente_usuario_id, seccion, modalidad_id, cupo, grupo_referencia, aula, activo) 
                VALUES (:asignatura_id, :periodo_id, :docente_usuario_id, :seccion, :modalidad_id, :cupo, :grupo_referencia, :aula, 1)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":asignatura_id", $data['asignatura_id']);
        $stmt->bindParam(":periodo_id", $data['periodo_id']);
        $stmt->bindParam(":docente_usuario_id", $data['docente_usuario_id']);
        $stmt->bindParam(":seccion", $data['seccion']);
        $stmt->bindParam(":modalidad_id", $data['modalidad_id']);
        $stmt->bindParam(":cupo", $data['cupo']);
        $stmt->bindParam(":grupo_referencia", $data['grupo_referencia']);
        $stmt->bindParam(":aula", $data['aula']);
        return $stmt->execute();
    }

    public function update($id, $data)
    {
        $sql = "UPDATE " . $this->table . " 
                SET asignatura_id = :asignatura_id, periodo_id = :periodo_id, docente_usuario_id = :docente_usuario_id, 
                    seccion = :seccion, modalidad_id = :modalidad_id, cupo = :cupo, grupo_referencia = :grupo_referencia, aula = :aula 
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":asignatura_id", $data['asignatura_id']);
        $stmt->bindParam(":periodo_id", $data['periodo_id']);
        $stmt->bindParam(":docente_usuario_id", $data['docente_usuario_id']);
        $stmt->bindParam(":seccion", $data['seccion']);
        $stmt->bindParam(":modalidad_id", $data['modalidad_id']);
        $stmt->bindParam(":cupo", $data['cupo']);
        $stmt->bindParam(":grupo_referencia", $data['grupo_referencia']);
        $stmt->bindParam(":aula", $data['aula']);
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
}
?>