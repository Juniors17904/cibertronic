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

            <main class="col-md-7 col-lg-8 px-5 py-4">
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Asignaciones Registradas</h4>
                        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalAsignarDocente">
                            <i class="fas fa-plus"></i> Nuevo Registro
                        </button>
                    </div>
                </div>







                <div class="card-body">
                    <?php
                    $query = "
                            SELECT a.id, 
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
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th>#</th>
                                        <th>Curso</th>
                                        <th>Horario</th>
                                        <th>Profesor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $n = 1;
                                    while ($row = $asignaciones->fetch_assoc()): ?>
                                        <tr>
                                            <td class="text-center"><?= $n++ ?></td>
                                            <td><?= htmlspecialchars($row['nombre_curso']) ?></td>
                                            <td><?= htmlspecialchars("{$row['dia']} - {$row['hora_inicio']} a {$row['hora_fin']}") ?></td>
                                            <td><?= htmlspecialchars("{$row['nombre_profesor']} {$row['apellidos']}") ?></td>
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
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Cuando cambia el área
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

        // Cuando cambia el curso
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

        // Envío del formulario
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
                        // Cerrar modal
                        const modalEl = document.getElementById('modalAsignarDocente');
                        const modalInstance = bootstrap.Modal.getInstance(modalEl);
                        modalInstance.hide();

                        // Esperar que se cierre y entonces:
                        modalEl.addEventListener('hidden.bs.modal', () => {
                            // Quitar backdrop manualmente si persiste
                            document.querySelector('.modal-backdrop')?.remove();

                            // Mostrar toast
                            showNotification('Asignación completada con éxito.', 'success', 'check');

                            // Esperar a mostrar el toast antes de recargar
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

        // Función para mostrar toast
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

        // Si el modal se cierra manualmente, ocultar el toast
        document.getElementById('modalAsignarDocente').addEventListener('hidden.bs.modal', () => {
            const toast = bootstrap.Toast.getInstance(document.getElementById('liveToast'));
            toast?.hide();
        });
    </script>



</body>

</html>