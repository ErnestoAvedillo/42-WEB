<?php
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
$csrf_token = $_SESSION['csrf_token'] ?? null;
if (!$csrf_token) {
    $csrf_token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $csrf_token;
}
require_once __DIR__ . '/../../database/mongo_db.php';
require_once __DIR__ . '/../../database/User.php';
// Validate CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $csrf_token) {
    file_put_contents('/tmp/sort_debug.log', "CSRF token validation failed. Expected: $csrf_token, Received: " . ($_POST['csrf_token'] ?? 'null') . "\n", FILE_APPEND);
    http_response_code(403);
    echo json_encode(['error' => 'Invalid CSRF token']);
    exit();
}

if (!SessionManager::getSessionKey('uuid')) {
    header('Location: /pages/login/login.php');
    exit();
}
file_put_contents('/tmp/pagination_debug.log', "POST data received for pagination: " . print_r($_POST, true) . "\n", FILE_APPEND);
$uuid = SessionManager::getSessionKey('uuid');
$user = $_POST['user'] ?? 'all';
$number_elements = intval($_POST['nr_elements'] ?? 10);
$page = intval($_POST['page'] ?? 1);
$client = new DocumentDB('combines');
$Users = new User();
$user_data = $Users->getUserByUsername($user);
if ($user === 'all'):
  $total_pictures = $client->getTotalFilesCount();
else:
  $total_pictures = $client->getFilesCountByUser($user_data['uuid']);
endif;
if ($number_elements <= 0) {
    return ""; // Default value
}
$total_buttons = ceil($total_pictures / $number_elements) ;
if ($page > 1):
  $first_button = $page - 1;
else:
  $first_button = $page;
endif;
file_put_contents('/tmp/pagination_debug.log', "Total pictures: $total_pictures, Number elements: $number_elements, Total buttons-: $total_buttons, First button: $first_button\n", FILE_APPEND);
?>
<?php for ($i = $first_button; $i <= $total_buttons; $i++): ?>
  <button class="pagination-button" value="<?php echo $i; ?>"><?php echo $i; ?></button>
<?php endfor; ?>
