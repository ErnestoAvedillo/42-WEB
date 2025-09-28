<?php
require_once __DIR__ . '/../../class_session/session.php';
?>
<aside id="right">
  <link rel="stylesheet" href="/pages/right_bar/right_bar.css">
  <div class="right-bar-header">
    <h2>User Menu</h2>
  </div>
  <nav class="right-bar-nav">
    <ul class="user-nav-right" id="userNavRight">
      <li><a href="/pages/logout/logout.php" <?php echo (basename($_SERVER['SCRIPT_NAME']) == 'logout.php') ? 'class="active"' : ''; ?>><span class="icon">🚪</span>Logout</a></li>
      <li><a href="/pages/profile/profile.php" <?php echo (basename($_SERVER['SCRIPT_NAME']) == 'profile.php') ? 'class="active"' : ''; ?>><span class="icon">👤</span>Profile</a></li>
      <li><a href="/pages/create_new_password/new_password_request.php" <?php echo (basename($_SERVER['SCRIPT_NAME']) == 'new_password_request.php') ? 'class="active"' : ''; ?>><span class="icon">🔑</span>Change password</a></li>
      <?php
      $is2FAEnabled = SessionManager::getSessionKey('two_factor_enabled');
      file_put_contents('/tmp/debug.log', "Current 2FA status: " . var_export($is2FAEnabled, true) . "\n", FILE_APPEND);
      if (!$is2FAEnabled) { ?>
        <li><a href="/pages/2FA_config/2FA_config.php" <?php echo (basename($_SERVER['SCRIPT_NAME']) == '2FA_config.php') ? 'class="active"' : ''; ?>><span class="icon">🔒</span>Enable 2FA</a></li>
      <?php } else { ?>
        <li><a href="/pages/2FA_config/2FA_disable.php" <?php echo (basename($_SERVER['SCRIPT_NAME']) == '2FA_enable.php') ? 'class="active"' : ''; ?>><span class="icon">🔒</span>Disable 2FA</a></li>
      <?php } ?>
    </ul>
  </nav>
</aside>
<?php
foreach ($_SESSION as $key => $value) {
  file_put_contents('/tmp/debug.log', "Session Key: $key => Value: " . var_export($value, true) . " Get en Session:" . var_export(SessionManager::getSessionKey($key), true) . "\n", FILE_APPEND);
}
// Debugging line to check current 2FA status
?>
<script src="/pages/right_bar/hide_right_bar.js"></script>