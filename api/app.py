"""
API REST Avanzada para Análisis de Riesgos Estudiantiles
Sistema de inferencias basado en Programación Lógica y Funcional
"""
from flask import Flask, jsonify, request
from flask_cors import CORS
from database import execute_query, execute_one
from estadisticas import (
    obtener_estadisticas_alumno, 
    obtener_estadisticas_comparativas,
    obtener_estadisticas_grupo
)
from inference import evaluar_riesgo, inference_engine, get_all_rules

app = Flask(__name__)
CORS(app)

@app.route('/')
def index():
    """Endpoint raíz con documentación completa"""
    return jsonify({
        'mensaje': 'API de Análisis de Riesgos Estudiantiles - Sistema Avanzado',
        'version': '2.0.0',
        'descripcion': 'Sistema de inferencias basado en Programación Lógica y Funcional',
        'endpoints': {
            'estadisticas': '/api/estadisticas/<alumno_id>',
            'estadisticas_comparativas': '/api/estadisticas/<alumno_id>/comparativa',
            'estadisticas_con_inferencia': '/api/estadisticas/<alumno_id>/inferencia',
            'riesgo': '/api/riesgo/<alumno_id>',
            'riesgo_detallado': '/api/riesgo/<alumno_id>/detallado',
            'grupo_riesgo': '/api/grupo/<grupo_id>/riesgo',
            'grupo_estadisticas': '/api/grupo/<grupo_id>/estadisticas',
            'dashboard': '/api/dashboard/<grupo_id>',
            'reglas': '/api/reglas',
            'periodos': '/api/periodos',
            'grupos': '/api/grupos'
        }
    })

@app.route('/api/estadisticas/<int:alumno_id>')
def get_estadisticas(alumno_id):
    """Obtiene las estadísticas básicas de un alumno"""
    try:
        periodo_id = request.args.get('periodo_id', type=int)
        estadisticas = obtener_estadisticas_alumno(alumno_id, periodo_id)
        
        if not estadisticas:
            return jsonify({'error': 'Alumno no encontrado'}), 404
        
        return jsonify(estadisticas)
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/estadisticas/<int:alumno_id>/comparativa')
def get_estadisticas_comparativa(alumno_id):
    """Obtiene estadísticas del alumno comparadas con su grupo"""
    try:
        periodo_id = request.args.get('periodo_id', type=int)
        
        # Obtener grupo del alumno
        grupo_query = "SELECT grupos_id_grupo FROM alumnos WHERE id_alumno = %s"
        grupo_result = execute_one(grupo_query, (alumno_id,))
        
        if not grupo_result:
            return jsonify({'error': 'Alumno no encontrado'}), 404
        
        grupo_id = grupo_result['grupos_id_grupo']
        comparativa = obtener_estadisticas_comparativas(alumno_id, grupo_id, periodo_id)
        
        return jsonify(comparativa)
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/estadisticas/<int:alumno_id>/inferencia')
def get_estadisticas_con_inferencia(alumno_id):
    """Obtiene estadísticas con análisis de inferencias"""
    try:
        periodo_id = request.args.get('periodo_id', type=int)
        estadisticas = obtener_estadisticas_alumno(alumno_id, periodo_id)
        
        if not estadisticas:
            return jsonify({'error': 'Alumno no encontrado'}), 404
        
        # Realizar inferencias
        nivel_riesgo, explicacion, score, recomendaciones, reglas = evaluar_riesgo(estadisticas)
        
        return jsonify({
            'alumno_id': alumno_id,
            'estadisticas': estadisticas,
            'inferencias': {
                'nivel_riesgo': nivel_riesgo,
                'score_riesgo': score,
                'explicacion': explicacion,
                'recomendaciones': recomendaciones,
                'reglas_aplicadas': reglas
            },
            'nivel_riesgo': nivel_riesgo,
            'score_riesgo': score,
            'explicacion': explicacion,
            'recomendaciones': recomendaciones
        })
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/riesgo/<int:alumno_id>')
def analizar_riesgo(alumno_id):
    """Analiza el riesgo de deserción de un alumno (versión simple)"""
    try:
        periodo_id = request.args.get('periodo_id', type=int)
        estadisticas = obtener_estadisticas_alumno(alumno_id, periodo_id)
        
        if not estadisticas:
            return jsonify({'error': 'Alumno no encontrado'}), 404
        
        nivel_riesgo, explicacion, score, recomendaciones, reglas = evaluar_riesgo(estadisticas)
        
        return jsonify({
            'alumno_id': alumno_id,
            'nivel_riesgo': nivel_riesgo,
            'score_riesgo': score,
            'explicacion': explicacion,
            'recomendaciones': recomendaciones,
            'reglas_aplicadas': reglas,
            'posible_desercion': nivel_riesgo in ['ALTO', 'CRITICO']
        })
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/riesgo/<int:alumno_id>/detallado')
def analizar_riesgo_detallado(alumno_id):
    """Análisis detallado de riesgo con todas las inferencias"""
    try:
        periodo_id = request.args.get('periodo_id', type=int)
        estadisticas = obtener_estadisticas_alumno(alumno_id, periodo_id)
        
        if not estadisticas:
            return jsonify({'error': 'Alumno no encontrado'}), 404
        
        nivel_riesgo, explicacion, score, recomendaciones, reglas = evaluar_riesgo(estadisticas)
        
        # Obtener información del alumno
        alumno_query = """
            SELECT a.id_alumno, a.matricula, a.nombre, a.apellido_paterno, 
                   a.apellido_materno, g.nombre as grupo, c.nombre as carrera
            FROM alumnos a
            LEFT JOIN grupos g ON a.grupos_id_grupo = g.id_grupo
            LEFT JOIN carreras c ON a.carreras_id_carrera = c.id_carrera
            WHERE a.id_alumno = %s
        """
        alumno_info = execute_one(alumno_query, (alumno_id,))
        
        return jsonify({
            'alumno': alumno_info,
            'estadisticas': estadisticas,
            'analisis_riesgo': {
                'nivel_riesgo': nivel_riesgo,
                'score_riesgo': score,
                'posible_desercion': nivel_riesgo in ['ALTO', 'CRITICO'],
                'explicacion': explicacion,
                'reglas_aplicadas': reglas,
                'recomendaciones': recomendaciones
            }
        })
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/grupo/<int:grupo_id>/riesgo')
def analizar_riesgo_grupo(grupo_id):
    """Analiza los riesgos de todos los alumnos de un grupo"""
    try:
        periodo_id = request.args.get('periodo_id', type=int)
        
        # Obtener alumnos del grupo
        query = """
            SELECT id_alumno, matricula, nombre, apellido_paterno, apellido_materno
            FROM alumnos
            WHERE grupos_id_grupo = %s AND estatus = 1
        """
        alumnos = execute_query(query, (grupo_id,))
        
        if not alumnos:
            return jsonify({'error': 'Grupo no encontrado o sin alumnos'}), 404
        
        resultados = []
        resumen = {'total': 0, 'critico': 0, 'alto': 0, 'medio': 0, 'bajo': 0}
        scores = []
        
        for alumno in alumnos:
            estadisticas = obtener_estadisticas_alumno(alumno['id_alumno'], periodo_id)
            if estadisticas:
                nivel_riesgo, _, score, _, _ = evaluar_riesgo(estadisticas)
                scores.append(score)
                
                resultados.append({
                    'alumno_id': alumno['id_alumno'],
                    'matricula': alumno['matricula'],
                    'nombre': f"{alumno['nombre']} {alumno['apellido_paterno']}",
                    'nivel_riesgo': nivel_riesgo,
                    'score_riesgo': score
                })
                resumen['total'] += 1
                resumen[nivel_riesgo.lower()] = resumen.get(nivel_riesgo.lower(), 0) + 1
        
        # Calcular estadísticas del grupo
        score_promedio = sum(scores) / len(scores) if scores else 50
        
        return jsonify({
            'grupo_id': grupo_id,
            'resumen': resumen,
            'score_promedio_grupo': round(score_promedio, 2),
            'alumnos': resultados
        })
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/grupo/<int:grupo_id>/estadisticas')
def get_estadisticas_grupo(grupo_id):
    """Obtiene estadísticas agregadas del grupo"""
    try:
        periodo_id = request.args.get('periodo_id', type=int)
        estadisticas = obtener_estadisticas_grupo(grupo_id, periodo_id)
        
        # Obtener información del grupo
        grupo_query = """
            SELECT g.id_grupo, g.nombre, c.nombre as carrera, 
                   COUNT(DISTINCT a.id_alumno) as total_alumnos
            FROM grupos g
            LEFT JOIN carreras c ON g.carreras_id_carrera = c.id_carrera
            LEFT JOIN alumnos a ON g.id_grupo = a.grupos_id_grupo AND a.estatus = 1
            WHERE g.id_grupo = %s
            GROUP BY g.id_grupo, g.nombre, c.nombre
        """
        grupo_info = execute_one(grupo_query, (grupo_id,))
        
        return jsonify({
            'grupo': grupo_info,
            'estadisticas': estadisticas
        })
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/dashboard/<int:grupo_id>')
def get_dashboard(grupo_id):
    """Dashboard completo con estadísticas e inferencias del grupo"""
    try:
        periodo_id = request.args.get('periodo_id', type=int)
        
        # Estadísticas del grupo
        stats_grupo = obtener_estadisticas_grupo(grupo_id, periodo_id)
        
        # Análisis de riesgo del grupo
        query_alumnos = """
            SELECT id_alumno, matricula, nombre, apellido_paterno, apellido_materno
            FROM alumnos
            WHERE grupos_id_grupo = %s AND estatus = 1
        """
        alumnos = execute_query(query_alumnos, (grupo_id,))
        
        analisis_alumnos = []
        resumen_riesgo = {'critico': 0, 'alto': 0, 'medio': 0, 'bajo': 0}
        
        for alumno in alumnos:
            stats = obtener_estadisticas_alumno(alumno['id_alumno'], periodo_id)
            if stats:
                nivel, _, score, _, _ = evaluar_riesgo(stats)
                analisis_alumnos.append({
                    'alumno_id': alumno['id_alumno'],
                    'matricula': alumno['matricula'],
                    'nombre': f"{alumno['nombre']} {alumno['apellido_paterno']}",
                    'nivel_riesgo': nivel,
                    'score_riesgo': score,
                    'estadisticas_clave': {
                        'participacion_tutorias': stats.get('asistencia_tutorias_grupales', 0),
                        'calificacion': stats.get('calificacion_promedio', 0),
                        'materias_reprobadas': stats.get('materias_reprobadas', 0),
                        'seguimientos_abiertos': stats.get('seguimientos_abiertos', 0)
                    }
                })
                resumen_riesgo[nivel.lower()] = resumen_riesgo.get(nivel.lower(), 0) + 1
        
        return jsonify({
            'grupo_id': grupo_id,
            'periodo_id': periodo_id,
            'estadisticas_grupo': stats_grupo,
            'resumen_riesgo': resumen_riesgo,
            'total_alumnos': len(analisis_alumnos),
            'alumnos': analisis_alumnos
        })
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/reglas')
def get_reglas():
    """Obtiene información sobre las reglas de inferencia"""
    reglas_info = get_all_rules()
    
    return jsonify({
        'total_reglas': len(reglas_info),
        'reglas': reglas_info
    })

@app.route('/api/periodos')
def get_periodos():
    """Obtiene la lista de periodos escolares"""
    try:
        query = "SELECT id, nombre, fecha_inicio, fecha_fin, activo FROM periodos_escolares ORDER BY fecha_inicio DESC"
        periodos = execute_query(query)
        return jsonify({'periodos': periodos})
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/grupos')
def get_grupos():
    """Obtiene la lista de grupos"""
    try:
        carrera_id = request.args.get('carrera_id', type=int)
        
        if carrera_id:
            query = """
                SELECT g.id_grupo, g.nombre, c.nombre as carrera
                FROM grupos g
                INNER JOIN carreras c ON g.carreras_id_carrera = c.id_carrera
                WHERE g.estatus = 1 AND g.carreras_id_carrera = %s
                ORDER BY g.nombre
            """
            grupos = execute_query(query, (carrera_id,))
        else:
            query = """
                SELECT g.id_grupo, g.nombre, c.nombre as carrera
                FROM grupos g
                INNER JOIN carreras c ON g.carreras_id_carrera = c.id_carrera
                WHERE g.estatus = 1
                ORDER BY g.nombre
            """
            grupos = execute_query(query)
        
        return jsonify({'grupos': grupos})
    except Exception as e:
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    print("=" * 60)
    print("API de Análisis de Riesgos Estudiantiles - Versión Avanzada")
    print("Sistema de Inferencias basado en Programación Lógica")
    print("=" * 60)
    print("Servidor iniciado en http://localhost:5000")
    print("Documentación: http://localhost:5000/")
    print(f"Total de reglas de inferencia: {len(inference_engine.rules)}")
    print("=" * 60)
    app.run(debug=True, host='0.0.0.0', port=5000)
