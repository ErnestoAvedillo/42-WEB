<header>
  <button id="toggleSidebarBtn">☰</button>
  <h1>Camagru</h1>
  <nav>
    <ul>
      <li><a href="index.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'class="active"' : ''; ?>>Home</a></li>
      <li><a href="gallery.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'gallery.php') ? 'class="active"' : ''; ?>>Gallery</a></li>
      <li><a href="login.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'login.php') ? 'class="active"' : ''; ?>>Login</a></li>
      <li><a href="register.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'register.php') ? 'class="active"' : ''; ?>>Register</a></li>
    </ul>
  </nav>
</header>