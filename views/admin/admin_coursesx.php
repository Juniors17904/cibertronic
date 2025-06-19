<?php
require_once '../../controllers/auth_check.php';
require_once '../../controllers/get_admin_data.php';

$admin = getAdminData($conn, $_SESSION['user_id']);

if (!$admin) {
    die("Administrador no encontrado.");
}

include '../header.php';
?>

<body>
    <?php include 'cabecera.php'; ?>

    <div class="container-fluid mt-0">
        <div class="row">
            <?php include 'lateral.php'; ?>

            <!-- Contenido principal -->
            <main class="col-md-7 col-lg-8 px-5">

                <h2 class="text-center mt-4 mb-4 text-primary">Nueva Matrícula</h2>

                <form>
                    <!-- Selección de alumno -->
                    <div class="mb-3">
                        <label for="alumno" class="form-label">Seleccionar Alumno</label>
                        <select id="alumno" class="form-select border-primary" required>
                            <option value="">-- Seleccione un alumno --</option>
                            <?php
                            $alumnos = $conn->query("
                                SELECT u.id, a.nombre, a.apellidos 
                                FROM usuarios u
                                JOIN alumnos a ON u.id = a.usuario_id
                                WHERE u.rol = 'alumno'
                            ");
                            while ($alumno = $alumnos->fetch_assoc()):
                            ?>
                                <option value="<?= $alumno['id'] ?>">
                                    <?= htmlspecialchars($alumno['nombre'] . ' ' . $alumno['apellidos']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Selección de área -->
                    <div class="mb-3">
                        <label for="area" class="form-label">Seleccionar Área</label>
                        <select id="area" class="form-select border-primary" required>
                            <option value="">-- Seleccione un área --</option>
                            <?php
                            $areas = $conn->query("SELECT id, nombre_area FROM areas WHERE estado = 1");
                            while ($area = $areas->fetch_assoc()):
                            ?>
                                <option value="<?= $area['id'] ?>"><?= htmlspecialchars($area['nombre_area']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Selección de curso -->
                    <div class="mb-3">
                        <label for="curso" class="form-label">Seleccionar Curso</label>
                        <select id="curso" class="form-select border-primary" required disabled>
                            <option value="">-- Seleccione un área primero --</option>
                        </select>
                    </div>

                    <!-- Selección de horario -->
                    <div class="mb-3">
                        <label for="horario" class="form-label">Seleccionar Horario</label>
                        <select id="horario" class="form-select border-primary" required disabled>
                            <option value="">-- Seleccione un curso primero --</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success" id="btnMatricular" disabled>Matricular</button>
                </form>

                <h2 class="text-center mt-5 mb-4 text-success">📋 Matrículas Registradas</h2>

                <div class="table-responsive rounded shadow border">
                    <table class="table table-bordered table-hover shadow-sm">
                        <thead class="table-primary text-center">
                            <tr>
                                <th>#</th>
                                <th>Alumno</th>
                                <th>Área</th>
                                <th>Curso</th>
                                <th>Horario</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php
                            $sql = "
                                SELECT 
                                    m.id,
                                    CONCAT(a.nombre, ' ', a.apellidos) AS alumno,
                                    ar.nombre_area AS area,
                                    c.nombre_curso AS curso,
                                    CONCAT(h.dia, ' ', h.hora_inicio, ' - ', h.hora_fin) AS horario
                                FROM matriculas m
                                INNER JOIN alumnos a ON m.alumno_id = a.id
                                INNER JOIN cursos c ON m.curso_id = c.id
                                INNER JOIN areas ar ON c.id_area = ar.id
                                INNER JOIN horarios h ON m.horario_id = h.id
                                ORDER BY alumno ASC
                            ";

                            $matriculas = $conn->query($sql);
                            if (!$matriculas) {
                                echo "<tr><td colspan='5' class='text-danger'>❌ Error en la consulta: " . htmlspecialchars($conn->error) . "</td></tr>";
                            } elseif ($matriculas->num_rows > 0) {
                                $n = 1;
                                while ($row = $matriculas->fetch_assoc()):
                            ?>
                                    <tr>
                                        <td><?= $n++ ?></td>
                                        <td><?= htmlspecialchars($row['alumno']) ?></td>
                                        <td><span class="badge bg-info text-dark"><?= htmlspecialchars($row['area']) ?></span></td>
                                        <td><span class="badge bg-secondary"><?= htmlspecialchars($row['curso']) ?></span></td>
                                        <td><span class="text-muted"><?= htmlspecialchars($row['horario']) ?></span></td>
                                    </tr>
                            <?php
                                endwhile;
                            } else {
                                echo "<tr><td colspan='5'>No hay matrículas registradas.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <?php
    include '../modals/toast_notificacion.php';
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.getElementById('area').addEventListener('change', function() {
            const areaId = this.value;
            const cursoSelect = document.getElementById('curso');
            const horarioSelect = document.getElementById('horario');

            cursoSelect.innerHTML = '<option value="">Cargando cursos...</option>';
            cursoSelect.disabled = true;
            horarioSelect.innerHTML = '<option value="">-- Seleccione un curso primero --</option>';
            horarioSelect.disabled = true;

            if (!areaId) {
                cursoSelect.innerHTML = '<option value="">-- Seleccione un área primero --</option>';
                return;
            }

            fetch(`../../controllers/get_cursos_por_area.php?area_id=${areaId}`)
                .then(res => res.json())
                .then(data => {
                    cursoSelect.innerHTML = '';
                    if (data.length === 0) {
                        cursoSelect.innerHTML = '<option value="">-- No hay cursos disponibles --</option>';
                    } else {
                        cursoSelect.innerHTML = '<option value="">-- Seleccione un curso --</option>';
                        data.forEach(curso => {
                            const option = document.createElement('option');
                            option.value = curso.id;
                            option.textContent = curso.nombre_curso;
                            cursoSelect.appendChild(option);
                        });
                    }
                    cursoSelect.disabled = false;
                });
        });

        document.getElementById('curso').addEventListener('change', function() {
            const cursoId = this.value;
            const horarioSelect = document.getElementById('horario');

            horarioSelect.innerHTML = '<option value="">Cargando horarios...</option>';
            horarioSelect.disabled = true;

            if (!cursoId) {
                horarioSelect.innerHTML = '<option value="">-- Seleccione un curso primero --</option>';
                return;
            }

            fetch(`../../controllers/get_horarios.php?curso_id=${cursoId}`)
                .then(res => res.json())
                .then(data => {
                    horarioSelect.innerHTML = '';
                    if (data.length === 0) {
                        horarioSelect.innerHTML = '<option value="">-- No hay horarios disponibles --</option>';
                    } else {
                        horarioSelect.innerHTML = '<option value="">-- Seleccione un horario --</option>';
                        data.forEach(h => {
                            const option = document.createElement('option');
                            option.value = h.id;
                            option.textContent = `${h.dia} - ${h.hora_inicio} a ${h.hora_fin}`;
                            horarioSelect.appendChild(option);
                        });
                    }
                    horarioSelect.disabled = false;
                });
        });

        document.querySelector('form')?.addEventListener('submit', function(e) {
            e.preventDefault();

            const alumnoId = document.getElementById('alumno').value;
            const cursoId = document.getElementById('curso').value;
            const horarioId = document.getElementById('horario').value;

            if (!alumnoId || !cursoId || !horarioId) {
                alert("Por favor, complete todos los campos.");
                return;
            }

            fetch('../../controllers/insert_matricula.php', {
                    method: 'POST',
                    body: new URLSearchParams({
                        alumno_id: alumnoId,
                        curso_id: cursoId,
                        horario_id: horarioId
                    }),
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Matrícula registrada correctamente.', 'success', 'check-circle');
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        showNotification(data.message, 'danger', 'exclamation-triangle');
                    }
                })

                .catch(err => {
                    console.error("❌ Error:", err);
                    alert("Error de red.");
                });
        });

        const alumno = document.getElementById('alumno');
        const curso = document.getElementById('curso');
        const horario = document.getElementById('horario');
        const btn = document.getElementById('btnMatricular');

        function validarCampos() {
            btn.disabled = !(alumno.value && curso.value && horario.value);
        }

        function showNotification(message, type, icon) {
            var toastEl = document.getElementById('liveToast');
            var toastHeader = toastEl.querySelector('.toast-header');
            var toastBody = toastEl.querySelector('.toast-body');

            // Limpiar clases anteriores
            toastHeader.className = 'toast-header';
            toastBody.className = 'toast-body';

            // Añadir clases de color según el tipo
            switch (type) {
                case 'success':
                    toastHeader.classList.add('bg-success', 'text-white');
                    break;
                case 'danger':
                    toastHeader.classList.add('bg-danger', 'text-white');
                    break;
                case 'warning':
                    toastHeader.classList.add('bg-warning', 'text-dark');
                    break;
                default:
                    toastHeader.classList.add('bg-primary', 'text-white');
            }

            toastBody.innerHTML = `<i class="fas fa-${icon} me-2"></i> ${message}`;
            var toast = new bootstrap.Toast(toastEl);
            toast.show();

            // Ocultar automáticamente después de 5 segundos
            setTimeout(() => toast.hide(), 5000);
        }


        alumno.addEventListener('change', validarCampos);
        curso.addEventListener('change', validarCampos);
        horario.addEventListener('change', validarCampos);

        //-------------------------
        if (data.success) {
            const toastElement = document.getElementById('liveToast');
            const toastBody = toastElement.querySelector('.toast-body');

            toastBody.textContent = "✅ Matrícula registrada correctamente.";
            const toast = new bootstrap.Toast(toastElement);
            toast.show();

            setTimeout(() => location.reload(), 2000);
        }
    </script>
</body>

</html>