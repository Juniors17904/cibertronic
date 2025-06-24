<!-- Modal: Editar Asignación -->
<div class="modal fade" id="modalEditarAsignacion" tabindex="-1" aria-labelledby="modalEditarAsignacionLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="modalEditarAsignacionLabel"><i class="fas fa-edit me-2"></i>Editar Asignación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form id="formEditarAsignacion" method="POST">
                <div class="modal-body">
                    <input type="hidden" id="editarAsignacionId" name="id">

                    <div class="mb-3">
                        <label for="editarArea" class="form-label">Área</label>
                        <select class="form-select" id="editarArea" name="area_id" required>
                            <option value="">Seleccione un área</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="editarCurso" class="form-label">Curso</label>
                        <select class="form-select" id="editarCurso" name="curso_id" required>
                            <option value="">Seleccione un curso</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="editarHorario" class="form-label">Horario</label>
                        <select class="form-select" id="editarHorario" name="horario_id" required>
                            <option value="">Seleccione un horario</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="editarProfesor" class="form-label">Profesor</label>
                        <select class="form-select" id="editarProfesor" name="profesor_id" required>
                            <option value="">Seleccione un profesor</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning"><i class="fas fa-save me-1"></i>Guardar Cambios</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>