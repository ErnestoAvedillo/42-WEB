<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../database/User.php';
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
if (!SessionManager::getSessionKey('temp_user')) {
    // echo "<script>alert('You must be logged in to access this page.');</script>";
    header('Location: /pages/login/login.php');
    exit();
}

$temp_user = SessionManager::getSessionKey('temp_user');
$user = new User();
$secret = $user->get2FASecret($temp_user['uuid']);
if (!$secret) {
    // Si no hay secreto, redirigir al login
    header('Location: /pages/login/login.php');
    exit();
}

use OTPHP\TOTP;

$totp = TOTP::create($secret);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = trim($_POST['token'] ?? '');
    if (empty($token) || !preg_match('/^\d{6}$/', $token)) {
        $_SESSION['error_messages'] = ['Invalid authentication code format.'];
        header('Location: /pages/2FA_config/2FA_introduce.php');
        exit();
    }
    if ($totp->verify($token)) {
        SessionManager::getInstance()->saveDataSession($temp_user);
        // Limpiar datos temporales
        unset($_SESSION['temp_user']);
        // Limpiar errores
        unset($_SESSION['error_messages']);
        unset($_SESSION['login_data']);
        // Mensaje de éxito
        $_SESSION['success_message'] = 'Login successful! Welcome back.';
        header('Location: /index.php');
        exit();
    } else {
        $_SESSION['error_messages'] = ['Invalid authentication code. Please try again.'];
        header('Location: /pages/2FA_config/2FA_introduce.php');
        exit();
    }
} else {
    // Si no es POST, redirigir al formulario de introducción
    header('Location: /pages/2FA_config/2FA_introduce.php');
    exit();
}
