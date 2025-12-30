<aside id="left">
  <link rel="stylesheet" href="/pages/left_bar/left_bar.css">
  <div class="left-bar-header">
    <h2>Menu</h2>
  </div>
  <nav class="left-bar-nav">
    <ul>
      <?php $isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
      if ($isLoggedIn) { ?>
        <li><a href="/index.php" <?php echo (basename($_SERVER['SCRIPT_NAME']) == 'index.php') ? 'class="active"' : ''; ?>><span class="icon">🏠</span>Home</a></li>
        <li><a href="/pages/upload/upload.php?type=photo" <?php echo (basename($_SERVER['SCRIPT_NAME']) == 'upload.php' && $_GET['type'] == 'photo') ? 'class="active"' : ''; ?>><span class="icon">📷</span>Upload pictures</a></li>
        <li><a href="/pages/upload/upload.php?type=master" <?php echo (basename($_SERVER['SCRIPT_NAME']) == 'upload.php' && $_GET['type'] == 'master') ? 'class="active"' : ''; ?>><span class="icon">🪟</span>Upload masters</a></li>
        <li><a href="/pages/combine/combine.php" <?php echo (basename($_SERVER['SCRIPT_NAME']) == 'combine.php') ? 'class="active"' : ''; ?>><span class="icon">🌁</span>Combine</a></li>
        <li><a href="/pages/gallery/gallery.php" <?php echo (basename($_SERVER['SCRIPT_NAME']) == 'gallery.php') ? 'class="active"' : ''; ?>><span class="icon">🖼️</span>Gallery</a></li>
      <?php } else { ?>
        <li><a href="/pages/login/login.php" <?php echo (basename($_SERVER['SCRIPT_NAME']) == 'login.php') ? 'class="active"' : ''; ?>><span class="icon">🔑</span>Login</a></li>
        <li><a href="/pages/register/register.php" <?php echo (basename($_SERVER['SCRIPT_NAME']) == 'register.php') ? 'class="active"' : ''; ?>><span class="icon">📝</span>Register</a></li>
      <?php } ?>
    </ul>
  </nav>
</aside>
<script src="/pages/left_bar/hide_left_bar.js"></script>