document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('toggleSidebarBtn');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('main');
    
    if (!toggleBtn || !sidebar) return;

    // Initialize: sidebar visible by default
    let sidebarVisible = true;

    toggleBtn.addEventListener('click', function () {
        if (sidebarVisible) {
            // Hide sidebar
            sidebar.classList.add('hidden');
            if (mainContent) {
                mainContent.classList.add('full-width');
            }
            sidebarVisible = false;
        } else {
            // Show sidebar
            sidebar.classList.remove('hidden');
            if (mainContent) {
                mainContent.classList.remove('full-width');
            }
            sidebarVisible = true;
        }
    });
});
console.log("hide_bar.js loaded");
