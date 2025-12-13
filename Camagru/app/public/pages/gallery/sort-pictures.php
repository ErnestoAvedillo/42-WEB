<?php
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
$csrf_token = $_SESSION['csrf_token'] ?? null;
file_put_contents('/tmp/sort_debug.log', "Current session data: " . print_r($_SESSION, true) . "\n", FILE_APPEND);
if (!$csrf_token) {
    $csrf_token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $csrf_token;
    file_put_contents('/tmp/sort_debug.log', "Generated new CSRF token: $csrf_token\n", FILE_APPEND);
}
require_once __DIR__ . '/../../database/mongo_db.php';
require_once __DIR__ . '/../../database/User.php';

// Debug: Log all POST data

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
file_put_contents('/tmp/sort_debug.log', "POST data received in sort-pictures: " . print_r($_POST, true) . "\n", FILE_APPEND);
$uuid = SessionManager::getSessionKey('uuid');
$sort_type = $_POST['sort-by'] ?? 'newest';
$number_elements = intval($_POST['nr_elements'] ?? 10);
$page = intval($_POST['page']); 
$user_filter = $_POST['user'] ?? 'all';

file_put_contents('/tmp/sort_debug.log', "!Number of elements: $number_elements, Page: $page, User filter: $user_filter\n", FILE_APPEND);

$client = new DocumentDB('combines');
if ($sort_type === 'newest'):
    $ascendant = false;
else:
    $ascendant = true;
endif;
file_put_contents('/tmp/sort_debug.log', "!!!Number of elements: $number_elements, Page: $page, User filter: $user_filter\n", FILE_APPEND);
if ($user_filter !== 'all'):
    $Users = new User();
    $user_data = $Users->getUserByUsername($user_filter);
    $pictures = $client->getFilesSortedByUsername($user_data['uuid'], $ascendant, $number_elements, ($page - 1) * $number_elements);
else:
    $pictures = $client->getFilesSortedByDate($ascendant, $number_elements, ($page - 1) * $number_elements);
endif;
$total_pictures = $pictures ? count($pictures) : 0;
?>

<div class="user-section">
    <?php foreach ($pictures as $photo) { ?>
        <?php if (isset($photo['filedata'])) { ?>
            <?php $mime = $photo['mime_type'] ?? 'image/png'; // Cambiar si usas otro tipo MIME 
            ?>
            <?php $base64 = base64_encode($photo['filedata']->getData()); ?>
            <?php $imgTag = '<img loading="lazy" src="data:' . $mime . ';base64,' . $base64 . '" alt="' . htmlspecialchars($photo['filename']) . '" width="200">'; ?>
            <?php $photoId = isset($photo['_id']) ? (string)$photo['_id'] : ''; ?>
            <div class="photo">
                <?php $user_uuid = $photo['user_uuid'] ?? ''; ?>
                <?php if ($user_uuid === SessionManager::getSessionKey('uuid')) { ?>
                    <div class="photo-actions">
                        <a href="data:<?php echo $mime; ?>;base64,<?php echo $base64; ?>" download="<?php echo htmlspecialchars($photo['filename']); ?>" class="download-icon">📥</a>
                        <a class="delete-image" href="#" data-image-id="<?php echo htmlspecialchars($photoId); ?>" data-container="<?php echo htmlspecialchars($container); ?>" class="delete-icon">🗑️</a>
                    </div>
                <?php } ?>
                <!-- <img src="data:<?php echo $mime; ?>;base64,<?php echo $base64; ?>" alt="<?php echo htmlspecialchars($photo['filename']); ?>" width="200"> -->
                <a href="/pages/picture/picture.php?picture_uuid=<?php echo urlencode($photoId); ?>"><?php echo $imgTag; ?></a>
            </div>
        <?php } ?>
    <?php } ?>
</div>