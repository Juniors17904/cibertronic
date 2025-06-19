<?php
session_start();

// Verificar si el usuario tiene sesión y es administrador o profesor
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['administrador', 'profesor'])) {
    header("Location: ../../index.php");
    exit();
}
