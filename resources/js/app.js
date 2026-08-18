import Alpine from 'alpinejs';

// Initialize Alpine.js
window.Alpine = Alpine;
Alpine.start();

// Global utility functions for interactivity
window.toggleMenu = function(menuId) {
    const menu = document.getElementById(menuId);
    if (menu) {
        menu.classList.toggle('hidden');
    }
};

// Close menus when clicking outside
document.addEventListener('click', function(event) {
    const menus = document.querySelectorAll('[data-dropdown]');
    menus.forEach(menu => {
        if (!menu.contains(event.target)) {
            menu.classList.add('hidden');
        }
    });
});
