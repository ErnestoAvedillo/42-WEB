<?php
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camagru</title>
    <link rel="icon" href="/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/request_login.css">
</head>

<body>
    <?php
    //    include __DIR__ . '/../../views/debugger.php';
    $pageTitle = "Home - Camagru";
    include __DIR__ . '/../../views/header.php';
    $pageTitle = "sidebar - Camagru";
    include __DIR__ . '/../../views/side_bar.php';
    ?>
    <div class="request-login">
        <h1>Please Log In</h1>
        <p>To upload your photos, or see your gallery, please log in.</p>
        <form action="/pages/login/login.php" method="post" enctype="multipart/form-data">
            <button type="submit" class="btn btn-primary">Log In</button>
        </form>
    </div>
    <?php
    //    echo "<pre>";
    //    var_dump($_SESSION);
    //    echo "</pre>";
    include __DIR__ . '/../../views/footer.php';
    ?>
</body>

</html>