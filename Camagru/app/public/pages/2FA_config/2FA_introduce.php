<?php
require_once __DIR__ . '/../../database/User.php';
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
if (!SessionManager::getSessionKey('temp_user')) {
    error_log("No temp_user in session, redirecting to login.");
    if (!isset($_GET['forward']) || empty($_GET['forward'])) {
        header('Location: /pages/login/login.php');
    } else {
        header('Location: /pages/login/login.php?forward=' . urlencode($_GET['forward']));
    }
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
</head>

<body>
    <?php
    $pageTitle = "Two-Factor Authentication - Camagru";
    include __DIR__ . '/../../pages/header/header.php';
    include __DIR__ . '/../../pages/left_bar/left_bar.php';
    ?>
    <div class="2FA-container">
        <h2>Two-Factor Authentication</h2>
        <p>Please enter the authentication code from your authenticator app.</p>
        <form action="/pages/2FA_config/2FA_verify.php?forward=<?php echo urlencode($_GET['forward'] ?? ''); ?>" method="POST">
            <div class="form-group">
                <label for="token">Authentication Code:</label>
                <input type="text" id="token" name="token" required pattern="\d{6}" title="Enter the 6-digit code from your authenticator app">
                <input type="hidden" name="user" value="<?php echo htmlspecialchars($temp_user['uuid']); ?>">
            </div>
            <button type="submit">Verify</button>
        </form>
        <button type="button" onclick="window.location.href='/pages/login/login.php'">Back to Login</button>
    </div>
    <?php
    include __DIR__ . '/../../pages/right_bar/right_bar.php';
    include __DIR__ . '/../../views/footer.php';
    ?>
</body>

</html>

Warning
: session_start(): Session cannot be started after headers have already been sent in
/var/www/html/class_session/class_session.php
on line
24