<?php
require_once '../../controllers/auth_check.php';
require_once '../../controllers/get_admin_data.php';
$admin = getAdminData($conn, $_SESSION['user_id']);
if (!$admin) die("Administrador no encontrado.");
include '../header.php';

$matricula_id = $_GET['matricula_id'] ?? 0;

// Consulta completa:
$sql = "SELECT 
                m.id AS matricula_id,
                m.estado AS estado,
                m.fecha_matricula,
                m.curso_id,
                m.horario_id,
                ar.id AS area_id, 
                a.codigo_usuario AS codigo_usuario,
                a.nombre AS alumno_nombre,
                a.apellidos AS alumno_apellidos,
                a.dni AS alumno_dni,
                a.telefono AS alumno_telefono,
                u.correo AS alumno_correo,
                c.nombre_curso,
                ar.nombre_area,
                h.dia, h.hora_inicio, h.hora_fin,
                asig.codigo_asignacion,
                p.nombre AS profesor_nombre,
                p.apellidos AS profesor_apellidos,
                p.dni AS profesor_dni,
                p.telefono AS profesor_telefono,
                p.codigo_usuario AS codigo_usuariop,
                pu.correo AS profesor_correo
                    FROM matriculas m
                    JOIN alumnos a ON m.alumno_id = a.id
                    JOIN usuarios u ON a.usuario_id = u.id
                    JOIN cursos c ON m.curso_id = c.id
                    LEFT JOIN areas ar ON c.id_area = ar.id
                    JOIN horarios h ON m.horario_id = h.id
                    LEFT JOIN asignaciones asig ON asig.curso_id = c.id AND asig.horario_id = h.id
                    LEFT JOIN profesores p ON asig.profesor_id = p.id
                    LEFT JOIN usuarios pu ON p.usuario_id = pu.id
                    WHERE m.id = $matricula_id";

$result = $conn->query($sql);
$matricula = $result->fetch_assoc();
?>

<body>
    <?php include 'cabecera.php'; ?>

    <div class="container-fluid mt-0">
        <div class="row">
            <?php include 'lateral.php'; ?>

            <main class="col-md-8 col-lg-9 px-md-5 py-4">

                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Detalles de Matrícula</h5>
                    </div>
                    <div class="card-body">
                        <!-- Alumno -->
                        <p><strong>Código Alumno:</strong> <?= htmlspecialchars($matricula['codigo_usuario']) ?></p>
                        <p><strong>Alumno:</strong> <?= htmlspecialchars($matricula['alumno_nombre'] . ' ' . $matricula['alumno_apellidos']) ?></p>
                        <p><strong>DNI:</strong> <?= htmlspecialchars($matricula['alumno_dni']) ?></p>
                        <p><strong>Teléfono:</strong> <?= htmlspecialchars($matricula['alumno_telefono']) ?></p>
                        <p><strong>Correo:</strong> <?= htmlspecialchars($matricula['alumno_correo']) ?></p>

                        <hr>

                        <!-- Curso -->
                        <p><strong>Código de Asignación:</strong> <?= htmlspecialchars($matricula['codigo_asignacion']) ?></p>
                        <p><strong>Área:</strong> <?= htmlspecialchars($matricula['nombre_area']) ?></p>
                        <p><strong>Curso:</strong> <?= htmlspecialchars($matricula['nombre_curso']) ?></p>
                        <p><strong>Horario:</strong> <?= htmlspecialchars("{$matricula['dia']} {$matricula['hora_inicio']} - {$matricula['hora_fin']}") ?></p>
                        <p><strong>Fecha Matrícula:</strong> <?= htmlspecialchars($matricula['fecha_matricula']) ?></p>
                        <p><strong>Estado de Matrícula:</strong> <?= htmlspecialchars($matricula['estado']) ?></p>


                        <hr>

                        <!-- Profesor -->
                        <p><strong>Código de Profesor:</strong> <?= htmlspecialchars($matricula['codigo_usuariop']) ?></p>
                        <p><strong>Profesor Asignado:</strong> <?= htmlspecialchars($matricula['profesor_nombre'] . ' ' . $matricula['profesor_apellidos']) ?></p>
                        <p><strong>DNI Profesor:</strong> <?= htmlspecialchars($matricula['profesor_dni']) ?></p>
                        <p><strong>Teléfono Profesor:</strong> <?= htmlspecialchars($matricula['profesor_telefono']) ?></p>
                        <p><strong>Correo Profesor:</strong> <?= htmlspecialchars($matricula['profesor_correo']) ?></p>

                        <?php if (empty($matricula['profesor_nombre'])): ?>
                            <a href="admin_courses.php?area_id=<?= $matricula['area_id'] ?>&curso_id=<?= $matricula['curso_id'] ?>&horario_id=<?= $matricula['horario_id'] ?>" class="btn btn-primary mt-3">
                                <i class="fas fa-user-plus"></i> Asignar Profesor
                            </a>
                        <?php endif; ?>



                        <a href="admin_matriculas.php" class="btn btn-secondary mt-3">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>