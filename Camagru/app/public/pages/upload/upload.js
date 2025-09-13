document.addEventListener('DOMContentLoaded', () => {
    const iconsDelete = document.getElementsByClassName('delete-image');
    for (let i = 0; i < iconsDelete.length; i++) {
        iconsDelete[i].addEventListener('click', async function (event) {
            event.preventDefault();
            if (!confirm('Are you sure you want to delete this image?')) {
                return;
            }
            const container = this.dataset.container;
            const imageId = this.dataset.imageId;
            console.log('Container:', container);
            console.log('Image ID:', imageId);
            try {
                const response = await fetch('/pages/upload/delete_image.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `container=${encodeURIComponent(container)}&image-id=${encodeURIComponent(imageId)}`,
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

