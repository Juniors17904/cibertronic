    <!-- Modal: Nueva Matrícula -->
    <div class="modal fade" id="modalNuevaMatricula" tabindex="-1" aria-labelledby="modalNuevaMatriculaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalNuevaMatriculaLabel"><i class="fas fa-user-plus me-2"></i>Nueva Matrícula</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form id="formMatriculaModal">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="alumno" class="form-label">Seleccionar Alumno</label>
                            <select id="alumno" name="alumno_id" class="form-select" required>
                                <option value="">-- Seleccione un alumno --</option>
                                <?php
                                $alumnos = $conn->query("SELECT u.id, a.nombre, a.apellidos FROM usuarios u
                                                    JOIN alumnos a ON u.id = a.usuario_id WHERE u.rol = 'alumno'");
                                while ($a = $alumnos->fetch_assoc()): ?>
                                    <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre'] . ' ' . $a['apellidos']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="area" class="form-label">Seleccionar Área</label>
                            <select id="area" class="form-select" required>
                                <option value="">-- Seleccione un área --</option>
                                <?php
                                $areas = $conn->query("SELECT id, nombre_area FROM areas WHERE estado = 1");
                                while ($ar = $areas->fetch_assoc()): ?>
                                    <option value="<?= $ar['id'] ?>"><?= htmlspecialchars($ar['nombre_area']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="curso" class="form-label">Seleccionar Curso</label>
                            <select id="curso" name="curso_id" class="form-select" required disabled>
                                <option value="">-- Seleccione un área primero --</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="horario" class="form-label">Seleccionar Horario</label>
                            <select id="horario" name="horario_id" class="form-select" required disabled>
                                <option value="">-- Seleccione un curso primero --</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Matricular</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>