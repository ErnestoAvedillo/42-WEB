import { combinedImages, maxImageHeight } from "/pages/combine/combine.js";
document.addEventListener('DOMContentLoaded', () => {
    const openCameraButton = document.getElementById('open_camera');
    const closeCameraButton = document.getElementById('close_camera');
    const snapshotButton = document.getElementById('take_snapshot');
    // Enable/disable camera controls initially
    closeCameraButton.disabled = true;
    snapshotButton.disabled = true;

    openCameraButton.addEventListener('click', () => {
        (async () => {
            const vid = document.createElement('video');
            vid.id = 'cameraPreview';
            vid.autoplay = true;
            vid.playsInline = true;
            vid.muted = true;
            vid.style.maxWidth = '100%';
            vid.style.height = 'auto';
            vid.style.alignSelf = 'center';
            combinedImages.appendChild(vid);

            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                vid.srcObject = stream;
                closeCameraButton.disabled = false;
                snapshotButton.disabled = false;
                openCameraButton.disabled = true;
                window.sharedState.allowDragFromMyPictures = false;
                await vid.play().catch(() => { });
            } catch (err) {
                console.error('getUserMedia error:', err);
                alert('Could not access camera: ' + (err && err.message || err));
                vid.remove();
            }
        })();
    })

    closeCameraButton.addEventListener('click', () => {
        const video = document.getElementById('cameraPreview');
        if (video) {
            const stream = video.srcObject;
            const tracks = stream.getTracks();

            tracks.forEach(function (track) {
                track.stop();
            });

            video.srcObject = null;
            video.remove();
            closeCameraButton.disabled = true;
            snapshotButton.disabled = true;
            openCameraButton.disabled = false;
            window.sharedState.allowDragFromMyPictures = true;

        }
    });

    snapshotButton.addEventListener('click', () => {
        const video = document.getElementById('cameraPreview');
        if (!video) {
            alert('Camera is not open!');
            return;
        }
        closeCameraButton.disabled = true;
        snapshotButton.disabled = true;
        // Create a canvas to capture the snapshot
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth || video.clientWidth || 640;
        canvas.height = video.videoHeight || video.clientHeight || 480;
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        video.srcObject = null;
        video.remove();
        // Convert the canvas to an image
        const img = document.createElement('img');
        img.src = canvas.toDataURL('image/png');
        img.style.maxWidth = '100%';
        img.style.height = 'auto';
        img.style.userSelect = 'none';
        img.classList.add('combined-image');
        img.draggable = true; // allow dragging of the captured image
        img.addEventListener('dragstart', (e) => {
            e.dataTransfer.setData('text/plain', e.target.src);
        });

        let aspectRatio = canvas.width / canvas.height;
        if (canvas.height > canvas.width) {
            const finalImageHeight = Math.min(Math.floor(canvas.height), combinedImages.offsetHeight);
            img.style.height = `${finalImageHeight}px`;
            img.style.width = `${Math.round(finalImageHeight * aspectRatio)}px`;
        } else {
            const finalImageWidth = Math.min(Math.floor(canvas.width), combinedImages.offsetWidth);
            img.style.width = `${finalImageWidth}px`;
            img.style.height = `${Math.round(finalImageWidth / aspectRatio)}px`;
        }

        const imageContainer = document.createElement('div');
        imageContainer.style.position = 'absolute';
        imageContainer.style.width = img.style.width;
        imageContainer.style.height = img.style.height;
        imageContainer.appendChild(img);
        combinedImages.appendChild(imageContainer);
        combinedImages.style.width = img.style.width;
        combinedImages.style.height = img.style.height;
        combinedImages.style.justifySelf = 'center';
        window.sharedState.allowDragFromMyPictures = false;
    });
});