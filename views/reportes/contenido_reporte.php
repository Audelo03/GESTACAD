<?php
// Este archivo renderiza el contenido del reporte
// La variable $data contiene todos los datos del reporte
if (!isset($data) || empty($data)) {
    echo '<div class="alert alert-warning">No hay datos para mostrar.</div>';
    return;
}
?>

<div class="reporte-container">
    <!-- Información del parcial a reportar -->
    <div class="card mb-3 mb-md-4 shadow-sm">
        <div class="card-header bg-warning text-dark py-2 py-md-3">
            <h5 class="mb-0 small d-sm-none"><i class="bi bi-info-circle me-1"></i>Información</h5>
            <h5 class="mb-0 d-none d-sm-block"><i class="bi bi-info-circle me-2"></i>Información del Parcial a Reportar</h5>
        </div>
        <div class="card-body p-2 p-md-3">
            <div class="row g-2 g-md-3">
                <div class="col-6 col-md-3 mb-2 mb-md-0">
                    <div class="small text-muted d-md-none mb-1">Carrera</div>
                    <div class="small d-md-none fw-semibold"><?= htmlspecialchars($data['grupo']['carrera_nombre'] ?? 'N/A') ?></div>
                    <div class="d-none d-md-block"><strong>Carrera:</strong> <?= htmlspecialchars($data['grupo']['carrera_nombre'] ?? 'N/A') ?></div>
                </div>
                <div class="col-6 col-md-3 mb-2 mb-md-0">
                    <div class="small text-muted d-md-none mb-1">Parcial</div>
                    <div class="small d-md-none fw-semibold"><?= htmlspecialchars($data['parcial']['nombre'] ?? 'N/A') ?></div>
                    <div class="d-none d-md-block"><strong>Parcial:</strong> <?= htmlspecialchars($data['parcial']['nombre'] ?? 'N/A') ?></div>
                </div>
                <div class="col-6 col-md-3 mb-2 mb-md-0">
                    <div class="small text-muted d-md-none mb-1">Grupo</div>
                    <div class="small d-md-none fw-semibold"><?= htmlspecialchars($data['grupo']['nombre'] ?? 'N/A') ?></div>
                    <div class="d-none d-md-block"><strong>Semestre y grupo:</strong> <?= htmlspecialchars($data['grupo']['nombre'] ?? 'N/A') ?></div>
                </div>
                <div class="col-6 col-md-3 mb-2 mb-md-0">
                    <div class="small text-muted d-md-none mb-1">Total Estudiantes</div>
                    <div class="small d-md-none fw-semibold"><?= $data['total_estudiantes'] ?? 0 ?></div>
                    <div class="d-none d-md-block"><strong>No. Total de estudiantes:</strong> <?= $data['total_estudiantes'] ?? 0 ?></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 4. Posible deserción -->
    <div class="card mb-3 mb-md-4 shadow-sm">
        <div class="card-header bg-danger text-white py-2 py-md-3">
            <h5 class="mb-0 small d-sm-none">4. Deserción</h5>
            <h5 class="mb-0 d-none d-sm-block">4. Posible Deserción</h5>
        </div>
        <div class="card-body p-2 p-md-3">
            <!-- Vista de tabla para desktop -->
            <div class="table-responsive d-none d-md-block" style="max-height: 300px; overflow-y: auto;">
                <table class="table table-bordered table-striped table-sm mb-0">
                    <thead class="sticky-top bg-danger text-white">
                        <tr>
                            <th class="small" style="width: 50px;">No.</th>
                            <th class="small">Estudiante</th>
                            <th class="small" style="width: 80px;">Nivel</th>
                            <th class="small">Causas</th>
                            <th class="small" style="width: 100px;">Fuente</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data['posible_desercion'])): ?>
                            <tr><td colspan="5" class="text-center text-muted small py-3">No hay alumnos en riesgo de deserción</td></tr>
                        <?php else: ?>
                            <?php $contador = 1; foreach ($data['posible_desercion'] as $alumno): ?>
                                <tr>
                                    <td class="small">4.<?= $contador ?></td>
                                    <td class="small fw-semibold"><?= htmlspecialchars($alumno['nombre_completo']) ?></td>
                                    <td><span class="badge bg-warning badge-sm"><?= htmlspecialchars($alumno['nivel']) ?></span></td>
                                    <td class="small"><?= htmlspecialchars($alumno['motivo'] ?? 'No especificado') ?></td>
                                    <td class="small"><?= htmlspecialchars($alumno['fuente'] ?? 'Manual') ?></td>
                                </tr>
                            <?php $contador++; endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Vista de tarjetas para móvil -->
            <div class="d-md-none">
                <?php if (empty($data['posible_desercion'])): ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                        <p class="mt-2 mb-0">No hay alumnos en riesgo de deserción</p>
                    </div>
                <?php else: ?>
                    <?php $contador = 1; foreach ($data['posible_desercion'] as $alumno): ?>
                        <div class="card mb-3 border-start border-danger border-3 shadow-sm">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="flex-grow-1">
                                        <div class="small text-muted mb-1">#4.<?= $contador ?></div>
                                        <h6 class="mb-1 fw-bold"><?= htmlspecialchars($alumno['nombre_completo']) ?></h6>
                                    </div>
                                    <span class="badge bg-warning text-dark"><?= htmlspecialchars($alumno['nivel']) ?></span>
                                </div>
                                
                                <?php if (!empty($alumno['motivo'])): ?>
                                    <div class="mb-2">
                                        <div class="small text-muted mb-1"><i class="bi bi-exclamation-circle me-1"></i>Causas:</div>
                                        <div class="small"><?= htmlspecialchars($alumno['motivo']) ?></div>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="small text-muted">
                                    <i class="bi bi-source me-1"></i>
                                    <strong>Fuente:</strong> <?= htmlspecialchars($alumno['fuente'] ?? 'Manual') ?>
                                </div>
                            </div>
                        </div>
                    <?php $contador++; endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- 5. Becas -->
    <div class="card mb-3 mb-md-4 shadow-sm">
        <div class="card-header bg-warning text-dark py-2 py-md-3">
            <h5 class="mb-0 small d-sm-none">5. Becas</h5>
            <h5 class="mb-0 d-none d-sm-block">5. Becas</h5>
        </div>
        <div class="card-body p-2 p-md-3">
            <!-- Vista de tabla para desktop -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-bordered table-sm mb-0">
                    <thead>
                        <tr>
                            <th class="small">No.</th>
                            <th class="small">Beca</th>
                            <th class="small text-center">Cantidad</th>
                            <th class="small text-center">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $contador = 1; foreach ($data['becas'] as $beca): ?>
                            <tr>
                                <td class="small">5.<?= $contador ?></td>
                                <td class="small"><?= htmlspecialchars($beca['nombre']) ?></td>
                                <td class="small text-center"><?= $beca['cantidad'] ?></td>
                                <td class="small text-center"><?= number_format($beca['porcentaje'], 2) ?>%</td>
                            </tr>
                        <?php $contador++; endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Vista de tarjetas para móvil -->
            <div class="d-md-none">
                <?php $contador = 1; foreach ($data['becas'] as $beca): ?>
                    <div class="card mb-3 border-start border-warning border-3 shadow-sm">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <div class="small text-muted mb-1">#5.<?= $contador ?></div>
                                    <h6 class="mb-0 fw-bold"><?= htmlspecialchars($beca['nombre']) ?></h6>
                                </div>
                                <div class="text-end">
                                    <div class="small text-muted">Cantidad</div>
                                    <div class="fw-bold"><?= $beca['cantidad'] ?></div>
                                </div>
                            </div>
                            <div class="mt-2">
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-warning text-dark" role="progressbar" 
                                         style="width: <?= min($beca['porcentaje'], 100) ?>%" 
                                         aria-valuenow="<?= $beca['porcentaje'] ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        <?= number_format($beca['porcentaje'], 2) ?>%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php $contador++; endforeach; ?>
            </div>
        </div>
    </div>
    
    <!-- 6. Áreas de Apoyo -->
    <div class="card mb-3 mb-md-4 shadow-sm">
        <div class="card-header bg-success text-white py-2 py-md-3">
            <h5 class="mb-0 small d-sm-none">6. Áreas de Apoyo</h5>
            <h5 class="mb-0 d-none d-sm-block">6. Áreas de Apoyo</h5>
        </div>
        <div class="card-body p-2 p-md-3">
            <!-- Vista de tabla para desktop -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-bordered table-sm mb-0">
                    <thead>
                        <tr>
                            <th class="small">No.</th>
                            <th class="small">Área</th>
                            <th class="small text-center">Cantidad</th>
                            <th class="small text-center">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $areas_contador = 1; foreach ($data['areas_apoyo'] as $area): ?>
                            <tr>
                                <td class="small">6.<?= $areas_contador ?></td>
                                <td class="small"><?= htmlspecialchars($area['nombre']) ?></td>
                                <td class="small text-center"><?= $area['cantidad'] ?></td>
                                <td class="small text-center"><?= number_format($area['porcentaje'], 2) ?>%</td>
                            </tr>
                        <?php $areas_contador++; endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Vista de tarjetas para móvil -->
            <div class="d-md-none">
                <?php $areas_contador = 1; foreach ($data['areas_apoyo'] as $area): ?>
                    <div class="card mb-3 border-start border-success border-3 shadow-sm">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <div class="small text-muted mb-1">#6.<?= $areas_contador ?></div>
                                    <h6 class="mb-0 fw-bold"><?= htmlspecialchars($area['nombre']) ?></h6>
                                </div>
                                <div class="text-end">
                                    <div class="small text-muted">Cantidad</div>
                                    <div class="fw-bold"><?= $area['cantidad'] ?></div>
                                </div>
                            </div>
                            <div class="mt-2">
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: <?= min($area['porcentaje'], 100) ?>%" 
                                         aria-valuenow="<?= $area['porcentaje'] ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        <?= number_format($area['porcentaje'], 2) ?>%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php $areas_contador++; endforeach; ?>
            </div>
        </div>
    </div>
    
    <!-- 8. Aprobado -->
    <div class="card mb-3 mb-md-4 shadow-sm">
        <div class="card-header bg-success text-white py-2 py-md-3">
            <h5 class="mb-0 small d-sm-none">8. Aprobado</h5>
            <h5 class="mb-0 d-none d-sm-block">8. Aprobado</h5>
        </div>
        <div class="card-body p-2 p-md-3">
            <!-- Vista de tabla para desktop -->
            <div class="table-responsive d-none d-md-block" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="sticky-top bg-success text-white">
                        <tr>
                            <th class="small" style="width: 50px;">No.</th>
                            <th class="small">Asignatura</th>
                            <th class="small text-center" style="width: 70px;">Cant.</th>
                            <th class="small text-center" style="width: 70px;">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $aprobado_contador = 1; foreach ($data['aprobado'] as $materia): ?>
                            <?php
                            $color_class = '';
                            $porcentaje = (float)$materia['porcentaje'];
                            if ($porcentaje >= 80) {
                                $color_class = 'table-success';
                            } elseif ($porcentaje < 50) {
                                $color_class = 'table-danger';
                            } else {
                                $color_class = 'table-warning';
                            }
                            ?>
                            <tr class="<?= $color_class ?>">
                                <td class="small"><?= $aprobado_contador ?></td>
                                <td class="small"><?= htmlspecialchars($materia['nombre']) ?></td>
                                <td class="small text-center"><?= $materia['cantidad_aprobados'] ?></td>
                                <td class="small text-center fw-bold"><?= number_format($porcentaje, 2) ?>%</td>
                            </tr>
                        <?php $aprobado_contador++; endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Vista de tarjetas para móvil -->
            <div class="d-md-none" style="max-height: 400px; overflow-y: auto;">
                <?php $aprobado_contador = 1; foreach ($data['aprobado'] as $materia): ?>
                    <?php
                    $porcentaje = (float)$materia['porcentaje'];
                    $border_color = 'border-success';
                    $progress_color = 'bg-success';
                    if ($porcentaje >= 80) {
                        $border_color = 'border-success';
                        $progress_color = 'bg-success';
                    } elseif ($porcentaje < 50) {
                        $border_color = 'border-danger';
                        $progress_color = 'bg-danger';
                    } else {
                        $border_color = 'border-warning';
                        $progress_color = 'bg-warning';
                    }
                    ?>
                    <div class="card mb-3 border-start <?= $border_color ?> border-3 shadow-sm">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="flex-grow-1">
                                    <div class="small text-muted mb-1">#<?= $aprobado_contador ?></div>
                                    <h6 class="mb-0 fw-bold"><?= htmlspecialchars($materia['nombre']) ?></h6>
                                </div>
                                <div class="text-end">
                                    <div class="small text-muted">Cantidad</div>
                                    <div class="fw-bold"><?= $materia['cantidad_aprobados'] ?></div>
                                </div>
                            </div>
                            <div class="mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small text-muted">Aprobación</span>
                                    <span class="fw-bold"><?= number_format($porcentaje, 2) ?>%</span>
                                </div>
                                <div class="progress" style="height: 24px;">
                                    <div class="progress-bar <?= $progress_color ?>" role="progressbar" 
                                         style="width: <?= min($porcentaje, 100) ?>%" 
                                         aria-valuenow="<?= $porcentaje ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        <?= number_format($porcentaje, 2) ?>%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php $aprobado_contador++; endforeach; ?>
            </div>
        </div>
    </div>
    
    <!-- 9. Reprobación -->
    <div class="card mb-3 mb-md-4 shadow-sm">
        <div class="card-header bg-danger text-white py-2 py-md-3">
            <h5 class="mb-0 small d-sm-none">9. Reprobación</h5>
            <h5 class="mb-0 d-none d-sm-block">9. Reprobación</h5>
        </div>
        <div class="card-body p-2 p-md-3">
            <!-- Vista de tabla para desktop -->
            <div class="row g-3 d-none d-md-flex">
                <div class="col-md-8">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-bordered table-sm mb-0">
                            <thead class="sticky-top bg-danger text-white">
                                <tr>
                                    <th class="small" style="width: 50px;">No.</th>
                                    <th class="small">Asignatura</th>
                                    <th class="small text-center" style="width: 70px;">Cant.</th>
                                    <th class="small text-center" style="width: 70px;">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $reprobacion_contador = 1; foreach ($data['reprobacion'] as $materia): ?>
                                    <?php
                                    $color_class = '';
                                    $porcentaje = (float)$materia['porcentaje'];
                                    if ($porcentaje >= 50) {
                                        $color_class = 'table-danger';
                                    } elseif ($porcentaje >= 25) {
                                        $color_class = 'table-warning';
                                    } else {
                                        $color_class = 'table-success';
                                    }
                                    ?>
                                    <tr class="<?= $color_class ?>">
                                        <td class="small"><?= $reprobacion_contador ?></td>
                                        <td class="small"><?= htmlspecialchars($materia['nombre']) ?></td>
                                        <td class="small text-center"><?= $materia['cantidad_reprobados'] ?></td>
                                        <td class="small text-center fw-bold"><?= number_format($porcentaje, 2) ?>%</td>
                                    </tr>
                                <?php $reprobacion_contador++; endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-4">
                    <h6 class="small mb-2 mb-md-3">Principales Causas</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0">
                            <thead>
                                <tr>
                                    <th class="small">Causa</th>
                                    <th class="small text-center">Cant.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['causas_reprobacion'] as $causa): ?>
                                    <?php if ($causa['cantidad'] > 0): ?>
                                        <tr>
                                            <td class="small"><?= htmlspecialchars($causa['causa']) ?></td>
                                            <td class="small text-center"><?= $causa['cantidad'] ?></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <?php 
                                $total_causas = array_sum(array_column($data['causas_reprobacion'], 'cantidad'));
                                if ($total_causas == 0): 
                                ?>
                                    <tr>
                                        <td colspan="2" class="text-center text-muted small py-2">No hay causas registradas</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Vista de tarjetas para móvil -->
            <div class="d-md-none">
                <h6 class="mb-3 fw-bold">Asignaturas con Reprobación</h6>
                <div style="max-height: 400px; overflow-y: auto;">
                    <?php $reprobacion_contador = 1; foreach ($data['reprobacion'] as $materia): ?>
                        <?php
                        $porcentaje = (float)$materia['porcentaje'];
                        $border_color = 'border-danger';
                        $progress_color = 'bg-danger';
                        if ($porcentaje >= 50) {
                            $border_color = 'border-danger';
                            $progress_color = 'bg-danger';
                        } elseif ($porcentaje >= 25) {
                            $border_color = 'border-warning';
                            $progress_color = 'bg-warning';
                        } else {
                            $border_color = 'border-success';
                            $progress_color = 'bg-success';
                        }
                        ?>
                        <div class="card mb-3 border-start <?= $border_color ?> border-3 shadow-sm">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="flex-grow-1">
                                        <div class="small text-muted mb-1">#<?= $reprobacion_contador ?></div>
                                        <h6 class="mb-0 fw-bold"><?= htmlspecialchars($materia['nombre']) ?></h6>
                                    </div>
                                    <div class="text-end">
                                        <div class="small text-muted">Cantidad</div>
                                        <div class="fw-bold"><?= $materia['cantidad_reprobados'] ?></div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small text-muted">Reprobación</span>
                                        <span class="fw-bold"><?= number_format($porcentaje, 2) ?>%</span>
                                    </div>
                                    <div class="progress" style="height: 24px;">
                                        <div class="progress-bar <?= $progress_color ?>" role="progressbar" 
                                             style="width: <?= min($porcentaje, 100) ?>%" 
                                             aria-valuenow="<?= $porcentaje ?>" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            <?= number_format($porcentaje, 2) ?>%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php $reprobacion_contador++; endforeach; ?>
                </div>
                
                <h6 class="mb-3 mt-4 fw-bold">Principales Causas</h6>
                <?php 
                $total_causas = array_sum(array_column($data['causas_reprobacion'], 'cantidad'));
                if ($total_causas == 0): 
                ?>
                    <div class="text-center text-muted py-3">
                        <p class="mb-0">No hay causas registradas</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($data['causas_reprobacion'] as $causa): ?>
                        <?php if ($causa['cantidad'] > 0): ?>
                            <div class="card mb-2 shadow-sm">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="small"><?= htmlspecialchars($causa['causa']) ?></span>
                                        <span class="badge bg-danger"><?= $causa['cantidad'] ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- 10. Asesorías -->
    <div class="card mb-3 mb-md-4 shadow-sm">
        <div class="card-header bg-info text-white py-2 py-md-3">
            <h5 class="mb-0 small d-sm-none">10. Asesorías</h5>
            <h5 class="mb-0 d-none d-sm-block">10. Asesorías</h5>
        </div>
        <div class="card-body p-2 p-md-3">
            <!-- Vista de tabla para desktop -->
            <div class="table-responsive d-none d-md-block" style="max-height: 300px; overflow-y: auto;">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="sticky-top bg-info text-white">
                        <tr>
                            <th class="small" style="width: 50px;">No.</th>
                            <th class="small">Asignatura</th>
                            <th class="small text-center" style="width: 70px;">Cant.</th>
                            <th class="small text-center" style="width: 70px;">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data['asesorias'])): ?>
                            <tr><td colspan="4" class="text-center text-muted small py-3">No hay asesorías registradas</td></tr>
                        <?php else: ?>
                            <?php $asesorias_contador = 1; foreach ($data['asesorias'] as $asesoria): ?>
                                <tr>
                                    <td class="small"><?= $asesorias_contador ?></td>
                                    <td class="small"><?= htmlspecialchars($asesoria['nombre']) ?></td>
                                    <td class="small text-center"><?= $asesoria['cantidad_asesorias'] ?></td>
                                    <td class="small text-center"><?= number_format((float)$asesoria['porcentaje'], 2) ?>%</td>
                                </tr>
                            <?php $asesorias_contador++; endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Vista de tarjetas para móvil -->
            <div class="d-md-none" style="max-height: 300px; overflow-y: auto;">
                <?php if (empty($data['asesorias'])): ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-info-circle" style="font-size: 2rem;"></i>
                        <p class="mt-2 mb-0">No hay asesorías registradas</p>
                    </div>
                <?php else: ?>
                    <?php $asesorias_contador = 1; foreach ($data['asesorias'] as $asesoria): ?>
                        <div class="card mb-3 border-start border-info border-3 shadow-sm">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="flex-grow-1">
                                        <div class="small text-muted mb-1">#<?= $asesorias_contador ?></div>
                                        <h6 class="mb-0 fw-bold"><?= htmlspecialchars($asesoria['nombre']) ?></h6>
                                    </div>
                                    <div class="text-end">
                                        <div class="small text-muted">Cantidad</div>
                                        <div class="fw-bold"><?= $asesoria['cantidad_asesorias'] ?></div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small text-muted">Asesorías</span>
                                        <span class="fw-bold"><?= number_format((float)$asesoria['porcentaje'], 2) ?>%</span>
                                    </div>
                                    <div class="progress" style="height: 24px;">
                                        <div class="progress-bar bg-info" role="progressbar" 
                                             style="width: <?= min((float)$asesoria['porcentaje'], 100) ?>%" 
                                             aria-valuenow="<?= $asesoria['porcentaje'] ?>" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            <?= number_format((float)$asesoria['porcentaje'], 2) ?>%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php $asesorias_contador++; endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos específicos para móvil en reportes */
@media (max-width: 767.98px) {
    .reporte-container .card {
        margin-bottom: 1rem !important;
    }
    
    .reporte-container .card-header {
        font-size: 0.875rem;
        padding: 0.75rem !important;
    }
    
    .reporte-container .card-header h5 {
        font-size: 0.875rem;
        margin: 0;
    }
    
    /* Estilos para las tarjetas móviles */
    .reporte-container .card-body .card {
        border-radius: 0.5rem;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .reporte-container .card-body .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
    }
    
    .reporte-container .card-body .card h6 {
        font-size: 0.9rem;
        line-height: 1.3;
    }
    
    .reporte-container .badge {
        font-size: 0.7rem;
        padding: 0.3rem 0.5rem;
        font-weight: 500;
    }
    
    /* Progress bars más visibles */
    .reporte-container .progress {
        border-radius: 0.5rem;
        background-color: #e9ecef;
    }
    
    .reporte-container .progress-bar {
        font-size: 0.75rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Ajustar espaciado de la información del parcial */
    .reporte-container .card-body .row > div {
        margin-bottom: 0.5rem;
    }
    
    /* Scroll suave para contenedores con scroll */
    .reporte-container [style*="overflow-y: auto"] {
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
    }
    
    .reporte-container [style*="overflow-y: auto"]::-webkit-scrollbar {
        width: 6px;
    }
    
    .reporte-container [style*="overflow-y: auto"]::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }
    
    .reporte-container [style*="overflow-y: auto"]::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }
}

/* Mejorar la legibilidad en móvil pequeño */
@media (max-width: 575.98px) {
    .reporte-container {
        font-size: 0.875rem;
    }
    
    .reporte-container h5, .reporte-container h6 {
        font-size: 0.875rem;
    }
    
    .reporte-container .card-body {
        padding: 0.75rem !important;
    }
    
    .reporte-container .card-body .card-body {
        padding: 0.75rem !important;
    }
    
    /* Mejorar los labels en la información del parcial */
    .reporte-container .small.text-muted {
        font-size: 0.7rem;
        display: block;
        margin-bottom: 0.25rem;
    }
    
    /* Espaciado más compacto */
    .reporte-container .mb-3 {
        margin-bottom: 0.75rem !important;
    }
}

/* Mejoras para pantallas muy pequeñas */
@media (max-width: 374.98px) {
    .reporte-container .card-header h5 {
        font-size: 0.8rem;
    }
    
    .reporte-container .card-body .card h6 {
        font-size: 0.85rem;
    }
    
    .reporte-container .badge {
        font-size: 0.65rem;
        padding: 0.25rem 0.4rem;
    }
    
    .reporte-container .progress-bar {
        font-size: 0.7rem;
    }
}
</style>

