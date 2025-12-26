<?php
session_start();

$is_logged_in = isset($_SESSION['user_id']);

if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

include 'header.php';
?>

<body style="background-color:rgb(248, 249, 250);">
    <div class="container vh-100 d-flex px-2 px-md-3">
        <div class="row justify-content-center align-items-center flex-grow-1 w-100">

            <!-- Columna de la imagen (oculta en móvil) -->
            <div class="col-md-6 d-none d-md-flex justify-content-center">
                <img src="../assets/images/login.jpg" alt="Equipo de Cibertronic" class="img-fluid rounded shadow" />
            </div>

            <!-- Columna del formulario -->
            <div class="col-12 col-md-6 d-flex flex-column justify-content-center px-2 px-md-4">
                <!-- Logo oficial de Cibertronic -->
                <div class="text-center mb-4">
                    <img src="../assets/images/Cibertro.png" alt="Logo Instituto Cibertronic"
                        class="img-fluid rounded-4" style="max-width: 500px; width: 100%;" />
                </div>

                <?php if ($is_logged_in): ?>
                    <div class="alert alert-success text-center">
                        Sesión activa. ID de Usuario: <?php echo $_SESSION['user_id']; ?>
                    </div>
                    <form action="login.php" method="POST" class="text-center mb-4">
                        <button type="submit" name="logout" class="btn btn-danger">Cerrar sesión</button>
                    </form>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger text-center">
                        <?php
                        switch ($_GET['error']) {
                            case 'correo_no_registrado':
                                echo "Correo electrónico no registrado.";
                                break;
                            case 'sesion':
                                echo "El acceso al panel de estudiantes aún no está disponible.";
                                break;
                            case 'contraseña_incorrecta':
                                echo "Contraseña incorrecta.";
                                break;
                            case 'inactividad':
                                echo "Sesión cerrada por inactividad. Vuelve a iniciar sesión.";
                                break;
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <?php if (!$is_logged_in): ?>
                    <div id="loadingMsg" class="alert alert-info text-center d-none">Iniciando sesión...</div>

                    <form id="loginForm" action="../controllers/login.php" method="POST" class="w-100 needs-validation" novalidate>
                        <!-- Grupo de correo con ícono -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" name="email" id="email" required autocomplete="username" />
                            </div>
                            <div class="invalid-feedback">Ingresa un correo válido.</div>
                        </div>

                        <!-- Grupo de contraseña con ícono y botón -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" name="password" id="password" required autocomplete="current-password" />
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword" tabindex="-1" title="Mostrar/Ocultar contraseña">
                                    <i class="bi bi-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">La contraseña es obligatoria.</div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Iniciar sesión</button>

                        <div class="text-center mt-2">
                            <a href="#">¿Olvidaste tu contraseña?</a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Validación + mensaje y envío manual -->
    <script>
        (() => {
            'use strict';
            const form = document.getElementById('loginForm');
            const loadingMsg = document.getElementById('loadingMsg');

            form.addEventListener('submit', function(event) {
                event.preventDefault();

                if (!form.checkValidity()) {
                    event.stopPropagation();
                    form.classList.add('was-validated');
                    return;
                }

                loadingMsg.classList.remove('d-none');

                setTimeout(() => {
                    form.submit();
                }, 400);
            });
        })();
    </script>

    <!-- Mostrar/ocultar contraseña -->
    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');

        togglePassword.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            toggleIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    </script>
</body>

</html>