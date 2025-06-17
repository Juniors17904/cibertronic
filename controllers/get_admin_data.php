<?php
require_once __DIR__ . '/../config/config.php';

function getAdminData($conn, $user_id)
{
    $sql = "SELECT a.*, u.correo, u.rol, u.estado 
            FROM administrador a
            INNER JOIN usuarios u ON a.usuario_id = u.id
            WHERE a.usuario_id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    } else {
        error_log("❌ Error al preparar consulta getAdminData: " . $conn->error);
        return null;
    }
}
