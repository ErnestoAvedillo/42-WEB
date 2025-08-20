<?php
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
require_once __DIR__ . '/../../database/mongo_db.php';
require_once __DIR__ . '/../../database/User.php';
$user = new User();
$picture_uuid =
  $client = new DocumentDB();
?>
<header>
  <link rel="stylesheet" href="/pages/header/header.css">
  <button id="toggleSidebarBtn">☰</button>
  <h1>Camagru</h1>
  <nav>
    <ul>
      <?php if ($isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in']) { ?>
        <div class="user-info" id="userInfo">
          <div class="avatar">
            <?php
            $userData = $user->getUserProfile($_SESSION['uuid']);
            // Check if user has a photo UUID and fetch the photo
            $photo = $client->getFileById($userData);
            if ($userData && isset($userData)) {
              if ($photo && isset($photo['filedata'])) {
                $base64 = base64_encode($photo['filedata']->getData());
                echo '<img src="data:' . $photo['mimetype'] . ';base64,' . $base64 . '" alt="User Photo" class="user-photo">';
              } else {
                echo '<img src="/img/avatar.jpg" alt="Default Avatar" class="user-photo">';
              }
            } else {
              echo '<img src="/img/avatar.jpg" alt="Default Avatar" class="user-photo">';
            }
            ?>
          </div>
          <div class="user-details">
            <span class="username">Guest User</span>
            <span class="status">Online</span>
          </div>
        </div>
      <?php } else { ?>
        <li><a href="/pages/login/login.php" <?php echo (basename($_SERVER['SCRIPT_NAME']) == 'login.php') ? 'class="active"' : ''; ?>>Login</a></li>
        <li><a href="/pages/register/register.php" <?php echo (basename($_SERVER['SCRIPT_NAME']) == 'register.php') ? 'class="active"' : ''; ?>>Register</a></li>
      <?php } ?>
    </ul>
  </nav>
  <script src="/pages/header/header.js"></script>
</header>