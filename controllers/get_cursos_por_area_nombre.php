<?php
require_once '../config/config.php';

header('Content-Type: application/json');

$nombre_area = $_GET['nombre_area'] ?? '';

if ($nombre_area === '') {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT c.nombre_curso 
    FROM cursos c 
    JOIN areas ar ON c.id_area = ar.id 
    WHERE ar.nombre_area = ?");
$stmt->bind_param('s', $nombre_area);
$stmt->execute();
$res = $stmt->get_result();

$cursos = [];
while ($row = $res->fetch_assoc()) {
    $cursos[] = $row;
}

echo json_encode($cursos);
