<?php
require_once   __DIR__ . '/../../database/User.php';
require_once   __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
$csrf_token = $_SESSION['csrf_token'] ?? null;
if (!$csrf_token) {
  $csrf_token = bin2hex(random_bytes(32));
  $_SESSION['csrf_token'] = $csrf_token;
}
if (!SessionManager::getSessionKey('uuid')) {
  // echo "<script>alert('You must be logged in to access this page.');</script>";
  header('Location: /pages/login/login.php');
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Document</title>
  <link rel="icon" type="image/x-icon" href="/img/favicon.ico">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/2FA_config/2FA_config.css">
</head>

<body>
  <?php
  if (isset($_GET['secret'])) {
    $secret = $_GET['secret'];
  }
  $pageTitle = "Make your own collages - Camagru";
  include __DIR__ . '/../../pages/header/header.php';
  include __DIR__ . '/../../pages/left_bar/left_bar.php';
  ?>
  <div class="2FA-container">
    <?php

    if (SessionManager::getSessionKey('two_factor_enabled') === null || !SessionManager::getSessionKey('two_factor_enabled')) {
      echo "<p>2FA is not enabled on your account. You cannot disable it.</p>";
      echo "<button onclick=\"location.href='index.php'\">Go back to Profile</button>";
      exit();
    } else { ?>
      <h1> Disable 2FA </h1>
      <p>Two-Factor Authentication (2FA) adds an extra layer of security to your account. </p>
      <p>By disabling 2FA, your account will rely solely on your password for authentication.</p>
      <p>This may make your account more vulnerable to unauthorized access.</p>
      <p>If you are sure you want to disable 2FA, please enter the 6-digit code from your Authenticator app below to confirm your decision.</p>
      <p>If you did not intend to disable 2FA, you can simply navigate away from this page.</p>
      <hr>
      <hr>
      <div>
        <form action="/pages/2FA_config/2FA_verify_disable.php" method="POST">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
          <div class="form-group">
            <label for="token">Authentication Code:</label>
            <input type="text" id="token" name="token" required pattern="\d{6}" title="Enter the 6-digit code from your authenticator app">
            <input type="hidden" name="user" value="<?php echo SessionManager::getSessionKey('uuid'); ?>">
          </div>
          <button type="submit">Disable</button>
        </form>
        <button type="button" onclick="window.location.href='index.php'">Back to Main</button>

      <?php
    }
      ?>
      </div>
      <?php
      include __DIR__ . '/../../pages/right_bar/right_bar.php';
      include __DIR__ . '/../../pages/footer/footer.php';
      ?>
  </div>
</body>

</html>