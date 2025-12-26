<?php
date_default_timezone_set('America/Lima'); // ✅ Zona horaria correcta para Perú

// Detección de entorno
if ($_SERVER['SERVER_NAME'] == '192.168.1.102' || $_SERVER['SERVER_NAME'] == 'localhost') {
    // Entorno local de desarrollo
    define('BASE_URL', 'http://192.168.1.102:81/cibertro');

    // Base de datos local
    $host = "localhost";
    $dbname = "cibertronic";
    $username = "root";
    $password = "";
} else {
    // Entorno de producción
    define('BASE_URL', 'http://cibertronic.infinityfree.me');

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
