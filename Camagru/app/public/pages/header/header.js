document.addEventListener('DOMContentLoaded', function () {
    // ... tu código existente ...

    const logoutIcon = document.querySelector('a[href="/pages/logout/logout.php"]');
    if (logoutIcon) {
        const tooltip = document.createElement('div');
        tooltip.textContent = 'Logout';
        tooltip.style.position = 'absolute';
        tooltip.style.backgroundColor = '#333';
        tooltip.style.color = 'white';
        tooltip.style.padding = '5px';
        tooltip.style.borderRadius = '3px';
        tooltip.style.display = 'none';
        tooltip.style.zIndex = '1000';
        document.body.appendChild(tooltip);

        logoutIcon.addEventListener('mouseenter', (e) => {
            const rect = logoutIcon.getBoundingClientRect();
            tooltip.style.left = `${rect.left}px`;
            tooltip.style.top = `${rect.bottom + 5}px`; // 5px debajo del icono
            tooltip.style.display = 'block';
        });

        logoutIcon.addEventListener('mouseleave', () => {
            tooltip.style.display = 'none';
        });
    }
});
