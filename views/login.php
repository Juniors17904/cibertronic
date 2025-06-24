<?php
// Iniciar la sesión
session_start();

// Verificar si ya hay una sesión activa
$is_logged_in = isset($_SESSION['user_id']);

// Cerrar sesión si se presiona el botón
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");  // Redirigir al login después de cerrar sesión
    exit();
}
include 'header.php';

?>

<body class="bg-light">

    <div class="container vh-100 d-flex">
        <div class="row justify-content-center align-items-center flex-grow-1 w-100">
            <!-- Columna de la imagen -->
            <div class="col-md-6 d-flex justify-content-center">
                <img src="../assets/images/login.jpg" alt="Imagen descriptiva" class="img-fluid" />
            </div>

            <!-- Columna del formulario -->
            <div class="col-md-6 d-flex flex-column justify-content-center">
                <h2 class="text-center mb-4">Iniciar sesión</h2>

                <?php if ($is_logged_in): ?>
                    <div class="alert alert-success text-center">
                        Sesión activa. ID de sesión: <?php echo $_SESSION['user_id']; ?>
                    </div>

                    <form action="login.php" method="POST" class="text-center mb-4">
                        <button type="submit" name="logout" class="btn btn-danger">Cerrar sesión</button>
                    </form>
                <?php else: ?>
                    <!-- <div class="alert alert-danger text-center">No hay sesión activa.</div> -->
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger text-center">
                        <?php
                        if ($_GET['error'] == 'correo_no_registrado') {
                            echo "Correo electrónico no registrado.";
                        } elseif ($_GET['error'] == 'sesion') {
                            echo "El acceso al panel de estudiantes aún no está disponible.";
                        } elseif ($_GET['error'] == 'contraseña_incorrecta') {
                            echo "Contraseña incorrecta.";
                        } elseif ($_GET['error'] == 'inactividad') {
                            echo "Sesión cerrada por inactividad. Vuelve a iniciar sesión.";
                        }

                        ?>
                    </div>
                <?php endif; ?>

                <form action="../controllers/login.php" method="POST" class="w-100">
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo electrónico:</label>
                        <input type="email" class="form-control" name="email" id="email" required />
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña:</label>
                        <input type="password" class="form-control" name="password" id="password" required />
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Iniciar sesión</button>
                </form>
            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>