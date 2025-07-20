<?php
// Obtener errores y datos previos si existen
$errors = $_SESSION['login_errors'] ?? [];
$data = $_SESSION['login_data'] ?? [];
$successMessage = $_SESSION['success_message'] ?? '';

// Verificar si viene del registro
$fromRegister = isset($_GET['registered']) && $_GET['registered'] == '1';
$registeredUser = $_SESSION['registered_user'] ?? '';

// Limpiar mensajes después de mostrarlos
unset($_SESSION['login_errors']);
unset($_SESSION['login_data']);
unset($_SESSION['success_message']);
unset($_SESSION['registered_user']);
?>

<div class="login-container">
    <h1>Login to Camagru</h1>

    <?php if ($fromRegister): ?>
        <div class="alert alert-success">
            Registration successful! <?php if ($registeredUser): ?>Welcome <?php echo htmlspecialchars($registeredUser); ?>!<?php endif; ?> Please login with your credentials.
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($successMessage)): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($successMessage); ?>
        </div>
    <?php endif; ?>

    <form action="login_handler.php" method="post" id="loginForm">
        <div class="form-group">
            <label for="username">Username or Email: <span class="required">*</span></label>
            <input type="text" id="username" name="username" required
                value="<?php echo htmlspecialchars($data['username'] ?? ''); ?>"
                placeholder="Enter your username or email">
        </div>
        <div class="form-group">
            <label for="password">Password: <span class="required">*</span></label>
            <input type="password" id="password" name="password" required
                placeholder="Enter your password">
            <div class="form-actions">
                <a href="#" class="forgot-password">Forgot your password?</a>
            </div>
        </div>

        <div class="form-group checkbox-group">
            <label>
                <input type="checkbox" id="remember" name="remember">
                Remember me
            </label>
        </div>

        <button type="submit" class="btn btn-primary" id="submitBtn" onclick="window.location.href='index.php?page=gallery'; return false;">
            <span class="btn-text">Login</span>
            <span class="btn-loading" style="display: none;">Logging in...</span>
        </button>
    </form>

    <p class="register-link">Don't have an account? <a href="index.php?page=register">Register here</a></p>
</div>

<link rel="stylesheet" href="css/login.css">

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('loginForm');

        form.addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;

            if (!username || !password) {
                e.preventDefault();
                alert('Please fill in all required fields');
                return;
            }

            // Mostrar loading
            const btnText = document.querySelector('.btn-text');
            const btnLoading = document.querySelector('.btn-loading');
            const submitBtn = document.getElementById('submitBtn');

            btnText.style.display = 'none';
            btnLoading.style.display = 'inline';
            submitBtn.disabled = true;
        });
    });
</script>n-container">