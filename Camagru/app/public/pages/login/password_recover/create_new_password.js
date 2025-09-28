document.addEventListener("DOMContentLoaded", function () {
    const passwordInput = document.getElementById('new-password');
    const confirmPasswordInput = document.getElementById('confirm-password');
    const passwordStrength = document.getElementById('passwordStrength');
    const passwordMatch = document.getElementById('passwordMatch');

    // Verificar fortaleza de contraseña
    passwordInput.addEventListener('input', function () {
        const password = this.value;
        const strength = checkPasswordStrength(password);

        passwordStrength.className = `password-strength ${strength}`;
        passwordStrength.textContent = `Password strength: ${strength}`;
    });

    // Verificar coincidencia de contraseñas
    confirmPasswordInput.addEventListener('input', function () {
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
        else return 'weak';
        if (/[a-z]/.test(password)) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;

        if (strength <= 3) return 'weak';
        if (strength <= 4) return 'medium';
        return 'strong';
    }

    const form = document.querySelector("form");
    form.addEventListener("submit", function (event) {
        const newPassword = document.getElementById("new-password").value;
        const confirmPassword = document.getElementById("confirm-password").value;

        if (newPassword !== confirmPassword) {
            event.preventDefault();
            alert("Passwords do not match.");
        }
        if (checkPasswordStrength(newPassword) === 'weak') {
            event.preventDefault();
            alert("Password is too weak. Please choose a stronger password.");
        }
    });
});