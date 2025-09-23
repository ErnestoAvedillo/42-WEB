document.addEventListener('DOMContentLoaded', function () {
	const passwordInput = document.getElementById('password');
	const confirmPasswordInput = document.getElementById('confirm_password');
	const passwordStrength = document.getElementById('passwordStrength');
	const passwordMatch = document.getElementById('passwordMatch');
	const form = document.getElementById('register-form');

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

	// Validación del formulario
	form.addEventListener('submit', function (e) {
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