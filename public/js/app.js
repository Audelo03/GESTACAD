/**
 * ARCHIVO PRINCIPAL DE JAVASCRIPT - GORA
 * 
 * Este archivo contiene las funciones principales de JavaScript
 * para la funcionalidad del sistema.
 */

// ========================================
// FUNCIONES DE NAVEGACIÓN Y UI
// ========================================

/**
 * Inicializa la aplicación cuando el DOM está listo
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Sistema GORA cargado correctamente');
    
    // Inicializar funcionalidades del sidebar
    initializeSidebar();
    
    // Inicializar tooltips de Bootstrap
    initializeTooltips();
    
    // Inicializar modales
    initializeModals();
    
    // Limpiar tooltips antes de salir de la página
    window.addEventListener('beforeunload', function() {
        cleanupAllTooltips();
    });
    
    // Limpiar tooltips cuando se detecta navegación
    window.addEventListener('pagehide', function() {
        cleanupAllTooltips();
    });
    
    // Limpiar tooltips cuando se detecta cambio de visibilidad
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'hidden') {
            cleanupAllTooltips();
        }
    });
    
    // Limpiar tooltips cuando se detecta navegación del navegador
    window.addEventListener('popstate', function() {
        cleanupAllTooltips();
    });
    
    // Limpiar tooltips periódicamente para casos edge
    setInterval(function() {
        const visibleTooltips = document.querySelectorAll('.tooltip.show');
        if (visibleTooltips.length > 0) {
            cleanupAllTooltips();
        }
    }, 5000);
});

/**
 * Inicializa la funcionalidad del sidebar
 */
function initializeSidebar() {
    const toggleBtn = document.getElementById('btn-toggle-sidebar');
    const mobileMenuBtn = document.getElementById('btn-mobile-menu');
    const sidebar = document.getElementById('app-sidebar');
    const content = document.getElementById('app-content');
    const overlay = document.querySelector('.sidebar-overlay');
    const mobileMenuIcon = mobileMenuBtn ? mobileMenuBtn.querySelector('i') : null;
    const bottomNavManage = document.getElementById('bottom-nav-manage');
    const bottomNavConfig = document.getElementById('bottom-nav-config');
    
    // Función para detectar si estamos en móvil
    function isMobile() {
        return window.innerWidth <= 991.98;
    }
    
    // Función para abrir sidebar en móvil
    function openMobileSidebar() {
        if (sidebar) {
            sidebar.classList.add('mobile-open');
            if (overlay) {
                overlay.classList.add('show');
            }
            // Prevenir scroll del body cuando el sidebar está abierto
            document.body.style.overflow = 'hidden';
        }
        if (mobileMenuIcon) {
            mobileMenuIcon.classList.remove('bi-list');
            mobileMenuIcon.classList.add('bi-x');
        }
    }
    
    // Función para cerrar sidebar en móvil
    function closeMobileSidebar() {
        if (sidebar) {
            sidebar.classList.remove('mobile-open');
            if (overlay) {
                overlay.classList.remove('show');
            }
            // Restaurar scroll del body
            document.body.style.overflow = '';
        }
        if (mobileMenuIcon) {
            mobileMenuIcon.classList.remove('bi-x');
            mobileMenuIcon.classList.add('bi-list');
        }
    }
    
    // Función para toggle sidebar (desktop)
    function toggleDesktopSidebar() {
        if (sidebar && content) {
            sidebar.classList.toggle('collapsed');
            content.classList.toggle('collapsed');
        }
    }
    
    // Event listener para botón toggle (desktop)
    if (toggleBtn && sidebar && content) {
        toggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (!isMobile()) {
                toggleDesktopSidebar();
            } else {
                // En móvil, el botón interno cierra el sidebar
                closeMobileSidebar();
            }
        });
    }
    
    // Event listener para botón hamburguesa (móvil)
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (sidebar && sidebar.classList.contains('mobile-open')) {
                closeMobileSidebar();
            } else {
                openMobileSidebar();
            }
        });
    }

    if (bottomNavManage) {
        bottomNavManage.addEventListener('click', function(e) {
            e.preventDefault();
            openMobileSidebar();
        });
    }

    if (bottomNavConfig) {
        bottomNavConfig.addEventListener('click', function(e) {
            e.preventDefault();
            openMobileSidebar();
        });
    }
    
    // Cerrar sidebar al hacer clic en el overlay
    if (overlay) {
        overlay.addEventListener('click', function() {
            closeMobileSidebar();
        });
    }
    
    // Cerrar sidebar al hacer clic fuera en móviles
    if (content) {
        content.addEventListener('click', function(e) {
            if (isMobile() && sidebar && sidebar.classList.contains('mobile-open')) {
                // Solo cerrar si no se hace clic en el sidebar mismo
                if (!sidebar.contains(e.target)) {
                    closeMobileSidebar();
                }
            }
        });
    }
    
    // Cerrar sidebar al hacer clic en enlaces (móvil)
    const sidebarLinks = sidebar ? sidebar.querySelectorAll('a.nav-link') : [];
    sidebarLinks.forEach(function(link) {
        link.addEventListener('click', function() {
            // Cerrar sidebar en móvil después de un pequeño delay para permitir la navegación
            if (isMobile()) {
                setTimeout(function() {
                    closeMobileSidebar();
                }, 300);
            }
            
            // Limpiar tooltips
            cleanupAllTooltips();
            setTimeout(function() {
                cleanupAllTooltips();
            }, 100);
        });
        
        // Limpiar tooltips al salir del elemento
        link.addEventListener('mouseleave', function() {
            const tooltip = bootstrap.Tooltip.getInstance(link);
            if (tooltip) {
                tooltip.hide();
            }
        });
        
        // Limpiar tooltips al perder el foco
        link.addEventListener('blur', function() {
            const tooltip = bootstrap.Tooltip.getInstance(link);
            if (tooltip) {
                tooltip.hide();
            }
        });
    });
    
    // Manejar cambio de tamaño de ventana
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            // Si cambiamos de móvil a desktop, asegurar que el sidebar esté en estado correcto
            if (!isMobile()) {
                closeMobileSidebar();
                // En desktop, el sidebar debe estar colapsado por defecto si tiene la clase
                if (sidebar && sidebar.classList.contains('collapsed')) {
                    if (content) {
                        content.classList.add('collapsed');
                    }
                }
            } else {
                // Si cambiamos a móvil, cerrar el sidebar
                closeMobileSidebar();
            }
        }, 250);
    });
    
    // Cerrar sidebar con tecla Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isMobile() && sidebar && sidebar.classList.contains('mobile-open')) {
            closeMobileSidebar();
        }
    });
}

/**
 * Limpia todos los tooltips existentes
 */
function cleanupAllTooltips() {
    const allTooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    allTooltips.forEach(function(element) {
        const tooltip = bootstrap.Tooltip.getInstance(element);
        if (tooltip) {
            tooltip.hide();
            tooltip.dispose();
        }
    });
    
    // Limpiar también cualquier tooltip que pueda estar en el DOM
    const existingTooltipElements = document.querySelectorAll('.tooltip');
    existingTooltipElements.forEach(function(element) {
        element.remove();
    });
}

/**
 * Inicializa los tooltips de Bootstrap
 */
function initializeTooltips() {
    // Primero, eliminar todos los tooltips existentes para evitar bugs
    cleanupAllTooltips();
    
    // Luego, inicializar todos los tooltips nuevamente
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Inicializa los modales de Bootstrap
 */
function initializeModals() {
    // Cerrar modales automáticamente después de operaciones exitosas
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function() {
            // Limpiar formularios cuando se cierra el modal
            const forms = modal.querySelectorAll('form');
            forms.forEach(form => form.reset());
        });
    });
}

// ========================================
// FUNCIONES DE UTILIDAD
// ========================================

/**
 * Muestra una notificación de éxito
 * @param {string} message - Mensaje a mostrar
 */
function showSuccess(message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: message,
            timer: 3000,
            showConfirmButton: false
        });
    } else {
        alert('Éxito: ' + message);
    }
}

/**
 * Muestra una notificación de error
 * @param {string} message - Mensaje a mostrar
 */
function showError(message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message
        });
    } else {
        alert('Error: ' + message);
    }
}

/**
 * Muestra una notificación de confirmación
 * @param {string} message - Mensaje a mostrar
 * @param {function} callback - Función a ejecutar si se confirma
 */
function showConfirm(message, callback) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: '¿Estás seguro?',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, continuar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed && callback) {
                callback();
            }
        });
    } else {
        if (confirm(message)) {
            callback();
        }
    }
}

/**
 * Formatea una fecha para mostrar
 * @param {string} dateString - Fecha en formato string
 * @returns {string} - Fecha formateada
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    });
}

/**
 * Valida un email
 * @param {string} email - Email a validar
 * @returns {boolean} - True si es válido, false si no
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Valida que un campo no esté vacío
 * @param {string} value - Valor a validar
 * @returns {boolean} - True si no está vacío, false si está vacío
 */
function isNotEmpty(value) {
    return value && value.trim().length > 0;
}

// ========================================
// FUNCIONES DE PAGINACIÓN
// ========================================

/**
 * Crea los controles de paginación
 * @param {number} currentPage - Página actual
 * @param {number} totalPages - Total de páginas
 * @param {function} onPageChange - Función a ejecutar al cambiar página
 */
function createPagination(currentPage, totalPages, onPageChange) {
    const paginationContainer = document.getElementById('pagination');
    if (!paginationContainer) return;
    
    let html = '<nav><ul class="pagination justify-content-center">';
    
    // Botón anterior
    html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" data-page="${currentPage - 1}">Anterior</a>
    </li>`;
    
    // Números de página
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
            <a class="page-link" href="#" data-page="${i}">${i}</a>
        </li>`;
    }
    
    // Botón siguiente
    html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
        <a class="page-link" href="#" data-page="${currentPage + 1}">Siguiente</a>
    </li>`;
    
    html += '</ul></nav>';
    
    paginationContainer.innerHTML = html;
    
    // Agregar event listeners
    paginationContainer.addEventListener('click', function(e) {
        e.preventDefault();
        if (e.target.classList.contains('page-link')) {
            const page = parseInt(e.target.dataset.page);
            if (page >= 1 && page <= totalPages && page !== currentPage) {
                onPageChange(page);
            }
        }
    });
}

// ========================================
// FUNCIONES DE BÚSQUEDA
// ========================================

/**
 * Implementa búsqueda con debounce
 * @param {function} searchFunction - Función a ejecutar para buscar
 * @param {number} delay - Delay en milisegundos (por defecto 300)
 */
function setupSearchWithDebounce(searchFunction, delay = 300) {
    let timeoutId;
    
    return function(event) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => {
            searchFunction(event.target.value);
        }, delay);
    };
}

// ========================================
// FUNCIONES DE EXPORTACIÓN
// ========================================

/**
 * Exporta datos a CSV
 * @param {Array} data - Datos a exportar
 * @param {string} filename - Nombre del archivo
 */
function exportToCSV(data, filename) {
    if (!data || data.length === 0) {
        showError('No hay datos para exportar');
        return;
    }
    
    const headers = Object.keys(data[0]);
    const csvContent = [
        headers.join(','),
        ...data.map(row => headers.map(header => `"${row[header] || ''}"`).join(','))
    ].join('\n');
    
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', filename);
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

/**
 * Exporta un gráfico a PNG
 * @param {string} canvasId - ID del canvas del gráfico
 * @param {string} filename - Nombre del archivo
 */
function exportChartToPNG(canvasId, filename) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) {
        showError('No se encontró el gráfico para exportar');
        return;
    }
    
    const link = document.createElement('a');
    link.download = filename;
    link.href = canvas.toDataURL();
    link.click();
}
