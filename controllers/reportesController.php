<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Periodo.php";
require_once __DIR__ . "/../models/Beca.php";
require_once __DIR__ . "/../models/RiesgoDesercion.php";
require_once __DIR__ . "/../models/Canalizacion.php";
require_once __DIR__ . "/../models/Usuario.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

class ReportesController {
    private $conn;
    private $periodo;
    private $beca;
    private $riesgo;
    private $canalizacion;
    private $usuario;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->periodo = new Periodo($conn);
        $this->beca = new Beca($conn);
        $this->riesgo = new RiesgoDesercion($conn);
        $this->canalizacion = new Canalizacion($conn);
        $this->usuario = new Usuario($conn);
    }

    /**
     * Obtiene los períodos disponibles según el rol
     */
    public function obtenerPeriodos() {
        return $this->periodo->getAll();
    }

    /**
     * Obtiene los parciales de un período
     */
    public function obtenerParciales($periodo_id) {
        $sql = "SELECT * FROM parciales WHERE periodo_id = :periodo_id ORDER BY numero ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":periodo_id", $periodo_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene los grupos disponibles según el rol
     */
    public function obtenerGrupos($periodo_id = null, $carrera_id = null) {
        $nivel = isset($_SESSION["usuario_nivel"]) ? (int)$_SESSION["usuario_nivel"] : 1;
        $usuario_id = isset($_SESSION["usuario_id"]) ? (int)$_SESSION["usuario_id"] : null;

        if ($nivel == 3 && $usuario_id) {
            // Tutor: solo sus grupos
            $sql = "SELECT g.id_grupo, g.nombre, c.nombre as carrera_nombre, m.nombre as modalidad_nombre
                    FROM grupos g
                    LEFT JOIN carreras c ON g.carreras_id_carrera = c.id_carrera
                    LEFT JOIN modalidades m ON g.modalidades_id_modalidad = m.id_modalidad
                    WHERE g.usuarios_id_usuario_tutor = :usuario_id AND g.estatus = 1
                    ORDER BY g.nombre ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":usuario_id", $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } elseif ($nivel == 2 && $carrera_id) {
            // Coordinador: grupos de su carrera
            $sql = "SELECT g.id_grupo, g.nombre, c.nombre as carrera_nombre, m.nombre as modalidad_nombre
                    FROM grupos g
                    LEFT JOIN carreras c ON g.carreras_id_carrera = c.id_carrera
                    LEFT JOIN modalidades m ON g.modalidades_id_modalidad = m.id_modalidad
                    WHERE g.carreras_id_carrera = :carrera_id AND g.estatus = 1
                    ORDER BY g.nombre ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":carrera_id", $carrera_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Admin: todos los grupos
            $sql = "SELECT g.id_grupo, g.nombre, c.nombre as carrera_nombre, m.nombre as modalidad_nombre
                    FROM grupos g
                    LEFT JOIN carreras c ON g.carreras_id_carrera = c.id_carrera
                    LEFT JOIN modalidades m ON g.modalidades_id_modalidad = m.id_modalidad
                    WHERE g.estatus = 1";
            
            if ($carrera_id) {
                $sql .= " AND g.carreras_id_carrera = :carrera_id";
            }
            
            $sql .= " ORDER BY g.nombre ASC";
            $stmt = $this->conn->prepare($sql);
            if ($carrera_id) {
                $stmt->bindParam(":carrera_id", $carrera_id, PDO::PARAM_INT);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    /**
     * Obtiene las carreras disponibles según el rol
     */
    public function obtenerCarreras() {
        $nivel = isset($_SESSION["usuario_nivel"]) ? (int)$_SESSION["usuario_nivel"] : 1;
        $usuario_id = isset($_SESSION["usuario_id"]) ? (int)$_SESSION["usuario_id"] : null;

        if ($nivel == 2 && $usuario_id) {
            // Coordinador: solo su carrera
            $carrera_id = $this->usuario->getCarrreraIdByUsuarioId($usuario_id);
            if ($carrera_id) {
                $sql = "SELECT * FROM carreras WHERE id_carrera = :carrera_id AND estatus = 1";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":carrera_id", $carrera_id, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            return [];
        } else {
            // Admin: todas las carreras
            $sql = "SELECT * FROM carreras WHERE estatus = 1 ORDER BY nombre ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    /**
     * Genera el reporte completo por parcial y grupo
     */
    public function generarReporte($parcial_id, $grupo_id) {
        // Obtener información del parcial y grupo
        $parcial = $this->obtenerParcial($parcial_id);
        if (!$parcial) {
            return ['error' => 'Parcial no encontrado'];
        }

        $grupo = $this->obtenerGrupo($grupo_id);
        if (!$grupo) {
            return ['error' => 'Grupo no encontrado'];
        }

        $periodo_id = $parcial['periodo_id'];

        // Información general
        $reporte = [
            'parcial' => $parcial,
            'grupo' => $grupo,
            'periodo' => $this->periodo->getById($periodo_id),
            'total_estudiantes' => $this->obtenerTotalEstudiantes($grupo_id),
            
            // Sección 4: Posible deserción
            'posible_desercion' => $this->obtenerPosibleDesercion($grupo_id, $periodo_id),
            
            // Sección 5: Becas
            'becas' => $this->obtenerBecas($grupo_id, $periodo_id),
            
            // Sección 6: Áreas de apoyo (canalizaciones)
            'areas_apoyo' => $this->obtenerAreasApoyo($grupo_id, $periodo_id),
            
            // Sección 8: Aprobado
            'aprobado' => $this->obtenerAprobado($grupo_id, $parcial_id, $periodo_id),
            
            // Sección 9: Reprobación
            'reprobacion' => $this->obtenerReprobacion($grupo_id, $parcial_id, $periodo_id),
            
            // Sección 10: Asesorías (tutorías)
            'asesorias' => $this->obtenerAsesorias($grupo_id, $parcial_id, $periodo_id),
            
            // Causas de reprobación
            'causas_reprobacion' => $this->obtenerCausasReprobacion($grupo_id, $periodo_id)
        ];

        return $reporte;
    }

    /**
     * Obtiene información de un parcial
     */
    public function obtenerParcial($parcial_id) {
        $sql = "SELECT * FROM parciales WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $parcial_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene información de un grupo
     */
    private function obtenerGrupo($grupo_id) {
        $sql = "SELECT g.*, c.nombre as carrera_nombre, m.nombre as modalidad_nombre
                FROM grupos g
                LEFT JOIN carreras c ON g.carreras_id_carrera = c.id_carrera
                LEFT JOIN modalidades m ON g.modalidades_id_modalidad = m.id_modalidad
                WHERE g.id_grupo = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $grupo_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el total de estudiantes en un grupo
     */
    private function obtenerTotalEstudiantes($grupo_id) {
        $sql = "SELECT COUNT(*) as total FROM alumnos WHERE grupos_id_grupo = :grupo_id AND estatus = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":grupo_id", $grupo_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total'];
    }

    /**
     * Obtiene los alumnos en posible deserción
     */
    private function obtenerPosibleDesercion($grupo_id, $periodo_id) {
        $sql = "SELECT a.id_alumno, 
                       CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', COALESCE(a.apellido_materno, '')) as nombre_completo,
                       ard.nivel, ard.motivo, ard.fuente
                FROM alumnos a
                INNER JOIN alumno_riesgo_desercion ard ON a.id_alumno = ard.alumno_id
                WHERE a.grupos_id_grupo = :grupo_id 
                  AND ard.periodo_id = :periodo_id
                  AND ard.posible = 1
                  AND a.estatus = 1
                ORDER BY a.apellido_paterno, a.apellido_materno, a.nombre";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":grupo_id", $grupo_id, PDO::PARAM_INT);
        $stmt->bindParam(":periodo_id", $periodo_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene las becas asignadas en el grupo
     */
    private function obtenerBecas($grupo_id, $periodo_id) {
        $sql = "SELECT cb.id, cb.clave, cb.nombre,
                       COUNT(ab.id) as cantidad,
                       (SELECT COUNT(*) FROM alumnos WHERE grupos_id_grupo = :grupo_id AND estatus = 1) as total_alumnos
                FROM cat_becas cb
                LEFT JOIN alumno_beca ab ON cb.id = ab.beca_id 
                    AND ab.periodo_id = :periodo_id
                    AND ab.alumno_id IN (SELECT id_alumno FROM alumnos WHERE grupos_id_grupo = :grupo_id2 AND estatus = 1)
                WHERE cb.activo = 1
                GROUP BY cb.id, cb.clave, cb.nombre
                ORDER BY cb.nombre";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":grupo_id", $grupo_id, PDO::PARAM_INT);
        $stmt->bindParam(":grupo_id2", $grupo_id, PDO::PARAM_INT);
        $stmt->bindParam(":periodo_id", $periodo_id, PDO::PARAM_INT);
        $stmt->execute();
        $becas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calcular porcentajes
        foreach ($becas as &$beca) {
            $total = (int)$beca['total_alumnos'];
            $cantidad = (int)$beca['cantidad'];
            $beca['porcentaje'] = $total > 0 ? round(($cantidad / $total) * 100, 2) : 0;
        }
        
        return $becas;
    }

    /**
     * Obtiene las áreas de apoyo (canalizaciones)
     */
    private function obtenerAreasApoyo($grupo_id, $periodo_id) {
        $areas = [
            ['nombre' => 'Psicología', 'cantidad' => 0],
            ['nombre' => 'Médico', 'cantidad' => 0],
            ['nombre' => 'Otro', 'cantidad' => 0]
        ];

        $sql = "SELECT cac.nombre, COUNT(c.id) as cantidad
                FROM canalizacion c
                INNER JOIN cat_areas_canalizacion cac ON c.area_id = cac.id
                INNER JOIN alumnos a ON c.alumno_id = a.id_alumno
                WHERE a.grupos_id_grupo = :grupo_id 
                  AND c.periodo_id = :periodo_id
                  AND a.estatus = 1
                GROUP BY cac.nombre";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":grupo_id", $grupo_id, PDO::PARAM_INT);
        $stmt->bindParam(":periodo_id", $periodo_id, PDO::PARAM_INT);
        $stmt->execute();
        $canalizaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total_alumnos = $this->obtenerTotalEstudiantes($grupo_id);

        // Mapear las áreas encontradas
        foreach ($canalizaciones as $can) {
            $nombre = strtolower($can['nombre']);
            if (strpos($nombre, 'psicol') !== false) {
                $areas[0]['cantidad'] = (int)$can['cantidad'];
            } elseif (strpos($nombre, 'médic') !== false || strpos($nombre, 'medic') !== false || strpos($nombre, 'salud') !== false) {
                $areas[1]['cantidad'] = (int)$can['cantidad'];
            } else {
                $areas[2]['cantidad'] += (int)$can['cantidad'];
            }
        }

        // Calcular porcentajes
        foreach ($areas as &$area) {
            $area['porcentaje'] = $total_alumnos > 0 ? round(($area['cantidad'] / $total_alumnos) * 100, 2) : 0;
        }

        return $areas;
    }

    /**
     * Obtiene las materias aprobadas en el parcial
     */
    private function obtenerAprobado($grupo_id, $parcial_id, $periodo_id) {
        // Obtener el número del parcial
        $parcial = $this->obtenerParcial($parcial_id);
        $numero_parcial = $parcial['numero'];
        $campo_estado = 'estado_parcial' . $numero_parcial;

        $sql = "SELECT asig.id, asig.clave, asig.nombre,
                       COUNT(CASE WHEN i.{$campo_estado} = 'APROBADO' THEN 1 END) as cantidad_aprobados,
                       COUNT(DISTINCT i.alumno_id) as total_inscritos
                FROM asignaturas asig
                INNER JOIN clases cl ON asig.id = cl.asignatura_id
                INNER JOIN inscripciones i ON cl.id = i.clase_id
                INNER JOIN alumnos a ON i.alumno_id = a.id_alumno
                WHERE a.grupos_id_grupo = :grupo_id
                  AND cl.periodo_id = :periodo_id
                  AND asig.activo = 1
                  AND a.estatus = 1
                GROUP BY asig.id, asig.clave, asig.nombre
                ORDER BY asig.nombre";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":grupo_id", $grupo_id, PDO::PARAM_INT);
        $stmt->bindParam(":periodo_id", $periodo_id, PDO::PARAM_INT);
        $stmt->execute();
        $materias = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calcular porcentajes
        foreach ($materias as &$materia) {
            $total = (int)$materia['total_inscritos'];
            $aprobados = (int)$materia['cantidad_aprobados'];
            $materia['porcentaje'] = $total > 0 ? round(($aprobados / $total) * 100, 2) : 0;
        }

        return $materias;
    }

    /**
     * Obtiene las materias reprobadas en el parcial
     */
    private function obtenerReprobacion($grupo_id, $parcial_id, $periodo_id) {
        // Obtener el número del parcial
        $parcial = $this->obtenerParcial($parcial_id);
        $numero_parcial = $parcial['numero'];
        $campo_estado = 'estado_parcial' . $numero_parcial;

        $sql = "SELECT asig.id, asig.clave, asig.nombre,
                       COUNT(CASE WHEN i.{$campo_estado} = 'REPROBADO' THEN 1 END) as cantidad_reprobados,
                       COUNT(DISTINCT i.alumno_id) as total_inscritos
                FROM asignaturas asig
                INNER JOIN clases cl ON asig.id = cl.asignatura_id
                INNER JOIN inscripciones i ON cl.id = i.clase_id
                INNER JOIN alumnos a ON i.alumno_id = a.id_alumno
                WHERE a.grupos_id_grupo = :grupo_id
                  AND cl.periodo_id = :periodo_id
                  AND asig.activo = 1
                  AND a.estatus = 1
                GROUP BY asig.id, asig.clave, asig.nombre
                ORDER BY asig.nombre";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":grupo_id", $grupo_id, PDO::PARAM_INT);
        $stmt->bindParam(":periodo_id", $periodo_id, PDO::PARAM_INT);
        $stmt->execute();
        $materias = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calcular porcentajes
        foreach ($materias as &$materia) {
            $total = (int)$materia['total_inscritos'];
            $reprobados = (int)$materia['cantidad_reprobados'];
            $materia['porcentaje'] = $total > 0 ? round(($reprobados / $total) * 100, 2) : 0;
        }

        return $materias;
    }

    /**
     * Obtiene las asesorías (tutorías individuales) del parcial
     */
    private function obtenerAsesorias($grupo_id, $parcial_id, $periodo_id) {
        // Obtener las fechas del parcial para filtrar las tutorías
        $parcial = $this->obtenerParcial($parcial_id);
        if (!$parcial) {
            return [];
        }
        
        $fecha_inicio = $parcial['fecha_inicio'];
        $fecha_fin = $parcial['fecha_fin'];
        
        $sql = "SELECT asig.id, asig.clave, asig.nombre,
                       COUNT(DISTINCT ti.id) as cantidad_asesorias,
                       COUNT(DISTINCT i.alumno_id) as total_inscritos
                FROM asignaturas asig
                INNER JOIN clases cl ON asig.id = cl.asignatura_id
                INNER JOIN inscripciones i ON cl.id = i.clase_id
                INNER JOIN alumnos a ON i.alumno_id = a.id_alumno
                LEFT JOIN tutorias_individuales ti ON ti.alumno_id = a.id_alumno 
                    AND ti.grupo_id = :grupo_id
                    AND ti.fecha >= :fecha_inicio
                    AND ti.fecha <= :fecha_fin
                WHERE a.grupos_id_grupo = :grupo_id2
                  AND cl.periodo_id = :periodo_id
                  AND asig.activo = 1
                  AND a.estatus = 1
                GROUP BY asig.id, asig.clave, asig.nombre
                HAVING cantidad_asesorias > 0
                ORDER BY asig.nombre";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":grupo_id", $grupo_id, PDO::PARAM_INT);
        $stmt->bindParam(":grupo_id2", $grupo_id, PDO::PARAM_INT);
        $stmt->bindParam(":fecha_inicio", $fecha_inicio);
        $stmt->bindParam(":fecha_fin", $fecha_fin);
        $stmt->bindParam(":periodo_id", $periodo_id, PDO::PARAM_INT);
        $stmt->execute();
        $asesorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calcular porcentajes
        foreach ($asesorias as &$asesoria) {
            $total = (int)$asesoria['total_inscritos'];
            $cantidad = (int)$asesoria['cantidad_asesorias'];
            $asesoria['porcentaje'] = $total > 0 ? round(($cantidad / $total) * 100, 2) : 0;
        }

        return $asesorias;
    }

    /**
     * Obtiene las causas de reprobación (basado en seguimientos y canalizaciones)
     */
    private function obtenerCausasReprobacion($grupo_id, $periodo_id) {
        // Obtener las fechas del período para filtrar los seguimientos
        $periodo = $this->periodo->getById($periodo_id);
        if (!$periodo) {
            return [];
        }
        
        $fecha_inicio = $periodo['fecha_inicio'];
        $fecha_fin = $periodo['fecha_fin'];
        
        // Causas predefinidas basadas en los datos comunes
        $causas = [
            'Económicas' => 0,
            'Inasistencias' => 0,
            'No entrega trabajos' => 0,
            'Indisciplina' => 0,
            'Familiares' => 0,
            'Salud' => 0,
            'Conectividad' => 0,
            'Desinterés' => 0,
            'Falta de equipo' => 0,
            'Otra' => 0
        ];

        // Obtener causas desde seguimientos que mencionen reprobación
        // Usar fecha_creacion para filtrar por período y alumnos_id_alumno como nombre de columna
        $sql = "SELECT s.descripcion
                FROM seguimientos s
                INNER JOIN alumnos a ON s.alumnos_id_alumno = a.id_alumno
                WHERE a.grupos_id_grupo = :grupo_id
                  AND s.fecha_creacion >= :fecha_inicio
                  AND s.fecha_creacion <= :fecha_fin
                  AND a.estatus = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":grupo_id", $grupo_id, PDO::PARAM_INT);
        $stmt->bindParam(":fecha_inicio", $fecha_inicio);
        $stmt->bindParam(":fecha_fin", $fecha_fin);
        $stmt->execute();
        $seguimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Analizar descripciones para encontrar causas
        foreach ($seguimientos as $seg) {
            $desc = strtolower($seg['descripcion'] ?? '');
            if (strpos($desc, 'económ') !== false || strpos($desc, 'dinero') !== false) {
                $causas['Económicas']++;
            } elseif (strpos($desc, 'inasist') !== false || strpos($desc, 'falta') !== false) {
                $causas['Inasistencias']++;
            } elseif (strpos($desc, 'trabajo') !== false || strpos($desc, 'tarea') !== false || strpos($desc, 'proyecto') !== false) {
                $causas['No entrega trabajos']++;
            } elseif (strpos($desc, 'disciplina') !== false || strpos($desc, 'comportamiento') !== false) {
                $causas['Indisciplina']++;
            } elseif (strpos($desc, 'familia') !== false || strpos($desc, 'familiar') !== false) {
                $causas['Familiares']++;
            } elseif (strpos($desc, 'salud') !== false || strpos($desc, 'enfermedad') !== false || strpos($desc, 'médico') !== false) {
                $causas['Salud']++;
            } elseif (strpos($desc, 'internet') !== false || strpos($desc, 'conectividad') !== false || strpos($desc, 'wifi') !== false) {
                $causas['Conectividad']++;
            } elseif (strpos($desc, 'desinterés') !== false || strpos($desc, 'motivación') !== false) {
                $causas['Desinterés']++;
            } elseif (strpos($desc, 'equipo') !== false || strpos($desc, 'computadora') !== false || strpos($desc, 'laptop') !== false) {
                $causas['Falta de equipo']++;
            } else {
                $causas['Otra']++;
            }
        }

        // Formatear resultado
        $resultado = [];
        foreach ($causas as $causa => $cantidad) {
            $resultado[] = [
                'causa' => $causa,
                'cantidad' => $cantidad
            ];
        }

        return $resultado;
    }
}

// Manejar peticiones AJAX
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    $controller = new ReportesController($conn);
    
    switch ($_GET['action']) {
        case 'periodos':
            echo json_encode($controller->obtenerPeriodos());
            break;
            
        case 'parciales':
            if (isset($_GET['periodo_id'])) {
                echo json_encode($controller->obtenerParciales($_GET['periodo_id']));
            } else {
                echo json_encode(['error' => 'periodo_id requerido']);
            }
            break;
            
        case 'grupos':
            $periodo_id = isset($_GET['periodo_id']) ? $_GET['periodo_id'] : null;
            $carrera_id = isset($_GET['carrera_id']) ? $_GET['carrera_id'] : null;
            echo json_encode($controller->obtenerGrupos($periodo_id, $carrera_id));
            break;
            
        case 'carreras':
            echo json_encode($controller->obtenerCarreras());
            break;
            
        case 'reporte':
            if (isset($_GET['parcial_id']) && isset($_GET['grupo_id'])) {
                echo json_encode($controller->generarReporte($_GET['parcial_id'], $_GET['grupo_id']));
            } else {
                echo json_encode(['error' => 'parcial_id y grupo_id requeridos']);
            }
            break;
            
        default:
            echo json_encode(['error' => 'Acción no válida']);
    }
    exit;
}
?>

