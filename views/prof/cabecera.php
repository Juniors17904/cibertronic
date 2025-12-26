<!-- Cabecera Profesor -->
<header class="navbar navbar-expand-md navbar-dark bg-dark shadow-lg">
    <div class="container-fluid">
        <!-- Botón para móviles (PRIMERO - a la izquierda) -->
        <button class="navbar-toggler me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Logo/Brand - Con Imagen Redonda -->
        <a class="navbar-brand d-flex align-items-center" href="#" style="font-family: 'Orbitron', sans-serif;">
            <div class="logo-container rounded-circle overflow-hidden me-2"
                style="width: 44px; height: 44px;">
                <img src="<?= BASE_URL ?>/assets/images/logo.jpg" alt="Logo" class="img-fluid" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <span class="brand-text">
                <span class="d-none d-md-inline-block">CIBERTRONIC</span>
                <small class="d-block d-md-none">CT</small>
            </span>
        </a>

        <!-- Menú superior derecho -->
        <div class="d-flex align-items-center ms-auto">
            <!-- Notificaciones -->
            <div class="dropdown me-3">
                <a href="#" class="text-white fs-5 position-relative" data-bs-toggle="dropdown">
                    <i class="fas fa-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger pulse"></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li>
                        <h6 class="dropdown-header">Notificaciones Recientes</h6>
                    </li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-envelope me-2 text-primary"></i> Nuevo mensaje</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-exclamation-triangle me-2 text-warning"></i> Alerta del sistema</a></li>
                </ul>
            </div>

            <!-- Perfil del usuario con última conexión -->
            <div class="dropdown d-flex align-items-center ms-3">

                <!-- Última conexión -->
                <div class="me-3 text-end">
                    <small class="text-info fst-italic d-block">
                        <i class="fas fa-clock me-1"></i> Última conexión:
                    </small>
                    <small class="text-info fw-semibold"><?= $_SESSION['hora_login'] ?? '---' ?></small>
                </div>

                <!-- Foto y nombre -->
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                    <div class="position-relative me-2">
                        <img src="<?= BASE_URL ?>/assets/images/<?= htmlspecialchars(!empty(trim($prof['foto'])) ? $prof['foto'] : 'perfil.jpg') ?>" alt="Perfil" width="36" height="36" class="rounded-circle border border-2 border-primary">
                        <span class="position-absolute bottom-0 end-0 bg-success rounded-circle" style="width: 10px; height: 10px;"></span>
                    </div>
                    <span class="d-none d-md-inline">
                        <?= htmlspecialchars($prof['nombre'] . ' ' . $prof['apellidos']) ?>
                    </span>
                </a>


                <!-- Dropdown -->
                <ul class="dropdown-menu dropdown-menu-end shadow-lg">
                    <li><a class="dropdown-item" href="admin_profile.php"><i class="fas fa-user-circle me-2 text-primary"></i> Mi perfil</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-sliders-h me-2 text-info"></i> Configuración</a></li>
                    <li>
                        <hr class="dropdown-divider mx-3">
                    </li>
                    <li><a class="dropdown-item text-danger" href="../../controllers/logout.php"><i class="fas fa-power-off me-2"></i> Cerrar sesión</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>