<!-- Modal for Group Tutoring -->
<div class="modal fade" id="modalTutoriaGrupal" tabindex="-1" aria-labelledby="modalTutoriaGrupalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-md-down modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTutoriaGrupalLabel">
                    <i class="bi bi-people-fill me-2"></i>Lista Grupal - <span id="grupal-grupo-nombre"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTutoriaGrupal" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="grupal-grupo-id" name="grupo_id">
                    <input type="hidden" id="grupal-parcial-id" name="parcial_id" value="1">

                    <div class="mb-3">
                        <label for="grupal-fecha" class="form-label">Fecha</label>
                        <input type="date" class="form-control" id="grupal-fecha" name="fecha" required readonly>
                    </div>

                    <div class="mb-3">
                        <label for="grupal-actividad-nombre" class="form-label">Nombre de la Actividad</label>
                        <input type="text" class="form-control" id="grupal-actividad-nombre" name="actividad_nombre"
                            required maxlength="200">
                    </div>

                    <div class="mb-3">
                        <label for="grupal-actividad-descripcion" class="form-label">Descripción de la Actividad</label>
                        <textarea class="form-control" id="grupal-actividad-descripcion" name="actividad_descripcion"
                            rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="grupal-evidencia-foto" class="form-label">Foto de Evidencia</label>
                        <input type="file" class="form-control" id="grupal-evidencia-foto" name="evidencia_foto"
                            accept="image/*">
                        <small class="text-muted">Formatos permitidos: JPG, PNG, GIF, WEBP. Tamaño máximo: 5MB</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Asistencia de Alumnos</label>
                        <div id="grupal-lista-alumnos" class="border rounded p-3"
                            style="max-height: 300px; overflow-y: auto;">
                            <div class="text-center text-muted">
                                <div class="spinner-border spinner-border-sm me-2" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                Cargando alumnos...
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer flex-column flex-sm-row gap-2">
                    <button type="button" class="btn btn-secondary w-100 w-sm-auto" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success w-100 w-sm-auto">
                        <i class="bi bi-save me-1"></i>Guardar Tutoría Grupal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal for Individual Tutoring -->
<div class="modal fade" id="modalTutoriaIndividual" tabindex="-1" aria-labelledby="modalTutoriaIndividualLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-md-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTutoriaIndividualLabel">
                    <i class="bi bi-person-fill me-2"></i>Tutoría Individual - <span
                        id="individual-grupo-nombre"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTutoriaIndividual">
                <div class="modal-body">
                    <input type="hidden" id="individual-grupo-id" name="grupo_id">
                    <input type="hidden" id="individual-parcial-id" name="parcial_id" value="1">

                    <div class="mb-3">
                        <label for="individual-fecha" class="form-label">Fecha</label>
                        <input type="date" class="form-control" id="individual-fecha" name="fecha" required readonly>
                    </div>

                    <div class="mb-3">
                        <label for="individual-alumno-id" class="form-label">Alumno</label>
                        <select class="form-select" id="individual-alumno-id" name="alumno_id" required>
                            <option value="">Seleccione un alumno...</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="individual-motivo-select" class="form-label">Motivo de la Tutoría</label>
                        <select class="form-select" id="individual-motivo-select" required>
                            <option value="">Seleccione un motivo...</option>
                            <option value="Bajo rendimiento académico">Bajo rendimiento académico</option>
                            <option value="Problemas de conducta">Problemas de conducta</option>
                            <option value="Falta de asistencia">Falta de asistencia</option>
                            <option value="otro">Otro (especificar)</option>
                        </select>
                        <div id="individual-motivo-otro-container" class="mt-2" style="display: none;">
                            <textarea class="form-control" id="individual-motivo-otro" rows="3"
                                placeholder="Especifique el motivo..."></textarea>
                        </div>
                        <input type="hidden" id="individual-motivo" name="motivo" required>
                    </div>

                    <div class="mb-3">
                        <label for="individual-acciones-select" class="form-label">Acciones a Implementar</label>
                        <select class="form-select" id="individual-acciones-select" required>
                            <option value="">Seleccione una acción...</option>
                            <option value="Seguimiento académico personalizado">Seguimiento académico personalizado</option>
                            <option value="Canalización a servicios de apoyo">Canalización a servicios de apoyo</option>
                            <option value="Reunión con padres o tutores">Reunión con padres o tutores</option>
                            <option value="otro">Otro (especificar)</option>
                        </select>
                        <div id="individual-acciones-otro-container" class="mt-2" style="display: none;">
                            <textarea class="form-control" id="individual-acciones-otro" rows="3"
                                placeholder="Especifique las acciones..."></textarea>
                        </div>
                        <input type="hidden" id="individual-acciones" name="acciones" required>
                    </div>
                </div>
                <div class="modal-footer flex-column flex-sm-row gap-2">
                    <button type="button" class="btn btn-secondary w-100 w-sm-auto" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info w-100 w-sm-auto">
                        <i class="bi bi-save me-1"></i>Guardar Tutoría Individual
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>