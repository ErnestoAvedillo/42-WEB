<?php
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Camagru</title>
  <link rel="icon" href="/img/favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/pages/register/register.css">
</head>

<body>
  <?php
  //include __DIR__ . '/../../views/debugger.php';
  $pageTitle = "Home - Camagru";
  include __DIR__ . '/../../pages/header/header.php';

  $pageTitle = "sidebar - Camagru";
  include __DIR__ . '/../../pages/left_bar/left_bar.php';

  include __DIR__ . '/../../utils/wait/wait.php';

  // Obtener errores y datos previos si existen
  $errors = $_SESSION['error_messages'] ?? [];
  $data = $_SESSION['register_data'] ?? [];
  $successMessage = $_SESSION['success_message'] ?? '';

  // Limpiar mensajes después de mostrarlos
  unset($_SESSION['error_messages']);
  unset($_SESSION['register_data']);
  unset($_SESSION['success_message']);
  ?>

  <div class="register-container">
    <h1>Register for Camagru</h1>
    <?php file_put_contents("/tmp/Camagru.log", "Register ==> register.php - fromRegister: " . date('Y-m-d H:i:s') . " Errors: " . print_r($errors, true) . " Data: " . print_r($data, true) . "\n", FILE_APPEND); ?>
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
    <form id="register-form" action="/pages/register/register_handler.php">
      <div class="form-row">
        <div class="form-group">
          <label for="first_name">First Name:</label>
          <input type="text" id="first_name" name="first_name"
            value="<?php echo htmlspecialchars($data['first_name'] ?? ''); ?>">
        </div>
        <div class="form-group">
          <label for="last_name">Last Name:</label>
          <input type="text" id="last_name" name="last_name"
            value="<?php echo htmlspecialchars($data['last_name'] ?? ''); ?>">
        </div>
      </div>

      <div class="form-group">
        <label for="username">Username: <span class="required">*</span></label>
        <input type="text" id="username" name="username" required
          value="<?php echo htmlspecialchars($data['username'] ?? ''); ?>"
          minlength="3" maxlength="50">
        <small class="help-text">3-50 characters, letters, numbers and underscores only</small>
      </div>

      <div class="form-group">
        <label for="email">Email: <span class="required">*</span></label>
        <input type="email" id="email" name="email" required
          value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>">
      </div>

      <div class="form-group">
        <label for="password">Password: <span class="required">*</span></label>
        <div class="password-input-container">
          <input type="password" id="password" name="password" required minlength="8">
          <button type="button" class="password-toggle" onclick="togglePassword('password')">
            <span class="toggle-text" id="password-toggle-text">👁️</span>
          </button>
        </div>
        <small class="help-text">At least 8 characters with uppercase, lowercase, and numbers</small>
        <div class="password-strength" id="passwordStrength"></div>
      </div>

      <div class="form-group">
        <label for="confirm_password">Confirm Password: <span class="required">*</span></label>
        <div class="password-input-container">
          <input type="password" id="confirm_password" name="confirm_password" required>
          <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
            <span class="toggle-text" id="confirm_password-toggle-text">👁️</span>
          </button>
        </div>
        <div class="password-match" id="passwordMatch"></div>
      </div>

      <div class="form-group checkbox-group">
        <label>
          <input type="checkbox" id="terms" name="terms" required>
          I agree to the <a href="/pages/terms&cond/terms-and-cond.php" target="_blank">Terms of Service</a> and <a href="#" target="_blank">Privacy Policy</a>
        </label>
      </div>

      <button type="button" class="btn btn-primary" id="submitBtn">
        <span class="btn-text">Register using code number</span>
        <span class="btn-loading" style="display: none;">Registering...</span>
      </button>
      <button type="button" class="btn btn-secondary" id="secondaryBtn">
        <span class="btn2-text">Register using link</span>
        <span class="btn2-loading" style="display: none;">Waiting for confirmation...</span>
      </button>
    </form>

    <p class="login-link">Already have an account? <a href="/pages/login/login.php">Login here</a></p>
  </div>
  <?php
  $pageTitle = "footer - Camagru";
  include __DIR__ . '/../../views/footer.php';
  ?>
</body>

</html>
<script src="/pages/register/register.js"></script>