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
  document.getElementById('mypictures-scroll-up').addEventListener('click', () => {
    myPictures.scrollBy({ top: -200, behavior: 'smooth' });
  });

  document.getElementById('mypictures-scroll-down').addEventListener('click', () => {
    myPictures.scrollBy({ top: 200, behavior: 'smooth' });
  });

  // Handle scroll buttons for Master container
  document.getElementById('master-scroll-up').addEventListener('click', () => {
    myMasters.scrollBy({ top: -200, behavior: 'smooth' });
  });

  document.getElementById('master-scroll-down').addEventListener('click', () => {
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
    // Clear all images in the dropzone
    combinedImages.innerHTML = '';
    // // Reset to default width
    combinedImages.removeAttribute('style');
    window.sharedState.allowDragFromMyPictures = true; // Reset to allow dragging from MyPictures again
    selectedImage = null; // Reset selected image
    cameraButton.disabled = false;
    closeCameraButton.disabled = true;
    snapshotButton.disabled = true;
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
    const container = document.getElementById('CombinedImages');
    const containerRect = container.getBoundingClientRect(); // Referencia del contenedor padre
    event.preventDefault();
    // Prepare the data to be sent
    const imagesData = [];
    const images = combinedImages.querySelectorAll('img');
    if (typeof startWait === 'function') {
      startWait('Creating your new picture...');
    }
    else {
      document.getElementById("waitOverlay").style.display = "flex";
    }
    images.forEach(img => {
      const rect = img.getBoundingClientRect();
      // Calculamos la posición RELATIVA al contenedor principal
      const relativeTop = rect.top - containerRect.top;
      const relativeLeft = rect.left - containerRect.left;

      imagesData.push({
        top: Math.round(relativeTop),
        left: Math.round(relativeLeft),
        width: Math.round(rect.width),
        height: Math.round(rect.height),
        img: img.src,
      });

    });
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
      const parsedData = JSON.parse(data);
      if (typeof stopWait === 'function') stopWait();
      if (parsedData.success && Array.isArray(parsedData.images) && parsedData.images.length > 0) {
        // Store the image data globally
        lastCombinedImage = parsedData.images[0].img;
        if (lastCombinedImage == null) {
          alert('Magic combine failed: No image data received.');
          return;
        }
        // Clear existing images and display the new combined image
        combinedImages.innerHTML = '';
        const img = new Image();
        img.src = parsedData.images[0].img;
        img.style.position = 'absolute'; // Ensure it's positioned correctly
        // img.style.top = '0';
        // img.style.left = '0';
        img.draggable = false; // Prevent default drag behavior
        img.style.userSelect = 'none'; // Prevent image selection
        img.onload = function () {
          // Calculate maximum available height based on viewport and reserved space for controls
          const viewportHeight = window.innerHeight;
          const reservedSpaceForControls = 150; // Reduced reserved space to allow larger images
          const maxHeight = Math.max(300, viewportHeight - reservedSpaceForControls); // Increased minimum height
          
          // Calculate the aspect ratio using natural dimensions
          const aspectRatio = img.naturalWidth / img.naturalHeight;
          
          // Calculate new dimensions maintaining aspect ratio and respecting available space
          let newWidth, newHeight;
          if (img.naturalHeight > maxHeight) {
            newHeight = maxHeight;
            newWidth = maxHeight * aspectRatio;
          } else {
            // Use a reasonable scaling factor for smaller images
            const scaleFactor = Math.min(1, maxHeight / img.naturalHeight);
            newHeight = img.naturalHeight * scaleFactor;
            newWidth = img.naturalWidth * scaleFactor;
          }
          
          // Ensure minimum size for better visibility in Chrome
          const minSize = 300;
          if (newWidth < minSize && newHeight < minSize) {
            if (aspectRatio > 1) { // wider than tall
              newWidth = minSize;
              newHeight = minSize / aspectRatio;
            } else { // taller than wide
              newHeight = minSize;
              newWidth = minSize * aspectRatio;
            }
          }
          
          // Set the image dimensions
          img.style.width = `${newWidth}px`;
          img.style.height = `${newHeight}px`;
          img.style.display = "block";
          
          // Center the image in the container
          img.style.top = '50%';
          img.style.left = '50%';
          img.style.transform = 'translate(-50%, -50%)';

          const imageContainer = document.createElement('div');
          imageContainer.style.position = 'relative';
          imageContainer.style.width = '100%';
          imageContainer.style.height = '100%';
          imageContainer.appendChild(img);
          combinedImages.appendChild(imageContainer);
          
          // Set the container dimensions to match the image
          combinedImages.style.width = `${newWidth}px`;
          combinedImages.style.height = `${newHeight}px`;
          
          // Center the combinedImages container horizontally
          combinedImages.style.margin = '10px auto';
          combinedImages.style.marginBottom = 'var(--combined-margin-bottom)';
          
          // Optionally, provide user feedback
          alert('Magic combine successful!');
          closeCameraButton.disabled = true;
          snapshotButton.disabled = true;
          cameraButton.disabled = true;
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
    const container = document.getElementById('CombinedImages');
    const containerRect = container.getBoundingClientRect(); // Referencia del contenedor padre

    const images = combinedImages.querySelectorAll('img');
    images.forEach(img => {
      const rect = img.getBoundingClientRect();

      // Calculamos la posición RELATIVA al contenedor principal
      const relativeTop = rect.top - containerRect.top;
      const relativeLeft = rect.left - containerRect.left;

      imagesData.push({
        top: Math.round(relativeTop),
        left: Math.round(relativeLeft),
        width: Math.round(rect.width),
        height: Math.round(rect.height),
        img: img.src,
      });


    });

    // Send the data to the server
    try {
      const DataToSend = {
        images: imagesData,
        csrf_token: window.CSRF_TOKEN
      };
      const response = await fetch('/pages/combine/save_image.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(DataToSend)
      });
      const data = await response.text();
      const parsedData = JSON.parse(data);
      if (parsedData.success) {
        // Optionally, provide user feedback
        alert('Images saved successfully!');
      } else {
        alert(parsedData.message || 'Error saving images.');
      }
    } catch (error) {
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
    }
  });

  // Add dragstart event to images
  // Ensure masters images are draggable too
  ensureDraggableImages(myMasters);

  myMasters.addEventListener('dragstart', (event) => {
    if (window.sharedState.allowDragFromMyPictures) {
      event.preventDefault(); // Prevent dragging from Masters if MyPictures is still allowed
      return;
    }

    const target = event.target;
    if (target && target.tagName === 'IMG') {
      if (event.dataTransfer) {
        event.dataTransfer.setData('text/plain', target.src); // Pass the image source
      } else {
        window.__dragImageSrc = target.src;
      }
    }
  });

  // Add dragover event to the dropzone
  combinedImages.addEventListener('dragover', (event) => {
    event.preventDefault(); // Allow dropping
    combinedImages.classList.add('drag-over'); // Add highlight
  });

  // Remove highlight when drag leaves the dropzone
  combinedImages.addEventListener('dragleave', () => {
    combinedImages.classList.remove('drag-over');
  });

  function fitImageInside(img, container) {
    const maxW = container.clientWidth;
    const maxH = container.clientHeight;

    const imgRatio = img.naturalWidth / img.naturalHeight;
    const containerRatio = maxW / maxH;

    if (imgRatio > containerRatio) {
      img.style.width = `${maxW}px`;
      img.style.height = `${maxW / imgRatio}px`;
    } else {
      img.style.height = `${maxH}px`;
      img.style.width = `${maxH * imgRatio}px`;
    }
  }

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
        img.style.position = 'absolute';
        img.draggable = false; // Prevent default drag behavior for base image
        img.style.userSelect = 'none'; // Prevent image selection

        // Calculate maximum available height based on viewport and reserved space for controls
        const viewportHeight = window.innerHeight;
        const reservedSpaceForControls = 150; // Reduced reserved space to allow larger images
        const maxHeight = Math.max(300, viewportHeight - reservedSpaceForControls); // Increased minimum height
        
        // Calculate the aspect ratio of the image
        const aspectRatio = img.naturalWidth / img.naturalHeight;
        
        // Calculate new dimensions maintaining aspect ratio and respecting available space
        let newWidth, newHeight;
        if (img.naturalHeight > maxHeight) {
          newHeight = maxHeight;
          newWidth = maxHeight * aspectRatio;
        } else {
          // Use a reasonable scaling factor for smaller images
          const scaleFactor = Math.min(1, maxHeight / img.naturalHeight);
          newHeight = img.naturalHeight * scaleFactor;
          newWidth = img.naturalWidth * scaleFactor;
        }
        
        // Ensure minimum size for better visibility in Chrome
        const minSize = 300;
        if (newWidth < minSize && newHeight < minSize) {
          if (aspectRatio > 1) { // wider than tall
            newWidth = minSize;
            newHeight = minSize / aspectRatio;
          } else { // taller than wide
            newHeight = minSize;
            newWidth = minSize * aspectRatio;
          }
        }
        
        // Set the container dimensions to match the image
        combinedImages.style.width = `${newWidth}px`;
        combinedImages.style.height = `${newHeight}px`;
        
        // Center the combinedImages container horizontally
        combinedImages.style.margin = '10px auto';
        combinedImages.style.marginBottom = 'var(--combined-margin-bottom)';
        
        // Set the image dimensions to fill the container
        img.style.width = `${newWidth}px`;
        img.style.height = `${newHeight}px`;
        
        // Center the image in the container
        img.style.top = '50%';
        img.style.left = '50%';
        img.style.transform = 'translate(-50%, -50%)';
        
        const imageContainer = document.createElement('div');
        imageContainer.style.position = 'relative';
        imageContainer.style.width = '100%';
        imageContainer.style.height = '100%';
        imageContainer.appendChild(img);
        combinedImages.appendChild(imageContainer);
        
        window.sharedState.allowDragFromMyPictures = false;
        cameraButton.disabled = true;
        closeCameraButton.disabled = true;
        snapshotButton.disabled = true;
        // Clear fallback after use
        window.__dragImageSrc = null;
      };
    } else {
      // Get the dropped image source and create a floating image
      createFloatingImage(droppedSrc, combinedImages);
      // Clear fallback after use
      window.__dragImageSrc = null;
    }
  });
});