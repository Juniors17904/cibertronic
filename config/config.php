<?php
// Configuración para la conexión a la base de datos
$host = "localhost";          // El servidor donde se encuentra la base de datos
$dbname = "cibertronicbd";   // Nombre de la base de datos
$username = "root";           // Usuario para la conexión a la base de datos
$password = "";               // Contraseña (si es que tiene)

// Crear la conexión con la base de datos
$conn = new mysqli($host, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
} else {
    // echo "conectado";
}
