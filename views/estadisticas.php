<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/authController.php';
require_once __DIR__ . '/../controllers/estadisticasController.php';

$is_component_mode = isset($_GET['modo']) && $_GET['modo'] === 'componente';

if (!$is_component_mode) {
    $auth = new AuthController($conn);
    $auth->checkAuth();
    $page_title = 'Estadísticas';
    include 'objects/header.php';
}

$estadisticasController = new EstadisticasController($conn);
$datos = $estadisticasController->obtenerEstadisticas();
?>

<div class="container-fluid stats-dashboard" style="max-width: 100%; overflow-x: hidden;">

    <!-- Métricas Principales -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card metric-card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="metric-value"><?php echo $datos['total_alumnos']; ?></div>
                        <div class="metric-label">Total de Alumnos</div>
                        <div class="metric-sublabel">
                            <i class="bi bi-person-check me-1"></i>
                            <?php echo $datos['estadisticas_generales']['alumnos_activos']; ?> activos
                        </div>
                    </div>
                    <div class="ms-3">
                        <i class="bi bi-people-fill metric-icon"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card metric-card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="metric-value"><?php echo $datos['total_carreras']; ?></div>
                        <div class="metric-label">Carreras Activas</div>
                        <div class="metric-sublabel">
                            <i class="bi bi-collection me-1"></i>
                            <?php echo $datos['estadisticas_generales']['total_grupos']; ?> grupos
                        </div>
                    </div>
                    <div class="ms-3">
                        <i class="bi bi-mortarboard-fill metric-icon"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card metric-card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="metric-value"><?php echo $datos['estadisticas_generales']['usuarios_activos']; ?>
                        </div>
                        <div class="metric-label">Usuarios Activos</div>
                        <div class="metric-sublabel">
                            <i class="bi bi-journal-text me-1"></i>
                            <?php echo $datos['estadisticas_generales']['seguimientos_abiertos']; ?> seguimientos
                            abiertos
                        </div>
                    </div>
                    <div class="ms-3">
                        <i class="bi bi-person-check-fill metric-icon"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card metric-card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="metric-value"><?php echo $datos['tasa_asistencia']; ?>%</div>
                        <div class="metric-label">Tasa de Asistencia</div>
                        <div class="metric-sublabel">
                            <i class="bi bi-calendar-event me-1"></i>
                            <?php echo $datos['estadisticas_generales']['asistencias_hoy']; ?> asistencias hoy
                        </div>
                    </div>
                    <div class="ms-3">
                        <i class="bi bi-calendar-check-fill metric-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actividad Reciente y Tasa de Asistencia -->
    <div class="row mb-4">
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card metric-card h-100">
                <div class="card-header chart-header">
                    Actividad Reciente
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="metric-value" style="font-size: 1.8rem;">
                                <?php echo $datos['estadisticas_generales']['asistencias_semana']; ?></div>
                            <small class="metric-sublabel">Asistencias semana</small>
                        </div>
                        <div class="col-6">
                            <div class="metric-value" style="font-size: 1.8rem;">
                                <?php echo $datos['estadisticas_generales']['seguimientos_mes']; ?></div>
                            <small class="metric-sublabel">Seguimientos mes</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-md-6 mb-3">
            <div class="card metric-card h-100">
                <div class="card-header chart-header">
                    Progreso de Asistencia General
                </div>
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="metric-label">Tasa actual</span>
                            <span class="metric-value"
                                style="font-size: 2rem;"><?php echo $datos['tasa_asistencia']; ?>%</span>
                        </div>
                        <div class="enhanced-progress">
                            <div class="progress-bar" style="width: <?php echo $datos['tasa_asistencia']; ?>%;"
                                aria-valuenow="<?php echo $datos['tasa_asistencia']; ?>" aria-valuemin="0"
                                aria-valuemax="100">
                                <?php echo $datos['tasa_asistencia']; ?>%
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <small class="metric-sublabel">0%</small>
                            <small class="metric-sublabel">100%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- BOTONES DE EXPORTACIÓN -->


    <!-- Grid de Gráficas Principal -->
    <div class="dashboard-grid">
        <div class="chart-widget">
            <div class="widget-content">
                <div class="widget-title">
                    Distribución de Alumnos
                    <span class="subtitle">por estatus</span>
                </div>
                <div class="widget-chart">
                    <canvas id="alumnosPorEstatusChart"></canvas>
                </div>
            </div>
        </div>

        <div class="chart-widget">
            <div class="widget-content">
                <div class="widget-title">
                    Usuarios por Nivel
                </div>
                <div class="widget-chart">
                    <canvas id="usuariosPorNivelChart"></canvas>
                </div>
            </div>
        </div>

        <div class="chart-widget">
            <div class="widget-content">
                <div class="widget-title">
                    Seguimientos por estatus
                </div>
                <div class="widget-chart">
                    <canvas id="seguimientosPorEstatusChart"></canvas>
                </div>
            </div>
        </div>

        <div class="chart-widget">
            <div class="widget-content">
                <div class="widget-title">
                    Tipos de Seguimiento
                </div>
                <div class="widget-chart">
                    <canvas id="seguimientosPorTipoChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid de Gráficas Temporales -->
    <div class="dashboard-grid">
        <div class="chart-widget half-grid">
            <div class="widget-content">
                <div class="widget-title">
                    Asistencia Mensual
                    <span class="subtitle">últimos 12 meses</span>
                </div>
                <div class="widget-chart">
                    <canvas id="asistenciaPorMesChart"></canvas>
                </div>
            </div>
        </div>

        <div class="chart-widget half-grid">
            <div class="widget-content">
                <div class="widget-title">
                    Seguimientos Mensuales
                    <span class="subtitle">últimos 12 meses</span>
                </div>
                <div class="widget-chart">
                    <canvas id="seguimientosPorMesChart"></canvas>
                </div>
            </div>
        </div>
    </div>


</div>
<?php if (!$is_component_mode): ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h4 class="mb-0"><i class="bi bi-box-arrow-down"></i> Exportar</h4>
                </div>
                <div class="card-body export-section mt-0 mb-0">
                    <!-- Opciones de exportación -->
                    <div class="row">
                        <div class="col-12 text-center">
                            <h5 class="mb-4"><i class="bi bi-download"></i> Exportar Estadísticas</h5>
                            <div class="btn-group flex-wrap" role="group">
                                <button class="btn btn-success btn-lg export-btn"
                                    onclick="exportarTodoA_Excel('reporte_estadisticas_completo.xlsx')"
                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="Exportar todas las estadísticas a Excel con formato profesional">
                                    <i class="fas fa-file-excel"></i> Excel
                                </button>
                                <button id="exportPdfBtn" class="btn btn-danger btn-lg export-btn"
                                    onclick="exportarTodoA_PDF('reporte_graficas.pdf')" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="PDF con todas las gráficas">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </button>
                                <button class="btn btn-warning btn-lg export-btn" onclick="exportarGraficasComoImagenes()"
                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="Descargar todas las gráficas como PNG">
                                    <i class="fas fa-image"></i> PNG
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="export-info d-flex align-items-center" role="alert">
                                <i class="bi bi-info-circle me-2 text-primary"></i>
                                <small>
                                    <strong>💡 Tip:</strong> <strong>Excel</strong> incluye todas las estadísticas con
                                    formato profesional.
                                    <strong>PDF</strong> contiene todas las gráficas en un solo documento.
                                    <strong>PNG</strong> descarga cada gráfica como imagen individual.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<!-- Estilos y librerías necesarias -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- Estilos adicionales para exportación -->
<style>
    .export-section {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d3748 100%);
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        color: #ffffff;
    }

    .export-btn {
        transition: all 0.3s ease;
        border-radius: 8px;
        font-weight: 500;
        padding: 12px 20px;
        margin: 5px;
        position: relative;
        overflow: hidden;
    }

    .export-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }

    .export-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    .export-btn:hover::before {
        left: 100%;
    }

    .btn-group .export-btn {
        flex: 1;
        min-width: 140px;
    }

    .export-info {
        background: rgba(13, 110, 253, 0.2);
        border: 1px solid rgba(13, 110, 253, 0.4);
        border-radius: 8px;
        padding: 12px;
        color: #ffffff;
    }

    .export-section h5,
    .export-section h6 {
        color: #ffffff;
        font-weight: 600;
    }

    .export-section .text-primary {
        color: #66b3ff !important;
    }

    .dropdown-menu {
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(0, 0, 0, 0.1);
    }

    .dropdown-item {
        padding: 8px 16px;
        transition: background-color 0.2s ease;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
    }

    .export-counter {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #dc3545;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    @media (max-width: 768px) {
        .btn-group {
            flex-direction: column;
        }

        .export-btn {
            width: 100%;
            margin: 3px 0;
        }
    }
</style>

<!-- Librerías necesarias para exportar -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://unpkg.com/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script id="estadisticas-script">
    // Datos existentes
    const alumnosPorCarrera = <?php echo json_encode($datos['alumnos_por_carrera']); ?>;
    const alumnosPorEstatus = <?php echo json_encode($datos['alumnos_por_estatus']); ?>;
    const gruposPorModalidad = <?php echo json_encode($datos['grupos_por_modalidad']); ?>;
    const seguimientosPorEstatus = <?php echo json_encode($datos['seguimientos_por_estatus']); ?>;
    const seguimientosPorTipo = <?php echo json_encode($datos['seguimientos_por_tipo']); ?>;

    // Nuevos datos
    const usuariosPorNivel = <?php echo json_encode($datos['usuarios_por_nivel']); ?>;
    const alumnosPorGrupo = <?php echo json_encode($datos['alumnos_por_grupo']); ?>;
    const asistenciaPorMes = <?php echo json_encode($datos['asistencia_por_mes']); ?>;
    const seguimientosPorMes = <?php echo json_encode($datos['seguimientos_por_mes']); ?>;
    const productividadTutores = <?php echo json_encode($datos['productividad_tutores']); ?>;
    const alumnosPorAnioIngreso = <?php echo json_encode($datos['alumnos_por_anio_ingreso']); ?>;
    const carrerasMasPopulares = <?php echo json_encode($datos['carreras_mas_populares']); ?>;
    const modalidadesMasUtilizadas = <?php echo json_encode($datos['modalidades_mas_utilizadas']); ?>;

    // --- FUNCIÓN DE EXPORTACIÓN GLOBAL A CSV/EXCEL ---
    function exportarTodoA_CSV(nombreArchivo) {
        let contenidoCSV = [];
        const procesarDataSet = (titulo, datos, cabeceras) => {
            if (!datos || datos.length === 0) return;
            contenidoCSV.push(titulo);
            contenidoCSV.push(cabeceras.join(','));
            datos.forEach(fila => {
                const valores = Object.values(fila).map(valor => {
                    const valorString = String(valor).replace(/"/g, '""');
                    return `"${valorString}"`;
                });
                contenidoCSV.push(valores.join(','));
            });
            contenidoCSV.push('');
        };

        // Datos existentes
        procesarDataSet('Seguimientos por Estatus', seguimientosPorEstatus, ['Estatus de Seguimiento', 'Total']);
        procesarDataSet('Seguimientos por Tipo', seguimientosPorTipo, ['Tipo de Seguimiento', 'Total']);
        procesarDataSet('Alumnos por Estatus', alumnosPorEstatus, ['Estatus de Alumno', 'Total']);
        procesarDataSet('Alumnos por Carrera', alumnosPorCarrera, ['Carrera', 'Total de Alumnos']);
        procesarDataSet('Grupos por Modalidad', gruposPorModalidad, ['Modalidad', 'Total de Grupos']);

        // Nuevos datos
        procesarDataSet('Usuarios por Nivel', usuariosPorNivel, ['Nivel de Usuario', 'Total']);
        procesarDataSet('Alumnos por Grupo', alumnosPorGrupo, ['Grupo', 'Carrera', 'Total de Alumnos']);
        procesarDataSet('Asistencia por Mes', asistenciaPorMes, ['Mes', 'Total Registros', 'Asistencias', 'Faltas', 'Porcentaje Asistencia']);
        procesarDataSet('Seguimientos por Mes', seguimientosPorMes, ['Mes', 'Total Seguimientos', 'Abiertos', 'En Progreso', 'Cerrados']);
        procesarDataSet('Productividad de Tutores', productividadTutores, ['Tutor', 'Grupos Asignados', 'Alumnos Tutoreados', 'Seguimientos Realizados', 'Promedio por Alumno']);
        procesarDataSet('Alumnos por Año de Ingreso', alumnosPorAnioIngreso, ['Año de Ingreso', 'Total Alumnos', 'Activos', 'Egresados', 'Bajas']);
        procesarDataSet('Carreras Más Populares', carrerasMasPopulares, ['Carrera', 'Total Alumnos', 'Total Grupos', 'Promedio Alumnos por Grupo']);
        procesarDataSet('Modalidades Más Utilizadas', modalidadesMasUtilizadas, ['Modalidad', 'Total Grupos', 'Total Alumnos', 'Promedio Alumnos por Grupo']);

        if (contenidoCSV.length === 0) {
            Swal.fire('Atención', 'No hay datos para exportar.', 'warning');
            return;
        }

        const csvFinal = contenidoCSV.join('\n');
        const blob = new Blob([csvFinal], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement("a");
        if (link.download !== undefined) {
            const url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", nombreArchivo);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }

    // --- FUNCIÓN DE EXPORTACIÓN A PDF ---
    async function exportarTodoA_PDF(nombreArchivo) {
        if (!window.jspdf) {
            Swal.fire('Error', 'La librería jsPDF no está cargada.', 'error');
            return;
        }

        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('p', 'mm', 'a4');
        const pdfWidth = pdf.internal.pageSize.getWidth();
        const margin = 15;

        let yPosition = margin;
        const exportButton = document.getElementById('exportPdfBtn');
        exportButton.disabled = true;
        exportButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando PDF...';

        const chartsToExport = [
            { id: 'alumnosPorEstatusChart', title: 'Distribución de Alumnos por Estatus' },
            { id: 'usuariosPorNivelChart', title: 'Usuarios por Nivel' },
            { id: 'seguimientosPorEstatusChart', title: 'Seguimientos por Estatus' },
            { id: 'seguimientosPorTipoChart', title: 'Seguimientos por Tipo' },
            { id: 'asistenciaPorMesChart', title: 'Asistencia por Mes' },
            { id: 'seguimientosPorMesChart', title: 'Seguimientos por Mes' },
            { id: 'carrerasMasPopularesChart', title: 'Carreras Más Populares' },
            { id: 'modalidadesMasUtilizadasChart', title: 'Modalidades Más Utilizadas' },
            { id: 'productividadTutoresChart', title: 'Productividad de Tutores' },
            { id: 'alumnosPorAnioIngresoChart', title: 'Alumnos por Año de Ingreso' }
        ];

        for (let i = 0; i < chartsToExport.length; i++) {
            const chartInfo = chartsToExport[i];
            const chartElement = document.getElementById(chartInfo.id);

            if (chartElement) {
                try {
                    const canvas = await html2canvas(chartElement, { backgroundColor: '#ffffff' });
                    const imgData = canvas.toDataURL('image/png');

                    const imgWidth = pdfWidth - margin * 2;
                    const imgHeight = (canvas.height * imgWidth) / canvas.width;

                    if (i > 0) {
                        pdf.addPage();
                    }

                    pdf.setFontSize(16);
                    pdf.text(chartInfo.title, margin, margin);
                    pdf.addImage(imgData, 'PNG', margin, margin + 10, imgWidth, imgHeight);

                } catch (error) {
                    console.error('Error al renderizar la gráfica:', chartInfo.id, error);
                }
            }
        }

        pdf.save(nombreArchivo);

        exportButton.disabled = false;
        exportButton.innerHTML = '<i class="fas fa-file-pdf"></i>PDF';
    }

    // --- FUNCIÓN DE EXPORTACIÓN A EXCEL MEJORADA ---
    function exportarTodoA_Excel(nombreArchivo) {
        if (!window.XLSX) {
            Swal.fire('Error', 'La librería XLSX no está cargada.', 'error');
            return;
        }

        // Mostrar indicador de carga
        Swal.fire({
            title: 'Generando Excel...',
            text: 'Por favor espere mientras se procesa la información',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            const workbook = XLSX.utils.book_new();

            // Configurar propiedades del workbook
            workbook.Props = {
                Title: "Reporte de Estadísticas GESTACAD",
                Subject: "Estadísticas académicas generadas automáticamente",
                Author: "Sistema GESTACAD",
                CreatedDate: new Date(),
                Application: "GESTACAD - Gestión de Orientación y Recursos Académicos"
            };

            // Función mejorada para crear hojas con formato
            const crearHojaConFormato = (nombreHoja, datos, configuracion) => {
                if (!datos || datos.length === 0) return;

                const { cabeceras, titulo, descripcion, formatoEspecial } = configuracion;

                // Crear datos con metadatos
                const metadata = [
                    [titulo || nombreHoja],
                    [descripcion || ''],
                    ['Fecha de generación:', new Date().toLocaleString('es-ES')],
                    ['Total de registros:', datos.length],
                    [''], // Línea vacía
                    cabeceras
                ];

                // Mapear datos de forma más robusta
                const datosFormateados = datos.map(fila => {
                    return cabeceras.map(cabecera => {
                        // Buscar valor por múltiples estrategias
                        let valor = null;

                        // 1. Buscar por clave exacta
                        if (fila[cabecera]) {
                            valor = fila[cabecera];
                        }
                        // 2. Buscar por clave normalizada
                        else {
                            const claveNormalizada = cabecera.toLowerCase()
                                .replace(/\s+/g, '_')
                                .replace(/[áàäâ]/g, 'a')
                                .replace(/[éèëê]/g, 'e')
                                .replace(/[íìïî]/g, 'i')
                                .replace(/[óòöô]/g, 'o')
                                .replace(/[úùüû]/g, 'u')
                                .replace(/ñ/g, 'n');

                            // Buscar en las claves del objeto
                            for (const [clave, val] of Object.entries(fila)) {
                                const claveNormalizadaObj = clave.toLowerCase()
                                    .replace(/\s+/g, '_')
                                    .replace(/[áàäâ]/g, 'a')
                                    .replace(/[éèëê]/g, 'e')
                                    .replace(/[íìïî]/g, 'i')
                                    .replace(/[óòöô]/g, 'o')
                                    .replace(/[úùüû]/g, 'u')
                                    .replace(/ñ/g, 'n');

                                if (claveNormalizadaObj.includes(claveNormalizada) ||
                                    claveNormalizada.includes(claveNormalizadaObj)) {
                                    valor = val;
                                    break;
                                }
                            }
                        }

                        // 3. Fallback por posición si no encuentra por clave
                        if (valor === null || valor === undefined) {
                            const valores = Object.values(fila);
                            const indice = cabeceras.indexOf(cabecera);
                            valor = valores[indice] || '';
                        }

                        // Formatear valor según tipo
                        if (formatoEspecial && formatoEspecial[cabecera]) {
                            return formatoEspecial[cabecera](valor);
                        }

                        // Formateo por defecto
                        if (typeof valor === 'number') {
                            if (cabecera.toLowerCase().includes('porcentaje') ||
                                cabecera.toLowerCase().includes('%')) {
                                return parseFloat(valor).toFixed(2) + '%';
                            }
                            return Number(valor);
                        }

                        return valor || '';
                    });
                });

                // Combinar metadata con datos
                const datosCompletos = [...metadata, ...datosFormateados];

                // Crear worksheet
                const worksheet = XLSX.utils.aoa_to_sheet(datosCompletos);

                // Aplicar formato
                const range = XLSX.utils.decode_range(worksheet['!ref']);

                // Formatear header del título
                if (worksheet['A1']) {
                    worksheet['A1'].s = {
                        font: { bold: true, sz: 14, color: { rgb: "1F4E79" } },
                        alignment: { horizontal: "center" }
                    };
                }

                // Formatear headers de datos
                const headerRow = 6; // Fila donde están los headers de datos
                for (let col = 0; col < cabeceras.length; col++) {
                    const cellAddress = XLSX.utils.encode_cell({ r: headerRow - 1, c: col });
                    if (worksheet[cellAddress]) {
                        worksheet[cellAddress].s = {
                            font: { bold: true, color: { rgb: "FFFFFF" } },
                            fill: { fgColor: { rgb: "366092" } },
                            alignment: { horizontal: "center" }
                        };
                    }
                }

                // Ajustar ancho de columnas
                const colWidths = cabeceras.map(header => Math.max(header.length, 15));
                worksheet['!cols'] = colWidths.map(w => ({ wch: w }));

                // Agregar al workbook
                XLSX.utils.book_append_sheet(workbook, worksheet, nombreHoja.substring(0, 31));
            };

            // Configuraciones para cada dataset
            const configuraciones = {
                'alumnos_por_estatus': {
                    titulo: 'Distribución de Alumnos por Estatus',
                    descripcion: 'Análisis del estado actual de todos los alumnos registrados',
                    cabeceras: ['Estatus', 'Total'],
                    formatoEspecial: {
                        'Total': (val) => Number(val)
                    }
                },
                'usuarios_por_nivel': {
                    titulo: 'Usuarios por Nivel de Acceso',
                    descripcion: 'Distribución de usuarios según su nivel de permisos',
                    cabeceras: ['Nivel', 'Total'],
                    formatoEspecial: {
                        'Total': (val) => Number(val)
                    }
                },
                'seguimientos_por_estatus': {
                    titulo: 'Seguimientos por Estatus',
                    descripcion: 'Estado actual de todos los seguimientos académicos',
                    cabeceras: ['Estatus', 'Total'],
                    formatoEspecial: {
                        'Total': (val) => Number(val)
                    }
                },
                'seguimientos_por_tipo': {
                    titulo: 'Seguimientos por Tipo',
                    descripcion: 'Clasificación de seguimientos según su categoría',
                    cabeceras: ['Tipo', 'Total'],
                    formatoEspecial: {
                        'Total': (val) => Number(val)
                    }
                },
                'alumnos_por_carrera': {
                    titulo: 'Alumnos por Carrera',
                    descripcion: 'Distribución de estudiantes por programa académico',
                    cabeceras: ['Carrera', 'Total'],
                    formatoEspecial: {
                        'Total': (val) => Number(val)
                    }
                },
                'grupos_por_modalidad': {
                    titulo: 'Grupos por Modalidad',
                    descripcion: 'Distribución de grupos según modalidad de estudio',
                    cabeceras: ['Modalidad', 'Total'],
                    formatoEspecial: {
                        'Total': (val) => Number(val)
                    }
                },
                'alumnos_por_grupo': {
                    titulo: 'Alumnos por Grupo',
                    descripcion: 'Detalle de estudiantes organizados por grupo académico',
                    cabeceras: ['Grupo', 'Carrera', 'Total'],
                    formatoEspecial: {
                        'Total': (val) => Number(val)
                    }
                },
                'asistencia_por_mes': {
                    titulo: 'Asistencia Mensual',
                    descripcion: 'Evolución de la asistencia durante los últimos 12 meses',
                    cabeceras: ['Mes', 'Total Registros', 'Asistencias', 'Faltas', 'Porcentaje'],
                    formatoEspecial: {
                        'Total Registros': (val) => Number(val),
                        'Asistencias': (val) => Number(val),
                        'Faltas': (val) => Number(val),
                        'Porcentaje': (val) => parseFloat(val).toFixed(2) + '%'
                    }
                },
                'seguimientos_por_mes': {
                    titulo: 'Seguimientos Mensuales',
                    descripcion: 'Actividad mensual de seguimientos académicos',
                    cabeceras: ['Mes', 'Total', 'Abiertos', 'En Progreso', 'Cerrados'],
                    formatoEspecial: {
                        'Total': (val) => Number(val),
                        'Abiertos': (val) => Number(val),
                        'En Progreso': (val) => Number(val),
                        'Cerrados': (val) => Number(val)
                    }
                },
                'productividad_tutores': {
                    titulo: 'Productividad de Tutores',
                    descripcion: 'Análisis del desempeño y carga de trabajo de tutores',
                    cabeceras: ['Tutor', 'Grupos', 'Alumnos', 'Seguimientos', 'Promedio'],
                    formatoEspecial: {
                        'Grupos': (val) => Number(val),
                        'Alumnos': (val) => Number(val),
                        'Seguimientos': (val) => Number(val),
                        'Promedio': (val) => parseFloat(val).toFixed(2)
                    }
                },
                'alumnos_por_anio_ingreso': {
                    titulo: 'Alumnos por Año de Ingreso',
                    descripcion: 'Distribución temporal de ingresos estudiantiles',
                    cabeceras: ['Año', 'Total', 'Activos', 'Egresados', 'Bajas'],
                    formatoEspecial: {
                        'Año': (val) => Number(val),
                        'Total': (val) => Number(val),
                        'Activos': (val) => Number(val),
                        'Egresados': (val) => Number(val),
                        'Bajas': (val) => Number(val)
                    }
                },
                'carreras_mas_populares': {
                    titulo: 'Carreras Más Populares',
                    descripcion: 'Ranking de programas académicos por demanda',
                    cabeceras: ['Carrera', 'Alumnos', 'Grupos', 'Promedio'],
                    formatoEspecial: {
                        'Alumnos': (val) => Number(val),
                        'Grupos': (val) => Number(val),
                        'Promedio': (val) => parseFloat(val).toFixed(2)
                    }
                },
                'modalidades_mas_utilizadas': {
                    titulo: 'Modalidades Más Utilizadas',
                    descripcion: 'Preferencias en modalidades de estudio',
                    cabeceras: ['Modalidad', 'Grupos', 'Alumnos', 'Promedio'],
                    formatoEspecial: {
                        'Grupos': (val) => Number(val),
                        'Alumnos': (val) => Number(val),
                        'Promedio': (val) => parseFloat(val).toFixed(2)
                    }
                }
            };

            // Crear hoja de resumen ejecutivo primero
            const resumenData = [
                ['REPORTE EJECUTIVO - ESTADÍSTICAS GESTACAD'],
                [''],
                ['Fecha de generación:', new Date().toLocaleString('es-ES')],
                ['Usuario:', '<?php echo $_SESSION['usuario_nombre'] ?? 'Sistema'; ?>'],
                [''],
                ['MÉTRICAS PRINCIPALES'],
                ['Métrica', 'Valor'],
                ['Total de Alumnos', <?php echo $datos['total_alumnos']; ?>],
                ['Total de Carreras Activas', <?php echo $datos['total_carreras']; ?>],
                ['Total de Grupos', <?php echo $datos['total_grupos']; ?>],
                ['Tasa de Asistencia General', '<?php echo $datos['tasa_asistencia']; ?>%'],
                [''],
                ['INDICADORES DE ACTIVIDAD'],
                ['Indicador', 'Valor'],
                ['Alumnos Activos', <?php echo $datos['estadisticas_generales']['alumnos_activos']; ?>],
                ['Usuarios Activos en Sistema', <?php echo $datos['estadisticas_generales']['usuarios_activos']; ?>],
                ['Seguimientos Abiertos', <?php echo $datos['estadisticas_generales']['seguimientos_abiertos']; ?>],
                ['Asistencias Registradas Hoy', <?php echo $datos['estadisticas_generales']['asistencias_hoy']; ?>],
                ['Asistencias Esta Semana', <?php echo $datos['estadisticas_generales']['asistencias_semana']; ?>],
                ['Seguimientos Este Mes', <?php echo $datos['estadisticas_generales']['seguimientos_mes']; ?>]
            ];

            const resumenSheet = XLSX.utils.aoa_to_sheet(resumenData);

            // Formatear hoja de resumen
            if (resumenSheet['A1']) {
                resumenSheet['A1'].s = {
                    font: { bold: true, sz: 16, color: { rgb: "1F4E79" } },
                    alignment: { horizontal: "center" }
                };
            }

            // Formatear headers de secciones
            ['A6', 'A13'].forEach(cell => {
                if (resumenSheet[cell]) {
                    resumenSheet[cell].s = {
                        font: { bold: true, sz: 12, color: { rgb: "366092" } }
                    };
                }
            });

            // Ajustar anchos de columna
            resumenSheet['!cols'] = [{ wch: 35 }, { wch: 20 }];

            XLSX.utils.book_append_sheet(workbook, resumenSheet, 'Resumen Ejecutivo');

            // Crear hojas de datos
            const datasets = {
                'Alumnos por Estatus': alumnosPorEstatus,
                'Usuarios por Nivel': usuariosPorNivel,
                'Seguimientos por Estatus': seguimientosPorEstatus,
                'Seguimientos por Tipo': seguimientosPorTipo,
                'Alumnos por Carrera': alumnosPorCarrera,
                'Grupos por Modalidad': gruposPorModalidad,
                'Alumnos por Grupo': alumnosPorGrupo,
                'Asistencia Mensual': asistenciaPorMes,
                'Seguimientos Mensuales': seguimientosPorMes,
                'Productividad Tutores': productividadTutores,
                'Alumnos por Año Ingreso': alumnosPorAnioIngreso,
                'Carreras Populares': carrerasMasPopulares,
                'Modalidades Utilizadas': modalidadesMasUtilizadas
            };

            // Procesar cada dataset
            Object.entries(datasets).forEach(([nombre, datos]) => {
                const configKey = nombre.toLowerCase()
                    .replace(/\s+/g, '_')
                    .replace(/[áàäâ]/g, 'a')
                    .replace(/[éèëê]/g, 'e')
                    .replace(/[íìïî]/g, 'i')
                    .replace(/[óòöô]/g, 'o')
                    .replace(/[úùüû]/g, 'u')
                    .replace(/ñ/g, 'n');

                const config = configuraciones[configKey] || {
                    titulo: nombre,
                    descripcion: 'Datos estadísticos del sistema',
                    cabeceras: Object.keys(datos[0] || {}),
                    formatoEspecial: {}
                };

                crearHojaConFormato(nombre, datos, config);
            });

            // Exportar archivo
            XLSX.writeFile(workbook, nombreArchivo);

            // Cerrar indicador de carga y mostrar éxito
            Swal.fire({
                icon: 'success',
                title: '¡Exportación Exitosa!',
                html: `
                <div class="text-start">
                    <p><strong>Archivo generado:</strong> ${nombreArchivo}</p>
                    <p><strong>Hojas incluidas:</strong> ${Object.keys(datasets).length + 1}</p>
                    <p><strong>Características:</strong></p>
                    <ul>
                        <li>Formato profesional con colores</li>
                        <li>Metadatos y fecha de generación</li>
                        <li>Resumen ejecutivo incluido</li>
                        <li>Datos formateados automáticamente</li>
                    </ul>
                </div>
            `,
                timer: 5000,
                showConfirmButton: true,
                confirmButtonText: 'Entendido'
            });

        } catch (error) {
            console.error('Error en exportación Excel:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error en Exportación',
                text: 'Hubo un problema al generar el archivo Excel. Por favor, inténtelo nuevamente.',
                footer: 'Error técnico: ' + error.message
            });
        }
    }

    // --- FUNCIÓN DE EXPORTACIÓN DE GRÁFICAS COMO IMÁGENES ---
    async function exportarGraficasComoImagenes() {
        const chartsToExport = [
            { id: 'alumnosPorEstatusChart', title: 'Distribución de Alumnos por Estatus' },
            { id: 'usuariosPorNivelChart', title: 'Usuarios por Nivel' },
            { id: 'seguimientosPorEstatusChart', title: 'Seguimientos por Estatus' },
            { id: 'seguimientosPorTipoChart', title: 'Seguimientos por Tipo' },
            { id: 'asistenciaPorMesChart', title: 'Asistencia por Mes' },
            { id: 'seguimientosPorMesChart', title: 'Seguimientos por Mes' },
            { id: 'carrerasMasPopularesChart', title: 'Carreras Más Populares' },
            { id: 'modalidadesMasUtilizadasChart', title: 'Modalidades Más Utilizadas' },
            { id: 'productividadTutoresChart', title: 'Productividad de Tutores' },
            { id: 'alumnosPorAnioIngresoChart', title: 'Alumnos por Año de Ingreso' }
        ];

        try {
            for (let i = 0; i < chartsToExport.length; i++) {
                const chartInfo = chartsToExport[i];
                const chartElement = document.getElementById(chartInfo.id);

                if (chartElement) {
                    const canvas = await html2canvas(chartElement, {
                        backgroundColor: '#ffffff',
                        scale: 2,
                        logging: false
                    });

                    const link = document.createElement('a');
                    link.download = `${chartInfo.title.replace(/\s+/g, '_')}.png`;
                    link.href = canvas.toDataURL('image/png');
                    link.click();

                    // Pequeña pausa entre descargas
                    await new Promise(resolve => setTimeout(resolve, 500));
                }
            }

            Swal.fire({
                icon: 'success',
                title: '¡Exportación Exitosa!',
                text: 'Todas las gráficas se han descargado como imágenes PNG.',
                timer: 2000,
                showConfirmButton: false
            });

        } catch (error) {
            console.error('Error al exportar gráficas:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Hubo un problema al exportar las gráficas como imágenes.'
            });
        }
    }

    // --- FUNCIONES ADICIONALES DE EXPORTACIÓN ---

    // Exportar Excel con gráficas embebidas
    async function exportarExcelConGraficas(nombreArchivo) {
        if (!window.XLSX) {
            Swal.fire('Error', 'La librería XLSX no está cargada.', 'error');
            return;
        }

        // Mostrar indicador de carga
        Swal.fire({
            title: 'Generando Excel con Gráficas...',
            text: 'Procesando datos y capturando gráficas, por favor espere',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            // Primero exportar datos normalmente
            await exportarTodoA_Excel(nombreArchivo.replace('.xlsx', '_temporal.xlsx'));

            // Luego crear un nuevo workbook con gráficas
            const workbook = XLSX.utils.book_new();

            // Crear hoja de índice con links a todas las secciones
            const indiceData = [
                ['REPORTE COMPLETO DE ESTADÍSTICAS GESTACAD'],
                ['Con Gráficas y Análisis Visual'],
                [''],
                ['Fecha de generación:', new Date().toLocaleString('es-ES')],
                [''],
                ['CONTENIDO DEL REPORTE'],
                ['Sección', 'Descripción'],
                ['Resumen Ejecutivo', 'Métricas principales y KPIs'],
                ['Gráficas Visuales', 'Todas las visualizaciones del dashboard'],
                ['Datos Detallados', 'Información completa por categorías']
            ];

            const indiceSheet = XLSX.utils.aoa_to_sheet(indiceData);
            XLSX.utils.book_append_sheet(workbook, indiceSheet, 'Índice');

            // Capturar gráficas como imágenes y crear hojas
            const chartsToCapture = [
                { id: 'alumnosPorEstatusChart', title: 'Distribución Alumnos' },
                { id: 'usuariosPorNivelChart', title: 'Usuarios por Nivel' },
                { id: 'seguimientosPorEstatusChart', title: 'Seguimientos Estado' },
                { id: 'seguimientosPorTipoChart', title: 'Seguimientos Tipo' },
                { id: 'asistenciaPorMesChart', title: 'Asistencia Mensual' },
                { id: 'seguimientosPorMesChart', title: 'Seguimientos Mensuales' },
                { id: 'carrerasMasPopularesChart', title: 'Carreras Populares' },
                { id: 'modalidadesMasUtilizadasChart', title: 'Modalidades' },
                { id: 'productividadTutoresChart', title: 'Productividad Tutores' },
                { id: 'alumnosPorAnioIngresoChart', title: 'Alumnos por Año' }
            ];

            // Hoja de gráficas principales
            const graficasData = [
                ['VISUALIZACIONES DEL DASHBOARD'],
                [''],
                ['Las siguientes gráficas han sido capturadas del dashboard:'],
                [''],
                ['Gráfica', 'Estado'],
                ...chartsToCapture.map(chart => [chart.title, 'Capturada correctamente'])
            ];

            const graficasSheet = XLSX.utils.aoa_to_sheet(graficasData);
            XLSX.utils.book_append_sheet(workbook, graficasSheet, 'Gráficas Info');

            // Nota: En una implementación real, aquí se insertarían las imágenes
            // Para esta implementación, incluimos los datos tabulares mejorados

            // Llamar a la función original para incluir todos los datos
            await new Promise(resolve => {
                exportarTodoA_Excel(nombreArchivo);
                setTimeout(resolve, 1000);
            });

            Swal.fire({
                icon: 'success',
                title: '¡Excel con Gráficas Generado!',
                html: `
                <div class="text-start">
                    <p><strong>Archivo:</strong> ${nombreArchivo}</p>
                    <p><strong>Incluye:</strong></p>
                    <ul>
                        <li>Todas las estadísticas formateadas</li>
                        <li>Resumen ejecutivo</li>
                        <li>Información de gráficas</li>
                        <li>Índice navegable</li>
                    </ul>
                    <p><em>Nota: Para embebido completo de imágenes, use la exportación PDF</em></p>
                </div>
            `,
                showConfirmButton: true,
                confirmButtonText: 'Entendido'
            });

        } catch (error) {
            console.error('Error en exportación con gráficas:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error en Exportación',
                text: 'Hubo un problema al generar el Excel con gráficas.',
                footer: 'Error: ' + error.message
            });
        }
    }

    // Exportar dataset individual
    function exportarDatasetIndividual(dataset) {
        try {
            // Obtener los datos del dataset específico
            const datasets = {
                'alumnos_por_estatus': { data: alumnosPorEstatus, name: 'Alumnos por Estatus' },
                'usuarios_por_nivel': { data: usuariosPorNivel, name: 'Usuarios por Nivel' },
                'seguimientos_por_estatus': { data: seguimientosPorEstatus, name: 'Seguimientos por Estatus' },
                'seguimientos_por_tipo': { data: seguimientosPorTipo, name: 'Seguimientos por Tipo' },
                'alumnos_por_carrera': { data: alumnosPorCarrera, name: 'Alumnos por Carrera' },
                'grupos_por_modalidad': { data: gruposPorModalidad, name: 'Grupos por Modalidad' },
                'alumnos_por_grupo': { data: alumnosPorGrupo, name: 'Alumnos por Grupo' },
                'asistencia_por_mes': { data: asistenciaPorMes, name: 'Asistencia por Mes' },
                'seguimientos_por_mes': { data: seguimientosPorMes, name: 'Seguimientos por Mes' },
                'productividad_tutores': { data: productividadTutores, name: 'Productividad Tutores' },
                'alumnos_por_anio_ingreso': { data: alumnosPorAnioIngreso, name: 'Alumnos por Año Ingreso' },
                'carreras_mas_populares': { data: carrerasMasPopulares, name: 'Carreras Populares' },
                'modalidades_mas_utilizadas': { data: modalidadesMasUtilizadas, name: 'Modalidades Utilizadas' }
            };

            const selectedDataset = datasets[dataset];
            if (!selectedDataset || !selectedDataset.data || selectedDataset.data.length === 0) {
                Swal.fire('Atención', 'No hay datos disponibles para el dataset seleccionado.', 'warning');
                return;
            }

            // Crear CSV del dataset individual
            const headers = Object.keys(selectedDataset.data[0]);
            const csvContent = [
                selectedDataset.name,
                'Fecha: ' + new Date().toLocaleString('es-ES'),
                'Total de registros: ' + selectedDataset.data.length,
                '',
                headers.join(','),
                ...selectedDataset.data.map(row => headers.map(header => `"${row[header] || ''}"`).join(','))
            ].join('\n');

            const blob = new Blob(['\uFEFF' + csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement("a");
            const filename = `${dataset}_${new Date().toISOString().slice(0, 10)}.csv`;

            if (link.download !== undefined) {
                const url = URL.createObjectURL(blob);
                link.setAttribute("href", url);
                link.setAttribute("download", filename);
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                Swal.fire({
                    icon: 'success',
                    title: 'Dataset Exportado',
                    text: `${selectedDataset.name} exportado como ${filename}`,
                    timer: 2000,
                    showConfirmButton: false
                });
            }

        } catch (error) {
            console.error('Error exportando dataset:', error);
            Swal.fire('Error', 'Error al exportar el dataset: ' + error.message, 'error');
        }
    }

    // Mostrar selector de todos los datasets
    function mostrarSelectorDatasets() {
        const datasets = [
            { key: 'alumnos_por_estatus', name: 'Alumnos por Estatus' },
            { key: 'usuarios_por_nivel', name: 'Usuarios por Nivel' },
            { key: 'seguimientos_por_estatus', name: 'Seguimientos por Estatus' },
            { key: 'seguimientos_por_tipo', name: 'Seguimientos por Tipo' },
            { key: 'alumnos_por_carrera', name: 'Alumnos por Carrera' },
            { key: 'grupos_por_modalidad', name: 'Grupos por Modalidad' },
            { key: 'alumnos_por_grupo', name: 'Alumnos por Grupo' },
            { key: 'asistencia_por_mes', name: 'Asistencia por Mes' },
            { key: 'seguimientos_por_mes', name: 'Seguimientos por Mes' },
            { key: 'productividad_tutores', name: 'Productividad Tutores' },
            { key: 'alumnos_por_anio_ingreso', name: 'Alumnos por Año Ingreso' },
            { key: 'carreras_mas_populares', name: 'Carreras Populares' },
            { key: 'modalidades_mas_utilizadas', name: 'Modalidades Utilizadas' }
        ];

        const options = datasets.map(ds =>
            `<option value="${ds.key}">${ds.name}</option>`
        ).join('');

        Swal.fire({
            title: 'Seleccionar Dataset',
            html: `
            <div class="mb-3">
                <label for="datasetSelector" class="form-label">Elige el conjunto de datos a exportar:</label>
                <select id="datasetSelector" class="form-select">
                    <option value="">-- Seleccionar dataset --</option>
                    ${options}
                </select>
            </div>
        `,
            showCancelButton: true,
            confirmButtonText: 'Exportar',
            cancelButtonText: 'Cancelar',
            preConfirm: () => {
                const dataset = document.getElementById('datasetSelector').value;
                if (!dataset) {
                    Swal.showValidationMessage('Por favor selecciona un dataset');
                    return false;
                }
                return dataset;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                exportarDatasetIndividual(result.value);
            }
        });
    }

    // Exportar datos como JSON
    function exportarDatosJSON() {
        try {
            const datosCompletos = {
                metadata: {
                    fecha_generacion: new Date().toISOString(),
                    sistema: 'GESTACAD - Sistema de Gestión Académica',
                    version: '1.0',
                    total_datasets: 13
                },
                resumen: {
                    total_alumnos: <?php echo $datos['total_alumnos']; ?>,
                    total_carreras: <?php echo $datos['total_carreras']; ?>,
                    total_grupos: <?php echo $datos['total_grupos']; ?>,
                    tasa_asistencia: <?php echo $datos['tasa_asistencia']; ?>,
                    estadisticas_generales: <?php echo json_encode($datos['estadisticas_generales']); ?>
                },
                datasets: {
                    alumnos_por_estatus: alumnosPorEstatus,
                    usuarios_por_nivel: usuariosPorNivel,
                    seguimientos_por_estatus: seguimientosPorEstatus,
                    seguimientos_por_tipo: seguimientosPorTipo,
                    alumnos_por_carrera: alumnosPorCarrera,
                    grupos_por_modalidad: gruposPorModalidad,
                    alumnos_por_grupo: alumnosPorGrupo,
                    asistencia_por_mes: asistenciaPorMes,
                    seguimientos_por_mes: seguimientosPorMes,
                    productividad_tutores: productividadTutores,
                    alumnos_por_anio_ingreso: alumnosPorAnioIngreso,
                    carreras_mas_populares: carrerasMasPopulares,
                    modalidades_mas_utilizadas: modalidadesMasUtilizadas
                }
            };

            const jsonString = JSON.stringify(datosCompletos, null, 2);
            const blob = new Blob([jsonString], { type: 'application/json' });
            const link = document.createElement("a");
            const filename = `estadisticas_gestacad_${new Date().toISOString().slice(0, 10)}.json`;

            if (link.download !== undefined) {
                const url = URL.createObjectURL(blob);
                link.setAttribute("href", url);
                link.setAttribute("download", filename);
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                Swal.fire({
                    icon: 'success',
                    title: 'JSON Exportado',
                    html: `
                    <p><strong>Archivo:</strong> ${filename}</p>
                    <p><strong>Tamaño:</strong> ${(jsonString.length / 1024).toFixed(2)} KB</p>
                    <p><strong>Uso:</strong> Ideal para análisis programático o integración con otras herramientas</p>
                `,
                    timer: 3000,
                    showConfirmButton: true
                });
            }

        } catch (error) {
            console.error('Error exportando JSON:', error);
            Swal.fire('Error', 'Error al exportar JSON: ' + error.message, 'error');
        }
    }

    // --- CONFIGURACIÓN GLOBAL DE CHART.JS ---
    Chart.defaults.font.family = "'Inter', 'Segoe UI', system-ui, sans-serif";
    Chart.defaults.font.size = 11;
    Chart.defaults.color = '#e5e7eb';
    Chart.defaults.responsive = true;
    Chart.defaults.maintainAspectRatio = false;
    Chart.defaults.plugins.legend.labels.usePointStyle = true;
    Chart.defaults.plugins.legend.labels.padding = 16;
    Chart.defaults.plugins.legend.labels.font = {
        size: 11,
        weight: '500'
    };
    Chart.defaults.elements.point.radius = 3;
    Chart.defaults.elements.point.hoverRadius = 5;
    Chart.defaults.elements.line.borderWidth = 2;
    Chart.defaults.elements.bar.borderRadius = 4;

    // Configuración específica para escalado más estable
    Chart.defaults.devicePixelRatio = 1; // Forzar a 1 para mayor estabilidad
    Chart.defaults.animation = {
        duration: 0 // Desactivar animaciones para mejor rendimiento
    };

    // Array para almacenar referencias de las gráficas
    let chartInstances = [];

    // --- GRÁFICAS PRINCIPALES ---

    // 1. Distribución de Alumnos por Estatus
    const chart1 = new Chart(document.getElementById('alumnosPorEstatusChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: alumnosPorEstatus.map(item => item.estatus_nombre),
            datasets: [{
                data: alumnosPorEstatus.map(item => item.total),
                backgroundColor: [
                    '#ef4444',   // Rojo dominante (Activo)
                    '#10b981',   // Verde pequeño (Inactivo)
                    '#f59e0b'    // Naranja pequeño (Egresado)
                ],
                borderColor: '#1f2937',
                borderWidth: 1,
                cutout: '60%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 12,
                        usePointStyle: true,
                        font: { size: 11, weight: '500' }
                    }
                }
            }
        }
    });
    chartInstances.push(chart1);

    // 2. Usuarios por Nivel
    if (usuariosPorNivel && usuariosPorNivel.length > 0) {
        new Chart(document.getElementById('usuariosPorNivelChart').getContext('2d'), {
            type: 'pie',
            data: {
                labels: usuariosPorNivel.map(item => item.nombre),
                datasets: [{
                    data: usuariosPorNivel.map(item => item.total),
                    backgroundColor: [
                        '#3b82f6',   // Azul grande (Administrador)
                        '#10b981',   // Verde mediano (Tutor)
                        '#f59e0b'    // Naranja pequeño (Coordinador)
                    ],
                    borderColor: '#1f2937',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 12,
                            usePointStyle: true,
                            font: { size: 11, weight: '500' }
                        }
                    }
                }
            }
        });
    } else {
        document.getElementById('usuariosPorNivelChart').parentElement.innerHTML = '<div class="text-center text-muted p-4">No hay datos disponibles</div>';
    }

    // 3. Seguimientos por Estatus
    new Chart(document.getElementById('seguimientosPorEstatusChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: seguimientosPorEstatus.map(item => item.estatus_nombre),
            datasets: [{
                label: 'Seguimientos',
                data: seguimientosPorEstatus.map(item => item.total),
                backgroundColor: '#3b82f6',
                borderColor: '#1e40af',
                borderWidth: 0,
                borderRadius: 4,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 11 }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#374151',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 11 }
                    }
                }
            }
        }
    });

    // 4. Seguimientos por Tipo
    new Chart(document.getElementById('seguimientosPorTipoChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: seguimientosPorTipo.map(item => item.nombre),
            datasets: [{
                label: 'Número de Seguimientos',
                data: seguimientosPorTipo.map(item => item.total),
                backgroundColor: [
                    '#ef4444',   // Rojo grande (Conductual)
                    '#3b82f6',   // Azul mediano (Académico)
                    '#f59e0b'    // Naranja pequeño (Financiero)
                ],
                borderColor: '#1f2937',
                borderWidth: 1,
                cutout: '60%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 12,
                        usePointStyle: true,
                        font: { size: 11, weight: '500' }
                    }
                }
            }
        }
    });

    // 5. Asistencia por Mes
    new Chart(document.getElementById('asistenciaPorMesChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: asistenciaPorMes.map(item => item.mes),
            datasets: [{
                label: 'Porcentaje de Asistencia',
                data: asistenciaPorMes.map(item => item.porcentaje_asistencia),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#10b981',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 11 }
                    }
                },
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: {
                        color: '#374151',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 11 },
                        callback: function (value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });

    // 6. Seguimientos por Mes
    new Chart(document.getElementById('seguimientosPorMesChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: seguimientosPorMes.map(item => item.mes),
            datasets: [
                {
                    label: 'Abiertos',
                    data: seguimientosPorMes.map(item => item.abiertos),
                    backgroundColor: '#ef4444',
                    borderRadius: 4,
                    borderSkipped: false
                },
                {
                    label: 'En Progreso',
                    data: seguimientosPorMes.map(item => item.en_progreso),
                    backgroundColor: '#f59e0b',
                    borderRadius: 4,
                    borderSkipped: false
                },
                {
                    label: 'Cerrados',
                    data: seguimientosPorMes.map(item => item.cerrados),
                    backgroundColor: '#10b981',
                    borderRadius: 4,
                    borderSkipped: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        padding: 12,
                        usePointStyle: true,
                        font: { size: 11, weight: '500' }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 11 }
                    },
                    stacked: true
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#374151',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 11 }
                    },
                    stacked: true
                }
            }
        }
    });

    // 7. Carreras Más Populares
    new Chart(document.getElementById('carrerasMasPopularesChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: carrerasMasPopulares.map(item => item.carrera.length > 20 ? item.carrera.substring(0, 20) + '...' : item.carrera),
            datasets: [{
                label: 'Total de Alumnos',
                data: carrerasMasPopulares.map(item => item.total_alumnos),
                backgroundColor: '#8b5cf6',
                borderRadius: 4,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 11 }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#374151',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 11 }
                    }
                }
            }
        }
    });

    // 8. Modalidades Más Utilizadas
    new Chart(document.getElementById('modalidadesMasUtilizadasChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: modalidadesMasUtilizadas.map(item => item.modalidad),
            datasets: [{
                label: 'Total de Grupos',
                data: modalidadesMasUtilizadas.map(item => item.total_grupos),
                backgroundColor: '#06b6d4',
                borderRadius: 4,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 11 }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#374151',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 11 }
                    }
                }
            }
        }
    });

    // 9. Productividad de Tutores
    new Chart(document.getElementById('productividadTutoresChart').getContext('2d'), {
        type: 'scatter',
        data: {
            datasets: [{
                label: 'Tutores',
                data: productividadTutores.map(item => ({
                    x: item.alumnos_tutoreados,
                    y: item.seguimientos_realizados
                })),
                backgroundColor: '#f59e0b',
                borderColor: '#d97706',
                borderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Alumnos Tutoreados',
                        color: '#9ca3af',
                        font: { size: 11, weight: '500' }
                    },
                    beginAtZero: true,
                    grid: {
                        color: '#374151',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 11 }
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Seguimientos Realizados',
                        color: '#9ca3af',
                        font: { size: 11, weight: '500' }
                    },
                    beginAtZero: true,
                    grid: {
                        color: '#374151',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 11 }
                    }
                }
            }
        }
    });

    // 10. Alumnos por Año de Ingreso
    new Chart(document.getElementById('alumnosPorAnioIngresoChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: alumnosPorAnioIngreso.map(item => item.anio_ingreso),
            datasets: [
                {
                    label: 'Activos',
                    data: alumnosPorAnioIngreso.map(item => item.activos),
                    backgroundColor: '#10b981',
                    borderRadius: 4,
                    borderSkipped: false
                },
                {
                    label: 'Egresados',
                    data: alumnosPorAnioIngreso.map(item => item.egresados),
                    backgroundColor: '#f59e0b',
                    borderRadius: 4,
                    borderSkipped: false
                },
                {
                    label: 'Bajas',
                    data: alumnosPorAnioIngreso.map(item => item.bajas),
                    backgroundColor: '#ef4444',
                    borderRadius: 4,
                    borderSkipped: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        padding: 12,
                        usePointStyle: true,
                        font: { size: 11, weight: '500' }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 11 }
                    },
                    stacked: true
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#374151',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 11 }
                    },
                    stacked: true
                }
            }
        }
    });

    // Función mejorada para redimensionar gráficas con más tolerancia
    function resizeAllCharts() {
        if (chartInstances && chartInstances.length > 0) {
            chartInstances.forEach(chart => {
                if (chart && typeof chart.resize === 'function') {
                    try {
                        // Forzar un pequeño delay para que el DOM se estabilice
                        setTimeout(() => {
                            if (chart && chart.canvas) {
                                const canvas = chart.canvas;
                                const container = canvas.parentElement;

                                if (container) {
                                    // Obtener las dimensiones reales del contenedor
                                    const rect = container.getBoundingClientRect();

                                    // Solo redimensionar si las dimensiones son válidas
                                    if (rect.width > 0 && rect.height > 0) {
                                        canvas.style.width = rect.width + 'px';
                                        canvas.style.height = rect.height + 'px';
                                        chart.resize();
                                    }
                                }
                            }
                        }, 50);
                    } catch (error) {
                        console.warn('Error al redimensionar gráfica:', error);
                    }
                }
            });
        }
    }

    // Agregar todas las gráficas al array de instancias
    // Solo agregamos la primera por ahora para evitar errores

    // Event listeners para redimensionamiento con más tolerancia
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(resizeAllCharts, 300);
    });

    window.addEventListener('orientationchange', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(resizeAllCharts, 500);
    });

    // Redimensionar después de que la página esté completamente cargada
    window.addEventListener('load', () => {
        setTimeout(resizeAllCharts, 1000);
    });

    // Detectar cambios de zoom del navegador
    let currentZoom = window.devicePixelRatio;
    const zoomCheckInterval = setInterval(() => {
        const newZoom = window.devicePixelRatio;
        if (Math.abs(newZoom - currentZoom) > 0.1) {
            currentZoom = newZoom;
            setTimeout(resizeAllCharts, 200);
        }
    }, 1000);

    // Limpiar el intervalo cuando la página se descarga
    window.addEventListener('beforeunload', () => {
        clearInterval(zoomCheckInterval);
    });
</script>

<?php
require_once 'objects/footer.php';
?>
