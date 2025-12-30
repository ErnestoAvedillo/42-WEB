document.addEventListener('DOMContentLoaded', function () {
  const toggleBtn = document.getElementById('userInfo');
  const right_bar = document.getElementById('right');
  const mainContent = document.querySelector('main');
  const footContent = document.querySelector('footer');

  if (!toggleBtn) {
    console.error('Required toggle button not found!');
    return;
  }
  if (!right_bar) {
    console.error('Required right bar not found!');
    return;
  }

  // Initialize: right_bar hidden by default
  let rightright_barVisible = false;

  // Set initial state - right_bar hidden
  right_bar.classList.add('hidden');
  if (mainContent) {
    mainContent.classList.add('full-width');
  }
  if (footContent) {
    footContent.classList.add('full-width');
  }

  toggleBtn.addEventListener('click', function () {
    if (rightright_barVisible) {
      // Hide right_bar
      right_bar.classList.remove('show');
      right_bar.classList.add('hidden');
      if (mainContent) {
        mainContent.classList.add('full-width');
      }
      if (footContent) {
        footContent.classList.add('full-width');
      }
      rightright_barVisible = false;
    } else {
      // Show right_bar
      right_bar.classList.remove('hidden');
      right_bar.classList.add('show');
      if (mainContent) {
        mainContent.classList.remove('full-width');
      }
      if (footContent) {
        footContent.classList.remove('full-width');
      }

      rightright_barVisible = true;
    }
  });
});
