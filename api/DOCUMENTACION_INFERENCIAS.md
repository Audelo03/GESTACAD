# Documentación del Sistema de Inferencias

## 📚 Fundamentos Teóricos

Este sistema implementa un **motor de inferencias basado en reglas** que utiliza conceptos de **Programación Lógica** y **Programación Funcional** para evaluar el riesgo de deserción estudiantil.

## 🧠 Arquitectura del Sistema

### 1. Estructura de Reglas

Cada regla está definida por:

```python
Rule(
    name: str,                    # Identificador único
    priority: int,                # Prioridad (mayor = más importante)
    conditions: List[callable],   # Lista de condiciones (funciones puras)
    conclusion: callable,         # Función que genera la conclusión
    description: str,             # Descripción humana
    score_impact: int            # Impacto en el score (0-100)
)
```

### 2. Proceso de Inferencia

```
1. Recolección de Hechos (Estadísticas)
   ↓
2. Evaluación de Condiciones
   ↓
3. Aplicación de Reglas (por prioridad)
   ↓
4. Generación de Conclusiones
   ↓
5. Cálculo de Score Final
   ↓
6. Generación de Recomendaciones
```

## 🔍 Ejemplo de Regla

```python
Rule(
    name="riesgo_alto_multiple_factores",
    priority=9,
    conditions=[
        lambda s: s.get('asistencia_promedio', 100) < 70,      # Condición 1
        lambda s: s.get('calificacion_promedio', 10) < 6.0,    # Condición 2
        lambda s: s.get('seguimientos_abiertos', 0) >= 2       # Condición 3
    ],
    conclusion=lambda s: (RiskLevel.ALTO, 80),
    description="Baja asistencia, bajas calificaciones y múltiples seguimientos",
    score_impact=80
)
```

**Interpretación Lógica**:
```
SI (asistencia < 70%) 
Y (calificacion < 6.0) 
Y (seguimientos_abiertos >= 2)
ENTONCES riesgo = ALTO (score = 80)
```

## 📊 Sistema de Prioridades

Las reglas se evalúan en orden de prioridad:

1. **Prioridad 10**: Reglas CRÍTICAS (máxima urgencia)
2. **Prioridad 9**: Reglas de riesgo ALTO
3. **Prioridad 8**: Reglas de riesgo MEDIO-ALTO
4. **Prioridad 7**: Reglas de riesgo MEDIO
5. **Prioridad 6**: Reglas de riesgo BAJO (buen rendimiento)
6. **Prioridad 5**: Reglas de riesgo BAJO (participación)

## 🎯 Cálculo del Score

El score de riesgo (0-100) se calcula de dos formas:

### 1. Por Reglas Aplicadas
- Se toma el score de la regla de mayor prioridad que se cumple
- Si múltiples reglas aplican, se usa la de mayor score

### 2. Score Base (si no aplican reglas)
```python
score_base = 50

# Ajustes:
- Asistencia < 50%: +30
- Asistencia < 70%: +15
- Asistencia >= 90%: -20

- Calificación < 6.0: +25
- Calificación < 7.0: +10
- Calificación >= 9.0: -15

- Materias reprobadas: +10 por cada una
```

### 3. Conversión a Nivel
```
Score 80-100 → CRÍTICO
Score 65-79  → ALTO
Score 40-64  → MEDIO
Score 0-39   → BAJO
```

## 🔄 Forward-Chaining

El sistema implementa **forward-chaining** (encadenamiento hacia adelante):

1. **Hechos iniciales**: Estadísticas del alumno
2. **Evaluación**: Se evalúan todas las reglas
3. **Aplicación**: Se aplican las reglas cuyas condiciones se cumplen
4. **Conclusión**: Se genera el nivel de riesgo y score

### Pseudocódigo

```python
def infer(estadisticas):
    reglas_aplicadas = []
    max_score = 0
    nivel_final = MEDIO
    
    for regla in reglas_ordenadas_por_prioridad:
        if todas_las_condiciones_se_cumplen(regla, estadisticas):
            nivel, score = aplicar_conclusion(regla, estadisticas)
            reglas_aplicadas.append(regla)
            
            if score > max_score:
                max_score = score
                nivel_final = nivel
    
    return {
        'nivel': nivel_final,
        'score': max_score,
        'reglas': reglas_aplicadas
    }
```

## 💡 Conceptos de Programación Funcional

### 1. Funciones Puras

Las condiciones son funciones puras:
- No tienen efectos secundarios
- Mismo input → mismo output
- No dependen de estado externo

```python
# Función pura
condicion_asistencia = lambda s: s.get('asistencia_promedio', 100) < 70
```

### 2. Funciones de Orden Superior

Las reglas almacenan funciones que pueden ser:
- Pasadas como parámetros
- Evaluadas dinámicamente
- Componerse entre sí

```python
# Evaluación dinámica
for condition in rule.conditions:
    if condition(estadisticas):  # Función como parámetro
        # ...
```

### 3. Inmutabilidad

Los hechos (estadísticas) no se modifican durante la inferencia:
- Se crean copias para análisis
- No hay mutación de datos originales

## 📈 Análisis de Tendencias

El sistema calcula tendencias comparando períodos:

### Tendencia de Asistencia
```
tendencia = asistencia_reciente - asistencia_anterior

Si tendencia < -10: Empeorando significativamente
Si tendencia < -5: Empeorando
Si tendencia > 5: Mejorando
```

### Tendencia de Calificaciones
```
tendencia = calificacion_reciente - calificacion_anterior

Si tendencia < -1.0: Empeorando
Si tendencia > 0.5: Mejorando
```

## 🎓 Ventajas del Enfoque

### 1. Declarativo
Las reglas describen **QUÉ** evaluar, no **CÓMO** hacerlo.

### 2. Extensible
Agregar nuevas reglas es simple:
```python
engine.rules.append(Rule(...))
```

### 3. Transparente
Cada inferencia puede ser explicada mostrando qué reglas se aplicaron.

### 4. Modular
Cada regla es independiente y puede modificarse sin afectar otras.

### 5. Funcional
No hay estado mutable, solo transformación de datos.

## 🔬 Ejemplo Completo

### Hechos (Estadísticas)
```python
estadisticas = {
    'asistencia_promedio': 45.5,
    'faltas_consecutivas': 8,
    'calificacion_promedio': 5.2,
    'materias_reprobadas': 3,
    'seguimientos_abiertos': 2,
    'tendencia_asistencia': -15.2
}
```

### Evaluación de Reglas

1. **Regla: riesgo_alto_asistencia_critica**
   - Condición 1: `45.5 < 50` ✅
   - Condición 2: `8 >= 5` ✅
   - **Resultado**: ALTO, Score 85

2. **Regla: riesgo_alto_multiple_factores**
   - Condición 1: `45.5 < 70` ✅
   - Condición 2: `5.2 < 6.0` ✅
   - Condición 3: `2 >= 2` ✅
   - **Resultado**: ALTO, Score 80

3. **Regla: riesgo_alto_tendencia_negativa**
   - Condición 1: `-15.2 < -10` ✅
   - Condición 2: `tendencia_calificacion < -1.0` ❌
   - **Resultado**: No aplica

### Conclusión Final
- **Nivel de Riesgo**: ALTO
- **Score**: 85 (de la regla de mayor score)
- **Regla Aplicada**: `riesgo_alto_asistencia_critica`

## 📚 Referencias Teóricas

- **Programación Lógica**: Basada en lógica de primer orden
- **Sistemas Expertos**: Arquitectura de reglas de producción
- **Forward-Chaining**: Algoritmo de inferencia en sistemas expertos
- **Programación Funcional**: Paradigma basado en funciones puras

---

**Este sistema demuestra la aplicación práctica de conceptos de Programación Lógica y Funcional en un problema del mundo real.**




