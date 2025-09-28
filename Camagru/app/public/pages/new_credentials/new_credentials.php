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
    <link rel="stylesheet" href="/css/profile.css">
</head>

<body>

    <?php
    $pageTitle = "Home - Camagru";
    include __DIR__ . '/../../pages/header/header.php';

    $pageTitle = "sidebar - Camagru";
    include __DIR__ . '/../../pages/left_bar/left_bar.php';

    $user = new User();
    $user_data = $user->getUserData(SessionManager::getSessionKey('id') ?? null);
    ?>
    <div class="profile-container">
        <h1>Your Profile</h1>
        <form class="profile-update-form" action="/pages/profile/profile_handler.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" readonly>
            </div>
            <button type="submit" class="btn btn-primary">Update Credentials</button>
        </form>
        <p>Here you can also view your uploaded photos.</p>
        <div class="profile-photos">
            <!-- Profile photos will go here -->
            <p>Photos coming soon...</p>
        </div>
    </div>
    <?php
    $pageTitle = "right side bar - Camagru";
    include __DIR__ . '/../../pages/right_bar/right_bar.php';

    $pageTitle = "footer - Camagru";
    include __DIR__ . '/../../views/footer.php';
    ?>
</body>

</html>

<script src="../../js/visualize_picture.js"></script>