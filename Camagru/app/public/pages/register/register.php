<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Camagru</title>
    <link rel="icon" href="/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/register.css">
</head>

<body>
    <?php
    $pageTitle = "Home - Camagru";
    include __DIR__ . '/../../views/header.php';

    $pageTitle = "sidebar - Camagru";
    include __DIR__ . '/../../views/side_bar.php';

    // Obtener errores y datos previos si existen
    $errors = $_SESSION['register_errors'] ?? [];
    $data = $_SESSION['register_data'] ?? [];
    $successMessage = $_SESSION['success_message'] ?? '';

    // Limpiar mensajes después de mostrarlos
    unset($_SESSION['register_errors']);
    unset($_SESSION['register_data']);
    unset($_SESSION['success_message']);
    ?>

    <div class="register-container">
        <h1>Register for Camagru</h1>
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

        <form action="/pages/register/register_handler.php" method="post" id="registerForm">
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name"
                        value="<?php echo htmlspecialchars($data['first_name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name"
                        value="<?php echo htmlspecialchars($data['last_name'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="username">Username: <span class="required">*</span></label>
                <input type="text" id="username" name="username" required
                    value="<?php echo htmlspecialchars($data['username'] ?? ''); ?>"
                    minlength="3" maxlength="50">
                <small class="help-text">3-50 characters, letters, numbers and underscores only</small>
            </div>

            <div class="form-group">
                <label for="email">Email: <span class="required">*</span></label>
                <input type="email" id="email" name="email" required
                    value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="password">Password: <span class="required">*</span></label>
                <input type="password" id="password" name="password" required minlength="8">
                <small class="help-text">At least 8 characters with uppercase, lowercase, and numbers</small>
                <div class="password-strength" id="passwordStrength"></div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password: <span class="required">*</span></label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <div class="password-match" id="passwordMatch"></div>
            </div>

            <div class="form-group checkbox-group">
                <label>
                    <input type="checkbox" id="terms" name="terms" required>
                    I agree to the <a href="/pages/terms&cond/terms-and-cond.php" target="_blank">Terms of Service</a> and <a href="#" target="_blank">Privacy Policy</a>
                </label>
            </div>

            <button type="submit" class="btn btn-primary" id="submitBtn">
                <span class="btn-text">Register</span>
                <span class="btn-loading" style="display: none;">Registering...</span>
            </button>
        </form>

        <p class="login-link">Already have an account? <a href="main.php?page=login">Login here</a></p>
    </div>
    <?php
    include __DIR__ . '/../../views/footer.php';
    ?>
</body>

</html>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const passwordStrength = document.getElementById('passwordStrength');
        const passwordMatch = document.getElementById('passwordMatch');
        const form = document.getElementById('registerForm');

        // Verificar fortaleza de contraseña
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = checkPasswordStrength(password);

            passwordStrength.className = `password-strength ${strength}`;
            passwordStrength.textContent = `Password strength: ${strength}`;
        });

        // Verificar coincidencia de contraseñas
        confirmPasswordInput.addEventListener('input', function() {
            const password = passwordInput.value;
            const confirmPassword = this.value;

            if (confirmPassword === '') {
                passwordMatch.textContent = '';
                passwordMatch.className = 'password-match';
            } else if (password === confirmPassword) {
                passwordMatch.textContent = '✓ Passwords match';
                passwordMatch.className = 'password-match success';
            } else {
                passwordMatch.textContent = '✗ Passwords do not match';
                passwordMatch.className = 'password-match error';
            }
        });

        function checkPasswordStrength(password) {
            let strength = 0;

            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;

            if (strength <= 2) return 'weak';
            if (strength <= 4) return 'medium';
            return 'strong';
        }

        // Validación del formulario
        form.addEventListener('submit', function(e) {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            const terms = document.getElementById('terms').checked;

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
                return;
            }

            if (!terms) {
                e.preventDefault();
                alert('Debes aceptar los términos y condiciones');
                return;
            }

            if (checkPasswordStrength(password) === 'weak') {
                e.preventDefault();
                alert('Por favor elige una contraseña más fuerte');
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
</script>