-- Script para modificar la tabla inscripciones
-- Agregar campos estado_parcial1, estado_parcial2, estado_parcial3, estado_parcial4
-- Eliminar campos cal_parcial1, cal_parcial2, cal_parcial3, cal_parcial4, faltas_parcial1, faltas_parcial2, faltas_parcial3, faltas_parcial4

-- Paso 1: Agregar los nuevos campos estado_parcial1-4
ALTER TABLE `inscripciones`
ADD COLUMN `estado_parcial1` ENUM('CURSANDO','APROBADO','REPROBADO') DEFAULT 'CURSANDO' AFTER `estado`,
ADD COLUMN `estado_parcial2` ENUM('CURSANDO','APROBADO','REPROBADO') DEFAULT 'CURSANDO' AFTER `estado_parcial1`,
ADD COLUMN `estado_parcial3` ENUM('CURSANDO','APROBADO','REPROBADO') DEFAULT 'CURSANDO' AFTER `estado_parcial2`,
ADD COLUMN `estado_parcial4` ENUM('CURSANDO','APROBADO','REPROBADO') DEFAULT 'CURSANDO' AFTER `estado_parcial3`;

-- Paso 2: Migrar datos del campo estado a estado_parcial1 (si el estado no es BAJA)
UPDATE `inscripciones`
SET `estado_parcial1` = CASE 
    WHEN `estado` = 'APROBADO' THEN 'APROBADO'
    WHEN `estado` = 'REPROBADO' THEN 'REPROBADO'
    ELSE 'CURSANDO'
END
WHERE `estado` != 'BAJA';

-- Paso 3: Eliminar campos de calificaciones y faltas
ALTER TABLE `inscripciones`
DROP COLUMN IF EXISTS `cal_parcial1`,
DROP COLUMN IF EXISTS `cal_parcial2`,
DROP COLUMN IF EXISTS `cal_parcial3`,
DROP COLUMN IF EXISTS `cal_parcial4`,
DROP COLUMN IF EXISTS `faltas_parcial1`,
DROP COLUMN IF EXISTS `faltas_parcial2`,
DROP COLUMN IF EXISTS `faltas_parcial3`,
DROP COLUMN IF EXISTS `faltas_parcial4`;

-- Nota: El campo `estado` se mantiene para el estado general de la inscripción (CURSANDO/BAJA)
-- El campo `cal_final` se mantiene si existe, o se puede eliminar si no se necesita

