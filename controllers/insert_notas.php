<?php
require_once '../config/config.php';

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $curso_id = $_POST['curso_id'] ?? null;
    $horario_id = $_POST['horario_id'] ?? null;
    $notas = $_POST['notas'] ?? [];

    if (!$curso_id || !$horario_id || empty($notas)) {
        $response['error'] = 'Datos incompletos.';
        echo json_encode($response);
        exit;
    }

    // Obtener nombre del curso
    $stmtCurso = $conn->prepare("SELECT nombre_curso FROM cursos WHERE id = ?");
    $stmtCurso->bind_param("i", $curso_id);
    $stmtCurso->execute();
    $resultCurso = $stmtCurso->get_result();
    $curso = $resultCurso->fetch_assoc();
    $nombre_curso = $curso ? $curso['nombre_curso'] : '';

    $stmtCurso->close();

    // Guardar las notas
    $stmt = $conn->prepare("INSERT INTO notas (alumno_id, asignatura, nota_01, nota_02, nota_03, ciclo) VALUES (?, ?, ?, ?, ?, NULL)
        ON DUPLICATE KEY UPDATE nota_01 = VALUES(nota_01), nota_02 = VALUES(nota_02), nota_03 = VALUES(nota_03)");

    if (!$stmt) {
        $response['error'] = 'Error al preparar la consulta.';
        echo json_encode($response);
        exit;
    }

    foreach ($notas as $alumno_id => $n) {
        $n1 = isset($n['n1']) ? floatval($n['n1']) : 0;
        $n2 = isset($n['n2']) ? floatval($n['n2']) : 0;
        $n3 = isset($n['n3']) ? floatval($n['n3']) : 0;

        $stmt->bind_param("isddd", $alumno_id, $nombre_curso, $n1, $n2, $n3);
        $stmt->execute();
    }

    $stmt->close();
    $conn->close();

    $response['success'] = true;
}

echo json_encode($response);
