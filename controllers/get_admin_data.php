<?php
require_once __DIR__ . '/../config/config.php';

function getAdminData($conn, $user_id)
{
    $sql = "SELECT a.*, u.correo, u.rol, u.estado 
            FROM administrador a
            JOIN usuarios u ON a.usuario_id = u.id
            WHERE a.usuario_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error en prepare: " . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
