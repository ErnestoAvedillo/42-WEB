<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../database/User.php';
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
if (!SessionManager::getSessionKey('uuid')) {
    // echo "<script>alert('You must be logged in to access this page.');</script>";
    header('Location: /pages/login/login.php');
    exit();
}

$user_uuid = SessionManager::getSessionKey('uuid');
$user = new User();
$secret = $user->get2FASecret($user_uuid);
if (!$secret) {
    // Si no hay secreto, redirigir al login
    header('Location: /index.php');
    exit();
}

use OTPHP\TOTP;

$totp = TOTP::create($secret);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = trim($_POST['token'] ?? '');
    if (empty($token) || !preg_match('/^\d{6}$/', $token)) {
        $_SESSION['error_messages'] = ['Invalid authentication code format.'];
        header('Location: /pages/2FA_config/2FA_disable.php');
        exit();
    }
    if ($totp->verify($token)) {
        $user = new User();
        $user->disable2FA($user_uuid);
        file_put_contents("/tmp/debug.log", "2FA disabled for user: " . $_SESSION['two_factor_enabled'] . "\n", FILE_APPEND);
        SessionManager::getInstance()->setSessionKey('two_factor_enabled', false);
        file_put_contents("/tmp/debug.log", "2FA disabled for user: " . $_SESSION['two_factor_enabled'] . "\n", FILE_APPEND);
        // Mensaje de éxito
        $_SESSION['success_message'] = 'Two-Factor Authentication has been disabled.';
        header('Location: /index.php');
        exit();
    } else {
        $_SESSION['error_messages'] = ['Invalid authentication code. Please try again.'];
        header('Location: /pages/2FA_config/2FA_disable.php');
        exit();
    }
} else {
    // Si no es POST, redirigir al formulario de introducción
    header('Location: /pages/2FA_config/2FA_disable.php');
    exit();
}
