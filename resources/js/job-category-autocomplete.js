document.querySelectorAll('[data-category-dropdown]').forEach((container) => {
    const categoryInput = container.querySelector('[data-category-value]');
    const menu = container.querySelector('[data-category-menu]');
    const searchInput = container.querySelector('[data-category-filter]');
    const optionsList = container.querySelector('[data-category-options]');
    const emptyMessage = container.querySelector('[data-category-empty]');
    const status = container.querySelector('[data-category-status]');
    const dataElement = container.querySelector('[data-category-data]');

    if (!categoryInput || !menu || !searchInput || !optionsList || !emptyMessage || !status || !dataElement) {
        return;
    }

    const categories = Object.entries(JSON.parse(dataElement.textContent)).flatMap(([group, labels]) =>
        labels.map((label) => ({ group, label }))
    );
    let activeIndex = -1;

    const closeMenu = (restoreFocus = false) => {
        menu.classList.add('hidden');
        categoryInput.setAttribute('aria-expanded', 'false');
        categoryInput.removeAttribute('aria-activedescendant');
        activeIndex = -1;

        if (restoreFocus) {
            categoryInput.focus();
        }
    };

    const selectCategory = (category) => {
        categoryInput.value = category.label;
        status.textContent = `Selected: ${category.label}`;
        closeMenu(true);
    };

    const renderOptions = () => {
        const query = searchInput.value.trim().toLocaleLowerCase();
        const matches = categories.filter((category) =>
            category.label.toLocaleLowerCase().includes(query)
        );

        optionsList.replaceChildren();
        activeIndex = -1;

        matches.forEach((category, index) => {
            const option = document.createElement('button');
            option.type = 'button';
            option.id = `category-option-${index}`;
            option.setAttribute('role', 'option');
            option.setAttribute('aria-selected', String(category.label === categoryInput.value));
            option.tabIndex = -1;
            option.className = `block w-full px-4 py-2.5 text-left text-sm transition hover:bg-brand-50 focus:bg-brand-50 focus:outline-none ${category.label === categoryInput.value ? 'bg-brand-50 font-semibold text-brand-800' : 'text-slate-800'}`;
            option.textContent = category.label;
            option.addEventListener('click', () => selectCategory(category));
            optionsList.append(option);
        });

        emptyMessage.classList.toggle('hidden', matches.length > 0);
        status.textContent = matches.length === 0 ? 'No categories found.' : `${matches.length} categories available.`;
    };

    const openMenu = () => {
        if (!menu.classList.contains('hidden')) {
            return;
        }

        menu.classList.remove('hidden');
        categoryInput.setAttribute('aria-expanded', 'true');
        searchInput.value = '';
        renderOptions();
        searchInput.focus();
    };

    const setActiveOption = (index) => {
        const options = optionsList.querySelectorAll('[role="option"]');

        if (!options.length) {
            return;
        }

        activeIndex = (index + options.length) % options.length;
        options.forEach((option, optionIndex) => {
            option.classList.toggle('bg-brand-50', optionIndex === activeIndex);
        });
        searchInput.setAttribute('aria-activedescendant', options[activeIndex].id);
        options[activeIndex].scrollIntoView({ block: 'nearest' });
    };

    categoryInput.addEventListener('click', () => {
        if (menu.classList.contains('hidden')) {
            openMenu();
        } else {
            closeMenu();
        }
    });

    categoryInput.addEventListener('keydown', (event) => {
        if (['Enter', ' ', 'ArrowDown'].includes(event.key)) {
            event.preventDefault();
            openMenu();
        } else if (event.key === 'Escape') {
            closeMenu();
        }
    });

    searchInput.addEventListener('input', renderOptions);
    searchInput.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowDown') {
            event.preventDefault();
            setActiveOption(activeIndex + 1);
        } else if (event.key === 'ArrowUp') {
            event.preventDefault();
            setActiveOption(activeIndex - 1);
        } else if (event.key === 'Enter' && activeIndex >= 0) {
            event.preventDefault();
            optionsList.querySelectorAll('[role="option"]')[activeIndex]?.click();
        } else if (event.key === 'Escape') {
            event.preventDefault();
            closeMenu(true);
        }
    });

    document.addEventListener('pointerdown', (event) => {
        if (!container.contains(event.target)) {
            closeMenu();
        }
    });
});