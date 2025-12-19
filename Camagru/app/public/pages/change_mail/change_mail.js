document.addEventListener('DOMContentLoaded', function () {
	const submitBtn = document.getElementById('submitBtn');
	const secondaryBtn = document.getElementById('secondaryBtn');

	// Validación del formulario
	submitBtn.addEventListener('click', function (e) {
		console.log("Submitting form data...");
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
		const form = document.querySelector('form');
		form.action = '/pages/change_mail/change_mail_handler.php';
		form.method = 'GET';
		form.submit();
	});
});
