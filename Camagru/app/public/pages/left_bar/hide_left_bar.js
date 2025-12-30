document.addEventListener('DOMContentLoaded', function () {
  const toggleBtn = document.getElementById('toggleSidebarBtn');
  const left_bar = document.getElementById('left');
  const mainContent = document.querySelector('main');
  const footContent = document.querySelector('footer');

  if (!toggleBtn || !left_bar) {
    console.error('Required elements not found!');
    return;
  }

  // Initialize: left_bar hidden by default
  let left_barVisible = false;

  // Set initial state - left_bar hidden
  left_bar.classList.add('hidden');
  if (mainContent) {
    mainContent.classList.add('full-width');
  }
  if (footContent) {
    footContent.classList.add('full-width');
  }

  toggleBtn.addEventListener('click', function () {
    if (left_barVisible) {
      // Hide left_bar
      left_bar.classList.remove('show');
      left_bar.classList.add('hidden');
      if (mainContent) {
        mainContent.classList.add('full-width');
      }
      if (footContent) {
        footContent.classList.add('full-width');
      }
      left_barVisible = false;
    } else {
      // Show left_bar
      left_bar.classList.remove('hidden');
      left_bar.classList.add('show');
      if (mainContent) {
        mainContent.classList.remove('full-width');
      }
      if (footContent) {
        footContent.classList.remove('full-width');
      }
      left_barVisible = true;
    }
  });
});
