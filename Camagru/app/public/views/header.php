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
    <div class="user-area">
      <div class="user-info">
        <div class="avatar">👤</div>
        <div class="user-details">
          <span class="username">Guest User</span>
          <span class="status">Online</span>
        </div>
      </div>
    </div>
  <?php } ?>
</header>