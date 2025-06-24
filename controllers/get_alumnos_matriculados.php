<?php
require_once '../config/config.php'; // Conexión a la base de datos

header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Verificación de parámetros
if (!isset($_GET['curso_id']) || !isset($_GET['horario_id'])) {
    http_response_code(400);
    error_log("FALTAN PARÁMETROS: " . json_encode($_GET));
    echo json_encode(["error" => "Faltan parámetros"]);
    exit;
}

$curso_id = intval($_GET['curso_id']);
$horario_id = intval($_GET['horario_id']);
error_log("→ Petición recibida: curso_id=$curso_id | horario_id=$horario_id");

// Obtener nombre del curso (para usar como 'asignatura')
function obtenerNombreCurso($conn, $curso_id)
{
    $stmt = $conn->prepare("SELECT nombre_curso FROM cursos WHERE id = ?");
    $stmt->bind_param("i", $curso_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $nombre = $res->fetch_assoc()['nombre_curso'] ?? '';
    $stmt->close();
    return $nombre;
}

$asignatura = obtenerNombreCurso($conn, $curso_id);

// Consulta alumnos matriculados
$query = "
    SELECT a.id, a.nombre, a.apellidos
    FROM matriculas m
    INNER JOIN alumnos a ON m.alumno_id = a.id
    WHERE m.curso_id = ? AND m.horario_id = ?
";

$stmt = $conn->prepare($query);
if (!$stmt) {
    http_response_code(500);
    error_log("❌ Error en prepare: " . $conn->error);
    echo json_encode(["error" => "Error en prepare"]);
    exit;
}

$stmt->bind_param("ii", $curso_id, $horario_id);
$stmt->execute();
$result = $stmt->get_result();

$alumnos = [];
$ids = [];

while ($row = $result->fetch_assoc()) {
    $alumnos[$row['id']] = [
        'id' => $row['id'],
        'nombre' => $row['nombre'] . ' ' . $row['apellidos'],
        'n1' => null,
        'n2' => null,
        'n3' => null
    ];
    $ids[] = $row['id'];
}

$stmt->close();

// Consulta notas si hay alumnos
if (!empty($ids)) {
    $ids_placeholder = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));
    $sql = "SELECT alumno_id, nota_01, nota_02, nota_03 FROM notas WHERE asignatura = ? AND alumno_id IN ($ids_placeholder)";
    $stmtNotas = $conn->prepare($sql);

    if ($stmtNotas) {
        $params = array_merge([$asignatura], $ids);
        $bindTypes = 's' . $types;

        // Referencia necesaria para bind_param dinámico
        $bindParams = [];
        $bindParams[] = &$bindTypes;
        foreach ($params as $k => $v) {
            $bindParams[] = &$params[$k];
        }

        call_user_func_array([$stmtNotas, 'bind_param'], $bindParams);
        $stmtNotas->execute();
        $resNotas = $stmtNotas->get_result();

        while ($nota = $resNotas->fetch_assoc()) {
            $id = $nota['alumno_id'];
            if (isset($alumnos[$id])) {
                $alumnos[$id]['n1'] = floatval($nota['nota_01']);
                $alumnos[$id]['n2'] = floatval($nota['nota_02']);
                $alumnos[$id]['n3'] = floatval($nota['nota_03']);
            }
        }
        $stmtNotas->close();
    } else {
        error_log("❌ Error al preparar consulta de notas: " . $conn->error);
    }
}

error_log("✅ Alumnos encontrados: " . count($alumnos));
echo json_encode(array_values($alumnos));
