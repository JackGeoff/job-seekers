import Alpine from 'alpinejs';
import { initializeCategoryDropdowns } from './job-category-autocomplete.js';
import { initializeMultipleJobPosting } from './multiple-job-posting.js';

const initializeDescriptionEditors = (root = document) => {
    import('./job-description-editor.js').then(({ initializeJobDescriptionEditors }) => {
        initializeJobDescriptionEditors(root);
    });
};

initializeCategoryDropdowns();
document.addEventListener('job-entry:added', (event) => {
    initializeCategoryDropdowns(event.detail);
    initializeDescriptionEditors(event.detail);
});
initializeMultipleJobPosting();

if (document.querySelector('[data-job-description-editor]')) {
    initializeDescriptionEditors();
}

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
