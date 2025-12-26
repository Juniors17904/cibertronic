<?php
include_once('../models/User.php');  // Incluir el modelo de usuario para verificar las credenciales

class AuthController
{
    public function login($email, $password)
    {
        // Zona horaria correcta antes de todo
        date_default_timezone_set('America/Lima');

        $user = new User();  // Instanciamos el modelo User
        $userData = $user->verificarUsuario($email); // Verificar si el correo existe

        if ($userData) {
            // Verificar si el rol está habilitado (solo admin y profesor)
            if (!in_array($userData['rol'], ['administrador', 'profesor'])) {
                header('Location: ../views/login.php?error=sesion');
                exit();
            }

            // Verificar la contraseña (hasheada)
            if (password_verify($password, $userData['password'])) {
                session_start();
                $_SESSION['user_id'] = $userData['id'];
                $_SESSION['user_role'] = $userData['rol'];


                $_SESSION['hora_login'] = date('d/m/Y H:i');

                // Redirige a archivo PRUEBA
                // header("Location: ../views/hora_test.php");
                // exit();




                // Redirigir según el rol
                if ($userData['rol'] === 'administrador') {
                    header("Location: ../views/admin/admin_dashboard.php");
                } elseif ($userData['rol'] === 'profesor') {
                    header("Location: ../views/prof/prof_dashboard.php");
                }
                exit();
            } else {
                header('Location: ../views/login.php?error=contraseña_incorrecta');
                exit();
            }
        } else {
            header('Location: ../views/login.php?error=correo_no_registrado');
            exit();
        }
    }
}
