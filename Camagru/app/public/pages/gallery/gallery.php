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
  $pageTitle = "Gallery - Camagru";
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
    <div class="filter-bar">
      <label for="sort-select">Sort by:</label>
      <select class="sort-select">
        <option value="newest">Newest</option>
        <option value="oldest">Oldest</option>
      </select>
      <select class="user-sort-select">
        <option value="all">all</option>
        <?php foreach ($users as $user) : ?>
          <option value="<?php echo htmlspecialchars($user['username']); ?>"><?php echo htmlspecialchars($user['username']); ?></option>
        <?php endforeach; ?>
      </select>
      <label for="number_elements">Pictures per page:</label>
      <select id="number_elements" class="number-elements-select">
        <option value="3" >3</option>
        <option value="5" selected>5</option>
        <option value="10">10</option>
        <option value="20">20</option>
        <option value="50">50</option>
        <option value="all">all</option>
      </select>
    </div>
    <div class="user-gallery">
      <a> Texto para visualizar el div correctamente </a>
    </div>
    <div id="pagination-container" class="pagination-container">
    </div>
  </div>
  <?php
  include __DIR__ . '/../../pages/right_bar/right_bar.php';
  include __DIR__ . '/../../pages/footer/footer.php';
  ?>
  <script src="/pages/gallery/gallery.js"></script>
</body>

</html>