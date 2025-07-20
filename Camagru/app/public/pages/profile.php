<div class="profile-container">
    <h1>Your Profile</h1>
    <p>Manage your profile settings and view your uploaded photos.</p>
    <form action="profile_handler.php" method="post">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="password">New Password:</label>
            <input type="password" id="password" name="password">
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" id="confirm_password" name="confirm_password">
        </div>
        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
    <p>Here you can also view your uploaded photos.</p>
    <div class="profile-photos">
        <!-- Profile photos will go here -->
        <p>Photos coming soon...</p>
    </div>
</div>