<?php
require_once __DIR__ . '/../../database/mongo_db.php';
require_once __DIR__ . '/../../database/User.php';
$user = new User();
$client = new DocumentDB('uploads');
?>
<header>
  <link rel="stylesheet" href="/pages/header/header.css">
  <div>
    <button id="toggleSidebarBtn">☰</button>
    <a href="/index.php"><img id="logo" src="/img/logo.png" alt="Logo" class="logo"></a>
  </div>
  <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
  <nav class="nav-links">
    <ul>
      <?php if ($isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in']) { ?>
        <div class="user-info" id="userInfo">
          <a href="/pages/logout/logout.php" class="logout-link"> <span class="icon">🚪</span></a>
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