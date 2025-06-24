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

            <main class="col-md-7 col-lg-8 px-5 py-4">
                <!-- Bienvenida -->
                <div class="mb-5">
                    <h2 class="mb-2 text-primary">Bienvenido, <?= htmlspecialchars($prof['nombre']) ?> </h2>
                    <p class="text-muted fs-5">Este es tu panel de profesor. Desde aquí podrás ver la información relacionada a tus cursos, alumnos y clases.</p>
                </div>

                <!-- Tarjetas principales -->
                <div class="row g-4 mb-4">
                    <div class="col-md-6 col-xl-4">
                        <div class="card bg-warning text-white shadow h-100">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-book me-2"></i> Cursos Asignados</h5>
                                <p class="display-6 fw-bold"><?= $cursos_total ?></p>
                                <a href="prof_courses.php" class="text-white text-decoration-underline">Ver cursos →</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="card bg-success text-white shadow h-100">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-users me-2"></i> Total de Alumnos</h5>
                                <p class="display-6 fw-bold"><?= $alumnos_total ?></p>
                                <a href="#" class="text-white text-decoration-underline">Ver lista →</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 col-xl-4">
                        <div class="card bg-info text-white shadow h-100">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-calendar-alt me-2"></i> Fecha Actual</h5>
                                <p class="fs-4"><?= date('d/m/Y') ?></p>
                                <span class="text-white-50">Hoy es <?= strftime('%A') ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Misión y visión -->
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title text-center text-primary mb-3"><i class="bi bi-bullseye"></i> Misión</h5>
                                <p class="card-text text-muted text-justify">
                                    Brindar educación de calidad a nuestros alumnos mediante docentes comprometidos.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title text-center text-primary mb-3"><i class="bi bi-eye-fill"></i> Visión</h5>
                                <p class="card-text text-muted text-justify">
                                    Ser reconocidos como líderes en formación técnica con impacto social.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>



        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>