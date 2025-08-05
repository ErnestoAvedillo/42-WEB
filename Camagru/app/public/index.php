<?php
require_once 'class_session/session.php';
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
  include 'views/header.php';

  $pageTitle = "sidebar - Camagru";
  include 'views/side_bar.php';
  ?>

  <div class="home-container">
    <div class="hero-section">
      <h2>Welcome to Camagru</h2>
      <p>A modern photo sharing platform where you can upload, edit, and share your favorite images with the world.</p>
      <div class="feature-buttons">
        <a href="/pages/upload/upload.php" class="btn btn-primary">Upload Photos</a>
        <a href="/pages/gallery/gallery.php" class="btn btn-secondary">View Gallery</a>
      </div>
    </div>
    <div class="features-section">
      <h3>Key Features</h3>
      <div class="features-grid">
        <div class="feature-card">
          <h4>📸 Easy Upload</h4>
          <p>Upload images, PDFs, Word documents and more with our simple drag-and-drop interface.</p>
        </div>
        <div class="feature-card">
          <h4>🖼️ Gallery View</h4>
          <p>Browse all uploaded files in a beautiful gallery layout with filtering options.</p>
        </div>
        <div class="feature-card">
          <h4>👥 User Management</h4>
          <p>Register an account to manage your uploads and personalize your experience.</p>
        </div>
      </div>
    </div>
  </div>
  <link rel="stylesheet" href="css/home.css">
  <?php
  include 'views/footer.php';
  ?>
</body>