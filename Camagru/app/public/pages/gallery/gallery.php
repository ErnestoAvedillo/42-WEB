<?php
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
$csrf_token = $_SESSION['csrf_token'] ?? null;
if (!$csrf_token) {
  $csrf_token = bin2hex(random_bytes(32));
  $_SESSION['csrf_token'] = $csrf_token;
}
require_once __DIR__ . '/../../database/mongo_db.php';
if (!SessionManager::getSessionKey('uuid')) {
  header('Location: /pages/login/login.php');
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
  <link rel="stylesheet" href="/pages/gallery/gallery.css">
</head>

<body>
  <input type="hidden" id="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
  <?php
  $pageTitle = "Home - Camagru";
  include __DIR__ . '/../../pages/header/header.php';
  include __DIR__ . '/../../pages/left_bar/left_bar.php';
  $container = 'combines';
  ?>
  <div class="gallery-container">
    <h1>Gallery of collages</h1>
    <p>Select the picture you want to comment. Or create <a href="/pages/combine/combine.php">your own collage.</a></p>
    <?php $userInstance = new User(); ?>
    <?php $users = $userInstance->getAllUsers(); ?>
    <?php $client = new DocumentDB($container); ?>
    <?php $client->connect(); ?>
    <div class="user-gallery">
      <?php $foundPictures = false; ?>
      <?php foreach ($users as $user) { ?>
        <?php $photos = $client->getUserPhotos($user['uuid']); ?>
        <div class="user-section">
          <?php if (!empty($photos)) { ?>
            <?php $foundPictures = true; ?>
            <div class="user-header">
              <h3><?php echo htmlspecialchars($user['username']); ?>'s Photos</h3>
            </div>
            <div class="photo-grid">
              <?php foreach ($photos as $photo) { ?>
                <?php if (isset($photo['filedata'])) { ?>
                  <?php $mime = $photo['mime_type'] ?? 'image/png'; // Cambiar si usas otro tipo MIME 
                  ?>
                  <?php $base64 = base64_encode($photo['filedata']->getData()); ?>
                  <?php $imgTag = '<img loading="lazy" src="data:' . $mime . ';base64,' . $base64 . '" alt="' . htmlspecialchars($photo['filename']) . '" width="200">'; ?>
                  <?php $photoId = isset($photo['_id']) ? (string)$photo['_id'] : ''; ?>
                  <div class="photo">
                    <?php if ($user['uuid'] === SessionManager::getSessionKey('uuid')) { ?>
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
          <?php } ?>
        </div>
      <?php } ?>
      <?php if (!$foundPictures) { ?>
        <div class="no-photos-message">
          <p>Upload your photos to make collages in our <a href="/pages/combine/combine.php">collage maker</a>.</p>
        </div>
      <?php } ?>
    </div>
    <?php
    include __DIR__ . '/../../pages/right_bar/right_bar.php';
    include __DIR__ . '/../../pages/footer/footer.php';
    ?>
    <script src="/pages/gallery/gallery.js"></script>
</body>

</html>