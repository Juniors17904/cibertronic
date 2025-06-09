<?php
// Iniciar la sesión para verificar si el usuario está logueado
session_start();

// Verificar si ya hay una sesión activa (si el usuario ya está logueado)
if (isset($_SESSION['user_id'])) {
    // Redirigir al panel correspondiente según el rol
    if ($_SESSION['user_role'] == 'administrador') {
        header("Location: admin_dashboard.php");
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
    include 'header.php';
?>

    <body class="bg-light">

        <!-- Contenedor para la página de bienvenida -->
        <div class="container d-flex justify-content-center align-items-center"
            style="height: 100vh;
            background-image: url( assets/images/img1.jpg);">
            <!-- Overlay con opacidad -->



        </div>




        <!-- Agregar el script de Bootstrap -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    </body>

    </html>
<?php
}
?>