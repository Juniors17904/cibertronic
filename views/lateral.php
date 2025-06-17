<!-- Panel lateral -->
<nav class="col-md-4 col-lg-3 d-md-block sidebar collapse bg-dark text-white">
    <div class="position-sticky pt-3 vh-100">
        <!-- Encabezado del menú -->
        <div class="sidebar-header px-3 pb-2 mb-2 border-bottom">
            <h5 class="text-center">Menú Principal</h5>
        </div>

        <!-- Lista de enlaces -->
        <ul class="nav flex-column">
            <?php
            $current_page = basename($_SERVER['PHP_SELF']);
            $menu_items = [
                'admin_dashboard.php'   => '<i class="fas fa-home me-2"></i> Página de la Institución',
                'admin_profile.php'     => '<i class="fas fa-user me-2"></i> Mi Perfil',
                'admin_ges_user.php'    => '<i class="fas fa-users me-2"></i> Gestión de Usuarios',
                'admin_courses.php'     => '<i class="fas fa-book me-2"></i> Gestión de Cursos',
                'admin_matriculas.php'  => '<i class="fas fa-user-check me-2"></i> Gestión de Matrículas', // NUEVO ENLACE
                'admin_reports.php'     => '<i class="fas fa-chart-bar me-2"></i> Ver Reportes'
            ];

            foreach ($menu_items as $page => $title) {
                $active_class = ($current_page == $page) ? 'active bg-primary' : '';
                echo '<li class="nav-item my-1">
                        <a class="nav-link text-white ' . $active_class . ' rounded-3 px-3 py-2" 
                            href="' . $page . '">
                            ' . $title . '
                        </a>
                    </li>';
            }
            ?>
        </ul>

        <!-- Botón cerrar sesión -->
        <div class="sidebar-footer mt-auto p-3 border-top">
            <a href="../controllers/logout.php" class="btn btn-danger w-100">
                <i class="fas fa-sign-out-alt me-2"></i> Cerrar sesión
            </a>
        </div>
    </div>
</nav>