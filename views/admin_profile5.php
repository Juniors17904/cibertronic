<?php
require_once '../controllers/auth_check.php';
require_once '../controllers/get_admin_data.php';


$admin = getAdminData($conn, $_SESSION['user_id']);
if (!$admin) {
    die("Administrador no encontrado.");
}

include 'header.php';
?>

<body>
    <?php include 'cabecera.php'; ?>

    <div class="container-fluid mt-0">
        <div class="row">
            <?php include 'lateral.php'; ?>

            <!-- Contenido principal -->
            <main class="col-md-6 col-lg-9 px-5 py-4">
                <h2 class="text-center mb-4">Cursos por Área</h2>
                <div class="row">
                    <?php
                    $areas = $conn->query("SELECT * FROM areas WHERE estado = 1 ORDER BY nombre_area ASC");
                    if ($areas && $areas->num_rows > 0):
                        while ($area = $areas->fetch_assoc()):
                            $area_id = $area['id'];
                            $cursos = $conn->query("SELECT nombre_curso FROM cursos WHERE id_area = $area_id ORDER BY nombre_curso ASC");
                    ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100 shadow">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0"><?= htmlspecialchars($area['nombre_area']) ?></h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text small text-muted"><?= htmlspecialchars($area['descripcion']) ?></p>
                                        <?php if ($cursos && $cursos->num_rows > 0): ?>
                                            <ul class="list-group list-group-flush">
                                                <?php while ($curso = $cursos->fetch_assoc()): ?>
                                                    <li class="list-group-item"><?= htmlspecialchars($curso['nombre_curso']) ?></li>
                                                <?php endwhile; ?>
                                            </ul>
                                        <?php else: ?>
                                            <p class="text-muted mt-2">No hay cursos registrados en esta área.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php
                        endwhile;
                    else:
                        ?>
                        <div class="col-12">
                            <div class="alert alert-warning">No hay áreas activas registradas.</div>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>