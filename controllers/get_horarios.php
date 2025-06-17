<?php
require_once '../config/config.php';

header('Content-Type: application/json');

if (!isset($_GET['curso_id']) || !is_numeric($_GET['curso_id'])) {
    echo json_encode([]);
    exit;
}

$curso_id = intval($_GET['curso_id']);

$query = $conn->prepare("SELECT id, dia, hora_inicio, hora_fin FROM horarios WHERE curso_id = ?");
$query->bind_param("i", $curso_id);
$query->execute();

$result = $query->get_result();
$horarios = [];

while ($row = $result->fetch_assoc()) {
    $horarios[] = $row;
}

echo json_encode($horarios);
