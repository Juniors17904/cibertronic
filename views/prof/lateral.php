<!-- Panel lateral Profesor -->
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
                'prof_dashboard.php'   => '<i class="fas fa-chalkboard-teacher me-2"></i> Inicio',
                'prof_profile.php'     => '<i class="fas fa-user-circle me-2"></i> Mi Perfil',
                'prof_courses.php'   => '<i class="fas fa-book-open me-2"></i> Mis Cursos',
                'prof_asistencia_lista.php' => '<i class="fas fa-calendar-check me-2"></i> Asistencia',
                'prof_notas_lista.php'     => '<i class="fas fa-clipboard-list me-2"></i> Calificaciones',
                'prof_reportes.php'  => '<i class="fas fa-chart-line me-2"></i> Reportes'
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
            <a href="../../controllers/logout.php" class="btn btn-danger w-100">
                <i class="fas fa-sign-out-alt me-2"></i> Cerrar sesión
            </a>
        </div>
    </div>
</nav>