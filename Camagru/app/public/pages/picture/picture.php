<?php
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
$csrf_token = $_SESSION['csrf_token'] ?? null;
if (!$csrf_token) {
    $csrf_token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $csrf_token;
}
require_once __DIR__ . '/../../database/mongo_db.php';
require_once __DIR__ . '/../../database/likes.php';

// Debug logging para diagnosticar el problema
$uuid = SessionManager::getSessionKey('uuid');
$logged_in = SessionManager::getSessionKey('logged_in');
if (!$uuid || !$logged_in) {
    header('Location: /pages/login/login.php?forward=/pages/picture/picture.php?picture_uuid=' . urlencode($_GET['picture_uuid'] ?? ''));
    exit();
}
require_once __DIR__ . '/../../database/posts.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo htmlspecialchars($csrf_token); ?>">
    <title>Gallery - Camagru</title>
    <link rel="icon" href="/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/pages/picture/picture.css">
</head>

<body>
    <?php
    $pageTitle = "Comment - Camagru";
    include __DIR__ . '/../../pages/header/header.php';

    $pageTitle = "sidebar - Camagru";
    include __DIR__ . '/../../pages/left_bar/left_bar.php';
    $picture_uuid = $_GET['picture_uuid'] ?? ($_POST['picture_uuid'] ?? null);
    $user_uuid = $_SESSION['uuid'] ?? null;
    $client = new DocumentDB('combines');
    $client->connect();
    $mongo = $client->getCollection();
    $photo = $client->getFileById($picture_uuid);
    ?>
    <div class="picture-container">
        <h2>Comment this file!!</h2>
        <div class="picture-grid">
            <?php
            if (empty($photo)) {
                echo '<p>Photo not found.</p>';
            } else {
                if (isset($photo['filedata'])) {
                    $mime = $photo['mime_type'] ?? 'image/png'; // Cambia si usas otro tipo MIME
                    $base64 = base64_encode($photo['filedata']->getData());
                    echo '<img id="picture" src="data:' . $mime . ';base64,' . $base64 . '" alt="' . htmlspecialchars($photo['filename']) . '" width="200">';
                } else {
                    echo '<p>No image data found.</p>';
                }
            }
            echo '</div class="likes_container">';
            $likes = new Likes();
            $user_liked = $likes->hasUserLiked($user_uuid, $picture_uuid);
            $user_disliked = $likes->hasUserDisliked($user_uuid, $picture_uuid);
            $user_reacted = $likes->hasUserReacted($user_uuid, $picture_uuid);
            $like_count = $likes->getLikeCount($picture_uuid);
            $dislike_count = $likes->getDislikeCount($picture_uuid);
            ?>
            <div class="likes_container">
                <button class="like_button" 
                        type="button"
                        data-picture-uuid="<?php echo htmlspecialchars($picture_uuid); ?>" 
                        data-liked="<?php echo $user_liked ? 'true' : 'false'; ?>" 
                        data-action="like"
                        <?php echo $user_liked ? 'aria-pressed="true"' : 'aria-pressed="false"'; ?>>
                    <span class="like-icon"><?php echo $user_liked ? '👍' : '🖒'; ?></span> 
                    <span class="like-count">(<?php echo $like_count; ?>)</span>
                </button>
                <button class="dislike_button" 
                        type="button"
                        data-picture-uuid="<?php echo htmlspecialchars($picture_uuid); ?>" 
                        data-disliked="<?php echo $user_disliked ? 'true' : 'false'; ?>" 
                        data-action="dislike"
                        <?php echo $user_disliked ? 'aria-pressed="true"' : 'aria-pressed="false"'; ?>>
                    <span class="dislike-icon"><?php echo $user_disliked ? '👎' : '👎︎'; ?></span> 
                    <span class="dislike-count">(<?php echo $dislike_count; ?>)</span>
                </button>
            </div>
            <?php
            $post = new Posts();
            $posts = $post->getPostsByDocUuid($picture_uuid);
            if ($posts) {
                foreach ($posts as $post) {
                    echo '<div class="post">';
                    echo '<p>' . htmlspecialchars($post['caption']) . '</p>';
                    echo '</div>';
                }
            } else {
                echo '<p>Start posting!</p>';
            }
            ?>
        </div>
        <?php include __DIR__ . '/../../utils/wait/wait.php'; ?>
        <div class="picture-actions">
            <form class="picture-action-form" action="/pages/picture/add_post.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <input type="hidden" name="picture_uuid" value="<?php echo htmlspecialchars($picture_uuid); ?>">
                <input type="hidden" name="user_uuid" value="<?php echo $_SESSION['uuid']; ?>">
                <textarea id="caption" name="caption" placeholder="Add a caption..." required></textarea>
                <button class="button" type="submit">Post</button>
                <button class="button" type="button" id="auto-fill">Auto Fill</button>
            </form>
        </div>
    </div>
    <?php
    $pageTitle = "right side bar - Camagru";
    include __DIR__ . '/../../pages/right_bar/right_bar.php';

    $pageTitle = "footer - Camagru";
    include __DIR__ . '/../../pages/footer/footer.php';
    ?>
</body>
<script src="/pages/picture/auto_fill.js"></script>
<script src="/pages/picture/picture.js"></script>

</html>