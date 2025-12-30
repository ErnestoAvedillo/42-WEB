<?php
require_once 'class_session/session.php';
SessionManager::getInstance();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($pageTitle) ? $pageTitle : 'Camagru'; ?></title>
  <link rel="stylesheet" href="/css/style.css">
  <link rel="icon" href="/img/favicon.ico" type="image/x-icon">
</head>

<body>
  <?php
  $pageTitle = "Home - Camagru";
  include __DIR__ . '/pages/header/header.php';

  include __DIR__ . '/pages/left_bar/left_bar.php';
  ?>
  <div class="home-container">
    <div class="hero-section">
      <h2>Welcome to Camagru</h2>
      <p>A modern photo sharing platform where you can upload, edit, and share your favorite images with the world.</p>
    </div>
    <div class="features-section">
      <h3>Key Features</h3>
      <div class="features-grid">
        <a href="/pages/upload/upload.php?type=photo" class="feature-card-link">
          <div class="feature-card">
            <h4>📸 Easy Upload</h4>
            <p>Upload your images to be and more with our simple drag-and-drop interface.</p>
          </div>
        </a>
        <a href="/pages/upload/upload.php?type=master" class="feature-card-link">
          <div class="feature-card">
            <h4>🪟 Upload Masters</h4>
            <p>Upload your masters to decorate your photos with our simple drag-and-drop interface.</p>
          </div>
        </a>
        <a href="/pages/combine/combine.php" class="feature-card-link">
          <div class="feature-card">
            <h4>🌁 Combine Photos</h4>
            <p>Create stunning collages by combining your photos with uploaded masters using our intuitive editor.</p>
          </div>
        </a>
        <a href="/pages/gallery/gallery.php" class="feature-card-link">
          <div class="feature-card">
            <h4>🖼️ Gallery View</h4>
            <p>Browse all uploaded files in a beautiful gallery layout with filtering options.</p>
          </div>
        </a>
      </div>
    </div>
  </div>
  <link rel="stylesheet" href="css/home.css">
  
  <?php
  if (isset($_SESSION) && !empty($_SESSION['user_id'])) {
    include __DIR__ . '/pages/right_bar/right_bar.php';
  }

  include __DIR__ . '/pages/footer/footer.php';
  ?>
</body>