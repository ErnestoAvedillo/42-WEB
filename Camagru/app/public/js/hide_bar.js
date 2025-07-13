document.addEventListener('DOMContentLoaded', function () {
  const toggleBtn = document.getElementById('toggleSidebarBtn');
  const sidebar = document.getElementById('sidebar');
  const mainContent = document.querySelector('main');

  console.log('Toggle button:', toggleBtn);
  console.log('Sidebar:', sidebar);
  console.log('Main content:', mainContent);

  if (!toggleBtn || !sidebar) {
    console.error('Required elements not found!');
    return;
  }

  // Initialize: sidebar visible by default
  let sidebarVisible = true;

  toggleBtn.addEventListener('click', function () {
    console.log('Toggle button clicked! Current state:', sidebarVisible);
    if (sidebarVisible) {
      // Hide sidebar
      sidebar.classList.add('hidden');
      if (mainContent) {
        mainContent.classList.add('full-width');
      }
      sidebarVisible = false;
      console.log('Sidebar hidden');
    } else {
      // Show sidebar
      sidebar.classList.remove('hidden');
      if (mainContent) {
        mainContent.classList.remove('full-width');
      }
      sidebarVisible = true;
      console.log('Sidebar shown');
    }
  });
});
console.log("hide_bar.js loaded");
