async function showPictures(csrfToken, sortBy = null, nrElements = null, user = null, page = 1) {
    const sortValue = sortBy || document.querySelector('.sort-select')?.value || 'oldest';
    const userSortValue = user || document.querySelector('.user-sort-select')?.value || 'all';
    const elementsValue = nrElements || document.querySelector('.number-elements-select')?.value || '10';

    console.log('Loading pictures with sort:', sortValue, 'elements:', elementsValue, 'user:', userSortValue, 'page:', page);
    try {
        const requestData = {
            'csrf_token': csrfToken,
            'sort-by': sortValue,
            'nr_elements': elementsValue,
            'user': userSortValue,
            'page': page
        };
        console.log('Request sent to server:', requestData);

        const response = await fetch('/pages/gallery/sort-pictures.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `csrf_token=${encodeURIComponent(csrfToken)}&sort-by=${encodeURIComponent(sortValue)}&nr_elements=${encodeURIComponent(elementsValue)}&user=${encodeURIComponent(userSortValue)}&page=${encodeURIComponent(page)}`,
        });


        const result = await response.text();
        const galleryContainer = document.querySelector('.user-gallery');
        if (galleryContainer) {
            galleryContainer.innerHTML = result;
        }
        createPagination(csrfToken, sortValue, elementsValue, userSortValue, page);
        console.log('Pictures loaded successfully');
        console.log('Response received, length:', result.length);
    } catch (error) {
        console.error('Error loading pictures:', error);
    }
}

async function createPagination(csrfToken, sortBy = null, nrElements = null, user = null, page = 1) {
    const sortValue = sortBy || document.querySelector('.sort-select')?.value || 'date_desc';
    const userSortValue = user || document.querySelector('.user-sort-select')?.value || 'all';
    const elementsValue = nrElements || document.querySelector('.number-elements-select')?.value || '10';

    console.log('Creating pagination with sort:', sortValue, 'elements:', elementsValue, 'page:', page);
    try {
        const response = await fetch('/pages/gallery/create_pagination.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `csrf_token=${encodeURIComponent(csrfToken)}&nr_elements=${encodeURIComponent(elementsValue)}&user=${encodeURIComponent(userSortValue)}&page=${encodeURIComponent(page)}`,
        });
        const result = await response.text();
        const paginationContainer = document.querySelector('.pagination-container');
        if (paginationContainer) {
            paginationContainer.innerHTML = result;
        }
    } catch (error) {
        console.error('Error creating pagination:', error);
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = document.getElementById('csrf_token')?.value;
    const sortSelect = document.getElementsByClassName('sort-select');
    const userSortSelect = document.getElementsByClassName('user-sort-select');
    const nr_elements = document.getElementsByClassName('number-elements-select');

    // Ejecutar showPictures al cargar el documento
    if (csrfToken) {
        console.log('CSRF Token found:', csrfToken);
        showPictures(csrfToken);
        createPagination(csrfToken);
    }

    for (let i = 0; i < nr_elements.length; i++) {
        nr_elements[i].addEventListener('change', async function (event) {
            // event.preventDefault();
            console.log('Number of elements selected: ' + this.value);
            await showPictures(csrfToken, sortSelect.value, this.value, userSortSelect.value);
        });
    }

    for (let i = 0; i < sortSelect.length; i++) {
        sortSelect[i].addEventListener('change', async function (event) {
            // event.preventDefault();
            console.log('Sort option selected: ' + this.value);
            await showPictures(csrfToken, this.value, nr_elements.value, userSortSelect.value);
        });
    }

    for (let i = 0; i < userSortSelect.length; i++) {
        userSortSelect[i].addEventListener('change', async function (event) {
            // event.preventDefault();
            console.log('User sort option selected: ' + this.value);
            await showPictures(csrfToken, sortSelect.value, nr_elements.value, this.value);
        });
    }

    // Usar delegación de eventos para los botones de paginación y borrado
    document.addEventListener('click', async function (event) {
        if (event.target.classList.contains('pagination-button')) {
            const selectedPage = parseInt(event.target.value);
            console.log('Pagination button clicked, page:', selectedPage);
            await showPictures(csrfToken, sortSelect[0]?.value, nr_elements[0]?.value, userSortSelect[0]?.value, selectedPage);
        }

        const deleteLink = event.target.closest('.delete-image');
        if (deleteLink) {
            event.preventDefault();
            if (!confirm('Are you sure you want to delete this image?')) {
                return;
            }
            console.log('Delete icon clicked');
            const container = deleteLink.dataset.container;
            const imageId = deleteLink.dataset.imageId;
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
                    deleteLink.closest('.photo').remove();
                    console.log('Image deleted successfully:', resultJson.message);
                } else {
                    console.error('Error deleting image:', resultJson.message);
                }
            } catch (error) {
                console.error('Error deleting image:', error);
                alert('An error occurred while deleting the image.');
            }
        }
    });
});

