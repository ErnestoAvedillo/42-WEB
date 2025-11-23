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
    // CSRF token check
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        echo '<p style="color: red;">Invalid CSRF token.</p>';
        exit();
    }
    $token = trim($_POST['token'] ?? '');
    if (empty($token) || !preg_match('/^\d{6}$/', $token)) {
        $_SESSION['error_messages'] = ['Invalid authentication code format.'];
        header('Location: /pages/2FA_config/2FA_disable.php');
        exit();
    }
    if ($totp->verify($token)) {
        $user = new User();
        $user->disable2FA($user_uuid);
        SessionManager::getInstance()->setSessionKey('two_factor_enabled', false);
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
