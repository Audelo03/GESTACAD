# Inferencias en Lenguaje de Predicados
## Sistema de Inferencias - Programación Lógica

Este documento presenta todas las reglas de inferencia del sistema en **lenguaje de predicados** (lógica de primer orden).

---

## Símbolos y Predicados Base

### Predicados Unarios (Propiedades)
- `Alumno(x)` - x es un alumno
- `Activo(x)` - x está activo
- `Reprobado(x, m)` - x reprobó la materia m

### Predicados Binarios (Relaciones)
- `Asistencia(x, p)` - Asistencia promedio del alumno x (porcentaje p)
- `Calificacion(x, c)` - Calificación promedio del alumno x (valor c)
- `FaltasConsecutivas(x, n)` - Alumno x tiene n faltas consecutivas
- `MateriasReprobadas(x, n)` - Alumno x tiene n materias reprobadas
- `SeguimientosAbiertos(x, n)` - Alumno x tiene n seguimientos abiertos
- `TendenciaAsistencia(x, t)` - Tendencia de asistencia del alumno x (variación t)
- `TendenciaCalificacion(x, t)` - Tendencia de calificación del alumno x (variación t)
- `AsistenciaTutorias(x, p)` - Asistencia a tutorías del alumno x (porcentaje p)
- `TutoriasDisponibles(x, n)` - Alumno x tiene n tutorías disponibles

### Predicados de Riesgo
- `RiesgoCritico(x)` - Alumno x tiene riesgo CRÍTICO
- `RiesgoAlto(x)` - Alumno x tiene riesgo ALTO
- `RiesgoMedio(x)` - Alumno x tiene riesgo MEDIO
- `RiesgoBajo(x)` - Alumno x tiene riesgo BAJO
- `ScoreRiesgo(x, s)` - Alumno x tiene score de riesgo s (0-100)

### Operadores Lógicos
- `∧` - Conjunción (Y)
- `∨` - Disyunción (O)
- `→` - Implicación (ENTONCES)
- `¬` - Negación (NO)
- `∀` - Cuantificador universal (PARA TODO)
- `∃` - Cuantificador existencial (EXISTE)

---

## Reglas de Inferencia en Lenguaje de Predicados

### 1. Regla de Riesgo CRÍTICO - Asistencia Extrema

**Formalización:**
```
∀x [Alumno(x) ∧ Asistencia(x, p) ∧ p < 30 ∧ FaltasConsecutivas(x, n) ∧ n ≥ 10 
    → RiesgoCritico(x) ∧ ScoreRiesgo(x, 95)]
```

**En español:**
```
PARA TODO alumno x:
SI (x es alumno) Y (asistencia de x < 30%) Y (faltas consecutivas de x ≥ 10)
ENTONCES (riesgo de x es CRÍTICO) Y (score de riesgo de x = 95)
```

**Prioridad:** 10

---

### 2. Regla de Riesgo CRÍTICO - Múltiples Reprobar

**Formalización:**
```
∀x [Alumno(x) ∧ MateriasReprobadas(x, n) ∧ n ≥ 5 ∧ Calificacion(x, c) ∧ c < 5.0
    → RiesgoCritico(x) ∧ ScoreRiesgo(x, 90)]
```

**En español:**
```
PARA TODO alumno x:
SI (x es alumno) Y (materias reprobadas de x ≥ 5) Y (calificación de x < 5.0)
ENTONCES (riesgo de x es CRÍTICO) Y (score de riesgo de x = 90)
```

**Prioridad:** 10

---

### 3. Regla de Riesgo ALTO - Asistencia Crítica

**Formalización:**
```
∀x [Alumno(x) ∧ Asistencia(x, p) ∧ p < 50 ∧ FaltasConsecutivas(x, n) ∧ n ≥ 5
    → RiesgoAlto(x) ∧ ScoreRiesgo(x, 85)]
```

**En español:**
```
PARA TODO alumno x:
SI (x es alumno) Y (asistencia de x < 50%) Y (faltas consecutivas de x ≥ 5)
ENTONCES (riesgo de x es ALTO) Y (score de riesgo de x = 85)
```

**Prioridad:** 9

---

### 4. Regla de Riesgo ALTO - Múltiples Factores Negativos

**Formalización:**
```
∀x [Alumno(x) ∧ Asistencia(x, p) ∧ p < 70 
    ∧ Calificacion(x, c) ∧ c < 6.0
    ∧ SeguimientosAbiertos(x, n) ∧ n ≥ 2
    → RiesgoAlto(x) ∧ ScoreRiesgo(x, 80)]
```

**En español:**
```
PARA TODO alumno x:
SI (x es alumno) Y (asistencia de x < 70%) 
    Y (calificación de x < 6.0)
    Y (seguimientos abiertos de x ≥ 2)
ENTONCES (riesgo de x es ALTO) Y (score de riesgo de x = 80)
```

**Prioridad:** 9

---

### 5. Regla de Riesgo ALTO - Reprobar Masiva

**Formalización:**
```
∀x [Alumno(x) ∧ MateriasReprobadas(x, n) ∧ n ≥ 3 
    ∧ Calificacion(x, c) ∧ c < 6.5
    → RiesgoAlto(x) ∧ ScoreRiesgo(x, 75)]
```

**En español:**
```
PARA TODO alumno x:
SI (x es alumno) Y (materias reprobadas de x ≥ 3) Y (calificación de x < 6.5)
ENTONCES (riesgo de x es ALTO) Y (score de riesgo de x = 75)
```

**Prioridad:** 9

---

### 6. Regla de Riesgo ALTO - Tendencia Negativa

**Formalización:**
```
∀x [Alumno(x) ∧ TendenciaAsistencia(x, t) ∧ t < -10 
    ∧ TendenciaCalificacion(x, tc) ∧ tc < -1.0
    → RiesgoAlto(x) ∧ ScoreRiesgo(x, 70)]
```

**En español:**
```
PARA TODO alumno x:
SI (x es alumno) Y (tendencia asistencia de x < -10%) 
    Y (tendencia calificación de x < -1.0)
ENTONCES (riesgo de x es ALTO) Y (score de riesgo de x = 70)
```

**Prioridad:** 9

---

### 7. Regla de Riesgo MEDIO - Asistencia Baja

**Formalización:**
```
∀x [Alumno(x) ∧ Asistencia(x, p) ∧ p ≥ 50 ∧ p < 75 
    ∧ SeguimientosAbiertos(x, n) ∧ n ≥ 1
    → RiesgoMedio(x) ∧ ScoreRiesgo(x, 60)]
```

**En español:**
```
PARA TODO alumno x:
SI (x es alumno) Y (asistencia de x entre 50% y 75%) 
    Y (seguimientos abiertos de x ≥ 1)
ENTONCES (riesgo de x es MEDIO) Y (score de riesgo de x = 60)
```

**Prioridad:** 8

---

### 8. Regla de Riesgo MEDIO - Calificaciones Bajas

**Formalización:**
```
∀x [Alumno(x) ∧ Calificacion(x, c) ∧ c ≥ 6.0 ∧ c < 7.0 
    ∧ MateriasReprobadas(x, n) ∧ n ≥ 1
    → RiesgoMedio(x) ∧ ScoreRiesgo(x, 55)]
```

**En español:**
```
PARA TODO alumno x:
SI (x es alumno) Y (calificación de x entre 6.0 y 7.0) 
    Y (materias reprobadas de x ≥ 1)
ENTONCES (riesgo de x es MEDIO) Y (score de riesgo de x = 55)
```

**Prioridad:** 8

---

### 9. Regla de Riesgo MEDIO - Sin Tutorías

**Formalización:**
```
∀x [Alumno(x) ∧ AsistenciaTutorias(x, p) ∧ p < 50 
    ∧ TutoriasDisponibles(x, n) ∧ n ≥ 3
    ∧ Asistencia(x, pa) ∧ pa < 80
    → RiesgoMedio(x) ∧ ScoreRiesgo(x, 50)]
```

**En español:**
```
PARA TODO alumno x:
SI (x es alumno) Y (asistencia a tutorías de x < 50%) 
    Y (tutorías disponibles para x ≥ 3)
    Y (asistencia general de x < 80%)
ENTONCES (riesgo de x es MEDIO) Y (score de riesgo de x = 50)
```

**Prioridad:** 7

---

### 10. Regla de Riesgo MEDIO - Tendencia Decreciente

**Formalización:**
```
∀x [Alumno(x) ∧ TendenciaAsistencia(x, t) ∧ t < -5 
    ∧ Asistencia(x, p) ∧ p < 80
    → RiesgoMedio(x) ∧ ScoreRiesgo(x, 45)]
```

**En español:**
```
PARA TODO alumno x:
SI (x es alumno) Y (tendencia asistencia de x < -5%) 
    Y (asistencia de x < 80%)
ENTONCES (riesgo de x es MEDIO) Y (score de riesgo de x = 45)
```

**Prioridad:** 7

---

### 11. Regla de Riesgo BAJO - Excelente Rendimiento

**Formalización:**
```
∀x [Alumno(x) ∧ Asistencia(x, p) ∧ p ≥ 90 
    ∧ Calificacion(x, c) ∧ c ≥ 9.0
    ∧ MateriasReprobadas(x, n) ∧ n = 0
    → RiesgoBajo(x) ∧ ScoreRiesgo(x, 10)]
```

**En español:**
```
PARA TODO alumno x:
SI (x es alumno) Y (asistencia de x ≥ 90%) 
    Y (calificación de x ≥ 9.0)
    Y (materias reprobadas de x = 0)
ENTONCES (riesgo de x es BAJO) Y (score de riesgo de x = 10)
```

**Prioridad:** 6

---

### 12. Regla de Riesgo BAJO - Buen Rendimiento

**Formalización:**
```
∀x [Alumno(x) ∧ Asistencia(x, p) ∧ p ≥ 85 
    ∧ Calificacion(x, c) ∧ c ≥ 8.0
    ∧ MateriasReprobadas(x, n) ∧ n = 0
    → RiesgoBajo(x) ∧ ScoreRiesgo(x, 15)]
```

**En español:**
```
PARA TODO alumno x:
SI (x es alumno) Y (asistencia de x ≥ 85%) 
    Y (calificación de x ≥ 8.0)
    Y (materias reprobadas de x = 0)
ENTONCES (riesgo de x es BAJO) Y (score de riesgo de x = 15)
```

**Prioridad:** 6

---

### 13. Regla de Riesgo BAJO - Participación Activa

**Formalización:**
```
∀x [Alumno(x) ∧ AsistenciaTutorias(x, p) ∧ p ≥ 80 
    ∧ SeguimientosCerrados(x, sc) ∧ SeguimientosAbiertos(x, sa) ∧ sc ≥ sa
    ∧ Asistencia(x, pa) ∧ pa ≥ 80
    → RiesgoBajo(x) ∧ ScoreRiesgo(x, 20)]
```

**En español:**
```
PARA TODO alumno x:
SI (x es alumno) Y (asistencia a tutorías de x ≥ 80%) 
    Y (seguimientos cerrados de x ≥ seguimientos abiertos de x)
    Y (asistencia general de x ≥ 80%)
ENTONCES (riesgo de x es BAJO) Y (score de riesgo de x = 20)
```

**Prioridad:** 5

---

### 14. Regla de Riesgo BAJO - Tendencia Positiva

**Formalización:**
```
∀x [Alumno(x) ∧ TendenciaAsistencia(x, t) ∧ t > 5 
    ∧ TendenciaCalificacion(x, tc) ∧ tc > 0.5
    ∧ Asistencia(x, p) ∧ p ≥ 75
    → RiesgoBajo(x) ∧ ScoreRiesgo(x, 25)]
```

**En español:**
```
PARA TODO alumno x:
SI (x es alumno) Y (tendencia asistencia de x > 5%) 
    Y (tendencia calificación de x > 0.5)
    Y (asistencia de x ≥ 75%)
ENTONCES (riesgo de x es BAJO) Y (score de riesgo de x = 25)
```

**Prioridad:** 5

---

## Sistema de Prioridades

El sistema implementa un mecanismo de **resolución de conflictos** basado en prioridades:

```
∀x ∀r1 ∀r2 [
    Regla(r1) ∧ Regla(r2) ∧ Prioridad(r1, p1) ∧ Prioridad(r2, p2) 
    ∧ p1 > p2 ∧ Aplicable(r1, x) ∧ Aplicable(r2, x)
    → UsarRegla(r1, x)
]
```

**En español:**
```
Si dos reglas aplican al mismo alumno, se usa la regla de mayor prioridad.
```

---

## Cálculo de Score Base

Cuando ninguna regla aplica, se calcula un score base:

```
ScoreBase(x, s) ↔ 
    ∃p ∃c ∃n [
        Asistencia(x, p) ∧ Calificacion(x, c) ∧ MateriasReprobadas(x, n) ∧
        s = 50 + AjusteAsistencia(p) + AjusteCalificacion(c) + (n × 10)
    ]
```

Donde:
- `AjusteAsistencia(p) = if p < 50 then 30 else if p < 70 then 15 else if p ≥ 90 then -20 else 0`
- `AjusteCalificacion(c) = if c < 6.0 then 25 else if c < 7.0 then 10 else if c ≥ 9.0 then -15 else 0`

---

## Conversión de Score a Nivel de Riesgo

```
∀x ∀s [
    ScoreRiesgo(x, s) ∧ s ≥ 80 → RiesgoCritico(x)
    ScoreRiesgo(x, s) ∧ s ≥ 65 ∧ s < 80 → RiesgoAlto(x)
    ScoreRiesgo(x, s) ∧ s ≥ 40 ∧ s < 65 → RiesgoMedio(x)
    ScoreRiesgo(x, s) ∧ s < 40 → RiesgoBajo(x)
]
```

---

## Ejemplo de Inferencia Completa

### Hechos (Base de Conocimiento)
```
Alumno(2)
Asistencia(2, 45.5)
FaltasConsecutivas(2, 8)
Calificacion(2, 5.2)
MateriasReprobadas(2, 3)
SeguimientosAbiertos(2, 2)
```

### Aplicación de Reglas

**Regla 3 (Prioridad 9):**
```
Asistencia(2, 45.5) ∧ 45.5 < 50 ∧ FaltasConsecutivas(2, 8) ∧ 8 ≥ 5
→ RiesgoAlto(2) ∧ ScoreRiesgo(2, 85)
```

**Regla 4 (Prioridad 9):**
```
Asistencia(2, 45.5) ∧ 45.5 < 70 
    ∧ Calificacion(2, 5.2) ∧ 5.2 < 6.0
    ∧ SeguimientosAbiertos(2, 2) ∧ 2 ≥ 2
→ RiesgoAlto(2) ∧ ScoreRiesgo(2, 80)
```

### Conclusión Final

Como ambas reglas tienen la misma prioridad (9), se toma la de mayor score:
```
RiesgoAlto(2) ∧ ScoreRiesgo(2, 85)
```

---

## Propiedades del Sistema

### 1. Monotonicidad
```
Si KB ⊢ RiesgoAlto(x) entonces KB ∪ {NuevoHecho} ⊢ RiesgoAlto(x) ∨ RiesgoMayor(x)
```

### 2. Consistencia
```
No existe x tal que RiesgoCritico(x) ∧ RiesgoBajo(x)
```

### 3. Completitud
```
Para todo alumno x, existe un nivel de riesgo: 
    RiesgoCritico(x) ∨ RiesgoAlto(x) ∨ RiesgoMedio(x) ∨ RiesgoBajo(x)
```

---

## Notación Alternativa (Horn Clauses)

Las reglas también pueden expresarse como **cláusulas de Horn**:

```
RiesgoCritico(x) ← Alumno(x), Asistencia(x, p), p < 30, FaltasConsecutivas(x, n), n ≥ 10

RiesgoAlto(x) ← Alumno(x), Asistencia(x, p), p < 50, FaltasConsecutivas(x, n), n ≥ 5

RiesgoAlto(x) ← Alumno(x), Asistencia(x, p), p < 70, 
                Calificacion(x, c), c < 6.0, 
                SeguimientosAbiertos(x, n), n ≥ 2

RiesgoBajo(x) ← Alumno(x), Asistencia(x, p), p ≥ 85, 
                Calificacion(x, c), c ≥ 8.0, 
                MateriasReprobadas(x, n), n = 0
```

---

**Este sistema demuestra la aplicación práctica de la lógica de predicados y sistemas de inferencia en un problema del mundo real.**



