<aside id="right">
  <link rel="stylesheet" href="/pages/right_bar/right_bar.css">
  <div class="right-bar-header">
    <h2>User Menu</h2>
  </div>
  <nav class="right-bar-nav">
    <ul class="user-nav-right" id="userNavRight">
      <li><a href="/pages/logout/logout.php" <?php echo (basename($_SERVER['SCRIPT_NAME']) == 'logout.php') ? 'class="active"' : ''; ?>><span class="icon">🚪</span>Logout</a></li>
      <li><a href="/pages/profile/profile.php" <?php echo (basename($_SERVER['SCRIPT_NAME']) == 'profile.php') ? 'class="active"' : ''; ?>><span class="icon">👤</span>Profile</a></li>
    </ul>
  </nav>
</aside>
<script src="/pages/right_bar/hide_right_bar.js"></script>