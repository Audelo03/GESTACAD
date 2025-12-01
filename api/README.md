# API de Análisis de Riesgos Estudiantiles - Versión Avanzada

API REST desarrollada en Python usando Flask con un **Sistema Avanzado de Inferencias** basado en **Programación Lógica y Funcional** para calcular estadísticas y analizar riesgos de deserción estudiantil.

## 🎯 Características Principales

### Sistema de Inferencias Avanzado
- **15+ reglas de inferencia** con sistema de prioridades
- **4 niveles de riesgo**: CRÍTICO, ALTO, MEDIO, BAJO
- **Score numérico de riesgo** (0-100)
- **Análisis de tendencias** temporales
- **Comparación con grupo** de referencia
- **Recomendaciones automáticas** personalizadas

### Estadísticas Avanzadas
- Estadísticas básicas y comparativas
- Análisis de tendencias (asistencia y calificaciones)
- Estadísticas agregadas por grupo
- Dashboard completo con inferencias

## 🚀 Instalación

### 1. Instalar dependencias

```bash
cd api
pip install -r requirements.txt
```

### 2. Configurar base de datos

Copia `.env.example` a `.env` y ajusta las credenciales:

```bash
cp .env.example .env
```

Edita `.env`:
```
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=tu_password
DB_NAME=gestacadv2
```

### 3. Ejecutar la API

```bash
python app.py
```

La API estará disponible en: `http://localhost:5000`

## 📚 Endpoints Disponibles

### Estadísticas

#### 1. Estadísticas Básicas
```
GET /api/estadisticas/<alumno_id>?periodo_id=<opcional>
```
Retorna estadísticas básicas del alumno.

#### 2. Estadísticas Comparativas
```
GET /api/estadisticas/<alumno_id>/comparativa?periodo_id=<opcional>
```
Retorna estadísticas del alumno comparadas con su grupo.

#### 3. Estadísticas con Inferencias
```
GET /api/estadisticas/<alumno_id>/inferencia?periodo_id=<opcional>
```
Retorna estadísticas completas con análisis de inferencias.

### Análisis de Riesgo

#### 4. Análisis de Riesgo Simple
```
GET /api/riesgo/<alumno_id>?periodo_id=<opcional>
```
Análisis básico de riesgo.

#### 5. Análisis de Riesgo Detallado
```
GET /api/riesgo/<alumno_id>/detallado?periodo_id=<opcional>
```
Análisis completo con todas las inferencias, reglas aplicadas y recomendaciones.

### Análisis de Grupo

#### 6. Riesgo de Grupo
```
GET /api/grupo/<grupo_id>/riesgo?periodo_id=<opcional>
```
Análisis de riesgo de todos los alumnos del grupo.

#### 7. Estadísticas de Grupo
```
GET /api/grupo/<grupo_id>/estadisticas?periodo_id=<opcional>
```
Estadísticas agregadas del grupo.

#### 8. Dashboard Completo
```
GET /api/dashboard/<grupo_id>?periodo_id=<opcional>
```
Dashboard completo con estadísticas e inferencias del grupo.

### Información

#### 9. Reglas de Inferencia
```
GET /api/reglas
```
Lista todas las reglas de inferencia disponibles.

#### 10. Periodos
```
GET /api/periodos
```
Lista de periodos escolares.

#### 11. Grupos
```
GET /api/grupos?carrera_id=<opcional>
```
Lista de grupos disponibles.

## 🧠 Sistema de Inferencias

### Niveles de Riesgo

1. **CRÍTICO** (Score 80-100): Intervención urgente requerida
2. **ALTO** (Score 65-79): Acción inmediata necesaria
3. **MEDIO** (Score 40-64): Monitoreo activo
4. **BAJO** (Score 0-39): Seguimiento preventivo

### Reglas de Inferencia Implementadas

#### Reglas de Riesgo CRÍTICO (Prioridad 10)
- Asistencia extremadamente baja (<30%) con faltas consecutivas críticas
- Múltiples materias reprobadas (≥5) con calificación muy baja

#### Reglas de Riesgo ALTO (Prioridad 9)
- Asistencia crítica (<50%) con faltas consecutivas
- Múltiples factores negativos (asistencia, calificaciones, seguimientos)
- Reprobar múltiples materias (≥3)
- Tendencia negativa en asistencia y calificaciones

#### Reglas de Riesgo MEDIO (Prioridad 7-8)
- Asistencia baja (50-75%) con seguimientos activos
- Calificaciones bajas (6.0-7.0) con materias reprobadas
- Baja participación en tutorías
- Tendencia decreciente en asistencia

#### Reglas de Riesgo BAJO (Prioridad 5-6)
- Excelente rendimiento académico
- Buen rendimiento con participación activa
- Tendencia positiva en rendimiento

### Factores Analizados

- **Asistencia**: Promedio, faltas consecutivas, tendencia
- **Calificaciones**: Promedio, materias reprobadas/aprobadas, tendencia
- **Seguimientos**: Abiertos, en progreso, cerrados
- **Tutorías**: Asistencia, participación
- **Comparación**: Vs. promedio del grupo
- **Tendencias**: Evolución temporal

## 📊 Ejemplo de Respuesta

### Análisis de Riesgo Detallado

```json
{
  "alumno": {
    "id_alumno": 2,
    "matricula": "20250002",
    "nombre": "Maria",
    "apellido_paterno": "López"
  },
  "estadisticas": {
    "asistencia_promedio": 45.5,
    "calificacion_promedio": 5.2,
    "materias_reprobadas": 3,
    "tendencia_asistencia": -15.2
  },
  "analisis_riesgo": {
    "nivel_riesgo": "ALTO",
    "score_riesgo": 80,
    "posible_desercion": true,
    "explicacion": "Nivel de Riesgo Inferido: ALTO\n\nReglas Aplicadas:\n• Baja asistencia, bajas calificaciones y múltiples seguimientos...",
    "reglas_aplicadas": [
      {
        "regla": "riesgo_alto_multiple_factores",
        "descripcion": "Baja asistencia, bajas calificaciones y múltiples seguimientos",
        "nivel": "ALTO",
        "score": 80,
        "prioridad": 9
      }
    ],
    "recomendaciones": [
      "⚠️ RIESGO ALTO - Acción inmediata necesaria",
      "📅 Implementar plan de mejora de asistencia",
      "📚 Asignar tutorías individuales intensivas"
    ]
  }
}
```

## 💡 Conceptos de Programación Lógica Implementados

### 1. Sistema de Reglas (Forward-Chaining)
- Evaluación de condiciones lógicas
- Aplicación de reglas por prioridad
- Inferencia de conclusiones

### 2. Funciones Puras
- Condiciones como funciones puras
- Sin efectos secundarios
- Determinísticas

### 3. Evaluación Lazy
- Reglas evaluadas solo cuando es necesario
- Optimización de rendimiento

### 4. Composición de Funciones
- Reglas compuestas de múltiples condiciones
- Reutilización de lógica

## 🔧 Estructura del Proyecto

```
api/
├── app.py              # API principal con todos los endpoints
├── database.py         # Conexión a base de datos
├── estadisticas.py     # Cálculo avanzado de estadísticas
├── inference.py        # Motor de inferencias avanzado
├── requirements.txt    # Dependencias
├── .env.example       # Ejemplo de configuración
└── README.md          # Este archivo
```

## 📈 Uso Avanzado

### Python (requests)

```python
import requests

BASE_URL = "http://localhost:5000"

# Análisis detallado con inferencias
response = requests.get(f"{BASE_URL}/api/riesgo/2/detallado")
data = response.json()

print(f"Nivel de Riesgo: {data['analisis_riesgo']['nivel_riesgo']}")
print(f"Score: {data['analisis_riesgo']['score_riesgo']}")
print(f"Reglas Aplicadas: {len(data['analisis_riesgo']['reglas_aplicadas'])}")

# Dashboard completo
response = requests.get(f"{BASE_URL}/api/dashboard/1")
dashboard = response.json()
print(f"Total alumnos: {dashboard['total_alumnos']}")
print(f"Riesgo alto: {dashboard['resumen_riesgo']['alto']}")
```

### cURL

```bash
# Análisis detallado
curl http://localhost:5000/api/riesgo/2/detallado

# Dashboard
curl http://localhost:5000/api/dashboard/1?periodo_id=3

# Estadísticas con inferencias
curl http://localhost:5000/api/estadisticas/2/inferencia
```

## 🎓 Para la Materia

Este sistema demuestra:

✅ **Programación Lógica**:
- Sistema de reglas de inferencia
- Forward-chaining
- Evaluación de condiciones lógicas

✅ **Programación Funcional**:
- Funciones puras
- Composición de funciones
- Inmutabilidad de datos

✅ **Análisis de Datos**:
- Estadísticas descriptivas
- Análisis comparativo
- Tendencias temporales

✅ **Sistema de Inferencias**:
- Múltiples reglas con prioridades
- Score numérico de riesgo
- Explicaciones automáticas

## 📝 Notas Técnicas

- **Motor de Inferencias**: Implementa un sistema de reglas con prioridades
- **Score de Riesgo**: Calculado basado en reglas aplicadas (0-100)
- **Tendencias**: Comparación de períodos recientes vs anteriores
- **Comparación**: Análisis relativo al grupo de referencia

---

**Desarrollado para la materia de Programación Lógica y Funcional**
