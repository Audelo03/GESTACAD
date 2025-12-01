<?php
class Estadisticas {
    private $conn;
    private $filtros = null; // Almacena los filtros por rol

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Establece los filtros según el rol del usuario
     * @param array $filtros ['nivel' => int, 'usuario_id' => int, 'carrera_id' => int|null, 'grupos_ids' => array|null]
     */
    public function setFiltros($filtros) {
        $this->filtros = $filtros;
    }

    /**
     * Construye la cláusula WHERE para filtrar alumnos según el rol
     */
    private function getWhereAlumnos() {
        if (!$this->filtros || $this->filtros['nivel'] == 1) {
            return ""; // Admin: sin filtros
        }
        
        if ($this->filtros['nivel'] == 2 && $this->filtros['carrera_id']) {
            // Coordinador: filtrar por carrera
            return " WHERE a.carreras_id_carrera = " . (int)$this->filtros['carrera_id'];
        }
        
        if ($this->filtros['nivel'] == 3 && !empty($this->filtros['grupos_ids'])) {
            // Tutor: filtrar por grupos
            $grupos_ids = array_map('intval', $this->filtros['grupos_ids']);
            return " WHERE a.grupos_id_grupo IN (" . implode(',', $grupos_ids) . ")";
        }
        
        return " WHERE 1=0"; // Sin acceso
    }

    /**
     * Construye la cláusula WHERE para filtrar grupos según el rol
     */
    private function getWhereGrupos() {
        if (!$this->filtros || $this->filtros['nivel'] == 1) {
            return ""; // Admin: sin filtros
        }
        
        if ($this->filtros['nivel'] == 2 && $this->filtros['carrera_id']) {
            // Coordinador: filtrar por carrera
            return " WHERE g.carreras_id_carrera = " . (int)$this->filtros['carrera_id'];
        }
        
        if ($this->filtros['nivel'] == 3 && !empty($this->filtros['grupos_ids'])) {
            // Tutor: filtrar por grupos
            $grupos_ids = array_map('intval', $this->filtros['grupos_ids']);
            return " WHERE g.id_grupo IN (" . implode(',', $grupos_ids) . ")";
        }
        
        return " WHERE 1=0"; // Sin acceso
    }

    /**
     * Construye la cláusula WHERE para filtrar seguimientos según el rol
     */
    private function getWhereSeguimientos() {
        if (!$this->filtros || $this->filtros['nivel'] == 1) {
            return ""; // Admin: sin filtros
        }
        
        if ($this->filtros['nivel'] == 2 && $this->filtros['carrera_id']) {
            // Coordinador: filtrar por carrera
            return " WHERE EXISTS (
                SELECT 1 FROM alumnos a 
                INNER JOIN grupos g ON a.grupos_id_grupo = g.id_grupo
                WHERE a.id_alumno = s.alumnos_id_alumno 
                AND g.carreras_id_carrera = " . (int)$this->filtros['carrera_id'] . "
            )";
        }
        
        if ($this->filtros['nivel'] == 3 && !empty($this->filtros['grupos_ids'])) {
            // Tutor: filtrar por grupos
            $grupos_ids = array_map('intval', $this->filtros['grupos_ids']);
            return " WHERE EXISTS (
                SELECT 1 FROM alumnos a 
                WHERE a.id_alumno = s.alumnos_id_alumno 
                AND a.grupos_id_grupo IN (" . implode(',', $grupos_ids) . ")
            )";
        }
        
        return " WHERE 1=0"; // Sin acceso
    }

    /**
     * Construye JOIN para filtrar alumnos según el rol
     */
    private function getJoinAlumnos() {
        if (!$this->filtros || $this->filtros['nivel'] == 1) {
            return ""; // Admin: sin JOIN adicional
        }
        
        if ($this->filtros['nivel'] == 2 && $this->filtros['carrera_id']) {
            // Coordinador: JOIN con grupos para filtrar por carrera
            return " INNER JOIN grupos g ON a.grupos_id_grupo = g.id_grupo 
                     AND g.carreras_id_carrera = " . (int)$this->filtros['carrera_id'];
        }
        
        if ($this->filtros['nivel'] == 3 && !empty($this->filtros['grupos_ids'])) {
            // Tutor: ya se filtra en WHERE, no necesita JOIN adicional
            return "";
        }
        
        return "";
    }

    public function totalAlumnos() {
        $where = $this->getWhereAlumnos();
        $sql = "SELECT COUNT(DISTINCT a.id_alumno) as total FROM alumnos a" . $this->getJoinAlumnos() . $where;
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'];
    }

    public function totalCarreras() {
        if (!$this->filtros || $this->filtros['nivel'] == 1) {
            // Admin: todas las carreras
            $sql = "SELECT COUNT(id_carrera) as total FROM carreras WHERE estatus = 1";
        } elseif ($this->filtros['nivel'] == 2 && $this->filtros['carrera_id']) {
            // Coordinador: solo su carrera
            $sql = "SELECT COUNT(id_carrera) as total FROM carreras WHERE estatus = 1 AND id_carrera = " . (int)$this->filtros['carrera_id'];
        } else {
            // Tutor: carreras de sus grupos
            if (!empty($this->filtros['grupos_ids'])) {
                $grupos_ids = array_map('intval', $this->filtros['grupos_ids']);
                $sql = "SELECT COUNT(DISTINCT g.carreras_id_carrera) as total 
                        FROM grupos g 
                        WHERE g.id_grupo IN (" . implode(',', $grupos_ids) . ")";
            } else {
                return 0;
            }
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'];
    }

    public function totalGrupos() {
        $where = $this->getWhereGrupos();
        $sql = "SELECT COUNT(DISTINCT g.id_grupo) as total FROM grupos g" . $where;
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'];
    }

    public function alumnosPorCarrera() {
        if (!$this->filtros || $this->filtros['nivel'] == 1) {
            // Admin: todas las carreras
            $sql = "SELECT c.nombre, COUNT(a.id_alumno) as total
                    FROM carreras c
                    LEFT JOIN grupos g ON c.id_carrera = g.carreras_id_carrera
                    LEFT JOIN alumnos a ON g.id_grupo = a.grupos_id_grupo
                    WHERE c.estatus = 1
                    GROUP BY c.id_carrera, c.nombre 
                    ORDER BY total DESC";
        } elseif ($this->filtros['nivel'] == 2 && $this->filtros['carrera_id']) {
            // Coordinador: solo su carrera
            $sql = "SELECT c.nombre, COUNT(a.id_alumno) as total
                    FROM carreras c
                    LEFT JOIN grupos g ON c.id_carrera = g.carreras_id_carrera
                    LEFT JOIN alumnos a ON g.id_grupo = a.grupos_id_grupo
                    WHERE c.estatus = 1 AND c.id_carrera = " . (int)$this->filtros['carrera_id'] . "
                    GROUP BY c.id_carrera, c.nombre 
                    ORDER BY total DESC";
        } else {
            // Tutor: carreras de sus grupos
            if (!empty($this->filtros['grupos_ids'])) {
                $grupos_ids = array_map('intval', $this->filtros['grupos_ids']);
                $sql = "SELECT c.nombre, COUNT(DISTINCT a.id_alumno) as total
                        FROM grupos g
                        INNER JOIN carreras c ON g.carreras_id_carrera = c.id_carrera
                        LEFT JOIN alumnos a ON g.id_grupo = a.grupos_id_grupo
                        WHERE g.id_grupo IN (" . implode(',', $grupos_ids) . ")
                        GROUP BY c.id_carrera, c.nombre 
                        ORDER BY total DESC";
            } else {
                return [];
            }
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el número de alumnos agrupados por su estatus.
     */
    public function alumnosPorEstatus() {
        $where = $this->getWhereAlumnos();
        $join = $this->getJoinAlumnos();
        $sql = "SELECT CASE 
                        WHEN a.estatus = 1 THEN 'Activo'
                        WHEN a.estatus = 0 THEN 'Inactivo'
                        WHEN a.estatus = 2 THEN 'Egresado'
                        WHEN a.estatus = 3 THEN 'Baja'
                        ELSE 'Desconocido'
                    END as estatus_nombre, 
                    COUNT(DISTINCT a.id_alumno) as total
                FROM alumnos a" . $join . $where . "
                GROUP BY a.estatus";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el número de grupos agrupados por modalidad.
     */
    public function gruposPorModalidad() {
        $where = $this->getWhereGrupos();
        
        // Si hay filtros, agregar la condición al JOIN
        if ($where) {
            // Extraer la condición del WHERE (quitar "WHERE")
            $condition = str_replace("WHERE ", "", $where);
            $sql = "SELECT m.nombre, COUNT(DISTINCT g.id_grupo) as total
                    FROM modalidades m
                    LEFT JOIN grupos g ON m.id_modalidad = g.modalidades_id_modalidad AND " . $condition . "
                    GROUP BY m.id_modalidad, m.nombre 
                    ORDER BY total DESC";
        } else {
            // Sin filtros (admin)
            $sql = "SELECT m.nombre, COUNT(DISTINCT g.id_grupo) as total
                    FROM modalidades m
                    LEFT JOIN grupos g ON m.id_modalidad = g.modalidades_id_modalidad
                    GROUP BY m.id_modalidad, m.nombre 
                    ORDER BY total DESC";
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Obtiene el número de seguimientos agrupados por su estatus.
     */
    public function seguimientosPorEstatus() {
        $where = $this->getWhereSeguimientos();
        $sql = "SELECT CASE 
                        WHEN s.estatus = 1 THEN 'Abierto'
                        WHEN s.estatus = 2 THEN 'En Progreso'
                        WHEN s.estatus = 3 THEN 'Cerrado'
                        ELSE 'Desconocido'
                    END as estatus_nombre, 
                    COUNT(s.id_seguimiento) as total
                FROM seguimientos s" . $where . "
                GROUP BY s.estatus";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el número de seguimientos agrupados por tipo.
     */
    public function seguimientosPorTipo() {
        $where = $this->getWhereSeguimientos();
        if ($where) {
            // Si hay filtros, aplicar WHERE después del JOIN
            $sql = "SELECT ts.nombre, COUNT(s.id_seguimiento) as total
                    FROM tipo_seguimiento ts
                    LEFT JOIN seguimientos s ON ts.id_tipo_seguimiento = s.tipo_seguimiento_id" . $where . "
                    GROUP BY ts.id_tipo_seguimiento, ts.nombre 
                    ORDER BY total DESC";
        } else {
            // Sin filtros (admin)
            $sql = "SELECT ts.nombre, COUNT(s.id_seguimiento) as total
                    FROM tipo_seguimiento ts
                    LEFT JOIN seguimientos s ON ts.id_tipo_seguimiento = s.tipo_seguimiento_id
                    GROUP BY ts.id_tipo_seguimiento, ts.nombre 
                    ORDER BY total DESC";
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene estadísticas de usuarios por nivel
     */
    public function usuariosPorNivel() {
        $sql = "SELECT nu.nombre, COUNT(u.id_usuario) as total
                FROM niveles_usuarios nu
                LEFT JOIN usuarios u ON nu.id_nivel_usuario = u.niveles_usuarios_id_nivel_usuario
                WHERE u.estatus = 1
                GROUP BY nu.nombre ORDER BY total DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene distribución de alumnos por grupo
     */
    public function alumnosPorGrupo() {
        $where = $this->getWhereGrupos();
        $sql = "SELECT g.nombre as grupo, c.nombre as carrera, COUNT(DISTINCT a.id_alumno) as total
                FROM grupos g
                LEFT JOIN alumnos a ON g.id_grupo = a.grupos_id_grupo
                LEFT JOIN carreras c ON g.carreras_id_carrera = c.id_carrera" . $where . "
                GROUP BY g.id_grupo, g.nombre, c.nombre
                HAVING total > 0
                ORDER BY total DESC
                LIMIT 10";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Obtiene seguimientos por mes
     */
    public function seguimientosPorMes() {
        $where = $this->getWhereSeguimientos();
        $fechaFilter = " s.fecha_creacion >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
        
        if ($where) {
            // Si ya hay WHERE, agregar AND
            $whereClause = $where . " AND" . $fechaFilter;
        } else {
            // Si no hay WHERE, agregar WHERE
            $whereClause = " WHERE" . $fechaFilter;
        }
        
        $sql = "SELECT 
                    DATE_FORMAT(s.fecha_creacion, '%Y-%m') as mes,
                    COUNT(s.id_seguimiento) as total_seguimientos,
                    SUM(CASE WHEN s.estatus = 1 THEN 1 ELSE 0 END) as abiertos,
                    SUM(CASE WHEN s.estatus = 2 THEN 1 ELSE 0 END) as en_progreso,
                    SUM(CASE WHEN s.estatus = 3 THEN 1 ELSE 0 END) as cerrados
                FROM seguimientos s" . $whereClause . "
                GROUP BY DATE_FORMAT(s.fecha_creacion, '%Y-%m')
                ORDER BY mes DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene productividad de tutores
     */
    public function productividadTutores() {
        if (!$this->filtros || $this->filtros['nivel'] == 1) {
            // Admin: todos los tutores
            $where = " WHERE u.niveles_usuarios_id_nivel_usuario = 3";
        } elseif ($this->filtros['nivel'] == 2 && $this->filtros['carrera_id']) {
            // Coordinador: tutores de su carrera
            $where = " WHERE u.niveles_usuarios_id_nivel_usuario = 3 
                       AND g.carreras_id_carrera = " . (int)$this->filtros['carrera_id'];
        } elseif ($this->filtros['nivel'] == 3 && $this->filtros['usuario_id']) {
            // Tutor: solo él mismo
            $where = " WHERE u.id_usuario = " . (int)$this->filtros['usuario_id'];
        } else {
            return [];
        }
        
        $sql = "SELECT 
                    CONCAT(u.nombre, ' ', u.apellido_paterno) as tutor,
                    COUNT(DISTINCT g.id_grupo) as grupos_asignados,
                    COUNT(DISTINCT a.id_alumno) as alumnos_tutoreados,
                    COUNT(s.id_seguimiento) as seguimientos_realizados,
                    ROUND(COUNT(s.id_seguimiento) / NULLIF(COUNT(DISTINCT a.id_alumno), 0), 2) as promedio_seguimientos_por_alumno
                FROM usuarios u
                LEFT JOIN grupos g ON u.id_usuario = g.usuarios_id_usuario_tutor
                LEFT JOIN alumnos a ON g.id_grupo = a.grupos_id_grupo
                LEFT JOIN seguimientos s ON a.id_alumno = s.alumnos_id_alumno" . $where . "
                GROUP BY u.id_usuario, u.nombre, u.apellido_paterno
                HAVING grupos_asignados > 0
                ORDER BY seguimientos_realizados DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene estadísticas de alumnos por año de ingreso
     */
    public function alumnosPorAnioIngreso() {
        $where = $this->getWhereAlumnos();
        $join = $this->getJoinAlumnos();
        $sql = "SELECT 
                    YEAR(a.fecha_creacion) as anio_ingreso,
                    COUNT(DISTINCT a.id_alumno) as total_alumnos,
                    SUM(CASE WHEN a.estatus = 1 THEN 1 ELSE 0 END) as activos,
                    SUM(CASE WHEN a.estatus = 2 THEN 1 ELSE 0 END) as egresados,
                    SUM(CASE WHEN a.estatus = 3 THEN 1 ELSE 0 END) as bajas
                FROM alumnos a" . $join . $where . "
                GROUP BY YEAR(a.fecha_creacion)
                ORDER BY anio_ingreso DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene estadísticas de carreras más populares
     */
    public function carrerasMasPopulares() {
        if (!$this->filtros || $this->filtros['nivel'] == 1) {
            // Admin: todas las carreras
            $where = " WHERE c.estatus = 1";
        } elseif ($this->filtros['nivel'] == 2 && $this->filtros['carrera_id']) {
            // Coordinador: solo su carrera
            $where = " WHERE c.estatus = 1 AND c.id_carrera = " . (int)$this->filtros['carrera_id'];
        } else {
            // Tutor: carreras de sus grupos
            if (!empty($this->filtros['grupos_ids'])) {
                $grupos_ids = array_map('intval', $this->filtros['grupos_ids']);
                $sql = "SELECT 
                            c.nombre as carrera,
                            COUNT(DISTINCT a.id_alumno) as total_alumnos,
                            COUNT(DISTINCT g.id_grupo) as total_grupos,
                            ROUND(COUNT(DISTINCT a.id_alumno) / NULLIF(COUNT(DISTINCT g.id_grupo), 0), 2) as promedio_alumnos_por_grupo
                        FROM grupos g
                        INNER JOIN carreras c ON g.carreras_id_carrera = c.id_carrera
                        LEFT JOIN alumnos a ON g.id_grupo = a.grupos_id_grupo
                        WHERE g.id_grupo IN (" . implode(',', $grupos_ids) . ")
                        GROUP BY c.id_carrera, c.nombre
                        HAVING total_alumnos > 0
                        ORDER BY total_alumnos DESC
                        LIMIT 10";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        }
        
        $sql = "SELECT 
                    c.nombre as carrera,
                    COUNT(DISTINCT a.id_alumno) as total_alumnos,
                    COUNT(DISTINCT g.id_grupo) as total_grupos,
                    ROUND(COUNT(DISTINCT a.id_alumno) / NULLIF(COUNT(DISTINCT g.id_grupo), 0), 2) as promedio_alumnos_por_grupo
                FROM carreras c
                LEFT JOIN grupos g ON c.id_carrera = g.carreras_id_carrera
                LEFT JOIN alumnos a ON g.id_grupo = a.grupos_id_grupo" . $where . "
                GROUP BY c.id_carrera, c.nombre
                HAVING total_alumnos > 0
                ORDER BY total_alumnos DESC
                LIMIT 10";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene estadísticas de modalidades más utilizadas
     */
    public function modalidadesMasUtilizadas() {
        $where = $this->getWhereGrupos();
        
        // Si hay filtros, necesitamos agregar la condición al JOIN o al WHERE final
        if ($where) {
            // Extraer la condición del WHERE (quitar "WHERE")
            $condition = str_replace("WHERE ", "", $where);
            $sql = "SELECT 
                        m.nombre as modalidad,
                        COUNT(DISTINCT g.id_grupo) as total_grupos,
                        COUNT(DISTINCT a.id_alumno) as total_alumnos,
                        ROUND(COUNT(DISTINCT a.id_alumno) / NULLIF(COUNT(DISTINCT g.id_grupo), 0), 2) as promedio_alumnos_por_grupo
                    FROM modalidades m
                    LEFT JOIN grupos g ON m.id_modalidad = g.modalidades_id_modalidad AND " . $condition . "
                    LEFT JOIN alumnos a ON g.id_grupo = a.grupos_id_grupo
                    GROUP BY m.id_modalidad, m.nombre
                    HAVING total_grupos > 0
                    ORDER BY total_grupos DESC";
        } else {
            // Sin filtros (admin)
            $sql = "SELECT 
                        m.nombre as modalidad,
                        COUNT(DISTINCT g.id_grupo) as total_grupos,
                        COUNT(DISTINCT a.id_alumno) as total_alumnos,
                        ROUND(COUNT(DISTINCT a.id_alumno) / NULLIF(COUNT(DISTINCT g.id_grupo), 0), 2) as promedio_alumnos_por_grupo
                    FROM modalidades m
                    LEFT JOIN grupos g ON m.id_modalidad = g.modalidades_id_modalidad
                    LEFT JOIN alumnos a ON g.id_grupo = a.grupos_id_grupo
                    GROUP BY m.id_modalidad, m.nombre
                    HAVING total_grupos > 0
                    ORDER BY total_grupos DESC";
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene estadísticas generales del sistema
     */
    public function estadisticasGenerales() {
        // Construir subconsultas con filtros
        $whereAlumnos = $this->getWhereAlumnos();
        $joinAlumnos = $this->getJoinAlumnos();
        $whereGrupos = $this->getWhereGrupos();
        $whereSeguimientos = $this->getWhereSeguimientos();
        
        // Alumnos activos
        $subAlumnos = "SELECT COUNT(DISTINCT a.id_alumno) FROM alumnos a" . $joinAlumnos . 
                      ($whereAlumnos ? $whereAlumnos . " AND a.estatus = 1" : " WHERE a.estatus = 1");
        
        // Grupos
        $subGrupos = "SELECT COUNT(DISTINCT g.id_grupo) FROM grupos g" . 
                     ($whereGrupos ? $whereGrupos . " AND g.estatus = 1" : " WHERE g.estatus = 1");
        
        // Carreras
        if (!$this->filtros || $this->filtros['nivel'] == 1) {
            $subCarreras = "SELECT COUNT(*) FROM carreras WHERE estatus = 1";
        } elseif ($this->filtros['nivel'] == 2 && $this->filtros['carrera_id']) {
            $subCarreras = "SELECT COUNT(*) FROM carreras WHERE estatus = 1 AND id_carrera = " . (int)$this->filtros['carrera_id'];
        } else {
            if (!empty($this->filtros['grupos_ids'])) {
                $grupos_ids = array_map('intval', $this->filtros['grupos_ids']);
                $subCarreras = "SELECT COUNT(DISTINCT g.carreras_id_carrera) FROM grupos g WHERE g.id_grupo IN (" . implode(',', $grupos_ids) . ")";
            } else {
                $subCarreras = "SELECT 0";
            }
        }
        
        // Usuarios activos (solo para admin)
        if (!$this->filtros || $this->filtros['nivel'] == 1) {
            $subUsuarios = "SELECT COUNT(*) FROM usuarios WHERE estatus = 1";
        } else {
            $subUsuarios = "SELECT 0"; // No mostrar para otros roles
        }
        
        // Seguimientos
        if ($whereSeguimientos) {
            $subSeguimientosAbiertos = "SELECT COUNT(s.id_seguimiento) FROM seguimientos s" . $whereSeguimientos . " AND s.estatus = 1";
            $subSeguimientosMes = "SELECT COUNT(s.id_seguimiento) FROM seguimientos s" . $whereSeguimientos . " AND s.fecha_creacion >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        } else {
            $subSeguimientosAbiertos = "SELECT COUNT(s.id_seguimiento) FROM seguimientos s WHERE s.estatus = 1";
            $subSeguimientosMes = "SELECT COUNT(s.id_seguimiento) FROM seguimientos s WHERE s.fecha_creacion >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        }
        
        $sql = "SELECT 
                    (" . $subAlumnos . ") as alumnos_activos,
                    (" . $subUsuarios . ") as usuarios_activos,
                    (" . $subGrupos . ") as total_grupos,
                    (" . $subCarreras . ") as total_carreras,
                    (" . $subSeguimientosAbiertos . ") as seguimientos_abiertos,
                    (" . $subSeguimientosMes . ") as seguimientos_mes";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * ============================================
     * ESTADÍSTICAS ESPECÍFICAS PARA TUTORES
     * ============================================
     */

    /**
     * Obtiene resumen de grupos del tutor
     */
    public function resumenGruposTutor() {
        if (!$this->filtros || $this->filtros['nivel'] != 3 || empty($this->filtros['grupos_ids'])) {
            return [];
        }

        $grupos_ids = array_map('intval', $this->filtros['grupos_ids']);
        $sql = "SELECT 
                    g.id_grupo,
                    g.nombre as grupo,
                    c.nombre as carrera,
                    COUNT(DISTINCT a.id_alumno) as total_alumnos,
                    COUNT(DISTINCT CASE WHEN a.estatus = 1 THEN a.id_alumno END) as alumnos_activos,
                    COUNT(DISTINCT tg.id) as tutorias_grupales_realizadas,
                    COUNT(DISTINCT ti.id) as tutorias_individuales_realizadas,
                    COUNT(DISTINCT s.id_seguimiento) as seguimientos_totales,
                    COUNT(DISTINCT CASE WHEN s.estatus = 1 THEN s.id_seguimiento END) as seguimientos_abiertos
                FROM grupos g
                INNER JOIN carreras c ON g.carreras_id_carrera = c.id_carrera
                LEFT JOIN alumnos a ON g.id_grupo = a.grupos_id_grupo
                LEFT JOIN tutorias_grupales tg ON g.id_grupo = tg.grupo_id
                LEFT JOIN tutorias_individuales ti ON g.id_grupo = ti.grupo_id
                LEFT JOIN seguimientos s ON a.id_alumno = s.alumnos_id_alumno
                WHERE g.id_grupo IN (" . implode(',', $grupos_ids) . ")
                GROUP BY g.id_grupo, g.nombre, c.nombre
                ORDER BY g.nombre";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene asistencia a tutorías grupales por grupo
     */
    public function asistenciaTutoriasPorGrupo() {
        if (!$this->filtros || $this->filtros['nivel'] != 3 || empty($this->filtros['grupos_ids'])) {
            return [];
        }

        $grupos_ids = array_map('intval', $this->filtros['grupos_ids']);
        $sql = "SELECT 
                    g.nombre as grupo,
                    COUNT(DISTINCT tg.id) as total_tutorias,
                    COUNT(DISTINCT tga.id) as total_registros_asistencia,
                    SUM(CASE WHEN tga.presente = 1 THEN 1 ELSE 0 END) as total_asistencias,
                    SUM(CASE WHEN tga.presente = 0 THEN 1 ELSE 0 END) as total_faltas,
                    ROUND((SUM(CASE WHEN tga.presente = 1 THEN 1 ELSE 0 END) / NULLIF(COUNT(DISTINCT tga.id), 0)) * 100, 2) as porcentaje_asistencia
                FROM grupos g
                LEFT JOIN tutorias_grupales tg ON g.id_grupo = tg.grupo_id
                LEFT JOIN tutorias_grupales_asistencia tga ON tg.id = tga.tutoria_grupal_id
                WHERE g.id_grupo IN (" . implode(',', $grupos_ids) . ")
                GROUP BY g.id_grupo, g.nombre
                ORDER BY g.nombre";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene tutorías grupales realizadas por mes
     */
    public function tutoriasGrupalesPorMes() {
        if (!$this->filtros || $this->filtros['nivel'] != 3 || empty($this->filtros['grupos_ids'])) {
            return [];
        }

        $grupos_ids = array_map('intval', $this->filtros['grupos_ids']);
        $sql = "SELECT 
                    DATE_FORMAT(tg.fecha, '%Y-%m') as mes,
                    COUNT(DISTINCT tg.id) as total_tutorias,
                    COUNT(DISTINCT tg.grupo_id) as grupos_atendidos,
                    COUNT(DISTINCT tga.alumno_id) as alumnos_participantes,
                    SUM(CASE WHEN tga.presente = 1 THEN 1 ELSE 0 END) as total_asistencias
                FROM tutorias_grupales tg
                LEFT JOIN tutorias_grupales_asistencia tga ON tg.id = tga.tutoria_grupal_id
                WHERE tg.grupo_id IN (" . implode(',', $grupos_ids) . ")
                    AND tg.fecha >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                GROUP BY DATE_FORMAT(tg.fecha, '%Y-%m')
                ORDER BY mes DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene tutorías grupales por parcial
     */
    public function tutoriasGrupalesPorParcial() {
        if (!$this->filtros || $this->filtros['nivel'] != 3 || empty($this->filtros['grupos_ids'])) {
            return [];
        }

        $grupos_ids = array_map('intval', $this->filtros['grupos_ids']);
        $sql = "SELECT 
                    p.nombre as parcial,
                    p.numero,
                    COUNT(DISTINCT tg.id) as total_tutorias,
                    COUNT(DISTINCT tg.grupo_id) as grupos_atendidos,
                    COUNT(DISTINCT tga.alumno_id) as alumnos_participantes
                FROM parciales p
                LEFT JOIN tutorias_grupales tg ON p.id = tg.parcial_id 
                    AND tg.grupo_id IN (" . implode(',', $grupos_ids) . ")
                LEFT JOIN tutorias_grupales_asistencia tga ON tg.id = tga.tutoria_grupal_id
                WHERE p.periodo_id = (SELECT id FROM periodos_escolares WHERE activo = 1 LIMIT 1)
                GROUP BY p.id, p.nombre, p.numero
                ORDER BY p.numero";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene tutorías individuales realizadas por mes
     */
    public function tutoriasIndividualesPorMes() {
        if (!$this->filtros || $this->filtros['nivel'] != 3 || empty($this->filtros['grupos_ids'])) {
            return [];
        }

        $grupos_ids = array_map('intval', $this->filtros['grupos_ids']);
        $sql = "SELECT 
                    DATE_FORMAT(ti.fecha, '%Y-%m') as mes,
                    COUNT(ti.id) as total_tutorias,
                    COUNT(DISTINCT ti.alumno_id) as alumnos_atendidos
                FROM tutorias_individuales ti
                WHERE ti.grupo_id IN (" . implode(',', $grupos_ids) . ")
                    AND ti.fecha >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                GROUP BY DATE_FORMAT(ti.fecha, '%Y-%m')
                ORDER BY mes DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene alumnos con seguimientos abiertos
     */
    public function alumnosConSeguimientosAbiertos() {
        if (!$this->filtros || $this->filtros['nivel'] != 3 || empty($this->filtros['grupos_ids'])) {
            return [];
        }

        $grupos_ids = array_map('intval', $this->filtros['grupos_ids']);
        $sql = "SELECT 
                    a.id_alumno,
                    CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', COALESCE(a.apellido_materno, '')) as nombre_completo,
                    g.nombre as grupo,
                    COUNT(s.id_seguimiento) as seguimientos_abiertos,
                    MAX(s.fecha_creacion) as ultimo_seguimiento
                FROM alumnos a
                INNER JOIN grupos g ON a.grupos_id_grupo = g.id_grupo
                INNER JOIN seguimientos s ON a.id_alumno = s.alumnos_id_alumno
                WHERE a.grupos_id_grupo IN (" . implode(',', $grupos_ids) . ")
                    AND s.estatus IN (1, 2) -- Abierto o En Progreso
                GROUP BY a.id_alumno, a.nombre, a.apellido_paterno, a.apellido_materno, g.nombre
                ORDER BY seguimientos_abiertos DESC, ultimo_seguimiento DESC
                LIMIT 20";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene alumnos con riesgo de deserción
     */
    public function alumnosConRiesgoDesercion() {
        if (!$this->filtros || $this->filtros['nivel'] != 3 || empty($this->filtros['grupos_ids'])) {
            return [];
        }

        $grupos_ids = array_map('intval', $this->filtros['grupos_ids']);
        $sql = "SELECT 
                    a.id_alumno,
                    CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', COALESCE(a.apellido_materno, '')) as nombre_completo,
                    g.nombre as grupo,
                    ard.nivel as nivel_riesgo,
                    ard.motivo,
                    ard.fecha_detectado
                FROM alumnos a
                INNER JOIN grupos g ON a.grupos_id_grupo = g.id_grupo
                INNER JOIN alumno_riesgo_desercion ard ON a.id_alumno = ard.alumno_id
                WHERE a.grupos_id_grupo IN (" . implode(',', $grupos_ids) . ")
                    AND ard.posible = 1
                    AND ard.periodo_id = (SELECT id FROM periodos_escolares WHERE activo = 1 LIMIT 1)
                ORDER BY 
                    CASE ard.nivel
                        WHEN 'ALTO' THEN 1
                        WHEN 'MEDIO' THEN 2
                        WHEN 'BAJO' THEN 3
                    END,
                    ard.fecha_detectado DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene canalizaciones pendientes
     */
    public function canalizacionesPendientes() {
        if (!$this->filtros || $this->filtros['nivel'] != 3 || empty($this->filtros['grupos_ids'])) {
            return [];
        }

        $grupos_ids = array_map('intval', $this->filtros['grupos_ids']);
        $sql = "SELECT 
                    c.id,
                    CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', COALESCE(a.apellido_materno, '')) as nombre_alumno,
                    g.nombre as grupo,
                    ca.nombre as area,
                    c.observacion,
                    c.fecha_solicitud,
                    c.estatus
                FROM canalizacion c
                INNER JOIN alumnos a ON c.alumno_id = a.id_alumno
                INNER JOIN grupos g ON a.grupos_id_grupo = g.id_grupo
                INNER JOIN cat_areas_canalizacion ca ON c.area_id = ca.id
                WHERE a.grupos_id_grupo IN (" . implode(',', $grupos_ids) . ")
                    AND c.estatus = 'PENDIENTE'
                ORDER BY c.fecha_solicitud DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene actividad reciente del tutor (últimos 30 días)
     */
    public function actividadRecienteTutor() {
        if (!$this->filtros || $this->filtros['nivel'] != 3 || !$this->filtros['usuario_id']) {
            return [];
        }

        $usuario_id = (int)$this->filtros['usuario_id'];
        $sql = "SELECT 
                    CAST('Tutoria Grupal' AS CHAR CHARACTER SET utf8mb4) COLLATE utf8mb4_unicode_ci as tipo,
                    tg.id as actividad_id,
                    CAST(g.nombre AS CHAR CHARACTER SET utf8mb4) COLLATE utf8mb4_unicode_ci as grupo,
                    CAST(tg.actividad_nombre AS CHAR CHARACTER SET utf8mb4) COLLATE utf8mb4_unicode_ci as descripcion,
                    tg.fecha as fecha_actividad,
                    tg.created_at as fecha_registro
                FROM tutorias_grupales tg
                INNER JOIN grupos g ON tg.grupo_id = g.id_grupo
                WHERE tg.usuario_id = $usuario_id
                    AND tg.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                
                UNION ALL
                
                SELECT 
                    CAST('Tutoria Individual' AS CHAR CHARACTER SET utf8mb4) COLLATE utf8mb4_unicode_ci as tipo,
                    ti.id as actividad_id,
                    CAST(g.nombre AS CHAR CHARACTER SET utf8mb4) COLLATE utf8mb4_unicode_ci as grupo,
                    CAST(CONCAT('Tutoría con: ', a.nombre, ' ', a.apellido_paterno) AS CHAR CHARACTER SET utf8mb4) COLLATE utf8mb4_unicode_ci as descripcion,
                    ti.fecha as fecha_actividad,
                    ti.created_at as fecha_registro
                FROM tutorias_individuales ti
                INNER JOIN grupos g ON ti.grupo_id = g.id_grupo
                INNER JOIN alumnos a ON ti.alumno_id = a.id_alumno
                WHERE ti.usuario_id = $usuario_id
                    AND ti.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                
                UNION ALL
                
                SELECT 
                    CAST('Seguimiento' AS CHAR CHARACTER SET utf8mb4) COLLATE utf8mb4_unicode_ci as tipo,
                    s.id_seguimiento as actividad_id,
                    CAST(g.nombre AS CHAR CHARACTER SET utf8mb4) COLLATE utf8mb4_unicode_ci as grupo,
                    CAST(LEFT(s.descripcion, 50) AS CHAR CHARACTER SET utf8mb4) COLLATE utf8mb4_unicode_ci as descripcion,
                    s.fecha_creacion as fecha_actividad,
                    s.fecha_movimiento as fecha_registro
                FROM seguimientos s
                INNER JOIN alumnos a ON s.alumnos_id_alumno = a.id_alumno
                INNER JOIN grupos g ON a.grupos_id_grupo = g.id_grupo
                WHERE s.tutor_id = $usuario_id
                    AND s.fecha_creacion >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                
                ORDER BY fecha_registro DESC
                LIMIT 20";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene estadísticas de asistencia promedio por grupo
     */
    public function asistenciaPromedioPorGrupo() {
        if (!$this->filtros || $this->filtros['nivel'] != 3 || empty($this->filtros['grupos_ids'])) {
            return [];
        }

        $grupos_ids = array_map('intval', $this->filtros['grupos_ids']);
        $sql = "SELECT 
                    g.nombre as grupo,
                    COUNT(DISTINCT tg.id) as total_tutorias,
                    COUNT(DISTINCT a.id_alumno) as total_alumnos,
                    COUNT(DISTINCT tga.alumno_id) as alumnos_con_registro,
                    ROUND(AVG(porcentaje_asistencia_alumno), 2) as promedio_asistencia_grupo
                FROM grupos g
                LEFT JOIN tutorias_grupales tg ON g.id_grupo = tg.grupo_id
                LEFT JOIN alumnos a ON g.id_grupo = a.grupos_id_grupo AND a.estatus = 1
                LEFT JOIN (
                    SELECT 
                        tga.tutoria_grupal_id,
                        tga.alumno_id,
                        (SUM(CASE WHEN tga.presente = 1 THEN 1 ELSE 0 END) * 100.0 / COUNT(*)) as porcentaje_asistencia_alumno
                    FROM tutorias_grupales_asistencia tga
                    INNER JOIN tutorias_grupales tg2 ON tga.tutoria_grupal_id = tg2.id
                    WHERE tg2.grupo_id IN (" . implode(',', $grupos_ids) . ")
                    GROUP BY tga.alumno_id
                ) asistencia_alumno ON a.id_alumno = asistencia_alumno.alumno_id
                LEFT JOIN tutorias_grupales_asistencia tga ON tg.id = tga.tutoria_grupal_id
                WHERE g.id_grupo IN (" . implode(',', $grupos_ids) . ")
                GROUP BY g.id_grupo, g.nombre
                ORDER BY promedio_asistencia_grupo DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene resumen ejecutivo para tutores
     */
    public function resumenEjecutivoTutor() {
        if (!$this->filtros || $this->filtros['nivel'] != 3 || empty($this->filtros['grupos_ids']) || !$this->filtros['usuario_id']) {
            return [];
        }

        $grupos_ids = array_map('intval', $this->filtros['grupos_ids']);
        $usuario_id = (int)$this->filtros['usuario_id'];
        
        $sql = "SELECT 
                    (SELECT COUNT(DISTINCT g.id_grupo) FROM grupos g WHERE g.id_grupo IN (" . implode(',', $grupos_ids) . ")) as total_grupos,
                    (SELECT COUNT(DISTINCT a.id_alumno) FROM alumnos a WHERE a.grupos_id_grupo IN (" . implode(',', $grupos_ids) . ") AND a.estatus = 1) as total_alumnos,
                    (SELECT COUNT(DISTINCT tg.id) FROM tutorias_grupales tg WHERE tg.grupo_id IN (" . implode(',', $grupos_ids) . ") AND tg.usuario_id = $usuario_id) as tutorias_grupales_totales,
                    (SELECT COUNT(DISTINCT tg.id) FROM tutorias_grupales tg WHERE tg.grupo_id IN (" . implode(',', $grupos_ids) . ") AND tg.usuario_id = $usuario_id AND tg.fecha >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)) as tutorias_grupales_mes,
                    (SELECT COUNT(ti.id) FROM tutorias_individuales ti WHERE ti.grupo_id IN (" . implode(',', $grupos_ids) . ") AND ti.usuario_id = $usuario_id) as tutorias_individuales_totales,
                    (SELECT COUNT(ti.id) FROM tutorias_individuales ti WHERE ti.grupo_id IN (" . implode(',', $grupos_ids) . ") AND ti.usuario_id = $usuario_id AND ti.fecha >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)) as tutorias_individuales_mes,
                    (SELECT COUNT(s.id_seguimiento) FROM seguimientos s INNER JOIN alumnos a ON s.alumnos_id_alumno = a.id_alumno WHERE a.grupos_id_grupo IN (" . implode(',', $grupos_ids) . ") AND s.tutor_id = $usuario_id AND s.estatus IN (1, 2)) as seguimientos_abiertos,
                    (SELECT COUNT(ard.id) FROM alumno_riesgo_desercion ard INNER JOIN alumnos a ON ard.alumno_id = a.id_alumno WHERE a.grupos_id_grupo IN (" . implode(',', $grupos_ids) . ") AND ard.posible = 1 AND ard.periodo_id = (SELECT id FROM periodos_escolares WHERE activo = 1 LIMIT 1)) as alumnos_riesgo,
                    (SELECT COUNT(c.id) FROM canalizacion c INNER JOIN alumnos a ON c.alumno_id = a.id_alumno WHERE a.grupos_id_grupo IN (" . implode(',', $grupos_ids) . ") AND c.estatus = 'PENDIENTE') as canalizaciones_pendientes";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>