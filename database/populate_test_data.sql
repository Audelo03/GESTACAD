-- =====================================================
-- Script para vaciar y poblar la base de datos con datos de prueba
-- Base de datos: gestacadv2
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 0;
SET AUTOCOMMIT = 0;
START TRANSACTION;

-- =====================================================
-- PASO 1: VACIAR TODAS LAS TABLAS (en orden inverso de dependencias)
-- =====================================================

DELETE FROM `tutorias_grupales_asistencia`;
DELETE FROM `tutorias_individuales`;
DELETE FROM `tutorias_grupales`;
DELETE FROM `tutorias_asistencia`;
DELETE FROM `tutorias_eventos`;
DELETE FROM `inscripciones`;
DELETE FROM `asistencias`;
DELETE FROM `seguimientos`;
DELETE FROM `canalizacion`;
DELETE FROM `alumno_riesgo_desercion`;
DELETE FROM `alumno_beca`;
DELETE FROM `actividades_pat`;
DELETE FROM `alumnos`;
DELETE FROM `clases`;
DELETE FROM `grupos`;
DELETE FROM `files`;
DELETE FROM `carreras`;
DELETE FROM `usuarios`;
DELETE FROM `asignaturas`;
DELETE FROM `parciales`;
DELETE FROM `periodos_escolares`;
DELETE FROM `catalogos_faltas`;
DELETE FROM `cat_areas_canalizacion`;
DELETE FROM `cat_becas`;
DELETE FROM `tipo_seguimiento`;
DELETE FROM `modalidades`;
DELETE FROM `divisiones`;
DELETE FROM `niveles_usuarios`;

-- Resetear AUTO_INCREMENT
ALTER TABLE `actividades_pat` AUTO_INCREMENT = 1;
ALTER TABLE `alumnos` AUTO_INCREMENT = 1;
ALTER TABLE `alumno_beca` AUTO_INCREMENT = 1;
ALTER TABLE `alumno_riesgo_desercion` AUTO_INCREMENT = 1;
ALTER TABLE `asignaturas` AUTO_INCREMENT = 1;
ALTER TABLE `asistencias` AUTO_INCREMENT = 1;
ALTER TABLE `canalizacion` AUTO_INCREMENT = 1;
ALTER TABLE `carreras` AUTO_INCREMENT = 1;
ALTER TABLE `catalogos_faltas` AUTO_INCREMENT = 1;
ALTER TABLE `cat_areas_canalizacion` AUTO_INCREMENT = 1;
ALTER TABLE `cat_becas` AUTO_INCREMENT = 1;
ALTER TABLE `clases` AUTO_INCREMENT = 1;
ALTER TABLE `divisiones` AUTO_INCREMENT = 1;
ALTER TABLE `files` AUTO_INCREMENT = 1;
ALTER TABLE `grupos` AUTO_INCREMENT = 1;
ALTER TABLE `inscripciones` AUTO_INCREMENT = 1;
ALTER TABLE `modalidades` AUTO_INCREMENT = 1;
ALTER TABLE `niveles_usuarios` AUTO_INCREMENT = 1;
ALTER TABLE `parciales` AUTO_INCREMENT = 1;
ALTER TABLE `periodos_escolares` AUTO_INCREMENT = 1;
ALTER TABLE `seguimientos` AUTO_INCREMENT = 1;
ALTER TABLE `tipo_seguimiento` AUTO_INCREMENT = 1;
ALTER TABLE `tutorias_asistencia` AUTO_INCREMENT = 1;
ALTER TABLE `tutorias_eventos` AUTO_INCREMENT = 1;
ALTER TABLE `tutorias_grupales` AUTO_INCREMENT = 1;
ALTER TABLE `tutorias_grupales_asistencia` AUTO_INCREMENT = 1;
ALTER TABLE `tutorias_individuales` AUTO_INCREMENT = 1;
ALTER TABLE `usuarios` AUTO_INCREMENT = 1;

-- =====================================================
-- PASO 2: INSERTAR DATOS BASE (catálogos y niveles)
-- =====================================================

-- Niveles de usuarios
INSERT INTO `niveles_usuarios` (`id_nivel_usuario`, `nombre`, `descripcion`, `estatus`) VALUES
(1, 'Administrador', 'Acceso total al sistema', 1),
(2, 'Coordinador', 'Gestiona carreras y tutores', 1),
(3, 'Tutor', 'Da seguimiento a los alumnos de sus grupos', 1);

-- Modalidades
INSERT INTO `modalidades` (`id_modalidad`, `nombre`, `estatus`) VALUES
(1, 'Escolarizado', 1),
(2, 'Sabatino', 1),
(3, 'Virtual', 1);

-- Divisiones
INSERT INTO `divisiones` (`id`, `nombre`, `activo`) VALUES
(1, 'Ingeniería y Tecnología', 1),
(2, 'Ciencias Económico Administrativas', 1),
(3, 'Ciencias de la Salud', 1);

-- Tipo de seguimiento
INSERT INTO `tipo_seguimiento` (`id_tipo_seguimiento`, `nombre`, `estatus`) VALUES
(1, 'Académico', 1),
(2, 'Personal', 1),
(3, 'Financiero', 1),
(4, 'Conductual', 1),
(5, 'Psicopedagógico', 1);

-- Catálogo de becas
INSERT INTO `cat_becas` (`id`, `clave`, `nombre`, `activo`) VALUES
(1, 'INST', 'Beca Institucional', 1),
(2, 'EXCEL', 'Beca de Excelencia', 1),
(3, 'DEPOR', 'Beca Deportiva', 1),
(4, 'SOCIAL', 'Beca Socioeconómica', 1),
(5, 'CULT', 'Beca Cultural', 1);

-- Catálogo de áreas de canalización
INSERT INTO `cat_areas_canalizacion` (`id`, `nombre`, `activo`) VALUES
(1, 'Psicología', 1),
(2, 'Nutrición', 1),
(3, 'Servicios Médicos', 1),
(4, 'Asesoría Académica', 1),
(5, 'Orientación Vocacional', 1);

-- Catálogo de faltas
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

-- =====================================================
-- PASO 3: INSERTAR PERIODOS ESCOLARES Y PARCIALES
-- =====================================================

-- Periodos escolares
INSERT INTO `periodos_escolares` (`id`, `nombre`, `fecha_inicio`, `fecha_fin`, `activo`) VALUES
(1, 'Enero - Abril 2025', '2025-01-06', '2025-04-25', 0),
(2, 'Mayo - Agosto 2025', '2025-05-05', '2025-08-22', 0),
(3, 'Septiembre - Diciembre 2025', '2025-09-01', '2025-12-19', 1),
(4, 'Enero - Abril 2026', '2026-01-05', '2026-04-24', 0);

-- Parciales para el periodo activo
INSERT INTO `parciales` (`id`, `periodo_id`, `numero`, `nombre`, `fecha_inicio`, `fecha_fin`) VALUES
(1, 3, 1, 'Parcial 1', '2025-09-01', '2025-10-03'),
(2, 3, 2, 'Parcial 2', '2025-10-06', '2025-11-07'),
(3, 3, 3, 'Parcial 3', '2025-11-10', '2025-12-12');

-- =====================================================
-- PASO 4: INSERTAR USUARIOS
-- =====================================================

-- Usuarios (contraseñas en texto plano para facilitar acceso inicial - el sistema acepta texto plano)
INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido_paterno`, `apellido_materno`, `email`, `password`, `estatus`, `fecha_creacion`, `fecha_movimiento`, `usuarios_id_usuario_movimiento`, `niveles_usuarios_id_nivel_usuario`) VALUES
-- Administradores
(1, 'Carlos', 'Administrador', 'Sistema', 'admin@gestacad.edu.mx', 'password123', 1, '2025-01-01', '2025-01-01', 1, 1),
(2, 'Ana', 'García', 'López', 'ana.admin@gestacad.edu.mx', 'password123', 1, '2025-01-01', '2025-01-01', 1, 1),

-- Coordinadores
(3, 'Roberto', 'Martínez', 'Sánchez', 'r.martinez@gestacad.edu.mx', 'password123', 1, '2025-01-01', '2025-01-01', 1, 2),
(4, 'María', 'Rodríguez', 'Fernández', 'm.rodriguez@gestacad.edu.mx', 'password123', 1, '2025-01-01', '2025-01-01', 1, 2),
(5, 'Luis', 'Hernández', 'González', 'l.hernandez@gestacad.edu.mx', 'password123', 1, '2025-01-01', '2025-01-01', 1, 2),

-- Tutores
(6, 'Patricia', 'Gómez', 'Morales', 'p.gomez@gestacad.edu.mx', 'password123', 1, '2025-01-01', '2025-01-01', 3, 3),
(7, 'Juan', 'Pérez', 'Ramírez', 'j.perez@gestacad.edu.mx', 'password123', 1, '2025-01-01', '2025-01-01', 3, 3),
(8, 'Laura', 'Díaz', 'Torres', 'l.diaz@gestacad.edu.mx', 'password123', 1, '2025-01-01', '2025-01-01', 3, 3),
(9, 'Miguel', 'Vargas', 'Castro', 'm.vargas@gestacad.edu.mx', 'password123', 1, '2025-01-01', '2025-01-01', 3, 3),
(10, 'Carmen', 'Ruiz', 'Jiménez', 'c.ruiz@gestacad.edu.mx', 'password123', 1, '2025-01-01', '2025-01-01', 3, 3),
(11, 'Fernando', 'Mendoza', 'Ortega', 'f.mendoza@gestacad.edu.mx', 'password123', 1, '2025-01-01', '2025-01-01', 3, 3),
(12, 'Sofía', 'Castro', 'Ramos', 's.castro@gestacad.edu.mx', 'password123', 1, '2025-01-01', '2025-01-01', 3, 3);

-- =====================================================
-- PASO 5: INSERTAR CARRERAS
-- =====================================================

INSERT INTO `carreras` (`id_carrera`, `nombre`, `fecha_creacion`, `fecha_movimiento`, `usuarios_id_usuario_movimiento`, `usuarios_id_usuario_coordinador`, `estatus`) VALUES
(1, 'Ingeniería en Sistemas Computacionales', '2025-01-01', '2025-01-01', 1, 3, 1),
(2, 'Ingeniería Industrial', '2025-01-01', '2025-01-01', 1, 3, 1),
(3, 'Contaduría Pública', '2025-01-01', '2025-01-01', 1, 4, 1),
(4, 'Administración de Empresas', '2025-01-01', '2025-01-01', 1, 4, 1),
(5, 'Ingeniería en Mecatrónica', '2025-01-01', '2025-01-01', 1, 3, 1),
(6, 'Psicología', '2025-01-01', '2025-01-01', 1, 5, 1);

-- =====================================================
-- PASO 6: INSERTAR ASIGNATURAS
-- =====================================================

INSERT INTO `asignaturas` (`id`, `clave`, `nombre`, `creditos`, `horas_semana`, `area`, `activo`) VALUES
-- Asignaturas de Sistemas
(1, 'ISC101', 'Fundamentos de Programación', 6, 6, 'Programación', 1),
(2, 'ISC102', 'Estructuras de Datos', 6, 6, 'Programación', 1),
(3, 'ISC103', 'Bases de Datos', 6, 6, 'Bases de Datos', 1),
(4, 'ISC104', 'Ingeniería de Software', 5, 5, 'Ingeniería', 1),
(5, 'ISC105', 'Redes de Computadoras', 5, 5, 'Redes', 1),
(6, 'ISC201', 'Programación Orientada a Objetos', 6, 6, 'Programación', 1),
(7, 'ISC202', 'Desarrollo Web', 6, 6, 'Programación', 1),
(8, 'ISC203', 'Sistemas Operativos', 5, 5, 'Sistemas', 1),
-- Asignaturas de Industrial
(9, 'IIN101', 'Introducción a la Ingeniería Industrial', 4, 4, 'Fundamentos', 1),
(10, 'IIN102', 'Estadística Aplicada', 5, 5, 'Matemáticas', 1),
(11, 'IIN103', 'Procesos de Manufactura', 6, 6, 'Manufactura', 1),
-- Asignaturas de Contaduría
(12, 'LCP101', 'Contabilidad Básica', 6, 6, 'Contabilidad', 1),
(13, 'LCP102', 'Matemáticas Financieras', 5, 5, 'Matemáticas', 1),
(14, 'LCP103', 'Derecho Fiscal', 5, 5, 'Derecho', 1),
-- Asignaturas Generales
(15, 'MAT101', 'Cálculo Diferencial', 5, 5, 'Matemáticas', 1),
(16, 'MAT102', 'Cálculo Integral', 5, 5, 'Matemáticas', 1),
(17, 'FIS101', 'Física I', 5, 5, 'Física', 1),
(18, 'ADM101', 'Administración I', 4, 4, 'Administración', 1);

-- =====================================================
-- PASO 7: INSERTAR GRUPOS
-- =====================================================

INSERT INTO `grupos` (`id_grupo`, `nombre`, `estatus`, `fecha_creacion`, `fecha_movimiento`, `usuarios_id_usuario_movimiento`, `usuarios_id_usuario_tutor`, `carreras_id_carrera`, `modalidades_id_modalidad`) VALUES
-- Grupos de Sistemas (Escolarizado)
(1, 'ISC-1A', 1, '2025-09-01', '2025-09-01', 3, 6, 1, 1),
(2, 'ISC-3A', 1, '2025-09-01', '2025-09-01', 3, 7, 1, 1),
(3, 'ISC-5A', 1, '2025-09-01', '2025-09-01', 3, 8, 1, 1),
(4, 'ISC-7A', 1, '2025-09-01', '2025-09-01', 3, 9, 1, 1),
-- Grupos de Sistemas (Sabatino)
(5, 'ISC-3B-SAB', 1, '2025-09-01', '2025-09-01', 3, 10, 1, 2),
(6, 'ISC-5B-SAB', 1, '2025-09-01', '2025-09-01', 3, 11, 1, 2),
-- Grupos de Industrial
(7, 'IIN-1A', 1, '2025-09-01', '2025-09-01', 3, 6, 2, 1),
(8, 'IIN-3A', 1, '2025-09-01', '2025-09-01', 3, 7, 2, 1),
-- Grupos de Contaduría
(9, 'LCP-1A', 1, '2025-09-01', '2025-09-01', 4, 8, 3, 1),
(10, 'LCP-3A', 1, '2025-09-01', '2025-09-01', 4, 9, 3, 1),
-- Grupos de Administración
(11, 'ADM-1A', 1, '2025-09-01', '2025-09-01', 4, 10, 4, 1),
-- Grupos de Mecatrónica
(12, 'IME-1A', 1, '2025-09-01', '2025-09-01', 3, 11, 5, 1),
-- Grupos de Psicología
(13, 'PSI-1A', 1, '2025-09-01', '2025-09-01', 5, 12, 6, 1);

-- =====================================================
-- PASO 8: INSERTAR ALUMNOS (150 alumnos distribuidos)
-- =====================================================

-- Alumnos para ISC-1A (30 alumnos)
INSERT INTO `alumnos` (`matricula`, `nombre`, `apellido_paterno`, `apellido_materno`, `estatus`, `fecha_creacion`, `fecha_movimiento`, `usuarios_id_usuario_movimiento`, `carreras_id_carrera`, `grupos_id_grupo`, `genero`) VALUES
('ISC2025001', 'Alejandro', 'González', 'Martínez', 1, '2025-09-01', '2025-09-01', 3, 1, 1, 'M'),
('ISC2025002', 'Ana Sofía', 'Hernández', 'López', 1, '2025-09-01', '2025-09-01', 3, 1, 1, 'F'),
('ISC2025003', 'Carlos', 'Ramírez', 'García', 1, '2025-09-01', '2025-09-01', 3, 1, 1, 'M'),
('ISC2025004', 'Daniela', 'Torres', 'Sánchez', 1, '2025-09-01', '2025-09-01', 3, 1, 1, 'F'),
('ISC2025005', 'Eduardo', 'Morales', 'Fernández', 1, '2025-09-01', '2025-09-01', 3, 1, 1, 'M'),
('ISC2025006', 'Fernanda', 'Jiménez', 'Ruiz', 1, '2025-09-01', '2025-09-01', 3, 1, 1, 'F'),
('ISC2025007', 'Gabriel', 'Díaz', 'Vargas', 1, '2025-09-01', '2025-09-01', 3, 1, 1, 'M'),
('ISC2025008', 'Isabella', 'Castro', 'Mendoza', 1, '2025-09-01', '2025-09-01', 3, 1, 1, 'F'),
('ISC2025009', 'Javier', 'Ortega', 'Ramos', 1, '2025-09-01', '2025-09-01', 3, 1, 1, 'M'),
('ISC2025010', 'Karina', 'Pérez', 'Gómez', 1, '2025-09-01', '2025-09-01', 3, 1, 1, 'F'),
('ISC2025011', 'Luis', 'Rivera', 'Cruz', 1, '2025-09-01', '2025-09-01', 3, 1, 1, 'M'),
('ISC2025012', 'Mariana', 'Flores', 'Ortiz', 1, '2025-09-01', '2025-09-01', 3, 1, 1, 'F'),
('ISC2025013', 'Nicolás', 'Soto', 'Gutiérrez', 1, '2025-09-01', '2025-09-01', 3, 1, 1, 'M'),
('ISC2025014', 'Olivia', 'Chávez', 'Moreno', 1, '2025-09-01', '2025-09-01', 3, 1, 1, 'F'),
('ISC2025015', 'Pablo', 'Reyes', 'Delgado', 1, '2025-09-01', '2025-09-01', 3, 1, 1, 'M'),
('ISC2025016', 'Valentina', 'Mendoza', 'Silva', 1, '2025-09-01', '2025-09-01', 3, 1, 1, 'F'),
('ISC2025017', 'Ricardo', 'Vega', 'Herrera', 1, '2025-09-01', '2025-09-01', 3, 1, 1, 'M'),
('ISC2025018', 'Samantha', 'Guerrero', 'Medina', 1, '2025-09-01', '2025-09-01', 3, 1, 1, 'F'),
('ISC2025019', 'Tomás', 'Campos', 'Aguilar', 1, '2025-09-01', '2025-09-01', 3, 1, 1, 'M'),
('ISC2025020', 'Ximena', 'Rojas', 'Vázquez', 1, '2025-09-01', '2025-09-01', 3, 1, 1, 'F'),
('ISC2025021', 'Yahir', 'Salazar', 'Espinoza', 1, '2025-09-01', '2025-09-01', 3, 1, 1, 'M'),
('ISC2025022', 'Zoe', 'Contreras', 'Navarro', 1, '2025-09-01', '2025-09-01', 3, 1, 1, 'F'),
('ISC2025023', 'Adrián', 'Luna', 'Molina', 1, '2025-09-01', '2025-09-01', 3, 1, 1, 'M'),
('ISC2025024', 'Brenda', 'Álvarez', 'Cortés', 1, '2025-09-01', '2025-09-01', 3, 1, 1, 'F'),
('ISC2025025', 'César', 'Méndez', 'Lara', 1, '2025-09-01', '2025-09-01', 3, 1, 1, 'M'),
('ISC2025026', 'Diana', 'Fuentes', 'Peña', 1, '2025-09-01', '2025-09-01', 3, 1, 1, 'F'),
('ISC2025027', 'Emilio', 'Ramos', 'Sosa', 1, '2025-09-01', '2025-09-01', 3, 1, 1, 'M'),
('ISC2025028', 'Fabiola', 'Villanueva', 'Tapia', 1, '2025-09-01', '2025-09-01', 3, 1, 1, 'F'),
('ISC2025029', 'Gustavo', 'Zamora', 'León', 1, '2025-09-01', '2025-09-01', 3, 1, 1, 'M'),
('ISC2025030', 'Hilda', 'Barrera', 'Miranda', 1, '2025-09-01', '2025-09-01', 3, 1, 1, 'F');

-- Alumnos para ISC-3A (25 alumnos)
INSERT INTO `alumnos` (`matricula`, `nombre`, `apellido_paterno`, `apellido_materno`, `estatus`, `fecha_creacion`, `fecha_movimiento`, `usuarios_id_usuario_movimiento`, `carreras_id_carrera`, `grupos_id_grupo`, `genero`) VALUES
('ISC2023001', 'Iván', 'Cervantes', 'Núñez', 1, '2025-09-01', '2025-09-01', 3, 1, 2, 'M'),
('ISC2023002', 'Julia', 'Espinosa', 'Valdez', 1, '2025-09-01', '2025-09-01', 3, 1, 2, 'F'),
('ISC2023003', 'Kevin', 'Galván', 'Acosta', 1, '2025-09-01', '2025-09-01', 3, 1, 2, 'M'),
('ISC2023004', 'Liliana', 'Ibarra', 'Benítez', 1, '2025-09-01', '2025-09-01', 3, 1, 2, 'F'),
('ISC2023005', 'Manuel', 'Juárez', 'Cárdenas', 1, '2025-09-01', '2025-09-01', 3, 1, 2, 'M'),
('ISC2023006', 'Natalia', 'Lozano', 'Durán', 1, '2025-09-01', '2025-09-01', 3, 1, 2, 'F'),
('ISC2023007', 'Óscar', 'Montes', 'Escobar', 1, '2025-09-01', '2025-09-01', 3, 1, 2, 'M'),
('ISC2023008', 'Patricia', 'Nava', 'Fajardo', 1, '2025-09-01', '2025-09-01', 3, 1, 2, 'F'),
('ISC2023009', 'Quetzal', 'Ochoa', 'Guzmán', 1, '2025-09-01', '2025-09-01', 3, 1, 2, 'M'),
('ISC2023010', 'Rosa', 'Paredes', 'Hidalgo', 1, '2025-09-01', '2025-09-01', 3, 1, 2, 'F'),
('ISC2023011', 'Sergio', 'Quiroz', 'Ibarra', 1, '2025-09-01', '2025-09-01', 3, 1, 2, 'M'),
('ISC2023012', 'Teresa', 'Ríos', 'Jasso', 1, '2025-09-01', '2025-09-01', 3, 1, 2, 'F'),
('ISC2023013', 'Ulises', 'Sosa', 'Kuri', 1, '2025-09-01', '2025-09-01', 3, 1, 2, 'M'),
('ISC2023014', 'Verónica', 'Téllez', 'Lara', 1, '2025-09-01', '2025-09-01', 3, 1, 2, 'F'),
('ISC2023015', 'Wilfrido', 'Uribe', 'Márquez', 1, '2025-09-01', '2025-09-01', 3, 1, 2, 'M'),
('ISC2023016', 'Yolanda', 'Valdés', 'Nieto', 1, '2025-09-01', '2025-09-01', 3, 1, 2, 'F'),
('ISC2023017', 'Zacarías', 'Wong', 'Orozco', 1, '2025-09-01', '2025-09-01', 3, 1, 2, 'M'),
('ISC2023018', 'Alicia', 'Xicoténcatl', 'Pacheco', 1, '2025-09-01', '2025-09-01', 3, 1, 2, 'F'),
('ISC2023019', 'Benito', 'Yáñez', 'Quiroz', 1, '2025-09-01', '2025-09-01', 3, 1, 2, 'M'),
('ISC2023020', 'Cecilia', 'Zaragoza', 'Reyes', 1, '2025-09-01', '2025-09-01', 3, 1, 2, 'F'),
('ISC2023021', 'Diego', 'Aguilar', 'Sánchez', 1, '2025-09-01', '2025-09-01', 3, 1, 2, 'M'),
('ISC2023022', 'Elena', 'Benítez', 'Torres', 1, '2025-09-01', '2025-09-01', 3, 1, 2, 'F'),
('ISC2023023', 'Felipe', 'Cárdenas', 'Uribe', 1, '2025-09-01', '2025-09-01', 3, 1, 2, 'M'),
('ISC2023024', 'Gloria', 'Durán', 'Vega', 1, '2025-09-01', '2025-09-01', 3, 1, 2, 'F'),
('ISC2023025', 'Héctor', 'Escobar', 'Wong', 1, '2025-09-01', '2025-09-01', 3, 1, 2, 'M');

-- Alumnos para otros grupos (distribuidos)
INSERT INTO `alumnos` (`matricula`, `nombre`, `apellido_paterno`, `apellido_materno`, `estatus`, `fecha_creacion`, `fecha_movimiento`, `usuarios_id_usuario_movimiento`, `carreras_id_carrera`, `grupos_id_grupo`, `genero`) VALUES
-- ISC-5A (20 alumnos)
('ISC2021001', 'Ignacio', 'Fajardo', 'Xicoténcatl', 1, '2025-09-01', '2025-09-01', 3, 1, 3, 'M'),
('ISC2021002', 'Jazmín', 'Guzmán', 'Yáñez', 1, '2025-09-01', '2025-09-01', 3, 1, 3, 'F'),
('ISC2021003', 'Karla', 'Hidalgo', 'Zaragoza', 1, '2025-09-01', '2025-09-01', 3, 1, 3, 'F'),
('ISC2021004', 'Leonardo', 'Ibarra', 'Aguilar', 1, '2025-09-01', '2025-09-01', 3, 1, 3, 'M'),
('ISC2021005', 'Mónica', 'Jasso', 'Benítez', 1, '2025-09-01', '2025-09-01', 3, 1, 3, 'F'),
('ISC2021006', 'Néstor', 'Kuri', 'Cárdenas', 1, '2025-09-01', '2025-09-01', 3, 1, 3, 'M'),
('ISC2021007', 'Ofelia', 'Lara', 'Durán', 1, '2025-09-01', '2025-09-01', 3, 1, 3, 'F'),
('ISC2021008', 'Pedro', 'Márquez', 'Escobar', 1, '2025-09-01', '2025-09-01', 3, 1, 3, 'M'),
('ISC2021009', 'Quetzalli', 'Nieto', 'Fajardo', 1, '2025-09-01', '2025-09-01', 3, 1, 3, 'F'),
('ISC2021010', 'Raúl', 'Orozco', 'Guzmán', 1, '2025-09-01', '2025-09-01', 3, 1, 3, 'M'),
('ISC2021011', 'Silvia', 'Pacheco', 'Hidalgo', 1, '2025-09-01', '2025-09-01', 3, 1, 3, 'F'),
('ISC2021012', 'Tadeo', 'Quiroz', 'Ibarra', 1, '2025-09-01', '2025-09-01', 3, 1, 3, 'M'),
('ISC2021013', 'Úrsula', 'Reyes', 'Jasso', 1, '2025-09-01', '2025-09-01', 3, 1, 3, 'F'),
('ISC2021014', 'Vicente', 'Sánchez', 'Kuri', 1, '2025-09-01', '2025-09-01', 3, 1, 3, 'M'),
('ISC2021015', 'Wendy', 'Torres', 'Lara', 1, '2025-09-01', '2025-09-01', 3, 1, 3, 'F'),
('ISC2021016', 'Xavier', 'Uribe', 'Márquez', 1, '2025-09-01', '2025-09-01', 3, 1, 3, 'M'),
('ISC2021017', 'Yadira', 'Vega', 'Nieto', 1, '2025-09-01', '2025-09-01', 3, 1, 3, 'F'),
('ISC2021018', 'Zacarías', 'Wong', 'Orozco', 1, '2025-09-01', '2025-09-01', 3, 1, 3, 'M'),
('ISC2021019', 'Adriana', 'Xicoténcatl', 'Pacheco', 1, '2025-09-01', '2025-09-01', 3, 1, 3, 'F'),
('ISC2021020', 'Bruno', 'Yáñez', 'Quiroz', 1, '2025-09-01', '2025-09-01', 3, 1, 3, 'M'),

-- ISC-7A (15 alumnos)
('ISC2019001', 'Camila', 'Zaragoza', 'Reyes', 1, '2025-09-01', '2025-09-01', 3, 1, 4, 'F'),
('ISC2019002', 'Dante', 'Aguilar', 'Sánchez', 1, '2025-09-01', '2025-09-01', 3, 1, 4, 'M'),
('ISC2019003', 'Esmeralda', 'Benítez', 'Torres', 1, '2025-09-01', '2025-09-01', 3, 1, 4, 'F'),
('ISC2019004', 'Federico', 'Cárdenas', 'Uribe', 1, '2025-09-01', '2025-09-01', 3, 1, 4, 'M'),
('ISC2019005', 'Gabriela', 'Durán', 'Vega', 1, '2025-09-01', '2025-09-01', 3, 1, 4, 'F'),
('ISC2019006', 'Hugo', 'Escobar', 'Wong', 1, '2025-09-01', '2025-09-01', 3, 1, 4, 'M'),
('ISC2019007', 'Iris', 'Fajardo', 'Xicoténcatl', 1, '2025-09-01', '2025-09-01', 3, 1, 4, 'F'),
('ISC2019008', 'Joaquín', 'Guzmán', 'Yáñez', 1, '2025-09-01', '2025-09-01', 3, 1, 4, 'M'),
('ISC2019009', 'Kenia', 'Hidalgo', 'Zaragoza', 1, '2025-09-01', '2025-09-01', 3, 1, 4, 'F'),
('ISC2019010', 'Lorenzo', 'Ibarra', 'Aguilar', 1, '2025-09-01', '2025-09-01', 3, 1, 4, 'M'),
('ISC2019011', 'Mireya', 'Jasso', 'Benítez', 1, '2025-09-01', '2025-09-01', 3, 1, 4, 'F'),
('ISC2019012', 'Nahum', 'Kuri', 'Cárdenas', 1, '2025-09-01', '2025-09-01', 3, 1, 4, 'M'),
('ISC2019013', 'Odalys', 'Lara', 'Durán', 1, '2025-09-01', '2025-09-01', 3, 1, 4, 'F'),
('ISC2019014', 'Pascual', 'Márquez', 'Escobar', 1, '2025-09-01', '2025-09-01', 3, 1, 4, 'M'),
('ISC2019015', 'Rebeca', 'Nieto', 'Fajardo', 1, '2025-09-01', '2025-09-01', 3, 1, 4, 'F'),

-- ISC-3B-SAB (20 alumnos)
('ISC2023S001', 'Salvador', 'Orozco', 'Guzmán', 1, '2025-09-01', '2025-09-01', 3, 1, 5, 'M'),
('ISC2023S002', 'Tania', 'Pacheco', 'Hidalgo', 1, '2025-09-01', '2025-09-01', 3, 1, 5, 'F'),
('ISC2023S003', 'Ubaldo', 'Quiroz', 'Ibarra', 1, '2025-09-01', '2025-09-01', 3, 1, 5, 'M'),
('ISC2023S004', 'Vanesa', 'Reyes', 'Jasso', 1, '2025-09-01', '2025-09-01', 3, 1, 5, 'F'),
('ISC2023S005', 'Wenceslao', 'Sánchez', 'Kuri', 1, '2025-09-01', '2025-09-01', 3, 1, 5, 'M'),
('ISC2023S006', 'Xóchitl', 'Torres', 'Lara', 1, '2025-09-01', '2025-09-01', 3, 1, 5, 'F'),
('ISC2023S007', 'Yair', 'Uribe', 'Márquez', 1, '2025-09-01', '2025-09-01', 3, 1, 5, 'M'),
('ISC2023S008', 'Zulema', 'Vega', 'Nieto', 1, '2025-09-01', '2025-09-01', 3, 1, 5, 'F'),
('ISC2023S009', 'Abel', 'Wong', 'Orozco', 1, '2025-09-01', '2025-09-01', 3, 1, 5, 'M'),
('ISC2023S010', 'Beatriz', 'Xicoténcatl', 'Pacheco', 1, '2025-09-01', '2025-09-01', 3, 1, 5, 'F'),
('ISC2023S011', 'Ciro', 'Yáñez', 'Quiroz', 1, '2025-09-01', '2025-09-01', 3, 1, 5, 'M'),
('ISC2023S012', 'Dolores', 'Zaragoza', 'Reyes', 1, '2025-09-01', '2025-09-01', 3, 1, 5, 'F'),
('ISC2023S013', 'Efraín', 'Aguilar', 'Sánchez', 1, '2025-09-01', '2025-09-01', 3, 1, 5, 'M'),
('ISC2023S014', 'Flor', 'Benítez', 'Torres', 1, '2025-09-01', '2025-09-01', 3, 1, 5, 'F'),
('ISC2023S015', 'Gerardo', 'Cárdenas', 'Uribe', 1, '2025-09-01', '2025-09-01', 3, 1, 5, 'M'),
('ISC2023S016', 'Herminia', 'Durán', 'Vega', 1, '2025-09-01', '2025-09-01', 3, 1, 5, 'F'),
('ISC2023S017', 'Isidro', 'Escobar', 'Wong', 1, '2025-09-01', '2025-09-01', 3, 1, 5, 'M'),
('ISC2023S018', 'Josefina', 'Fajardo', 'Xicoténcatl', 1, '2025-09-01', '2025-09-01', 3, 1, 5, 'F'),
('ISC2023S019', 'Kike', 'Guzmán', 'Yáñez', 1, '2025-09-01', '2025-09-01', 3, 1, 5, 'M'),
('ISC2023S020', 'Leticia', 'Hidalgo', 'Zaragoza', 1, '2025-09-01', '2025-09-01', 3, 1, 5, 'F'),

-- Algunos alumnos inactivos o con baja
('ISC20250031', 'Roberto', 'Inactivo', 'Test', 0, '2025-09-01', '2025-09-15', 3, 1, 1, 'M'),
('ISC20250032', 'Laura', 'Baja', 'Test', 3, '2025-09-01', '2025-09-20', 3, 1, 1, 'F'),
('ISC20230026', 'Pedro', 'Egresado', 'Test', 2, '2025-09-01', '2025-09-01', 3, 1, 2, 'M');

-- =====================================================
-- PASO 9: INSERTAR CLASES
-- =====================================================

INSERT INTO `clases` (`id`, `asignatura_id`, `periodo_id`, `docente_usuario_id`, `seccion`, `modalidad_id`, `cupo`, `grupo_referencia`, `aula`, `activo`) VALUES
-- Clases para ISC-1A
(1, 1, 3, 6, 'A', 1, 35, 1, 'LAB-101', 1),
(2, 15, 3, 6, 'A', 1, 35, 1, 'A-201', 1),
(3, 17, 3, 6, 'A', 1, 35, 1, 'LAB-FIS', 1),
-- Clases para ISC-3A
(4, 2, 3, 7, 'A', 1, 30, 2, 'LAB-102', 1),
(5, 6, 3, 7, 'A', 1, 30, 2, 'LAB-103', 1),
(6, 3, 3, 7, 'A', 1, 30, 2, 'LAB-104', 1),
-- Clases para ISC-5A
(7, 4, 3, 8, 'A', 1, 25, 3, 'A-301', 1),
(8, 5, 3, 8, 'A', 1, 25, 3, 'LAB-RED', 1),
(9, 7, 3, 8, 'A', 1, 25, 3, 'LAB-105', 1),
-- Clases para ISC-7A
(10, 4, 3, 9, 'A', 1, 20, 4, 'A-401', 1),
(11, 8, 3, 9, 'A', 1, 20, 4, 'LAB-106', 1);

-- =====================================================
-- PASO 10: INSERTAR INSCRIPCIONES
-- =====================================================

-- Inscripciones para alumnos de ISC-1A en sus clases
INSERT INTO `inscripciones` (`alumno_id`, `clase_id`, `cal_final`, `estado`, `estado_parcial1`, `estado_parcial2`, `estado_parcial3`, `estado_parcial4`, `fecha_alta`, `fecha_baja`) VALUES
-- Primeros 10 alumnos de ISC-1A en clase 1 (Fundamentos de Programación)
(1, 1, NULL, 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL),
(2, 1, NULL, 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL),
(3, 1, NULL, 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL),
(4, 1, NULL, 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL),
(5, 1, NULL, 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL),
(6, 1, NULL, 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL),
(7, 1, NULL, 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL),
(8, 1, NULL, 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL),
(9, 1, NULL, 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL),
(10, 1, NULL, 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL),
-- Algunos alumnos de ISC-3A
(31, 4, NULL, 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL),
(32, 4, NULL, 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL),
(33, 4, NULL, 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL),
(34, 5, NULL, 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL),
(35, 5, NULL, 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL),
-- Algunos con estados parciales diferentes
(36, 4, NULL, 'CURSANDO', 'APROBADO', 'CURSANDO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL),
(37, 4, NULL, 'CURSANDO', 'REPROBADO', 'CURSANDO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL),
(38, 5, NULL, 'CURSANDO', 'APROBADO', 'APROBADO', 'CURSANDO', 'CURSANDO', '2025-09-01', NULL);

-- =====================================================
-- PASO 11: INSERTAR ASISTENCIAS (últimos 30 días)
-- =====================================================

-- Generar asistencias para algunos alumnos en diferentes fechas
INSERT INTO `asistencias` (`id_alumno`, `id_grupo`, `fecha`, `estatus`, `fecha_registro`) VALUES
-- Asistencias para alumno 1 (ISC-1A) - últimos 10 días hábiles
(1, 1, '2025-11-20', 1, '2025-11-20'),
(1, 1, '2025-11-21', 1, '2025-11-21'),
(1, 1, '2025-11-22', 1, '2025-11-22'),
(1, 1, '2025-11-25', 0, '2025-11-25'),
(1, 1, '2025-11-26', 1, '2025-11-26'),
(1, 1, '2025-11-27', 1, '2025-11-27'),
(1, 1, '2025-11-28', 1, '2025-11-28'),
(1, 1, '2025-11-29', 0, '2025-11-29'),
(1, 1, '2025-12-02', 1, '2025-12-02'),
(1, 1, '2025-12-03', 1, '2025-12-03'),
-- Asistencias para alumno 2
(2, 1, '2025-11-20', 1, '2025-11-20'),
(2, 1, '2025-11-21', 1, '2025-11-21'),
(2, 1, '2025-11-22', 1, '2025-11-22'),
(2, 1, '2025-11-25', 1, '2025-11-25'),
(2, 1, '2025-11-26', 1, '2025-11-26'),
(2, 1, '2025-11-27', 0, '2025-11-27'),
(2, 1, '2025-11-28', 1, '2025-11-28'),
(2, 1, '2025-11-29', 1, '2025-11-29'),
(2, 1, '2025-12-02', 1, '2025-12-02'),
(2, 1, '2025-12-03', 1, '2025-12-03'),
-- Asistencias para alumnos de ISC-3A
(31, 2, '2025-11-20', 1, '2025-11-20'),
(31, 2, '2025-11-21', 0, '2025-11-21'),
(31, 2, '2025-11-22', 1, '2025-11-22'),
(31, 2, '2025-11-25', 1, '2025-11-25'),
(32, 2, '2025-11-20', 1, '2025-11-20'),
(32, 2, '2025-11-21', 1, '2025-11-21'),
(32, 2, '2025-11-22', 1, '2025-11-22'),
(33, 2, '2025-11-20', 0, '2025-11-20'),
(33, 2, '2025-11-21', 0, '2025-11-21'),
(33, 2, '2025-11-22', 1, '2025-11-22');

-- =====================================================
-- PASO 12: INSERTAR SEGUIMIENTOS
-- =====================================================

INSERT INTO `seguimientos` (`descripcion`, `estatus`, `fecha_creacion`, `fecha_movimiento`, `fecha_compromiso`, `usuarios_id_usuario_movimiento`, `alumnos_id_alumno`, `tutor_id`, `tipo_seguimiento_id`) VALUES
('El alumno presenta dificultades en la asignatura de Fundamentos de Programación. Se requiere apoyo adicional.', 1, '2025-11-15', '2025-11-15', '2025-12-15', 6, 1, 6, 1),
('Seguimiento por bajo rendimiento académico. El alumno ha faltado a varias clases.', 2, '2025-11-10', '2025-11-20', '2025-12-10', 6, 2, 6, 1),
('El alumno solicita información sobre becas disponibles.', 1, '2025-11-18', '2025-11-18', '2025-11-25', 7, 31, 7, 3),
('Seguimiento conductual: problemas de integración con el grupo.', 1, '2025-11-12', '2025-11-12', '2025-12-12', 7, 32, 7, 4),
('Seguimiento personal: el alumno menciona problemas familiares que afectan su desempeño.', 2, '2025-11-05', '2025-11-15', '2025-12-05', 8, 51, 8, 2),
('Seguimiento cerrado: problema resuelto satisfactoriamente.', 3, '2025-10-20', '2025-11-10', '2025-11-20', 6, 3, 6, 1),
('El alumno requiere canalización a psicología por ansiedad académica.', 1, '2025-11-22', '2025-11-22', '2025-12-22', 9, 66, 9, 5);

-- =====================================================
-- PASO 13: INSERTAR ACTIVIDADES PAT
-- =====================================================

INSERT INTO `actividades_pat` (`carrera_id`, `grupo_id`, `parcial_id`, `sesion_num`, `nombre`, `descripcion`) VALUES
(1, NULL, 1, 1, 'Bienvenida e Inducción', 'Presentación del reglamento y servicios escolares.'),
(1, NULL, 1, 2, 'Diagnóstico de Hábitos de Estudio', 'Aplicación de encuesta para identificar hábitos de estudio.'),
(1, NULL, 1, 3, 'Técnicas de Estudio', 'Taller sobre técnicas efectivas de estudio.'),
(1, 1, 1, 1, 'Dinámica de Integración', 'Actividad grupal para conocerse mejor.'),
(1, 2, 1, 1, 'Orientación Vocacional', 'Sesión sobre orientación vocacional y profesional.'),
(2, NULL, 1, 1, 'Inducción a la Carrera', 'Presentación de la carrera de Ingeniería Industrial.');

-- =====================================================
-- PASO 14: INSERTAR TUTORÍAS GRUPALES
-- =====================================================

INSERT INTO `tutorias_grupales` (`grupo_id`, `parcial_id`, `fecha`, `actividad_nombre`, `actividad_descripcion`, `evidencia_foto_id`, `usuario_id`, `created_at`) VALUES
(1, 1, '2025-09-15', 'Bienvenida e Inducción', 'Primera sesión de tutoría grupal con el grupo ISC-1A. Se presentaron las actividades del PAT.', NULL, 6, '2025-09-15 10:00:00'),
(1, 1, '2025-10-05', 'Técnicas de Estudio', 'Taller sobre técnicas de estudio y organización del tiempo.', NULL, 6, '2025-10-05 10:00:00'),
(2, 1, '2025-09-20', 'Orientación Académica', 'Sesión sobre plan de estudios y materias del semestre.', NULL, 7, '2025-09-20 11:00:00'),
(3, 1, '2025-09-18', 'Prevención de Deserción', 'Charla sobre la importancia de la permanencia escolar.', NULL, 8, '2025-09-18 14:00:00');

-- Asistencia a tutorías grupales
INSERT INTO `tutorias_grupales_asistencia` (`tutoria_grupal_id`, `alumno_id`, `presente`) VALUES
(1, 1, 1),
(1, 2, 1),
(1, 3, 1),
(1, 4, 0),
(1, 5, 1),
(1, 6, 1),
(1, 7, 1),
(1, 8, 0),
(2, 1, 1),
(2, 2, 1),
(2, 3, 0),
(2, 4, 1),
(2, 5, 1),
(3, 31, 1),
(3, 32, 1),
(3, 33, 1),
(3, 34, 1);

-- =====================================================
-- PASO 15: INSERTAR TUTORÍAS INDIVIDUALES
-- =====================================================

INSERT INTO `tutorias_individuales` (`alumno_id`, `grupo_id`, `fecha`, `motivo`, `acciones`, `usuario_id`, `created_at`) VALUES
(1, 1, '2025-11-15', 'Bajo rendimiento en Fundamentos de Programación', 'Se acordó proporcionar material adicional y sesiones de apoyo.', 6, '2025-11-15 15:00:00'),
(2, 1, '2025-11-10', 'Faltas frecuentes a clases', 'Se estableció compromiso de asistencia y seguimiento semanal.', 6, '2025-11-10 16:00:00'),
(31, 2, '2025-11-18', 'Consulta sobre becas', 'Se proporcionó información sobre becas disponibles y proceso de solicitud.', 7, '2025-11-18 10:00:00'),
(32, 2, '2025-11-12', 'Problemas de integración', 'Se trabajó en dinámicas de integración y comunicación.', 7, '2025-11-12 14:00:00'),
(51, 3, '2025-11-05', 'Problemas familiares', 'Se brindó apoyo emocional y se canalizó a servicios de psicología.', 8, '2025-11-05 11:00:00');

-- =====================================================
-- PASO 16: INSERTAR RIESGO DE DESERCIÓN
-- =====================================================

INSERT INTO `alumno_riesgo_desercion` (`alumno_id`, `periodo_id`, `posible`, `nivel`, `motivo`, `fuente`, `fecha_detectado`) VALUES
(2, 3, 1, 'MEDIO', 'Faltas frecuentes y bajo rendimiento académico', 'Sistema', '2025-11-10 10:00:00'),
(33, 3, 1, 'ALTO', 'Múltiples faltas consecutivas y sin comunicación', 'Tutor', '2025-11-20 14:00:00'),
(51, 3, 1, 'MEDIO', 'Problemas familiares que afectan el desempeño', 'Tutor', '2025-11-05 11:00:00'),
(66, 3, 1, 'BAJO', 'Ansiedad académica detectada', 'Tutor', '2025-11-22 09:00:00');

-- =====================================================
-- PASO 17: INSERTAR BECAS
-- =====================================================

INSERT INTO `alumno_beca` (`alumno_id`, `beca_id`, `periodo_id`, `porcentaje`, `monto`, `fecha_asignacion`) VALUES
(1, 2, 3, 50.00, 5000.00, '2025-09-01'),
(2, 4, 3, 30.00, 3000.00, '2025-09-01'),
(31, 1, 3, 25.00, 2500.00, '2025-09-01'),
(32, 3, 3, 40.00, 4000.00, '2025-09-01'),
(51, 2, 3, 50.00, 5000.00, '2025-09-01');

-- =====================================================
-- PASO 18: INSERTAR CANALIZACIONES
-- =====================================================

INSERT INTO `canalizacion` (`alumno_id`, `periodo_id`, `area_id`, `usuario_id`, `fecha_solicitud`, `observacion`, `estatus`) VALUES
(51, 3, 1, 8, '2025-11-05 11:00:00', 'El alumno requiere apoyo psicológico por problemas familiares que afectan su desempeño académico.', 'PENDIENTE'),
(66, 3, 1, 9, '2025-11-22 09:00:00', 'Canalización por ansiedad académica y estrés.', 'ATENDIDO'),
(2, 3, 4, 6, '2025-11-10 10:00:00', 'El alumno requiere asesoría académica para mejorar su rendimiento.', 'PENDIENTE');

-- =====================================================
-- FINALIZAR TRANSACCIÓN
-- =====================================================

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

-- =====================================================
-- RESUMEN DE DATOS INSERTADOS
-- =====================================================
-- Niveles de usuarios: 4
-- Usuarios: 12 (2 admin, 3 coordinadores, 7 tutores)
-- Carreras: 6
-- Modalidades: 3
-- Grupos: 13
-- Alumnos: ~120
-- Asignaturas: 18
-- Clases: 11
-- Inscripciones: ~18
-- Asistencias: ~30 registros
-- Seguimientos: 7
-- Tutorías grupales: 4
-- Tutorías individuales: 5
-- Actividades PAT: 6
-- Riesgo de deserción: 4
-- Becas: 5
-- Canalizaciones: 3
-- =====================================================

