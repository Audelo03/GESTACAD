-- Script de Actualización de Base de Datos GORA
-- Agrega tablas faltantes y datos de ejemplo

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- 1. Estructura Académica
-- --------------------------------------------------------

-- Tabla: divisiones
CREATE TABLE IF NOT EXISTS `divisiones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `divisiones` (`nombre`, `activo`) VALUES
('Ingeniería y Tecnología', 1),
('Ciencias Económico Administrativas', 1);

-- Tabla: periodos_escolares
CREATE TABLE IF NOT EXISTS `periodos_escolares` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `periodos_escolares` (`nombre`, `fecha_inicio`, `fecha_fin`, `activo`) VALUES
('Enero - Abril 2025', '2025-01-06', '2025-04-25', 0),
('Mayo - Agosto 2025', '2025-05-05', '2025-08-22', 0),
('Septiembre - Diciembre 2025', '2025-09-01', '2025-12-19', 1);

-- Tabla: parciales
CREATE TABLE IF NOT EXISTS `parciales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `periodo_id` int(11) NOT NULL,
  `numero` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `periodo_id` (`periodo_id`),
  CONSTRAINT `fk_parciales_periodo` FOREIGN KEY (`periodo_id`) REFERENCES `periodos_escolares` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `parciales` (`periodo_id`, `numero`, `nombre`, `fecha_inicio`, `fecha_fin`) VALUES
(3, 1, 'Parcial 1', '2025-09-01', '2025-10-03'),
(3, 2, 'Parcial 2', '2025-10-06', '2025-11-07'),
(3, 3, 'Parcial 3', '2025-11-10', '2025-12-12');

-- Tabla: asignaturas
CREATE TABLE IF NOT EXISTS `asignaturas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clave` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `creditos` int(11) NOT NULL,
  `horas_semana` int(11) NOT NULL,
  `area` varchar(50) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `asignaturas` (`clave`, `nombre`, `creditos`, `horas_semana`, `area`, `activo`) VALUES
('MAT101', 'Cálculo Diferencial', 5, 5, 'Ciencias Básicas', 1),
('PRO101', 'Fundamentos de Programación', 6, 6, 'Programación', 1),
('ADM101', 'Administración I', 4, 4, 'Administración', 1);

-- --------------------------------------------------------
-- 2. Gestión de Clases
-- --------------------------------------------------------

-- Tabla: clases
CREATE TABLE IF NOT EXISTS `clases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asignatura_id` int(11) NOT NULL,
  `periodo_id` int(11) NOT NULL,
  `docente_usuario_id` int(10) UNSIGNED NOT NULL,
  `seccion` varchar(10) NOT NULL,
  `modalidad_id` int(10) UNSIGNED DEFAULT NULL,
  `cupo` int(11) DEFAULT 30,
  `grupo_referencia` int(10) UNSIGNED DEFAULT NULL,
  `aula` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `asignatura_id` (`asignatura_id`),
  KEY `periodo_id` (`periodo_id`),
  KEY `docente_usuario_id` (`docente_usuario_id`),
  KEY `modalidad_id` (`modalidad_id`),
  KEY `grupo_referencia` (`grupo_referencia`),
  CONSTRAINT `fk_clases_asignatura` FOREIGN KEY (`asignatura_id`) REFERENCES `asignaturas` (`id`),
  CONSTRAINT `fk_clases_periodo` FOREIGN KEY (`periodo_id`) REFERENCES `periodos_escolares` (`id`),
  CONSTRAINT `fk_clases_docente` FOREIGN KEY (`docente_usuario_id`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `fk_clases_modalidad` FOREIGN KEY (`modalidad_id`) REFERENCES `modalidades` (`id_modalidad`),
  CONSTRAINT `fk_clases_grupo` FOREIGN KEY (`grupo_referencia`) REFERENCES `grupos` (`id_grupo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar clases de ejemplo (Asegúrate de que los IDs de usuario docente existan, usaremos ID 10 y 12 del dump anterior)
INSERT INTO `clases` (`asignatura_id`, `periodo_id`, `docente_usuario_id`, `seccion`, `modalidad_id`, `cupo`, `grupo_referencia`, `aula`) VALUES
(1, 3, 10, 'A', 1, 30, 1, 'B-101'),
(2, 3, 12, 'A', 1, 25, 1, 'Lab-1');

-- Tabla: inscripciones
CREATE TABLE IF NOT EXISTS `inscripciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alumno_id` int(10) UNSIGNED NOT NULL,
  `clase_id` int(11) NOT NULL,
  `cal_parcial1` decimal(4,2) DEFAULT NULL,
  `cal_parcial2` decimal(4,2) DEFAULT NULL,
  `cal_parcial3` decimal(4,2) DEFAULT NULL,
  `cal_parcial4` decimal(4,2) DEFAULT NULL,
  `cal_final` decimal(4,2) DEFAULT NULL,
  `faltas_parcial1` int(11) DEFAULT 0,
  `faltas_parcial2` int(11) DEFAULT 0,
  `faltas_parcial3` int(11) DEFAULT 0,
  `estado` enum('CURSANDO','APROBADO','REPROBADO','BAJA') DEFAULT 'CURSANDO',
  `fecha_alta` date DEFAULT curdate(),
  PRIMARY KEY (`id`),
  KEY `alumno_id` (`alumno_id`),
  KEY `clase_id` (`clase_id`),
  CONSTRAINT `fk_inscripciones_alumno` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id_alumno`),
  CONSTRAINT `fk_inscripciones_clase` FOREIGN KEY (`clase_id`) REFERENCES `clases` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar inscripciones de ejemplo (Usando alumnos existentes ID 2 y 3)
INSERT INTO `inscripciones` (`alumno_id`, `clase_id`, `estado`, `fecha_alta`) VALUES
(2, 1, 'CURSANDO', CURDATE()),
(3, 1, 'CURSANDO', CURDATE()),
(2, 2, 'CURSANDO', CURDATE());

-- --------------------------------------------------------
-- 3. Tutorías y Seguimiento
-- --------------------------------------------------------

-- Tabla: actividades_pat
CREATE TABLE IF NOT EXISTS `actividades_pat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `carrera_id` int(10) UNSIGNED DEFAULT NULL,
  `grupo_id` int(10) UNSIGNED DEFAULT NULL,
  `parcial_id` int(11) NOT NULL,
  `sesion_num` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `carrera_id` (`carrera_id`),
  KEY `grupo_id` (`grupo_id`),
  KEY `parcial_id` (`parcial_id`),
  CONSTRAINT `fk_pat_carrera` FOREIGN KEY (`carrera_id`) REFERENCES `carreras` (`id_carrera`),
  CONSTRAINT `fk_pat_grupo` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id_grupo`),
  CONSTRAINT `fk_pat_parcial` FOREIGN KEY (`parcial_id`) REFERENCES `parciales` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `actividades_pat` (`carrera_id`, `grupo_id`, `parcial_id`, `sesion_num`, `nombre`, `descripcion`) VALUES
(1, NULL, 1, 1, 'Bienvenida e Inducción', 'Presentación del reglamento y servicios escolares.'),
(1, 1, 1, 2, 'Diagnóstico de Hábitos de Estudio', 'Aplicación de encuesta.');

-- Tabla: tutorias_eventos
CREATE TABLE IF NOT EXISTS `tutorias_eventos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `grupo_id` int(10) UNSIGNED NOT NULL,
  `parcial_id` int(11) NOT NULL,
  `sesion_num` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `tipo` enum('GRUPAL','INDIVIDUAL') NOT NULL,
  `actividad_id` int(11) DEFAULT NULL,
  `actividad_nombre` varchar(150) DEFAULT NULL,
  `actividad_descripcion` text DEFAULT NULL,
  `evidencia_file_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `grupo_id` (`grupo_id`),
  KEY `parcial_id` (`parcial_id`),
  KEY `actividad_id` (`actividad_id`),
  CONSTRAINT `fk_eventos_grupo` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id_grupo`),
  CONSTRAINT `fk_eventos_parcial` FOREIGN KEY (`parcial_id`) REFERENCES `parciales` (`id`),
  CONSTRAINT `fk_eventos_actividad` FOREIGN KEY (`actividad_id`) REFERENCES `actividades_pat` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: cat_areas_canalizacion
CREATE TABLE IF NOT EXISTS `cat_areas_canalizacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `cat_areas_canalizacion` (`nombre`, `activo`) VALUES
('Psicología', 1),
('Nutrición', 1),
('Servicios Médicos', 1),
('Asesoría Académica', 1);

-- Tabla: canalizacion
CREATE TABLE IF NOT EXISTS `canalizacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alumno_id` int(10) UNSIGNED NOT NULL,
  `periodo_id` int(11) NOT NULL,
  `area_id` int(11) NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `fecha_solicitud` datetime DEFAULT current_timestamp(),
  `observacion` text NOT NULL,
  `estatus` enum('PENDIENTE','ATENDIDO','CANCELADO') DEFAULT 'PENDIENTE',
  PRIMARY KEY (`id`),
  KEY `alumno_id` (`alumno_id`),
  KEY `periodo_id` (`periodo_id`),
  KEY `area_id` (`area_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `fk_canalizacion_alumno` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id_alumno`),
  CONSTRAINT `fk_canalizacion_periodo` FOREIGN KEY (`periodo_id`) REFERENCES `periodos_escolares` (`id`),
  CONSTRAINT `fk_canalizacion_area` FOREIGN KEY (`area_id`) REFERENCES `cat_areas_canalizacion` (`id`),
  CONSTRAINT `fk_canalizacion_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: alumno_riesgo_desercion
CREATE TABLE IF NOT EXISTS `alumno_riesgo_desercion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alumno_id` int(10) UNSIGNED NOT NULL,
  `periodo_id` int(11) NOT NULL,
  `posible` tinyint(1) DEFAULT 0,
  `nivel` enum('BAJO','MEDIO','ALTO') DEFAULT 'BAJO',
  `motivo` varchar(255) DEFAULT NULL,
  `fuente` varchar(50) DEFAULT NULL,
  `fecha_detectado` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `alumno_id` (`alumno_id`),
  KEY `periodo_id` (`periodo_id`),
  CONSTRAINT `fk_riesgo_alumno` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id_alumno`),
  CONSTRAINT `fk_riesgo_periodo` FOREIGN KEY (`periodo_id`) REFERENCES `periodos_escolares` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: catalogos_faltas
CREATE TABLE IF NOT EXISTS `catalogos_faltas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` enum('LEVE','GRAVE','MUY_GRAVE') NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `catalogos_faltas` (`tipo`, `descripcion`, `activo`) VALUES
('LEVE', 'Retardo', 1),
('LEVE', 'Uniforme incompleto', 1),
('GRAVE', 'Falta de respeto a compañeros', 1),
('MUY_GRAVE', 'Agresión física', 1);

-- --------------------------------------------------------
-- 4. Becas y Archivos
-- --------------------------------------------------------

-- Tabla: cat_becas
CREATE TABLE IF NOT EXISTS `cat_becas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clave` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `cat_becas` (`clave`, `nombre`, `activo`) VALUES
('INST', 'Beca Institucional', 1),
('EXCEL', 'Beca de Excelencia', 1),
('DEPOR', 'Beca Deportiva', 1);

-- Tabla: alumno_beca
CREATE TABLE IF NOT EXISTS `alumno_beca` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alumno_id` int(10) UNSIGNED NOT NULL,
  `beca_id` int(11) NOT NULL,
  `periodo_id` int(11) NOT NULL,
  `porcentaje` decimal(5,2) DEFAULT 0.00,
  `monto` decimal(10,2) DEFAULT 0.00,
  `fecha_asignacion` date DEFAULT curdate(),
  PRIMARY KEY (`id`),
  KEY `alumno_id` (`alumno_id`),
  KEY `beca_id` (`beca_id`),
  KEY `periodo_id` (`periodo_id`),
  CONSTRAINT `fk_beca_alumno` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id_alumno`),
  CONSTRAINT `fk_beca_cat` FOREIGN KEY (`beca_id`) REFERENCES `cat_becas` (`id`),
  CONSTRAINT `fk_beca_periodo` FOREIGN KEY (`periodo_id`) REFERENCES `periodos_escolares` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: files
CREATE TABLE IF NOT EXISTS `files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ruta` varchar(255) NOT NULL,
  `tipo_mime` varchar(100) DEFAULT NULL,
  `tamano` int(11) DEFAULT NULL,
  `hash` varchar(64) DEFAULT NULL,
  `fecha_subida` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;
