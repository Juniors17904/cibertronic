<?php
require_once '../../controllers/auth_check.php';
require_once '../../controllers/get_prof_data.php';
require_once '../../controllers/get_prof_courses.php';

$prof = getProfData($conn, $_SESSION['user_id']);
if (!$prof) {
    die("Profesor no encontrado.");
}

$cursos = getCursosAsignados($conn, $_SESSION['user_id']);
include '../header.php';
?>

<body>
    <?php include 'cabecera.php'; ?>
    <div class="container-fluid mt-0">
        <div class="row">
            <?php include 'lateral.php'; ?>

            <main class="col-md-6 col-lg-7 px-5 py-4">
                <h4 class="text-primary mb-4"><i class="fas fa-calendar-check me-2"></i> Seleccionar Curso para Asistencia</h4>

                <?php if ($cursos && $cursos->num_rows > 0): ?>
                    <div class="card shadow border-0">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="table-primary text-center">
                                        <tr>
                                            <th>Curso</th>
                                            <th>Área</th>
                                            <th>Horario</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $cursos->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($row['nombre_curso']) ?></td>
                                                <td><?= htmlspecialchars($row['nombre_area']) ?></td>
                                                <td><?= htmlspecialchars($row['dia']) . ' ' . $row['hora_inicio'] . ' - ' . $row['hora_fin'] ?></td>
                                                <td>
                                                    <form action="prof_asistencia.php" method="POST">
                                                        <input type="hidden" name="nombre_curso" value="<?= htmlspecialchars($row['nombre_curso']) ?>">
                                                        <input type="hidden" name="horario_texto" value="<?= htmlspecialchars($row['dia'] . ' ' . $row['hora_inicio'] . ' - ' . $row['hora_fin']) ?>">
                                                        <input type="hidden" name="curso_id" value="<?= $row['curso_id'] ?>">
                                                        <input type="hidden" name="horario_id" value="<?= $row['horario_id'] ?>">

                                                        <button type="submit" class="btn btn-outline-primary btn-sm">
                                                            <i class="fas fa-check-circle me-1"></i> Registrar Asistencia
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning text-center m-3" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i> No tienes cursos asignados actualmente.
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <?php include '../modals/toast_notificacion.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>