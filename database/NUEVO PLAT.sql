CREATE TABLE `niveles_usuarios` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255),
  `activo` boolean NOT NULL DEFAULT true,
  `creado_en` datetime NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `usuarios` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(150) NOT NULL,
  `email` varchar(150) UNIQUE NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `nivel_id` int NOT NULL,
  `estatus` boolean NOT NULL DEFAULT true,
  `fecha_creacion` datetime NOT NULL DEFAULT (CURRENT_TIMESTAMP),
  `fecha_movimiento` datetime NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `perfiles_usuario` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `usuario_id` int UNIQUE NOT NULL,
  `num_empleado` varchar(30),
  `grado_academico` varchar(80),
  `area_servicio` varchar(120)
);

CREATE TABLE `divisiones` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nombre` varchar(120) UNIQUE NOT NULL,
  `activo` boolean NOT NULL DEFAULT true
);

CREATE TABLE `modalidades` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nombre` varchar(80) UNIQUE NOT NULL,
  `activo` boolean NOT NULL DEFAULT true
);

CREATE TABLE `carreras` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `division_id` int NOT NULL,
  `nombre` varchar(150) UNIQUE NOT NULL,
  `coordinador_usuario_id` int,
  `activo` boolean NOT NULL DEFAULT true
);

CREATE TABLE `grupos` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `carrera_id` int NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `modalidad_id` int NOT NULL,
  `tutor_usuario_id` int,
  `activo` boolean NOT NULL DEFAULT true
);

CREATE TABLE `alumnos` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `usuario_id` int UNIQUE NOT NULL,
  `matricula` varchar(20) UNIQUE NOT NULL,
  `carrera_id` int NOT NULL,
  `grupo_id` int NOT NULL,
  `genero` ENUM ('H', 'M', 'O') NOT NULL,
  `estatus` smallint NOT NULL DEFAULT 1,
  `fecha_creacion` datetime NOT NULL DEFAULT (CURRENT_TIMESTAMP),
  `fecha_movimiento` datetime NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `periodos_escolares` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nombre` varchar(50) UNIQUE NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `activo` boolean NOT NULL DEFAULT false
);

CREATE TABLE `parciales` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `periodo_id` int NOT NULL,
  `numero` smallint NOT NULL,
  `fecha_inicio` date,
  `fecha_fin` date
);

CREATE TABLE `actividades_pat` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `carrera_id` int,
  `grupo_id` int,
  `parcial_id` int NOT NULL,
  `sesion_num` smallint NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text
);

CREATE TABLE `files` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `ruta` varchar(500) NOT NULL,
  `tipo_mime` varchar(100) NOT NULL,
  `tamano` int NOT NULL,
  `hash` char(64) UNIQUE NOT NULL,
  `creado_en` datetime NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `tutorias_eventos` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `grupo_id` int NOT NULL,
  `parcial_id` int NOT NULL,
  `sesion_num` smallint NOT NULL,
  `fecha` date NOT NULL,
  `tipo` ENUM ('GRUPAL', 'INDIVIDUAL') NOT NULL,
  `actividad_id` int,
  `actividad_nombre` varchar(150),
  `actividad_descripcion` text,
  `evidencia_file_id` int
);

CREATE TABLE `catalogos_faltas` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `tipo` ENUM ('MOTIVO', 'ACCION', 'RAZON') NOT NULL,
  `clave` varchar(60) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `activo` boolean NOT NULL DEFAULT true
);

CREATE TABLE `asistencias` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `evento_id` int NOT NULL,
  `alumno_id` int NOT NULL,
  `estado` ENUM ('ASISTIO', 'FALTA', 'JUSTIFICADA'),
  `razon_inasistencia_id` int,
  `asistio` boolean,
  `motivo_id` int,
  `accion_id` int,
  `observaciones` varchar(500),
  `firma_file_id` int,
  `creado_en` datetime NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `reportes_generados` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `tipo` ENUM ('GRUPAL', 'INDIVIDUAL', 'LISTA') NOT NULL,
  `grupo_id` int NOT NULL,
  `parcial_id` int NOT NULL,
  `sesion_num` smallint,
  `archivo_file_id` int NOT NULL,
  `generado_por` int NOT NULL,
  `generado_en` datetime NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `asignaturas` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `clave` varchar(30) UNIQUE NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `creditos` smallint,
  `horas_semana` smallint,
  `area` varchar(80),
  `activo` boolean NOT NULL DEFAULT true
);

CREATE TABLE `clases` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `asignatura_id` int NOT NULL,
  `periodo_id` int NOT NULL,
  `docente_usuario_id` int NOT NULL,
  `seccion` varchar(20) NOT NULL,
  `modalidad_id` int,
  `cupo` smallint,
  `grupo_referencia` int,
  `aula` varchar(40),
  `activo` boolean NOT NULL DEFAULT true
);

CREATE TABLE `inscripciones` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `alumno_id` int NOT NULL,
  `clase_id` int NOT NULL,
  `estado` ENUM ('CURSANDO', 'BAJA', 'ACREDITADA', 'REPROBADA', 'IRREGULAR') NOT NULL DEFAULT 'CURSANDO',
  `fecha_alta` date NOT NULL DEFAULT (CURRENT_DATE),
  `fecha_baja` date,
  `cal_parcial1` decimal(5,2),
  `cal_parcial2` decimal(5,2),
  `cal_parcial3` decimal(5,2),
  `cal_parcial4` decimal(5,2),
  `cal_final` decimal(5,2)
);

CREATE TABLE `cat_becas` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `clave` varchar(40) UNIQUE NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `activo` boolean NOT NULL DEFAULT true
);

CREATE TABLE `alumno_beca` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `alumno_id` int NOT NULL,
  `periodo_id` int NOT NULL,
  `beca_id` int NOT NULL,
  `observacion` varchar(250),
  `vigente` boolean NOT NULL DEFAULT true,
  `creado_en` datetime NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `cat_areas_canalizacion` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `clave` varchar(40) UNIQUE NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `activo` boolean NOT NULL DEFAULT true
);

CREATE TABLE `canalizacion` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `alumno_id` int NOT NULL,
  `periodo_id` int NOT NULL,
  `area_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `observacion` varchar(250),
  `creado_en` datetime NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `alumno_riesgo_desercion` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `alumno_id` int NOT NULL,
  `periodo_id` int NOT NULL,
  `posible` boolean NOT NULL DEFAULT false,
  `nivel` ENUM ('NINGUNO', 'BAJO', 'MEDIO', 'ALTO') NOT NULL DEFAULT 'NINGUNO',
  `motivo` varchar(300),
  `fuente` varchar(80),
  `creado_en` datetime NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE INDEX `usuarios_index_0` ON `usuarios` (`nivel_id`);

CREATE UNIQUE INDEX `usuarios_index_1` ON `usuarios` (`email`);

CREATE INDEX `carreras_index_2` ON `carreras` (`division_id`);

CREATE INDEX `carreras_index_3` ON `carreras` (`coordinador_usuario_id`);

CREATE INDEX `grupos_index_4` ON `grupos` (`carrera_id`);

CREATE INDEX `grupos_index_5` ON `grupos` (`modalidad_id`);

CREATE INDEX `grupos_index_6` ON `grupos` (`tutor_usuario_id`);

CREATE INDEX `grupos_index_7` ON `grupos` (`nombre`);

CREATE INDEX `alumnos_index_8` ON `alumnos` (`grupo_id`);

CREATE INDEX `alumnos_index_9` ON `alumnos` (`carrera_id`);

CREATE INDEX `alumnos_index_10` ON `alumnos` (`genero`);

CREATE UNIQUE INDEX `alumnos_index_11` ON `alumnos` (`matricula`);

CREATE UNIQUE INDEX `parciales_index_12` ON `parciales` (`periodo_id`, `numero`);

CREATE INDEX `parciales_index_13` ON `parciales` (`periodo_id`);

CREATE UNIQUE INDEX `actividades_pat_index_14` ON `actividades_pat` (`parcial_id`, `grupo_id`, `sesion_num`);

CREATE INDEX `actividades_pat_index_15` ON `actividades_pat` (`carrera_id`);

CREATE INDEX `actividades_pat_index_16` ON `actividades_pat` (`grupo_id`);

CREATE UNIQUE INDEX `tutorias_eventos_index_17` ON `tutorias_eventos` (`grupo_id`, `parcial_id`, `sesion_num`, `tipo`);

CREATE INDEX `tutorias_eventos_index_18` ON `tutorias_eventos` (`parcial_id`, `fecha`);

CREATE INDEX `tutorias_eventos_index_19` ON `tutorias_eventos` (`actividad_id`);

CREATE UNIQUE INDEX `catalogos_faltas_index_20` ON `catalogos_faltas` (`tipo`, `clave`);

CREATE UNIQUE INDEX `asistencias_index_21` ON `asistencias` (`evento_id`, `alumno_id`);

CREATE INDEX `asistencias_index_22` ON `asistencias` (`alumno_id`, `evento_id`);

CREATE INDEX `reportes_generados_index_23` ON `reportes_generados` (`tipo`, `grupo_id`, `parcial_id`, `sesion_num`);

CREATE INDEX `clases_index_24` ON `clases` (`asignatura_id`);

CREATE INDEX `clases_index_25` ON `clases` (`periodo_id`);

CREATE INDEX `clases_index_26` ON `clases` (`docente_usuario_id`);

CREATE UNIQUE INDEX `clases_index_27` ON `clases` (`asignatura_id`, `periodo_id`, `seccion`);

CREATE UNIQUE INDEX `inscripciones_index_28` ON `inscripciones` (`alumno_id`, `clase_id`);

CREATE INDEX `inscripciones_index_29` ON `inscripciones` (`clase_id`);

CREATE INDEX `inscripciones_index_30` ON `inscripciones` (`estado`);

CREATE UNIQUE INDEX `alumno_beca_index_31` ON `alumno_beca` (`alumno_id`, `periodo_id`, `beca_id`);

CREATE INDEX `alumno_beca_index_32` ON `alumno_beca` (`periodo_id`);

CREATE UNIQUE INDEX `canalizacion_index_33` ON `canalizacion` (`alumno_id`, `periodo_id`, `area_id`);

CREATE INDEX `canalizacion_index_34` ON `canalizacion` (`periodo_id`);

CREATE UNIQUE INDEX `alumno_riesgo_desercion_index_35` ON `alumno_riesgo_desercion` (`alumno_id`, `periodo_id`);

CREATE INDEX `alumno_riesgo_desercion_index_36` ON `alumno_riesgo_desercion` (`periodo_id`, `nivel`);

ALTER TABLE `grupos` COMMENT = 'Un grupo pertenece a una carrera; puede cambiar de tutor en el tiempo.';

ALTER TABLE `actividades_pat` COMMENT = 'Plan oficial por parcial y sesión (nivel carrera o grupo).';

ALTER TABLE `perfiles_usuario` ADD FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

ALTER TABLE `usuarios` ADD FOREIGN KEY (`nivel_id`) REFERENCES `niveles_usuarios` (`id`);

ALTER TABLE `carreras` ADD FOREIGN KEY (`division_id`) REFERENCES `divisiones` (`id`);

ALTER TABLE `carreras` ADD FOREIGN KEY (`coordinador_usuario_id`) REFERENCES `usuarios` (`id`);

ALTER TABLE `grupos` ADD FOREIGN KEY (`carrera_id`) REFERENCES `carreras` (`id`);

ALTER TABLE `grupos` ADD FOREIGN KEY (`modalidad_id`) REFERENCES `modalidades` (`id`);

ALTER TABLE `grupos` ADD FOREIGN KEY (`tutor_usuario_id`) REFERENCES `usuarios` (`id`);

ALTER TABLE `alumnos` ADD FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

ALTER TABLE `alumnos` ADD FOREIGN KEY (`carrera_id`) REFERENCES `carreras` (`id`);

ALTER TABLE `alumnos` ADD FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id`);

ALTER TABLE `parciales` ADD FOREIGN KEY (`periodo_id`) REFERENCES `periodos_escolares` (`id`);

ALTER TABLE `actividades_pat` ADD FOREIGN KEY (`carrera_id`) REFERENCES `carreras` (`id`);

ALTER TABLE `actividades_pat` ADD FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id`);

ALTER TABLE `actividades_pat` ADD FOREIGN KEY (`parcial_id`) REFERENCES `parciales` (`id`);

ALTER TABLE `tutorias_eventos` ADD FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id`);

ALTER TABLE `tutorias_eventos` ADD FOREIGN KEY (`parcial_id`) REFERENCES `parciales` (`id`);

ALTER TABLE `tutorias_eventos` ADD FOREIGN KEY (`actividad_id`) REFERENCES `actividades_pat` (`id`);

ALTER TABLE `tutorias_eventos` ADD FOREIGN KEY (`evidencia_file_id`) REFERENCES `files` (`id`);

ALTER TABLE `asistencias` ADD FOREIGN KEY (`evento_id`) REFERENCES `tutorias_eventos` (`id`);

ALTER TABLE `asistencias` ADD FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id`);

ALTER TABLE `asistencias` ADD FOREIGN KEY (`razon_inasistencia_id`) REFERENCES `catalogos_faltas` (`id`);

ALTER TABLE `asistencias` ADD FOREIGN KEY (`motivo_id`) REFERENCES `catalogos_faltas` (`id`);

ALTER TABLE `asistencias` ADD FOREIGN KEY (`accion_id`) REFERENCES `catalogos_faltas` (`id`);

ALTER TABLE `asistencias` ADD FOREIGN KEY (`firma_file_id`) REFERENCES `files` (`id`);

ALTER TABLE `reportes_generados` ADD FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id`);

ALTER TABLE `reportes_generados` ADD FOREIGN KEY (`parcial_id`) REFERENCES `parciales` (`id`);

ALTER TABLE `reportes_generados` ADD FOREIGN KEY (`archivo_file_id`) REFERENCES `files` (`id`);

ALTER TABLE `reportes_generados` ADD FOREIGN KEY (`generado_por`) REFERENCES `usuarios` (`id`);

ALTER TABLE `clases` ADD FOREIGN KEY (`asignatura_id`) REFERENCES `asignaturas` (`id`);

ALTER TABLE `clases` ADD FOREIGN KEY (`periodo_id`) REFERENCES `periodos_escolares` (`id`);

ALTER TABLE `clases` ADD FOREIGN KEY (`modalidad_id`) REFERENCES `modalidades` (`id`);

ALTER TABLE `clases` ADD FOREIGN KEY (`grupo_referencia`) REFERENCES `grupos` (`id`);

ALTER TABLE `clases` ADD FOREIGN KEY (`docente_usuario_id`) REFERENCES `usuarios` (`id`);

ALTER TABLE `inscripciones` ADD FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id`);

ALTER TABLE `inscripciones` ADD FOREIGN KEY (`clase_id`) REFERENCES `clases` (`id`);

ALTER TABLE `alumno_beca` ADD FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id`);

ALTER TABLE `alumno_beca` ADD FOREIGN KEY (`periodo_id`) REFERENCES `periodos_escolares` (`id`);

ALTER TABLE `alumno_beca` ADD FOREIGN KEY (`beca_id`) REFERENCES `cat_becas` (`id`);

ALTER TABLE `canalizacion` ADD FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id`);

ALTER TABLE `canalizacion` ADD FOREIGN KEY (`periodo_id`) REFERENCES `periodos_escolares` (`id`);

ALTER TABLE `canalizacion` ADD FOREIGN KEY (`area_id`) REFERENCES `cat_areas_canalizacion` (`id`);

ALTER TABLE `canalizacion` ADD FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

ALTER TABLE `alumno_riesgo_desercion` ADD FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id`);

ALTER TABLE `alumno_riesgo_desercion` ADD FOREIGN KEY (`periodo_id`) REFERENCES `periodos_escolares` (`id`);

ALTER TABLE `grupos` ADD FOREIGN KEY (`nombre`) REFERENCES `alumnos` (`matricula`);
