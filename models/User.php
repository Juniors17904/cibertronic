<?php
class User
{
    private $conn;

    public function __construct()
    {
        // Conexión a la base de datos
        include_once(__DIR__ . '/../config/config.php');
        $this->conn = $conn;
    }

    // Método para verificar si el usuario existe
    public function verificarUsuario($email)
    {
        $query = "SELECT * FROM usuarios WHERE correo = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();  // Retorna los datos del usuario si existe
    }
}
