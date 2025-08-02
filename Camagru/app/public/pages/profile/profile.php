<?php
require_once __DIR__ . '/../../database/User.php';
require_once __DIR__ . '/../../database/Profiles.php';
$user = new User();
$profile = new Profiles();
$user_data = $user->getUserData($_SESSION['user_id'] ?? null);
$profile_data = $profile->getProfileData($_SESSION['user_id'] ?? null);
?>
<link rel="stylesheet" href="/css/profile.css">
<div class="profile-container">
    <h1>Your Profile</h1>
    <p>Manage your profile settings and view your uploaded photos.</p>
    <?php foreach ($user_data as $key => $value): ?>
        <p><strong><?php echo htmlspecialchars(ucfirst($key)); ?>:</strong> <?php echo htmlspecialchars($value); ?></p>
    <?php endforeach; ?>
    <h2>Profile Information</h2>
    <?php if (!$profile_data): ?>
        <p>No profile information found. Please complete your profile.</p>
    <?php else: ?>
        <h3>Profile Details</h3>
        <?php foreach ($profile_data as $key => $value): ?>
            <p><strong><?php echo htmlspecialchars(ucfirst($key)); ?>:</strong> <?php echo htmlspecialchars($value); ?></p>
        <?php endforeach; ?>
    <?php endif; ?>
    <h2>Update Profile</h2>
    <form class="profile-update-form" action="profile_handler.php" method="post">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="password">New Password:</label>
            <input type="password" id="password" name="password">
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" id="confirm_password" name="confirm_password">
        </div>
        <div class="form-group">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($profile_data['first_name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($profile_data['last_name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="bio">Bio:</label>
            <textarea id="bio" name="bio"><?php echo htmlspecialchars($profile_data['bio'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label for="profile_picture">Profile Picture:</label>
            <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
        </div>
        <div class="form-group">
            <label for="privacy">Privacy Settings:</label>
            <select id="privacy" name="privacy">
                <option value="public" <?php echo $profile_data['privacy'] === 'public' ? 'selected' : ''; ?>>Public</option>
                <option value="private" <?php echo $profile_data['privacy'] === 'private' ? 'selected' : ''; ?>>Private</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
    <p>Here you can also view your uploaded photos.</p>
    <div class="profile-photos">
        <!-- Profile photos will go here -->
        <p>Photos coming soon...</p>
    </div>
</div>