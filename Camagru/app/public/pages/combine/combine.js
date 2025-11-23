import { createFloatingImage } from '/js/resize_image.js';
export const myPictures = document.getElementById('MyPictures');
export const myMasters = document.getElementById('Master');
export const combinedImages = document.getElementById('CombinedImages');
export const cleanButton = document.getElementById('clean');
export const saveButton = document.getElementById('save');
export const magicButton = document.getElementById('magic');
export const cameraButton = document.getElementById('open_camera');
export const closeCameraButton = document.getElementById('close_camera');
export const snapshotButton = document.getElementById('take_snapshot');
export const maxImageHeight = "100%";
// const maxImageWidth = "auto";
// const maxImageHeight = document.getElementById('CombinedImages').offsetHeight;
// export const maxImageHeight = document.getElementById('CombinedImages').style.height.replace('px', '');
// Maximum width for combined images
// const maxImageWidth = document.getElementById('CombinedImages').offsetWidth;
// export const maxImageWidth = document.getElementById('CombinedImages').style.width.replace('px', '');
window.sharedState = {
  allowDragFromMyPictures: true,
};
// Global shared state

// Main DOMContentLoaded handler
document.addEventListener('DOMContentLoaded', () => {
  let selectedImage = null;
  let lastCombinedImage = null; // Store the last combined image data

  // Function to get the last combined image
  function getLastCombinedImage() {
    return lastCombinedImage;
  }
  // closeCameraButton.disabled = true;
  // snapshotButton.disabled = true;
  // Handle scroll buttons for MyPictures container
  document.getElementById('mypictures-scroll-up').addEventListener('click', () => {
    console.log('scroll up MyPictures');
    myPictures.scrollBy({ top: -200, behavior: 'smooth' });
  });

  document.getElementById('mypictures-scroll-down').addEventListener('click', () => {
    console.log('scroll down MyPictures');
    myPictures.scrollBy({ top: 200, behavior: 'smooth' });
  });

  // Handle scroll buttons for Master container
  document.getElementById('master-scroll-up').addEventListener('click', () => {
    console.log('scroll up Master');
    myMasters.scrollBy({ top: -200, behavior: 'smooth' });
  });

  document.getElementById('master-scroll-down').addEventListener('click', () => {
    console.log('scroll down Master');
    myMasters.scrollBy({ top: 200, behavior: 'smooth' });
  });



  // Function to handle image selection
  function selectImage(image) {
    // Deselect the previously selected image, if any
    if (selectedImage) {
      selectedImage.classList.remove('selected');
    }
    // Select the new image
    selectedImage = image;
    selectedImage.classList.add('selected');
  }

  // Handle the clean button click
  cleanButton.addEventListener('click', (event) => {
    event.preventDefault();
    combinedImages.innerHTML = ''; // Clear all images in the dropzone
    // combinedImages.style.width = maxImageWidth; // Reset to default width
    combinedImages.style.height = maxImageHeight; // Reset to default height
    window.sharedState.allowDragFromMyPictures = true; // Reset to allow dragging from MyPictures again
    selectedImage = null; // Reset selected image
    cameraButton.disabled = false;
    closeCameraButton.disabled = true;
    snapshotButton.disabled = true;

    console.log('Cleared images. Dragging from MyPictures re-enabled.');
  });

  // Handle the saveButton click
  const referenceWidth = combinedImages.offsetWidth; // Reference width of the container
  const referenceHeight = combinedImages.offsetHeight; // Reference height of the container

  const tooltip = document.createElement('div');
  tooltip.textContent = 'Click to combine images using Gemini AI';
  Object.assign(tooltip.style, {
    position: 'fixed',
    padding: '6px 8px',
    background: 'yellow',
    color: '#000',
    borderRadius: '4px',
    fontSize: '12px',
    pointerEvents: 'none',
    zIndex: 9999,
    display: 'none',
    transform: 'translate(10%, -20px)'
  });
  document.body.appendChild(tooltip);

  function showTooltip(e) {
    const rect = magicButton.getBoundingClientRect();
    tooltip.style.left = `${rect.left + rect.width / 2}px`;
    tooltip.style.top = `${rect.top}px`;
    tooltip.style.display = 'block';
  }

  function hideTooltip() {
    tooltip.style.display = 'none';
  }

  magicButton.addEventListener('mouseenter', showTooltip);
  magicButton.addEventListener('mouseleave', hideTooltip);

  magicButton.addEventListener('click', async (event) => {
    event.preventDefault();
    // Prepare the data to be sent
    const imagesData = [];
    const images = combinedImages.querySelectorAll('img');
    if (typeof startWait === 'function') {
      startWait('Generando comentario...');
      console.log("startWait function called.");
    }
    else {
      document.getElementById("waitOverlay").style.display = "flex";
      console.log("waitOverlay displayed.");
    }
    images.forEach(img => {
      console.log('Processing top, left, width, height for image:');
      const parentDiv = img.parentElement;
      console.log('Top:', parentDiv.style.top);
      console.log('Left:', parentDiv.style.left);
      console.log('Width:', parentDiv.style.width);
      console.log('Height:', parentDiv.style.height);

      // Convert percentage values to pixels based on reference dimensions
      const top = parentDiv.style.top.includes('%')
        ? (parseFloat(parentDiv.style.top) / 100) * referenceHeight
        : parseFloat(parentDiv.style.top);
      const left = parentDiv.style.left.includes('%')
        ? (parseFloat(parentDiv.style.left) / 100) * referenceWidth
        : parseFloat(parentDiv.style.left);
      const width = parentDiv.style.width.includes('auto')
        ? referenceWidth
        : parentDiv.style.width.includes('%')
          ? (parseFloat(parentDiv.style.width) / 100) * referenceWidth
          : parseFloat(parentDiv.style.width);
      const height = parentDiv.style.height.includes('auto')
        ? referenceHeight
        : parentDiv.style.height.includes('%')
          ? (parseFloat(parentDiv.style.height) / 100) * referenceHeight
          : parseFloat(parentDiv.style.height);

      imagesData.push({
        top: Math.trunc(top),
        left: Math.trunc(left),
        width: Math.trunc(width),
        height: Math.trunc(height),
        img: img.src,
      });
    });
    console.log('imagesData for magic combine:', imagesData);
    console.log('Prompt:', document.getElementById('prompt').value);
    const DataToSend = {
      prompt: document.getElementById('prompt').value,
      images: imagesData,
      csrf_token: window.CSRF_TOKEN
    };


    // Send the data to the server
    try {
      const response = await fetch('/pages/combine/magic_combine.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(DataToSend)
      });
      const data = await response.text();
      console.log('Raw response:', data);
      const parsedData = JSON.parse(data);
      console.log('Response parsed as JSON.');
      console.log('Parsed Data:', parsedData);
      if (typeof stopWait === 'function') stopWait();
      if (parsedData.success && Array.isArray(parsedData.images) && parsedData.images.length > 0) {
        // Store the image data globally
        lastCombinedImage = parsedData.images[0];
        if (lastCombinedImage == null) {
          console.error('No image data received from magic combine.');
          alert('Magic combine failed: No image data received.');
          return;
        } else {
          console.log('Image data received from magic combine.');
        }
        console.log('Last combined image updated.');
        // Clear existing images and display the new combined image
        combinedImages.innerHTML = '';
        const img = new Image();
        img.src = parsedData.images[0];
        img.style.position = 'absolute'; // Ensure it's positioned correctly
        // img.style.top = '0';
        // img.style.left = '0';
        img.draggable = false; // Prevent default drag behavior
        img.style.userSelect = 'none'; // Prevent image selection
        img.onload = function () {
          // Ahora img.width y img.height tienen los valores correctos
          let aspectRatio = img.height / img.width;
          if (img.width >= img.height) {
            const finalImageWidth = Math.min(Math.floor(img.height), combinedImages.offsetWidth);
            img.style.width = `${finalImageWidth}px`;
            img.style.height = `${Math.round(finalImageWidth * aspectRatio)}px`;
          } else {
            const finalImageHeight = Math.min(Math.floor(img.width), combinedImages.offsetHeight);
            img.style.height = `${finalImageHeight}px`;
            img.style.width = `${Math.round(finalImageHeight / aspectRatio)}px`;
          }
          img.style.display = "block";
          img.style.margin = "0 auto";
          img.style.alignSelf = 'center';

          // const maxImageHeight = Math.min(Math.floor(window.innerHeight * 0.8), img.height);
          const imageContainer = document.createElement('div');
          imageContainer.style.position = 'absolute';
          // imageContainer.style.top = '0%';
          // imageContainer.style.left = '0%';
          imageContainer.style.width = img.style.width;
          // Ensure the container has height so absolutely positioned children are visible
          imageContainer.style.height = img.style.height;
          imageContainer.style.justifySelf = 'center';
          imageContainer.style.alignSelf = 'center';
          imageContainer.appendChild(img);
          combinedImages.appendChild(imageContainer);
          combinedImages.style.width = `${img.style.width}`;
          combinedImages.style.height = `${img.style.height}`;
          combinedImages.style.justifySelf = 'center';
          // Optionally, provide user feedback
          alert('Magic combine successful!');
        };
      } else {
        alert(parsedData.error || parsedData.message || 'Magic combine failed.');
      }
    } catch (error) {
      console.error('Error:', error);
      // Optionally, provide user feedback
      alert('Error during magic combine.');
    }
  });



  saveButton.addEventListener('click', async (event) => {
    event.preventDefault();
    // Prepare the data to be sent
    const imagesData = [];
    const images = combinedImages.querySelectorAll('img');
    images.forEach(img => {
      console.log('Processing top, left, width, height for image:');
      const parentDiv = img.parentElement;
      console.log('Top:', parentDiv.style.top);
      console.log('Left:', parentDiv.style.left);
      console.log('Width:', parentDiv.style.width);
      console.log('Height:', parentDiv.style.height);

      // Convert percentage values to pixels based on reference dimensions
      const top = parentDiv.style.top.includes('%')
        ? (parseFloat(parentDiv.style.top) / 100) * referenceHeight
        : parseFloat(parentDiv.style.top);
      const left = parentDiv.style.left.includes('%')
        ? (parseFloat(parentDiv.style.left) / 100) * referenceWidth
        : parseFloat(parentDiv.style.left);
      const width = parentDiv.style.width.includes('auto')
        ? referenceWidth
        : parentDiv.style.width.includes('%')
          ? (parseFloat(parentDiv.style.width) / 100) * referenceWidth
          : parseFloat(parentDiv.style.width);
      const height = parentDiv.style.height.includes('auto')
        ? referenceHeight
        : parentDiv.style.height.includes('%')
          ? (parseFloat(parentDiv.style.height) / 100) * referenceHeight
          : parseFloat(parentDiv.style.height);

      imagesData.push({
        top: Math.trunc(top),
        left: Math.trunc(left),
        width: Math.trunc(width),
        height: Math.trunc(height),
        img: img.src,
      });
    });

    // Send the data to the server
    console.log('imagesData:', imagesData);
    try {
      const DataToSend = imagesData;
      DataToSend.csrf_token = window.CSRF_TOKEN;
      const response = await fetch('/pages/combine/save_image.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(DataToSend)
      });
      const data = await response.text();
      console.log('Success:', data);
      const parsedData = JSON.parse(data);
      console.log('Parsed Data:', parsedData);
      if (parsedData.success) {
        // Optionally, provide user feedback
        alert('Images saved successfully!');
      } else {
        alert(parsedData.message || 'Error saving images.');
      }
    } catch (error) {
      console.error('Error:', error);
      // Optionally, provide user feedback
      alert('Error saving images.');
    }
  });

  // Add dragstart event to images
  // Ensure images in MyPictures are explicitly draggable. Some browsers/devices
  // (or dynamically added images) might not have draggable=true by default.
  function ensureDraggableImages(container) {
    const imgs = container.querySelectorAll('img');
    imgs.forEach(img => {
      // Only change if not explicitly set to false
      if (img.draggable !== true) img.draggable = true;
    });
  }

  // Run once on load for existing images
  ensureDraggableImages(myPictures);

  // Track drag source as a fallback when DataTransfer is unavailable (e.g., touch)
  window.__dragImageSrc = null;

  myPictures.addEventListener('dragstart', (event) => {
    if (!window.sharedState.allowDragFromMyPictures) {
      event.preventDefault(); // Prevent dragging if not allowed
      console.log('Dragging from MyPictures is not allowed right now.');
      return;
    }

    const target = event.target;
    if (target && target.tagName === 'IMG') {
      if (event.dataTransfer) {
        event.dataTransfer.setData('text/plain', target.src); // Pass the image source
      } else {
        // Fallback for environments without DataTransfer (touch, some webviews)
        window.__dragImageSrc = target.src;
      }
      console.log('Dragging from MyPictures allowed.', window.sharedState.allowDragFromMyPictures);
    }
  });

  // Add dragstart event to images
  // Ensure masters images are draggable too
  ensureDraggableImages(myMasters);

  myMasters.addEventListener('dragstart', (event) => {
    if (window.sharedState.allowDragFromMyPictures) {
      event.preventDefault(); // Prevent dragging from Masters if MyPictures is still allowed
      console.log('Dragging from Masters is not allowed until MyPictures is used.');
      return;
    }

    const target = event.target;
    if (target && target.tagName === 'IMG') {
      if (event.dataTransfer) {
        event.dataTransfer.setData('text/plain', target.src); // Pass the image source
      } else {
        window.__dragImageSrc = target.src;
      }
      console.log('Dragging from Masters allowed.', window.sharedState.allowDragFromMyPictures);
    }
  });

  // Add dragover event to the dropzone
  combinedImages.addEventListener('dragover', (event) => {
    event.preventDefault(); // Allow dropping
    console.log('drag over');
    combinedImages.classList.add('drag-over'); // Add highlight
  });

  // Remove highlight when drag leaves the dropzone
  combinedImages.addEventListener('dragleave', () => {
    console.log('drag leave');
    combinedImages.classList.remove('drag-over');
  });

  // Handle drop event
  combinedImages.addEventListener('drop', (event) => {
    event.preventDefault();
    combinedImages.classList.remove('drag-over'); // Remove highlight

    // Prefer dataTransfer but fall back to our stored drag src
    const dt = event.dataTransfer;
    const droppedSrc = (dt && dt.getData && dt.getData('text/plain')) || window.__dragImageSrc || '';

    if (!droppedSrc) {
      console.warn('No dragged image source found (dataTransfer and fallback empty).');
      return;
    }

    // If we are in the initial/drop-in background mode, add as base image
    if (window.sharedState.allowDragFromMyPictures) {
      const img = new Image();
      img.src = droppedSrc;
      img.onload = () => {
        // Set combinedImages dimensions to the image dimensions
        img.style.position = 'absolute'; // Ensure it's positioned correctly
        // img.style.top = '0';
        // img.style.left = '0';
        img.draggable = false; // Prevent default drag behavior for base image
        img.style.userSelect = 'none'; // Prevent image selection

        let aspectRatio = img.height / img.width;
        if (img.width >= img.height) {
          const finalImageWidth = Math.min(Math.floor(img.height), combinedImages.offsetWidth);
          img.style.width = `${finalImageWidth}px`;
          img.style.height = `${Math.round(finalImageWidth * aspectRatio)}px`;
        } else {
          const finalImageHeight = Math.min(Math.floor(img.width), combinedImages.offsetHeight);
          img.style.height = `${finalImageHeight}px`;
          img.style.width = `${Math.round(finalImageHeight / aspectRatio)}px`;
        }
        img.style.display = "block";
        img.style.margin = "0 auto";
        img.style.alignSelf = 'center';

        // const maxImageHeight = Math.min(Math.floor(window.innerHeight * 0.8), img.height);
        const imageContainer = document.createElement('div');
        imageContainer.style.position = 'absolute';
        // imageContainer.style.top = '0%';
        // imageContainer.style.left = '0%';
        imageContainer.style.width = img.style.width;
        // Ensure the container has height so absolutely positioned children are visible
        imageContainer.style.height = img.style.height;
        imageContainer.style.justifySelf = 'center';
        imageContainer.style.alignSelf = 'center';
        imageContainer.appendChild(img);
        combinedImages.appendChild(imageContainer);
        combinedImages.style.width = `${img.style.width}`;
        combinedImages.style.height = `${img.style.height}`;
        combinedImages.style.justifySelf = 'center';
        window.sharedState.allowDragFromMyPictures = false;
        cameraButton.disabled = true;
        closeCameraButton.disabled = true;
        snapshotButton.disabled = true;
        console.log('Base image added from MyPictures. Further drags must be from Masters.');
        // Clear fallback after use
        window.__dragImageSrc = null;
      };
    } else {
      // Get the dropped image source and create a floating image
      console.log('Max image window size:', window.innerWidth, window.innerHeight);
      createFloatingImage(droppedSrc, combinedImages);
      // Clear fallback after use
      window.__dragImageSrc = null;
    }
  });
});
// Note: previously there was an accidental self-import here which caused a circular import.
// The camera module should import the exported elements from this file instead.