<?php
require_once '../config/config.php';
header('Content-Type: application/json');

error_log("📡 insert_matricula.php ejecutado");

// Obtener usuario_id desde POST
$usuario_id = intval($_POST['alumno_id'] ?? 0); // Realmente es usuario_id
$curso_id   = intval($_POST['curso_id'] ?? 0);
$horario_id = intval($_POST['horario_id'] ?? 0);

error_log("📥 Recibido POST - usuario_id: $usuario_id, curso_id: $curso_id, horario_id: $horario_id");

// Buscar el alumno_id correspondiente
$getAlumno = $conn->prepare("SELECT id FROM alumnos WHERE usuario_id = ?");
$getAlumno->bind_param("i", $usuario_id);
$getAlumno->execute();
$result = $getAlumno->get_result();
$alumno = $result->fetch_assoc();
$alumno_id = $alumno['id'] ?? 0;
error_log("👤 alumno_id obtenido: $alumno_id");

if (!$alumno_id || !$curso_id || !$horario_id) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos o inválidos.']);
    exit;
}

// Validar curso
$checkCurso = $conn->prepare("SELECT id FROM cursos WHERE id = ?");
$checkCurso->bind_param("i", $curso_id);
$checkCurso->execute();
$checkCurso->store_result();
error_log("📘 Curso encontrado: " . $checkCurso->num_rows);

// Validar horario
$checkHorario = $conn->prepare("SELECT id FROM horarios WHERE id = ?");
$checkHorario->bind_param("i", $horario_id);
$checkHorario->execute();
$checkHorario->store_result();
error_log("⏰ Horario encontrado: " . $checkHorario->num_rows);

// Verificar matrícula existente
$checkMatricula = $conn->prepare("SELECT id FROM matriculas WHERE alumno_id = ? AND curso_id = ?");
$checkMatricula->bind_param("ii", $alumno_id, $curso_id);
$checkMatricula->execute();
$checkMatricula->store_result();
error_log("🔍 Ya matriculado?: " . $checkMatricula->num_rows);

if ($checkMatricula->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'El alumno ya está matriculado en ese curso.']);
    exit;
}

// Insertar matrícula
$insert = $conn->prepare("INSERT INTO matriculas (alumno_id, curso_id, horario_id) VALUES (?, ?, ?)");
$insert->bind_param("iii", $alumno_id, $curso_id, $horario_id);

if ($insert->execute()) {
    echo json_encode(['success' => true, 'message' => 'Matrícula registrada correctamente.']);
} else {
    error_log("❌ Error al insertar matrícula: " . $insert->error);
    echo json_encode(['success' => false, 'message' => 'Error al guardar la matrícula.']);
}
