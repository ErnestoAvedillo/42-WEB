<?php
require_once __DIR__ . '/../../database/User.php';
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
$user = new User();
if ($userExists = $user->userExists($_GET['username'], $_GET['email'])) {
    $_SESSION['success'] = true;
    $_SESSION['success_message'] = "Your account has been successfully created. You can now log in.";
    error_log("User " . json_encode($userExists) . " successfully registered.");
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit();
} else {
    error_log("User registration pending or failed for username: " . json_encode($_GET['username']) . ", email: " . json_encode($_GET['email']));
    header('Content-Type: application/json');
    echo json_encode(['success' => false]);
}
