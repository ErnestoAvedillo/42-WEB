<aside id="sidebar">
  <link rel="stylesheet" href="css/side_bar.css">
  <div class="sidebar-header">
    <h2>Menu</h2>
  </div>
  <nav class="sidebar-nav">
    <ul>
      <?php $isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
      if ($isLoggedIn) { ?>
        <li><a href="index.php?page=home" <?php echo (!isset($_GET['page']) || $_GET['page'] == 'home') ? 'class="active"' : ''; ?>><span class="icon">🏠</span>Home</a></li>
        <li><a href="index.php?page=gallery" <?php echo (isset($_GET['page']) && $_GET['page'] == 'gallery') ? 'class="active"' : ''; ?>><span class="icon">🖼️</span>Gallery</a></li>
        <li><a href="index.php?page=upload" <?php echo (isset($_GET['page']) && $_GET['page'] == 'upload') ? 'class="active"' : ''; ?>><span class="icon">📤</span>Upload</a></li>
        <li><a href="index.php?page=login" <?php echo (isset($_GET['page']) && $_GET['page'] == 'login') ? 'class="active"' : ''; ?>><span class="icon">🔐</span>Login</a></li>
        <li><a href="index.php?page=register" <?php echo (isset($_GET['page']) && $_GET['page'] == 'register') ? 'class="active"' : ''; ?>><span class="icon">📝</span>Register</a></li>
      <?php } else { ?>
        <li><a href="index.php?page=login" <?php echo (isset($_GET['page']) && $_GET['page'] == 'login') ? 'class="active"' : ''; ?>><span class="icon">🔐</span>Login</a></li>
        <li><a href="index.php?page=register" <?php echo (isset($_GET['page']) && $_GET['page'] == 'register') ? 'class="active"' : ''; ?>><span class="icon">📝</span>Register</a></li>
      <?php } ?>
    </ul>
  </nav>
</aside>
<script src="js/hide_bar.js"></script>