<?php
require_once __DIR__ . '/../../database/User.php';
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
if (!SessionManager::getSessionKey('uuid')) {
  echo "<script>alert('You must be logged in to access this page.');</script>";
  header('Location: /pages/request_login/request_login.php');
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Camagru</title>
  <link rel="icon" href="/img/favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/pages/upload/upload.css">
</head>

<body>
  <?php
  //    include __DIR__ . '/../../views/debugger.php';
  $pageTitle = "Home - Camagru";
  include __DIR__ . '/../header/header.php';
  include __DIR__ . '/../left_bar/left_bar.php';
  ?>
  <div class="uopload-container">
    <?php
    $type = $_GET['type'] ?: null;
    // if ($successMessage != '') {
    //     echo "<div class='alert-success'>" . htmlspecialchars($successMessage) . "</div>";
    // }
    if (isset($errors) && !empty($errors)) {
      // Mostrar errores
      foreach ($errors as $error) {
        echo "<div class='alert-error'>" . htmlspecialchars($error) . "</div>";
      }
    }
    ?>
    <?php if ($type === 'master') { ?>
      <h1>Upload Your Master to decorate your Photos</h1>
      <p>Share your creativity with the world by uploading your masters.</p>
    <?php } elseif ($type === 'photo') { ?>
      <h1>Upload Your Photos to decorate with Masters</h1>
      <p>Share your creativity with the world by uploading your photos.</p>
    <?php } ?>
    <form class="upload-form" action="upload_handler.php?type=<?php echo htmlspecialchars($type); ?>" method="post" enctype="multipart/form-data">
      <?php if ($type === 'master') { ?>
        <input class="upload-input" type="file" multiple name="file[]" accept=".png" required>
      <?php } elseif ($type === 'photo') { ?>
        <input class="upload-input" type="file" multiple name="file[]" accept=".jpg,.jpeg,.png,.gif" required>
      <?php } ?>
      <!-- <input type="file" name="photo" accept=".jpg,.jpeg,.png,.gif,.pdf,.mp4,.zip,.docx" required> -->
      <input type="hidden" name="user_uuid" value="<?php echo htmlspecialchars(SessionManager::getSessionKey('uuid')); ?>">
      <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>">
      <button type="submit" class="btn btn-primary">Upload Photos</button>
      <button href="/pages/gallery/gallery.php" class="btn btn-secondary">View Gallery</button>
    </form>
    <?php if ($type === 'master') { ?>
      <p>Supported format: PNG (with transparent background)</p>
    <?php } elseif ($type === 'photo') { ?>
      <p>Supported formats: JPG, PNG, GIF</p>
    <?php } ?>
    <!-- <p>Supported formats: JPG, PNG, GIF, PDF, MP4, ZIP, DOCX</p> -->
  </div>
  <div class="container">
    <?php if ($type === 'master') { ?>
      <h2>Your Uploaded Masters</h2>
    <?php } elseif ($type === 'photo') { ?>
      <h2>Your Uploaded Photos</h2>
    <?php } ?>
    <?php
    $user_uuid = SessionManager::getSessionKey('uuid');
    if ($type === 'master') {
      $collection = 'masters';
    } else {
      $collection = 'uploads';
    }
    $client = new DocumentDB($collection);
    $client->connect();
    // $mongo = $client->getCollection();
    $photos = $client->getUserPhotos($user_uuid);
    if (!empty($photos)) {
    ?>
      <div class="galeria">
        <
          <?php
          foreach ($photos as $photo) {
            if (isset($photo['filedata'])) {
              $mime = $photo['mime_type'] ?? 'image/png'; // Cambia si usas otro tipo MIME
              $base64 = base64_encode($photo['filedata']->getData());
              $postData = 'data-container=' . urlencode($collection) . ' data-image-id=' . urlencode($photo['_id']);
          ?>
          <div class="photo">
          <div class="photo-actions">
            <a href="data:<?php echo $mime; ?>;base64,<?php echo $base64; ?>" download="<?php echo htmlspecialchars($photo['filename']); ?>" class="download-icon">📥</a>
            <a class="delete-image" href="#" <?php echo htmlspecialchars($postData); ?> class="delete-icon">🗑️</a>
          </div>
          <img src="data:<?php echo $mime; ?>;base64,<?php echo $base64; ?>" alt="<?php echo htmlspecialchars($photo['filename']); ?>" width="200">
      </div>
    <?php
            } else {
    ?>
      echo '<p>No image data found.</p>';
    <?php
            }
          }
        } else {
          if ($type === 'master') {
    ?>
    <p>You have not uploaded any masters yet. Start by uploading your first master!</p>
  <?php
          } else {
  ?>
    <p>You have not uploaded any photos yet. Start by uploading your first photo!</p>
<?php
          }
        }
?>
  </div>
  </div>
  <?php
  //    echo "<pre>";
  //    var_dump($_SESSION);
  //    echo "</pre>";
  include __DIR__ . '/../right_bar/right_bar.php';
  include __DIR__ . '/../../views/footer.php';
  ?>
  <script src="/pages/upload/upload.js"></script>
</body>

</html>