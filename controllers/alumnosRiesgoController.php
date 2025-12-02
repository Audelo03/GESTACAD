<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Alumno.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

class AlumnosRiesgoController
{
    private $conn;
    private $apiUrl = 'http://localhost:5000'; // URL de la API de Python

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * Obtiene alumnos en riesgo según el rol del usuario
     */
    public function obtenerAlumnosEnRiesgo($filtros = [])
    {
        try {
            $nivel = isset($_SESSION['usuario_nivel']) ? (int)$_SESSION['usuario_nivel'] : null;
            $usuario_id = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : null;
            
            // Obtener periodo activo por defecto
            $periodo_id = $filtros['periodo_id'] ?? $this->obtenerPeriodoActivo();
            
            // Obtener alumnos según el rol
            $alumnos = $this->obtenerAlumnosPorRol($nivel, $usuario_id, $filtros);
            
            if (empty($alumnos)) {
                return [];
            }
            
            // Analizar riesgo para cada alumno usando la API
            $alumnosRiesgo = [];
            
            foreach ($alumnos as $alumno) {
                $riesgo = $this->analizarRiesgoAlumno($alumno['id_alumno'], $periodo_id);
                
                if ($riesgo && isset($riesgo['nivel_riesgo'])) {
                    $nivelRiesgo = $riesgo['nivel_riesgo'];
                    
                    // Filtrar por nivel de riesgo si se especifica
                    if (isset($filtros['nivel_riesgo']) && $filtros['nivel_riesgo'] !== '') {
                        if ($nivelRiesgo !== $filtros['nivel_riesgo']) {
                            continue;
                        }
                    }
                    
                    // Filtrar solo si es de riesgo medio, alto o crítico
                    if ($nivelRiesgo === 'BAJO') {
                        continue;
                    }
                    
                    $alumno['riesgo'] = [
                        'nivel_riesgo' => $nivelRiesgo,
                        'score_riesgo' => $riesgo['score_riesgo'] ?? 0,
                        'explicacion' => $riesgo['explicacion'] ?? '',
                        'recomendaciones' => $riesgo['recomendaciones'] ?? [],
                        'posible_desercion' => $riesgo['posible_desercion'] ?? false
                    ];
                    
                    // Obtener estadísticas adicionales
                    $estadisticas = $this->obtenerEstadisticasAlumno($alumno['id_alumno'], $periodo_id);
                    if ($estadisticas) {
                        $alumno['estadisticas'] = [
                            'materias_reprobadas' => $estadisticas['materias_reprobadas'] ?? 0,
                            'calificacion_promedio' => $estadisticas['calificacion_promedio'] ?? 0,
                            'asistencia_tutorias_grupales' => $estadisticas['asistencia_tutorias_grupales'] ?? 100,
                            'seguimientos_abiertos' => $estadisticas['seguimientos_abiertos'] ?? 0
                        ];
                    }
                    
                    $alumnosRiesgo[] = $alumno;
                }
            }
            
            // Ordenar por score de riesgo (mayor a menor)
            usort($alumnosRiesgo, function($a, $b) {
                $scoreA = $a['riesgo']['score_riesgo'] ?? 0;
                $scoreB = $b['riesgo']['score_riesgo'] ?? 0;
                return $scoreB <=> $scoreA;
            });
            
            return $alumnosRiesgo;
            
        } catch (Exception $e) {
            error_log("Error en obtenerAlumnosEnRiesgo: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene alumnos según el rol del usuario
     */
    private function obtenerAlumnosPorRol($nivel, $usuario_id, $filtros = [])
    {
        $sql = "SELECT a.id_alumno, a.matricula, a.nombre, a.apellido_paterno, 
                       a.apellido_materno, g.nombre as grupo_nombre, 
                       c.nombre as carrera_nombre, c.id_carrera
                FROM alumnos a
                LEFT JOIN grupos g ON a.grupos_id_grupo = g.id_grupo
                LEFT JOIN carreras c ON a.carreras_id_carrera = c.id_carrera
                WHERE a.estatus = 1";
        
        $params = [];
        
        // Filtrar según rol
        if ($nivel == 3) { // TUTOR
            // Solo alumnos de los grupos del tutor
            $sql .= " AND g.usuarios_id_usuario_tutor = :tutor_id";
            $params[':tutor_id'] = $usuario_id;
        } elseif ($nivel == 2) { // COORDINADOR
            // Solo alumnos de la carrera del coordinador
            $sql_coord = "SELECT carreras_id_carrera FROM usuarios WHERE id_usuario = :usuario_id";
            $stmt_coord = $this->conn->prepare($sql_coord);
            $stmt_coord->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt_coord->execute();
            $coordinador = $stmt_coord->fetch(PDO::FETCH_ASSOC);
            
            if ($coordinador && isset($coordinador['carreras_id_carrera'])) {
                $sql .= " AND a.carreras_id_carrera = :carrera_id";
                $params[':carrera_id'] = $coordinador['carreras_id_carrera'];
            }
        }
        
        // Filtros adicionales
        if (isset($filtros['carrera_id']) && $filtros['carrera_id']) {
            $sql .= " AND a.carreras_id_carrera = :filtro_carrera_id";
            $params[':filtro_carrera_id'] = $filtros['carrera_id'];
        }
        
        if (isset($filtros['grupo_id']) && $filtros['grupo_id']) {
            $sql .= " AND a.grupos_id_grupo = :filtro_grupo_id";
            $params[':filtro_grupo_id'] = $filtros['grupo_id'];
        }
        
        if (isset($filtros['busqueda']) && $filtros['busqueda']) {
            $busqueda = '%' . $filtros['busqueda'] . '%';
            $sql .= " AND (a.matricula LIKE :busqueda 
                    OR a.nombre LIKE :busqueda 
                    OR a.apellido_paterno LIKE :busqueda
                    OR a.apellido_materno LIKE :busqueda)";
            $params[':busqueda'] = $busqueda;
        }
        
        $sql .= " ORDER BY a.apellido_paterno, a.apellido_materno, a.nombre";
        
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Analiza el riesgo de un alumno usando la API de Python
     */
    private function analizarRiesgoAlumno($alumno_id, $periodo_id = null)
    {
        try {
            $url = $this->apiUrl . '/api/riesgo/' . $alumno_id;
            if ($periodo_id) {
                $url .= '?periodo_id=' . $periodo_id;
            }
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            
            $response = curl_exec($ch);
            
            if (curl_errno($ch)) {
                error_log("Error de cURL al analizar riesgo del alumno $alumno_id: " . curl_error($ch));
                curl_close($ch);
                return null;
            }
            
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200 && $response) {
                $data = json_decode($response, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $data;
                }
            }
            
            return null;
        } catch (Exception $e) {
            error_log("Error al analizar riesgo del alumno $alumno_id: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtiene estadísticas de un alumno desde la API
     */
    private function obtenerEstadisticasAlumno($alumno_id, $periodo_id = null)
    {
        try {
            $url = $this->apiUrl . '/api/estadisticas/' . $alumno_id;
            if ($periodo_id) {
                $url .= '?periodo_id=' . $periodo_id;
            }
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            
            $response = curl_exec($ch);
            
            if (curl_errno($ch)) {
                error_log("Error de cURL al obtener estadísticas del alumno $alumno_id: " . curl_error($ch));
                curl_close($ch);
                return null;
            }
            
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200 && $response) {
                $data = json_decode($response, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $data;
                }
            }
            
            return null;
        } catch (Exception $e) {
            error_log("Error al obtener estadísticas del alumno $alumno_id: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtiene el periodo activo
     */
    private function obtenerPeriodoActivo()
    {
        try {
            $sql = "SELECT id FROM periodos_escolares WHERE activo = 1 LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $periodo = $stmt->fetch(PDO::FETCH_ASSOC);
            return $periodo ? $periodo['id'] : null;
        } catch (Exception $e) {
            error_log("Error al obtener periodo activo: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtiene las carreras disponibles según el rol
     */
    public function obtenerCarrerasDisponibles()
    {
        try {
            $nivel = isset($_SESSION['usuario_nivel']) ? (int)$_SESSION['usuario_nivel'] : null;
            $usuario_id = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : null;
            
            $sql = "SELECT DISTINCT c.id_carrera, c.nombre 
                    FROM carreras c
                    INNER JOIN alumnos a ON c.id_carrera = a.carreras_id_carrera
                    WHERE a.estatus = 1";
            
            $params = [];
            
            if ($nivel == 2 && $usuario_id) { // COORDINADOR
                $sql_coord = "SELECT carreras_id_carrera FROM usuarios WHERE id_usuario = :usuario_id";
                $stmt_coord = $this->conn->prepare($sql_coord);
                $stmt_coord->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                $stmt_coord->execute();
                $coordinador = $stmt_coord->fetch(PDO::FETCH_ASSOC);
                
                if ($coordinador && isset($coordinador['carreras_id_carrera'])) {
                    $sql .= " AND c.id_carrera = :carrera_id";
                    $params[':carrera_id'] = $coordinador['carreras_id_carrera'];
                }
            }
            
            $sql .= " ORDER BY c.nombre";
            
            $stmt = $this->conn->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error al obtener carreras: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene los grupos disponibles según el rol
     */
    public function obtenerGruposDisponibles($carrera_id = null)
    {
        try {
            $nivel = isset($_SESSION['usuario_nivel']) ? (int)$_SESSION['usuario_nivel'] : null;
            $usuario_id = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : null;
            
            $sql = "SELECT DISTINCT g.id_grupo, g.nombre, c.nombre as carrera_nombre
                    FROM grupos g
                    INNER JOIN alumnos a ON g.id_grupo = a.grupos_id_grupo
                    LEFT JOIN carreras c ON g.carreras_id_carrera = c.id_carrera
                    WHERE a.estatus = 1 AND g.estatus = 1";
            
            $params = [];
            
            if ($nivel == 3 && $usuario_id) { // TUTOR
                $sql .= " AND g.usuarios_id_usuario_tutor = :tutor_id";
                $params[':tutor_id'] = $usuario_id;
            } elseif ($nivel == 2 && $usuario_id) { // COORDINADOR
                $sql_coord = "SELECT carreras_id_carrera FROM usuarios WHERE id_usuario = :usuario_id";
                $stmt_coord = $this->conn->prepare($sql_coord);
                $stmt_coord->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                $stmt_coord->execute();
                $coordinador = $stmt_coord->fetch(PDO::FETCH_ASSOC);
                
                if ($coordinador && isset($coordinador['carreras_id_carrera'])) {
                    $sql .= " AND g.carreras_id_carrera = :carrera_id";
                    $params[':carrera_id'] = $coordinador['carreras_id_carrera'];
                }
            }
            
            if ($carrera_id) {
                $sql .= " AND g.carreras_id_carrera = :filtro_carrera_id";
                $params[':filtro_carrera_id'] = $carrera_id;
            }
            
            $sql .= " ORDER BY g.nombre";
            
            $stmt = $this->conn->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error al obtener grupos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene los periodos escolares
     */
    public function obtenerPeriodos()
    {
        try {
            $sql = "SELECT id, nombre, fecha_inicio, fecha_fin, activo 
                    FROM periodos_escolares 
                    ORDER BY fecha_inicio DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error al obtener periodos: " . $e->getMessage());
            return [];
        }
    }
}

