<?php
session_start();
require_once __DIR__ . '/../../../database/User.php';
require_once __DIR__ . '/../../../utils/send_mail.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Invalid email address';
        header('Location: /pages/login/password_recover/recover.php');
        exit;
    }

    // Check if user exists
    $user = new User();
    if (!$user->isEmailTaken($email)) {
        $_SESSION['error'] = 'Email does not exist in our records';
        header('Location: /pages/login/password_recover/recover.php');
        exit;
    }

    // Generate recovery token
    $token = bin2hex(random_bytes(50));
    $userData = $user->getUserByEmail($email);
    $user->setRecoveryToken($userData['uuid'], $token);

    // Send recovery email
    send_recovery_email($email, $userData['username'], $token);

    header('Location: /pages/login/password_recover/recover_success.php');
    exit;
}
