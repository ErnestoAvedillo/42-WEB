<?php
require_once __DIR__ . '/../../database/likes.php';
require_once __DIR__ . '/../../class_session/session.php';

SessionManager::getInstance();

// Verificar que el usuario esté logueado
if (!SessionManager::isSessionActive()) {
    header('HTTP/1.1 403 Forbidden');
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Obtener datos del POST (JSON o form data)
$input = json_decode(file_get_contents('php://input'), true);
if ($input === null) {
    // Si no es JSON, intentar con $_POST
    $picture_uuid = $_POST['picture_uuid'] ?? null;
    $action = $_POST['action'] ?? null;
    $csrf_token = $_POST['csrf_token'] ?? null;
} else {
    // Es JSON
    $picture_uuid = $input['picture_uuid'] ?? null;
    $action = $input['action'] ?? null;
    $csrf_token = $input['csrf_token'] ?? null;
}

$user_uuid = SessionManager::getSessionKey('uuid');

// CSRF token check
if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf_token)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit();
}

// Validar parámetros requeridos
if (!$picture_uuid || !$user_uuid || !$action) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit();
}

// Validar acción
if (!in_array($action, ['like', 'dislike'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit();
}

try {
    $likes = new Likes();
    
    if ($action === 'like') {
        $result = $likes->toggleLike($user_uuid, $picture_uuid);
    } elseif ($action === 'dislike') {
        $result = $likes->toggleDislike($user_uuid, $picture_uuid);
    }

    // Obtener estado actualizado
    $current_like_status = $likes->getUserLikeStatus($user_uuid, $picture_uuid);
    $like_count = $likes->getLikeCount($picture_uuid);
    $dislike_count = $likes->getDislikeCount($picture_uuid);

    header('Content-Type: application/json');
    echo json_encode([
        'success' => $result['success'] ?? true,
        'message' => $result['message'] ?? 'Action completed',
        'like_count' => $like_count,
        'dislike_count' => $dislike_count,
        'user_reaction' => $current_like_status
    ]);

} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>