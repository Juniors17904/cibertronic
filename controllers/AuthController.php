<?php
include_once('../models/User.php');  // Incluir el modelo de usuario para verificar las credenciales

class AuthController
{
    public function login($email, $password)
    {
        $user = new User();  // Instanciamos el modelo User
        $userData = $user->verificarUsuario($email); // Verificar si el correo existe

        if ($userData) {
            // Verificar la contraseña (usando password_verify si la contraseña está hashada)
            //if (password_verify($password, $userData['password'])) {
            if ($password === $userData['password']) {
                // Almacenar el id y el rol del usuario en la sesión
                session_start();
                $_SESSION['user_id'] = $userData['id'];
                $_SESSION['user_role'] = $userData['rol'];

                // Redirigir según el rol del usuario
                if ($userData['rol'] == 'administrador') {
                    header("Location: ../views/admin_dashboard.php");
                } elseif ($userData['rol'] == 'profesor') {
                    header("Location: /professor_dashboard.php");
                } else {
                    header("Location: /student_dashboard.php");
                }
                exit();  // Asegúrate de detener la ejecución después de redirigir
            } else {
                // Contraseña incorrecta, redirige al login con mensaje de error
                header('Location: ../views/login.php?error=contraseña_incorrecta');
                exit();
            }
        } else {
            // Correo no registrado, redirige al login con mensaje de error
            header('Location: ../views/login.php?error=correo_no_registrado');
            exit();
        }
    }
}
