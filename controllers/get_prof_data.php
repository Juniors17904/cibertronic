<?php
require_once __DIR__ . '/../config/config.php';

//Obtiene los datos del profesor a partir del ID de usuario
function getProfData($conn, $user_id)
{
    // Consulta SQL para obtener datos del profesor y del usuario asociado
    $sql = "SELECT p.*, u.correo, u.rol, u.estado 
            FROM profesores p
            INNER JOIN usuarios u ON p.usuario_id = u.id
            WHERE p.usuario_id = ?";

    // Preparar y ejecutar la consulta de forma segura
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $user_id);  // Enlaza el parámetro del ID de usuario
        $stmt->execute();
        $result = $stmt->get_result();     // Obtiene el resultado de la consulta
        return $result->fetch_assoc();     // Retorna los datos como array asociativo
    } else {
        // Si falla la preparación, registra el error en el log
        error_log("❌ Error al preparar consulta getProfData: " . $conn->error);
        return null;  // Retorna null en caso de error
    }
}
