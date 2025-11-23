<?php
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
$autofilling = '/tmp/Camagru.log';
// Log the incoming GET parameters for debugging
file_put_contents($autofilling, "Register ==> confirm.php - fromRegister: " . date('Y-m-d H:i:s') . " Validation token generated: " . print_r($_GET, true) . "\n", FILE_APPEND);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Registration</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/pages/register/confirm.css">
</head>
<?php file_put_contents($autofilling, "Register ==> confirm.php - fromRegister: " . date('Y-m-d H:i:s') . " Loaded header confirm.php page\n", FILE_APPEND); ?>

<body>

    <div class="confirm-registration">
        <?php $validationToken = $_GET['validation_token'] ?? ''; ?>
        <?php if (!empty($_GET['error'])): ?>
            <div class="error-message">
                <?php
                if (isset($_GET['error'])) {
                    echo "<p class='error'>Los tokens de validación no coinciden.</p>";
                    unset($_GET['error']);
                }
                ?>
            </div>
        <?php endif; ?>
        <?php file_put_contents($autofilling, "Register ==> confirm.php - fromRegister: " . date('Y-m-d H:i:s') . " Getting variables: " . json_encode($validationToken) . "\n", FILE_APPEND); ?>
        <form class="confirm-form" action="/pages/register/confirm_handler.php" method="post">
            <h1>Confirm Your Registration</h1>
            <input type="hidden" name="validation_token" value="<?php echo $validationToken; ?>">
            <input type="number" name="confirm_validation_token" placeholder="Enter your confirmation code" required>
            <button type="submit">Confirm Registration</button>
            <h1>Confirm Your Registration.</h1>
            <p>Please check your email for a confirmation code.</p>
            <p>If you haven't received it, please check your spam folder or <a href="/pages/register/register.php">try registering again</a>.</p>
        </form>
        <?php file_put_contents($autofilling, "Register ==> confirm.php - fromRegister: " . date('Y-m-d H:i:s') . " Loaded confirm form\n", FILE_APPEND); ?>
    </div>
</body>

</html>