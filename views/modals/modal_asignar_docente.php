    <!-- Modal: Asignar Docente a Curso -->
    <div class="modal fade" id="modalAsignarDocente" tabindex="-1" aria-labelledby="modalAsignarDocenteLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalAsignarDocenteLabel"><i class="fas fa-chalkboard-teacher me-2"></i>Asignar Docente a Curso</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form id="formAsignarModal" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="modalArea" class="form-label">Área</label>
                            <select id="modalArea" name="area_id" class="form-select" required>
                                <option value="">-- Seleccione un área --</option>
                                <?php
                                $areas = $conn->query("SELECT id, nombre_area FROM areas WHERE estado = 1 ORDER BY nombre_area ASC");
                                while ($area = $areas->fetch_assoc()):
                                ?>
                                    <option value="<?= $area['id'] ?>"><?= htmlspecialchars($area['nombre_area']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="modalCurso" class="form-label">Curso</label>
                            <select id="modalCurso" name="curso_id" class="form-select" required disabled>
                                <option value="">-- Seleccione un área primero --</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="modalHorario" class="form-label">Horario</label>
                            <select id="modalHorario" name="horario_id" class="form-select" required disabled>
                                <option value="">-- Seleccione un curso primero --</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="modalProfesor" class="form-label">Profesor</label>
                            <select id="modalProfesor" name="profesor_id" class="form-select" required>
                                <option value="">-- Seleccione un profesor --</option>
                                <?php
                                $profesores = $conn->query("SELECT id, nombre, apellidos FROM profesores");
                                while ($p = $profesores->fetch_assoc()):
                                ?>
                                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre'] . ' ' . $p['apellidos']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success"><i class="fas fa-plus-circle me-1"></i>Asignar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>