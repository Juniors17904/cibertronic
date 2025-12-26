<?php
date_default_timezone_set('America/Lima'); // ✅ Zona horaria correcta para Perú

// Configuración para la conexión a la base de datos
$host = "localhost";
$dbname = "cibertronicbd";
$username = "root";
$password = "";

// Crear conexión
$conn = new mysqli($host, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
