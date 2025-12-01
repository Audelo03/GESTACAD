-- Migration Script: Add IP blocking to attendance tokens
-- Date: 2025-01-XX
-- Description: Adds IP address tracking and blocking to prevent abuse

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- Table: asistencia_ip_bloqueos
-- Stores IP blocks to prevent multiple registrations
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `asistencia_ip_bloqueos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `bloqueado_hasta` datetime NOT NULL,
  `ultimo_registro` datetime NOT NULL,
  `token_usado` varchar(64) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `ip_address` (`ip_address`),
  KEY `bloqueado_hasta` (`bloqueado_hasta`),
  KEY `token_usado` (`token_usado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Agregar columna ip_address a asistencia_tokens si no existe
-- Nota: MySQL no soporta IF NOT EXISTS en ALTER TABLE, se debe ejecutar manualmente si la columna ya existe
SET @dbname = DATABASE();
SET @tablename = "asistencia_tokens";
SET @columnname = "ip_address";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " varchar(45) DEFAULT NULL AFTER `usado`")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Agregar columna ultimo_uso
SET @columnname = "ultimo_uso";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " datetime DEFAULT NULL AFTER `ip_address`")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Agregar índices si no existen
SET @indexname = "idx_ip_address";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (index_name = @indexname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD INDEX ", @indexname, " (`ip_address`)")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

SET @indexname = "idx_ultimo_uso";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (index_name = @indexname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD INDEX ", @indexname, " (`ultimo_uso`)")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

COMMIT;

