<?php
date_default_timezone_set('America/Lima'); // ✅ Zona horaria correcta para Perú

// Detección de entorno
if ($_SERVER['SERVER_NAME'] == '192.168.1.102' || $_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1') {
    // Entorno local de desarrollo
    define('BASE_URL', 'http://' . $_SERVER['SERVER_NAME'] . ':81/CIBERTRO');
    define('IS_LOCAL', true);

    // Base de datos local
    $host = "localhost";
    $dbname = "cibertronicbd";
    $username = "root";
    $password = "";
} else {
    // Entorno de producción
    define('BASE_URL', 'https://cibertronic.infinityfree.me');
    define('IS_LOCAL', false);

    // Base de datos producción
    $host = "sql105.infinityfree.com";
    $dbname = "if0_40763764_cibertronic";
    $username = "if0_40763764";
    $password = "Juniors17904JMV";
}

// Crear conexión
$conn = new mysqli($host, $username, $password, $dbname);

// Configurar charset UTF-8
$conn->set_charset("utf8mb4");

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
