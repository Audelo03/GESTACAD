<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Estadisticas.php";
require_once __DIR__ . "/../models/Usuario.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

class EstadisticasController {
    public $estadisticas;
    private $usuario;

    public function __construct($conn) {
        $this->estadisticas = new Estadisticas($conn);
        $this->usuario = new Usuario($conn);
        $this->configurarFiltrosPorRol();
    }

    /**
     * Configura los filtros según el rol del usuario
     */
    private function configurarFiltrosPorRol() {
        if (!isset($_SESSION["usuario_id"]) || !isset($_SESSION["usuario_nivel"])) {
            // Si no hay sesión, no se configuran filtros (acceso denegado)
            return;
        }

        $usuario_id = $_SESSION["usuario_id"];
        $nivel = $_SESSION["usuario_nivel"];
        
        $filtros = [
            'nivel' => (int)$nivel,
            'usuario_id' => (int)$usuario_id,
            'carrera_id' => null,
            'grupos_ids' => null
        ];

        // Configurar filtros según el rol
        if ($nivel == 2) {
            // Coordinador: obtener su carrera
            $carrera_id = $this->usuario->getCarrreraIdByUsuarioId($usuario_id);
            $filtros['carrera_id'] = $carrera_id;
        } elseif ($nivel == 3) {
            // Tutor: obtener sus grupos
            $grupos_ids = $this->usuario->getGruposIdByUsuarioId($usuario_id);
            $filtros['grupos_ids'] = $grupos_ids;
        }
        // Nivel 1 (Admin) no necesita filtros adicionales

        $this->estadisticas->setFiltros($filtros);
    }

    public function obtenerEstadisticas() {
        $datos = [];
        
        // --- Estadísticas Básicas ---
        $datos['total_alumnos'] = $this->estadisticas->totalAlumnos();
        $datos['total_carreras'] = $this->estadisticas->totalCarreras();
        $datos['total_grupos'] = $this->estadisticas->totalGrupos();
        $datos['alumnos_por_carrera'] = $this->estadisticas->alumnosPorCarrera();
        
        // --- Estadísticas Existentes ---
        $datos['alumnos_por_estatus'] = $this->estadisticas->alumnosPorEstatus();
        $datos['grupos_por_modalidad'] = $this->estadisticas->gruposPorModalidad();
        $datos['tasa_asistencia'] = 0; // Ya no se calcula desde tabla asistencias
        $datos['seguimientos_por_estatus'] = $this->estadisticas->seguimientosPorEstatus();
        $datos['seguimientos_por_tipo'] = $this->estadisticas->seguimientosPorTipo();
        
        // --- Nuevas Estadísticas Avanzadas ---
        // Usuarios por nivel solo para admin
        if (!isset($_SESSION["usuario_nivel"]) || $_SESSION["usuario_nivel"] == 1) {
            $datos['usuarios_por_nivel'] = $this->estadisticas->usuariosPorNivel();
        } else {
            $datos['usuarios_por_nivel'] = [];
        }
        
        $datos['alumnos_por_grupo'] = $this->estadisticas->alumnosPorGrupo();
        $datos['asistencia_por_mes'] = []; // Ya no se calcula desde tabla asistencias
        $datos['seguimientos_por_mes'] = $this->estadisticas->seguimientosPorMes();
        $datos['productividad_tutores'] = $this->estadisticas->productividadTutores();
        $datos['alumnos_por_anio_ingreso'] = $this->estadisticas->alumnosPorAnioIngreso();
        $datos['carreras_mas_populares'] = $this->estadisticas->carrerasMasPopulares();
        $datos['modalidades_mas_utilizadas'] = $this->estadisticas->modalidadesMasUtilizadas();
        $datos['estadisticas_generales'] = $this->estadisticas->estadisticasGenerales();

        // --- Estadísticas específicas para tutores ---
        if (isset($_SESSION["usuario_nivel"]) && $_SESSION["usuario_nivel"] == 3) {
            $datos['resumen_grupos_tutor'] = $this->estadisticas->resumenGruposTutor();
            $datos['alumnos_seguimientos_abiertos'] = $this->estadisticas->alumnosConSeguimientosAbiertos();
            $datos['alumnos_riesgo_desercion'] = $this->estadisticas->alumnosConRiesgoDesercion();
            $datos['canalizaciones_pendientes'] = $this->estadisticas->canalizacionesPendientes();
            $datos['actividad_reciente_tutor'] = $this->estadisticas->actividadRecienteTutor();
            $datos['asistencia_promedio_por_grupo'] = $this->estadisticas->asistenciaPromedioPorGrupo();
            $datos['resumen_ejecutivo_tutor'] = $this->estadisticas->resumenEjecutivoTutor();
        } else {
            // Para otros roles, inicializar como arrays vacíos
            $datos['resumen_grupos_tutor'] = [];
            $datos['alumnos_seguimientos_abiertos'] = [];
            $datos['alumnos_riesgo_desercion'] = [];
            $datos['canalizaciones_pendientes'] = [];
            $datos['actividad_reciente_tutor'] = [];
            $datos['asistencia_promedio_por_grupo'] = [];
            $datos['resumen_ejecutivo_tutor'] = [];
        }

        return $datos;
    }
}

if (isset($_GET['accion']) && $_GET['accion'] == 'obtener_datos') {
    header('Content-Type: application/json');
    $controller = new EstadisticasController($conn);
    $datos = $controller->obtenerEstadisticas();
    echo json_encode($datos);
}
?>