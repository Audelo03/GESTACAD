-- Script SQL para agregar la columna usuario_id a la tabla tutorias_individuales
-- Fecha: 2025-01-XX
-- 
-- Este script agrega la columna usuario_id con su índice y foreign key
-- Si la columna ya existe, los comandos fallarán de forma segura

-- ============================================
-- PASO 1: Agregar la columna usuario_id
-- ============================================
-- Si la columna ya existe, este comando fallará con un error
-- que puedes ignorar si la columna ya está presente

ALTER TABLE `tutorias_individuales` 
ADD COLUMN `usuario_id` int(10) UNSIGNED NOT NULL AFTER `acciones`;

-- Si necesitas agregar la columna en otra posición, puedes usar:
-- ALTER TABLE `tutorias_individuales` 
-- ADD COLUMN `usuario_id` int(10) UNSIGNED NOT NULL AFTER `grupo_id`;

-- ============================================
-- PASO 2: Agregar el índice para usuario_id
-- ============================================
-- Esto mejora el rendimiento de las consultas que filtran por usuario_id

ALTER TABLE `tutorias_individuales` 
ADD INDEX `idx_usuario_id` (`usuario_id`);

-- ============================================
-- PASO 3: Agregar la Foreign Key
-- ============================================
-- Esto asegura la integridad referencial con la tabla usuarios
-- Si la foreign key ya existe, este comando fallará

ALTER TABLE `tutorias_individuales` 
ADD CONSTRAINT `fk_tutorias_individuales_usuario` 
FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id_usuario`);

-- ============================================
-- NOTAS IMPORTANTES:
-- ============================================
-- 1. Si la tabla tiene registros existentes, necesitarás actualizar
--    el campo usuario_id antes de agregar la foreign key, ya que
--    NO NULL requiere un valor.
--
-- 2. Si necesitas actualizar registros existentes, usa:
--    UPDATE `tutorias_individuales` 
--    SET `usuario_id` = 1 
--    WHERE `usuario_id` IS NULL;
--    (Reemplaza 1 con el ID de usuario apropiado)
--
-- 3. Si la columna ya existe pero es NULL, primero hazla NOT NULL:
--    ALTER TABLE `tutorias_individuales` 
--    MODIFY COLUMN `usuario_id` int(10) UNSIGNED NOT NULL;

