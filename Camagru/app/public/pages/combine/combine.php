<?php
require_once __DIR__ . '/../../database/User.php';
require_once __DIR__ . '/../../database/mongo_db.php';
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
if (!SessionManager::getSessionKey('uuid')) {
  // echo "<script>alert('You must be logged in to access this page.');</script>";
  header('Location: /pages/login/login.php');
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Document</title>
  <link rel="icon" type="image/x-icon" href="/img/favicon.ico">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/pages/combine/combine.css">
</head>

<body>
  <?php
  $pageTitle = "Make your own collages - Camagru";
  include __DIR__ . '/../../pages/header/header.php';
  include __DIR__ . '/../../pages/left_bar/left_bar.php';
  ?>
  <h1> Create your personalized pictures </h1>
  <div id="MyPictures-container" class="dragzone-container">
    <button id="mypictures-scroll-left" class="scroll-button">◀</button>
    <div id=MyPictures class="dragzone">
      <?php
      $user_uuid = SessionManager::getSessionKey('uuid');
      $MyPicturesInstance = new DocumentDB('uploads');
      $MyPicturesInstance->connect();
      // $mongo = $MyPicturesInstance->getCollection();
      $photos = $MyPicturesInstance->getUserPhotos($user_uuid);
      // Display images
      if (!empty($photos)) {
        // Loop through each photo and display it
        foreach ($photos as $photo) {
          if (isset($photo['filedata'])) {
            $mime = $photo['mimetype']; // Cambia si usas otro tipo MIME
            if ($mime == 'image/png' || $mime == 'image/jpeg' || $mime == 'image/jpg' || $mime == 'image/gif' || $mime == 'image/avif') {
              echo ('<script>console.log("' . $mime . '")</script>');
              $base64 = base64_encode($photo['filedata']->getData());
              // $imgTag = '<img src="data:' . $mime . ';base64,' . $base64 . '" alt="' . htmlspecialchars($photo['filename']) . '" width="200">';
              $photoId = isset($photo['_id']) ? (string)$photo['_id'] : '';
              // Link to a photo details page, passing photo id as GET parameter
              echo '<div class="draggable-item" draggable="true" id="photo-' . htmlspecialchars($photoId) . '">';
              echo '<img src="data:' . $mime . ';base64,' . $base64 . '" alt="' . htmlspecialchars($photo['filename']) . '">';
              echo '</div>';
              // $photoId = is
              // echo '<a href="/pages/picture/picture.php?picture_uuid=' . urlencode($photoId) . '">' . $imgTag . '</a>';
            } else {
              echo ('<script>console.log("Unsupported image format: ' . $mime . ' fichero: ' . htmlspecialchars($photo['filename']) . '")</script>');
            }
          }
        }
      } else { ?>
        <p>Upload your photos to make collages in our <a href="/pages/upload/upload.php?type=photo">Upload</a></p>
      <?php } ?>
    </div>
    <button id="mypictures-scroll-right" class="scroll-button">▶</button>
  </div>
  <div id="combine-container">
    <h2>Combine Pictures</h2>
    <p>Drag and drop your pictures here starting from the upper bar</p>
    <div id="CombinedImages" class="dropzone">
      <!-- Combined images will appear here -->

    </div>
    <button class="Save" id="save" type="submit">Save</button>
    <button class="Magic" id="magic" type="submit">Magic</button>
    <button class="Clean" id="clean" type="clean">Clean</button>
    </form>
  </div>
  <div id="Master-container" class="dragzone-container">
    <button id="master-scroll-left" class="scroll-button">◀</button>
    <div id=Master class="dragzone">
      <?php
      // $user_uuid = SessionManager::getSessionKey('uuid');
      $MasterInstance = new DocumentDB('masters');
      $MasterInstance->connect();
      $photos = $MasterInstance->getAllFiles();
      // Display images
      if (!empty($photos)) {
        // Loop through each photo and display it
        foreach ($photos as $photo) {
          if (isset($photo['filedata'])) {
            $mime = $photo['mime_type'] ?? 'image/png'; // Cambia si usas otro tipo MIME
            $base64 = base64_encode($photo['filedata']->getData());
            $imgTag = '<img src="data:' . $mime . ';base64,' . $base64 . '" alt="' . htmlspecialchars($photo['filename']) . '" width="200">';
            // Link to a photo details page, passing photo id as GET parameter
            $photoId = isset($photo['_id']) ? (string)$photo['_id'] : '';
            echo '<div class="draggable-item" draggable="true" id="photo-' . htmlspecialchars($photoId) . '">';
            echo '<img src="data:' . $mime . ';base64,' . $base64 . '" alt="' . htmlspecialchars($photo['filename']) . '">';
            echo '</div>';
            // echo '<a href="/pages/picture/picture.php?picture_uuid=' . urlencode($photoId) . '" draggable="true">' . $imgTag . '</a>';
          }
        }
      } else { ?>
        <p>Upload your photos to make collages in our <a href="/pages/upload/upload.php?type=master">Upload</a></p>
      <?php } ?>
    </div>
    <button id="master-scroll-right" class="scroll-button">▶</button>
  </div>
  <?php
  include __DIR__ . '/../../pages/right_bar/right_bar.php';
  include __DIR__ . '/../../views/footer.php';
  ?>
</body>
<script src="/js/resize_image.js" type="module"></script>
<script type="module" src="/pages/combine/combine.js"></script>

</html>