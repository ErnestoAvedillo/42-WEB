document.getElementById('profile_picture').addEventListener('change', function (event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            // Buscar la imagen actual o crear una nueva
            let imgElement = document.querySelector('.current-profile-picture img') ||
                document.querySelector('.no-profile-picture div');

            if (!document.querySelector('.current-profile-picture img')) {
                // Si no hay imagen, crear una nueva
                const container = document.querySelector('.current-profile-picture') ||
                    document.querySelector('.no-profile-picture');
                container.innerHTML = `
                    <img src="${e.target.result}" 
                         alt="Profile Picture Preview" 
                         style="max-width: 150px; max-height: 150px; border-radius: 50%; object-fit: cover; display: block; margin: 10px 0;">
                    <p><small>New picture preview</small></p>
                `;
            } else {
                // Actualizar imagen existente
                imgElement.src = e.target.result;
                imgElement.nextElementSibling.innerHTML = '<small>New picture preview</small>';
            }
        };
        reader.readAsDataURL(file);
    }
});