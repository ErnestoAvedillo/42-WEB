<?php
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
require_once __DIR__ . '/../../database/mongo_db.php';
if (!SessionManager::getSessionKey('uuid')) {
    header('Location: /pages/request_login/request_login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery - Camagru</title>
    <link rel="icon" href="/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/gallery.css">
</head>

<body>
    <?php
    $pageTitle = "Home - Camagru";
    include __DIR__ . '/../../views/header.php';

    $pageTitle = "sidebar - Camagru";
    include __DIR__ . '/../../views/side_bar.php';
    ?>
    <div class="gallery-container">
        <h1>Photo Gallery</h1>
        <p>Here you can view all the amazing photos shared by our community.</p>
        <div class="gallery-grid">
            <!-- Gallery content will go here -->
            <p>Gallery coming soon...</p>
        </div>
        <?php
        $user_uuid = SessionManager::getSessionKey('uuid');
        $client = new DocumentDB();
        $client->connect();
        $mongo = $client->getCollection();
        $photos = $client->getUserPhotos($user_uuid);
        if (!empty($photos)) {
            echo '<div class="user-gallery">';
            echo '<h2>Your Photos</h2>';
            echo '<p>Here are the photos you have uploaded:</p>';
            echo '<div class="photo-grid">';
            if (empty($photos)) {
                echo '<p>No photos found.</p>';
            } else {
                foreach ($photos as $photo) {
                    if (isset($photo['filedata'])) {
                        $mime = $photo['mime_type'] ?? 'image/png'; // Cambia si usas otro tipo MIME
                        $base64 = base64_encode($photo['filedata']->getData());
                        $imgTag = '<img src="data:' . $mime . ';base64,' . $base64 . '" alt="' . htmlspecialchars($photo['filename']) . '" width="200">';
                        // Link to a photo details page, passing photo id as GET parameter
                        $photoId = isset($photo['_id']) ? (string)$photo['_id'] : '';
                        echo '<a href="/pages/picture/picture.php?picture_uuid=' . urlencode($photoId) . '">' . $imgTag . '</a>';
                    } else {
                        echo '<p>No image data found.</p>';
                    }
                }
            }
            echo '</div>';
        } else {
            echo '<p>No photos uploaded yet.</p>';
        }
        ?>
    </div>
    <?php
    $pageTitle = "footer - Camagru";
    include __DIR__ . '/../../views/footer.php';
    ?>
</body>

</html>