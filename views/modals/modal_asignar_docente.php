<!-- Modal: Asignar Docente a Curso -->
<div class="modal fade" id="modalAsignarDocente" tabindex="-1" aria-labelledby="modalAsignarDocenteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalAsignarDocenteLabel">
                    <i class="fas fa-chalkboard-teacher me-2"></i>Asignar Docente a Curso
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form id="formAsignarModal" method="POST">
                <div class="modal-body">
                    <!-- Área -->
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

                    <!-- Curso -->
                    <div class="mb-3">
                        <label for="modalCurso" class="form-label">Curso</label>
                        <select id="modalCurso" name="curso_id" class="form-select" required disabled>
                            <option value="">-- Seleccione un área primero --</option>
                        </select>
                    </div>

                    <!-- Horario -->
                    <div class="mb-3">
                        <label for="modalHorario" class="form-label">Horario</label>
                        <select id="modalHorario" name="horario_id" class="form-select" required disabled>
                            <option value="">-- Seleccione un curso primero --</option>
                        </select>
                    </div>




                    <!-- Profesor con buscador y select en la misma fila -->
                    <div class="mb-3">
                        <label for="modalProfesor" class="form-label">Profesor</label>

                        <div class="row">
                            <!-- Buscador -->
                            <div class="col-md-6 mb-2 mb-md-0">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" id="buscadorProfesor" class="form-control" placeholder="Buscar o escribir profesor...">
                                </div>
                            </div>

                            <!-- Select -->
                            <div class="col-md-6">
                                <select id="modalProfesor" name="profesor_id" class="form-select" required>
                                    <option value="">-- Seleccione un profesor --</option>
                                    <?php
                                    $profesores = $conn->query("SELECT id, nombre, apellidos FROM profesores ORDER BY nombre ASC");
                                    while ($p = $profesores->fetch_assoc()):
                                    ?>
                                        <option value="<?= $p['id'] ?>">
                                            <?= htmlspecialchars($p['nombre'] . ' ' . $p['apellidos']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                    </div>





                </div>

                <!-- Botones -->
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-plus-circle me-1"></i>Asignar
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script: lógica para que el input cree una opción dinámica -->

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const buscador = document.getElementById('buscadorProfesor');
        const select = document.getElementById('modalProfesor');

        buscador.addEventListener('input', () => {
            const texto = buscador.value.trim().toLowerCase();
            let coincidencias = 0;

            // Quitar opción especial si ya existe
            let tempOption = select.querySelector('option[data-temp="true"]');
            if (tempOption) tempOption.remove();

            // Filtrar opciones existentes
            Array.from(select.options).forEach(opt => {
                if (opt.value === "") return; // Ignorar placeholder
                const visible = opt.text.toLowerCase().includes(texto);
                opt.style.display = visible ? "" : "none";
                if (visible) coincidencias++;
            });

            if (texto === "") {
                select.selectedIndex = 0; // Reset
                return;
            }

            if (coincidencias === 0) {
                // Crear opción "No se encuentra"
                tempOption = document.createElement('option');
                tempOption.setAttribute('data-temp', 'true');
                tempOption.value = "";
                tempOption.disabled = true;
                tempOption.text = `No se encuentra: "${buscador.value}"`;
                select.appendChild(tempOption);
                tempOption.selected = true;
            } else {
                // Seleccionar la primera opción visible
                const firstVisible = Array.from(select.options).find(opt => opt.style.display !== 'none' && opt.value !== "");
                if (firstVisible) {
                    firstVisible.selected = true;
                }
            }
        });
    });
</script>