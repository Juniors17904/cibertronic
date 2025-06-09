<?php
// Iniciar la sesión para verificar si el usuario está logueado
session_start();

// Verificar si ya hay una sesión activa (si el usuario ya está logueado)
if (isset($_SESSION['user_id'])) {
    // Redirigir al panel correspondiente según el rol
    if ($_SESSION['user_role'] == 'administrador') {
        header("Location: views/admin_dashboard.php");
        exit();
    } elseif ($_SESSION['user_role'] == 'profesor') {
        header("Location: profesor_dashboard.php");
        exit();
    } else {
        header("Location: student_dashboard.php");
        exit();
    }
} else {
    // Si no está logueado, mostrar la página de bienvenida con botón de login
    include 'views/header.php';
?>


    <body class="bg-light fd">

        <!-- Contenedor para la página de bienvenida -->
        <div class="container d-flex justify-content-center align-items-center imagen-fondo"
            style="height: 100vh;">
            <!-- Overlay con opacidad -->
            <div class="overlay"></div>

            <div class="text-center bienvenido">
                <h1 class="display-3 mb-4">Bienvenido a Cybertronic</h1>
                <p class="lead mb-4">El Instituto Cybertronic ofrece educación de calidad en el ámbito tecnológico y más.</p>

                <!-- Botón de Login -->
                <a href="views/login.php" class="btn btn-primary btn-lg">Iniciar sesión</a>
            </div>
        </div>

        <?php include('views/footer.php'); ?>


        <!-- Agregar el script de Bootstrap -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    </body>

    </html>
<?php
}
?>