<?php
function getCursosAsignados($conn, $usuario_id)
{
    $query = "
        SELECT 
            a.id AS asignacion_id,                 -- ID único de la asignación
            c.nombre_curso,                        -- Nombre del curso desde la tabla 'cursos'
            ar.nombre_area,                        -- Nombre del área desde la tabla 'areas'
            h.dia,                                 -- Día desde la tabla 'horarios'
            h.hora_inicio,                         -- Hora de inicio desde la tabla 'horarios'
            h.hora_fin,                            -- Hora de fin desde la tabla 'horarios'
            a.curso_id,                            -- ID del curso
            a.horario_id,                          -- ID del horario

            -- Total de alumnos matriculados en ese curso y horario
            (SELECT COUNT(*) 
                FROM matriculas m 
                WHERE m.curso_id = a.curso_id 
                AND m.horario_id = a.horario_id) AS total_alumnos

        FROM asignaciones a
        INNER JOIN cursos c ON a.curso_id = c.id
        INNER JOIN areas ar ON c.id_area = ar.id
        INNER JOIN horarios h ON a.horario_id = h.id
        INNER JOIN profesores p ON a.profesor_id = p.id
        WHERE p.usuario_id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    return $stmt->get_result();
}
