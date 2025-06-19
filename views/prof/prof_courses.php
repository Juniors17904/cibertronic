<?php
require_once '../../controllers/auth_check.php';
require_once '../../controllers/get_prof_data.php';

$prof = getProfData($conn, $_SESSION['user_id']);
if (!$prof) {
    die("Profesor no encontrado.");
}
include '../header.php';
?>

<body>
    <?php include 'cabecera.php' ?>
    <div class="container-fluid mt-0">
        <div class="row">
            <?php include 'lateral.php' ?>

            <!-- Contenido principal -->
            <main class="col-md-6 col-lg-7 px-5 py-4">
                <h3 class="mb-4 text-primary">Mis Cursos Asignados</h3>
                <div class="row">
                    <?php
                    require_once '../../controllers/get_prof_courses.php';
                    $result = getCursosAsignados($conn, $_SESSION['user_id']);
                    if ($result && $result->num_rows > 0):
                        while ($row = $result->fetch_assoc()):
                    ?>
                            <!-- Dentro del while de cada curso -->
                            <div class="col-md-6 mb-4">
                                <div class="card shadow border-0">

                                    <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                                        <div>
                                            <!--curso-->
                                            <h5 class="card-title mb-3"><?= htmlspecialchars($row['nombre_curso']) ?></h5>
                                            <!-- area -->
                                            <h6 class="card-subtitle text-muted mb-2"><?= htmlspecialchars($row['nombre_area']) ?></h6>
                                            <!--horario-->
                                            <p class="card-text mb-2">
                                                <strong>Horario:</strong> <?= $row['dia'] ?> <?= $row['hora_inicio'] ?> - <?= $row['hora_fin'] ?>
                                            </p>
                                            <!-- Total de alumno -->
                                            <span class="badge bg-info text-dark">Alumnos: <?= $row['total_alumnos'] ?></span>
                                        </div>
                                        <!-- Boton asignar -->
                                        <div class="ms-auto">
                                            <button
                                                class="btn btn-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalAsistencia"
                                                data-curso="<?= htmlspecialchars($row['nombre_curso']) ?>"
                                                data-horario="<?= htmlspecialchars($row['dia']) . ' ' . $row['hora_inicio'] . ' - ' . $row['hora_fin'] ?>"
                                                data-cursoid="<?= $row['curso_id'] ?>"
                                                data-horarioid="<?= $row['horario_id'] ?>">
                                                <i class="fas fa-check-circle me-1"></i> Asistencia
                                            </button>


                                        </div>
                                    </div>


                                </div>
                            </div>

                        <?php
                        endwhile;
                    else:
                        ?>
                        <p>No tienes cursos asignados actualmente.</p>
                    <?php endif; ?>
                </div>
            </main>

        </div>
    </div>
    <?php
    include '../modals/modal_asistencias.php';
    include '../modals/toast_notificacion.php';
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('modalAsistencia');

            modal.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget;

                const curso = button.getAttribute('data-curso');
                const horario = button.getAttribute('data-horario');
                const cursoId = button.getAttribute('data-cursoid');
                const horarioId = button.getAttribute('data-horarioid');

                document.getElementById('modalCursoNombre').textContent = curso;
                document.getElementById('modalHorario').textContent = horario;
                document.getElementById('curso_id').value = cursoId;
                document.getElementById('horario_id').value = horarioId;

                const tbody = document.getElementById('tablaAlumnos');
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Cargando...</td></tr>';

                fetch(`../../controllers/get_alumnos_matriculados.php?curso_id=${cursoId}&horario_id=${horarioId}`)
                    .then(res => res.json())
                    .then(data => {
                        tbody.innerHTML = '';
                        if (data.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No hay alumnos.</td></tr>';
                        } else {
                            data.forEach(alumno => {
                                const row = `
                            <tr>
                                <td>${alumno.nombre}</td>
                                <td class="text-center">
                                    <input type="radio" name="asistencia[${alumno.id}]" value="Presente" required>
                                </td>
                                <td class="text-center">
                                    <input type="radio" name="asistencia[${alumno.id}]" value="Ausente">
                                </td>
                                <td class="text-center">
                                    <input type="radio" name="asistencia[${alumno.id}]" value="Justificado">
                                </td>
                            </tr>
                        `;
                                tbody.insertAdjacentHTML('beforeend', row);
                            });
                        }
                    })
                    .catch(err => {
                        console.error('Error al cargar alumnos:', err);
                        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Error al cargar.</td></tr>';
                    });
            });
        });

        document.getElementById('formAsistencia').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);

            fetch('../../controllers/insert_asistencia.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Asistencia registrada correctamente', 'success', 'check-circle');
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalAsistencia'));
                        modal.hide();
                    } else {
                        showNotification(data.error || 'No se pudo guardar', 'danger', 'exclamation-circle');
                    }

                })
                .catch(err => {
                    console.error('Error al enviar:', err);
                    showNotification('Error de red o del servidor', 'danger', 'server');
                });
        });

        function showNotification(message, type = 'success', icon = 'check-circle') {
            const toastEl = document.getElementById('liveToast');
            const toastHeader = toastEl.querySelector('.toast-header');
            const toastBody = toastEl.querySelector('.toast-body');

            // Resetear clases anteriores
            toastHeader.className = 'toast-header';
            toastBody.className = 'toast-body';

            // Aplicar estilo según tipo
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

            // Agregar contenido con ícono
            toastBody.innerHTML = `<i class="fas fa-${icon} me-2"></i> ${message}`;

            // Mostrar el toast
            const toast = new bootstrap.Toast(toastEl);
            toast.show();

            // Ocultar automáticamente en 5 segundos
            setTimeout(() => toast.hide(), 5000);
        }
    </script>




</body>

</html>