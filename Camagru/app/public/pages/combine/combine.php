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
  <script>
    // Expose CSRF token to JS
    <?php
    if (!isset($_SESSION['csrf_token'])) {
      $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    $csrf_token = $_SESSION['csrf_token'];
    ?>
    window.CSRF_TOKEN = "<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>";
  </script>
  <div class="working-area">
    <div id="MyPictures-container" class="dragzone-container">
      <button id="mypictures-scroll-up" class="scroll-button">◀</button>
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
      <button id="mypictures-scroll-down" class="scroll-button">▶</button>
    </div>
    <div id="combine-container">
      <p>Drag and drop your pictures here starting from the upper bar</p>
      <div id="CombinedImages" class="dropzone">
        <!-- <canvas id="combined_canvas" hidden=true></canvas> -->
        <!-- Combined images will appear here -->
      </div>
      <div id="controls-container">
        <div id="camera-save-controls">
          <div id="camera_controls">
            <button class="manage" id="open_camera" type="button">Open Camera</button>
            <button class="manage" id="close_camera" type="button">Close Camera</button>
            <button class="manage" id="take_snapshot" type="button">Take Snapshot</button>
          </div>
          <div id="save-controls">
            <button class="manage" id="save" type="submit">Save</button>
            <button class="manage" id="clean" type="clean">Clean</button>
          </div>
        </div>
        <div id="magic_controls">
          <textarea id="prompt" name="prompt" placeholder="Enter prompt for AI generation" rows="4"></textarea>
          <button class="manage" id="magic" type="submit">Magic</button>
        </div>
      </div>
    </div>
    <div id="Master-container" class="dragzone-container">
      <button id="master-scroll-up" class="scroll-button">◀</button>
      <div id=Master class="dragzone">
        <?php
        // $user_uuid = SessionManager::getSessionKey('uuid');
        $MasterInstance = new DocumentDB('masters');
        $MasterInstance->connect();
        $photos = $MasterInstance->getAllFilesFromUser($user_uuid);
        file_put_contents('/tmp/combine_debug.log', "Processing photo: "  . htmlspecialchars($photoId) . "\n", FILE_APPEND);
        // Display images
        if (!empty($photos)) {
          // Loop through each photo and display it
          foreach ($photos as $photo) {
            file_put_contents('/tmp/combine_debug.log', "Processing photo: "  . htmlspecialchars($photoId) . "\n", FILE_APPEND);
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
      <button id="master-scroll-down" class="scroll-button">▶</button>
    </div>
  </div>
  <?php
  include __DIR__ . '/../../pages/right_bar/right_bar.php';
  include __DIR__ . '/../../pages/footer/footer.php';
  include __DIR__ . '/../../utils/wait/wait.php';
  ?>
</body>
<script src="/js/resize_image.js" type="module"></script>
<script type="module" src="/pages/combine/combine.js"></script>
<script type="module" src="/pages/combine/camera.js"></script>

</html>