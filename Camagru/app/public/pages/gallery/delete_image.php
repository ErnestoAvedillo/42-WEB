<?php
require_once __DIR__ . '/../../database/User.php';
require_once __DIR__ . '/../../database/posts.php';
require_once __DIR__ . '/../../database/mongo_db.php';
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
if (!SessionManager::getSessionKey('uuid')) {
    // echo "<script>alert('You must be logged in to access this page.');</script>";
    header('Location: /pages/request_login/request_login.php');
    exit();
}
$successMessage = '';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF token check
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token']);
        exit();
    }
    // Validate file upload
    $container = $_POST['container'] ?: null;
    $pictureid = $_POST['image-id'];
    if (!$container || !$pictureid) {
        $errors[] = 'Invalid form submission.';
    } else {
        // Process deletion
        $user_uuid = SessionManager::getSessionKey('uuid');
        $posts = new Posts();
        if (!$posts->deletePostsByDocUuid($pictureid)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete posts from database']);
        } else {
            $client = new DocumentDB($container);
            $client->connect();
            $client->setCollection($container);
            if ($client->delete($pictureid)) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'message' => 'Photo deleted successfully']);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete photo']);
            }
        }
        exit();
    }
}
