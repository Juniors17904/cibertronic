<?php
require_once '../../controllers/auth_check.php';
require_once '../../controllers/get_admin_data.php';
$admin = getAdminData($conn, $_SESSION['user_id']);
$asignacion_id = $_GET['asignacion_id'] ?? 0;

include '../header.php';

$asig = $conn->query("
    SELECT 
        a.*,
        c.nombre_curso,
        h.dia, h.hora_inicio, h.hora_fin,
        p.nombre AS nombre_profesor,
        p.apellidos AS apellidos_profesor,
        p.dni AS dni_profesor,
        p.telefono AS telefono_profesor,
        p.codigo_usuario AS usuario_profesor_id,
        u.correo AS correo_profesor,
        u.id AS id_usuario_profesor
    FROM asignaciones a
    JOIN cursos c ON a.curso_id = c.id
    JOIN horarios h ON a.horario_id = h.id
    JOIN profesores p ON a.profesor_id = p.id
    JOIN usuarios u ON p.usuario_id = u.id
    WHERE a.id = $asignacion_id
")->fetch_assoc();



$alumnos = $conn->query("
    SELECT al.*, u.correo
    FROM matriculas m
    JOIN alumnos al ON m.alumno_id = al.id
    JOIN usuarios u ON al.usuario_id = u.id
    WHERE m.curso_id = {$asig['curso_id']} AND m.horario_id = {$asig['horario_id']}
    ");
?>

<body>
    <?php include 'cabecera.php'; ?>

    <div class="container-fluid mt-0">
        <div class="row">
            <?php include 'lateral.php'; ?>
            <!-- <main class="col-md-7 col-lg-8 px-5"> -->
            <main class="col-md-8 col-lg-9 px-2 px-md-5 py-4">








                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Detalles de Asignación</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!--Columna 1: Detalles de la asignación -->
                            <div class="col-md-6 mb-3">
                                <p><strong>Código:</strong> <?= htmlspecialchars($asig['codigo_asignacion']) ?></p>
                                <p><strong>Curso:</strong> <?= htmlspecialchars($asig['nombre_curso']) ?></p>
                                <p><strong>Horario:</strong> <?= htmlspecialchars("{$asig['dia']} {$asig['hora_inicio']} - {$asig['hora_fin']}") ?></p>
                                <p><strong>Fecha Inicio:</strong> <?= htmlspecialchars($asig['fecha_inicio']) ?></p>
                                <p><strong>Fecha Fin:</strong> <?= htmlspecialchars($asig['fecha_fin']) ?></p>
                                <p><strong>Estado:</strong> <?= htmlspecialchars($asig['estado']) ?></p>
                            </div>

                            <!-- Columna 2: Detalles del profesor -->
                            <div class="col-md-6 mb-3">
                                <p><strong>Profesor:</strong> <?= htmlspecialchars($asig['nombre_profesor'] . ' ' . $asig['apellidos_profesor']) ?></p>
                                <p><strong>Código Usuario:</strong> <?= htmlspecialchars($asig['usuario_profesor_id']) ?></p>
                                <p><strong>Correo:</strong> <?= htmlspecialchars($asig['correo_profesor']) ?></p>
                                <p><strong>DNI Profesor:</strong> <?= htmlspecialchars($asig['dni_profesor']) ?></p>
                                <p><strong>Teléfono Profesor:</strong> <?= htmlspecialchars($asig['telefono_profesor']) ?></p>
                            </div>
                        </div>
                    </div>


                </div>

                <div class="card shadow">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0">Alumnos Matriculados</h6>
                    </div>
                    <div class="card-body">
                        <?php if ($alumnos->num_rows > 0): ?>
                            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                                <table class="table table-striped table-hover text-center table-sm mb-0">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th>#</th>
                                            <th>Nombre</th>
                                            <th>Apellidos</th>
                                            <th>DNI</th>
                                            <th>Correo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $n = 1;
                                        while ($row = $alumnos->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $n++ ?></td>
                                                <td><?= htmlspecialchars($row['nombre']) ?></td>
                                                <td><?= htmlspecialchars($row['apellidos']) ?></td>
                                                <td><?= htmlspecialchars($row['dni']) ?></td>
                                                <td><?= htmlspecialchars($row['correo']) ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No hay alumnos matriculados.</p>
                        <?php endif; ?>


                        <!-- Botón VOLVER -->
                        <a href="admin_courses.php" class="btn btn-secondary mt-3">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>

                        <!-- Botón Ver Detalles Asistencia -->
                        <a href="admin_detalles_asignacion_prof.php?asignacion_id=<?= urlencode($asignacion_id) ?>&profesor_id=<?= urlencode($asig['usuario_profesor_id']) ?>" class="btn btn-info mt-3 ms-2">
                            <i class="fas fa-calendar-check"></i> Ver Detalles Asistencia
                        </a>

                        <!-- Botón Ver Detalles Notas -->
                        <a href="admin_detalles_notas.php?asignacion_id=<?= urlencode($asignacion_id) ?>&profesor_id=<?= urlencode($asig['usuario_profesor_id']) ?>" class="btn btn-primary mt-3 ms-2">
                            <i class="fas fa-clipboard-list"></i> Ver Detalles Notas
                        </a>





                    </div>
                </div>

            </main>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>