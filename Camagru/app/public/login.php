<?php 
$pageTitle = "Login - Camagru";
include __DIR__ . '/../views/header.php'; 
?>

<main id="mainContent">
    <h1>Login</h1>
    <form class="login-form">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Login</button>
    </form>
    <p><a href="register.php">Don't have an account? Register here</a></p>
</main>

<?php include __DIR__ . '/../views/footer.php'; ?>
