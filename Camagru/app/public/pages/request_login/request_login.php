<?php
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
$csrf_token = $_SESSION['csrf_token'] ?? null;
if (!$csrf_token) {
    $csrf_token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $csrf_token;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camagru</title>
    <link rel="icon" href="/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/pages/request_login/request_login.css">
</head>

<body>
    <?php
    //    include __DIR__ . '/../../views/debugger.php';
    $pageTitle = "Home - Camagru";
    include __DIR__ . '/../../pages/header/header.php';
    $pageTitle = "sidebar - Camagru";
    include __DIR__ . '/../../pages/left_bar/left_bar.php';
    ?>
    <div class="request-login">
        <h1>Please Log In</h1>
        <p>To upload your photos, or see your gallery, please log in.</p>
        <form action="/pages/login/login.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <button type="submit" class="btn btn-primary">Log In</button>
        </form>
    </div>
    <?php
    //    echo "<pre>";
    //    var_dump($_SESSION);
    //    echo "</pre>";
    $pageTitle = "right side bar - Camagru";
    include __DIR__ . '/../../pages/right_bar/right_bar.php';

    $pageTitle = "footer - Camagru";
    include __DIR__ . '/../../pages/footer/footer.php';
    ?>
</body>

</html>