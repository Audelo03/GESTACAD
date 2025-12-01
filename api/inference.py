"""
Motor de Inferencias Avanzado - Programación Lógica y Funcional
Sistema completo de reglas para análisis de riesgo estudiantil
Basado en Tutorías, Inscripciones y Seguimientos (sin tabla asistencias)
"""
from typing import Dict, List, Tuple, Optional
from dataclasses import dataclass
from enum import Enum

class RiskLevel(Enum):
    """Niveles de riesgo"""
    BAJO = "BAJO"
    MEDIO = "MEDIO"
    ALTO = "ALTO"
    CRITICO = "CRITICO"

@dataclass
class Rule:
    """Estructura de una regla de inferencia"""
    name: str
    priority: int
    conditions: List[callable]
    conclusion: callable
    description: str
    score_impact: int  # Impacto en el score (0-100)

class InferenceEngine:
    """Motor de inferencias avanzado con sistema de reglas"""
    
    def __init__(self):
        self.rules: List[Rule] = []
        self._initialize_rules()
    
    def _initialize_rules(self):
        """Inicializa todas las reglas de inferencia basadas en tutorías, calificaciones y seguimientos"""
        
        # REGLAS DE RIESGO CRÍTICO (Prioridad 10)
        self.rules.append(Rule(
            name="riesgo_critico_baja_participacion_extrema",
            priority=10,
            conditions=[
                lambda s: s.get('asistencia_tutorias_grupales', 100) < 20,  # Menos del 20% de asistencia a tutorías
                lambda s: s.get('faltas_consecutivas_tutorias', 0) >= 5,  # 5+ faltas consecutivas
                lambda s: s.get('tutorias_individuales_recientes', 0) == 0  # Sin tutorías individuales
            ],
            conclusion=lambda s: (RiskLevel.CRITICO, 95),
            description="Participación extremadamente baja en tutorías con múltiples faltas consecutivas y sin tutorías individuales",
            score_impact=95
        ))
        
        self.rules.append(Rule(
            name="riesgo_critico_multiple_reprobacion",
            priority=10,
            conditions=[
                lambda s: s.get('materias_reprobadas', 0) >= 5,
                lambda s: s.get('calificacion_promedio', 10) < 5.0
            ],
            conclusion=lambda s: (RiskLevel.CRITICO, 90),
            description="Múltiples materias reprobadas con calificación promedio muy baja",
            score_impact=90
        ))
        
        self.rules.append(Rule(
            name="riesgo_critico_abandono_total",
            priority=10,
            conditions=[
                lambda s: s.get('asistencia_tutorias_grupales', 100) < 10,  # Menos del 10%
                lambda s: s.get('seguimientos_abiertos', 0) >= 3,  # 3+ seguimientos abiertos
                lambda s: s.get('materias_reprobadas', 0) >= 3
            ],
            conclusion=lambda s: (RiskLevel.CRITICO, 92),
            description="Abandono casi total: sin participación en tutorías, múltiples seguimientos abiertos y materias reprobadas",
            score_impact=92
        ))
        
        # REGLAS DE RIESGO ALTO (Prioridad 9)
        self.rules.append(Rule(
            name="riesgo_alto_baja_participacion_tutorias",
            priority=9,
            conditions=[
                lambda s: s.get('asistencia_tutorias_grupales', 100) < 40,  # Menos del 40%
                lambda s: s.get('faltas_consecutivas_tutorias', 0) >= 3,  # 3+ faltas consecutivas
                lambda s: s.get('tutorias_individuales_recientes', 0) == 0
            ],
            conclusion=lambda s: (RiskLevel.ALTO, 85),
            description="Baja participación en tutorías grupales con faltas consecutivas y sin tutorías individuales",
            score_impact=85
        ))
        
        self.rules.append(Rule(
            name="riesgo_alto_multiple_factores",
            priority=9,
            conditions=[
                lambda s: s.get('asistencia_tutorias_grupales', 100) < 60,  # Menos del 60%
                lambda s: s.get('calificacion_promedio', 10) < 6.0,
                lambda s: s.get('seguimientos_abiertos', 0) >= 2
            ],
            conclusion=lambda s: (RiskLevel.ALTO, 80),
            description="Baja participación en tutorías, bajas calificaciones y múltiples seguimientos abiertos",
            score_impact=80
        ))
        
        self.rules.append(Rule(
            name="riesgo_alto_reprobacion_masiva",
            priority=9,
            conditions=[
                lambda s: s.get('materias_reprobadas', 0) >= 3,
                lambda s: s.get('calificacion_promedio', 10) < 6.5
            ],
            conclusion=lambda s: (RiskLevel.ALTO, 75),
            description="Múltiples materias reprobadas con calificaciones bajas",
            score_impact=75
        ))
        
        self.rules.append(Rule(
            name="riesgo_alto_tendencia_negativa",
            priority=9,
            conditions=[
                lambda s: s.get('tendencia_tutorias', 0) < -20,  # Empeorando más del 20%
                lambda s: s.get('tendencia_calificacion', 0) < -1.0,
                lambda s: s.get('seguimientos_abiertos', 0) >= 1
            ],
            conclusion=lambda s: (RiskLevel.ALTO, 70),
            description="Tendencia negativa en participación y calificaciones con seguimientos abiertos",
            score_impact=70
        ))
        
        self.rules.append(Rule(
            name="riesgo_alto_sin_compromiso",
            priority=9,
            conditions=[
                lambda s: s.get('participacion_general', 100) < 40,  # Baja participación general
                lambda s: s.get('compromiso_academico', 100) < 50,  # Bajo compromiso académico
                lambda s: s.get('seguimientos_abiertos', 0) >= 2
            ],
            conclusion=lambda s: (RiskLevel.ALTO, 72),
            description="Baja participación general y bajo compromiso académico con múltiples seguimientos",
            score_impact=72
        ))
        
        # REGLAS DE RIESGO MEDIO (Prioridad 7-8)
        self.rules.append(Rule(
            name="riesgo_medio_participacion_baja",
            priority=8,
            conditions=[
                lambda s: 40 <= s.get('asistencia_tutorias_grupales', 100) < 70,  # Entre 40-70%
                lambda s: s.get('seguimientos_abiertos', 0) >= 1
            ],
            conclusion=lambda s: (RiskLevel.MEDIO, 60),
            description="Participación moderada en tutorías con seguimientos activos",
            score_impact=60
        ))
        
        self.rules.append(Rule(
            name="riesgo_medio_calificaciones_bajas",
            priority=8,
            conditions=[
                lambda s: 6.0 <= s.get('calificacion_promedio', 10) < 7.0,
                lambda s: s.get('materias_reprobadas', 0) >= 1
            ],
            conclusion=lambda s: (RiskLevel.MEDIO, 55),
            description="Calificaciones bajas con materias reprobadas",
            score_impact=55
        ))
        
        self.rules.append(Rule(
            name="riesgo_medio_participacion_irregular",
            priority=7,
            conditions=[
                lambda s: s.get('asistencia_tutorias_grupales', 100) < 60,
                lambda s: s.get('total_tutorias_grupales', 0) >= 3,  # Hay tutorías disponibles
                lambda s: s.get('tutorias_individuales_recientes', 0) == 0  # Sin tutorías individuales
            ],
            conclusion=lambda s: (RiskLevel.MEDIO, 50),
            description="Participación irregular en tutorías grupales sin tutorías individuales",
            score_impact=50
        ))
        
        self.rules.append(Rule(
            name="riesgo_medio_tendencia_decreciente",
            priority=7,
            conditions=[
                lambda s: s.get('tendencia_tutorias', 0) < -10,  # Empeorando más del 10%
                lambda s: s.get('asistencia_tutorias_grupales', 100) < 70
            ],
            conclusion=lambda s: (RiskLevel.MEDIO, 45),
            description="Tendencia decreciente en participación en tutorías",
            score_impact=45
        ))
        
        self.rules.append(Rule(
            name="riesgo_medio_seguimientos_pendientes",
            priority=7,
            conditions=[
                lambda s: s.get('seguimientos_abiertos', 0) >= 2,
                lambda s: s.get('seguimientos_cerrados_recientes', 0) == 0,  # No ha cerrado seguimientos
                lambda s: s.get('calificacion_promedio', 10) < 7.5
            ],
            conclusion=lambda s: (RiskLevel.MEDIO, 48),
            description="Múltiples seguimientos abiertos sin resolver y calificaciones mejorables",
            score_impact=48
        ))
        
        # REGLAS DE RIESGO BAJO (Prioridad 5-6)
        self.rules.append(Rule(
            name="riesgo_bajo_excelente_rendimiento",
            priority=6,
            conditions=[
                lambda s: s.get('asistencia_tutorias_grupales', 0) >= 80,  # 80%+ asistencia a tutorías
                lambda s: s.get('calificacion_promedio', 0) >= 9.0,
                lambda s: s.get('materias_reprobadas', 10) == 0
            ],
            conclusion=lambda s: (RiskLevel.BAJO, 10),
            description="Excelente rendimiento: alta participación en tutorías y excelentes calificaciones",
            score_impact=10
        ))
        
        self.rules.append(Rule(
            name="riesgo_bajo_buen_rendimiento",
            priority=6,
            conditions=[
                lambda s: s.get('asistencia_tutorias_grupales', 0) >= 70,  # 70%+ asistencia
                lambda s: s.get('calificacion_promedio', 0) >= 8.0,
                lambda s: s.get('materias_reprobadas', 10) == 0
            ],
            conclusion=lambda s: (RiskLevel.BAJO, 15),
            description="Buen rendimiento: buena participación y buenas calificaciones",
            score_impact=15
        ))
        
        self.rules.append(Rule(
            name="riesgo_bajo_participacion_activa",
            priority=5,
            conditions=[
                lambda s: s.get('asistencia_tutorias_grupales', 0) >= 80,
                lambda s: s.get('tutorias_individuales_recientes', 0) >= 1,  # Tiene tutorías individuales
                lambda s: s.get('seguimientos_cerrados_recientes', 0) >= s.get('seguimientos_abiertos', 1),  # Más cerrados que abiertos
                lambda s: s.get('participacion_general', 0) >= 70
            ],
            conclusion=lambda s: (RiskLevel.BAJO, 20),
            description="Participación activa en tutorías grupales e individuales con seguimientos resueltos",
            score_impact=20
        ))
        
        self.rules.append(Rule(
            name="riesgo_bajo_tendencia_positiva",
            priority=5,
            conditions=[
                lambda s: s.get('tendencia_tutorias', 0) > 10,  # Mejorando más del 10%
                lambda s: s.get('tendencia_calificacion', 0) > 0.5,
                lambda s: s.get('asistencia_tutorias_grupales', 0) >= 60
            ],
            conclusion=lambda s: (RiskLevel.BAJO, 25),
            description="Tendencia positiva en participación y calificaciones",
            score_impact=25
        ))
        
        self.rules.append(Rule(
            name="riesgo_bajo_compromiso_alto",
            priority=5,
            conditions=[
                lambda s: s.get('compromiso_academico', 0) >= 80,
                lambda s: s.get('participacion_general', 0) >= 70,
                lambda s: s.get('seguimientos_abiertos', 0) <= 1
            ],
            conclusion=lambda s: (RiskLevel.BAJO, 18),
            description="Alto compromiso académico y buena participación general",
            score_impact=18
        ))
        
        # Ordenar reglas por prioridad (mayor primero)
        self.rules.sort(key=lambda r: r.priority, reverse=True)
    
    def evaluate_rule(self, rule: Rule, stats: Dict) -> Optional[Tuple]:
        """Evalúa si una regla se cumple"""
        try:
            for condition in rule.conditions:
                if not condition(stats):
                    return None
            return rule.conclusion(stats)
        except (KeyError, TypeError, AttributeError):
            return None
    
    def infer(self, stats: Dict) -> Dict:
        """
        Realiza inferencias basadas en estadísticas
        Retorna: nivel de riesgo, score, explicaciones y recomendaciones
        """
        applied_rules = []
        max_score = 0
        final_level = RiskLevel.MEDIO
        final_score = 50
        
        # Evaluar todas las reglas
        for rule in self.rules:
            result = self.evaluate_rule(rule, stats)
            if result:
                level, score = result
                applied_rules.append({
                    'regla': rule.name,
                    'descripcion': rule.description,
                    'nivel': level.value,
                    'score': score,
                    'prioridad': rule.priority
                })
                
                # Usar la regla de mayor prioridad y score
                if rule.priority >= 9 or score > max_score:
                    max_score = score
                    final_level = level
                    final_score = score
        
        # Si no se aplicó ninguna regla, calcular score base
        if not applied_rules:
            final_score = self._calculate_base_score(stats)
            final_level = self._score_to_level(final_score)
        
        # Generar recomendaciones
        recomendaciones = self._generate_recommendations(final_level, stats, applied_rules)
        
        # Generar explicación
        explicacion = self._generate_explanation(final_level, applied_rules, stats)
        
        return {
            'nivel_riesgo': final_level.value,
            'score_riesgo': final_score,
            'reglas_aplicadas': applied_rules,
            'explicacion': explicacion,
            'recomendaciones': recomendaciones,
            'posible_desercion': final_level in [RiskLevel.ALTO, RiskLevel.CRITICO]
        }
    
    def _calculate_base_score(self, stats: Dict) -> int:
        """Calcula un score base si no se aplicaron reglas"""
        score = 50  # Base
        
        # Ajustar por participación en tutorías (proxy de asistencia)
        participacion = stats.get('asistencia_tutorias_grupales', 100)
        if participacion < 30:
            score += 30
        elif participacion < 50:
            score += 20
        elif participacion < 70:
            score += 10
        elif participacion >= 90:
            score -= 20
        
        # Ajustar por calificación
        calificacion = stats.get('calificacion_promedio', 10)
        if calificacion < 6.0:
            score += 25
        elif calificacion < 7.0:
            score += 10
        elif calificacion >= 9.0:
            score -= 15
        
        # Ajustar por materias reprobadas
        reprobadas = stats.get('materias_reprobadas', 0)
        score += reprobadas * 10
        
        # Ajustar por seguimientos abiertos
        seguimientos_abiertos = stats.get('seguimientos_abiertos', 0)
        score += seguimientos_abiertos * 5
        
        # Ajustar por participación general
        participacion_general = stats.get('participacion_general', 100)
        if participacion_general < 40:
            score += 15
        elif participacion_general >= 80:
            score -= 10
        
        return max(0, min(100, score))
    
    def _score_to_level(self, score: int) -> RiskLevel:
        """Convierte un score numérico a nivel de riesgo"""
        if score >= 80:
            return RiskLevel.CRITICO
        elif score >= 65:
            return RiskLevel.ALTO
        elif score >= 40:
            return RiskLevel.MEDIO
        else:
            return RiskLevel.BAJO
    
    def _generate_recommendations(self, level: RiskLevel, stats: Dict, rules: List[Dict]) -> List[str]:
        """Genera recomendaciones basadas en el nivel de riesgo"""
        recomendaciones = []
        
        if level == RiskLevel.CRITICO:
            recomendaciones.append("🚨 INTERVENCIÓN URGENTE REQUERIDA")
            recomendaciones.append("Contactar inmediatamente al alumno y familia")
            recomendaciones.append("Reunión de emergencia con tutor y coordinador")
            recomendaciones.append("Canalización inmediata a áreas de apoyo")
            if stats.get('asistencia_tutorias_grupales', 100) < 30:
                recomendaciones.append("📅 Implementar plan urgente de participación en tutorías")
            if stats.get('tutorias_individuales_recientes', 0) == 0:
                recomendaciones.append("👤 Programar tutorías individuales inmediatas")
        
        if level == RiskLevel.ALTO:
            recomendaciones.append("⚠️ RIESGO ALTO - Acción inmediata necesaria")
            if stats.get('asistencia_tutorias_grupales', 100) < 60:
                recomendaciones.append("📅 Implementar plan de mejora de participación en tutorías")
                recomendaciones.append("Establecer comunicación regular con el alumno")
            if stats.get('calificacion_promedio', 10) < 6.0:
                recomendaciones.append("📚 Asignar tutorías individuales intensivas")
                recomendaciones.append("Revisar estrategias de aprendizaje")
            if stats.get('materias_reprobadas', 0) >= 2:
                recomendaciones.append("🎓 Evaluar carga académica y considerar reducción")
            if stats.get('tutorias_individuales_recientes', 0) == 0:
                recomendaciones.append("👤 Programar tutorías individuales para identificar problemas")
        
        if level == RiskLevel.MEDIO:
            recomendaciones.append("⚠️ RIESGO MEDIO - Monitoreo activo")
            if stats.get('asistencia_tutorias_grupales', 100) < 70:
                recomendaciones.append("📅 Seguimiento semanal de participación en tutorías")
            if stats.get('calificacion_promedio', 10) < 7.5:
                recomendaciones.append("📚 Ofrecer tutorías grupales de refuerzo")
            if stats.get('seguimientos_abiertos', 0) > 0:
                recomendaciones.append("📋 Revisar y dar seguimiento a casos abiertos")
            recomendaciones.append("💬 Mantener comunicación regular con el alumno")
        
        if level == RiskLevel.BAJO:
            recomendaciones.append("✅ RIESGO BAJO - Mantener seguimiento preventivo")
            recomendaciones.append("💡 Continuar con el apoyo actual")
            recomendaciones.append("🎯 Fomentar participación en actividades extracurriculares")
            if stats.get('participacion_general', 0) >= 90:
                recomendaciones.append("🌟 Reconocer y fomentar el excelente compromiso del alumno")
        
        return recomendaciones
    
    def _generate_explanation(self, level: RiskLevel, rules: List[Dict], stats: Dict) -> str:
        """Genera una explicación textual de las inferencias"""
        if not rules:
            return f"Nivel de riesgo: {level.value}. Evaluación basada en métricas generales de participación y rendimiento académico."
        
        explanation = f"Nivel de Riesgo Inferido: {level.value}\n\n"
        explanation += "Reglas Aplicadas:\n"
        
        for rule in rules[:3]:  # Mostrar máximo 3 reglas principales
            explanation += f"• {rule['descripcion']} (Prioridad: {rule['prioridad']}, Score: {rule['score']})\n"
        
        if len(rules) > 3:
            explanation += f"\n... y {len(rules) - 3} regla(s) adicional(es)\n"
        
        # Agregar contexto adicional
        explanation += f"\nContexto:\n"
        explanation += f"- Participación en tutorías grupales: {stats.get('asistencia_tutorias_grupales', 0):.1f}%\n"
        explanation += f"- Calificación promedio: {stats.get('calificacion_promedio', 0):.2f}\n"
        explanation += f"- Materias reprobadas: {stats.get('materias_reprobadas', 0)}\n"
        explanation += f"- Seguimientos abiertos: {stats.get('seguimientos_abiertos', 0)}\n"
        
        return explanation

# Instancia global del motor
inference_engine = InferenceEngine()

def evaluar_riesgo(estadisticas: Dict) -> Tuple[str, str, int, List[str], List[Dict]]:
    """
    Función principal para evaluar riesgo
    Retorna: (nivel_riesgo, explicacion, score_riesgo, recomendaciones, reglas_aplicadas)
    """
    resultado = inference_engine.infer(estadisticas)
    return (
        resultado['nivel_riesgo'],
        resultado['explicacion'],
        resultado['score_riesgo'],
        resultado['recomendaciones'],
        resultado['reglas_aplicadas']
    )

def get_all_rules() -> List[Dict]:
    """Obtiene todas las reglas del motor de inferencias"""
    return [
        {
            'nombre': rule.name,
            'descripcion': rule.description,
            'prioridad': rule.priority,
            'score_impact': rule.score_impact
        }
        for rule in inference_engine.rules
    ]
