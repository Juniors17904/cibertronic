<?php
require_once '../config/config.php';

header('Content-Type: application/json');

if (!isset($_GET['area_id']) || !is_numeric($_GET['area_id'])) {
    echo json_encode([]);
    exit;
}

$area_id = intval($_GET['area_id']);

$query = $conn->prepare("SELECT id, nombre_curso FROM cursos WHERE id_area = ?");
$query->bind_param("i", $area_id);
$query->execute();

$result = $query->get_result();
$cursos = [];

while ($row = $result->fetch_assoc()) {
    $cursos[] = $row;
}

echo json_encode($cursos);
