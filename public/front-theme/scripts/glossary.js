(function () {
    'use strict';

    const root = document.querySelector('[data-glossary-root]');

    if (!(root instanceof HTMLElement)) {
        return;
    }

    const form = root.querySelector('[data-glossary-form]');
    const searchInput = root.querySelector('[data-glossary-search]');
    const letterInput = root.querySelector('[data-glossary-letter-input]');
    const clearButton = root.querySelector('[data-glossary-clear]');
    const resultCount = root.querySelector('[data-glossary-count]');
    const emptyState = root.querySelector('[data-glossary-empty]');
    const items = Array.from(root.querySelectorAll('[data-glossary-item]'));
    const groups = Array.from(root.querySelectorAll('[data-glossary-group]'));
    const letterButtons = Array.from(root.querySelectorAll('[data-glossary-letter]'));

    if (
        !(form instanceof HTMLFormElement)
        || !(searchInput instanceof HTMLInputElement)
        || !(letterInput instanceof HTMLInputElement)
        || !(clearButton instanceof HTMLButtonElement)
        || !(resultCount instanceof HTMLElement)
        || !(emptyState instanceof HTMLElement)
        || items.length === 0
    ) {
        return;
    }

    let activeLetter = root.dataset.activeLetter || 'ALL';
    let debounceHandle = 0;

    const normalize = (value) => value
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, ' ')
        .trim();

    const syncButtons = () => {
        letterButtons.forEach((button) => {
            const isActive = button.dataset.glossaryLetter === activeLetter;
            button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            button.classList.toggle('is-active', isActive);
        });

        letterInput.value = activeLetter;
    };

    const updateUrl = () => {
        const params = new URLSearchParams(window.location.search);
        const rawQuery = searchInput.value.trim();

        if (rawQuery !== '') {
            params.set('q', rawQuery);
        } else {
            params.delete('q');
        }

        if (activeLetter !== 'ALL') {
            params.set('letter', activeLetter);
        } else {
            params.delete('letter');
        }

        const nextQuery = params.toString();
        const nextUrl = `${window.location.pathname}${nextQuery ? `?${nextQuery}` : ''}`;
        window.history.replaceState({}, '', nextUrl);
    };

    const applyFilters = () => {
        const query = normalize(searchInput.value);
        let visibleCount = 0;

        items.forEach((item) => {
            const matchesLetter = activeLetter === 'ALL' || item.dataset.letter === activeLetter;
            const haystack = item.dataset.search || '';
            const matchesSearch = query === '' || haystack.includes(query);
            const visible = matchesLetter && matchesSearch;

            item.hidden = !visible;

            if (visible) {
                visibleCount += 1;
            }
        });

        groups.forEach((group) => {
            const hasVisibleItems = Array.from(group.querySelectorAll('[data-glossary-item]'))
                .some((item) => !item.hidden);
            group.hidden = !hasVisibleItems;
        });

        resultCount.textContent = String(visibleCount);
        emptyState.hidden = visibleCount > 0;
        clearButton.hidden = searchInput.value.trim() === '' && activeLetter === 'ALL';
        syncButtons();
        updateUrl();
    };

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        applyFilters();
    });

    searchInput.addEventListener('input', () => {
        window.clearTimeout(debounceHandle);
        debounceHandle = window.setTimeout(applyFilters, 140);
    });

    clearButton.addEventListener('click', () => {
        searchInput.value = '';
        activeLetter = 'ALL';
        applyFilters();
        searchInput.focus();
    });

    letterButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const nextLetter = button.dataset.glossaryLetter || 'ALL';
            activeLetter = activeLetter === nextLetter ? 'ALL' : nextLetter;
            applyFilters();
        });
    });

    applyFilters();
})();
