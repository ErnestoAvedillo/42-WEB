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

document.querySelector('.upload-form').addEventListener('submit', function (event) {
    const files = document.querySelector('input[name="file[]"]');
    const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10 MB in bytes
    let SumSizes = 0
    for (let i = 0; i < files.files.length; i++) {
        if (files.files[i].size > MAX_FILE_SIZE) {
            alert('File size exceeds 10 MB limit.');
            event.preventDefault();
            return;
        }
        SumSizes += files.files[i].size;
    }
    if (SumSizes > MAX_FILE_SIZE) {
        alert('Total file size exceeds 10 MB limit.');
        event.preventDefault();
        return;
    }
});