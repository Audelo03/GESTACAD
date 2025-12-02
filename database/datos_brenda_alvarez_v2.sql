-- =====================================================
-- Script SQL para agregar datos de prueba
-- Alumna: Brenda Álvarez Cortés
-- Matrícula: ISC2025024
-- VERSIÓN 2: Sin tabla asistencias, solo tutorías
-- =====================================================

USE gestacadv2;

-- Variables para facilitar el mantenimiento
SET @matricula = 'ISC2025024';
SET @nombre = 'Brenda';
SET @apellido_paterno = 'Álvarez';
SET @apellido_materno = 'Cortés';
SET @carrera_id = 1; -- Ingeniería en Sistemas Computacionales
SET @grupo_id = 1; -- ISC-5A (ajusta según tu necesidad)
SET @periodo_id = 3; -- Septiembre - Diciembre 2025
SET @parcial_id = 1; -- Parcial 1
SET @usuario_id = 1; -- Usuario que realiza el movimiento

-- =====================================================
-- 1. CREAR O ACTUALIZAR ALUMNO
-- =====================================================

-- Verificar si el alumno ya existe
SET @alumno_id = (SELECT id_alumno FROM alumnos WHERE matricula = @matricula LIMIT 1);

-- Si no existe, crearlo
INSERT INTO alumnos (
    matricula, 
    nombre, 
    apellido_paterno, 
    apellido_materno, 
    estatus, 
    fecha_creacion, 
    fecha_movimiento, 
    usuarios_id_usuario_movimiento, 
    carreras_id_carrera, 
    grupos_id_grupo,
    genero
)
SELECT 
    @matricula,
    @nombre,
    @apellido_paterno,
    @apellido_materno,
    1, -- Activo
    CURDATE(),
    NOW(),
    @usuario_id,
    @carrera_id,
    @grupo_id,
    'M'
WHERE NOT EXISTS (SELECT 1 FROM alumnos WHERE matricula = @matricula);

-- Obtener el ID del alumno (ya existía o se acaba de crear)
SET @alumno_id = (SELECT id_alumno FROM alumnos WHERE matricula = @matricula LIMIT 1);

SELECT CONCAT('Alumno ID: ', @alumno_id) AS 'Estado';

-- =====================================================
-- 2. AGREGAR TUTORÍAS GRUPALES (con baja asistencia)
-- =====================================================

-- Crear tutorías grupales para el grupo
-- Tutoría 1: Hace 8 semanas - ASISTIÓ
INSERT INTO tutorias_grupales (
    grupo_id,
    parcial_id,
    fecha,
    actividad_nombre,
    actividad_descripcion,
    evidencia_foto_id,
    usuario_id
)
SELECT 
    @grupo_id,
    @parcial_id,
    DATE_SUB(CURDATE(), INTERVAL 56 DAY),
    'Bienvenida e Inducción',
    'Presentación del reglamento y servicios escolares',
    NULL,
    (SELECT usuarios_id_usuario_tutor FROM grupos WHERE id_grupo = @grupo_id)
WHERE NOT EXISTS (
    SELECT 1 FROM tutorias_grupales 
    WHERE grupo_id = @grupo_id 
    AND parcial_id = @parcial_id 
    AND fecha = DATE_SUB(CURDATE(), INTERVAL 56 DAY)
);

SET @tutoria_id_1 = COALESCE(LAST_INSERT_ID(), (SELECT id FROM tutorias_grupales WHERE grupo_id = @grupo_id AND fecha = DATE_SUB(CURDATE(), INTERVAL 56 DAY) LIMIT 1));

-- Marcar asistencia: PRESENTE
INSERT INTO tutorias_grupales_asistencia (tutoria_grupal_id, alumno_id, presente)
VALUES (@tutoria_id_1, @alumno_id, 1)
ON DUPLICATE KEY UPDATE presente = 1;

-- Tutoría 2: Hace 7 semanas - ASISTIÓ
INSERT INTO tutorias_grupales (grupo_id, parcial_id, fecha, actividad_nombre, actividad_descripcion, evidencia_foto_id, usuario_id)
SELECT @grupo_id, @parcial_id, DATE_SUB(CURDATE(), INTERVAL 49 DAY), 'Técnicas de Estudio', 'Taller sobre métodos de estudio efectivos', NULL, (SELECT usuarios_id_usuario_tutor FROM grupos WHERE id_grupo = @grupo_id)
WHERE NOT EXISTS (SELECT 1 FROM tutorias_grupales WHERE grupo_id = @grupo_id AND parcial_id = @parcial_id AND fecha = DATE_SUB(CURDATE(), INTERVAL 49 DAY));

SET @tutoria_id_2 = COALESCE(LAST_INSERT_ID(), (SELECT id FROM tutorias_grupales WHERE grupo_id = @grupo_id AND fecha = DATE_SUB(CURDATE(), INTERVAL 49 DAY) LIMIT 1));
INSERT INTO tutorias_grupales_asistencia (tutoria_grupal_id, alumno_id, presente) VALUES (@tutoria_id_2, @alumno_id, 1) ON DUPLICATE KEY UPDATE presente = 1;

-- Tutoría 3: Hace 6 semanas - FALTÓ
INSERT INTO tutorias_grupales (grupo_id, parcial_id, fecha, actividad_nombre, actividad_descripcion, evidencia_foto_id, usuario_id)
SELECT @grupo_id, @parcial_id, DATE_SUB(CURDATE(), INTERVAL 42 DAY), 'Manejo del Tiempo', 'Taller sobre organización y gestión del tiempo', NULL, (SELECT usuarios_id_usuario_tutor FROM grupos WHERE id_grupo = @grupo_id)
WHERE NOT EXISTS (SELECT 1 FROM tutorias_grupales WHERE grupo_id = @grupo_id AND parcial_id = @parcial_id AND fecha = DATE_SUB(CURDATE(), INTERVAL 42 DAY));

SET @tutoria_id_3 = COALESCE(LAST_INSERT_ID(), (SELECT id FROM tutorias_grupales WHERE grupo_id = @grupo_id AND fecha = DATE_SUB(CURDATE(), INTERVAL 42 DAY) LIMIT 1));
INSERT INTO tutorias_grupales_asistencia (tutoria_grupal_id, alumno_id, presente) VALUES (@tutoria_id_3, @alumno_id, 0) ON DUPLICATE KEY UPDATE presente = 0;

-- Tutoría 4: Hace 5 semanas - FALTÓ
INSERT INTO tutorias_grupales (grupo_id, parcial_id, fecha, actividad_nombre, actividad_descripcion, evidencia_foto_id, usuario_id)
SELECT @grupo_id, @parcial_id, DATE_SUB(CURDATE(), INTERVAL 35 DAY), 'Motivación Académica', 'Taller sobre motivación y metas académicas', NULL, (SELECT usuarios_id_usuario_tutor FROM grupos WHERE id_grupo = @grupo_id)
WHERE NOT EXISTS (SELECT 1 FROM tutorias_grupales WHERE grupo_id = @grupo_id AND parcial_id = @parcial_id AND fecha = DATE_SUB(CURDATE(), INTERVAL 35 DAY));

SET @tutoria_id_4 = COALESCE(LAST_INSERT_ID(), (SELECT id FROM tutorias_grupales WHERE grupo_id = @grupo_id AND fecha = DATE_SUB(CURDATE(), INTERVAL 35 DAY) LIMIT 1));
INSERT INTO tutorias_grupales_asistencia (tutoria_grupal_id, alumno_id, presente) VALUES (@tutoria_id_4, @alumno_id, 0) ON DUPLICATE KEY UPDATE presente = 0;

-- Tutoría 5: Hace 4 semanas - FALTÓ (3 faltas consecutivas)
INSERT INTO tutorias_grupales (grupo_id, parcial_id, fecha, actividad_nombre, actividad_descripcion, evidencia_foto_id, usuario_id)
SELECT @grupo_id, @parcial_id, DATE_SUB(CURDATE(), INTERVAL 28 DAY), 'Estrategias de Aprendizaje', 'Taller sobre diferentes estilos de aprendizaje', NULL, (SELECT usuarios_id_usuario_tutor FROM grupos WHERE id_grupo = @grupo_id)
WHERE NOT EXISTS (SELECT 1 FROM tutorias_grupales WHERE grupo_id = @grupo_id AND parcial_id = @parcial_id AND fecha = DATE_SUB(CURDATE(), INTERVAL 28 DAY));

SET @tutoria_id_5 = COALESCE(LAST_INSERT_ID(), (SELECT id FROM tutorias_grupales WHERE grupo_id = @grupo_id AND fecha = DATE_SUB(CURDATE(), INTERVAL 28 DAY) LIMIT 1));
INSERT INTO tutorias_grupales_asistencia (tutoria_grupal_id, alumno_id, presente) VALUES (@tutoria_id_5, @alumno_id, 0) ON DUPLICATE KEY UPDATE presente = 0;

-- Tutoría 6: Hace 3 semanas - ASISTIÓ
INSERT INTO tutorias_grupales (grupo_id, parcial_id, fecha, actividad_nombre, actividad_descripcion, evidencia_foto_id, usuario_id)
SELECT @grupo_id, @parcial_id, DATE_SUB(CURDATE(), INTERVAL 21 DAY), 'Comunicación Efectiva', 'Taller sobre comunicación y trabajo en equipo', NULL, (SELECT usuarios_id_usuario_tutor FROM grupos WHERE id_grupo = @grupo_id)
WHERE NOT EXISTS (SELECT 1 FROM tutorias_grupales WHERE grupo_id = @grupo_id AND parcial_id = @parcial_id AND fecha = DATE_SUB(CURDATE(), INTERVAL 21 DAY));

SET @tutoria_id_6 = COALESCE(LAST_INSERT_ID(), (SELECT id FROM tutorias_grupales WHERE grupo_id = @grupo_id AND fecha = DATE_SUB(CURDATE(), INTERVAL 21 DAY) LIMIT 1));
INSERT INTO tutorias_grupales_asistencia (tutoria_grupal_id, alumno_id, presente) VALUES (@tutoria_id_6, @alumno_id, 1) ON DUPLICATE KEY UPDATE presente = 1;

-- Tutoría 7: Hace 2 semanas - FALTÓ
INSERT INTO tutorias_grupales (grupo_id, parcial_id, fecha, actividad_nombre, actividad_descripcion, evidencia_foto_id, usuario_id)
SELECT @grupo_id, @parcial_id, DATE_SUB(CURDATE(), INTERVAL 14 DAY), 'Planificación de Proyectos', 'Taller sobre planificación y organización de proyectos', NULL, (SELECT usuarios_id_usuario_tutor FROM grupos WHERE id_grupo = @grupo_id)
WHERE NOT EXISTS (SELECT 1 FROM tutorias_grupales WHERE grupo_id = @grupo_id AND parcial_id = @parcial_id AND fecha = DATE_SUB(CURDATE(), INTERVAL 14 DAY));

SET @tutoria_id_7 = COALESCE(LAST_INSERT_ID(), (SELECT id FROM tutorias_grupales WHERE grupo_id = @grupo_id AND fecha = DATE_SUB(CURDATE(), INTERVAL 14 DAY) LIMIT 1));
INSERT INTO tutorias_grupales_asistencia (tutoria_grupal_id, alumno_id, presente) VALUES (@tutoria_id_7, @alumno_id, 0) ON DUPLICATE KEY UPDATE presente = 0;

-- Tutoría 8: Hace 1 semana - FALTÓ (2 faltas consecutivas recientes)
INSERT INTO tutorias_grupales (grupo_id, parcial_id, fecha, actividad_nombre, actividad_descripcion, evidencia_foto_id, usuario_id)
SELECT @grupo_id, @parcial_id, DATE_SUB(CURDATE(), INTERVAL 7 DAY), 'Prevención de Deserción', 'Taller sobre factores de riesgo y prevención', NULL, (SELECT usuarios_id_usuario_tutor FROM grupos WHERE id_grupo = @grupo_id)
WHERE NOT EXISTS (SELECT 1 FROM tutorias_grupales WHERE grupo_id = @grupo_id AND parcial_id = @parcial_id AND fecha = DATE_SUB(CURDATE(), INTERVAL 7 DAY));

SET @tutoria_id_8 = COALESCE(LAST_INSERT_ID(), (SELECT id FROM tutorias_grupales WHERE grupo_id = @grupo_id AND fecha = DATE_SUB(CURDATE(), INTERVAL 7 DAY) LIMIT 1));
INSERT INTO tutorias_grupales_asistencia (tutoria_grupal_id, alumno_id, presente) VALUES (@tutoria_id_8, @alumno_id, 0) ON DUPLICATE KEY UPDATE presente = 0;

-- Total: 8 tutorías, 3 asistidas, 5 faltadas = 37.5% de asistencia
-- Faltas consecutivas: 3 (tutorías 3, 4, 5) y 2 recientes (tutorías 7, 8)

SELECT CONCAT('Tutorías grupales agregadas para alumno ID: ', @alumno_id) AS 'Estado';

-- =====================================================
-- 3. AGREGAR INSCRIPCIONES CON CALIFICACIONES
-- =====================================================

-- Obtener IDs de clases existentes
SET @clase_id_1 = (SELECT id FROM clases WHERE periodo_id = @periodo_id LIMIT 1);
SET @clase_id_2 = (SELECT id FROM clases WHERE periodo_id = @periodo_id AND id != @clase_id_1 LIMIT 1);
SET @clase_id_1 = COALESCE(@clase_id_1, 1);
SET @clase_id_2 = COALESCE(@clase_id_2, 2);

-- Inscripción 1: Materia con calificación baja (reprobada)
INSERT INTO inscripciones (alumno_id, clase_id, cal_final, estado, estado_parcial1, estado_parcial2, estado_parcial3, estado_parcial4, fecha_alta)
VALUES (@alumno_id, @clase_id_1, 5.2, 'REPROBADO', 'REPROBADO', 'CURSANDO', 'CURSANDO', 'CURSANDO', DATE_SUB(CURDATE(), INTERVAL 60 DAY))
ON DUPLICATE KEY UPDATE cal_final = VALUES(cal_final), estado = VALUES(estado);

-- Inscripción 2: Materia con calificación regular (aprobada pero baja)
INSERT INTO inscripciones (alumno_id, clase_id, cal_final, estado, estado_parcial1, estado_parcial2, estado_parcial3, estado_parcial4, fecha_alta)
VALUES (@alumno_id, @clase_id_2, 6.5, 'APROBADO', 'APROBADO', 'CURSANDO', 'CURSANDO', 'CURSANDO', DATE_SUB(CURDATE(), INTERVAL 60 DAY))
ON DUPLICATE KEY UPDATE cal_final = VALUES(cal_final), estado = VALUES(estado);

-- Promedio: ~5.85, 1 materia reprobada

SELECT CONCAT('Inscripciones agregadas para alumno ID: ', @alumno_id) AS 'Estado';

-- =====================================================
-- 4. AGREGAR SEGUIMIENTOS (algunos abiertos)
-- =====================================================

-- Seguimiento 1: Abierto (estatus = 1)
INSERT INTO seguimientos (descripcion, estatus, fecha_creacion, fecha_movimiento, fecha_compromiso, usuarios_id_usuario_movimiento, alumnos_id_alumno, tutor_id, tipo_seguimiento_id)
VALUES (
    'Baja participación en tutorías detectada. Se requiere seguimiento académico para identificar causas y establecer plan de mejora.',
    1, -- Abierto
    DATE_SUB(CURDATE(), INTERVAL 15 DAY),
    DATE_SUB(CURDATE(), INTERVAL 15 DAY),
    DATE_ADD(CURDATE(), INTERVAL 7 DAY),
    @usuario_id,
    @alumno_id,
    (SELECT usuarios_id_usuario_tutor FROM grupos WHERE id_grupo = @grupo_id),
    1 -- Académico
);

-- Seguimiento 2: Abierto (estatus = 1)
INSERT INTO seguimientos (descripcion, estatus, fecha_creacion, fecha_movimiento, fecha_compromiso, usuarios_id_usuario_movimiento, alumnos_id_alumno, tutor_id, tipo_seguimiento_id)
VALUES (
    'Materia reprobada en primer parcial. Necesita apoyo en técnicas de estudio y asesorías.',
    1, -- Abierto
    DATE_SUB(CURDATE(), INTERVAL 10 DAY),
    DATE_SUB(CURDATE(), INTERVAL 10 DAY),
    DATE_ADD(CURDATE(), INTERVAL 5 DAY),
    @usuario_id,
    @alumno_id,
    (SELECT usuarios_id_usuario_tutor FROM grupos WHERE id_grupo = @grupo_id),
    1 -- Académico
);

-- Seguimiento 3: Cerrado recientemente (estatus = 3)
INSERT INTO seguimientos (descripcion, estatus, fecha_creacion, fecha_movimiento, fecha_compromiso, usuarios_id_usuario_movimiento, alumnos_id_alumno, tutor_id, tipo_seguimiento_id)
VALUES (
    'Seguimiento de bienvenida completado. Alumna informada sobre servicios y programas de apoyo.',
    3, -- Cerrado
    DATE_SUB(CURDATE(), INTERVAL 45 DAY),
    DATE_SUB(CURDATE(), INTERVAL 30 DAY),
    DATE_SUB(CURDATE(), INTERVAL 30 DAY),
    @usuario_id,
    @alumno_id,
    (SELECT usuarios_id_usuario_tutor FROM grupos WHERE id_grupo = @grupo_id),
    1 -- Académico
);

-- Total: 2 seguimientos abiertos, 1 cerrado

SELECT CONCAT('Seguimientos agregados para alumno ID: ', @alumno_id) AS 'Estado';

-- =====================================================
-- 5. AGREGAR TUTORÍAS INDIVIDUALES
-- =====================================================

-- Tutoría individual reciente
INSERT INTO tutorias_individuales (alumno_id, grupo_id, parcial_id, fecha, motivo, acciones, usuario_id)
VALUES (
    @alumno_id,
    @grupo_id,
    @parcial_id,
    DATE_SUB(CURDATE(), INTERVAL 5 DAY),
    'Baja participación en tutorías grupales y calificaciones. Revisar situación personal y académica.',
    'Se acordó plan de estudio personalizado y seguimiento semanal. Alumna comprometida a mejorar participación.',
    (SELECT usuarios_id_usuario_tutor FROM grupos WHERE id_grupo = @grupo_id)
);

SELECT CONCAT('Tutoría individual agregada para alumno ID: ', @alumno_id) AS 'Estado';

-- =====================================================
-- RESUMEN DE DATOS AGREGADOS
-- =====================================================

SELECT 
    'RESUMEN DE DATOS PARA BRENDA ÁLVAREZ CORTÉS' AS 'Título',
    @alumno_id AS 'ID Alumno',
    @matricula AS 'Matrícula',
    CONCAT(@nombre, ' ', @apellido_paterno, ' ', @apellido_materno) AS 'Nombre Completo';

SELECT 
    'TUTORÍAS GRUPALES' AS 'Tipo',
    COUNT(DISTINCT tg.id) AS 'Total',
    SUM(CASE WHEN tga.presente = 1 THEN 1 ELSE 0 END) AS 'Asistidas',
    SUM(CASE WHEN tga.presente = 0 THEN 1 ELSE 0 END) AS 'Faltadas',
    ROUND(SUM(CASE WHEN tga.presente = 1 THEN 1 ELSE 0 END) * 100.0 / COUNT(DISTINCT tg.id), 2) AS 'Porcentaje Asistencia'
FROM tutorias_grupales tg
LEFT JOIN tutorias_grupales_asistencia tga ON tg.id = tga.tutoria_grupal_id AND tga.alumno_id = @alumno_id
WHERE tg.grupo_id = @grupo_id AND tg.fecha >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH);

SELECT 
    'INSCRIPCIONES' AS 'Tipo',
    COUNT(*) AS 'Total',
    SUM(CASE WHEN estado = 'REPROBADO' THEN 1 ELSE 0 END) AS 'Reprobadas',
    SUM(CASE WHEN estado = 'APROBADO' THEN 1 ELSE 0 END) AS 'Aprobadas',
    ROUND(AVG(cal_final), 2) AS 'Promedio Calificaciones'
FROM inscripciones
WHERE alumno_id = @alumno_id;

SELECT 
    'SEGUIMIENTOS' AS 'Tipo',
    COUNT(*) AS 'Total',
    SUM(CASE WHEN estatus = 1 THEN 1 ELSE 0 END) AS 'Abiertos',
    SUM(CASE WHEN estatus = 3 THEN 1 ELSE 0 END) AS 'Cerrados'
FROM seguimientos
WHERE alumnos_id_alumno = @alumno_id;

SELECT 
    'TUTORÍAS INDIVIDUALES' AS 'Tipo',
    COUNT(*) AS 'Total'
FROM tutorias_individuales
WHERE alumno_id = @alumno_id;

-- =====================================================
-- FIN DEL SCRIPT
-- =====================================================

SELECT 'Script ejecutado correctamente. Los datos están listos para el análisis de inferencias.' AS 'Mensaje Final';



