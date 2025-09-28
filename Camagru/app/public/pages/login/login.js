document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('loginForm');

    form.addEventListener('submit', function (e) {
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

function togglePassword(inputId) {
    const passwordInput = document.getElementById(inputId);
    const toggleText = document.getElementById(inputId + '-toggle-text');
    const newPassword = document.getElementById("password").value;

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleText.textContent = '🙈'; // Icono de ocultar
    } else {
        passwordInput.type = 'password';
        toggleText.textContent = '👁️'; // Icono de mostrar
    }
}