<?php
require_once __DIR__ . '/../../database/User.php';
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
if (!SessionManager::getSessionKey('uuid')) {
    // echo "<script>alert('You must be logged in to access this page.');</script>";
    header('Location: /pages/login/login.php');
    exit();
}
$username = SessionManager::getSessionKey('username') ?? '';
$userId = SessionManager::getSessionKey('uuid') ?? '';
$token = bin2hex(random_bytes(32));
$userInstance = new User();
$recoveryTokenSet = $userInstance->setRecoveryToken($userId, $token);
if ($username && $userId && $recoveryTokenSet) {
    error_log("Recovery token set for user: $username");
    header("Location: /pages/create_new_password/create_new_password.php?username=" . urlencode($username) . "&token=" . urlencode($token));
    exit();
}
error_log("Failed to set recovery token for user: $username");
header('Location: /pages/index.php');
exit();
