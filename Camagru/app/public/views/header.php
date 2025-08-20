<?php
require_once __DIR__ . '/../class_session/session.php';
SessionManager::getInstance();
require_once __DIR__ . '/../database/mongo_db.php';
require_once __DIR__ . '/../database/User.php';
$user = new User();
$picture_uuid =
  $client = new DocumentDB();
?>
<header>
  <link rel="stylesheet" href="/css/header.css">
  <button id="toggleSidebarBtn">☰</button>
  <h1>Camagru</h1>
  <nav>
    <ul>
      <?php if ($isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in']) { ?>
        <li><a href="/pages/upload/upload.php" <?php echo (isset($_GET['page']) && $_GET['page'] == 'upload') ? 'class="active"' : ''; ?>>Upload</a></li>
        <li><a href="/pages/logout/logout.php" <?php echo (basename($_SERVER['SCRIPT_NAME']) == 'logout.php') ? 'class="active"' : ''; ?>>Logout</a></li>
        <li><a href="/pages/profile/profile.php" <?php echo (basename($_SERVER['SCRIPT_NAME']) == 'profile.php') ? 'class="active"' : ''; ?>>Profile</a></li>
      <?php } else { ?>
        <li><a href="/pages/login/login.php" <?php echo (basename($_SERVER['SCRIPT_NAME']) == 'login.php') ? 'class="active"' : ''; ?>>Login</a></li>
        <li><a href="/pages/register/register.php" <?php echo (basename($_SERVER['SCRIPT_NAME']) == 'register.php') ? 'class="active"' : ''; ?>>Register</a></li>
      <?php } ?>
    </ul>
  </nav>
  <?php if ($isLoggedIn) { ?>
    <div class="user-area" if="userArea>
      <div class=" user-info">
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
    </div>
    </div>
  <?php } ?>
</header>