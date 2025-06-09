<?php
// Código PHP igual
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'administrador') {
    header("Location: ../index.php");
    exit();
}
include 'header.php';

?>

<body>

    <div class="container-fluid mt-3">
        <div class="row">
            <!-- Panel lateral -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="dashboard.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_users.php">Gestión de Usuarios</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_courses.php">Gestión de Cursos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="reports.php">Ver Reportes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="settings.php">Configuración</a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Contenido principal -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <h1 class="text-center mb-4">Bienvenido al Panel de Administración</h1>

                <div class="alert alert-success text-center" role="alert">
                    <?php echo "¡Hola, " . htmlspecialchars($_SESSION['user_id']) . "! Estás logueado como Administrador."; ?>
                </div>

                <div class="row">
                    <!-- Tus tarjetas aquí -->
                </div>

                <div class="text-center mt-4">
                    <a href="../controllers/logout.php" class="btn btn-danger">Cerrar sesión</a>
                </div>
            </main>
        </div>
    </div>

    <?php include('footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>