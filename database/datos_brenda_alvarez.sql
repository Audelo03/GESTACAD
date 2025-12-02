-- =====================================================
-- Script SQL para agregar datos de prueba
-- Alumna: Brenda Álvarez Cortés
-- Matrícula: ISC2025024
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
-- 2. AGREGAR ASISTENCIAS (con faltas para generar riesgo)
-- =====================================================

-- Eliminar asistencias existentes del alumno (opcional, comenta si quieres conservar)
-- DELETE FROM asistencias WHERE id_alumno = @alumno_id;

-- Generar asistencias para las últimas 8 semanas (40 días de clase)
-- Asistencia del 60% (24 presentes, 16 faltas) con algunas faltas consecutivas
-- Esto generará un riesgo MEDIO-ALTO

INSERT INTO asistencias (id_alumno, id_grupo, fecha, estatus, fecha_registro)
VALUES
-- Semana 1 (5 días) - 3 presentes, 2 faltas
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 56 DAY), 1, CURDATE()),
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 55 DAY), 1, CURDATE()),
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 54 DAY), 0, CURDATE()), -- Falta
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 53 DAY), 1, CURDATE()),
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 52 DAY), 0, CURDATE()), -- Falta

-- Semana 2 (5 días) - 4 presentes, 1 falta
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 49 DAY), 1, CURDATE()),
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 48 DAY), 1, CURDATE()),
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 47 DAY), 1, CURDATE()),
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 46 DAY), 0, CURDATE()), -- Falta
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 45 DAY), 1, CURDATE()),

-- Semana 3 (5 días) - 2 presentes, 3 faltas (empeorando)
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 42 DAY), 1, CURDATE()),
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 41 DAY), 0, CURDATE()), -- Falta
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 40 DAY), 0, CURDATE()), -- Falta
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 39 DAY), 0, CURDATE()), -- Falta (3 consecutivas)
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 38 DAY), 1, CURDATE()),

-- Semana 4 (5 días) - 3 presentes, 2 faltas
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 35 DAY), 1, CURDATE()),
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 34 DAY), 0, CURDATE()), -- Falta
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 33 DAY), 1, CURDATE()),
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 32 DAY), 1, CURDATE()),
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 31 DAY), 0, CURDATE()), -- Falta

-- Semana 5 (5 días) - 2 presentes, 3 faltas (más faltas consecutivas)
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 28 DAY), 1, CURDATE()),
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 27 DAY), 0, CURDATE()), -- Falta
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 26 DAY), 0, CURDATE()), -- Falta
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 25 DAY), 0, CURDATE()), -- Falta
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 24 DAY), 0, CURDATE()), -- Falta (4 consecutivas)

-- Semana 6 (5 días) - 3 presentes, 2 faltas
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 21 DAY), 1, CURDATE()),
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 20 DAY), 1, CURDATE()),
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 19 DAY), 0, CURDATE()), -- Falta
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 18 DAY), 1, CURDATE()),
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 17 DAY), 0, CURDATE()), -- Falta

-- Semana 7 (5 días) - 4 presentes, 1 falta
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 14 DAY), 1, CURDATE()),
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 13 DAY), 1, CURDATE()),
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 12 DAY), 1, CURDATE()),
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 11 DAY), 0, CURDATE()), -- Falta
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 10 DAY), 1, CURDATE()),

-- Semana 8 (5 días) - 3 presentes, 2 faltas
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 7 DAY), 1, CURDATE()),
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 6 DAY), 0, CURDATE()), -- Falta
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 5 DAY), 0, CURDATE()), -- Falta (2 consecutivas recientes)
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 4 DAY), 1, CURDATE()),
(@alumno_id, @grupo_id, DATE_SUB(CURDATE(), INTERVAL 3 DAY), 1, CURDATE())

ON DUPLICATE KEY UPDATE estatus = VALUES(estatus);

-- Total: 24 presentes, 16 faltas = 60% de asistencia
-- Faltas consecutivas: 3, 4, y 2 (recientes)

SELECT CONCAT('Asistencias agregadas para alumno ID: ', @alumno_id) AS 'Estado';

-- =====================================================
-- 3. AGREGAR INSCRIPCIONES CON CALIFICACIONES
-- =====================================================

-- Obtener IDs de clases existentes (ajusta según tus datos)
SET @clase_id_1 = (SELECT id FROM clases WHERE periodo_id = @periodo_id LIMIT 1);
SET @clase_id_2 = (SELECT id FROM clases WHERE periodo_id = @periodo_id AND id != @clase_id_1 LIMIT 1);

-- Si no hay clases, crear una clase de ejemplo
-- INSERT INTO clases (asignatura_id, periodo_id, docente_usuario_id, seccion, modalidad_id, cupo, grupo_referencia, aula, activo)
-- VALUES (2, @periodo_id, 10, 'A', 1, 30, @grupo_id, 'A-101', 1)
-- ON DUPLICATE KEY UPDATE activo = 1;

-- Si no hay clases, usar las existentes o crear una
SET @clase_id_1 = COALESCE(@clase_id_1, 1);
SET @clase_id_2 = COALESCE(@clase_id_2, 2);

-- Inscripción 1: Materia con calificación baja (reprobada)
INSERT INTO inscripciones (
    alumno_id, 
    clase_id, 
    cal_final, 
    estado, 
    estado_parcial1, 
    estado_parcial2, 
    estado_parcial3, 
    estado_parcial4, 
    fecha_alta
)
VALUES (
    @alumno_id,
    @clase_id_1,
    5.2, -- Calificación final baja
    'REPROBADO',
    'REPROBADO', -- Primer parcial reprobado
    'CURSANDO',
    'CURSANDO',
    'CURSANDO',
    DATE_SUB(CURDATE(), INTERVAL 60 DAY)
)
ON DUPLICATE KEY UPDATE 
    cal_final = VALUES(cal_final),
    estado = VALUES(estado);

-- Inscripción 2: Materia con calificación regular (aprobada pero baja)
INSERT INTO inscripciones (
    alumno_id, 
    clase_id, 
    cal_final, 
    estado, 
    estado_parcial1, 
    estado_parcial2, 
    estado_parcial3, 
    estado_parcial4, 
    fecha_alta
)
VALUES (
    @alumno_id,
    @clase_id_2,
    6.5, -- Calificación regular
    'APROBADO',
    'APROBADO',
    'CURSANDO',
    'CURSANDO',
    'CURSANDO',
    DATE_SUB(CURDATE(), INTERVAL 60 DAY)
)
ON DUPLICATE KEY UPDATE 
    cal_final = VALUES(cal_final),
    estado = VALUES(estado);

-- Si hay más clases, agregar más inscripciones con calificaciones variadas
-- Esto generará: 1 materia reprobada, promedio de ~5.85

SELECT CONCAT('Inscripciones agregadas para alumno ID: ', @alumno_id) AS 'Estado';

-- =====================================================
-- 4. AGREGAR SEGUIMIENTOS (algunos abiertos)
-- =====================================================

-- Seguimiento 1: Abierto (estatus = 1)
INSERT INTO seguimientos (
    descripcion,
    estatus,
    fecha_creacion,
    fecha_movimiento,
    fecha_compromiso,
    usuarios_id_usuario_movimiento,
    alumnos_id_alumno,
    tutor_id,
    tipo_seguimiento_id
)
VALUES (
    'Baja asistencia detectada. Se requiere seguimiento académico para identificar causas y establecer plan de mejora.',
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
INSERT INTO seguimientos (
    descripcion,
    estatus,
    fecha_creacion,
    fecha_movimiento,
    fecha_compromiso,
    usuarios_id_usuario_movimiento,
    alumnos_id_alumno,
    tutor_id,
    tipo_seguimiento_id
)
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
INSERT INTO seguimientos (
    descripcion,
    estatus,
    fecha_creacion,
    fecha_movimiento,
    fecha_compromiso,
    usuarios_id_usuario_movimiento,
    alumnos_id_alumno,
    tutor_id,
    tipo_seguimiento_id
)
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
-- 5. AGREGAR TUTORÍAS GRUPALES (con asistencia baja)
-- =====================================================

-- Obtener ID de tutoría grupal existente o crear una
SET @tutoria_grupal_id = (SELECT id FROM tutorias_grupales WHERE grupo_id = @grupo_id AND parcial_id = @parcial_id LIMIT 1);

-- Si no existe, crear una tutoría grupal
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
    DATE_SUB(CURDATE(), INTERVAL 20 DAY),
    'Técnicas de Estudio',
    'Taller sobre métodos de estudio efectivos',
    NULL,
    (SELECT usuarios_id_usuario_tutor FROM grupos WHERE id_grupo = @grupo_id)
WHERE NOT EXISTS (
    SELECT 1 FROM tutorias_grupales 
    WHERE grupo_id = @grupo_id 
    AND parcial_id = @parcial_id 
    AND fecha = DATE_SUB(CURDATE(), INTERVAL 20 DAY)
);

SET @tutoria_grupal_id = COALESCE(@tutoria_grupal_id, LAST_INSERT_ID());

-- Marcar asistencia a tutoría (presente = 1)
INSERT INTO tutorias_grupales_asistencia (
    tutoria_grupal_id,
    alumno_id,
    presente
)
VALUES (
    @tutoria_grupal_id,
    @alumno_id,
    1 -- Presente
)
ON DUPLICATE KEY UPDATE presente = 1;

-- Crear otra tutoría a la que NO asistió
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
    DATE_SUB(CURDATE(), INTERVAL 10 DAY),
    'Manejo del Tiempo',
    'Taller sobre organización y gestión del tiempo',
    NULL,
    (SELECT usuarios_id_usuario_tutor FROM grupos WHERE id_grupo = @grupo_id)
WHERE NOT EXISTS (
    SELECT 1 FROM tutorias_grupales 
    WHERE grupo_id = @grupo_id 
    AND parcial_id = @parcial_id 
    AND fecha = DATE_SUB(CURDATE(), INTERVAL 10 DAY)
);

SET @tutoria_grupal_id_2 = COALESCE(LAST_INSERT_ID(), (SELECT id FROM tutorias_grupales WHERE grupo_id = @grupo_id AND fecha = DATE_SUB(CURDATE(), INTERVAL 10 DAY) LIMIT 1));

-- NO marcar asistencia (faltó)
-- Esto generará baja asistencia a tutorías

SELECT CONCAT('Tutorías grupales configuradas para alumno ID: ', @alumno_id) AS 'Estado';

-- =====================================================
-- 6. AGREGAR TUTORÍAS INDIVIDUALES
-- =====================================================

-- Tutoría individual reciente
INSERT INTO tutorias_individuales (
    alumno_id,
    grupo_id,
    fecha,
    motivo,
    acciones,
    usuario_id
)
VALUES (
    @alumno_id,
    @grupo_id,
    DATE_SUB(CURDATE(), INTERVAL 5 DAY),
    'Baja asistencia y calificaciones. Revisar situación personal y académica.',
    'Se acordó plan de estudio personalizado y seguimiento semanal. Alumna comprometida a mejorar asistencia.',
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
    'ASISTENCIAS' AS 'Tipo',
    COUNT(*) AS 'Total',
    SUM(CASE WHEN estatus = 1 THEN 1 ELSE 0 END) AS 'Presentes',
    SUM(CASE WHEN estatus = 0 THEN 1 ELSE 0 END) AS 'Faltas',
    ROUND(SUM(CASE WHEN estatus = 1 THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 2) AS 'Porcentaje Asistencia'
FROM asistencias
WHERE id_alumno = @alumno_id;

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
    'TUTORÍAS GRUPALES' AS 'Tipo',
    COUNT(DISTINCT tga.tutoria_grupal_id) AS 'Total Disponibles',
    SUM(CASE WHEN tga.presente = 1 THEN 1 ELSE 0 END) AS 'Asistidas'
FROM tutorias_grupales tg
LEFT JOIN tutorias_grupales_asistencia tga ON tg.id = tga.tutoria_grupal_id AND tga.alumno_id = @alumno_id
WHERE tg.grupo_id = @grupo_id AND tg.parcial_id = @parcial_id;

SELECT 
    'TUTORÍAS INDIVIDUALES' AS 'Tipo',
    COUNT(*) AS 'Total'
FROM tutorias_individuales
WHERE alumno_id = @alumno_id;

-- =====================================================
-- FIN DEL SCRIPT
-- =====================================================

SELECT 'Script ejecutado correctamente. Los datos están listos para el análisis de inferencias.' AS 'Mensaje Final';



