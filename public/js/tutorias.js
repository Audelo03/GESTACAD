/**
 * Tutorías - Group and Individual Tutoring Management
 */

(function () {
    'use strict';

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function () {
        initTutoriaButtons();
        initTutoriaForms();
        setDefaultDates();
        initIndividualTutoriaSelects();
    });

    /**
     * Initialize button click handlers for opening modals
     */
    function initTutoriaButtons() {
        // Group tutoring buttons
        document.addEventListener('click', function (e) {
            if (e.target.closest('.btn-tutoria-grupal')) {
                e.preventDefault();
                const btn = e.target.closest('.btn-tutoria-grupal');
                const grupoId = btn.dataset.grupoId;
                const grupoNombre = btn.dataset.grupoNombre;
                // Verificar si existe una lista para hoy antes de abrir
                openModalTutoriaGrupalToday(grupoId, grupoNombre);
            }
            
            // Button for today's tutoring (check if exists)
            if (e.target.closest('.btn-tutoria-grupal-today')) {
                e.preventDefault();
                const btn = e.target.closest('.btn-tutoria-grupal-today');
                const grupoId = btn.dataset.grupoId;
                const grupoNombre = btn.dataset.grupoNombre;
                openModalTutoriaGrupalToday(grupoId, grupoNombre);
            }
        });

        // Individual tutoring buttons
        document.addEventListener('click', function (e) {
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
            formGrupal.addEventListener('submit', function (e) {
                e.preventDefault();
                submitTutoriaGrupal(this);
            });
        }

        // Individual tutoring form
        const formIndividual = document.getElementById('formTutoriaIndividual');
        if (formIndividual) {
            formIndividual.addEventListener('submit', function (e) {
                e.preventDefault();
                submitTutoriaIndividual(this);
            });
        }
    }

    /**
     * Set default dates to today and make them readonly
     */
    function setDefaultDates() {
        const today = new Date().toISOString().split('T')[0];
        const grupalFecha = document.getElementById('grupal-fecha');
        const individualFecha = document.getElementById('individual-fecha');
        
        if (grupalFecha) {
            grupalFecha.value = today;
            grupalFecha.setAttribute('readonly', 'readonly');
        }
        if (individualFecha) {
            individualFecha.value = today;
            individualFecha.setAttribute('readonly', 'readonly');
        }
    }

    /**
     * Open group tutoring modal
     */
    function openModalTutoriaGrupal(grupoId, grupoNombre) {
        // Set group info
        const grupoIdInput = document.getElementById('grupal-grupo-id');
        const grupoNombreSpan = document.getElementById('grupal-grupo-nombre');
        const modalLabel = document.getElementById('modalTutoriaGrupalLabel');
        const fechaInput = document.getElementById('grupal-fecha');
        const submitBtn = document.querySelector('#formTutoriaGrupal button[type="submit"]');
        
        if (grupoIdInput) grupoIdInput.value = grupoId;
        if (grupoNombreSpan) grupoNombreSpan.textContent = grupoNombre;
        if (modalLabel) modalLabel.innerHTML = `<i class="bi bi-people-fill me-2"></i>Lista Grupal - ${grupoNombre}`;
        
        // Set today's date and make it readonly
        const today = new Date().toISOString().split('T')[0];
        if (fechaInput) {
            fechaInput.value = today;
            fechaInput.setAttribute('readonly', 'readonly');
        }
        if (submitBtn) submitBtn.innerHTML = '<i class="bi bi-save me-1"></i>Guardar Tutoría Grupal';

        // Clear any existing tutoria ID
        const tutoriaIdInput = document.getElementById('grupal-tutoria-id');
        if (tutoriaIdInput) {
            tutoriaIdInput.remove();
        }

        // Load students for attendance
        loadAlumnosForGrupal(grupoId);

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('modalTutoriaGrupal'));
        modal.show();
    }

    /**
     * Open group tutoring modal for today (check if exists and edit if so)
     */
    function openModalTutoriaGrupalToday(grupoId, grupoNombre) {
        const today = new Date().toISOString().split('T')[0];
        
        // Check if a tutoring session exists for today
        fetch(`/GESTACAD/controllers/tutoriasController.php?action=getGrupalByGrupoAndDate&grupo_id=${grupoId}&fecha=${today}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    // Existe, abrir en modo edición
                    openModalTutoriaGrupalEdit(data.data, grupoId, grupoNombre);
                } else {
                    // No existe, abrir en modo creación
                    openModalTutoriaGrupal(grupoId, grupoNombre);
                }
            })
            .catch(error => {
                console.error('Error checking for existing tutoring:', error);
                // En caso de error, abrir en modo creación
                openModalTutoriaGrupal(grupoId, grupoNombre);
            });
    }

    /**
     * Open group tutoring modal in edit mode
     */
    function openModalTutoriaGrupalEdit(tutoria, grupoId, grupoNombre) {
        // Set group info
        const grupoIdInput = document.getElementById('grupal-grupo-id');
        const grupoNombreSpan = document.getElementById('grupal-grupo-nombre');
        const modalLabel = document.getElementById('modalTutoriaGrupalLabel');
        const fechaInput = document.getElementById('grupal-fecha');
        const actividadNombreInput = document.getElementById('grupal-actividad-nombre');
        const actividadDescripcionInput = document.getElementById('grupal-actividad-descripcion');
        const submitBtn = document.querySelector('#formTutoriaGrupal button[type="submit"]');
        
        if (grupoIdInput) grupoIdInput.value = grupoId;
        if (grupoNombreSpan) grupoNombreSpan.textContent = grupoNombre;
        if (modalLabel) modalLabel.innerHTML = `<i class="bi bi-pencil-square me-2"></i>Editar Tutoría Grupal - ${grupoNombre}`;
        if (fechaInput) {
            fechaInput.value = tutoria.fecha;
            fechaInput.setAttribute('readonly', 'readonly');
        }
        if (actividadNombreInput) actividadNombreInput.value = tutoria.actividad_nombre || '';
        if (actividadDescripcionInput) actividadDescripcionInput.value = tutoria.actividad_descripcion || '';
        if (submitBtn) submitBtn.innerHTML = '<i class="bi bi-save me-1"></i>Actualizar Tutoría Grupal';
        
        // Add hidden input for tutoria ID
        const form = document.getElementById('formTutoriaGrupal');
        if (form) {
            let tutoriaIdInput = document.getElementById('grupal-tutoria-id');
            if (!tutoriaIdInput) {
                tutoriaIdInput = document.createElement('input');
                tutoriaIdInput.type = 'hidden';
                tutoriaIdInput.id = 'grupal-tutoria-id';
                tutoriaIdInput.name = 'tutoria_id';
                form.appendChild(tutoriaIdInput);
            }
            tutoriaIdInput.value = tutoria.id;
        }
        
        // Load students and mark present ones
        loadAlumnosForGrupalEdit(grupoId, tutoria.asistencia || []);
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('modalTutoriaGrupal'));
        modal.show();
    }

    /**
     * Initialize motivo and acciones selects for individual tutoring
     */
    function initIndividualTutoriaSelects() {
        // Motivo select handler
        const motivoSelect = document.getElementById('individual-motivo-select');
        const motivoOtroContainer = document.getElementById('individual-motivo-otro-container');
        const motivoOtro = document.getElementById('individual-motivo-otro');
        const motivoHidden = document.getElementById('individual-motivo');

        if (motivoSelect) {
            motivoSelect.addEventListener('change', function() {
                if (this.value === 'otro') {
                    motivoOtroContainer.style.display = 'block';
                    motivoOtro.required = true;
                    motivoOtro.value = '';
                    motivoHidden.value = '';
                } else if (this.value) {
                    motivoOtroContainer.style.display = 'none';
                    motivoOtro.required = false;
                    motivoOtro.value = '';
                    motivoHidden.value = this.value;
                } else {
                    motivoOtroContainer.style.display = 'none';
                    motivoOtro.required = false;
                    motivoOtro.value = '';
                    motivoHidden.value = '';
                }
            });
        }

        // Motivo otro textarea handler
        if (motivoOtro) {
            motivoOtro.addEventListener('input', function() {
                motivoHidden.value = this.value;
            });
        }

        // Acciones select handler
        const accionesSelect = document.getElementById('individual-acciones-select');
        const accionesOtroContainer = document.getElementById('individual-acciones-otro-container');
        const accionesOtro = document.getElementById('individual-acciones-otro');
        const accionesHidden = document.getElementById('individual-acciones');

        if (accionesSelect) {
            accionesSelect.addEventListener('change', function() {
                if (this.value === 'otro') {
                    accionesOtroContainer.style.display = 'block';
                    accionesOtro.required = true;
                    accionesOtro.value = '';
                    accionesHidden.value = '';
                } else if (this.value) {
                    accionesOtroContainer.style.display = 'none';
                    accionesOtro.required = false;
                    accionesOtro.value = '';
                    accionesHidden.value = this.value;
                } else {
                    accionesOtroContainer.style.display = 'none';
                    accionesOtro.required = false;
                    accionesOtro.value = '';
                    accionesHidden.value = '';
                }
            });
        }

        // Acciones otro textarea handler
        if (accionesOtro) {
            accionesOtro.addEventListener('input', function() {
                accionesHidden.value = this.value;
            });
        }
    }

    /**
     * Reset individual tutoring form selects
     */
    function resetIndividualTutoriaSelects() {
        const motivoSelect = document.getElementById('individual-motivo-select');
        const motivoOtroContainer = document.getElementById('individual-motivo-otro-container');
        const motivoOtro = document.getElementById('individual-motivo-otro');
        const motivoHidden = document.getElementById('individual-motivo');
        
        const accionesSelect = document.getElementById('individual-acciones-select');
        const accionesOtroContainer = document.getElementById('individual-acciones-otro-container');
        const accionesOtro = document.getElementById('individual-acciones-otro');
        const accionesHidden = document.getElementById('individual-acciones');

        if (motivoSelect) {
            motivoSelect.value = '';
            motivoSelect.dispatchEvent(new Event('change'));
        }
        if (accionesSelect) {
            accionesSelect.value = '';
            accionesSelect.dispatchEvent(new Event('change'));
        }
    }

    /**
     * Open individual tutoring modal
     */
    function openModalTutoriaIndividual(grupoId, grupoNombre) {
        // Set group info
        const grupoIdInput = document.getElementById('individual-grupo-id');
        const grupoNombreSpan = document.getElementById('individual-grupo-nombre');
        const fechaInput = document.getElementById('individual-fecha');
        
        if (grupoIdInput) grupoIdInput.value = grupoId;
        if (grupoNombreSpan) grupoNombreSpan.textContent = grupoNombre;
        
        // Set today's date and make it readonly
        const today = new Date().toISOString().split('T')[0];
        if (fechaInput) {
            fechaInput.value = today;
            fechaInput.setAttribute('readonly', 'readonly');
        }

        // Reset motivo and acciones selects
        resetIndividualTutoriaSelects();

        // Load students for selection
        loadAlumnosForIndividual(grupoId);

        // Show modal
        const modalElement = document.getElementById('modalTutoriaIndividual');
        const modal = new bootstrap.Modal(modalElement);
        
        // Clean up Select2 when modal is hidden
        modalElement.addEventListener('hidden.bs.modal', function() {
            const select = document.getElementById('individual-alumno-id');
            if (select && typeof $ !== 'undefined' && $(select).hasClass('select2-hidden-accessible')) {
                $(select).select2('destroy');
            }
            // Reset selects when modal is closed
            resetIndividualTutoriaSelects();
        }, { once: true });
        
        modal.show();
    }

    /**
     * Load students for group tutoring attendance
     */
    function loadAlumnosForGrupal(grupoId) {
        const container = document.getElementById('grupal-lista-alumnos');
        if (!container) return;
        
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
     * Load students for group tutoring attendance in edit mode
     */
    function loadAlumnosForGrupalEdit(grupoId, asistencia) {
        const container = document.getElementById('grupal-lista-alumnos');
        if (!container) return;
        
        container.innerHTML = '<div class="text-center text-muted"><div class="spinner-border spinner-border-sm me-2" role="status"><span class="visually-hidden">Cargando...</span></div>Cargando alumnos...</div>';

        // Create a map of present students
        const presentesMap = {};
        if (asistencia && asistencia.length > 0) {
            asistencia.forEach(function(a) {
                if (a.presente == 1 || a.presente === '1') {
                    presentesMap[a.alumno_id] = true;
                }
            });
        }

        fetch(`controllers/tutoriasController.php?action=getAlumnosByGrupo&grupo_id=${grupoId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    let html = '<div class="list-group list-group-flush">';
                    data.data.forEach(alumno => {
                        const checked = presentesMap[alumno.id_alumno] ? 'checked' : '';
                        html += `
                            <div class="list-group-item">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="asistencia[${alumno.id_alumno}]" value="1" id="asist_${alumno.id_alumno}" ${checked}>
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
        
        // Destroy existing Select2 instance if it exists
        if (typeof $ !== 'undefined' && $(select).hasClass('select2-hidden-accessible')) {
            $(select).select2('destroy');
        }
        
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
                    
                    // Initialize Select2 after a small delay to ensure modal is fully rendered
                    setTimeout(() => {
                        if (typeof $ !== 'undefined') {
                            $(select).select2({
                                theme: 'bootstrap-5',
                                placeholder: 'Seleccione un alumno...',
                                allowClear: true,
                                dropdownParent: $('#modalTutoriaIndividual'),
                                language: {
                                    noResults: function() {
                                        return "No se encontraron resultados";
                                    },
                                    searching: function() {
                                        return "Buscando...";
                                    }
                                }
                            });
                        }
                    }, 100);
                } else {
                    select.innerHTML = '<option value="">No hay alumnos disponibles</option>';
                    // Initialize Select2 even if no options
                    setTimeout(() => {
                        if (typeof $ !== 'undefined') {
                            $(select).select2({
                                theme: 'bootstrap-5',
                                placeholder: 'No hay alumnos disponibles',
                                allowClear: true,
                                dropdownParent: $('#modalTutoriaIndividual')
                            });
                        }
                    }, 100);
                }
            })
            .catch(error => {
                console.error('Error loading students:', error);
                select.innerHTML = '<option value="">Error al cargar alumnos</option>';
                // Initialize Select2 even on error
                setTimeout(() => {
                    if (typeof $ !== 'undefined') {
                        $(select).select2({
                            theme: 'bootstrap-5',
                            placeholder: 'Error al cargar alumnos',
                            allowClear: true,
                            dropdownParent: $('#modalTutoriaIndividual')
                        });
                    }
                }, 100);
            });
    }

    /**
     * Submit group tutoring form
     */
    function submitTutoriaGrupal(form) {
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const tutoriaIdInput = document.getElementById('grupal-tutoria-id');
        const tutoriaId = tutoriaIdInput ? tutoriaIdInput.value : null;

        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        // Determine if it's an update or create
        const action = tutoriaId ? 'updateGrupal' : 'createGrupal';
        if (tutoriaId) {
            formData.append('id', tutoriaId);
        }

        fetch(`controllers/tutoriasController.php?action=${action}`, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    const message = tutoriaId ? 'Tutoría grupal actualizada exitosamente' : 'Tutoría grupal creada exitosamente';
                    showAlert('success', data.message || message);

                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalTutoriaGrupal'));
                    modal.hide();

                    // Reset form
                    form.reset();
                    setDefaultDates();
                    
                    // Reload page to refresh the list
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showAlert('danger', data.message || 'Error al guardar la tutoría grupal');
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
        // Validate motivo and acciones before submitting
        const motivoHidden = document.getElementById('individual-motivo');
        const accionesHidden = document.getElementById('individual-acciones');
        const motivoSelect = document.getElementById('individual-motivo-select');
        const accionesSelect = document.getElementById('individual-acciones-select');
        const motivoOtro = document.getElementById('individual-motivo-otro');
        const accionesOtro = document.getElementById('individual-acciones-otro');

        // Update hidden fields with current values
        if (motivoSelect && motivoSelect.value === 'otro' && motivoOtro) {
            motivoHidden.value = motivoOtro.value.trim();
        } else if (motivoSelect && motivoSelect.value) {
            motivoHidden.value = motivoSelect.value;
        }

        if (accionesSelect && accionesSelect.value === 'otro' && accionesOtro) {
            accionesHidden.value = accionesOtro.value.trim();
        } else if (accionesSelect && accionesSelect.value) {
            accionesHidden.value = accionesSelect.value;
        }

        // Validate required fields
        if (!motivoHidden.value || motivoHidden.value.trim() === '') {
            showAlert('danger', 'Por favor seleccione o especifique un motivo');
            if (motivoSelect && motivoSelect.value === 'otro' && motivoOtro) {
                motivoOtro.focus();
            } else if (motivoSelect) {
                motivoSelect.focus();
            }
            return;
        }

        if (!accionesHidden.value || accionesHidden.value.trim() === '') {
            showAlert('danger', 'Por favor seleccione o especifique las acciones');
            if (accionesSelect && accionesSelect.value === 'otro' && accionesOtro) {
                accionesOtro.focus();
            } else if (accionesSelect) {
                accionesSelect.focus();
            }
            return;
        }

        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');

        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        fetch('/GESTACAD/controllers/tutoriasController.php?action=createIndividual', {
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
                    
                    // Destroy Select2 instance before reset
                    const select = document.getElementById('individual-alumno-id');
                    if (typeof $ !== 'undefined' && $(select).hasClass('select2-hidden-accessible')) {
                        $(select).select2('destroy');
                    }

                    // Reset form
                    form.reset();
                    setDefaultDates();
                    resetIndividualTutoriaSelects();
                    
                    // Hide modal after cleanup
                    modal.hide();
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
