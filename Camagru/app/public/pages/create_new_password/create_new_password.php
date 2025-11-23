$csrf_token = $_SESSION['csrf_token'] ?? null;
if (!$csrf_token) {
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;
}
<?php
require_once __DIR__ . '/../../database/User.php';
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/pages/create_new_password/create_new_password.css">
    <title>Create New Password</title>
</head>

<body>
    <?php
    $token = $_GET['token'] ?? '';
    $username = $_GET['username'] ?? '';
    $userInstance = new User();
    error_log("Verifying token for user: $username with token: $token");
    if (!$userInstance->isRecoveryTokenValid($username, $token)) {
        echo "<p>Invalid or expired token. Please request a new password recovery.</p>";
        exit;
    }
    $pageTitle = "Create New Password - Camagru";
    require_once __DIR__ . '/../../pages/header/header.php';
    require_once __DIR__ . '/../../pages/left_bar/left_bar.php';
    ?>
    <div class="new-password-container">
        <h2>Create a New Password</h2>
        <form action="create_new_password_handler.php" method="POST" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <label for="new-password">New Password:</label>
            <div class="password-input-container">
                <input type="password" id="new-password" name="new-password" required autocomplete="new-password">
                <button type="button" class="password-toggle" onclick="togglePassword('new-password')">
                    <span class="toggle-text" id="new-password-toggle-text">👁️</span>
                </button>
            </div>
            <div class="password-strength" id="passwordStrength"></div>
            <label for="confirm-password">Confirm Password:</label>
            <div class="password-input-container">
                <input type="password" id="confirm-password" name="confirm-password" required>
                <button type="button" class="password-toggle" onclick="togglePassword('confirm-password')">
                    <span class="toggle-text" id="confirm-password-toggle-text">👁️</span>
                </button>
            </div>
            <div class="password-match" id="passwordMatch"></div>
            <button type="submit">Change Password</button>
        </form>
    </div>
</body>
<script src="/pages/create_new_password/create_new_password.js"></script>

</html>