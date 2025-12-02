<?php
class ActividadPAT
{
    private $conn;
    private $table = "actividades_pat";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAll()
    {
        try {
            $sql = "SELECT 
                        a.id,
                        a.carrera_id,
                        a.grupo_id,
                        a.parcial_id,
                        a.sesion_num,
                        a.nombre,
                        a.descripcion,
                        COALESCE(p.numero, a.parcial_id) as parcial_numero,
                        c.nombre as carrera_nombre,
                        g.nombre as grupo_nombre
                    FROM " . $this->table . " a
                    LEFT JOIN parciales p ON a.parcial_id = p.id
                    LEFT JOIN carreras c ON a.carrera_id = c.id_carrera
                    LEFT JOIN grupos g ON a.grupo_id = g.id_grupo
                    ORDER BY a.parcial_id, a.sesion_num, a.nombre";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Asegurar que los valores NULL se manejen correctamente
            foreach ($result as &$row) {
                $row['carrera_id'] = $row['carrera_id'] ?? null;
                $row['grupo_id'] = $row['grupo_id'] ?? null;
                $row['descripcion'] = $row['descripcion'] ?? null;
                $row['carrera_nombre'] = $row['carrera_nombre'] ?? null;
                $row['grupo_nombre'] = $row['grupo_nombre'] ?? null;
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("Error in ActividadPAT::getAll(): " . $e->getMessage());
            return [];
        }
    }

    public function create($data)
    {
        $sql = "INSERT INTO " . $this->table . " (carrera_id, grupo_id, parcial_id, sesion_num, nombre, descripcion) 
                VALUES (:carrera_id, :grupo_id, :parcial_id, :sesion_num, :nombre, :descripcion)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":carrera_id", $data['carrera_id']);
        $stmt->bindParam(":grupo_id", $data['grupo_id']);
        $stmt->bindParam(":parcial_id", $data['parcial_id']);
        $stmt->bindParam(":sesion_num", $data['sesion_num']);
        $stmt->bindParam(":nombre", $data['nombre']);
        $stmt->bindParam(":descripcion", $data['descripcion']);
        return $stmt->execute();
    }

    public function update($id, $data)
    {
        $sql = "UPDATE " . $this->table . " 
                SET carrera_id = :carrera_id, grupo_id = :grupo_id, parcial_id = :parcial_id, 
                    sesion_num = :sesion_num, nombre = :nombre, descripcion = :descripcion
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":carrera_id", $data['carrera_id']);
        $stmt->bindParam(":grupo_id", $data['grupo_id']);
        $stmt->bindParam(":parcial_id", $data['parcial_id']);
        $stmt->bindParam(":sesion_num", $data['sesion_num']);
        $stmt->bindParam(":nombre", $data['nombre']);
        $stmt->bindParam(":descripcion", $data['descripcion']);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $sql = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>