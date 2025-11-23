<?php
require_once __DIR__ . '/../../../class_session/session.php';
SessionManager::getInstance();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Recovery - Camagru</title>
    <link rel="icon" href="/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/pages/login/password_recover/recover.css">
</head>

<body>
    <?php
    $pageTitle = "Recover password - Camagru";
    include __DIR__ . '/../../../pages/header/header.php';
    include __DIR__ . '/../../../pages/left_bar/left_bar.php';
    ?>
    <div class="recover-container">
        <h1>Password Recovery</h1>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<div class="alert-error">' . htmlspecialchars($_SESSION['error']) . '</div>';
            unset($_SESSION['error']);
        }
        ?>
        <form action="recover_handler.php" method="POST">
            <div class="form-group">
                <label for="email">Enter your email address:</label>
                <input type="email" id="email" name="email" required placeholder="Enter your email">
            </div>
            <button type="submit" class="btn">Send Recovery Link</button>
        </form>
        <p class="note">A recovery link will be sent to your email if it is registered.</p>
    </div>
    <?php
    $pageTitle = "footer - Camagru";
    include __DIR__ . '/../../../pages/footer/footer.php';
    ?>
</body>
<script src="/pages/login/password_recover/recover.js"></script>

</html>