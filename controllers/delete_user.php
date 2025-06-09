<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/auth_check.php';

header('Content-Type: application/json');

// Verificar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['success' => false, 'message' => 'Método no permitido']));
}

// Obtener ID (para borrado individual)
$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? null;

try {
    $conn->begin_transaction();

    // Resto de tu lógica de borrado...
    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
