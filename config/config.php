<?php
date_default_timezone_set('America/Lima'); // ✅ Zona horaria correcta para Perú

// Cargar credenciales desde archivo separado (no incluido en Git)
$db_config = require __DIR__ . '/database.php';

// Detección de entorno
if ($_SERVER['SERVER_NAME'] == '192.168.1.102' || $_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1') {
    // Entorno local de desarrollo
    define('BASE_URL', 'http://' . $_SERVER['SERVER_NAME'] . ':81/CIBERTRO');
    define('IS_LOCAL', true);
    $config = $db_config['local'];
} else {
    // Entorno de producción
    define('BASE_URL', 'https://cibertronic.infinityfree.me');
    define('IS_LOCAL', false);
    $config = $db_config['production'];
}

// Asignar credenciales
$host = $config['host'];
$dbname = $config['dbname'];
$username = $config['username'];
$password = $config['password'];

// Crear conexión
$conn = new mysqli($host, $username, $password, $dbname);

// Configurar charset UTF-8
$conn->set_charset("utf8mb4");

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
