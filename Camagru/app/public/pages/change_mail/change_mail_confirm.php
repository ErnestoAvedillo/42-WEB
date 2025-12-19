<?php
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
$autofilling = '/tmp/confirm.log';
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
<?php
  //include __DIR__ . '/../../views/debugger.php';
  $pageTitle = "Change email confirmation - Camagru";
  include __DIR__ . '/../../pages/header/header.php';

  $pageTitle = "sidebar - Camagru";
  include __DIR__ . '/../../pages/left_bar/left_bar.php';
  ?>    

    <div class="confirm-registration">
        <?php 
        // Obtener datos de la sesión en lugar de parámetros URL
        $changeMailData = $_SESSION['change_mail_data'] ?? null;
        if (!$changeMailData) {
            // Si no hay datos en la sesión, redirigir al formulario de cambio de email
            header('Location: /pages/change_mail/change_mail.php');
            exit();
        }
        
        $validationToken = $changeMailData['validation_token'] ?? '';
        $old_mail = $changeMailData['old_email'] ?? '';
        $new_mail = $changeMailData['new_email'] ?? '';
        
        file_put_contents($autofilling, "Register ==> confirm.php - fromRegister: " . date('Y-m-d H:i:s') . " Getting variables from session: " . json_encode($changeMailData) . "\n", FILE_APPEND);
        ?>
        <?php if (!empty($_SESSION['error_messages'])): ?>
            <div class="error-message">
                <?php
                foreach ($_SESSION['error_messages'] as $error) {
                    echo "<p class='error'>" . htmlspecialchars($error) . "</p>";
                }
                unset($_SESSION['error_messages']);
                ?>
            </div>
        <?php endif; ?>
        <?php file_put_contents($autofilling, "Register ==> confirm.php - fromRegister: " . date('Y-m-d H:i:s') . " Getting variables: " . json_encode($validationToken) . "\n", FILE_APPEND); ?>
        <form class="confirm-form" action="/pages/change_mail/change_mail_confirm_handler.php" method="post">
            <h1>Confirm your email change</h1>
            <input type="hidden" name="old_email" value="<?php echo $old_mail; ?>">
            <input type="hidden" name="new_email" value="<?php echo $new_mail; ?>">
            <input type="hidden" name="validation_token" value="<?php echo $validationToken; ?>">
            <input type="number" name="confirm_validation_token" placeholder="Enter your confirmation code" required>
            <button type="submit">Confirm change mail</button>
            <p>Please check your email for a confirmation code.</p>
        </form>
        <?php file_put_contents($autofilling, "Register ==> confirm.php - fromRegister: " . date('Y-m-d H:i:s') . " Loaded confirm form\n", FILE_APPEND); ?>
    </div>
    <?php
  $pageTitle = "footer - Camagru";
  include __DIR__ . '/../../pages/footer/footer.php';
  include __DIR__ . '/../../pages/right_bar/right_bar.php';
  ?>
</body>

</html>