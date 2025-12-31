<?php
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();

// Log the incoming GET parameters for debugging
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

<body>

    <div class="confirm-registration">
        <?php $validationToken = $_SESSION['register_data']['token'] ?? ''; ?>
        <?php if (isset($_SESSION['error_messages'])): ?>
            <div class="error-message">
                <?php
                echo "<p class='error'>Los tokens de validación no coinciden.</p>";
                unset($_SESSION['error_messages']);
                ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error_messages'])): ?>
            <div class="error-message">
                <?php
                echo "<p class='error'>Los tokens de validación no coinciden.</p>";
                unset($_SESSION['error_messages']);
                ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['info_messages'])): ?>
            <div class="error-message info">
                <?php
                echo "<p class='info'>{$_SESSION['info_messages']}</p>";
                unset($_SESSION['info_messages']);
                ?>
            </div>
        <?php endif; ?>
        <form class="confirm-form" action="/pages/register/confirm_handler.php" method="post">
            <h1>Confirm Your Registration</h1>
            <input type="hidden" name="validation_token" value="<?php echo htmlspecialchars($validationToken); ?>">
            <input type="number" name="confirm_validation_token" placeholder="Enter your confirmation code" required>
            <button type="submit">Confirm Registration</button>
            <h1>Confirm Your Registration.</h1>
            <p>Please check your email for a confirmation code.</p>
            <p>If you haven't received it, please check your spam folder or <a href="/pages/register/resend_token.php">send code again</a>.</p>
        </form>
    </div>
</body>

</html>