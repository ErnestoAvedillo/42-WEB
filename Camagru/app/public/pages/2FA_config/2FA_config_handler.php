<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../database/User.php';
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
if (!SessionManager::getSessionKey('uuid')) {
    header('Location: /pages/login/login.php');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? '';
    $secret = $_POST['2fa_secret'] ?? '';
    $input_code = $_POST['2fa_code'] ?? '';
} else {
    echo "Invalid request method.";
    http_response_code(405);
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>2FA Configuration</title>
</head>

<body>
    <h1>2FA Configuration</h1>
    <?php
    $website_name = 'Camagru';
    $user_email = SessionManager::getSessionKey('email');
    $username = SessionManager::getSessionKey('uuid');

    use OTPHP\TOTP;

    $totp = TOTP::create($secret); // Usa el secreto almacenado
    // Get the current server time

    echo "<p>Server time: " . date('Y-m-d H:i:s T') . "<br></p>";
    // Show the code PHP calculates
    echo "<p>Current TOTP code (server side): " . $totp->now() . "<br></p>";
    echo "<p>The code that you have introduced is: " . $input_code . "<br></p>";
    echo "<p>TOTP secret code: " . $totp->getSecret() . "<br></p>";

    // Verificar el código ingresado
    if ($totp->verify($input_code)) {
        // Código correcto, guardar el secreto en la base de datos y activar 2FA
        $user = new User();
        if ($user->activate2FA($user_id, $secret)) {
            SessionManager::getInstance()->setSessionKey('two_factor_enabled', true);

            file_put_contents("/tmp/debug.log", "2FA enabled for user: " . print_r($_SESSION, true) . "\n", FILE_APPEND);

            echo '<p style="color: green;">Two-Factor Authentication has been enabled successfully.</p>';
            echo '<a method="GET" href="/index.php">Back to main page</a>';
        } else {
            echo '<p style="color: red;">Error enabling Two-Factor Authentication. Please try again.</p>';
            echo '<a method="GET" href="/pages/2FA_config/2FA_config.php?secret=' . htmlspecialchars($secret) . '">Back to 2FA Configuration</a>';
        }
    } else {
        // Código incorrecto
        echo '<p style="color: red;">Invalid 2FA code "' . htmlspecialchars($input_code) . '". Please try again.</p>';
        echo '<a method="GET" href="/pages/2FA_config/2FA_config.php?secret=' . htmlspecialchars($secret) . '">Back to 2FA Configuration</a>';
    }
    ?>
</body>

</html>