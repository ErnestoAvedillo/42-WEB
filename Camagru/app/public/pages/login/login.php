<?php
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
if (SessionManager::getSessionKey('uuid')) {
    // echo "<script>alert('You are already logged in.');</script>";
    if (isset($_GET['forward']) && !empty($_GET['forward'])) {
        $forward = $_GET['forward'];
        // Prevent open redirect vulnerabilities
        if (strpos($forward, '/') === 0 && strpos($forward, 'http') === false) {
            header('Location: ' . $forward);
            exit();
        }
    }
    header('Location: /index.php');
    exit();
}
$csrf_token = $_SESSION['csrf_token'] ?? null;
if (!$csrf_token) {
    $csrf_token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $csrf_token;
}
$autofilling = '/tmp/Camagru.log';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Camagru</title>
    <link rel="icon" href="/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/pages/login/login.css">
</head>

<body>
    <?php
    $pageTitle = "Home - Camagru";
    include __DIR__ . '/../../pages/header/header.php';

    $pageTitle = "sidebar - Camagru";
    include __DIR__ . '/../../pages/left_bar/left_bar.php';

    // Obtener errores y datos previos si existen
    $errors = $_SESSION['error_messages'] ?? [];
    $data = $_SESSION['login_data'] ?? [];
    $successMessage = $_SESSION['success_message'] ?? '';
    // Verificar si viene del registro
    $fromRegister = isset($_GET['registered']) && $_GET['registered'] == '1';
    // $registeredUser = $_SESSION['registered_user'] ?? '';
    // debugger
    file_put_contents($autofilling, "Login ==> login.php - " . $fromRegister . ": " . date('Y-m-d H:i:s') . "Errors: " . json_encode($errors) . "\n", FILE_APPEND);
    file_put_contents($autofilling, "Login ==> login.php - " . $fromRegister . ": " . date('Y-m-d H:i:s') . "Edit link: " . json_encode($data) . "\n", FILE_APPEND);
    file_put_contents($autofilling, "Login ==> login.php - " . $fromRegister . ": " . date('Y-m-d H:i:s') . "Success msg: " . json_encode($successMessage) . "\n", FILE_APPEND);
    // file_put_contents($autofilling, "Login ==> login.php - fromRegister: " . date('Y-m-d H:i:s') . "Registered user: " . $registeredUser . "\n", FILE_APPEND);
    // Limpiar mensajes después de mostrarlos
    unset($_SESSION['error_messages']);
    unset($_SESSION['login_data']);
    unset($_SESSION['success_message']);
    unset($_SESSION['registered_user']);
    ?>
    <div class="login-container">
        <h1>Login to Camagru</h1>
        <?php if (!empty($errors)): ?>
            <div class="alert-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($successMessage)): ?>
            <div class="alert-success">
                <?php echo htmlspecialchars(json_encode($successMessage)); ?>
            </div>
        <?php endif; ?>
        <p>Please enter your credentials to access your account.</p>
        <form action="/pages/login/login_handler.php" method="post" id="loginForm">
            <input type="hidden" name="forward" value="<?php echo isset($_GET['forward']) ? htmlspecialchars($_GET['forward']) : ''; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <div class="form-group">
                <label for="username">Username or Email: <span class="required">*</span></label>
                <input type="text" id="username" name="username" required
                    value="<?php echo htmlspecialchars($data['username'] ?? ''); ?>"
                    placeholder="Enter your username or email">
            </div>
            <div class="form-group">
                <label for="password">Password: <span class="required">*</span></label>
                <div class="password-input-container">
                    <input type="password" id="password" name="password" required placeholder="Enter your password">
                    <button type="button" class="password-toggle" onclick="togglePassword('password')">
                        <span class="toggle-text" id="password-toggle-text">👁️</span>
                    </button>
                </div>
                <div class="form-actions">
                    <a href="/pages/login/password_recover/recover.php" class="forgot-password">Forgot your password???</a>
                </div>
            </div>

            <div class="form-group checkbox-group">
                <label>
                    <input type="checkbox" id="remember" name="remember">
                    Remember me
                </label>
            </div>

            <button type="submit" class="btn btn-primary" id="submitBtn">
                <span class="btn-text">Login</span>
                <span class="btn-loading" style="display: none;">Logging in...</span>
            </button>
        </form>

        <p class="register-link">Don't have an account? <a href="/pages/register/register.php">Register here</a></p>
    </div>
    <?php
    $pageTitle = "left side bar - Camagru";
    include __DIR__ . '/../../pages/right_bar/right_bar.php';

    include __DIR__ . '/../../pages/footer/footer.php';
    ?>
</body>
<script src="../../pages/login/login.js"></script>

</html>