<?php
require_once __DIR__ . '/../../class_session/session.php';
require_once '../../database/User.php';
// Verificar que la petición sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /pages/login/login.php');
    //echo "login_handler.php: Método no permitido";
    exit();
}
// Obtener datos del formulario
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
// Validaciones básicas
$errors = [];
if (empty($username)) {
    $errors[] = 'El nombre de usuario o email es requerido';
}
if (empty($password)) {
    $errors[] = 'La contraseña es requerida';
}
// Si hay errores, regresar al formulario
if (!empty($errors)) {
    $_SESSION['error_messages'] = $errors;
    $_SESSION['login_data'] = ['username' => $username];
    header('Location: /pages/login/login.php');
    //echo "login_handler.php: Errores de validación";
    exit();
}
// Intentar autenticar al usuario
try {
    $user = new User();
    $result = $user->login($username, $password);
    if ($result['success']) {
        if ($user->is2FAEnabled($result['user']['uuid'])) {
            // Usuario con 2FA habilitado, redirigir a la página de verificación
            $_SESSION['temp_user'] = $result['user']; // Guardar datos temporales del usuario
            header('Location: /pages/2FA_config/2FA_introduce.php');
            exit();
        }
        // Autenticación exitosa sin 2FA
        SessionManager::getInstance()->saveDataSession($result['user']);
        // Limpiar errores
        unset($_SESSION['error_messages']);
        unset($_SESSION['login_data']);
        // Mensaje de éxito
        $_SESSION['success_message'] = 'Login successful! Welcome back.';

        header('Location: /index.php');
        //echo "login_handler.php: Login exitoso, redirigiendo a $redirect";
        //echo "<pre>";
        //var_dump($_SESSION);
        //echo "</pre>";
        exit();
    } else {
        $_SESSION['error_messages'] = [$result['message']];
        $_SESSION['login_data'] = ['username' => $username];
        header('Location: /pages/login/login.php');
        //echo "login_handler.php: Errores de autenticación";
        exit();
    }
} catch (Exception $e) {
    $_SESSION['error_messages'] = ['Error del servidor: ' . $e->getMessage()];
    $_SESSION['login_data'] = ['username' => $username];
    header('Location: /pages/login/login.php');
    //echo "login_handler.php: Error del servidor";
    exit();
}
