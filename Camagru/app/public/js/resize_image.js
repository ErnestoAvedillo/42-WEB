function makeDraggable(el) {
  let isDragging = false, offsetX, offsetY;

  function onMouseDown(e) {
    isDragging = true;
    offsetX = e.clientX - el.offsetLeft;
    offsetY = e.clientY - el.offsetTop;
    el.style.cursor = 'grabbing';
  }

  function onMouseMove(e) {
    if (!isDragging) return;
    el.style.left = e.clientX - offsetX + 'px';
    el.style.top = e.clientY - offsetY + 'px';
  }

  function onMouseUp() {
    isDragging = false;
    el.style.cursor = 'grab';
  }

  el.addEventListener('mousedown', onMouseDown);
  document.addEventListener('mousemove', onMouseMove);
  document.addEventListener('mouseup', onMouseUp);

  // Store the event listeners on the element for later removal
  el._onMouseDown = onMouseDown;
  el._onMouseMove = onMouseMove;
  el._onMouseUp = onMouseUp;
}

function unmakeDraggable(el) {
  el.style.cursor = 'auto';
  if (el._onMouseDown && el._onMouseMove && el._onMouseUp) {
    el.removeEventListener('mousedown', el._onMouseDown);
    document.removeEventListener('mousemove', el._onMouseMove);
    document.removeEventListener('mouseup', el._onMouseUp);

    // Clean up the stored references
    delete el._onMouseDown;
    delete el._onMouseMove;
    delete el._onMouseUp;
  }
}

function createResizeHandle(parent, position) {
  const handle = document.createElement('div');
  handle.style.position = 'absolute';
  handle.style.width = '20px';
  handle.style.height = '20px';
  handle.style.backgroundColor = position.includes('center') ? 'green' : 'blue';
  handle.style.zIndex = '1000'; // Ensure the handle is above other elements

  const posMap = {
    'top-left': { top: '-10px', left: '-10px', cursor: 'nwse-resize' },
    'top-right': { top: '-10px', right: '-10px', cursor: 'nesw-resize' },
    'bottom-left': { bottom: '-10px', left: '-10px', cursor: 'nesw-resize' },
    'bottom-right': { bottom: '-10px', right: '-10px', cursor: 'nwse-resize' },
    'center-top': { top: '-10px', left: '50%', transform: 'translateX(-50%)', cursor: 'ns-resize' },
    'center-bottom': { bottom: '-10px', left: '50%', transform: 'translateX(-50%)', cursor: 'ns-resize' },
    'center-left': { left: '-10px', top: '50%', transform: 'translateY(-50%)', cursor: 'ew-resize' },
    'center-right': { right: '-10px', top: '50%', transform: 'translateY(-50%)', cursor: 'ew-resize' }
  };
  Object.assign(handle.style, posMap[position]);

  handle.addEventListener('mousedown', e => {
    e.preventDefault();
    const startX = e.clientX;
    const startY = e.clientY;
    const origW = parent.offsetWidth;
    const origH = parent.offsetHeight;
    const origLeft = parent.offsetLeft;
    const origTop = parent.offsetTop;

    function onMouseMove(ev) {
      let newW = origW, newH = origH, newL = origLeft, newT = origTop;

      if (position.includes('top')) {
        newH = origH - (ev.clientY - startY);
        newT = origTop + (ev.clientY - startY);
      }
      if (position.includes('bottom')) newH = origH + (ev.clientY - startY);
      if (position.includes('left')) {
        newW = origW - (ev.clientX - startX);
        newL = origLeft + (ev.clientX - startX);
      }
      if (position.includes('right')) newW = origW + (ev.clientX - startX);

      if (newW > 20) { parent.style.width = newW + 'px'; parent.style.left = newL + 'px'; }
      if (newH > 20) { parent.style.height = newH + 'px'; parent.style.top = newT + 'px'; }
    }

    function onMouseUp() {
      window.removeEventListener('mousemove', onMouseMove);
      window.removeEventListener('mouseup', onMouseUp);
    }

    window.addEventListener('mousemove', onMouseMove);
    window.addEventListener('mouseup', onMouseUp);
  });

  parent.appendChild(handle); // Append handle directly to the parent
}

function hideResizeHandles(parent) {
  const handles = parent.querySelectorAll('div');
  handles.forEach(handle => {
    handle.style.display = 'none';
    handle.selected = false;
  });
}

function showResizeHandles(parent) {
  const handles = parent.querySelectorAll('div');
  handles.forEach(handle => {
    handle.style.display = 'block';
    handle.selected = true;
  });
}

function selectImage(parent) {
  // Select the image
  const selected = parent.querySelector('.selected');
  parent.classList.add('selected');
  parent.style.cursor = 'grab';
  parent.style.border = '2px solid red';
  makeDraggable(parent);
  showResizeHandles(parent);
};

function unSelectImage(parent) {
  // Unselect the image
  parent.classList.remove('selected');
  parent.style.cursor = 'auto';
  parent.style.border = '2px solid transparent';
  unmakeDraggable(parent);
  hideResizeHandles(parent);
};

function createFloatingImage(src, container) {
  // const selected = container.querySelector('.selected');

  const img = document.createElement('div');
  const innersrc = document.createElement('img');
  innersrc.src = src;
  innersrc.style.top = '0px';
  innersrc.style.left = '0px';
  innersrc.style.width = '100%';
  innersrc.style.height = '100%';
  innersrc.style.userSelect = 'none'; // Prevent image selection
  innersrc.draggable = false; // Prevent default drag behavior
  img.appendChild(innersrc);
  // img.style.width = `${innersrc.width}px`;
  // img.style.height = `${innersrc.height}px`;
  img.style.top = '0px';
  img.style.left = '0px';
  img.style.position = 'absolute';
  img.draggable = false; // Prevent default drag behavior

  innersrc.onload = () => {
    console.log('Image loaded:', innersrc.naturalWidth, innersrc.naturalHeight);
    // img.style.width = innersrc.naturalWidth + 'px';
    // img.style.height = innersrc.naturalHeight + 'px';
    console.log('Image resized:', container.style.naturalWidth, container.style.naturalHeight);
    img.style.width = container.style.width;
    img.style.height = container.style.height;
  };
  // Esquinas y centros
  ['top-left', 'top-right', 'bottom-left', 'bottom-right',
    'center-top', 'center-bottom', 'center-left', 'center-right']
    .forEach(pos => createResizeHandle(img, pos));

  // Doble clic para seleccionar/deseleccionar
  img.addEventListener('dblclick', () => {
    if (img.classList.contains('selected')) {
      unSelectImage(img);
    } else {
      // Select the image
      selectImage(img);
    }
  });

  container.appendChild(img);
  selectImage(img);
}
export { createFloatingImage };

// // Uso:
// const combinedImages = document.getElementById('combinedImages'); // tu contenedor
// createFloatingImage('ruta/de/imagen.jpg', combinedImages);
