<?php
require_once '../controllers/auth_check.php';
require_once '../controllers/get_admin_data.php';
require_once '../config/config.php';

$admin = getAdminData($conn, $_SESSION['user_id']);
if (!$admin) {
    die("Administrador no encontrado.");
}

// Contadores desde la base de datos
$usuarios_total    = $conn->query("SELECT COUNT(*) AS total FROM usuarios")->fetch_assoc()['total'];
$cursos_total      = $conn->query("SELECT COUNT(*) AS total FROM cursos")->fetch_assoc()['total'];
$matriculas_total  = $conn->query("SELECT COUNT(*) AS total FROM matriculas")->fetch_assoc()['total'];

include 'header.php';
?>

<body>
    <?php include 'cabecera.php' ?>
    <div class="container-fluid mt-0">
        <div class="row">
            <?php include 'lateral.php' ?>

            <!-- Contenido principal -->
            <main class="col-md-8 col-lg-9 px-md-5 py-4">
                <!-- Tarjetas de Resumen -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="card bg-primary text-white p-3 shadow">
                            <h5 class="card-title"><i class="bi bi-people-fill"></i> Usuarios</h5>
                            <p class="card-text fs-4"><?= $usuarios_total ?></p>
                            <a href="admin_ges_user.php" class="text-white">Ver más <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card bg-success text-white p-3 shadow">
                            <h5 class="card-title"><i class="bi bi-book-fill"></i> Cursos</h5>
                            <p class="card-text fs-4"><?= $cursos_total ?></p>
                            <a href="admin_courses.php" class="text-white">Ver más <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card bg-warning text-dark p-3 shadow">
                            <h5 class="card-title"><i class="bi bi-clipboard-check-fill"></i> Matrículas</h5>
                            <p class="card-text fs-4"><?= $matriculas_total ?></p>
                            <a href="admin_matriculas.php" class="text-dark">Ver más <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Acerca de la Institución -->
                <div class="row mb-5">
                    <h2 class="mb-4 text-center border-bottom pb-2">Acerca de Nuestra Institución</h2>
                    <section class="mb-4">
                        <h3><i class="bi bi-bullseye"></i> Misión</h3>
                        <p class="ps-4">Formar profesionales competentes y éticos con educación de calidad.</p>
                    </section>
                    <section class="mb-4">
                        <h3><i class="bi bi-eye-fill"></i> Visión</h3>
                        <p class="ps-4">Líderes en innovación educativa.</p>
                    </section>
                    <section class="mb-4">
                        <h3><i class="bi bi-heart-fill"></i> Valores</h3>
                        <ul class="ps-4">
                            <li>Integridad</li>
                            <li>Compromiso</li>
                            <li>Excelencia</li>
                        </ul>
                    </section>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>