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
            <main class="col-md-8 col-lg-9 px-md-5 py-4">

                <?php
                $curso_id = $_GET['curso_id'] ?? 0;
                $horario_id = $_GET['horario_id'] ?? 0;

                $curso = $conn->query("SELECT * FROM cursos WHERE id = $curso_id")->fetch_assoc();
                $horario = $conn->query("SELECT * FROM horarios WHERE id = $horario_id")->fetch_assoc();
                ?>

                <div class="container mt-4">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Alumnos Matriculados</h5>
                            <small class="text-white">
                                Curso: <?= htmlspecialchars($curso['nombre_curso']) ?> |
                                Horario: <?= htmlspecialchars("{$horario['dia']} {$horario['hora_inicio']} - {$horario['hora_fin']}") ?>
                            </small>
                        </div>
                        <div class="card-body">
                            <?php
                            $query = "
                            SELECT 
                                a.id AS alumno_id,
                                a.nombre,
                                a.apellidos,
                                a.dni,
                                a.telefono,
                                a.codigo_usuario,
                                u.correo
                            FROM matriculas m
                            JOIN alumnos a ON m.alumno_id = a.id
                            JOIN usuarios u ON a.usuario_id = u.id
                            WHERE m.curso_id = $curso_id AND m.horario_id = $horario_id
                            ORDER BY a.apellidos ASC

                        ";

                            $alumnos = $conn->query($query);

                            if ($alumnos && $alumnos->num_rows > 0): ?>
                                <div class="mx-n3 mx-md-0">
                                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto; overflow-x: auto;">
                                        <table class="table table-striped table-hover table-sm mb-0">
                                            <thead class="table-light sticky-top">
                                            <tr>
                                                <th>#</th>
                                                <th>Código de Alumno</th>
                                                <th>Nombre</th>
                                                <th>Apellidos</th>
                                                <th>DNI</th>
                                                <th>Teléfono</th>
                                                <th>Correo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $n = 1;
                                            while ($row = $alumnos->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?= $n++ ?></td>

                                                    <td><?= htmlspecialchars($row['codigo_usuario']) ?></td>
                                                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                                                    <td><?= htmlspecialchars($row['apellidos']) ?></td>
                                                    <td><?= htmlspecialchars($row['dni']) ?></td>
                                                    <td><?= htmlspecialchars($row['telefono']) ?></td>
                                                    <td><?= htmlspecialchars($row['correo']) ?></td>

                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning mb-0">No hay alumnos matriculados en este curso.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>