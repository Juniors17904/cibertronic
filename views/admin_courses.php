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

    <?php
    include 'cabecera.php'
    ?>
    <div class="container-fluid mt-0">
        <div class="row">

            <?php include 'lateral.php' ?>

            <!-- Contenido principal -->
            <main class="col-md-7 col-lg-8 px-5">
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