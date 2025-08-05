<?php
require_once __DIR__ . '/../../database/User.php';
require_once __DIR__ . '/../../database/Profiles.php';
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Camagru</title>
    <link rel="icon" href="/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/upload.css">
</head>

<body>

    <?php
    $pageTitle = "Home - Camagru";
    include __DIR__ . '/../../views/header.php';

    $pageTitle = "sidebar - Camagru";
    include __DIR__ . '/../../views/side_bar.php';

    $user = new User();
    $profile = new Profiles();
    $user_data = $user->getUserData(SessionManager::getSessionKey('id') ?? null);
    $profile_data = $profile->getProfileData(SessionManager::getSessionKey('uuid') ?? null);
    ?>

    <div class="uopload-container">
        <h1>Upload Your Photos</h1>
        <p>Share your creativity with the world by uploading your photos.</p>
        <form action="upload_handler.php" method="post" enctype="multipart/form-data">
            <input type="file" name="photo" accept="image/*" required>
            <button type="submit" class="btn btn-primary">Upload Photo</button>
        </form>
        <p>Supported formats: JPG, PNG, GIF</p>
    </div>
    <?php
    include __DIR__ . '/../../views/footer.php';
    ?>
</body>

</html>