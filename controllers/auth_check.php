<?php
session_start();

$tiempo_maximo_inactividad = 3600; // 1 hora = 3600 segundos

if (isset($_SESSION['ultimo_acceso'])) {
    $tiempo_transcurrido = time() - $_SESSION['ultimo_acceso'];

    if ($tiempo_transcurrido > $tiempo_maximo_inactividad) {
        session_unset();
        session_destroy();
        header("Location: ../../index.php?error=inactividad");
        exit();
    }
}

$_SESSION['ultimo_acceso'] = time();
// En auth_check.php o en login.php después de login exitoso
$_SESSION['hora_login'] = date('d/m/Y H:i');




// Verificar si el usuario tiene sesión y es administrador o profesor
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['administrador', 'profesor'])) {
    header("Location: ../../index.php");
    exit();
}
