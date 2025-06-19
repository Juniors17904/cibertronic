<?php
require_once '../../controllers/auth_check.php';
require_once '../../controllers/get_prof_data.php';
$prof = getProfData($conn, $_SESSION['user_id']);
if (!$prof) {
    die("Profesor no encontrado.");
}

$cursos_total = $conn->query(
    "SELECT COUNT(*) AS total 
        FROM asignaciones 
        WHERE profesor_id = (
            SELECT id FROM profesores WHERE usuario_id = {$_SESSION['user_id']}
        )"
)->fetch_assoc()['total'];


$alumnos_total = $conn->query("
    SELECT COUNT(*) AS total 
    FROM matriculas m
    INNER JOIN asignaciones a ON m.curso_id = a.curso_id AND m.horario_id = a.horario_id
    INNER JOIN profesores p ON a.profesor_id = p.id
    WHERE p.usuario_id = {$_SESSION['user_id']}
")->fetch_assoc()['total'];


include '../header.php';
?>

<body>
    <?php include 'cabecera.php'; ?>
    <div class="container-fluid mt-0">
        <div class="row">
            <?php include 'lateral.php'; ?>

            <!-- Contenido principal -->
            <main class="col-md-8 col-lg-9 px-md-5 py-4">
                <!-- Bienvenida -->
                <div class="mb-4">
                    <h2 class="mb-3 text-primary">Bienvenido, <?= htmlspecialchars($prof['nombre']) ?> </h2>
                    <p class="text-muted">Este es tu panel de profesor. Desde aquí podrás ver la información relacionada a tus cursos, alumnos y clases.</p>
                </div>

                <!-- Tarjetas -->
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card bg-warning text-white shadow p-3">
                            <h5 class="card-title"><i class="fas fa-book me-2"></i> Cursos Asignados</h5>
                            <p class="card-text fs-4">

                                <?php echo $cursos_total ?>

                            </p>
                            <a href="prof_courses.php" class="text-white">Ver cursos <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card bg-success text-white shadow p-3">
                            <h5 class="card-title"><i class="fas fa-users me-2"></i> Total de Alumnos</h5>
                            <p class="card-text fs-4">
                                <?php echo $alumnos_total ?>
                            </p>
                            <a href="#" class="text-white">Ver lista <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Misión institucional -->
                <div class="row mb-5">
                    <h4 class="mb-4 border-bottom pb-2 text-center">Acerca de Cibertronic</h4>
                    <section class="mb-3">
                        <h5><i class="bi bi-bullseye"></i> Misión</h5>
                        <p>Brindar educación de calidad a nuestros alumnos mediante docentes comprometidos.</p>
                    </section>
                    <section class="mb-3">
                        <h5><i class="bi bi-eye-fill"></i> Visión</h5>
                        <p>Ser reconocidos como líderes en formación técnica con impacto social.</p>
                    </section>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>