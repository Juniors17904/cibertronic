<?php
require_once '../../controllers/auth_check.php';
require_once '../../controllers/get_admin_data.php';

$admin = getAdminData($conn, $_SESSION['user_id']);

if (!$admin) {
    die("Administrador no encontrado.");
}
include '../header.php';
?>

<body>
    <?php include 'cabecera.php' ?>
    <div class="container-fluid mt-0">
        <div class="row">
            <?php include 'lateral.php' ?>
            <!-- Contenido principal -->
            <main class="col-md-7 col-lg-8 px-5">
                <!-- Contenido principal -->
                <h2 class="mt-4 mb-4 text-center">Gestión de Cursos por Área</h2>

                <div class="row">
                    <?php
                    $areas = $conn->query("SELECT * FROM areas WHERE estado = 1");

                    while ($area = $areas->fetch_assoc()):
                        $area_id = $area['id'];
                    ?>
                        <div class="col-md-6 mb-4">
                            <div class="card shadow">
                                <div class="card-body">
                                    <h5 class="card-title text-primary"><?= htmlspecialchars($area['nombre_area']) ?></h5>
                                    <p class="card-text"><?= htmlspecialchars($area['descripcion']) ?></p>

                                    <?php
                                    $cursos = $conn->query("SELECT * FROM cursos WHERE id_area = $area_id");

                                    if ($cursos->num_rows > 0):
                                    ?>
                                        <ul class="list-group list-group-flush">
                                            <?php while ($curso = $cursos->fetch_assoc()): ?>
                                                <?php
                                                $curso_id = $curso['id'];
                                                $res = $conn->query("SELECT COUNT(*) AS total FROM matriculas WHERE curso_id = $curso_id");
                                                $total = $res->fetch_assoc()['total'];
                                                ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <?= htmlspecialchars($curso['nombre_curso']) ?>
                                                    <span class="badge bg-success rounded-pill"><?= $total ?> alumno(s)</span>
                                                </li>
                                            <?php endwhile; ?>
                                        </ul>
                                    <?php else: ?>
                                        <div class="alert alert-warning mt-2 mb-0">No hay cursos registrados en esta área.</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>