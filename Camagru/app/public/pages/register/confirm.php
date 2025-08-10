<?php
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Registration</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/confirm.css">
</head>

<body>

    <div class="confirm-registration">
        <?php $validationToken = $_SESSION['validation_token'] ?? ''; ?>
        <?php if (!empty($_GET['error'])): ?>
            <div class="error-message">
                <?php
                switch ($_GET['error']) {
                    case 'validation_token_required':
                        echo "<p class='error'>El token de validación es requerido.</p>";
                        break;
                    case 'validation_token_mismatch':
                        echo "<p class='error'>Los tokens de validación no coinciden.</p>";
                        break;
                }
                ?>
            </div>
        <?php endif; ?>
        <form class="confirm-form" action="/pages/register/confirm_handler.php" method="post">
            <h1>Confirm Your Registration</h1>
            <input type="hidden" name="validation_token" value="<?php echo htmlspecialchars($validationToken ?? ''); ?>">
            <input type="number" name="confirm_validation_token" placeholder="Enter your confirmation code" required>
            <button type="submit">Confirm Registration</button>
            <h1>Confirm Your Registration.</h1>
            <p>Please check your email for a confirmation code.</p>
            <p>If you haven't received it, please check your spam folder or <a href="/pages/register/register.php">try registering again</a>.</p>
        </form>
    </div>
</body>

</html>