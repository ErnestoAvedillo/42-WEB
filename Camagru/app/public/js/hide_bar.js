document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('toggleSidebarBtn');
    const sidebar = document.getElementById('sidebar');

    if (!toggleBtn || !sidebar) return;

    toggleBtn.addEventListener('click', function () {
        sidebar.classList.toggle('hidden');
    });
});
console.log("hide_bar.js loaded");
