<?php
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID de usuario no válido.']);
    exit;
}

$user_id = intval($_GET['id']);

$sql = "SELECT u.id, u.correo, u.rol, u.estado,
            COALESCE(a.nombre, p.nombre, ad.nombre) AS nombre,
            COALESCE(a.apellidos, p.apellidos, ad.apellidos) AS apellidos,
            COALESCE(a.telefono, p.telefono, ad.telefono) AS telefono,
            COALESCE(a.dni, p.dni, ad.dni) AS dni
        FROM usuarios u
        LEFT JOIN alumnos a ON u.id = a.usuario_id
        LEFT JOIN profesores p ON u.id = p.usuario_id
        LEFT JOIN administrador ad ON u.id = ad.usuario_id
        WHERE u.id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta.']);
    exit;
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    echo json_encode(['success' => true, 'user' => $user]);
} else {
    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado.']);
}
