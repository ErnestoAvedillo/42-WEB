<?php
require_once __DIR__ . '/../../database/User.php';
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();

// Verificar que la petición sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

$user = new User();
if ($userExists = $user->userExists($_POST['username'], $_POST['email'])) {
    $_SESSION['success'] = true;
    $_SESSION['success_message'] = "Your account has been successfully created. You can now log in.";
    error_log("User " . json_encode($userExists) . " successfully registered.");
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit();
} else {
    error_log("User registration pending or failed for username: " . json_encode($_POST['username']) . ", email: " . json_encode($_POST['email']));
    header('Content-Type: application/json');
    echo json_encode(['success' => false]);
}
