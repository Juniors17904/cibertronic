<?php
require_once '../../controllers/auth_check.php';
require_once '../../controllers/get_admin_data.php';
$admin = getAdminData($conn, $_SESSION['user_id']);
if (!$admin) die("Administrador no encontrado.");
include '../header.php';
?>

<body>
    <?php include 'cabecera.php'; ?>
    <div class="container-fluid mt-0">
        <div class="row">
            <?php include 'lateral.php'; ?>
            <!-- <main class="col-md-7 col-lg-8 px-5"> -->
            <main class="col-md-7 col-lg-8 px-md-5 py-5">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Asignaciones Registradas</h4>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAsignarDocente">
                            <i class="fas fa-plus"></i> Nuevo Registro
                        </button>
                    </div>
                    <div class="card-body">
                        <?php
                        $query = "
                        SELECT 
                            a.id, a.codigo_asignacion,
                            a.curso_id, a.horario_id, a.profesor_id,  -- ¡Agrega estas líneas!
                            c.nombre_curso, 
                            h.dia, h.hora_inicio, h.hora_fin, 
                            p.nombre AS nombre_profesor, p.apellidos
                            FROM asignaciones a
                            JOIN cursos c ON a.curso_id = c.id
                            JOIN horarios h ON a.horario_id = h.id
                            JOIN profesores p ON a.profesor_id = p.id
                            ORDER BY c.nombre_curso ASC, h.dia ASC
";

                        $asignaciones = $conn->query($query);
                        ?>

                        <?php if ($asignaciones && $asignaciones->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle text-center">
                                    <thead class="table-light">
                                        <tr>
                                            <th>N°</th>
                                            <th>Código</th>
                                            <th>Curso</th>
                                            <th>Horario</th>
                                            <th>Profesor</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $n = 1;
                                        while ($row = $asignaciones->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $n++ ?></td>
                                                <td><?= htmlspecialchars($row['codigo_asignacion']) ?></td>
                                                <td><?= htmlspecialchars($row['nombre_curso']) ?></td>
                                                <td><?= htmlspecialchars("{$row['dia']} - {$row['hora_inicio']} a {$row['hora_fin']}") ?></td>
                                                <td><?= htmlspecialchars("{$row['nombre_profesor']} {$row['apellidos']}") ?></td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-primary btnEditarAsignacion"
                                                            data-id="<?= htmlspecialchars($row['id']) ?>"
                                                            data-area-id="<?= htmlspecialchars($row['area_id'] ?? '') ?>"
                                                            data-curso-id="<?= htmlspecialchars($row['curso_id']) ?>"
                                                            data-horario-id="<?= htmlspecialchars($row['horario_id']) ?>"
                                                            data-profesor-id="<?= htmlspecialchars($row['profesor_id']) ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>

                                                        <a href="../../controllers/delete_asignacion.php?id=<?= $row['id'] ?>"
                                                            class="btn btn-sm btn-outline-danger"
                                                            onclick="return confirm('¿Estás seguro de eliminar esta asignación?');">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>

                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning text-center mb-0">No hay asignaciones registradas.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php
    include '../modals/toast_notificacion.php';
    include '../modals/modal_asignar_docente.php';
    include '../modals/modal_editar_asignacion.php';
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Lógica dinámica para cargar cursos y horarios en el modal
        document.getElementById('modalArea')?.addEventListener('change', function() {
            const areaId = this.value;
            const cursoSelect = document.getElementById('modalCurso');
            const horarioSelect = document.getElementById('modalHorario');

            cursoSelect.disabled = true;
            horarioSelect.disabled = true;
            cursoSelect.innerHTML = '<option value="">Cargando cursos...</option>';
            horarioSelect.innerHTML = '<option value="">-- Seleccione un curso primero --</option>';

            if (!areaId) return;

            fetch(`../../controllers/get_cursos_por_area.php?area_id=${areaId}`)
                .then(res => res.json())
                .then(data => {
                    cursoSelect.innerHTML = '<option value="">-- Seleccione un curso --</option>';
                    data.forEach(curso => {
                        const opt = document.createElement('option');
                        opt.value = curso.id;
                        opt.textContent = curso.nombre_curso;
                        cursoSelect.appendChild(opt);
                    });
                    cursoSelect.disabled = false;
                });
        });

        document.getElementById('modalCurso')?.addEventListener('change', function() {
            const cursoId = this.value;
            const horarioSelect = document.getElementById('modalHorario');
            horarioSelect.disabled = true;
            horarioSelect.innerHTML = '<option value="">Cargando horarios...</option>';

            if (!cursoId) return;

            fetch(`../../controllers/get_horarios.php?curso_id=${cursoId}`)
                .then(res => res.json())
                .then(data => {
                    horarioSelect.innerHTML = '<option value="">-- Seleccione un horario --</option>';
                    data.forEach(h => {
                        const opt = document.createElement('option');
                        opt.value = h.id;
                        opt.textContent = `${h.dia} ${h.hora_inicio} - ${h.hora_fin}`;
                        horarioSelect.appendChild(opt);
                    });
                    horarioSelect.disabled = false;
                });
        });

        document.getElementById('formAsignarModal')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const data = new FormData(form);

            fetch('../../controllers/insert_asignacion.php', {
                    method: 'POST',
                    body: data
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const modalEl = document.getElementById('modalAsignarDocente');
                        const modalInstance = bootstrap.Modal.getInstance(modalEl);
                        modalInstance.hide();

                        modalEl.addEventListener('hidden.bs.modal', () => {
                            document.querySelector('.modal-backdrop')?.remove();
                            showNotification('Asignación completada con éxito.', 'success', 'check');
                            setTimeout(() => location.reload(), 2000);
                        }, {
                            once: true
                        });
                    } else {
                        showNotification(data.message, 'warning', 'exclamation-triangle');
                    }
                })
                .catch(err => {
                    console.error("❌ Error al guardar:", err);
                    showNotification('Error de red al guardar asignación.', 'danger', 'times');
                });
        });

        function showNotification(message, type, icon) {
            const toastEl = document.getElementById('liveToast');
            const toastHeader = toastEl.querySelector('.toast-header');
            const toastBody = toastEl.querySelector('.toast-body');

            toastHeader.className = 'toast-header';
            toastBody.className = 'toast-body';

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
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
        }

        document.getElementById('modalAsignarDocente')?.addEventListener('hidden.bs.modal', () => {
            const toast = bootstrap.Toast.getInstance(document.getElementById('liveToast'));
            toast?.hide();
        });


        document.querySelectorAll('.btnEditarAsignacion').forEach(boton => {
            boton.addEventListener('click', () => {
                // ¿Llega aquí?
                console.log("🟢 Botón editar clickeado");
                // ...
                new bootstrap.Modal(document.getElementById('modalEditarAsignacion')).show();
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Botón editar (usa clase .btnEditarAsignacion y dataset con datos)
            document.querySelectorAll('.btnEditarAsignacion').forEach(boton => {
                boton.addEventListener('click', () => {
                    const id = boton.dataset.id;
                    const areaId = boton.dataset.areaId;
                    const cursoId = boton.dataset.cursoId;
                    const horarioId = boton.dataset.horarioId;
                    const profesorId = boton.dataset.profesorId;

                    document.getElementById('editarAsignacionId').value = id;

                    // Cargar áreas (solo una vez si es estático, opcional si ya tienes esto)
                    fetch('../../controllers/get_areas.php')
                        .then(res => res.json())
                        .then(areas => {
                            const selectArea = document.getElementById('editarArea');
                            selectArea.innerHTML = '<option value="">Seleccione un área</option>';
                            areas.forEach(a => {
                                const opt = new Option(a.nombre_area, a.id);
                                if (a.id == areaId) opt.selected = true;
                                selectArea.appendChild(opt);
                            });

                            // Luego cargar cursos para esa área
                            return fetch(`../../controllers/get_cursos_por_area.php?area_id=${areaId}`);
                        })
                        .then(res => res.json())
                        .then(cursos => {
                            const selectCurso = document.getElementById('editarCurso');
                            selectCurso.innerHTML = '<option value="">Seleccione un curso</option>';
                            cursos.forEach(c => {
                                const opt = new Option(c.nombre_curso, c.id);
                                if (c.id == cursoId) opt.selected = true;
                                selectCurso.appendChild(opt);
                            });

                            // Y luego cargar horarios para ese curso
                            return fetch(`../../controllers/get_horarios.php?curso_id=${cursoId}`);
                        })
                        .then(res => res.json())
                        .then(horarios => {
                            const selectHorario = document.getElementById('editarHorario');
                            selectHorario.innerHTML = '<option value="">Seleccione un horario</option>';
                            horarios.forEach(h => {
                                const opt = new Option(`${h.dia} ${h.hora_inicio} - ${h.hora_fin}`, h.id);
                                if (h.id == horarioId) opt.selected = true;
                                selectHorario.appendChild(opt);
                            });

                            // Finalmente cargar profesores
                            return fetch(`../../controllers/get_profesores.php`);
                        })
                        .then(res => res.json())
                        .then(profesores => {
                            const selectProfesor = document.getElementById('editarProfesor');
                            selectProfesor.innerHTML = '<option value="">Seleccione un profesor</option>';
                            profesores.forEach(p => {
                                const opt = new Option(`${p.nombre} ${p.apellidos}`, p.id);
                                if (p.id == profesorId) opt.selected = true;
                                selectProfesor.appendChild(opt);
                            });
                        });
                });
            });

            // Si cambia el área, cargar cursos
            document.getElementById('editarArea').addEventListener('change', function() {
                const areaId = this.value;
                const cursoSelect = document.getElementById('editarCurso');
                const horarioSelect = document.getElementById('editarHorario');

                cursoSelect.disabled = true;
                horarioSelect.disabled = true;
                cursoSelect.innerHTML = '<option value="">Cargando cursos...</option>';
                horarioSelect.innerHTML = '<option value="">-- Seleccione un curso primero --</option>';

                fetch(`../../controllers/get_cursos_por_area.php?area_id=${areaId}`)
                    .then(res => res.json())
                    .then(cursos => {
                        cursoSelect.innerHTML = '<option value="">Seleccione un curso</option>';
                        cursos.forEach(curso => {
                            const opt = new Option(curso.nombre_curso, curso.id);
                            cursoSelect.appendChild(opt);
                        });
                        cursoSelect.disabled = false;
                    });
            });

            // Si cambia el curso, cargar horarios
            document.getElementById('editarCurso').addEventListener('change', function() {
                const cursoId = this.value;
                const horarioSelect = document.getElementById('editarHorario');
                horarioSelect.disabled = true;
                horarioSelect.innerHTML = '<option value="">Cargando horarios...</option>';

                fetch(`../../controllers/get_horarios.php?curso_id=${cursoId}`)
                    .then(res => res.json())
                    .then(horarios => {
                        horarioSelect.innerHTML = '<option value="">Seleccione un horario</option>';
                        horarios.forEach(h => {
                            const opt = new Option(`${h.dia} ${h.hora_inicio} - ${h.hora_fin}`, h.id);
                            horarioSelect.appendChild(opt);
                        });
                        horarioSelect.disabled = false;
                    });
            });

            // Enviar formulario
            document.getElementById('formEditarAsignacion').addEventListener('submit', function(e) {
                e.preventDefault();
                const data = new FormData(this);

                fetch('../../controllers/update_asignacion.php', {
                        method: 'POST',
                        body: data
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditarAsignacion'));
                            modal.hide();
                            showNotification('Asignación actualizada con éxito', 'success', 'check');
                            setTimeout(() => location.reload(), 2000);
                        } else {
                            showNotification(data.message, 'danger', 'exclamation-triangle');
                        }
                    })
                    .catch(err => {
                        console.error('Error:', err);
                        showNotification('Error de red o del servidor', 'danger', 'server');
                    });
            });
        });
    </script>

</body>

</html>