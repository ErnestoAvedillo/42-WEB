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
  <link rel="stylesheet" href="/pages/gallery/gallery.css">
</head>

<body>
  <?php
  $pageTitle = "Home - Camagru";
  include __DIR__ . '/../../pages/header/header.php';
  include __DIR__ . '/../../pages/left_bar/left_bar.php';
  $container = 'combines';
  ?>
  <div class="gallery-container">
    <h1>Gallery of collages</h1>
    <p>Select the picture you want to comment.</p>
    <?php $user_uuid = SessionManager::getSessionKey('uuid'); ?>
    <?php $client = new DocumentDB($container); ?>
    <?php $client->connect(); ?>
    <?php $photos = $client->getUserPhotos($user_uuid); ?>
    <?php if (!empty($photos)) { ?>
      <div class="user-gallery">
        <div class="photo-grid">
          <?php foreach ($photos as $photo) { ?>
            <?php if (isset($photo['filedata'])) { ?>
              <?php $mime = $photo['mime_type'] ?? 'image/png'; // Cambia si usas otro tipo MIME 
              ?>
              <?php $base64 = base64_encode($photo['filedata']->getData()); ?>
              <?php $imgTag = '<img src="data:' . $mime . ';base64,' . $base64 . '" alt="' . htmlspecialchars($photo['filename']) . '" width="200">'; ?>
              <?php $photoId = isset($photo['_id']) ? (string)$photo['_id'] : ''; ?>
              <div class="photo">
                <div class="photo-actions">
                  <a href="data:<?php echo $mime; ?>;base64,<?php echo $base64; ?>" download="<?php echo htmlspecialchars($photo['filename']); ?>" class="download-icon">📥</a>
                  <a class="delete-image" href="#" data-image-id="<?php echo htmlspecialchars($photoId); ?>" data-container="<?php echo htmlspecialchars($container); ?>" class="delete-icon">🗑️</a>
                </div>
                <!-- <img src="data:<?php echo $mime; ?>;base64,<?php echo $base64; ?>" alt="<?php echo htmlspecialchars($photo['filename']); ?>" width="200"> -->
                <a href="/pages/picture/picture.php?picture_uuid=<?php echo urlencode($photoId); ?>"><?php echo $imgTag; ?></a>
              </div>
            <?php } else { ?>
              <p>No image data found.</p>
            <?php } ?>
          <?php } ?>
        </div>
      <?php } else { ?>
        <p>Upload your photos to make collages in our <a href="/pages/upload/upload.php">Upload</a></p>
      <?php } ?>
      </div>
  </div>
  <?php
  include __DIR__ . '/../../pages/right_bar/right_bar.php';
  include __DIR__ . '/../../views/footer.php';
  ?>
  <script src="/pages/gallery/gallery.js"></script>
</body>

</html>