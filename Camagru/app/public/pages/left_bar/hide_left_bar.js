document.addEventListener('DOMContentLoaded', function () {
  const toggleBtn = document.getElementById('toggleSidebarBtn');
  const left_bar = document.getElementById('left');
  const mainContent = document.querySelector('main');
  const footContent = document.querySelector('footer');

  console.log('Toggle button:', toggleBtn);
  console.log('left_bar:', left_bar);
  console.log('Main content:', mainContent);
  console.log('Foot content:', footContent);

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
    console.log('Toggle button clicked! Current state:', left_barVisible);
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
      console.log('left_bar hidden');
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
      console.log('left_bar shown');
    }
  });
});
console.log("hide_bar.js loaded");
