-- Migration Script: Add Temporary Attendance Tokens
-- Date: 2025-01-XX
-- Description: Creates table for temporary QR tokens for student self-attendance

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- Table: asistencia_tokens
-- Stores temporary tokens for QR-based self-attendance
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `asistencia_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(64) NOT NULL,
  `grupo_id` int(10) UNSIGNED NOT NULL,
  `tutoria_grupal_id` int(11) DEFAULT NULL,
  `fecha` date NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL COMMENT 'Usuario que generó el token (tutor/docente)',
  `expira_en` datetime NOT NULL,
  `usado` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `grupo_id` (`grupo_id`),
  KEY `tutoria_grupal_id` (`tutoria_grupal_id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `expira_en` (`expira_en`),
  CONSTRAINT `fk_asistencia_tokens_grupo` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id_grupo`) ON DELETE CASCADE,
  CONSTRAINT `fk_asistencia_tokens_tutoria` FOREIGN KEY (`tutoria_grupal_id`) REFERENCES `tutorias_grupales` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_asistencia_tokens_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;

