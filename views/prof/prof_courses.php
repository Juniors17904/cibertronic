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
            <!-- <main class="col-md-7 col-lg-8 px-5 py-4"> -->
            <main class="col-md-8 col-lg-9 px-md-5 py-4">
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

                                    <div class="card-body d-flex flex-column">
                                        <!-- Información del curso -->
                                        <div class="mb-3">
                                            <h5 class="card-title mb-2"><?= htmlspecialchars($row['nombre_curso']) ?></h5>

                                            <div class="mb-1"><strong>Código:</strong> <?= htmlspecialchars($row['codigo_asignacion']) ?></div>
                                            <div class="text-muted mb-1"><?= htmlspecialchars($row['nombre_area']) ?></div>
                                            <div class="mb-1">
                                                <strong>Horario:</strong> <?= $row['dia'] ?> <?= $row['hora_inicio'] ?> - <?= $row['hora_fin'] ?>
                                            </div>
                                            <div>
                                                <span class="badge bg-info text-dark">Alumnos: <?= $row['total_alumnos'] ?></span>
                                            </div>
                                        </div>

                                        <!-- Botones -->
                                        <div class="d-flex justify-content-end gap-2 mt-auto">
                                            <form action="prof_asistencia.php" method="POST">
                                                <input type="hidden" name="nombre_curso" value="<?= htmlspecialchars($row['nombre_curso']) ?>">
                                                <input type="hidden" name="horario_texto" value="<?= htmlspecialchars($row['dia'] . ' ' . $row['hora_inicio'] . ' - ' . $row['hora_fin']) ?>">
                                                <input type="hidden" name="curso_id" value="<?= $row['curso_id'] ?>">
                                                <input type="hidden" name="horario_id" value="<?= $row['horario_id'] ?>">
                                                <input type="hidden" name="codigo_asignacion" value="<?= $row['codigo_asignacion'] ?>">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-check-circle me-1"></i> Asistencia
                                                </button>
                                            </form>

                                            <form action="prof_notas.php" method="POST">
                                                <input type="hidden" name="nombre_curso" value="<?= htmlspecialchars($row['nombre_curso']) ?>">
                                                <input type="hidden" name="horario_texto" value="<?= htmlspecialchars($row['dia'] . ' ' . $row['hora_inicio'] . ' - ' . $row['hora_fin']) ?>">
                                                <input type="hidden" name="curso_id" value="<?= $row['curso_id'] ?>">
                                                <input type="hidden" name="horario_id" value="<?= $row['horario_id'] ?>">
                                                <input type="hidden" name="codigo_asignacion" value="<?= $row['codigo_asignacion'] ?>">
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-edit me-1"></i> Calificaciones
                                                </button>
                                            </form>
                                        </div>
                                    </div>



                                </div>
                            </div>

                        <?php
                        endwhile;
                    else:
                        ?>
                        <div class="alert alert-warning text-center m-3" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> No tienes cursos asignados actualmente.
                        </div>
                    <?php endif; ?>
                </div>
            </main>

        </div>
    </div>
    <?php
    include '../modals/toast_notificacion.php';
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const msgSuccess = sessionStorage.getItem('flash_success');
            const msgWarning = sessionStorage.getItem('flash_warning');
            if (msgSuccess) {
                showNotification(msgSuccess, 'success', 'check-circle');
                sessionStorage.removeItem('flash_success');
            } else if (msgWarning) {
                showNotification(msgWarning, 'warning', 'ban');
                sessionStorage.removeItem('flash_warning');
            }
        });

        function showNotification(message, type = 'success', icon = 'check-circle') {
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
            setTimeout(() => toast.hide(), 5000);
        }
    </script>





</body>

</html>