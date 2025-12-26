<?php
date_default_timezone_set('America/Lima'); // ✅ Zona horaria correcta para Perú

// Configuración para la conexión a la base de datos
$host = "sql105.infinityfree.com";
$dbname = "if0_40763764_cibertronic";
$username = "if0_40763764";
$password = "Juniors17904JMV";

// Crear conexión
$conn = new mysqli($host, $username, $password, $dbname);

// Configurar charset UTF-8
$conn->set_charset("utf8mb4");

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
