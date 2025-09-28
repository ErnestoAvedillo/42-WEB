<?php
require_once __DIR__ . '/../../../class_session/session.php';
SessionManager::getInstance();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/pages/login/password_recover/recover_success.css">
    <title>Password Recovery Success</title>
</head>

<body>
    <?php
    $pageTitle = "Password Recovery - Camagru";
    require_once __DIR__ . '/../../../pages/header/header.php';
    require_once __DIR__ . '/../../../pages/left_bar/left_bar.php';
    ?>
    <div class="recover-container">
        <h2>Password Recovery Email Sent</h2>
        <h2>Check your mail</h2>
        <p>If the email you provided is associated with an account, a password recovery link has been sent to it. <br></p>
        <p>Please check your inbox and follow the instructions to reset your password.</p>
    </div>
</body>

</html>