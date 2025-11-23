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
    <link rel="stylesheet" href="/pages/profile/profile.css">
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
        <?php if (isset($_SESSION['errors'])) { ?>
            <div class="error-message">
                <h2>Please correct the following errors:</h2>
                <p><?php echo htmlspecialchars($_SESSION['errors']); ?></p>
            </div>
            <?php unset($_SESSION['errors']); ?>
        <?php } ?>
        <form class="profile-update-form" action="/pages/profile/profile_handler.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="send_notifications">Send Notifications:</label>
                <input type="checkbox" id="send_notifications" name="send_notifications" <?php echo $user_data['send_notifications'] ? 'checked' : ''; ?>>
            </div>
            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user_data['first_name']); ?>" required>
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user_data['last_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="national_id">National ID:</label>
                <input type="text" id="national_id" name="national_id" value="<?php echo htmlspecialchars($user_data['national_id_nr'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="nationality">Nationality:</label>
                <input type="text" id="nationality" name="nationality" value="<?php echo htmlspecialchars($user_data['nationality'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="birth_date">Birth Date:</label>
                <input type="date" id="birth_date" name="birth_date" value="<?php echo htmlspecialchars($user_data['date_of_birth'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" id="address" name="street" value="<?php echo htmlspecialchars($user_data['street'] ?? ''); ?>">
                <label for="city">City:</label>
                <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($user_data['city'] ?? ''); ?>">
                <label for="state">State:</label>
                <input type="text" id="state" name="state" value="<?php echo htmlspecialchars($user_data['state'] ?? ''); ?>">
                <label for="zip_code">Postal Code:</label>
                <input type="text" id="zip_code" name="zip_code" value="<?php echo htmlspecialchars($user_data['zip_code'] ?? ''); ?>">
                <label for="country">Country:</label>
                <input type="text" id="country" name="country" value="<?php echo htmlspecialchars($user_data['country'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="phone_number">Phone Number:</label>
                <input type="tel" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user_data['phone_number'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="profile_picture">Profile Picture:</label>
                <!-- ✅ Mostrar imagen actual si existe -->
                <?php if (!empty($user_data['profile_picture'])): ?>
                    <?php $imageDataUrl = 'data:image/png;base64,' . $user_data['profile_picture']; ?>
                    <div class="current-profile-picture">
                        <img src="<?php echo $imageDataUrl; ?>"
                            alt="Current Profile Picture"
                            style="max-width: 150px; max-height: 150px; border-radius: 50%; object-fit: cover; display: block; margin: 10px 0;">
                        <p><small>Current picture</small></p>
                    </div>
                <?php else: ?>
                    <div class="no-profile-picture">
                        <div style="width: 150px; height: 150px; background-color: #f0f0f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 10px 0;">
                            <span style="color: #666;">No image</span>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Input para subir nueva imagen -->
                <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
                <small>Choose a new image to replace current one</small>
            </div>
            <button type="submit" class="btn btn-primary">Update Profile</button>
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
    include __DIR__ . '/../../pages/footer/footer.php';
    ?>
</body>

</html>

<script src="../../js/visualize_picture.js"></script>