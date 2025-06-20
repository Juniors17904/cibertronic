<?php
session_start();

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] == 'administrador') {
        header("Location: views/admin/admin_dashboard.php");
        exit();
    } elseif ($_SESSION['user_role'] == 'profesor') {
        header("Location: views/prof/prof_dashboard.php");
        exit();
    }
}

include 'views/header.php';
?>

<body class="bg-light fd">
    <div class="container d-flex justify-content-center align-items-center imagen-fondo" style="height: 100vh;">
        <div class="overlay"></div>

        <div class="text-center bienvenido">
            <h1 class="display-3 mb-4">Bienvenido a Cibertronic</h1>
            <p class="lead mb-4">El Instituto Cibertronic ofrece educación de calidad en el ámbito tecnológico y más.</p>

            <?php if (!empty($mensaje)): ?>
                <div class="alert alert-warning"><?= $mensaje ?></div>
            <?php endif; ?>

            <a href="views/login.php" class="btn btn-primary btn-lg">Iniciar sesión</a>
        </div>
    </div>

    <?php include('views/footer.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>