"""
Cálculo Avanzado de Estadísticas con Inferencias
Basado en Tutorías, Inscripciones y Seguimientos (sin tabla asistencias)
"""
from database import execute_query, execute_one
from typing import Dict, List, Optional
from datetime import datetime, timedelta

def obtener_estadisticas_alumno(alumno_id: int, periodo_id: Optional[int] = None) -> Optional[Dict]:
    """Obtiene todas las estadísticas de un alumno basadas en tutorías, inscripciones y seguimientos"""
    
    # Obtener grupo del alumno
    grupo_query = "SELECT grupos_id_grupo FROM alumnos WHERE id_alumno = %s"
    grupo_result = execute_one(grupo_query, (alumno_id,))
    
    if not grupo_result:
        return None
    
    grupo_id = grupo_result['grupos_id_grupo']
    
    # ============================================
    # ESTADÍSTICAS DE TUTORÍAS GRUPALES
    # ============================================
    # Asistencia a tutorías grupales (últimos 3 meses)
    tutorias_grupales_query = """
        SELECT 
            COUNT(DISTINCT tg.id) as total_tutorias,
            COUNT(DISTINCT CASE WHEN tga.presente = 1 THEN tg.id END) as tutorias_asistidas,
            COUNT(DISTINCT CASE WHEN tga.presente = 0 THEN tg.id END) as tutorias_faltadas
        FROM tutorias_grupales tg
        LEFT JOIN tutorias_grupales_asistencia tga ON tg.id = tga.tutoria_grupal_id AND tga.alumno_id = %s
        WHERE tg.grupo_id = %s 
        AND tg.fecha >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
    """
    tutorias_grupales = execute_one(tutorias_grupales_query, (alumno_id, grupo_id))
    
    total_tutorias_grupales = tutorias_grupales['total_tutorias'] or 0
    tutorias_grupales_asistidas = tutorias_grupales['tutorias_asistidas'] or 0
    tutorias_grupales_faltadas = tutorias_grupales['tutorias_faltadas'] or 0
    
    # Calcular porcentaje de asistencia a tutorías grupales
    asistencia_tutorias_grupales = (tutorias_grupales_asistidas / total_tutorias_grupales * 100) if total_tutorias_grupales > 0 else 0
    
    # Faltas consecutivas en tutorías grupales (últimas 5 tutorías)
    faltas_consecutivas_tutorias_query = """
        SELECT tg.fecha, COALESCE(tga.presente, 0) as presente
        FROM tutorias_grupales tg
        LEFT JOIN tutorias_grupales_asistencia tga ON tg.id = tga.tutoria_grupal_id AND tga.alumno_id = %s
        WHERE tg.grupo_id = %s
        ORDER BY tg.fecha DESC
        LIMIT 5
    """
    faltas_tutorias = execute_query(faltas_consecutivas_tutorias_query, (alumno_id, grupo_id))
    faltas_consecutivas_tutorias = 0
    for record in faltas_tutorias:
        if record.get('presente', 0) == 0:
            faltas_consecutivas_tutorias += 1
        else:
            break
    
    # ============================================
    # ESTADÍSTICAS DE TUTORÍAS INDIVIDUALES
    # ============================================
    tutorias_individuales_query = """
        SELECT COUNT(*) as total
        FROM tutorias_individuales
        WHERE alumno_id = %s 
        AND grupo_id = %s
        AND fecha >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
    """
    tutorias_individuales = execute_one(tutorias_individuales_query, (alumno_id, grupo_id))
    tutorias_individuales_recientes = tutorias_individuales['total'] or 0
    
    # ============================================
    # ESTADÍSTICAS DE CALIFICACIONES
    # Basado en estados de inscripciones y parciales
    # ============================================
    if periodo_id:
        calificaciones_query = """
            SELECT 
                estado,
                estado_parcial1,
                estado_parcial2,
                estado_parcial3,
                estado_parcial4,
                cal_final
            FROM inscripciones i
            INNER JOIN clases c ON i.clase_id = c.id
            WHERE i.alumno_id = %s AND c.periodo_id = %s
        """
        inscripciones = execute_query(calificaciones_query, (alumno_id, periodo_id))
    else:
        calificaciones_query = """
            SELECT 
                estado,
                estado_parcial1,
                estado_parcial2,
                estado_parcial3,
                estado_parcial4,
                cal_final
            FROM inscripciones
            WHERE alumno_id = %s
        """
        inscripciones = execute_query(calificaciones_query, (alumno_id,))
    
    # Contar materias por estado
    materias_aprobadas = 0
    materias_reprobadas = 0
    materias_cursando = 0
    materias_baja = 0
    total_materias = len(inscripciones)
    
    # Calcular calificación promedio basada en estados
    # Si tiene cal_final, usarlo; si no, estimar basado en parciales
    suma_calificaciones = 0
    materias_con_calificacion = 0
    
    for inscripcion in inscripciones:
        estado = inscripcion.get('estado', 'CURSANDO')
        
        # Contar por estado final
        if estado == 'APROBADO':
            materias_aprobadas += 1
        elif estado == 'REPROBADO':
            materias_reprobadas += 1
        elif estado == 'CURSANDO':
            materias_cursando += 1
        elif estado == 'BAJA':
            materias_baja += 1
        
        # Calcular calificación para esta materia
        calificacion_materia = None
        
        # Si tiene cal_final, usarlo
        if inscripcion.get('cal_final') is not None:
            calificacion_materia = float(inscripcion['cal_final'])
        else:
            # Estimar basado en parciales
            parciales_aprobados = 0
            parciales_reprobados = 0
            parciales_cursando = 0
            
            for parcial in ['estado_parcial1', 'estado_parcial2', 'estado_parcial3', 'estado_parcial4']:
                estado_parcial = inscripcion.get(parcial, 'CURSANDO')
                if estado_parcial == 'APROBADO':
                    parciales_aprobados += 1
                elif estado_parcial == 'REPROBADO':
                    parciales_reprobados += 1
                else:
                    parciales_cursando += 1
            
            # Calcular calificación estimada basada en parciales
            total_parciales = parciales_aprobados + parciales_reprobados + parciales_cursando
            if total_parciales > 0:
                # Si todos los parciales están aprobados: 9.0
                # Si la mayoría están aprobados: 7.5
                # Si están balanceados: 6.5
                # Si la mayoría están reprobados: 5.0
                if parciales_reprobados == 0 and parciales_aprobados > 0:
                    calificacion_materia = 9.0
                elif parciales_aprobados > parciales_reprobados:
                    calificacion_materia = 7.5
                elif parciales_aprobados == parciales_reprobados and parciales_aprobados > 0:
                    calificacion_materia = 6.5
                elif parciales_reprobados > parciales_aprobados:
                    calificacion_materia = 5.0
                else:
                    # Solo cursando o sin datos
                    if estado == 'APROBADO':
                        calificacion_materia = 7.0
                    elif estado == 'REPROBADO':
                        calificacion_materia = 5.0
                    else:
                        calificacion_materia = 6.0  # Neutral para cursando
            else:
                # Sin parciales, usar estado final
                if estado == 'APROBADO':
                    calificacion_materia = 7.0
                elif estado == 'REPROBADO':
                    calificacion_materia = 5.0
                else:
                    calificacion_materia = 6.0  # Neutral para cursando
        
        if calificacion_materia is not None:
            suma_calificaciones += calificacion_materia
            materias_con_calificacion += 1
    
    # Calcular promedio
    if materias_con_calificacion > 0:
        calificacion_promedio = suma_calificaciones / materias_con_calificacion
    else:
        # Si no hay calificaciones, estimar basado en estados
        if materias_aprobadas > 0 or materias_reprobadas > 0:
            # Calcular promedio basado en proporción
            total_finalizadas = materias_aprobadas + materias_reprobadas
            if total_finalizadas > 0:
                porcentaje_aprobadas = (materias_aprobadas / total_finalizadas) * 100
                # Mapear porcentaje a calificación (0% = 5.0, 100% = 9.0)
                calificacion_promedio = 5.0 + (porcentaje_aprobadas / 100) * 4.0
            else:
                calificacion_promedio = 6.0  # Neutral
        else:
            calificacion_promedio = 6.0  # Neutral si solo está cursando
    
    # ============================================
    # ESTADÍSTICAS DE SEGUIMIENTOS
    # ============================================
    seguimientos_query = """
        SELECT 
            SUM(CASE WHEN estatus = 1 THEN 1 ELSE 0 END) as abiertos,
            SUM(CASE WHEN estatus = 2 THEN 1 ELSE 0 END) as en_progreso,
            SUM(CASE WHEN estatus = 3 THEN 1 ELSE 0 END) as cerrados,
            COUNT(*) as total
        FROM seguimientos
        WHERE alumnos_id_alumno = %s
    """
    seguimientos = execute_one(seguimientos_query, (alumno_id,))
    seguimientos_abiertos = seguimientos['abiertos'] or 0
    seguimientos_en_progreso = seguimientos['en_progreso'] or 0
    seguimientos_cerrados = seguimientos['cerrados'] or 0
    seguimientos_total = seguimientos['total'] or 0
    
    # Seguimientos cerrados recientemente (últimos 3 meses)
    seguimientos_cerrados_recientes_query = """
        SELECT COUNT(*) as cerrados_recientes
        FROM seguimientos
        WHERE alumnos_id_alumno = %s 
        AND estatus = 3 
        AND fecha_movimiento >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
    """
    seguimientos_cerrados_recientes = execute_one(seguimientos_cerrados_recientes_query, (alumno_id,))
    seguimientos_cerrados_recientes_count = seguimientos_cerrados_recientes['cerrados_recientes'] or 0
    
    # ============================================
    # TENDENCIAS
    # ============================================
    # Tendencia de asistencia a tutorías (últimas 2 semanas vs anteriores)
    tendencia_tutorias = calcular_tendencia_tutorias(alumno_id, grupo_id)
    
    # Tendencia de calificaciones
    tendencia_calificacion = calcular_tendencia_calificacion(alumno_id, periodo_id)
    
    # ============================================
    # MÉTRICAS COMPUESTAS
    # ============================================
    # Participación general (basada en tutorías y seguimientos)
    participacion_general = calcular_participacion_general(
        asistencia_tutorias_grupales,
        tutorias_individuales_recientes,
        seguimientos_abiertos,
        seguimientos_cerrados_recientes_count
    )
    
    # Compromiso académico (basado en calificaciones y materias)
    compromiso_academico = calcular_compromiso_academico(
        calificacion_promedio,
        materias_reprobadas,
        materias_aprobadas,
        materias_cursando,
        total_materias
    )
    
    # ============================================
    # COMPARACIÓN CON GRUPO
    # ============================================
    promedio_grupo = obtener_promedio_grupo(grupo_id, periodo_id)
    
    return {
        'alumno_id': alumno_id,
        'grupo_id': grupo_id,
        
        # Tutorías grupales
        'asistencia_tutorias_grupales': round(asistencia_tutorias_grupales, 2),
        'total_tutorias_grupales': total_tutorias_grupales,
        'tutorias_grupales_asistidas': tutorias_grupales_asistidas,
        'tutorias_grupales_faltadas': tutorias_grupales_faltadas,
        'faltas_consecutivas_tutorias': faltas_consecutivas_tutorias,
        
        # Tutorías individuales
        'tutorias_individuales_recientes': tutorias_individuales_recientes,
        
        # Calificaciones
        'calificacion_promedio': round(calificacion_promedio, 2),
        'materias_reprobadas': materias_reprobadas,
        'materias_aprobadas': materias_aprobadas,
        'materias_cursando': materias_cursando,
        'materias_baja': materias_baja,
        'total_materias': total_materias,
        
        # Seguimientos
        'seguimientos_abiertos': seguimientos_abiertos,
        'seguimientos_en_progreso': seguimientos_en_progreso,
        'seguimientos_cerrados': seguimientos_cerrados,
        'seguimientos_total': seguimientos_total,
        'seguimientos_cerrados_recientes': seguimientos_cerrados_recientes_count,
        
        # Tendencias
        'tendencia_tutorias': round(tendencia_tutorias, 2),
        'tendencia_calificacion': round(tendencia_calificacion, 2),
        
        # Métricas compuestas
        'participacion_general': round(participacion_general, 2),
        'compromiso_academico': round(compromiso_academico, 2),
        
        # Comparación con grupo
        'promedio_grupo': promedio_grupo,
        
        # Compatibilidad con código anterior (usando tutorías como proxy de asistencia)
        'asistencia_promedio': round(asistencia_tutorias_grupales, 2),  # Proxy: asistencia a tutorías
        'total_clases': total_tutorias_grupales,  # Proxy: total de tutorías
        'asistencias': tutorias_grupales_asistidas,  # Proxy: tutorías asistidas
        'faltas': tutorias_grupales_faltadas,  # Proxy: tutorías faltadas
        'faltas_consecutivas': faltas_consecutivas_tutorias,  # Faltas consecutivas en tutorías
        'asistencia_tutorias': round(asistencia_tutorias_grupales, 2),
        'tutorias_disponibles': total_tutorias_grupales,
        'tutorias_asistidas': tutorias_grupales_asistidas,
        'tendencia_asistencia': round(tendencia_tutorias, 2)
    }

def calcular_tendencia_tutorias(alumno_id: int, grupo_id: int) -> float:
    """Calcula la tendencia de asistencia a tutorías (últimas 2 semanas vs anteriores)"""
    try:
        fecha_limite = (datetime.now() - timedelta(days=14)).date()
        
        # Últimas 2 semanas
        query_reciente = """
            SELECT 
                COUNT(DISTINCT tg.id) as total,
                COUNT(DISTINCT CASE WHEN tga.presente = 1 THEN tg.id END) as asistidas
            FROM tutorias_grupales tg
            LEFT JOIN tutorias_grupales_asistencia tga ON tg.id = tga.tutoria_grupal_id AND tga.alumno_id = %s
            WHERE tg.grupo_id = %s AND tg.fecha >= %s
        """
        reciente = execute_one(query_reciente, (alumno_id, grupo_id, fecha_limite))
        
        # Anteriores
        query_anterior = """
            SELECT 
                COUNT(DISTINCT tg.id) as total,
                COUNT(DISTINCT CASE WHEN tga.presente = 1 THEN tg.id END) as asistidas
            FROM tutorias_grupales tg
            LEFT JOIN tutorias_grupales_asistencia tga ON tg.id = tga.tutoria_grupal_id AND tga.alumno_id = %s
            WHERE tg.grupo_id = %s AND tg.fecha < %s
        """
        anterior = execute_one(query_anterior, (alumno_id, grupo_id, fecha_limite))
        
        total_reciente = reciente['total'] or 0
        asistidas_reciente = reciente['asistidas'] or 0
        total_anterior = anterior['total'] or 0
        asistidas_anterior = anterior['asistidas'] or 0
        
        porc_reciente = (asistidas_reciente / total_reciente * 100) if total_reciente > 0 else 0
        porc_anterior = (asistidas_anterior / total_anterior * 100) if total_anterior > 0 else 0
        
        return porc_reciente - porc_anterior
    except:
        return 0.0

def calcular_tendencia_calificacion(alumno_id: int, periodo_id: Optional[int]) -> float:
    """Calcula la tendencia de calificaciones"""
    try:
        if periodo_id:
            # Comparar parciales si están disponibles
            query = """
                SELECT 
                    AVG(CASE WHEN estado_parcial1 = 'APROBADO' THEN 7.0 
                             WHEN estado_parcial1 = 'REPROBADO' THEN 5.0 
                             ELSE 6.0 END) as parcial1,
                    AVG(CASE WHEN estado_parcial2 = 'APROBADO' THEN 7.0 
                             WHEN estado_parcial2 = 'REPROBADO' THEN 5.0 
                             ELSE 6.0 END) as parcial2
                FROM inscripciones i
                INNER JOIN clases c ON i.clase_id = c.id
                WHERE i.alumno_id = %s AND c.periodo_id = %s
            """
            resultado = execute_one(query, (alumno_id, periodo_id))
            parcial1 = float(resultado.get('parcial1') or 0)
            parcial2 = float(resultado.get('parcial2') or 0)
            return parcial2 - parcial1
        return 0.0
    except:
        return 0.0

def calcular_participacion_general(asistencia_tutorias: float, tutorias_individuales: int, 
                                   seguimientos_abiertos: int, seguimientos_cerrados: int) -> float:
    """Calcula un índice de participación general (0-100)"""
    # Base: asistencia a tutorías (peso 50%)
    base = asistencia_tutorias * 0.5
    
    # Ajuste por tutorías individuales (peso 20%)
    # Más tutorías individuales = mejor participación
    ajuste_individual = min(20, tutorias_individuales * 5)  # Máximo 20 puntos
    
    # Ajuste por seguimientos (peso 30%)
    # Seguimientos cerrados recientes = buena participación
    # Seguimientos abiertos = puede indicar problemas
    if seguimientos_cerrados > 0 and seguimientos_abiertos == 0:
        ajuste_seguimientos = 30  # Excelente: problemas resueltos
    elif seguimientos_cerrados > seguimientos_abiertos:
        ajuste_seguimientos = 20  # Bueno: resolviendo problemas
    elif seguimientos_abiertos > 0:
        ajuste_seguimientos = 10  # Regular: problemas pendientes
    else:
        ajuste_seguimientos = 15  # Neutral: sin seguimientos
    
    return min(100, base + ajuste_individual + ajuste_seguimientos)

def calcular_compromiso_academico(calificacion_promedio: float, materias_reprobadas: int, 
                                  materias_aprobadas: int, materias_cursando: int,
                                  total_materias: int) -> float:
    """Calcula un índice de compromiso académico (0-100)"""
    # Base: calificación promedio (peso 50%)
    base = (calificacion_promedio / 10.0) * 50
    
    # Ajuste por proporción de materias aprobadas vs reprobadas (peso 30%)
    if total_materias > 0:
        materias_finalizadas = materias_aprobadas + materias_reprobadas
        if materias_finalizadas > 0:
            porcentaje_aprobadas = (materias_aprobadas / materias_finalizadas) * 30
        else:
            porcentaje_aprobadas = 15  # Solo cursando = compromiso medio
    else:
        porcentaje_aprobadas = 15  # Sin materias = compromiso medio
    
    # Ajuste por materias cursando (peso 20%)
    # Más materias cursando = más compromiso
    if total_materias > 0:
        porcentaje_cursando = (materias_cursando / total_materias) * 20
    else:
        porcentaje_cursando = 10
    
    return min(100, base + porcentaje_aprobadas + porcentaje_cursando)

def obtener_promedio_grupo(grupo_id: int, periodo_id: Optional[int] = None) -> Dict:
    """Obtiene promedios del grupo para comparación"""
    try:
        # Promedio de asistencia a tutorías del grupo
        query_tutorias = """
            SELECT 
                COUNT(DISTINCT tg.id) as total_tutorias,
                AVG(participacion_alumno) as promedio_asistencia
            FROM tutorias_grupales tg
            LEFT JOIN (
                SELECT 
                    tga.tutoria_grupal_id,
                    a.grupos_id_grupo,
                    (COUNT(CASE WHEN tga.presente = 1 THEN 1 END) * 100.0 / COUNT(*)) as participacion_alumno
                FROM tutorias_grupales_asistencia tga
                INNER JOIN alumnos a ON tga.alumno_id = a.id_alumno
                WHERE a.grupos_id_grupo = %s
                GROUP BY tga.tutoria_grupal_id, a.grupos_id_grupo
            ) participacion ON tg.id = participacion.tutoria_grupal_id
            WHERE tg.grupo_id = %s
            AND tg.fecha >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
        """
        resultado_tutorias = execute_one(query_tutorias, (grupo_id, grupo_id))
        
        # Promedio de calificaciones del grupo (basado en estados)
        if periodo_id:
            query_calificaciones = """
                SELECT 
                    estado,
                    estado_parcial1,
                    estado_parcial2,
                    estado_parcial3,
                    estado_parcial4,
                    cal_final
                FROM inscripciones i
                INNER JOIN clases c ON i.clase_id = c.id
                INNER JOIN alumnos a ON i.alumno_id = a.id_alumno
                WHERE a.grupos_id_grupo = %s AND c.periodo_id = %s
            """
            inscripciones_grupo = execute_query(query_calificaciones, (grupo_id, periodo_id))
        else:
            query_calificaciones = """
                SELECT 
                    estado,
                    estado_parcial1,
                    estado_parcial2,
                    estado_parcial3,
                    estado_parcial4,
                    cal_final
                FROM inscripciones i
                INNER JOIN alumnos a ON i.alumno_id = a.id_alumno
                WHERE a.grupos_id_grupo = %s
            """
            inscripciones_grupo = execute_query(query_calificaciones, (grupo_id,))
        
        # Calcular promedio del grupo usando la misma lógica
        suma_calificaciones_grupo = 0
        materias_con_calificacion_grupo = 0
        
        for inscripcion in inscripciones_grupo:
            calificacion_materia = None
            
            if inscripcion.get('cal_final') is not None:
                calificacion_materia = float(inscripcion['cal_final'])
            else:
                # Estimar basado en parciales (misma lógica que arriba)
                estado = inscripcion.get('estado', 'CURSANDO')
                parciales_aprobados = sum(1 for p in ['estado_parcial1', 'estado_parcial2', 'estado_parcial3', 'estado_parcial4'] 
                                         if inscripcion.get(p) == 'APROBADO')
                parciales_reprobados = sum(1 for p in ['estado_parcial1', 'estado_parcial2', 'estado_parcial3', 'estado_parcial4'] 
                                          if inscripcion.get(p) == 'REPROBADO')
                
                if parciales_reprobados == 0 and parciales_aprobados > 0:
                    calificacion_materia = 9.0
                elif parciales_aprobados > parciales_reprobados:
                    calificacion_materia = 7.5
                elif parciales_aprobados == parciales_reprobados and parciales_aprobados > 0:
                    calificacion_materia = 6.5
                elif parciales_reprobados > parciales_aprobados:
                    calificacion_materia = 5.0
                else:
                    if estado == 'APROBADO':
                        calificacion_materia = 7.0
                    elif estado == 'REPROBADO':
                        calificacion_materia = 5.0
                    else:
                        calificacion_materia = 6.0
            
            if calificacion_materia is not None:
                suma_calificaciones_grupo += calificacion_materia
                materias_con_calificacion_grupo += 1
        
        if materias_con_calificacion_grupo > 0:
            promedio_calificacion_grupo = suma_calificaciones_grupo / materias_con_calificacion_grupo
        else:
            promedio_calificacion_grupo = 6.0
        
        promedio_asistencia_grupo = float(resultado_tutorias.get('promedio_asistencia') or 0)
        
        return {
            'asistencia_promedio_grupo': round(promedio_asistencia_grupo, 2),
            'calificacion_promedio_grupo': round(promedio_calificacion_grupo, 2)
        }
    except:
        return {
            'asistencia_promedio_grupo': 0,
            'calificacion_promedio_grupo': 0
        }

def obtener_estadisticas_grupo(grupo_id: int, periodo_id: Optional[int] = None) -> Dict:
    """Obtiene estadísticas agregadas del grupo"""
    try:
        # Estadísticas de tutorías del grupo
        query_tutorias = """
            SELECT 
                COUNT(DISTINCT tg.id) as total_tutorias,
                AVG(participacion) as promedio_asistencia
            FROM tutorias_grupales tg
            LEFT JOIN (
                SELECT 
                    tga.tutoria_grupal_id,
                    (COUNT(CASE WHEN tga.presente = 1 THEN 1 END) * 100.0 / COUNT(*)) as participacion
                FROM tutorias_grupales_asistencia tga
                INNER JOIN alumnos a ON tga.alumno_id = a.id_alumno
                WHERE a.grupos_id_grupo = %s
                GROUP BY tga.tutoria_grupal_id
            ) stats ON tg.id = stats.tutoria_grupal_id
            WHERE tg.grupo_id = %s
        """
        resultado_tutorias = execute_one(query_tutorias, (grupo_id, grupo_id))
        
        # Estadísticas de calificaciones del grupo (basado en estados)
        if periodo_id:
            query_calificaciones = """
                SELECT 
                    estado,
                    estado_parcial1,
                    estado_parcial2,
                    estado_parcial3,
                    estado_parcial4,
                    cal_final
                FROM inscripciones i
                INNER JOIN clases c ON i.clase_id = c.id
                INNER JOIN alumnos a ON i.alumno_id = a.id_alumno
                WHERE a.grupos_id_grupo = %s AND c.periodo_id = %s
            """
            inscripciones_grupo = execute_query(query_calificaciones, (grupo_id, periodo_id))
        else:
            query_calificaciones = """
                SELECT 
                    estado,
                    estado_parcial1,
                    estado_parcial2,
                    estado_parcial3,
                    estado_parcial4,
                    cal_final
                FROM inscripciones i
                INNER JOIN alumnos a ON i.alumno_id = a.id_alumno
                WHERE a.grupos_id_grupo = %s
            """
            inscripciones_grupo = execute_query(query_calificaciones, (grupo_id,))
        
        # Calcular estadísticas del grupo
        suma_calificaciones_grupo = 0
        materias_con_calificacion_grupo = 0
        total_reprobadas_grupo = 0
        total_inscripciones_grupo = len(inscripciones_grupo)
        
        for inscripcion in inscripciones_grupo:
            if inscripcion.get('estado') == 'REPROBADO':
                total_reprobadas_grupo += 1
            
            calificacion_materia = None
            if inscripcion.get('cal_final') is not None:
                calificacion_materia = float(inscripcion['cal_final'])
            else:
                # Estimar basado en parciales
                estado = inscripcion.get('estado', 'CURSANDO')
                parciales_aprobados = sum(1 for p in ['estado_parcial1', 'estado_parcial2', 'estado_parcial3', 'estado_parcial4'] 
                                         if inscripcion.get(p) == 'APROBADO')
                parciales_reprobados = sum(1 for p in ['estado_parcial1', 'estado_parcial2', 'estado_parcial3', 'estado_parcial4'] 
                                          if inscripcion.get(p) == 'REPROBADO')
                
                if parciales_reprobados == 0 and parciales_aprobados > 0:
                    calificacion_materia = 9.0
                elif parciales_aprobados > parciales_reprobados:
                    calificacion_materia = 7.5
                elif parciales_aprobados == parciales_reprobados and parciales_aprobados > 0:
                    calificacion_materia = 6.5
                elif parciales_reprobados > parciales_aprobados:
                    calificacion_materia = 5.0
                else:
                    if estado == 'APROBADO':
                        calificacion_materia = 7.0
                    elif estado == 'REPROBADO':
                        calificacion_materia = 5.0
                    else:
                        calificacion_materia = 6.0
            
            if calificacion_materia is not None:
                suma_calificaciones_grupo += calificacion_materia
                materias_con_calificacion_grupo += 1
        
        if materias_con_calificacion_grupo > 0:
            promedio_calificacion_grupo = suma_calificaciones_grupo / materias_con_calificacion_grupo
        else:
            promedio_calificacion_grupo = 6.0
        
        promedio_reprobadas_grupo = (total_reprobadas_grupo / total_inscripciones_grupo) if total_inscripciones_grupo > 0 else 0
        
        return {
            'asistencia_promedio_grupo': round(float(resultado_tutorias.get('promedio_asistencia') or 0), 2),
            'calificacion_promedio_grupo': round(promedio_calificacion_grupo, 2),
            'materias_reprobadas_promedio': round(promedio_reprobadas_grupo, 2)
        }
    except Exception as e:
        return {
            'asistencia_promedio_grupo': 0,
            'calificacion_promedio_grupo': 0,
            'materias_reprobadas_promedio': 0
        }

def obtener_estadisticas_comparativas(alumno_id: int, grupo_id: int, periodo_id: Optional[int] = None) -> Dict:
    """Obtiene estadísticas del alumno comparadas con su grupo"""
    stats_alumno = obtener_estadisticas_alumno(alumno_id, periodo_id)
    stats_grupo = obtener_estadisticas_grupo(grupo_id, periodo_id)
    
    if not stats_alumno:
        return None
    
    return {
        'alumno': stats_alumno,
        'grupo': stats_grupo,
        'comparacion': {
            'asistencia_diferencia': round(stats_alumno['asistencia_tutorias_grupales'] - stats_grupo['asistencia_promedio_grupo'], 2),
            'calificacion_diferencia': round(stats_alumno['calificacion_promedio'] - stats_grupo['calificacion_promedio_grupo'], 2),
            'por_encima_promedio': stats_alumno['asistencia_tutorias_grupales'] > stats_grupo['asistencia_promedio_grupo'] and 
                                   stats_alumno['calificacion_promedio'] > stats_grupo['calificacion_promedio_grupo']
        }
    }
