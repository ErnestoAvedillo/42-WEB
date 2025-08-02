<header>
  <link rel="stylesheet" href="css/header.css">
  <button id="toggleSidebarBtn">☰</button>
  <h1>Camagru</h1>
  <nav>
    <ul>
      <?php if ($isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in']) { ?>
        <li><a href="index.php?page=upload" <?php echo (isset($_GET['page']) && $_GET['page'] == 'upload') ? 'class="active"' : ''; ?>>Upload</a></li>
        <li><a href="../pages/logout/logout.php">Logout</a></li>
        <li><a href="index.php?page=profile" <?php echo (isset($_GET['page']) && $_GET['page'] == 'profile') ? 'class="active"' : ''; ?>>Profile</a></li>
      <?php } else { ?>
        <li><a href="index.php?page=login" <?php echo (isset($_GET['page']) && $_GET['page'] == 'login') ? 'class="active"' : ''; ?>>Login</a></li>
        <li><a href="index.php?page=register" <?php echo (isset($_GET['page']) && $_GET['page'] == 'register') ? 'class="active"' : ''; ?>>Register</a></li>
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