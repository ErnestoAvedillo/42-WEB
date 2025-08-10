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
    <link rel="stylesheet" href="/css/picture.css">
</head>

<body>
    <?php
    $pageTitle = "Home - Camagru";
    include __DIR__ . '/../../views/header.php';

    $pageTitle = "sidebar - Camagru";
    include __DIR__ . '/../../views/side_bar.php';
    ?>
    <?php
    $picture_uuid = $_GET['picture_uuid'] ?? ($_POST['picture_uuid'] ?? null);
    $client = new PictureDB();
    $client->connect();
    $mongo = $client->getCollection();
    $photo = $client->getFileById($picture_uuid);
    echo '<div class="picture-container">';
    echo '<h2>Your Photo</h2>';
    echo '<p>Here is the photo you have uploaded:</p>';
    echo '<div class="picture-grid">';
    if (empty($photo)) {
        echo '<p>Photo not found.</p>';
    } else {
        if (isset($photo['filedata'])) {
            $mime = $photo['mime_type'] ?? 'image/png'; // Cambia si usas otro tipo MIME
            $base64 = base64_encode($photo['filedata']->getData());
            echo '<img src="data:' . $mime . ';base64,' . $base64 . '" alt="' . htmlspecialchars($photo['filename']) . '" width="200">';
        } else {
            echo '<p>No image data found.</p>';
        }
    }
    echo '</div>';
    echo '</div>';
    ?>
    <?php
    $pageTitle = "footer - Camagru";
    include __DIR__ . '/../../views/footer.php';
    ?>
</body>

</html>