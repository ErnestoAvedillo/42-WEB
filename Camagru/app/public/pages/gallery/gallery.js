document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = document.getElementById('csrf_token')?.value;
    const sortSelect = document.getElementsByClassName('sort-select');
    const nr_elements = document.getElementsByClassName('number-elements-select');

    for (let i = 0; i < nr_elements.length; i++) {
        nr_elements[i].addEventListener('click', async function (event) {
            // event.preventDefault();
            console.log('Number of elements selected: ' + this.value);
            try {
                const response = await fetch('/pages/gallery/sort-pictures.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `csrf_token=${encodeURIComponent(csrfToken)}&
                        sort-by=${encodeURIComponent(document.querySelector('.sort-select').value)}&
                        nr_elements=${encodeURIComponent(this.value)}&
                        page=1`,
                });
                const result = await response.text();
                const galleryContainer = document.querySelector('.user-gallery');
                if (galleryContainer) {
                    galleryContainer.innerHTML = result;
                }
                console.log('Number of elements updated successfully to: ' + this.value);
                console.log('result obtained: ' + result);
            } catch (error) {
                console.error('Error updating number of elements:', error);
            }
        });
    }

    for (let i = 0; i < sortSelect.length; i++) {
        sortSelect[i].addEventListener('click', async function (event) {
            // event.preventDefault();
            console.log('Sort option selected: ' + this.value);
            try {
                const response = await fetch('/pages/gallery/sort-pictures.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `csrf_token=${encodeURIComponent(csrfToken)}&
                        sort-by=${encodeURIComponent(this.value)}&
                        nr_elements=${encodeURIComponent(document.querySelector('.number-elements-select').value)}&
                        page=1`,
                });
                const result = await response.text();
                const galleryContainer = document.querySelector('.user-gallery');
                if (galleryContainer) {
                    galleryContainer.innerHTML = result;
                }
                console.log('Gallery sorted successfully by: ' + this.value);
                console.log('result obtained: ' + result);
            } catch (error) {
                console.error('Error sorting gallery:', error);
            }
        });
    }

    const iconsDelete = document.getElementsByClassName('delete-image');
    for (let i = 0; i < iconsDelete.length; i++) {
        iconsDelete[i].addEventListener('click', async function (event) {
            event.preventDefault();
            if (!confirm('Are you sure you want to delete this image?')) {
                return;
            }
            console.log('Delete icon clicked');
            const container = this.dataset.container;
            const imageId = this.dataset.imageId;
            console.log('Container:', container);
            console.log('Image ID:', imageId);
            try {
                const response = await fetch('/pages/gallery/delete_image.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `container=${encodeURIComponent(container)}&image-id=${encodeURIComponent(imageId)}&csrf_token=${encodeURIComponent(csrfToken)}`,
                });
                const result = await response.text();
                const resultJson = JSON.parse(result);
                if (response.ok && resultJson.status === 'success') {
                    this.closest('.photo').remove();
                    console.log('Image deleted successfully:', resultJson.message);
                } else {
                    console.error('Error deleting image:', resultJson.message);
                }
            } catch (error) {
                console.error('Error deleting image:', error);
                alert('An error occurred while deleting the image.');
            }
        });
    }
});

