/**
 * Tutorías - Group and Individual Tutoring Management
 */

(function() {
    'use strict';

    // Modal instances
    let modalGrupalInstance = null;
    let modalIndividualInstance = null;

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        initTutoriaButtons();
        initTutoriaForms();
        setDefaultDates();
        initModalCleanup();
    });

    /**
     * Initialize button click handlers for opening modals
     */
    function initTutoriaButtons() {
        // Group tutoring buttons
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-tutoria-grupal')) {
                e.preventDefault();
                const btn = e.target.closest('.btn-tutoria-grupal');
                const grupoId = btn.dataset.grupoId;
                const grupoNombre = btn.dataset.grupoNombre;
                openModalTutoriaGrupal(grupoId, grupoNombre);
            }
        });

        // Individual tutoring buttons
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-tutoria-individual')) {
                e.preventDefault();
                const btn = e.target.closest('.btn-tutoria-individual');
                const grupoId = btn.dataset.grupoId;
                const grupoNombre = btn.dataset.grupoNombre;
                openModalTutoriaIndividual(grupoId, grupoNombre);
            }
        });
    }

    /**
     * Initialize form submission handlers
     */
    function initTutoriaForms() {
        // Group tutoring form
        const formGrupal = document.getElementById('formTutoriaGrupal');
        if (formGrupal) {
            formGrupal.addEventListener('submit', function(e) {
                e.preventDefault();
                submitTutoriaGrupal(this);
            });
        }

        // Individual tutoring form
        const formIndividual = document.getElementById('formTutoriaIndividual');
        if (formIndividual) {
            formIndividual.addEventListener('submit', function(e) {
                e.preventDefault();
                submitTutoriaIndividual(this);
            });
        }
    }

    /**
     * Set default dates to today
     */
    function setDefaultDates() {
        const today = new Date().toISOString().split('T')[0];
        const grupalFecha = document.getElementById('grupal-fecha');
        const individualFecha = document.getElementById('individual-fecha');
        
        if (grupalFecha) grupalFecha.value = today;
        if (individualFecha) individualFecha.value = today;
    }

    /**
     * Initialize modal cleanup handlers
     */
    function initModalCleanup() {
        const modalGrupal = document.getElementById('modalTutoriaGrupal');
        const modalIndividual = document.getElementById('modalTutoriaIndividual');

        if (modalGrupal) {
            modalGrupal.addEventListener('hidden.bs.modal', function() {
                // Remove any remaining backdrop
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(backdrop => backdrop.remove());
                // Remove modal-open class from body
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            });
        }

        if (modalIndividual) {
            modalIndividual.addEventListener('hidden.bs.modal', function() {
                // Remove any remaining backdrop
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(backdrop => backdrop.remove());
                // Remove modal-open class from body
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            });
        }
    }

    /**
     * Open group tutoring modal
     */
    function openModalTutoriaGrupal(grupoId, grupoNombre) {
        // Set group info
        document.getElementById('grupal-grupo-id').value = grupoId;
        document.getElementById('grupal-grupo-nombre').textContent = grupoNombre;

        // Load students for attendance
        loadAlumnosForGrupal(grupoId);

        // Get or create modal instance
        const modalElement = document.getElementById('modalTutoriaGrupal');
        if (!modalGrupalInstance) {
            modalGrupalInstance = new bootstrap.Modal(modalElement);
        }
        modalGrupalInstance.show();
    }

    /**
     * Open individual tutoring modal
     */
    function openModalTutoriaIndividual(grupoId, grupoNombre) {
        // Set group info
        document.getElementById('individual-grupo-id').value = grupoId;
        document.getElementById('individual-grupo-nombre').textContent = grupoNombre;

        // Load students for selection
        loadAlumnosForIndividual(grupoId);

        // Get or create modal instance
        const modalElement = document.getElementById('modalTutoriaIndividual');
        if (!modalIndividualInstance) {
            modalIndividualInstance = new bootstrap.Modal(modalElement);
        }
        modalIndividualInstance.show();
    }

    /**
     * Load students for group tutoring attendance
     */
    function loadAlumnosForGrupal(grupoId) {
        const container = document.getElementById('grupal-lista-alumnos');
        container.innerHTML = '<div class="text-center text-muted"><div class="spinner-border spinner-border-sm me-2" role="status"><span class="visually-hidden">Cargando...</span></div>Cargando alumnos...</div>';

        fetch(`controllers/tutoriasController.php?action=getAlumnosByGrupo&grupo_id=${grupoId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    let html = '<div class="list-group list-group-flush">';
                    data.data.forEach(alumno => {
                        html += `
                            <div class="list-group-item">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="asistencia[${alumno.id_alumno}]" value="1" id="asist_${alumno.id_alumno}">
                                    <label class="form-check-label" for="asist_${alumno.id_alumno}">
                                        ${alumno.nombre} ${alumno.apellido_paterno} ${alumno.apellido_materno}
                                        <small class="text-muted d-block">${alumno.matricula}</small>
                                    </label>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                    container.innerHTML = html;
                } else {
                    container.innerHTML = '<div class="alert alert-warning mb-0">No hay alumnos en este grupo</div>';
                }
            })
            .catch(error => {
                console.error('Error loading students:', error);
                container.innerHTML = '<div class="alert alert-danger mb-0">Error al cargar los alumnos</div>';
            });
    }

    /**
     * Load students for individual tutoring selection
     */
    function loadAlumnosForIndividual(grupoId) {
        const select = document.getElementById('individual-alumno-id');
        select.innerHTML = '<option value="">Cargando...</option>';

        fetch(`controllers/tutoriasController.php?action=getAlumnosByGrupo&grupo_id=${grupoId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    let options = '<option value="">Seleccione un alumno...</option>';
                    data.data.forEach(alumno => {
                        options += `<option value="${alumno.id_alumno}">${alumno.nombre} ${alumno.apellido_paterno} ${alumno.apellido_materno} - ${alumno.matricula}</option>`;
                    });
                    select.innerHTML = options;
                } else {
                    select.innerHTML = '<option value="">No hay alumnos disponibles</option>';
                }
            })
            .catch(error => {
                console.error('Error loading students:', error);
                select.innerHTML = '<option value="">Error al cargar alumnos</option>';
            });
    }

    /**
     * Submit group tutoring form
     */
    function submitTutoriaGrupal(form) {
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        
        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        fetch('controllers/tutoriasController.php?action=createGrupal', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showAlert('success', data.message || 'Tutoría grupal creada exitosamente');
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalTutoriaGrupal'));
                modal.hide();
                
                // Reset form
                form.reset();
                setDefaultDates();
            } else {
                showAlert('danger', data.message || 'Error al crear la tutoría grupal');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'Error al procesar la solicitud');
        })
        .finally(() => {
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-save me-1"></i>Guardar Tutoría Grupal';
        });
    }

    /**
     * Submit individual tutoring form
     */
    function submitTutoriaIndividual(form) {
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        
        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        fetch('controllers/tutoriasController.php?action=createIndividual', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showAlert('success', data.message || 'Tutoría individual creada exitosamente');
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalTutoriaIndividual'));
                modal.hide();
                
                // Reset form
                form.reset();
                setDefaultDates();
            } else {
                showAlert('danger', data.message || 'Error al crear la tutoría individual');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'Error al procesar la solicitud');
        })
        .finally(() => {
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-save me-1"></i>Guardar Tutoría Individual';
        });
    }

    /**
     * Show alert message
     */
    function showAlert(type, message) {
        // Create alert element
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
        alert.style.zIndex = '9999';
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Add to body
        document.body.appendChild(alert);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 150);
        }, 5000);
    }

})();
