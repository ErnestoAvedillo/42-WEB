document.addEventListener('DOMContentLoaded', function () {
  const toggleBtn = document.getElementById('toggleSidebarBtn');
  const sidebar = document.getElementById('sidebar');
  const mainContent = document.querySelector('main');
  const footContent = document.querySelector('footer');

  console.log('Toggle button:', toggleBtn);
  console.log('Sidebar:', sidebar);
  console.log('Main content:', mainContent);
  console.log('Foot content:', footContent);

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
    console.log('Toggle button clicked! Current state:', sidebarVisible);
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
      console.log('Sidebar hidden');
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
      console.log('Sidebar shown');
    }
  });
});
console.log("hide_bar.js loaded");
