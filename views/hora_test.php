<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Prueba Hora</title>
</head>

<body style="font-family: Arial, sans-serif; text-align: center; margin-top: 50px;">
    <h1>🕒 Hora de última conexión:</h1>
    <h2 style="color: green;">
        <?= $_SESSION['hora_login'] ?? 'No hay hora' ?>
    </h2>
</body>

</html>