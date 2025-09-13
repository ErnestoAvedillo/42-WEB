import { createFloatingImage } from '/js/resize_image.js';

const myPictures = document.getElementById('MyPictures');
const myMasters = document.getElementById('Master');
const combinedImages = document.getElementById('CombinedImages');
const cleanButton = document.getElementById('clean');
const saveButton = document.getElementById('save');
let selectedImage = null;
let allowDragFromMyPictures = true;

['left', 'right'].forEach(direction => {
  document.getElementById(`scroll-${direction}`).addEventListener('click', () => {
    console.log(`scroll ${direction}`);
    // dragZone.scrollLeft += direction === 'left' ? -200 : 200;
    const dragZone = document.querySelector('.dragzone');
    dragZone.scrollBy({ left: direction === 'left' ? -200 : 200, behavior: 'smooth' }); // Scroll by 200px
  });
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
  allowDragFromMyPictures = true; // Reset to allow dragging from MyPictures again
  selectedImage = null; // Reset selected image
  console.log('Cleared images. Dragging from MyPictures re-enabled.');
});

// Handle the saveutton click
const referenceWidth = combinedImages.offsetWidth; // Reference width of the container
const referenceHeight = combinedImages.offsetHeight; // Reference height of the container

saveButton.addEventListener('click', async (event) => {
  event.preventDefault();
  // Prepare the data to be sent
  const imagesData = [];
  const images = combinedImages.querySelectorAll('img');
  images.forEach(img => {
    const top = img.style.top.includes('%')
      ? (parseFloat(img.style.top) / 100) * referenceHeight
      : parseFloat(img.style.top);
    const left = img.style.left.includes('%')
      ? (parseFloat(img.style.left) / 100) * referenceWidth
      : parseFloat(img.style.left);
    const width = img.style.width.includes('auto')
      ? referenceWidth
      : img.style.width.includes('%')
        ? (parseFloat(img.style.width) / 100) * referenceWidth
        : parseFloat(img.style.width);
    const height = img.style.height.includes('auto')
      ? referenceHeight
      : img.style.height.includes('%')
        ? (parseFloat(img.style.height) / 100) * referenceHeight
        : parseFloat(img.style.height);

    imagesData.push({
      top: Math.trunc(top),
      left: Math.trunc(left),
      width: Math.trunc(width),
      height: Math.trunc(height),
      img: img.src
    });
  });

  // Send the data to the server
  console.log('imagesData:', imagesData);
  try {
    const response = await fetch('/pages/combine/save_image.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(imagesData)
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
myPictures.addEventListener('dragstart', (event) => {
  if (!allowDragFromMyPictures) {
    event.preventDefault(); // Prevent dragging if not allowed
    console.log('Dragging from MyPictures is not allowed right now.');
  } else if (event.target.tagName === 'IMG') {
    event.dataTransfer.setData('text/plain', event.target.src); // Pass the image source
    console.log('Dragging from MyPictures allowed.', allowDragFromMyPictures);
  }
});

// Add dragstart event to images
myMasters.addEventListener('dragstart', (event) => {
  if (allowDragFromMyPictures) {
    event.preventDefault(); // Prevent dragging from Masters if MyPictures is still allowed
    console.log('Dragging from Masters is not allowed until MyPictures is used.');
  } else if (event.target.tagName === 'IMG') {
    event.dataTransfer.setData('text/plain', event.target.src); // Pass the image source
    console.log('Dragging from Masters allowed.', allowDragFromMyPictures);
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
  const img = new Image();
  if (allowDragFromMyPictures) {
    img.src = event.dataTransfer.getData('text/plain');
    img.onload = () => {
      // Set combinedImages dimensions to the image dimensions
      img.style.position = 'absolute'; // Ensure it's positioned correctly
      img.style.top = '0';
      img.style.left = '0';
      img.draggable = false; // Prevent default drag behavior
      img.style.userSelect = 'none'; // Prevent image selection
      combinedImages.style.width = `${img.width}px`;
      combinedImages.style.height = `${img.height}px`;
      combinedImages.appendChild(img);
      allowDragFromMyPictures = false;
    };
  } else {
    // Get the dropped image source
    const imageSrc = event.dataTransfer.getData('text/plain');
    createFloatingImage(imageSrc, combinedImages);
  }
});
