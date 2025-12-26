<!DOCTYPE html>
<html lang="es">

<head>
    <?php
    // Incluir config si no está incluido
    if (!defined('BASE_URL')) {
        require_once __DIR__ . '/../config/config.php';
    }
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>/assets/images/logo.jpg">
    <title>Bienvenido a Cibertronic</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome (si lo usas) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap Icons (necesario para bi bi-search) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Estilos personalizados -->
    <link href="<?= BASE_URL ?>/assets/css/styles.css" rel="stylesheet">
</head>