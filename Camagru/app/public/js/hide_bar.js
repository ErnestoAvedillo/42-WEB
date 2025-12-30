document.addEventListener('DOMContentLoaded', function () {
  const toggleBtn = document.getElementById('toggleSidebarBtn');
  const sidebar = document.getElementById('sidebar');
  const mainContent = document.querySelector('main');
  const footContent = document.querySelector('footer');

  if (!toggleBtn || !sidebar) {
    console.error('Required elements not found!');
    return;
  }

  // Initialize: sidebar hidden by default
  let sidebarVisible = false;

  // Set initial state - sidebar hidden
  sidebar.classList.add('hidden');
  if (mainContent) {
    mainContent.classList.add('full-width');
  }
  if (footContent) {
    footContent.classList.add('full-width');
  }

  toggleBtn.addEventListener('click', function () {
    if (sidebarVisible) {
      // Hide sidebar
      sidebar.classList.remove('show');
      sidebar.classList.add('hidden');
      if (mainContent) {
        mainContent.classList.add('full-width');
      }
      if (footContent) {
        footContent.classList.add('full-width');
      }
      sidebarVisible = false;
    } else {
      // Show sidebar
      sidebar.classList.remove('hidden');
      sidebar.classList.add('show');
      if (mainContent) {
        mainContent.classList.remove('full-width');
      }
      if (footContent) {
        footContent.classList.remove('full-width');
      }
      sidebarVisible = true;
    }
  });
});
