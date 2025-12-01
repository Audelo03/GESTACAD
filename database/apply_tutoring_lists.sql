-- Migration Script: Add Group and Individual Tutoring Lists
-- Date: 2025-11-30
-- Description: Creates tables for managing group and individual tutoring sessions

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- Table: tutorias_grupales
-- Stores group tutoring session information
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `tutorias_grupales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `grupo_id` int(10) UNSIGNED NOT NULL,
  `parcial_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `actividad_nombre` varchar(200) NOT NULL,
  `actividad_descripcion` text DEFAULT NULL,
  `evidencia_foto_id` int(11) DEFAULT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `grupo_id` (`grupo_id`),
  KEY `parcial_id` (`parcial_id`),
  KEY `evidencia_foto_id` (`evidencia_foto_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `fk_tutorias_grupales_grupo` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id_grupo`) ON DELETE CASCADE,
  CONSTRAINT `fk_tutorias_grupales_parcial` FOREIGN KEY (`parcial_id`) REFERENCES `parciales` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_tutorias_grupales_file` FOREIGN KEY (`evidencia_foto_id`) REFERENCES `files` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_tutorias_grupales_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: tutorias_grupales_asistencia
-- Tracks attendance for each student in group tutoring sessions
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `tutorias_grupales_asistencia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tutoria_grupal_id` int(11) NOT NULL,
  `alumno_id` int(10) UNSIGNED NOT NULL,
  `presente` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `tutoria_grupal_id` (`tutoria_grupal_id`),
  KEY `alumno_id` (`alumno_id`),
  CONSTRAINT `fk_asistencia_tutoria_grupal` FOREIGN KEY (`tutoria_grupal_id`) REFERENCES `tutorias_grupales` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_asistencia_alumno` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id_alumno`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: tutorias_individuales
-- Stores individual tutoring sessions
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `tutorias_individuales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alumno_id` int(10) UNSIGNED NOT NULL,
  `grupo_id` int(10) UNSIGNED NOT NULL,
  `parcial_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `motivo` text NOT NULL,
  `acciones` text NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `alumno_id` (`alumno_id`),
  KEY `grupo_id` (`grupo_id`),
  KEY `parcial_id` (`parcial_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `fk_tutorias_individuales_alumno` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id_alumno`) ON DELETE CASCADE,
  CONSTRAINT `fk_tutorias_individuales_grupo` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id_grupo`) ON DELETE CASCADE,
  CONSTRAINT `fk_tutorias_individuales_parcial` FOREIGN KEY (`parcial_id`) REFERENCES `parciales` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_tutorias_individuales_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;
