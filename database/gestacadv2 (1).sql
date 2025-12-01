-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-12-2025 a las 05:55:03
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `gestacadv2`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividades_pat`
--

CREATE TABLE `actividades_pat` (
  `id` int(11) NOT NULL,
  `carrera_id` int(10) UNSIGNED DEFAULT NULL,
  `grupo_id` int(10) UNSIGNED DEFAULT NULL,
  `parcial_id` int(11) NOT NULL,
  `sesion_num` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `actividades_pat`
--

INSERT INTO `actividades_pat` (`id`, `carrera_id`, `grupo_id`, `parcial_id`, `sesion_num`, `nombre`, `descripcion`) VALUES
(1, 1, NULL, 1, 1, 'Bienvenida e Inducción', 'Presentación del reglamento y servicios escolares.'),
(2, 1, NULL, 1, 2, 'Diagnóstico de Hábitos de Estudio', 'Aplicación de encuesta para identificar hábitos de estudio.'),
(3, 1, NULL, 1, 3, 'Técnicas de Estudio', 'Taller sobre técnicas efectivas de estudio.'),
(4, 1, 1, 1, 1, 'Dinámica de Integración', 'Actividad grupal para conocerse mejor.'),
(5, 1, 2, 1, 1, 'Orientación Vocacional', 'Sesión sobre orientación vocacional y profesional.'),
(6, 2, NULL, 1, 1, 'Inducción a la Carrera', 'Presentación de la carrera de Ingeniería Industrial.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumnos`
--

CREATE TABLE `alumnos` (
  `id_alumno` int(10) UNSIGNED NOT NULL,
  `matricula` varchar(20) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `apellido_paterno` varchar(60) NOT NULL,
  `apellido_materno` varchar(60) DEFAULT NULL,
  `estatus` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=Activo, 0=Inactivo, 2=Egresado, 3=Baja',
  `fecha_creacion` date NOT NULL DEFAULT current_timestamp(),
  `fecha_movimiento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `usuarios_id_usuario_movimiento` int(10) UNSIGNED DEFAULT NULL,
  `carreras_id_carrera` int(10) UNSIGNED NOT NULL,
  `grupos_id_grupo` int(10) UNSIGNED DEFAULT NULL,
  `genero` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `alumnos`
--

INSERT INTO `alumnos` (`id_alumno`, `matricula`, `nombre`, `apellido_paterno`, `apellido_materno`, `estatus`, `fecha_creacion`, `fecha_movimiento`, `usuarios_id_usuario_movimiento`, `carreras_id_carrera`, `grupos_id_grupo`, `genero`) VALUES
(1, 'ISC2025001', 'Alejandro', 'González', 'Martínez', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 1, 'M'),
(2, 'ISC2025002', 'Ana Sofía', 'Hernández', 'López', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 1, 'F'),
(3, 'ISC2025003', 'Carlos', 'Ramírez', 'García', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 1, 'M'),
(4, 'ISC2025004', 'Daniela', 'Torres', 'Sánchez', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 1, 'F'),
(5, 'ISC2025005', 'Eduardo', 'Morales', 'Fernández', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 1, 'M'),
(6, 'ISC2025006', 'Fernanda', 'Jiménez', 'Ruiz', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 1, 'F'),
(7, 'ISC2025007', 'Gabriel', 'Díaz', 'Vargas', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 1, 'M'),
(8, 'ISC2025008', 'Isabella', 'Castro', 'Mendoza', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 1, 'F'),
(9, 'ISC2025009', 'Javier', 'Ortega', 'Ramos', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 1, 'M'),
(10, 'ISC2025010', 'Karina', 'Pérez', 'Gómez', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 1, 'F'),
(11, 'ISC2025011', 'Luis', 'Rivera', 'Cruz', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 1, 'M'),
(12, 'ISC2025012', 'Mariana', 'Flores', 'Ortiz', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 1, 'F'),
(13, 'ISC2025013', 'Nicolás', 'Soto', 'Gutiérrez', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 1, 'M'),
(14, 'ISC2025014', 'Olivia', 'Chávez', 'Moreno', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 1, 'F'),
(15, 'ISC2025015', 'Pablo', 'Reyes', 'Delgado', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 1, 'M'),
(16, 'ISC2025016', 'Valentina', 'Mendoza', 'Silva', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 1, 'F'),
(17, 'ISC2025017', 'Ricardo', 'Vega', 'Herrera', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 1, 'M'),
(18, 'ISC2025018', 'Samantha', 'Guerrero', 'Medina', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 1, 'F'),
(19, 'ISC2025019', 'Tomás', 'Campos', 'Aguilar', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 1, 'M'),
(20, 'ISC2025020', 'Ximena', 'Rojas', 'Vázquez', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 1, 'F'),
(21, 'ISC2025021', 'Yahir', 'Salazar', 'Espinoza', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 1, 'M'),
(22, 'ISC2025022', 'Zoe', 'Contreras', 'Navarro', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 1, 'F'),
(23, 'ISC2025023', 'Adrián', 'Luna', 'Molina', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 1, 'M'),
(24, 'ISC2025024', 'Brenda', 'Álvarez', 'Cortés', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 1, 'F'),
(25, 'ISC2025025', 'César', 'Méndez', 'Lara', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 1, 'M'),
(26, 'ISC2025026', 'Diana', 'Fuentes', 'Peña', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 1, 'F'),
(27, 'ISC2025027', 'Emilio', 'Ramos', 'Sosa', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 1, 'M'),
(28, 'ISC2025028', 'Fabiola', 'Villanueva', 'Tapia', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 1, 'F'),
(29, 'ISC2025029', 'Gustavo', 'Zamora', 'León', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 1, 'M'),
(30, 'ISC2025030', 'Hilda', 'Barrera', 'Miranda', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 1, 'F'),
(31, 'ISC2023001', 'Iván', 'Cervantes', 'Núñez', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 2, 'M'),
(32, 'ISC2023002', 'Julia', 'Espinosa', 'Valdez', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 2, 'F'),
(33, 'ISC2023003', 'Kevin', 'Galván', 'Acosta', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 2, 'M'),
(34, 'ISC2023004', 'Liliana', 'Ibarra', 'Benítez', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 2, 'F'),
(35, 'ISC2023005', 'Manuel', 'Juárez', 'Cárdenas', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 2, 'M'),
(36, 'ISC2023006', 'Natalia', 'Lozano', 'Durán', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 2, 'F'),
(37, 'ISC2023007', 'Óscar', 'Montes', 'Escobar', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 2, 'M'),
(38, 'ISC2023008', 'Patricia', 'Nava', 'Fajardo', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 2, 'F'),
(39, 'ISC2023009', 'Quetzal', 'Ochoa', 'Guzmán', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 2, 'M'),
(40, 'ISC2023010', 'Rosa', 'Paredes', 'Hidalgo', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 2, 'F'),
(41, 'ISC2023011', 'Sergio', 'Quiroz', 'Ibarra', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 2, 'M'),
(42, 'ISC2023012', 'Teresa', 'Ríos', 'Jasso', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 2, 'F'),
(43, 'ISC2023013', 'Ulises', 'Sosa', 'Kuri', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 2, 'M'),
(44, 'ISC2023014', 'Verónica', 'Téllez', 'Lara', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 2, 'F'),
(45, 'ISC2023015', 'Wilfrido', 'Uribe', 'Márquez', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 2, 'M'),
(46, 'ISC2023016', 'Yolanda', 'Valdés', 'Nieto', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 2, 'F'),
(47, 'ISC2023017', 'Zacarías', 'Wong', 'Orozco', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 2, 'M'),
(48, 'ISC2023018', 'Alicia', 'Xicoténcatl', 'Pacheco', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 2, 'F'),
(49, 'ISC2023019', 'Benito', 'Yáñez', 'Quiroz', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 2, 'M'),
(50, 'ISC2023020', 'Cecilia', 'Zaragoza', 'Reyes', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 2, 'F'),
(51, 'ISC2023021', 'Diego', 'Aguilar', 'Sánchez', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 2, 'M'),
(52, 'ISC2023022', 'Elena', 'Benítez', 'Torres', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 2, 'F'),
(53, 'ISC2023023', 'Felipe', 'Cárdenas', 'Uribe', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 2, 'M'),
(54, 'ISC2023024', 'Gloria', 'Durán', 'Vega', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 2, 'F'),
(55, 'ISC2023025', 'Héctor', 'Escobar', 'Wong', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 2, 'M'),
(56, 'ISC2021001', 'Ignacio', 'Fajardo', 'Xicoténcatl', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 3, 'M'),
(57, 'ISC2021002', 'Jazmín', 'Guzmán', 'Yáñez', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 3, 'F'),
(58, 'ISC2021003', 'Karla', 'Hidalgo', 'Zaragoza', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 3, 'F'),
(59, 'ISC2021004', 'Leonardo', 'Ibarra', 'Aguilar', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 3, 'M'),
(60, 'ISC2021005', 'Mónica', 'Jasso', 'Benítez', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 3, 'F'),
(61, 'ISC2021006', 'Néstor', 'Kuri', 'Cárdenas', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 3, 'M'),
(62, 'ISC2021007', 'Ofelia', 'Lara', 'Durán', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 3, 'F'),
(63, 'ISC2021008', 'Pedro', 'Márquez', 'Escobar', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 3, 'M'),
(64, 'ISC2021009', 'Quetzalli', 'Nieto', 'Fajardo', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 3, 'F'),
(65, 'ISC2021010', 'Raúl', 'Orozco', 'Guzmán', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 3, 'M'),
(66, 'ISC2021011', 'Silvia', 'Pacheco', 'Hidalgo', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 3, 'F'),
(67, 'ISC2021012', 'Tadeo', 'Quiroz', 'Ibarra', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 3, 'M'),
(68, 'ISC2021013', 'Úrsula', 'Reyes', 'Jasso', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 3, 'F'),
(69, 'ISC2021014', 'Vicente', 'Sánchez', 'Kuri', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 3, 'M'),
(70, 'ISC2021015', 'Wendy', 'Torres', 'Lara', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 3, 'F'),
(71, 'ISC2021016', 'Xavier', 'Uribe', 'Márquez', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 3, 'M'),
(72, 'ISC2021017', 'Yadira', 'Vega', 'Nieto', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 3, 'F'),
(73, 'ISC2021018', 'Zacarías', 'Wong', 'Orozco', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 3, 'M'),
(74, 'ISC2021019', 'Adriana', 'Xicoténcatl', 'Pacheco', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 3, 'F'),
(75, 'ISC2021020', 'Bruno', 'Yáñez', 'Quiroz', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 3, 'M'),
(76, 'ISC2019001', 'Camila', 'Zaragoza', 'Reyes', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 4, 'F'),
(77, 'ISC2019002', 'Dante', 'Aguilar', 'Sánchez', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 4, 'M'),
(78, 'ISC2019003', 'Esmeralda', 'Benítez', 'Torres', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 4, 'F'),
(79, 'ISC2019004', 'Federico', 'Cárdenas', 'Uribe', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 4, 'M'),
(80, 'ISC2019005', 'Gabriela', 'Durán', 'Vega', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 4, 'F'),
(81, 'ISC2019006', 'Hugo', 'Escobar', 'Wong', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 4, 'M'),
(82, 'ISC2019007', 'Iris', 'Fajardo', 'Xicoténcatl', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 4, 'F'),
(83, 'ISC2019008', 'Joaquín', 'Guzmán', 'Yáñez', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 4, 'M'),
(84, 'ISC2019009', 'Kenia', 'Hidalgo', 'Zaragoza', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 4, 'F'),
(85, 'ISC2019010', 'Lorenzo', 'Ibarra', 'Aguilar', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 4, 'M'),
(86, 'ISC2019011', 'Mireya', 'Jasso', 'Benítez', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 4, 'F'),
(87, 'ISC2019012', 'Nahum', 'Kuri', 'Cárdenas', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 4, 'M'),
(88, 'ISC2019013', 'Odalys', 'Lara', 'Durán', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 4, 'F'),
(89, 'ISC2019014', 'Pascual', 'Márquez', 'Escobar', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 4, 'M'),
(90, 'ISC2019015', 'Rebeca', 'Nieto', 'Fajardo', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 4, 'F'),
(91, 'ISC2023S001', 'Salvador', 'Orozco', 'Guzmán', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 5, 'M'),
(92, 'ISC2023S002', 'Tania', 'Pacheco', 'Hidalgo', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 5, 'F'),
(93, 'ISC2023S003', 'Ubaldo', 'Quiroz', 'Ibarra', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 5, 'M'),
(94, 'ISC2023S004', 'Vanesa', 'Reyes', 'Jasso', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 5, 'F'),
(95, 'ISC2023S005', 'Wenceslao', 'Sánchez', 'Kuri', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 5, 'M'),
(96, 'ISC2023S006', 'Xóchitl', 'Torres', 'Lara', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 5, 'F'),
(97, 'ISC2023S007', 'Yair', 'Uribe', 'Márquez', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 5, 'M'),
(98, 'ISC2023S008', 'Zulema', 'Vega', 'Nieto', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 5, 'F'),
(99, 'ISC2023S009', 'Abel', 'Wong', 'Orozco', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 5, 'M'),
(100, 'ISC2023S010', 'Beatriz', 'Xicoténcatl', 'Pacheco', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 5, 'F'),
(101, 'ISC2023S011', 'Ciro', 'Yáñez', 'Quiroz', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 5, 'M'),
(102, 'ISC2023S012', 'Dolores', 'Zaragoza', 'Reyes', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 5, 'F'),
(103, 'ISC2023S013', 'Efraín', 'Aguilar', 'Sánchez', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 5, 'M'),
(104, 'ISC2023S014', 'Flor', 'Benítez', 'Torres', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 5, 'F'),
(105, 'ISC2023S015', 'Gerardo', 'Cárdenas', 'Uribe', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 5, 'M'),
(106, 'ISC2023S016', 'Herminia', 'Durán', 'Vega', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 5, 'F'),
(107, 'ISC2023S017', 'Isidro', 'Escobar', 'Wong', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 5, 'M'),
(108, 'ISC2023S018', 'Josefina', 'Fajardo', 'Xicoténcatl', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 5, 'F'),
(109, 'ISC2023S019', 'Kike', 'Guzmán', 'Yáñez', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 5, 'M'),
(110, 'ISC2023S020', 'Leticia', 'Hidalgo', 'Zaragoza', 1, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 5, 'F'),
(111, 'ISC20250031', 'Roberto', 'Inactivo', 'Test', 0, '2025-09-01', '2025-09-15 06:00:00', 3, 1, 1, 'M'),
(112, 'ISC20250032', 'Laura', 'Baja', 'Test', 3, '2025-09-01', '2025-09-20 06:00:00', 3, 1, 1, 'F'),
(113, 'ISC20230026', 'Pedro', 'Egresado', 'Test', 2, '2025-09-01', '2025-09-01 06:00:00', 3, 1, 2, 'M');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumno_beca`
--

CREATE TABLE `alumno_beca` (
  `id` int(11) NOT NULL,
  `alumno_id` int(10) UNSIGNED NOT NULL,
  `beca_id` int(11) NOT NULL,
  `periodo_id` int(11) NOT NULL,
  `porcentaje` decimal(5,2) DEFAULT 0.00,
  `monto` decimal(10,2) DEFAULT 0.00,
  `fecha_asignacion` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alumno_beca`
--

INSERT INTO `alumno_beca` (`id`, `alumno_id`, `beca_id`, `periodo_id`, `porcentaje`, `monto`, `fecha_asignacion`) VALUES
(1, 1, 2, 3, 50.00, 5000.00, '2025-09-01'),
(2, 2, 4, 3, 30.00, 3000.00, '2025-09-01'),
(3, 31, 1, 3, 25.00, 2500.00, '2025-09-01'),
(4, 32, 3, 3, 40.00, 4000.00, '2025-09-01'),
(5, 51, 2, 3, 50.00, 5000.00, '2025-09-01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumno_riesgo_desercion`
--

CREATE TABLE `alumno_riesgo_desercion` (
  `id` int(11) NOT NULL,
  `alumno_id` int(10) UNSIGNED NOT NULL,
  `periodo_id` int(11) NOT NULL,
  `posible` tinyint(1) DEFAULT 0,
  `nivel` enum('BAJO','MEDIO','ALTO') DEFAULT 'BAJO',
  `motivo` varchar(255) DEFAULT NULL,
  `fuente` varchar(50) DEFAULT NULL,
  `fecha_detectado` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alumno_riesgo_desercion`
--

INSERT INTO `alumno_riesgo_desercion` (`id`, `alumno_id`, `periodo_id`, `posible`, `nivel`, `motivo`, `fuente`, `fecha_detectado`) VALUES
(1, 2, 3, 1, 'MEDIO', 'Faltas frecuentes y bajo rendimiento académico', 'Sistema', '2025-11-10 10:00:00'),
(2, 33, 3, 1, 'ALTO', 'Múltiples faltas consecutivas y sin comunicación', 'Tutor', '2025-11-20 14:00:00'),
(3, 51, 3, 1, 'MEDIO', 'Problemas familiares que afectan el desempeño', 'Tutor', '2025-11-05 11:00:00'),
(4, 66, 3, 1, 'BAJO', 'Ansiedad académica detectada', 'Tutor', '2025-11-22 09:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignaturas`
--

CREATE TABLE `asignaturas` (
  `id` int(11) NOT NULL,
  `clave` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `creditos` int(11) NOT NULL,
  `horas_semana` int(11) NOT NULL,
  `area` varchar(50) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asignaturas`
--

INSERT INTO `asignaturas` (`id`, `clave`, `nombre`, `creditos`, `horas_semana`, `area`, `activo`) VALUES
(1, 'ISC101', 'Fundamentos de Programación', 6, 6, 'Programación', 1),
(2, 'ISC102', 'Estructuras de Datos', 6, 6, 'Programación', 1),
(3, 'ISC103', 'Bases de Datos', 6, 6, 'Bases de Datos', 1),
(4, 'ISC104', 'Ingeniería de Software', 5, 5, 'Ingeniería', 1),
(5, 'ISC105', 'Redes de Computadoras', 5, 5, 'Redes', 1),
(6, 'ISC201', 'Programación Orientada a Objetos', 6, 6, 'Programación', 1),
(7, 'ISC202', 'Desarrollo Web', 6, 6, 'Programación', 1),
(8, 'ISC203', 'Sistemas Operativos', 5, 5, 'Sistemas', 1),
(9, 'IIN101', 'Introducción a la Ingeniería Industrial', 4, 4, 'Fundamentos', 1),
(10, 'IIN102', 'Estadística Aplicada', 5, 5, 'Matemáticas', 1),
(11, 'IIN103', 'Procesos de Manufactura', 6, 6, 'Manufactura', 1),
(12, 'LCP101', 'Contabilidad Básica', 6, 6, 'Contabilidad', 1),
(13, 'LCP102', 'Matemáticas Financieras', 5, 5, 'Matemáticas', 1),
(14, 'LCP103', 'Derecho Fiscal', 5, 5, 'Derecho', 1),
(15, 'MAT101', 'Cálculo Diferencial', 5, 5, 'Matemáticas', 1),
(16, 'MAT102', 'Cálculo Integral', 5, 5, 'Matemáticas', 1),
(17, 'FIS101', 'Física I', 5, 5, 'Física', 1),
(18, 'ADM101', 'Administración I', 4, 4, 'Administración', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencia_ip_bloqueos`
--

CREATE TABLE `asistencia_ip_bloqueos` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `bloqueado_hasta` datetime NOT NULL,
  `ultimo_registro` datetime NOT NULL,
  `token_usado` varchar(64) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asistencia_ip_bloqueos`
--

INSERT INTO `asistencia_ip_bloqueos` (`id`, `ip_address`, `bloqueado_hasta`, `ultimo_registro`, `token_usado`, `created_at`) VALUES
(1, '::1', '2025-12-01 05:44:07', '2025-12-01 05:43:07', '868a50890c07ec680e4e803f5d21948d1b1e2aa796c111a9eb937a627597abdc', '2025-12-01 04:43:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencia_tokens`
--

CREATE TABLE `asistencia_tokens` (
  `id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `grupo_id` int(10) UNSIGNED NOT NULL,
  `tutoria_grupal_id` int(11) DEFAULT NULL,
  `fecha` date NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL COMMENT 'Usuario que generó el token (tutor/docente)',
  `expira_en` datetime NOT NULL,
  `usado` tinyint(1) NOT NULL DEFAULT 0,
  `ip_address` varchar(45) DEFAULT NULL,
  `ultimo_uso` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asistencia_tokens`
--

INSERT INTO `asistencia_tokens` (`id`, `token`, `grupo_id`, `tutoria_grupal_id`, `fecha`, `usuario_id`, `expira_en`, `usado`, `ip_address`, `ultimo_uso`, `created_at`) VALUES
(1, '0f1bac6ae44fd19efd1f2d583b5e947f1da08a4afc9179eadda715758076ccea', 1, NULL, '2025-12-01', 6, '2025-12-01 05:37:21', 0, NULL, NULL, '2025-12-01 04:32:21'),
(2, '027a67756026c49124481b568fa5652bb400b859ab7eee45d3d67e3dc7376d7f', 1, NULL, '2025-12-01', 6, '2025-12-01 05:37:54', 0, NULL, NULL, '2025-12-01 04:32:54'),
(3, 'cf98fddad0c1d5ead201ce95df466df2f03ef76cc7cb808292837b79d54d9796', 1, NULL, '2025-12-01', 6, '2025-12-01 05:38:20', 0, NULL, NULL, '2025-12-01 04:33:20'),
(4, '9b00e9f5e09367059a082bd0a5eda96bda4f6ee2a1adcfcf9530b1615356e86b', 1, NULL, '2025-12-01', 6, '2025-12-01 05:39:23', 0, NULL, NULL, '2025-12-01 04:34:23'),
(5, '82708048d4c8d974d819eea2381f16ac1a42005de8efc00dcd7d135c85b0f26f', 1, NULL, '2025-12-01', 6, '2025-12-01 05:39:40', 0, NULL, NULL, '2025-12-01 04:34:40'),
(6, 'deacd6e7577de3a1aae1ca7759e4665ac7902e9ce65d1b12e1efc4ac8902f2ff', 1, NULL, '2025-12-01', 6, '2025-12-01 05:41:00', 0, NULL, NULL, '2025-12-01 04:36:00'),
(7, '01fb5a5d1c617d38847a3a603fec3bc0d1cb70581e271a9f32e49911ee1e9d30', 1, NULL, '2025-12-01', 6, '2025-12-01 05:41:05', 0, NULL, NULL, '2025-12-01 04:36:05'),
(8, '69a2e76851f0247b3755fc452887944b0a8b4435fc91358d7e47227ab3f9fe38', 1, NULL, '2025-12-01', 6, '2025-12-01 05:41:51', 0, NULL, NULL, '2025-12-01 04:36:51'),
(9, '6d0740af671fc1a0bdbfe27c96e729a2575cf6e62ee08b6e4d2d296313d8838d', 1, NULL, '2025-12-01', 6, '2025-12-01 05:43:17', 0, NULL, NULL, '2025-12-01 04:38:17'),
(10, 'f7ccb688cd57e63df963de003c3eb6cf6e73a654a2cc267d1f752ea91b95d635', 1, NULL, '2025-12-01', 6, '2025-12-01 05:43:40', 0, NULL, NULL, '2025-12-01 04:38:40'),
(11, '307a89d5c92371eab426c69845c6fe0bf6ef47527aee9ff0ff0a3e312e487d44', 1, NULL, '2025-12-01', 6, '2025-12-01 05:44:06', 0, NULL, NULL, '2025-12-01 04:39:06'),
(12, 'ae8bb8b17e9ad3ee4c4c01758850db7b569cc7955b7abc50dc2250705cd149ae', 1, NULL, '2025-12-01', 6, '2025-12-01 05:45:18', 0, NULL, NULL, '2025-12-01 04:40:18'),
(13, '868a50890c07ec680e4e803f5d21948d1b1e2aa796c111a9eb937a627597abdc', 1, NULL, '2025-12-01', 6, '2025-12-01 05:45:34', 0, NULL, NULL, '2025-12-01 04:40:34'),
(14, '006b73c028e5ab8e52dd5b4be18d1f6b394f40e53432ded88eebd4b0becaf5bb', 1, NULL, '2025-12-01', 6, '2025-12-01 05:55:34', 0, NULL, NULL, '2025-12-01 04:50:34');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `canalizacion`
--

CREATE TABLE `canalizacion` (
  `id` int(11) NOT NULL,
  `alumno_id` int(10) UNSIGNED NOT NULL,
  `periodo_id` int(11) NOT NULL,
  `area_id` int(11) NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `fecha_solicitud` datetime DEFAULT current_timestamp(),
  `observacion` text NOT NULL,
  `estatus` enum('PENDIENTE','ATENDIDO','CANCELADO') DEFAULT 'PENDIENTE'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `canalizacion`
--

INSERT INTO `canalizacion` (`id`, `alumno_id`, `periodo_id`, `area_id`, `usuario_id`, `fecha_solicitud`, `observacion`, `estatus`) VALUES
(1, 51, 3, 1, 8, '2025-11-05 11:00:00', 'El alumno requiere apoyo psicológico por problemas familiares que afectan su desempeño académico.', 'PENDIENTE'),
(2, 66, 3, 1, 9, '2025-11-22 09:00:00', 'Canalización por ansiedad académica y estrés.', 'ATENDIDO'),
(3, 2, 3, 4, 6, '2025-11-10 10:00:00', 'El alumno requiere asesoría académica para mejorar su rendimiento.', 'PENDIENTE');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carreras`
--

CREATE TABLE `carreras` (
  `id_carrera` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `fecha_creacion` date NOT NULL DEFAULT curdate(),
  `fecha_movimiento` date NOT NULL DEFAULT curdate(),
  `usuarios_id_usuario_movimiento` int(10) UNSIGNED DEFAULT NULL,
  `usuarios_id_usuario_coordinador` int(10) UNSIGNED DEFAULT NULL,
  `estatus` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=Activo, 0=Inactivo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `carreras`
--

INSERT INTO `carreras` (`id_carrera`, `nombre`, `fecha_creacion`, `fecha_movimiento`, `usuarios_id_usuario_movimiento`, `usuarios_id_usuario_coordinador`, `estatus`) VALUES
(1, 'Ingeniería en Sistemas Computacionales', '2025-01-01', '2025-01-01', 1, 3, 1),
(2, 'Ingeniería Industrial', '2025-01-01', '2025-01-01', 1, 3, 1),
(3, 'Contaduría Pública', '2025-01-01', '2025-01-01', 1, 4, 1),
(4, 'Administración de Empresas', '2025-01-01', '2025-01-01', 1, 4, 1),
(5, 'Ingeniería en Mecatrónica', '2025-01-01', '2025-01-01', 1, 3, 1),
(6, 'Psicología', '2025-01-01', '2025-01-01', 1, 5, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `catalogos_faltas`
--

CREATE TABLE `catalogos_faltas` (
  `id` int(11) NOT NULL,
  `tipo` enum('LEVE','GRAVE','MUY_GRAVE') NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `catalogos_faltas`
--

INSERT INTO `catalogos_faltas` (`id`, `tipo`, `descripcion`, `activo`) VALUES
(1, 'LEVE', 'Retardo', 1),
(2, 'LEVE', 'Uniforme incompleto', 1),
(3, 'LEVE', 'Falta de material', 1),
(4, 'GRAVE', 'Falta de respeto a compañeros', 1),
(5, 'GRAVE', 'Falta de respeto a docentes', 1),
(6, 'GRAVE', 'Uso de dispositivos no permitidos', 1),
(7, 'MUY_GRAVE', 'Agresión física', 1),
(8, 'MUY_GRAVE', 'Agresión verbal', 1),
(9, 'MUY_GRAVE', 'Falsificación de documentos', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cat_areas_canalizacion`
--

CREATE TABLE `cat_areas_canalizacion` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cat_areas_canalizacion`
--

INSERT INTO `cat_areas_canalizacion` (`id`, `nombre`, `activo`) VALUES
(1, 'Psicología', 1),
(2, 'Nutrición', 1),
(3, 'Servicios Médicos', 1),
(4, 'Asesoría Académica', 1),
(5, 'Orientación Vocacional', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cat_becas`
--

CREATE TABLE `cat_becas` (
  `id` int(11) NOT NULL,
  `clave` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cat_becas`
--

INSERT INTO `cat_becas` (`id`, `clave`, `nombre`, `activo`) VALUES
(1, 'INST', 'Beca Institucional', 1),
(2, 'EXCEL', 'Beca de Excelencia', 1),
(3, 'DEPOR', 'Beca Deportiva', 1),
(4, 'SOCIAL', 'Beca Socioeconómica', 1),
(5, 'CULT', 'Beca Cultural', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clases`
--

CREATE TABLE `clases` (
  `id` int(11) NOT NULL,
  `asignatura_id` int(11) NOT NULL,
  `periodo_id` int(11) NOT NULL,
  `docente_usuario_id` int(10) UNSIGNED NOT NULL,
  `seccion` varchar(10) NOT NULL,
  `modalidad_id` int(10) UNSIGNED DEFAULT NULL,
  `cupo` int(11) DEFAULT 30,
  `grupo_referencia` int(10) UNSIGNED DEFAULT NULL,
  `aula` varchar(20) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clases`
--

INSERT INTO `clases` (`id`, `asignatura_id`, `periodo_id`, `docente_usuario_id`, `seccion`, `modalidad_id`, `cupo`, `grupo_referencia`, `aula`, `activo`) VALUES
(1, 1, 3, 6, 'A', 1, 35, 1, 'LAB-101', 1),
(2, 15, 3, 6, 'A', 1, 35, 1, 'A-201', 1),
(3, 17, 3, 6, 'A', 1, 35, 1, 'LAB-FIS', 1),
(4, 2, 3, 7, 'A', 1, 30, 2, 'LAB-102', 1),
(5, 6, 3, 7, 'A', 1, 30, 2, 'LAB-103', 1),
(6, 3, 3, 7, 'A', 1, 30, 2, 'LAB-104', 1),
(7, 4, 3, 8, 'A', 1, 25, 3, 'A-301', 1),
(8, 5, 3, 8, 'A', 1, 25, 3, 'LAB-RED', 1),
(9, 7, 3, 8, 'A', 1, 25, 3, 'LAB-105', 1),
(10, 4, 3, 9, 'A', 1, 20, 4, 'A-401', 1),
(11, 8, 3, 9, 'A', 1, 20, 4, 'LAB-106', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `divisiones`
--

CREATE TABLE `divisiones` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `divisiones`
--

INSERT INTO `divisiones` (`id`, `nombre`, `activo`) VALUES
(1, 'Ingeniería y Tecnología', 1),
(2, 'Ciencias Económico Administrativas', 1),
(3, 'Ciencias de la Salud', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `files`
--

CREATE TABLE `files` (
  `id` int(11) NOT NULL,
  `ruta` varchar(255) NOT NULL,
  `tipo_mime` varchar(100) DEFAULT NULL,
  `tamano` int(11) DEFAULT NULL,
  `hash` varchar(64) DEFAULT NULL,
  `fecha_subida` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `files`
--

INSERT INTO `files` (`id`, `ruta`, `tipo_mime`, `tamano`, `hash`, `fecha_subida`) VALUES
(1, 'uploads/tutorias/tutoria_692d05b4c35186.87692550.png', 'image/png', 266983, '7e028090195c090e4a438b301c7d3973816bcd9969f30c240a151ea5512cae9c', '2025-11-30 21:04:20'),
(2, 'uploads/tutorias/tutoria_692d13a32ec0d8.16941074.png', 'image/png', 266983, '7e028090195c090e4a438b301c7d3973816bcd9969f30c240a151ea5512cae9c', '2025-11-30 22:03:47'),
(3, 'uploads/tutorias/tutoria_692d13ab6b4979.62043203.png', 'image/png', 266983, '7e028090195c090e4a438b301c7d3973816bcd9969f30c240a151ea5512cae9c', '2025-11-30 22:03:55'),
(4, 'uploads/tutorias/tutoria_692d1426dd5ff8.10005273.png', 'image/png', 266983, '7e028090195c090e4a438b301c7d3973816bcd9969f30c240a151ea5512cae9c', '2025-11-30 22:05:58'),
(5, 'uploads/tutorias/tutoria_692d1428694b06.70228746.png', 'image/png', 266983, '7e028090195c090e4a438b301c7d3973816bcd9969f30c240a151ea5512cae9c', '2025-11-30 22:06:00'),
(6, 'uploads/tutorias/tutoria_692d1428ddd9f8.55762582.png', 'image/png', 266983, '7e028090195c090e4a438b301c7d3973816bcd9969f30c240a151ea5512cae9c', '2025-11-30 22:06:00'),
(7, 'uploads/tutorias/tutoria_692d142942b730.12240339.png', 'image/png', 266983, '7e028090195c090e4a438b301c7d3973816bcd9969f30c240a151ea5512cae9c', '2025-11-30 22:06:01'),
(8, 'uploads/tutorias/tutoria_692d142967b483.32392499.png', 'image/png', 266983, '7e028090195c090e4a438b301c7d3973816bcd9969f30c240a151ea5512cae9c', '2025-11-30 22:06:01'),
(9, 'uploads/tutorias/tutoria_692d142990d765.58382583.png', 'image/png', 266983, '7e028090195c090e4a438b301c7d3973816bcd9969f30c240a151ea5512cae9c', '2025-11-30 22:06:01'),
(10, 'uploads/tutorias/tutoria_692d1429be0a76.44763683.png', 'image/png', 266983, '7e028090195c090e4a438b301c7d3973816bcd9969f30c240a151ea5512cae9c', '2025-11-30 22:06:01'),
(11, 'uploads/tutorias/tutoria_692d1429e543a0.02947182.png', 'image/png', 266983, '7e028090195c090e4a438b301c7d3973816bcd9969f30c240a151ea5512cae9c', '2025-11-30 22:06:01'),
(12, 'uploads/tutorias/tutoria_692d14386ac602.86942222.png', 'image/png', 266983, '7e028090195c090e4a438b301c7d3973816bcd9969f30c240a151ea5512cae9c', '2025-11-30 22:06:16'),
(13, 'uploads/tutorias/tutoria_692d1439046046.83267339.png', 'image/png', 266983, '7e028090195c090e4a438b301c7d3973816bcd9969f30c240a151ea5512cae9c', '2025-11-30 22:06:17'),
(14, 'uploads/tutorias/tutoria_692d14393201b2.37158723.png', 'image/png', 266983, '7e028090195c090e4a438b301c7d3973816bcd9969f30c240a151ea5512cae9c', '2025-11-30 22:06:17'),
(15, 'uploads/tutorias/tutoria_692d14395427c4.67502300.png', 'image/png', 266983, '7e028090195c090e4a438b301c7d3973816bcd9969f30c240a151ea5512cae9c', '2025-11-30 22:06:17'),
(16, 'uploads/tutorias/tutoria_692d14397a59c5.08644756.png', 'image/png', 266983, '7e028090195c090e4a438b301c7d3973816bcd9969f30c240a151ea5512cae9c', '2025-11-30 22:06:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos`
--

CREATE TABLE `grupos` (
  `id_grupo` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `estatus` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=Activo, 0=Inactivo',
  `fecha_creacion` date NOT NULL DEFAULT curdate(),
  `fecha_movimiento` date NOT NULL DEFAULT curdate(),
  `usuarios_id_usuario_movimiento` int(10) UNSIGNED DEFAULT NULL,
  `usuarios_id_usuario_tutor` int(10) UNSIGNED DEFAULT NULL,
  `carreras_id_carrera` int(10) UNSIGNED NOT NULL,
  `modalidades_id_modalidad` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `grupos`
--

INSERT INTO `grupos` (`id_grupo`, `nombre`, `estatus`, `fecha_creacion`, `fecha_movimiento`, `usuarios_id_usuario_movimiento`, `usuarios_id_usuario_tutor`, `carreras_id_carrera`, `modalidades_id_modalidad`) VALUES
(1, 'ISC-1A', 1, '2025-09-01', '2025-09-01', 3, 6, 1, 1),
(2, 'ISC-3A', 1, '2025-09-01', '2025-09-01', 3, 7, 1, 1),
(3, 'ISC-5A', 1, '2025-09-01', '2025-09-01', 3, 8, 1, 1),
(4, 'ISC-7A', 1, '2025-09-01', '2025-09-01', 3, 9, 1, 1),
(5, 'ISC-3B-SAB', 1, '2025-09-01', '2025-09-01', 3, 10, 1, 2),
(6, 'ISC-5B-SAB', 1, '2025-09-01', '2025-09-01', 3, 11, 1, 2),
(7, 'IIN-1A', 1, '2025-09-01', '2025-09-01', 3, 6, 2, 1),
(8, 'IIN-3A', 1, '2025-09-01', '2025-09-01', 3, 7, 2, 1),
(9, 'LCP-1A', 1, '2025-09-01', '2025-09-01', 4, 8, 3, 1),
(10, 'LCP-3A', 1, '2025-09-01', '2025-09-01', 4, 9, 3, 1),
(11, 'ADM-1A', 1, '2025-09-01', '2025-09-01', 4, 10, 4, 1),
(12, 'IME-1A', 1, '2025-09-01', '2025-09-01', 3, 11, 5, 1),
(13, 'PSI-1A', 1, '2025-09-01', '2025-09-01', 5, 12, 6, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inscripciones`
--

CREATE TABLE `inscripciones` (
  `id` int(11) NOT NULL,
  `alumno_id` int(10) UNSIGNED NOT NULL,
  `clase_id` int(11) NOT NULL,
  `cal_final` decimal(4,2) DEFAULT NULL,
  `estado` enum('CURSANDO','APROBADO','REPROBADO','BAJA') DEFAULT 'CURSANDO',
  `estado_parcial1` enum('CURSANDO','APROBADO','REPROBADO') DEFAULT 'CURSANDO',
  `estado_parcial2` enum('CURSANDO','APROBADO','REPROBADO') DEFAULT 'CURSANDO',
  `estado_parcial3` enum('CURSANDO','APROBADO','REPROBADO') DEFAULT 'CURSANDO',
  `estado_parcial4` enum('CURSANDO','APROBADO','REPROBADO') DEFAULT 'CURSANDO',
  `fecha_alta` date DEFAULT curdate(),
  `fecha_baja` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inscripciones`
--

INSERT INTO `inscripciones` (`id`, `alumno_id`, `clase_id`, `cal_final`, `estado`, `estado_parcial1`, `estado_parcial2`, `estado_parcial3`, `estado_parcial4`, `fecha_alta`, `fecha_baja`) VALUES
(1, 1, 1, NULL, 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL),
(2, 2, 1, NULL, 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL),
(3, 3, 1, NULL, 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL),
(4, 4, 1, NULL, 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL),
(5, 5, 1, NULL, 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL),
(6, 6, 1, NULL, 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL),
(7, 7, 1, NULL, 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL),
(8, 8, 1, NULL, 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL),
(9, 9, 1, NULL, 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL),
(10, 10, 1, NULL, 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL),
(11, 31, 4, NULL, 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL),
(12, 32, 4, NULL, 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL),
(13, 33, 4, NULL, 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL),
(14, 34, 5, NULL, 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL),
(15, 35, 5, NULL, 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL),
(16, 36, 4, NULL, 'CURSANDO', 'APROBADO', 'CURSANDO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL),
(17, 37, 4, NULL, 'CURSANDO', 'REPROBADO', 'CURSANDO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL),
(18, 38, 5, NULL, 'CURSANDO', 'APROBADO', 'APROBADO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL),
(19, 24, 10, NULL, 'CURSANDO', 'APROBADO', 'APROBADO', 'REPROBADO', 'REPROBADO', '2025-11-30', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modalidades`
--

CREATE TABLE `modalidades` (
  `id_modalidad` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `estatus` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=Activo, 0=Inactivo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `modalidades`
--

INSERT INTO `modalidades` (`id_modalidad`, `nombre`, `estatus`) VALUES
(1, 'Escolarizado', 1),
(2, 'Sabatino', 1),
(3, 'Virtual', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `niveles_usuarios`
--

CREATE TABLE `niveles_usuarios` (
  `id_nivel_usuario` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `estatus` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=Activo, 0=Inactivo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `niveles_usuarios`
--

INSERT INTO `niveles_usuarios` (`id_nivel_usuario`, `nombre`, `descripcion`, `estatus`) VALUES
(1, 'Administrador', 'Acceso total al sistema', 1),
(2, 'Coordinador', 'Gestiona carreras y tutores', 1),
(3, 'Tutor', 'Da seguimiento a los alumnos de sus grupos', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `parciales`
--

CREATE TABLE `parciales` (
  `id` int(11) NOT NULL,
  `periodo_id` int(11) NOT NULL,
  `numero` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `parciales`
--

INSERT INTO `parciales` (`id`, `periodo_id`, `numero`, `nombre`, `fecha_inicio`, `fecha_fin`) VALUES
(1, 3, 1, 'Parcial 1', '2025-09-01', '2025-10-03'),
(2, 3, 2, 'Parcial 2', '2025-10-06', '2025-11-07'),
(3, 3, 3, 'Parcial 3', '2025-11-10', '2025-12-12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `periodos_escolares`
--

CREATE TABLE `periodos_escolares` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `periodos_escolares`
--

INSERT INTO `periodos_escolares` (`id`, `nombre`, `fecha_inicio`, `fecha_fin`, `activo`) VALUES
(1, 'Enero - Abril 2025', '2025-01-06', '2025-04-25', 0),
(2, 'Mayo - Agosto 2025', '2025-05-05', '2025-08-22', 0),
(3, 'Septiembre - Diciembre 2025', '2025-09-01', '2025-12-19', 1),
(4, 'Enero - Abril 2026', '2026-01-05', '2026-04-24', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seguimientos`
--

CREATE TABLE `seguimientos` (
  `id_seguimiento` bigint(20) UNSIGNED NOT NULL,
  `descripcion` text NOT NULL,
  `estatus` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=Abierto, 2=En Progreso, 3=Cerrado',
  `fecha_creacion` date NOT NULL DEFAULT curdate(),
  `fecha_movimiento` date NOT NULL DEFAULT curdate(),
  `fecha_compromiso` date DEFAULT NULL,
  `usuarios_id_usuario_movimiento` int(10) UNSIGNED DEFAULT NULL,
  `alumnos_id_alumno` int(10) UNSIGNED NOT NULL,
  `tutor_id` int(10) UNSIGNED DEFAULT NULL,
  `tipo_seguimiento_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `seguimientos`
--

INSERT INTO `seguimientos` (`id_seguimiento`, `descripcion`, `estatus`, `fecha_creacion`, `fecha_movimiento`, `fecha_compromiso`, `usuarios_id_usuario_movimiento`, `alumnos_id_alumno`, `tutor_id`, `tipo_seguimiento_id`) VALUES
(1, 'El alumno presenta dificultades en la asignatura de Fundamentos de Programación. Se requiere apoyo adicional.', 1, '2025-11-15', '2025-11-15', '2025-12-15', 6, 1, 6, 1),
(2, 'Seguimiento por bajo rendimiento académico. El alumno ha faltado a varias clases.', 2, '2025-11-10', '2025-11-20', '2025-12-10', 6, 2, 6, 1),
(3, 'El alumno solicita información sobre becas disponibles.', 1, '2025-11-18', '2025-11-18', '2025-11-25', 7, 31, 7, 3),
(4, 'Seguimiento conductual: problemas de integración con el grupo.', 1, '2025-11-12', '2025-11-12', '2025-12-12', 7, 32, 7, 4),
(5, 'Seguimiento personal: el alumno menciona problemas familiares que afectan su desempeño.', 2, '2025-11-05', '2025-11-15', '2025-12-05', 8, 51, 8, 2),
(6, 'Seguimiento cerrado: problema resuelto satisfactoriamente.', 3, '2025-10-20', '2025-11-10', '2025-11-20', 6, 3, 6, 1),
(7, 'El alumno requiere canalización a psicología por ansiedad académica.', 1, '2025-11-22', '2025-11-22', '2025-12-22', 9, 66, 9, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_seguimiento`
--

CREATE TABLE `tipo_seguimiento` (
  `id_tipo_seguimiento` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `estatus` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=Activo, 0=Inactivo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipo_seguimiento`
--

INSERT INTO `tipo_seguimiento` (`id_tipo_seguimiento`, `nombre`, `estatus`) VALUES
(1, 'Académico', 1),
(2, 'Personal', 1),
(3, 'Financiero', 1),
(4, 'Conductual', 1),
(5, 'Psicopedagógico', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tutorias_asistencia`
--

CREATE TABLE `tutorias_asistencia` (
  `id` int(11) NOT NULL,
  `tutoria_evento_id` int(11) NOT NULL,
  `alumno_id` int(10) UNSIGNED NOT NULL,
  `asistio` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tutorias_eventos`
--

CREATE TABLE `tutorias_eventos` (
  `id` int(11) NOT NULL,
  `grupo_id` int(10) UNSIGNED NOT NULL,
  `parcial_id` int(11) NOT NULL,
  `sesion_num` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `tipo` enum('GRUPAL','INDIVIDUAL') NOT NULL,
  `actividad_id` int(11) DEFAULT NULL,
  `actividad_nombre` varchar(150) DEFAULT NULL,
  `actividad_descripcion` text DEFAULT NULL,
  `evidencia_file_id` int(11) DEFAULT NULL,
  `evidencia_foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tutorias_grupales`
--

CREATE TABLE `tutorias_grupales` (
  `id` int(11) NOT NULL,
  `grupo_id` int(10) UNSIGNED NOT NULL,
  `parcial_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `actividad_nombre` varchar(200) NOT NULL,
  `actividad_descripcion` text DEFAULT NULL,
  `evidencia_foto_id` int(11) DEFAULT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tutorias_grupales`
--

INSERT INTO `tutorias_grupales` (`id`, `grupo_id`, `parcial_id`, `fecha`, `actividad_nombre`, `actividad_descripcion`, `evidencia_foto_id`, `usuario_id`, `created_at`) VALUES
(1, 1, 1, '2025-09-15', 'Bienvenida e Inducción', 'Primera sesión de tutoría grupal con el grupo ISC-1A. Se presentaron las actividades del PAT.', NULL, 6, '2025-09-15 16:00:00'),
(2, 1, 1, '2025-10-05', 'Técnicas de Estudio', 'Taller sobre técnicas de estudio y organización del tiempo.', NULL, 6, '2025-10-05 16:00:00'),
(3, 2, 1, '2025-09-20', 'Orientación Académica', 'Sesión sobre plan de estudios y materias del semestre.', NULL, 7, '2025-09-20 17:00:00'),
(4, 3, 1, '2025-09-18', 'Prevención de Deserción', 'Charla sobre la importancia de la permanencia escolar.', NULL, 8, '2025-09-18 20:00:00'),
(5, 1, 1, '2025-12-01', 'hola mundo', 'desde', 1, 6, '2025-12-01 03:04:20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tutorias_grupales_asistencia`
--

CREATE TABLE `tutorias_grupales_asistencia` (
  `id` int(11) NOT NULL,
  `tutoria_grupal_id` int(11) NOT NULL,
  `alumno_id` int(10) UNSIGNED NOT NULL,
  `presente` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tutorias_grupales_asistencia`
--

INSERT INTO `tutorias_grupales_asistencia` (`id`, `tutoria_grupal_id`, `alumno_id`, `presente`) VALUES
(14, 3, 31, 1),
(15, 3, 32, 1),
(16, 3, 33, 1),
(17, 3, 34, 1),
(45, 2, 24, 1),
(46, 2, 1, 1),
(47, 2, 2, 1),
(48, 2, 5, 1),
(49, 2, 4, 1),
(50, 1, 24, 1),
(51, 1, 7, 1),
(52, 1, 1, 1),
(53, 1, 2, 1),
(54, 1, 6, 1),
(55, 1, 5, 1),
(56, 1, 3, 1),
(177, 5, 24, 1),
(178, 5, 112, 1),
(179, 5, 30, 1),
(180, 5, 19, 1),
(181, 5, 8, 1),
(182, 5, 14, 1),
(183, 5, 22, 1),
(184, 5, 7, 1),
(185, 5, 12, 1),
(186, 5, 26, 1),
(187, 5, 1, 1),
(188, 5, 18, 1),
(189, 5, 2, 1),
(190, 5, 111, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tutorias_individuales`
--

CREATE TABLE `tutorias_individuales` (
  `id` int(11) NOT NULL,
  `alumno_id` int(10) UNSIGNED NOT NULL,
  `grupo_id` int(10) UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `motivo` text DEFAULT NULL,
  `acciones` text DEFAULT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tutorias_individuales`
--

INSERT INTO `tutorias_individuales` (`id`, `alumno_id`, `grupo_id`, `fecha`, `motivo`, `acciones`, `usuario_id`, `created_at`) VALUES
(1, 1, 1, '2025-11-15', 'Bajo rendimiento en Fundamentos de Programación', 'Se acordó proporcionar material adicional y sesiones de apoyo.', 6, '2025-11-15 21:00:00'),
(2, 2, 1, '2025-11-10', 'Faltas frecuentes a clases', 'Se estableció compromiso de asistencia y seguimiento semanal.', 6, '2025-11-10 22:00:00'),
(3, 31, 2, '2025-11-18', 'Consulta sobre becas', 'Se proporcionó información sobre becas disponibles y proceso de solicitud.', 7, '2025-11-18 16:00:00'),
(4, 32, 2, '2025-11-12', 'Problemas de integración', 'Se trabajó en dinámicas de integración y comunicación.', 7, '2025-11-12 20:00:00'),
(5, 51, 3, '2025-11-05', 'Problemas familiares', 'Se brindó apoyo emocional y se canalizó a servicios de psicología.', 8, '2025-11-05 17:00:00'),
(6, 24, 1, '2025-12-01', 'Bajo rendimiento académico', 'Canalización a servicios de apoyo', 6, '2025-12-01 04:17:11');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `apellido_paterno` varchar(50) NOT NULL,
  `apellido_materno` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL COMMENT 'Almacenar siempre como hash, nunca texto plano',
  `estatus` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=Activo, 0=Inactivo',
  `fecha_creacion` date NOT NULL DEFAULT curdate(),
  `fecha_movimiento` date NOT NULL DEFAULT curdate(),
  `usuarios_id_usuario_movimiento` int(10) UNSIGNED DEFAULT NULL,
  `niveles_usuarios_id_nivel_usuario` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido_paterno`, `apellido_materno`, `email`, `password`, `estatus`, `fecha_creacion`, `fecha_movimiento`, `usuarios_id_usuario_movimiento`, `niveles_usuarios_id_nivel_usuario`) VALUES
(1, 'Carlos', 'Administrador', 'Sistema', 'admin@gestacad.edu.mx', '$2y$10$DA0yUN7mYyblZxLi7HDZ.e0unE1ftObEmwzWvD/thSH0S0qBgujl6', 1, '2025-01-01', '2025-01-01', 1, 1),
(2, 'Ana', 'García', 'López', 'ana.admin@gestacad.edu.mx', '$2y$10$2P0xT/0DNgx1iXQB7bChjeBfmILeHbTP181rKI/Uhnaxx8H1LG7Cu', 1, '2025-01-01', '2025-01-01', 1, 1),
(3, 'Roberto', 'Martínez', 'Sánchez', 'r.martinez@gestacad.edu.mx', '$2y$10$o57Yd3we38LaoUmPc5IcHeubqoxJcUpJezbuaJa3l19wdFDthenRO', 1, '2025-01-01', '2025-01-01', 1, 2),
(4, 'María', 'Rodríguez', 'Fernández', 'm.rodriguez@gestacad.edu.mx', '$2y$10$2P0xT/0DNgx1iXQB7bChjeBfmILeHbTP181rKI/Uhnaxx8H1LG7Cu', 1, '2025-01-01', '2025-01-01', 1, 2),
(5, 'Luis', 'Hernández', 'González', 'l.hernandez@gestacad.edu.mx', '$2y$10$2P0xT/0DNgx1iXQB7bChjeBfmILeHbTP181rKI/Uhnaxx8H1LG7Cu', 1, '2025-01-01', '2025-01-01', 1, 2),
(6, 'Patricia', 'Gómez', 'Morales', 'p.gomez@gestacad.edu.mx', '$2y$10$9XBR7OW8AJ2EZImWu5TBl.ABHfOxaD.FGsiYQnR4jsCakeimZDEKK', 1, '2025-01-01', '2025-01-01', 1, 3),
(7, 'Juan', 'Pérez', 'Ramírez', 'j.perez@gestacad.edu.mx', '$2y$10$2P0xT/0DNgx1iXQB7bChjeBfmILeHbTP181rKI/Uhnaxx8H1LG7Cu', 1, '2025-01-01', '2025-01-01', 3, 3),
(8, 'Laura', 'Díaz', 'Torres', 'l.diaz@gestacad.edu.mx', '$2y$10$2P0xT/0DNgx1iXQB7bChjeBfmILeHbTP181rKI/Uhnaxx8H1LG7Cu', 1, '2025-01-01', '2025-01-01', 3, 3),
(9, 'Miguel', 'Vargas', 'Castro', 'm.vargas@gestacad.edu.mx', '$2y$10$2P0xT/0DNgx1iXQB7bChjeBfmILeHbTP181rKI/Uhnaxx8H1LG7Cu', 1, '2025-01-01', '2025-01-01', 3, 3),
(10, 'Carmen', 'Ruiz', 'Jiménez', 'c.ruiz@gestacad.edu.mx', '$2y$10$2P0xT/0DNgx1iXQB7bChjeBfmILeHbTP181rKI/Uhnaxx8H1LG7Cu', 1, '2025-01-01', '2025-01-01', 3, 3),
(11, 'Fernando', 'Mendoza', 'Ortega', 'f.mendoza@gestacad.edu.mx', '$2y$10$2P0xT/0DNgx1iXQB7bChjeBfmILeHbTP181rKI/Uhnaxx8H1LG7Cu', 1, '2025-01-01', '2025-01-01', 3, 3),
(12, 'Sofía', 'Castro', 'Ramos', 's.castro@gestacad.edu.mx', '$2y$10$YlBL7.wUSPlMTZCPm/bgwusWzfc7euN3l6.uFJSBFKREKrXG6ebX.', 1, '2025-01-01', '2025-01-01', 1, 3);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actividades_pat`
--
ALTER TABLE `actividades_pat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `carrera_id` (`carrera_id`),
  ADD KEY `grupo_id` (`grupo_id`),
  ADD KEY `parcial_id` (`parcial_id`);

--
-- Indices de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD PRIMARY KEY (`id_alumno`),
  ADD UNIQUE KEY `matricula` (`matricula`),
  ADD KEY `fk_alumnos_usuario_movimiento` (`usuarios_id_usuario_movimiento`),
  ADD KEY `fk_alumnos_carreras` (`carreras_id_carrera`),
  ADD KEY `fk_alumnos_grupos` (`grupos_id_grupo`),
  ADD KEY `idx_alumnos_grupo` (`grupos_id_grupo`),
  ADD KEY `idx_alumnos_carrera` (`carreras_id_carrera`),
  ADD KEY `idx_alumnos_estatus` (`estatus`),
  ADD KEY `idx_alumnos_matricula` (`matricula`),
  ADD KEY `idx_alumnos_nombre` (`nombre`),
  ADD KEY `idx_alumnos_grupo_estatus` (`grupos_id_grupo`,`estatus`);
ALTER TABLE `alumnos` ADD FULLTEXT KEY `idx_alumnos_busqueda` (`nombre`,`apellido_paterno`,`apellido_materno`,`matricula`);

--
-- Indices de la tabla `alumno_beca`
--
ALTER TABLE `alumno_beca`
  ADD PRIMARY KEY (`id`),
  ADD KEY `alumno_id` (`alumno_id`),
  ADD KEY `beca_id` (`beca_id`),
  ADD KEY `periodo_id` (`periodo_id`);

--
-- Indices de la tabla `alumno_riesgo_desercion`
--
ALTER TABLE `alumno_riesgo_desercion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `alumno_id` (`alumno_id`),
  ADD KEY `periodo_id` (`periodo_id`);

--
-- Indices de la tabla `asignaturas`
--
ALTER TABLE `asignaturas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `asistencia_ip_bloqueos`
--
ALTER TABLE `asistencia_ip_bloqueos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ip_address` (`ip_address`),
  ADD KEY `bloqueado_hasta` (`bloqueado_hasta`),
  ADD KEY `token_usado` (`token_usado`);

--
-- Indices de la tabla `asistencia_tokens`
--
ALTER TABLE `asistencia_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `grupo_id` (`grupo_id`),
  ADD KEY `tutoria_grupal_id` (`tutoria_grupal_id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `expira_en` (`expira_en`),
  ADD KEY `idx_ip_address` (`ip_address`),
  ADD KEY `idx_ultimo_uso` (`ultimo_uso`);

--
-- Indices de la tabla `canalizacion`
--
ALTER TABLE `canalizacion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `alumno_id` (`alumno_id`),
  ADD KEY `periodo_id` (`periodo_id`),
  ADD KEY `area_id` (`area_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `carreras`
--
ALTER TABLE `carreras`
  ADD PRIMARY KEY (`id_carrera`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD KEY `fk_carreras_usuario_movimiento` (`usuarios_id_usuario_movimiento`),
  ADD KEY `fk_carreras_usuario_coordinador` (`usuarios_id_usuario_coordinador`);

--
-- Indices de la tabla `catalogos_faltas`
--
ALTER TABLE `catalogos_faltas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cat_areas_canalizacion`
--
ALTER TABLE `cat_areas_canalizacion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cat_becas`
--
ALTER TABLE `cat_becas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `clases`
--
ALTER TABLE `clases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asignatura_id` (`asignatura_id`),
  ADD KEY `periodo_id` (`periodo_id`),
  ADD KEY `docente_usuario_id` (`docente_usuario_id`),
  ADD KEY `modalidad_id` (`modalidad_id`),
  ADD KEY `grupo_referencia` (`grupo_referencia`);

--
-- Indices de la tabla `divisiones`
--
ALTER TABLE `divisiones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD PRIMARY KEY (`id_grupo`),
  ADD KEY `fk_grupos_usuario_movimiento` (`usuarios_id_usuario_movimiento`),
  ADD KEY `fk_grupos_usuario_tutor` (`usuarios_id_usuario_tutor`),
  ADD KEY `fk_grupos_carreras` (`carreras_id_carrera`),
  ADD KEY `fk_grupos_modalidades` (`modalidades_id_modalidad`),
  ADD KEY `idx_grupos_tutor` (`usuarios_id_usuario_tutor`),
  ADD KEY `idx_grupos_carrera` (`carreras_id_carrera`),
  ADD KEY `idx_grupos_modalidad` (`modalidades_id_modalidad`);

--
-- Indices de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `alumno_id` (`alumno_id`),
  ADD KEY `clase_id` (`clase_id`);

--
-- Indices de la tabla `modalidades`
--
ALTER TABLE `modalidades`
  ADD PRIMARY KEY (`id_modalidad`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `niveles_usuarios`
--
ALTER TABLE `niveles_usuarios`
  ADD PRIMARY KEY (`id_nivel_usuario`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `parciales`
--
ALTER TABLE `parciales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `periodo_id` (`periodo_id`);

--
-- Indices de la tabla `periodos_escolares`
--
ALTER TABLE `periodos_escolares`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `seguimientos`
--
ALTER TABLE `seguimientos`
  ADD PRIMARY KEY (`id_seguimiento`),
  ADD KEY `fk_seguimientos_usuario_movimiento` (`usuarios_id_usuario_movimiento`),
  ADD KEY `fk_seguimientos_alumnos` (`alumnos_id_alumno`),
  ADD KEY `fk_seguimientos_tipo` (`tipo_seguimiento_id`),
  ADD KEY `fk_seguimientos_tutor` (`tutor_id`),
  ADD KEY `idx_seguimientos_alumno` (`alumnos_id_alumno`),
  ADD KEY `idx_seguimientos_tipo` (`tipo_seguimiento_id`),
  ADD KEY `idx_seguimientos_fecha` (`fecha_creacion`),
  ADD KEY `idx_seguimientos_estatus` (`estatus`),
  ADD KEY `idx_seguimientos_alumno_fecha` (`alumnos_id_alumno`,`fecha_creacion`);

--
-- Indices de la tabla `tipo_seguimiento`
--
ALTER TABLE `tipo_seguimiento`
  ADD PRIMARY KEY (`id_tipo_seguimiento`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `tutorias_asistencia`
--
ALTER TABLE `tutorias_asistencia`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_asistencia` (`tutoria_evento_id`,`alumno_id`),
  ADD KEY `alumno_id` (`alumno_id`);

--
-- Indices de la tabla `tutorias_eventos`
--
ALTER TABLE `tutorias_eventos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grupo_id` (`grupo_id`),
  ADD KEY `parcial_id` (`parcial_id`),
  ADD KEY `actividad_id` (`actividad_id`);

--
-- Indices de la tabla `tutorias_grupales`
--
ALTER TABLE `tutorias_grupales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grupo_id` (`grupo_id`),
  ADD KEY `parcial_id` (`parcial_id`),
  ADD KEY `evidencia_foto_id` (`evidencia_foto_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `tutorias_grupales_asistencia`
--
ALTER TABLE `tutorias_grupales_asistencia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tutoria_grupal_id` (`tutoria_grupal_id`),
  ADD KEY `alumno_id` (`alumno_id`);

--
-- Indices de la tabla `tutorias_individuales`
--
ALTER TABLE `tutorias_individuales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `alumno_id` (`alumno_id`),
  ADD KEY `fk_tutorias_individuales_grupo` (`grupo_id`),
  ADD KEY `idx_usuario_id` (`usuario_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `fk_usuarios_niveles` (`niveles_usuarios_id_nivel_usuario`),
  ADD KEY `fk_usuarios_self_movimiento` (`usuarios_id_usuario_movimiento`),
  ADD KEY `idx_usuarios_email` (`email`),
  ADD KEY `idx_usuarios_nivel` (`niveles_usuarios_id_nivel_usuario`),
  ADD KEY `idx_usuarios_estatus` (`estatus`),
  ADD KEY `idx_usuarios_nivel_estatus` (`niveles_usuarios_id_nivel_usuario`,`estatus`);
ALTER TABLE `usuarios` ADD FULLTEXT KEY `idx_usuarios_busqueda` (`nombre`,`apellido_paterno`,`apellido_materno`,`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actividades_pat`
--
ALTER TABLE `actividades_pat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  MODIFY `id_alumno` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT de la tabla `alumno_beca`
--
ALTER TABLE `alumno_beca`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `alumno_riesgo_desercion`
--
ALTER TABLE `alumno_riesgo_desercion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `asignaturas`
--
ALTER TABLE `asignaturas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `asistencia_ip_bloqueos`
--
ALTER TABLE `asistencia_ip_bloqueos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `asistencia_tokens`
--
ALTER TABLE `asistencia_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `canalizacion`
--
ALTER TABLE `canalizacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `carreras`
--
ALTER TABLE `carreras`
  MODIFY `id_carrera` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `catalogos_faltas`
--
ALTER TABLE `catalogos_faltas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `cat_areas_canalizacion`
--
ALTER TABLE `cat_areas_canalizacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `cat_becas`
--
ALTER TABLE `cat_becas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `clases`
--
ALTER TABLE `clases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `divisiones`
--
ALTER TABLE `divisiones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `grupos`
--
ALTER TABLE `grupos`
  MODIFY `id_grupo` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `modalidades`
--
ALTER TABLE `modalidades`
  MODIFY `id_modalidad` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `niveles_usuarios`
--
ALTER TABLE `niveles_usuarios`
  MODIFY `id_nivel_usuario` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `parciales`
--
ALTER TABLE `parciales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `periodos_escolares`
--
ALTER TABLE `periodos_escolares`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `seguimientos`
--
ALTER TABLE `seguimientos`
  MODIFY `id_seguimiento` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `tipo_seguimiento`
--
ALTER TABLE `tipo_seguimiento`
  MODIFY `id_tipo_seguimiento` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tutorias_asistencia`
--
ALTER TABLE `tutorias_asistencia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tutorias_eventos`
--
ALTER TABLE `tutorias_eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tutorias_grupales`
--
ALTER TABLE `tutorias_grupales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tutorias_grupales_asistencia`
--
ALTER TABLE `tutorias_grupales_asistencia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=191;

--
-- AUTO_INCREMENT de la tabla `tutorias_individuales`
--
ALTER TABLE `tutorias_individuales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `actividades_pat`
--
ALTER TABLE `actividades_pat`
  ADD CONSTRAINT `fk_pat_carrera` FOREIGN KEY (`carrera_id`) REFERENCES `carreras` (`id_carrera`),
  ADD CONSTRAINT `fk_pat_grupo` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id_grupo`),
  ADD CONSTRAINT `fk_pat_parcial` FOREIGN KEY (`parcial_id`) REFERENCES `parciales` (`id`);

--
-- Filtros para la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD CONSTRAINT `fk_alumnos_carreras` FOREIGN KEY (`carreras_id_carrera`) REFERENCES `carreras` (`id_carrera`),
  ADD CONSTRAINT `fk_alumnos_grupos` FOREIGN KEY (`grupos_id_grupo`) REFERENCES `grupos` (`id_grupo`),
  ADD CONSTRAINT `fk_alumnos_usuario_movimiento` FOREIGN KEY (`usuarios_id_usuario_movimiento`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `alumno_beca`
--
ALTER TABLE `alumno_beca`
  ADD CONSTRAINT `fk_beca_alumno` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id_alumno`),
  ADD CONSTRAINT `fk_beca_cat` FOREIGN KEY (`beca_id`) REFERENCES `cat_becas` (`id`),
  ADD CONSTRAINT `fk_beca_periodo` FOREIGN KEY (`periodo_id`) REFERENCES `periodos_escolares` (`id`);

--
-- Filtros para la tabla `alumno_riesgo_desercion`
--
ALTER TABLE `alumno_riesgo_desercion`
  ADD CONSTRAINT `fk_riesgo_alumno` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id_alumno`),
  ADD CONSTRAINT `fk_riesgo_periodo` FOREIGN KEY (`periodo_id`) REFERENCES `periodos_escolares` (`id`);

--
-- Filtros para la tabla `asistencia_tokens`
--
ALTER TABLE `asistencia_tokens`
  ADD CONSTRAINT `fk_asistencia_tokens_grupo` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id_grupo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_asistencia_tokens_tutoria` FOREIGN KEY (`tutoria_grupal_id`) REFERENCES `tutorias_grupales` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_asistencia_tokens_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `canalizacion`
--
ALTER TABLE `canalizacion`
  ADD CONSTRAINT `fk_canalizacion_alumno` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id_alumno`),
  ADD CONSTRAINT `fk_canalizacion_area` FOREIGN KEY (`area_id`) REFERENCES `cat_areas_canalizacion` (`id`),
  ADD CONSTRAINT `fk_canalizacion_periodo` FOREIGN KEY (`periodo_id`) REFERENCES `periodos_escolares` (`id`),
  ADD CONSTRAINT `fk_canalizacion_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `carreras`
--
ALTER TABLE `carreras`
  ADD CONSTRAINT `fk_carreras_usuario_coordinador` FOREIGN KEY (`usuarios_id_usuario_coordinador`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `fk_carreras_usuario_movimiento` FOREIGN KEY (`usuarios_id_usuario_movimiento`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `clases`
--
ALTER TABLE `clases`
  ADD CONSTRAINT `fk_clases_asignatura` FOREIGN KEY (`asignatura_id`) REFERENCES `asignaturas` (`id`),
  ADD CONSTRAINT `fk_clases_docente` FOREIGN KEY (`docente_usuario_id`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `fk_clases_grupo` FOREIGN KEY (`grupo_referencia`) REFERENCES `grupos` (`id_grupo`),
  ADD CONSTRAINT `fk_clases_modalidad` FOREIGN KEY (`modalidad_id`) REFERENCES `modalidades` (`id_modalidad`),
  ADD CONSTRAINT `fk_clases_periodo` FOREIGN KEY (`periodo_id`) REFERENCES `periodos_escolares` (`id`);

--
-- Filtros para la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD CONSTRAINT `fk_grupos_carreras` FOREIGN KEY (`carreras_id_carrera`) REFERENCES `carreras` (`id_carrera`),
  ADD CONSTRAINT `fk_grupos_modalidades` FOREIGN KEY (`modalidades_id_modalidad`) REFERENCES `modalidades` (`id_modalidad`),
  ADD CONSTRAINT `fk_grupos_usuario_movimiento` FOREIGN KEY (`usuarios_id_usuario_movimiento`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `fk_grupos_usuario_tutor` FOREIGN KEY (`usuarios_id_usuario_tutor`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD CONSTRAINT `fk_inscripciones_alumno` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id_alumno`),
  ADD CONSTRAINT `fk_inscripciones_clase` FOREIGN KEY (`clase_id`) REFERENCES `clases` (`id`);

--
-- Filtros para la tabla `parciales`
--
ALTER TABLE `parciales`
  ADD CONSTRAINT `fk_parciales_periodo` FOREIGN KEY (`periodo_id`) REFERENCES `periodos_escolares` (`id`);

--
-- Filtros para la tabla `seguimientos`
--
ALTER TABLE `seguimientos`
  ADD CONSTRAINT `fk_seguimientos_alumnos` FOREIGN KEY (`alumnos_id_alumno`) REFERENCES `alumnos` (`id_alumno`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_seguimientos_tipo` FOREIGN KEY (`tipo_seguimiento_id`) REFERENCES `tipo_seguimiento` (`id_tipo_seguimiento`),
  ADD CONSTRAINT `fk_seguimientos_tutor` FOREIGN KEY (`tutor_id`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `fk_seguimientos_usuario_movimiento` FOREIGN KEY (`usuarios_id_usuario_movimiento`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `tutorias_asistencia`
--
ALTER TABLE `tutorias_asistencia`
  ADD CONSTRAINT `tutorias_asistencia_ibfk_1` FOREIGN KEY (`tutoria_evento_id`) REFERENCES `tutorias_eventos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tutorias_asistencia_ibfk_2` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id_alumno`);

--
-- Filtros para la tabla `tutorias_eventos`
--
ALTER TABLE `tutorias_eventos`
  ADD CONSTRAINT `fk_eventos_actividad` FOREIGN KEY (`actividad_id`) REFERENCES `actividades_pat` (`id`),
  ADD CONSTRAINT `fk_eventos_grupo` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id_grupo`),
  ADD CONSTRAINT `fk_eventos_parcial` FOREIGN KEY (`parcial_id`) REFERENCES `parciales` (`id`);

--
-- Filtros para la tabla `tutorias_grupales`
--
ALTER TABLE `tutorias_grupales`
  ADD CONSTRAINT `fk_tutorias_grupales_file` FOREIGN KEY (`evidencia_foto_id`) REFERENCES `files` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tutorias_grupales_grupo` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id_grupo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tutorias_grupales_parcial` FOREIGN KEY (`parcial_id`) REFERENCES `parciales` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tutorias_grupales_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `tutorias_grupales_asistencia`
--
ALTER TABLE `tutorias_grupales_asistencia`
  ADD CONSTRAINT `fk_asistencia_alumno` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id_alumno`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_asistencia_tutoria_grupal` FOREIGN KEY (`tutoria_grupal_id`) REFERENCES `tutorias_grupales` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tutorias_individuales`
--
ALTER TABLE `tutorias_individuales`
  ADD CONSTRAINT `fk_tutorias_individuales_grupo` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id_grupo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tutorias_individuales_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `tutorias_individuales_ibfk_1` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id_alumno`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_niveles` FOREIGN KEY (`niveles_usuarios_id_nivel_usuario`) REFERENCES `niveles_usuarios` (`id_nivel_usuario`),
  ADD CONSTRAINT `fk_usuarios_self_movimiento` FOREIGN KEY (`usuarios_id_usuario_movimiento`) REFERENCES `usuarios` (`id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
