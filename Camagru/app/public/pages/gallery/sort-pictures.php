<?php
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
$csrf_token = $SESSION['csrf_token'] ?? null;
if (!$csrf_token) {
    $csrf_token = bin2hex(random_bytes(32));
    $SESSION['csrf_token'] = $csrf_token;
}
require_once __DIR__ . '/../../database/mongo_db.php';
require_once __DIR__ . '/../../database/User.php';
if (!SessionManager::getSessionKey('uuid')) {
    header('Location: /pages/login/login.php');
    exit();
}
$uuid = SessionManager::getSessionKey('uuid');
$sort_type = $_POST['sort-by'] ?? 'newest';
$number_elements = $_POST['nr_elements'] ?? 10;
$page = $_POST['page'] ?? 1; ?>
<?php $client = new DocumentDB('combines'); ?>
<?php if ($sort_type === 'newest'): ?>
    <?php $pictures = $client->getFilesSortedByDate(false, $number_elements, ($page - 1) * $number_elements); ?>
<?php elseif ($sort_type === 'oldest'): ?>
    <?php $pictures = $client->getFilesSortedByDate(true, $number_elements, ($page - 1) * $number_elements); ?>
<?php elseif ($sort_type === 'username'): ?>
    <?php $pictures = $client->getFilesSortedByUsername($number_elements, ($page - 1) * $number_elements); ?>
    <?php $Users = new User(); ?>
    <?php $all_users = $Users->getAllUsers(); ?>
<?php endif; ?>
<?php $total_pictures = $client->getTotalFilesCount(); ?>
<?php $foundPictures = false; ?>

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
<div id="pagination_info" data-total-pictures="<?php echo $total_pictures; ?>"> </div>