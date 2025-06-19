<div class="modal fade" id="modalAsistencia" tabindex="-1" aria-labelledby="modalAsistenciaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border border-warning border-2">
            <form id="formAsistencia" method="POST">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="modalAsistenciaLabel">
                        <i class="fas fa-clipboard-check me-2"></i> Registrar Asistencia
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <p><strong>Curso:</strong> <span id="modalCursoNombre"></span></p>
                    <p><strong>Horario:</strong> <span id="modalHorario"></span></p>

                    <div class="mb-3">
                        <label for="fechaAsistencia" class="form-label"><strong>Fecha de asistencia:</strong></label>
                        <?php date_default_timezone_set('America/Lima'); ?>
                        <input type="date" id="fechaAsistencia" name="fecha" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <!-- Campos ocultos -->
                    <input type="hidden" name="curso_id" id="curso_id">
                    <input type="hidden" name="horario_id" id="horario_id">

                    <hr class="mb-4">




                    <!-- Tabla de asistencia con scroll interno -->
                    <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="table-warning text-center sticky-top">
                                <tr>
                                    <th>Alumno</th>
                                    <th>Presente</th>
                                    <th>Ausente</th>
                                    <th>Justificado</th>
                                </tr>
                            </thead>
                            <tbody id="tablaAlumnos">
                                <!-- Aquí se insertarán las filas de alumnos desde JavaScript -->
                            </tbody>
                        </table>
                    </div>







                    <div class="modal-footer bg-light">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i> Guardar Asistencia
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                    </div>
            </form>
        </div>
    </div>
</div>