document.addEventListener('DOMContentLoaded', function () {
    const lightbox = document.querySelector('[data-team-lightbox]');
    const triggers = Array.from(document.querySelectorAll('[data-team-lightbox-trigger]'));

    if (!(lightbox instanceof HTMLElement) || triggers.length === 0) {
        return;
    }

    document.body.appendChild(lightbox);

    const image = lightbox.querySelector('[data-team-lightbox-image]');
    const caption = lightbox.querySelector('[data-team-lightbox-caption]');
    const position = lightbox.querySelector('[data-team-lightbox-position]');
    const closeButtons = lightbox.querySelectorAll('[data-team-lightbox-close]');
    const previousButton = lightbox.querySelector('[data-team-lightbox-previous]');
    const nextButton = lightbox.querySelector('[data-team-lightbox-next]');
    const focusableSelector = 'button:not([disabled]), [href], input:not([disabled]), [tabindex]:not([tabindex="-1"])';
    let activeIndex = 0;
    let lastFocusedElement = null;
    let closeTimer = null;

    const setActiveItem = function (index) {
        activeIndex = (index + triggers.length) % triggers.length;
        const trigger = triggers[activeIndex];
        const source = trigger.dataset.teamLightboxSrc || '';
        const alternativeText = trigger.dataset.teamLightboxAlt || '';

        if (image instanceof HTMLImageElement) {
            image.src = source;
            image.alt = alternativeText;
        }

        if (caption instanceof HTMLElement) {
            caption.textContent = alternativeText;
        }

        if (position instanceof HTMLElement) {
            position.textContent = String(activeIndex + 1).padStart(2, '0') + ' / ' + String(triggers.length).padStart(2, '0');
        }
    };

    const openLightbox = function (index, trigger) {
        if (closeTimer !== null) {
            window.clearTimeout(closeTimer);
            closeTimer = null;
        }

        lastFocusedElement = trigger instanceof HTMLElement ? trigger : document.activeElement;
        setActiveItem(index);
        lightbox.hidden = false;
        lightbox.setAttribute('aria-hidden', 'false');
        document.body.classList.add('ac-team-lightbox-open');

        window.requestAnimationFrame(function () {
            lightbox.classList.add('is-open');

            const closeButton = lightbox.querySelector('.ac-team-lightbox-close');
            if (closeButton instanceof HTMLButtonElement) {
                closeButton.focus();
            }
        });
    };

    const closeLightbox = function () {
        if (lightbox.hidden) {
            return;
        }

        lightbox.classList.remove('is-open');
        lightbox.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('ac-team-lightbox-open');

        closeTimer = window.setTimeout(function () {
            lightbox.hidden = true;

            if (image instanceof HTMLImageElement) {
                image.removeAttribute('src');
                image.alt = '';
            }

            if (lastFocusedElement instanceof HTMLElement) {
                lastFocusedElement.focus();
            }

            closeTimer = null;
        }, 320);
    };

    const showPrevious = function () {
        setActiveItem(activeIndex - 1);
    };

    const showNext = function () {
        setActiveItem(activeIndex + 1);
    };

    triggers.forEach(function (trigger, index) {
        trigger.addEventListener('click', function () {
            openLightbox(index, trigger);
        });
    });

    closeButtons.forEach(function (button) {
        button.addEventListener('click', closeLightbox);
    });

    if (previousButton instanceof HTMLButtonElement) {
        previousButton.addEventListener('click', showPrevious);
    }

    if (nextButton instanceof HTMLButtonElement) {
        nextButton.addEventListener('click', showNext);
    }

    document.addEventListener('keydown', function (event) {
        if (lightbox.hidden) {
            return;
        }

        if (event.key === 'Escape') {
            event.preventDefault();
            closeLightbox();
            return;
        }

        if (event.key === 'ArrowLeft') {
            event.preventDefault();
            showPrevious();
            return;
        }

        if (event.key === 'ArrowRight') {
            event.preventDefault();
            showNext();
            return;
        }

        if (event.key !== 'Tab') {
            return;
        }

        const focusableElements = Array.from(lightbox.querySelectorAll(focusableSelector))
            .filter(function (element) {
                return element instanceof HTMLElement && element.offsetParent !== null;
            });

        if (focusableElements.length === 0) {
            return;
        }

        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];

        if (event.shiftKey && document.activeElement === firstElement) {
            event.preventDefault();
            lastElement.focus();
        } else if (!event.shiftKey && document.activeElement === lastElement) {
            event.preventDefault();
            firstElement.focus();
        }
    });
});
