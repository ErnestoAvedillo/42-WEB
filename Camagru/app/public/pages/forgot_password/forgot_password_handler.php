<?php
require_once __DIR__ . "/../../database/User.php";
require_once __DIR__ . "/../../class_session/session.php";
SessionManager::getInstance();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF token check
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['error_messages'] = ['Invalid CSRF token.'];
        header("Location: /pages/forgot_password/forgot_password.php?token=" . ($_POST['token'] ?? '') . "&username=" . ($_POST['username'] ?? ''));
        exit;
    }
    $newPassword = $_POST['new-password'] ?? '';
    $confirmPassword = $_POST['confirm-password'] ?? '';
    $username = $_POST['username'] ?? '';
    $token = $_POST['token'] ?? '';

    // Basic validation
    if (empty($newPassword) || empty($confirmPassword)) {
        $_SESSION['error_messages'] = ['All fields are required.'];
        header("Location: /pages/forgot_password/forgot_password.php?token=$token&username=$username");
        exit;
    }

    $userInstance = new User();
    $userData = $userInstance->getUserByUsername($username);
    if (!$userData) {
        $_SESSION['error_messages'] = ['User not found.'];
        header("Location: /pages/forgot_password/forgot_password.php?token=$token&username=$username");
        exit;
    }

    // Update the password
    if ($userInstance->updatePassword($userData['uuid'], $newPassword)) {
        $_SESSION['success_message'] = 'Password updated successfully. <br> You can now log in with your new password.';
        header("Location: /pages/login/login.php");
        exit;
    } else {
        $_SESSION['error_messages'] = ['Failed to update password. Please try again later.'];
        header("Location: /pages/forgot_password/forgot_password.php?token=$token&username=$username");
        exit;
    }
} else {
    // If not a POST request, redirect to the form
    header("Location: /pages/forgot_password/forgot_password.php?token=$token&username=$username");
    exit;
}
