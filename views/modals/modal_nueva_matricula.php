<!-- Modal: Nueva Matrícula -->
<div class="modal fade" id="modalNuevaMatricula" tabindex="-1" aria-labelledby="modalNuevaMatriculaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalNuevaMatriculaLabel">
                    <i class="fas fa-user-plus me-2"></i>Nueva Matrícula
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form id="formMatriculaModal">
                <div class="modal-body">

                    <!-- Bloque: Buscador + Select de alumno en la misma fila -->
                    <div class="mb-3">
                        <label for="alumno" class="form-label">Seleccionar Alumno</label>

                        <div class="row">
                            <!-- Columna: Buscador -->
                            <div class="col-md-6 mb-2 mb-md-0">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" id="buscadorAlumno" class="form-control" placeholder="Buscar o escribir alumno...">
                                </div>
                            </div>

                            <!-- Columna: Select -->
                            <div class="col-md-6">
                                <select id="alumno" name="alumno_id" class="form-select" required>
                                    <option value="">-- Seleccione un alumno --</option>
                                    <?php
                                    $alumnos = $conn->query("SELECT u.id, a.nombre, a.apellidos FROM usuarios u
                                        JOIN alumnos a ON u.id = a.usuario_id WHERE u.rol = 'alumno'");
                                    while ($a = $alumnos->fetch_assoc()): ?>
                                        <option value="<?= $a['id'] ?>">
                                            <?= htmlspecialchars($a['nombre'] . ' ' . $a['apellidos']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                    </div>


                    <!-- Área -->
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

                    <!-- Curso -->
                    <div class="mb-3">
                        <label for="curso" class="form-label">Seleccionar Curso</label>
                        <select id="curso" name="curso_id" class="form-select" required disabled>
                            <option value="">-- Seleccione un área primero --</option>
                        </select>
                    </div>

                    <!-- Horario -->
                    <div class="mb-3">
                        <label for="horario" class="form-label">Seleccionar Horario</label>
                        <select id="horario" name="horario_id" class="form-select" required disabled>
                            <option value="">-- Seleccione un curso primero --</option>
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-user-check me-1"></i>Matricular
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script: buscador dinámico alumno -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const buscador = document.getElementById('buscadorAlumno');
        const select = document.getElementById('alumno');

        buscador.addEventListener('input', () => {
            const texto = buscador.value.trim().toLowerCase();
            let coincidencias = 0;

            // Quitar opción temporal si existe
            let tempOption = select.querySelector('option[data-temp="true"]');
            if (tempOption) tempOption.remove();

            // Filtrar opciones
            Array.from(select.options).forEach(opt => {
                if (opt.value === "") return;
                const visible = opt.text.toLowerCase().includes(texto);
                opt.style.display = visible ? "" : "none";
                if (visible) coincidencias++;
            });

            if (texto === "") {
                select.selectedIndex = 0;
                return;
            }

            if (coincidencias === 0) {
                tempOption = document.createElement('option');
                tempOption.setAttribute('data-temp', 'true');
                tempOption.value = "";
                tempOption.disabled = true;
                tempOption.text = `No se encuentra: "${buscador.value}"`;
                select.appendChild(tempOption);
                tempOption.selected = true;
            } else {
                const firstVisible = Array.from(select.options).find(opt => opt.style.display !== 'none' && opt.value !== "");
                if (firstVisible) {
                    firstVisible.selected = true;
                }
            }
        });

        // Reset al cerrar modal
        const modal = document.getElementById('modalNuevaMatricula');
        modal.addEventListener('hidden.bs.modal', () => {
            buscador.value = "";
            select.selectedIndex = 0;
            Array.from(select.options).forEach(opt => {
                opt.style.display = "";
            });
        });
    });
</script>