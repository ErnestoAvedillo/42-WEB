<aside id="left">
  <link rel="stylesheet" href="/pages/left_bar/left_bar.css">
  <div class="left-bar-header">
    <h2>Menu</h2>
  </div>
  <nav class="left-bar-nav">
    <ul>
      <?php $isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
      if ($isLoggedIn) { ?>
        <li><a href="/index.php" <?php echo (!isset($_GET['page']) || $_GET['page'] == 'home') ? 'class="active"' : ''; ?>><span class="icon">🏠</span>Home</a></li>
        <li><a href="/pages/upload/upload.php" <?php echo (basename($_SERVER['SCRIPT_NAME']) == 'upload.php') ? 'class="active"' : ''; ?>><span class="icon">📤</span>Upload</a></li>
        <li><a href="/pages/logout/logout.php" <?php echo (basename($_SERVER['SCRIPT_NAME']) == 'logout.php') ? 'class="active"' : ''; ?>><span class="icon">🚪</span>Logout</a></li>
        <li><a href="/pages/gallery/gallery.php" <?php echo (basename($_SERVER['SCRIPT_NAME']) == 'gallery.php') ? 'class="active"' : ''; ?>><span class="icon">🖼️</span>Gallery</a></li>
        <li><a href="/pages/facturas/factura.php" <?php echo (basename($_SERVER['SCRIPT_NAME']) == 'factura.php') ? 'class="active"' : ''; ?>><span class="icon">🧾</span>Start a factura</a></li>
        <li><a href="/pages/demandas/demandas.php" <?php echo (basename($_SERVER['SCRIPT_NAME']) == 'demandas.php') ? 'class="active"' : ''; ?>><span class="icon">⚖️</span>Start a demanda</a></li>
      <?php } else { ?>
        <li><a href="/pages/login/login.php" <?php echo (basename($_SERVER['SCRIPT_NAME']) == 'login.php') ? 'class="active"' : ''; ?>><span class="icon">🔑</span>Login</a></li>
        <li><a href="/pages/register/register.php" <?php echo (basename($_SERVER['SCRIPT_NAME']) == 'register.php') ? 'class="active"' : ''; ?>><span class="icon">📝</span>Register</a></li>
      <?php } ?>
    </ul>
  </nav>
</aside>
<script src="/pages/left_bar/hide_left_bar.js"></script>