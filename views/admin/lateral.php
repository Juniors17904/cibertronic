<?php
// Generar contenido del menú una sola vez
$current_page = basename($_SERVER['PHP_SELF']);

// Agrupa páginas relacionadas
$matriculas_pages = ['admin_matriculas.php', 'admin_matriculas_detalles.php'];
$asignaciones_pages = [
    'admin_courses.php',
    'admin_detalle_asignaciones.php',
    'admin_detalles_asistencias.php',
    'admin_detalles_notas.php'
];

$menu_items = [
    'admin_dashboard.php'           => '<i class="fas fa-home me-2"></i> Página de la Institución',
    'admin_profile.php'             => '<i class="fas fa-user me-2"></i> Mi Perfil',
    'admin_ges_user.php'            => '<i class="fas fa-users me-2"></i> Gestión de Usuarios',
    'admin_matriculas.php'          => '<i class="fas fa-user-check me-2"></i> Gestión de Matrículas',
    'admin_courses.php'             => '<i class="fas fa-book me-2"></i> Gestión de Asignaciones',
    'admin_reports.php'             => '<i class="fas fa-chart-bar me-2"></i> Ver Reportes'
];

// Generar HTML del menú
$menu_html = '<ul class="nav flex-column">';
foreach ($menu_items as $page => $title) {
    if ($page === 'admin_matriculas.php') {
        $active_class = in_array($current_page, $matriculas_pages) ? 'active bg-primary' : '';
    } elseif ($page === 'admin_courses.php') {
        $active_class = in_array($current_page, $asignaciones_pages) ? 'active bg-primary' : '';
    } else {
        $active_class = ($current_page == $page) ? 'active bg-primary' : '';
    }

    $menu_html .= '<li class="nav-item my-1">
                    <a class="nav-link text-white ' . $active_class . ' rounded-3 px-3 py-2"
                    href="' . $page . '">
                        ' . $title . '
                    </a>
                </li>';
}
$menu_html .= '</ul>';

$logout_button = '<div class="sidebar-footer mt-auto p-3 border-top">
                    <a href="../../controllers/logout.php" class="btn btn-danger w-100">
                        <i class="fas fa-sign-out-alt me-2"></i> Cerrar sesión
                    </a>
                </div>';
?>

<!-- Sidebar para MÓVIL (Offcanvas) -->
<div class="offcanvas offcanvas-start bg-dark text-white d-md-none" tabindex="-1" id="sidebarMenu">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Menú Principal</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="d-flex flex-column h-100">
            <!-- Menú -->
            <div class="flex-grow-1 pt-3">
                <?= $menu_html ?>
            </div>

            <!-- Botón cerrar sesión -->
            <?= $logout_button ?>
        </div>
    </div>
</div>

<!-- Sidebar para DESKTOP (Fijo) -->
<nav class="col-md-4 col-lg-3 d-none d-md-block sidebar bg-dark text-white">
    <div class="position-sticky pt-3 vh-100">
        <!-- Encabezado del menú -->
        <div class="sidebar-header px-3 pb-2 mb-2 border-bottom">
            <h5 class="text-center">Menú Principal</h5>
        </div>

        <!-- Lista de enlaces -->
        <?= $menu_html ?>

        <!-- Botón cerrar sesión -->
        <?= $logout_button ?>
    </div>
</nav>
