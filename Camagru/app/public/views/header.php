<header>
  <link rel="stylesheet" href="css/header.css">
  <button id="toggleSidebarBtn">☰</button>
  <h1>Camagru</h1>
  <nav>
    <ul>
      <li><a href="index.php?page=login" <?php echo (isset($_GET['page']) && $_GET['page'] == 'login') ? 'class="active"' : ''; ?>>Login</a></li>
      <li><a href="index.php?page=register" <?php echo (isset($_GET['page']) && $_GET['page'] == 'register') ? 'class="active"' : ''; ?>>Register</a></li>
    </ul>
  </nav>
  <div class="user-area">
    <div class="user-info">
      <div class="avatar">👤</div>
      <div class="user-details">
        <span class="username">Guest User</span>
        <span class="status">Online</span>
      </div>
    </div>
  </div>
</header>