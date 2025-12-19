<?php
require_once __DIR__ . '/../../database/User.php';
require_once __DIR__ . '/../../database/Profiles.php';
$user = new User();
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
$csrf_token = $_SESSION['csrf_token'] ?? null;
if (!$csrf_token) {
  $csrf_token = bin2hex(random_bytes(32));
  $_SESSION['csrf_token'] = $csrf_token;
}
$user_data = $user->getUserData(SessionManager::getSessionKey('id') ?? null);
$data = [
  'username' => $user_data['username'] ?? '',
  'email' => $user_data['email'] ?? '',
  'first_name' => $user_data['first_name'] ?? '',
  'last_name' => $user_data['last_name'] ?? ''
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Camagru</title>
  <link rel="icon" href="/img/favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/pages/change_mail/change_mail.css">
</head>

<body>
  <?php
  //include __DIR__ . '/../../views/debugger.php';
  $pageTitle = "Change email - Camagru";
  include __DIR__ . '/../../pages/header/header.php';

  $pageTitle = "sidebar - Camagru";
  include __DIR__ . '/../../pages/left_bar/left_bar.php';

  include __DIR__ . '/../../utils/wait/wait.php';
  $errors = $_SESSION['error_messages'] ?? [];
  unset($_SESSION['error_messages']);
  ?>
  <div class="register-container">
    <h1>Change register email</h1>
    <?php if (!empty($errors)) { ?>
      <?php file_put_contents("/tmp/Camagru.log", "Register ==> register.php - fromRegister: " . date('Y-m-d H:i:s') . " Showing errors: " . print_r($errors, true) . "\n", FILE_APPEND); ?>
      <div class="alert-error">
        <h2>Please fix the following errors:</h2>
        <ul>
          <?php foreach ($errors as $error): ?>
            <li><?php echo htmlspecialchars($error); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php } ?>
    <form id="register-form" action="/pages/change_mail/change_mail_handler.php">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
      <input type="hidden" name="current_email" value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>">
      <div class="form-group">
        <label for="email">Email: <span class="required">*</span></label>
        <input type="email" id="email" name="email" required
          value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>">
      </div>

      <button type="button" class="btn btn-primary" id="submitBtn">
        <span class="btn-text">Change Register using code number</span>
        <span class="btn-loading" style="display: none;">Registering...</span>
      </button>

    </form>
  </div>
  <?php
  $pageTitle = "footer - Camagru";
  include __DIR__ . '/../../pages/footer/footer.php';
  include __DIR__ . '/../../pages/right_bar/right_bar.php';
  ?>
</body>

</html>
<script src="/pages/change_mail/change_mail.js"></script>