<?php
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
require_once __DIR__ . '/../../database/mongo_db.php';
if (!SessionManager::getSessionKey('uuid')) {
  header('Location: /pages/login/login.php');
    exit();
}
require_once __DIR__ . '/../../database/posts.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery - Camagru</title>
    <link rel="icon" href="/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/pages/picture/picture.css">
</head>

<body>
    <?php
    $pageTitle = "Home - Camagru";
    include __DIR__ . '/../../pages/header/header.php';

    $pageTitle = "sidebar - Camagru";
    include __DIR__ . '/../../pages/left_bar/left_bar.php';
    $picture_uuid = $_GET['picture_uuid'] ?? ($_POST['picture_uuid'] ?? null);
    $client = new DocumentDB('combines');
    $client->connect();
    $mongo = $client->getCollection();
    $photo = $client->getFileById($picture_uuid);
    ?>
    <div class="picture-container">
        <h2>Your Photo</h2>
        <p>Here is the photo you have uploaded:</p>
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
    include __DIR__ . '/../../views/footer.php';
    ?>
</body>
<script src="/pages/picture/auto_fill.js"></script>

</html>