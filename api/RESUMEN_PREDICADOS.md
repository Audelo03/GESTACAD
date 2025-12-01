# Resumen de Inferencias en Lenguaje de Predicados

## Notación Utilizada

- **Predicados**: `P(x, y)` - Relación entre entidades
- **Cuantificadores**: `∀x` (para todo), `∃x` (existe)
- **Operadores**: `∧` (Y), `∨` (O), `→` (ENTONCES), `¬` (NO)

---

## Reglas de Inferencia (Resumen)

### 🔴 RIESGO CRÍTICO (Prioridad 10)

#### R1: Asistencia Extrema
```
∀x [Asistencia(x, p) < 30 ∧ FaltasConsecutivas(x, n) ≥ 10 
    → RiesgoCritico(x) ∧ Score(x, 95)]
```

#### R2: Múltiples Reprobar
```
∀x [MateriasReprobadas(x, n) ≥ 5 ∧ Calificacion(x, c) < 5.0
    → RiesgoCritico(x) ∧ Score(x, 90)]
```

---

### 🟠 RIESGO ALTO (Prioridad 9)

#### R3: Asistencia Crítica
```
∀x [Asistencia(x, p) < 50 ∧ FaltasConsecutivas(x, n) ≥ 5
    → RiesgoAlto(x) ∧ Score(x, 85)]
```

#### R4: Múltiples Factores
```
∀x [Asistencia(x, p) < 70 ∧ Calificacion(x, c) < 6.0 ∧ SeguimientosAbiertos(x, n) ≥ 2
    → RiesgoAlto(x) ∧ Score(x, 80)]
```

#### R5: Reprobar Masiva
```
∀x [MateriasReprobadas(x, n) ≥ 3 ∧ Calificacion(x, c) < 6.5
    → RiesgoAlto(x) ∧ Score(x, 75)]
```

#### R6: Tendencia Negativa
```
∀x [TendenciaAsistencia(x, t) < -10 ∧ TendenciaCalificacion(x, tc) < -1.0
    → RiesgoAlto(x) ∧ Score(x, 70)]
```

---

### 🟡 RIESGO MEDIO (Prioridad 7-8)

#### R7: Asistencia Baja
```
∀x [50 ≤ Asistencia(x, p) < 75 ∧ SeguimientosAbiertos(x, n) ≥ 1
    → RiesgoMedio(x) ∧ Score(x, 60)]
```

#### R8: Calificaciones Bajas
```
∀x [6.0 ≤ Calificacion(x, c) < 7.0 ∧ MateriasReprobadas(x, n) ≥ 1
    → RiesgoMedio(x) ∧ Score(x, 55)]
```

#### R9: Sin Tutorías
```
∀x [AsistenciaTutorias(x, p) < 50 ∧ TutoriasDisponibles(x, n) ≥ 3 ∧ Asistencia(x, pa) < 80
    → RiesgoMedio(x) ∧ Score(x, 50)]
```

#### R10: Tendencia Decreciente
```
∀x [TendenciaAsistencia(x, t) < -5 ∧ Asistencia(x, p) < 80
    → RiesgoMedio(x) ∧ Score(x, 45)]
```

---

### 🟢 RIESGO BAJO (Prioridad 5-6)

#### R11: Excelente Rendimiento
```
∀x [Asistencia(x, p) ≥ 90 ∧ Calificacion(x, c) ≥ 9.0 ∧ MateriasReprobadas(x, n) = 0
    → RiesgoBajo(x) ∧ Score(x, 10)]
```

#### R12: Buen Rendimiento
```
∀x [Asistencia(x, p) ≥ 85 ∧ Calificacion(x, c) ≥ 8.0 ∧ MateriasReprobadas(x, n) = 0
    → RiesgoBajo(x) ∧ Score(x, 15)]
```

#### R13: Participación Activa
```
∀x [AsistenciaTutorias(x, p) ≥ 80 ∧ SeguimientosCerrados(x, sc) ≥ SeguimientosAbiertos(x, sa) ∧ Asistencia(x, pa) ≥ 80
    → RiesgoBajo(x) ∧ Score(x, 20)]
```

#### R14: Tendencia Positiva
```
∀x [TendenciaAsistencia(x, t) > 5 ∧ TendenciaCalificacion(x, tc) > 0.5 ∧ Asistencia(x, p) ≥ 75
    → RiesgoBajo(x) ∧ Score(x, 25)]
```

---

## Sistema de Prioridades

```
∀x ∀r1 ∀r2 [
    Regla(r1) ∧ Regla(r2) ∧ Prioridad(r1, p1) ∧ Prioridad(r2, p2) 
    ∧ p1 > p2 ∧ Aplicable(r1, x) ∧ Aplicable(r2, x)
    → UsarRegla(r1, x)
]
```

---

## Conversión Score → Nivel

```
∀x ∀s [
    ScoreRiesgo(x, s) ∧ s ≥ 80 → RiesgoCritico(x)
    ScoreRiesgo(x, s) ∧ 65 ≤ s < 80 → RiesgoAlto(x)
    ScoreRiesgo(x, s) ∧ 40 ≤ s < 65 → RiesgoMedio(x)
    ScoreRiesgo(x, s) ∧ s < 40 → RiesgoBajo(x)
]
```

---

**Total de Reglas: 14**


