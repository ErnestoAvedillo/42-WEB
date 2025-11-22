document.addEventListener('DOMContentLoaded', function () {
	const passwordInput = document.getElementById('password');
	const confirmPasswordInput = document.getElementById('confirm_password');
	const passwordStrength = document.getElementById('passwordStrength');
	const passwordMatch = document.getElementById('passwordMatch');
	const submitBtn = document.getElementById('submitBtn');
	const secondaryBtn = document.getElementById('secondaryBtn');

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
	submitBtn.addEventListener('click', function (e) {
		const password = passwordInput.value;
		const confirmPassword = confirmPasswordInput.value;
		const terms = document.getElementById('terms').checked;
		console.log("Submitting form data...");
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
		const formData = new FormData(document.querySelector('form'));
		const params = new URLSearchParams(formData);
		console.log(params.toString());
		// fetch('/pages/register/register_handler.php?send_link=0&' + params.toString())
		// 	.then(response => response.json())
		// 	.then(data => {
		// 		console.log(data);
		// 		if (!data.success) {
		// 			submitBtn.disabled = true;
		// 			window.location.href = '/pages/register/register.php';
		// 		}
		// 	});
		// En lugar de fetch, hacer submit normal del formulario
		// Esto permitirá que PHP haga la redirección directamente
		const form = document.querySelector('form');
		form.action = '/pages/register/register_handler.php';
		form.method = 'GET';
		form.submit();
	});

	secondaryBtn.addEventListener('click', function (e) {
		const password = passwordInput.value;
		const confirmPassword = confirmPasswordInput.value;
		const terms = document.getElementById('terms').checked;
		console.log("Submitting form data...");
		if (typeof startWait === 'function') {
			startWait('Generando comentario...');
			console.log("startWait function called.");
		}
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
		const btnText = document.querySelector('.btn2-text');
		const btnLoading = document.querySelector('.btn2-loading');
		const secondaryBtn = document.getElementById('secondaryBtn');

		const formData = new FormData(document.querySelector('form'));
		const params = new URLSearchParams(formData);
		console.log(params.toString());
		fetch('/pages/register/register_handler.php?send_link=1&' + params.toString())
			.then(response => response.json())
			.then(data => {
				console.log(data);
				if (data.success) {
					console.log("Registration pending link created, starting status check.");
					btnText.style.display = 'none';
					btnLoading.style.display = 'inline';
					secondaryBtn.disabled = true;
					setInterval(() => {
						fetch('/pages/register/check_status.php?' + params.toString())
							.then(response => response.json())
							.then(data => {
								if (data.success === true) {
									if (typeof stopWait === 'function') stopWait();
									else document.getElementById("waitOverlay").style.display = "none";
									window.location.href = '/pages/login/login.php';
								}
							});
					}, 2000);
				} else {
					window.location.href = '/pages/register/register.php';
				}
			});
	});
});
function togglePassword(inputId) {
	const passwordInput = document.getElementById(inputId);
	const toggleText = document.getElementById(inputId + '-toggle-text');

	if (passwordInput.type === 'password') {
		passwordInput.type = 'text';
		toggleText.textContent = '🙈'; // Icono de ocultar
	} else {
		passwordInput.type = 'password';
		toggleText.textContent = '👁️'; // Icono de mostrar
	}
}
