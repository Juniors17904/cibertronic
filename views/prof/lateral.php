<?php
// Generar contenido del menú una sola vez
$current_page = basename($_SERVER['PHP_SELF']);
$asistencia_pages = ['prof_asistencia_lista.php', 'prof_asistencia.php'];
$notas_pages = ['prof_notas_lista.php', 'prof_notas.php'];
$reporte_pages = ['prof_reportes.php', 'prof_reportesAs.php', 'prof_reportesNt.php'];

$menu_items = [
    'prof_dashboard.php'        => '<i class="fas fa-chalkboard-teacher me-2"></i> Inicio',
    'prof_profile.php'          => '<i class="fas fa-user-circle me-2"></i> Mi Perfil',
    'prof_courses.php'          => '<i class="fas fa-book-open me-2"></i> Mis Cursos',
    'prof_asistencia_lista.php' => '<i class="fas fa-calendar-check me-2"></i> Asistencia',
    'prof_notas_lista.php'      => '<i class="fas fa-clipboard-list me-2"></i> Calificaciones',
    'prof_reportes.php'         => '<i class="fas fa-chart-line me-2"></i> Reportes'
];

// Generar HTML del menú
$menu_html = '<ul class="nav flex-column">';
foreach ($menu_items as $page => $title) {
    if ($page === 'prof_asistencia_lista.php') {
        $active_class = in_array($current_page, $asistencia_pages) ? 'active bg-primary' : '';
    } elseif ($page === 'prof_notas_lista.php') {
        $active_class = in_array($current_page, $notas_pages) ? 'active bg-primary' : '';
    } elseif ($page === 'prof_reportes.php') {
        $active_class = in_array($current_page, $reporte_pages) ? 'active bg-primary' : '';
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
