-- Script SQL para eliminar el campo parcial_id de la tabla tutorias_individuales
-- Fecha: 2025-01-XX
-- 
-- Este script elimina el campo parcial_id y todas sus referencias
-- de la tabla tutorias_individuales

-- Paso 1: Eliminar la foreign key de parcial_id si existe
ALTER TABLE `tutorias_individuales` 
DROP FOREIGN KEY IF EXISTS `fk_tutorias_individuales_parcial`;

-- Paso 2: Eliminar los índices relacionados con parcial_id si existen
ALTER TABLE `tutorias_individuales` 
DROP INDEX IF EXISTS `parcial_id`,
DROP INDEX IF EXISTS `idx_parcial_id`;

-- Paso 3: Eliminar la columna parcial_id
ALTER TABLE `tutorias_individuales` 
DROP COLUMN IF EXISTS `parcial_id`;

-- NOTA: Si tu versión de MySQL no soporta "IF EXISTS" en DROP COLUMN,
-- usa este comando alternativo (puede dar error si la columna no existe):
-- ALTER TABLE `tutorias_individuales` DROP COLUMN `parcial_id`;

