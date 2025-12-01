-- Script SQL para crear/modificar la tabla tutorias_individuales
-- Compatible con el método create() de TutoriaIndividual.php (líneas 17-38)
-- 
-- Campos requeridos por el método create():
-- - alumno_id (int)
-- - grupo_id (int)
-- - fecha (date)
-- - motivo (text)
-- - acciones (text)
-- - usuario_id (int)
-- 
-- NOTA: El campo parcial_id ha sido eliminado de la tutoría individual

-- ============================================
-- OPCIÓN 1: Si la tabla NO EXISTE, usa este comando:
-- ============================================

CREATE TABLE IF NOT EXISTS `tutorias_individuales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alumno_id` int(10) UNSIGNED NOT NULL,
  `grupo_id` int(10) UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `motivo` text NOT NULL,
  `acciones` text NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `alumno_id` (`alumno_id`),
  KEY `grupo_id` (`grupo_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `fk_tutorias_individuales_alumno` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id_alumno`) ON DELETE CASCADE,
  CONSTRAINT `fk_tutorias_individuales_grupo` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id_grupo`) ON DELETE CASCADE,
  CONSTRAINT `fk_tutorias_individuales_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- OPCIÓN 2: Si la tabla YA EXISTE pero le faltan columnas, usa estos comandos:
-- ============================================
-- Ejecuta solo los ALTER TABLE que necesites según tu estructura actual

-- Agregar columna alumno_id (si no existe)
ALTER TABLE `tutorias_individuales` 
ADD COLUMN `alumno_id` int(10) UNSIGNED NOT NULL AFTER `id`;

-- Agregar columna grupo_id (si no existe)
ALTER TABLE `tutorias_individuales` 
ADD COLUMN `grupo_id` int(10) UNSIGNED NOT NULL AFTER `alumno_id`;

-- Agregar columna fecha (si no existe)
ALTER TABLE `tutorias_individuales` 
ADD COLUMN `fecha` date NOT NULL AFTER `grupo_id`;

-- Agregar columna motivo (si no existe)
ALTER TABLE `tutorias_individuales` 
ADD COLUMN `motivo` text NOT NULL AFTER `fecha`;

-- Agregar columna acciones (si no existe)
ALTER TABLE `tutorias_individuales` 
ADD COLUMN `acciones` text NOT NULL AFTER `motivo`;

-- Agregar columna usuario_id (si no existe)
ALTER TABLE `tutorias_individuales` 
ADD COLUMN `usuario_id` int(10) UNSIGNED NOT NULL AFTER `acciones`;

-- Agregar columna created_at (si no existe)
ALTER TABLE `tutorias_individuales` 
ADD COLUMN `created_at` timestamp NOT NULL DEFAULT current_timestamp() AFTER `usuario_id`;

-- Agregar índices (ignora errores si ya existen)
ALTER TABLE `tutorias_individuales` ADD INDEX `idx_alumno_id` (`alumno_id`);
ALTER TABLE `tutorias_individuales` ADD INDEX `idx_grupo_id` (`grupo_id`);
ALTER TABLE `tutorias_individuales` ADD INDEX `idx_usuario_id` (`usuario_id`);

-- Agregar foreign keys (ignora errores si ya existen)
-- NOTA: Si las foreign keys ya existen, estos comandos fallarán. 
-- Elimínalas primero si necesitas recrearlas.

ALTER TABLE `tutorias_individuales` 
ADD CONSTRAINT `fk_tutorias_individuales_alumno` 
FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id_alumno`) ON DELETE CASCADE;

ALTER TABLE `tutorias_individuales` 
ADD CONSTRAINT `fk_tutorias_individuales_grupo` 
FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id_grupo`) ON DELETE CASCADE;

ALTER TABLE `tutorias_individuales` 
ADD CONSTRAINT `fk_tutorias_individuales_usuario` 
FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id_usuario`);

-- ============================================
-- OPCIÓN 3: Si la tabla YA EXISTE y tiene parcial_id, elimínalo:
-- ============================================
-- Ejecuta estos comandos para eliminar parcial_id de una tabla existente

-- Eliminar foreign key de parcial_id si existe
ALTER TABLE `tutorias_individuales` 
DROP FOREIGN KEY IF EXISTS `fk_tutorias_individuales_parcial`;

-- Eliminar índice de parcial_id si existe
ALTER TABLE `tutorias_individuales` 
DROP INDEX IF EXISTS `parcial_id`,
DROP INDEX IF EXISTS `idx_parcial_id`;

-- Eliminar columna parcial_id
ALTER TABLE `tutorias_individuales` 
DROP COLUMN IF EXISTS `parcial_id`;
