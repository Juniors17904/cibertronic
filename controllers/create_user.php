<?php
session_start();
require_once __DIR__ . '/../config/config.php';

// Verificar si el usuario tiene permisos de administrador
if ($_SESSION['user_role'] !== 'administrador') {
    $_SESSION['notification'] = [
        'type' => 'danger',
        'message' => 'No tienes permisos para realizar esta acción',
        'icon' => 'exclamation-circle'
    ];
    header("Location: ../views/admin/admin_ges_user.php");
    exit();
}

// Recoger datos del formulario
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$rol = $_POST['rol'] ?? '';
$estado = $_POST['estado'] ?? 'activo';
$telefono = $_POST['telefono'] ?? '';
$nombre = $_POST['nombre'] ?? '';
$apellidos = $_POST['apellidos'] ?? '';
$dni = $_POST['dni'] ?? '';

// Validaciones básicas
if (empty($email) || empty($password) || empty($rol) || empty($nombre) || empty($apellidos)) {
    $_SESSION['notification'] = [
        'type' => 'danger',
        'message' => 'Todos los campos obligatorios deben ser completados',
        'icon' => 'exclamation-circle'
    ];
    $_SESSION['show_modal'] = true;
    $_SESSION['form_data'] = $_POST;
    header("Location: ../views/admin/admin_ges_user.php");
    exit();
}

// Validación de email válido
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['notification'] = [
        'type' => 'danger',
        'message' => 'El correo electrónico no es válido',
        'icon' => 'exclamation-circle'
    ];
    $_SESSION['show_modal'] = true;
    $_SESSION['form_data'] = $_POST;
    header("Location: ../views/admin/admin_ges_user.php");
    exit();
}

// Validación de contraseña mínima
if (strlen($password) < 6) {
    $_SESSION['notification'] = [
        'type' => 'danger',
        'message' => 'La contraseña debe tener al menos 6 caracteres',
        'icon' => 'exclamation-circle'
    ];
    $_SESSION['show_modal'] = true;
    $_SESSION['form_data'] = $_POST;
    header("Location: ../views/admin/admin_ges_user.php");
    exit();
}

// Validación de teléfono numérico (si no está vacío)
if (!empty($telefono) && !preg_match('/^\d+$/', $telefono)) {
    $_SESSION['notification'] = [
        'type' => 'danger',
        'message' => 'El teléfono solo debe contener números',
        'icon' => 'exclamation-circle'
    ];
    $_SESSION['show_modal'] = true;
    $_SESSION['form_data'] = $_POST;
    header("Location: ../views/admin/admin_ges_user.php");
    exit();
}

// Validación de DNI numérico (si no está vacío)
if (!empty($dni) && !preg_match('/^\d+$/', $dni)) {
    $_SESSION['notification'] = [
        'type' => 'danger',
        'message' => 'El DNI solo debe contener números',
        'icon' => 'exclamation-circle'
    ];
    $_SESSION['show_modal'] = true;
    $_SESSION['form_data'] = $_POST;
    header("Location: ../views/admin/admin_ges_user.php");
    exit();
}

// Validación de roles permitidos
$roles_permitidos = ['administrador', 'profesor', 'alumno'];
if (!in_array($rol, $roles_permitidos)) {
    $_SESSION['notification'] = [
        'type' => 'danger',
        'message' => 'Rol no válido',
        'icon' => 'exclamation-circle'
    ];
    $_SESSION['show_modal'] = true;
    $_SESSION['form_data'] = $_POST;
    header("Location: ../views/admin/admin_ges_user.php");
    exit();
}

try {
    // Verificar si el email ya existe
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['notification'] = [
            'type' => 'danger',
            'message' => 'El correo electrónico ya está registrado',
            'icon' => 'exclamation-circle'
        ];
        $_SESSION['show_modal'] = true;
        $_SESSION['form_data'] = $_POST;
        header("Location: ../views/admin/admin_ges_user.php");
        exit();
    }

    // Hash de la contraseña
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insertar en tabla usuarios
    $stmt = $conn->prepare("INSERT INTO usuarios (correo, password, rol, estado) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $email, $hashed_password, $rol, $estado);
    $stmt->execute();
    $user_id = $stmt->insert_id;

    // Insertar en tabla correspondiente según el rol (incluyendo DNI)
    switch ($rol) {
        case 'administrador':
            $stmt = $conn->prepare("INSERT INTO administrador (usuario_id, nombre, apellidos, telefono, dni) VALUES (?, ?, ?, ?, ?)");
            break;
        case 'profesor':
            $stmt = $conn->prepare("INSERT INTO profesores (usuario_id, nombre, apellidos, telefono, dni) VALUES (?, ?, ?, ?, ?)");
            break;
        case 'alumno':
            $stmt = $conn->prepare("INSERT INTO alumnos (usuario_id, nombre, apellidos, telefono, dni) VALUES (?, ?, ?, ?, ?)");
            break;
    }

    $stmt->bind_param("issss", $user_id, $nombre, $apellidos, $telefono, $dni);
    $stmt->execute();

    $_SESSION['notification'] = [
        'type' => 'success',
        'message' => 'Usuario creado exitosamente',
        'icon' => 'check-circle'
    ];
    header("Location: ../views/admin/admin_ges_user.php");
    exit();
} catch (Exception $e) {
    $_SESSION['notification'] = [
        'type' => 'danger',
        'message' => 'Error al crear el usuario: ' . $e->getMessage(),
        'icon' => 'exclamation-circle'
    ];
    $_SESSION['show_modal'] = true;
    $_SESSION['form_data'] = $_POST;
    header("Location: ../views/admin/admin_ges_user.php");
    exit();
}
