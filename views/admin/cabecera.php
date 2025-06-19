<!-- Cabecera Premium -->
<header class="navbar navbar-expand-md navbar-dark bg-dark shadow-lg">
    <div class="container-fluid">
        <!-- Logo/Brand - Versión Mejorada -->
        <a class="navbar-brand d-flex align-items-center" href="#" style="font-family: 'Orbitron', sans-serif;">
            <div class="logo-container bg-gradient-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                style="width: 44px; height: 44px;">
                <i class="fas fa-shield-alt fa-lg text-white"></i>
            </div>
            <span class="brand-text">
                <span class="d-none d-md-inline-block">CIBERTRONIC</span>
                <small class="d-block d-md-none">CT</small>
            </span>
        </a>

        <!-- Botón para móviles -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>

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

            <!-- Perfil del usuario -->
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                    <div class="position-relative">
                        <img src="../../assets/images/perfil.jpg" alt="Perfil" width="36" height="36" class="rounded-circle border border-2 border-primary me-2">
                        <span class="position-absolute bottom-0 end-0 bg-success rounded-circle" style="width: 10px; height: 10px;"></span>
                    </div>
                    <span class="d-none d-md-inline">
                        <?= htmlspecialchars($admin['nombre'] . ' ' . $admin['apellidos']) ?>
                    </span>
                </a>
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