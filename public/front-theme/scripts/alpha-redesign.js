document.addEventListener('DOMContentLoaded', function () {
    const body = document.body;
    const stickyHeader = document.querySelector('[data-front-sticky-header]');
    const homeHero = body.classList.contains('front-route-home')
        ? document.querySelector('.hero')
        : null;
    const video = document.querySelector('[data-alpha-hero-video]');
    const menuToggle = document.querySelector('[data-alpha-menu-toggle]');
    const mobileMenu = document.querySelector('[data-alpha-mobile-menu]');
    const submenuOpen = mobileMenu?.querySelector('[data-alpha-submenu-open]');
    const submenuClose = mobileMenu?.querySelector('[data-alpha-submenu-close]');
    const rootMenuPanel = mobileMenu?.querySelector('[data-alpha-menu-panel="root"]');
    const servicesMenuPanel = mobileMenu?.querySelector('[data-alpha-menu-panel="services"]');
    const searchToggle = document.querySelector('[data-header-search-toggle]');
    const searchPanel = document.querySelector('[data-header-search-panel]');

    const syncStickyHeaderState = function () {
        if (!(stickyHeader instanceof HTMLElement)) {
            return;
        }

        const configuredHeaderHeight = Number.parseFloat(
            window.getComputedStyle(document.documentElement).getPropertyValue('--header-height')
        );
        const headerHeight = Number.isFinite(configuredHeaderHeight)
            ? configuredHeaderHeight
            : stickyHeader.offsetHeight;
        const shouldUseStickyBar = homeHero instanceof HTMLElement
            ? homeHero.getBoundingClientRect().bottom <= headerHeight
            : window.scrollY > 8;

        stickyHeader.classList.toggle('is-scrolled', shouldUseStickyBar);
    };

    syncStickyHeaderState();
    window.addEventListener('scroll', syncStickyHeaderState, { passive: true });
    window.addEventListener('resize', syncStickyHeaderState);

    document.querySelectorAll('[data-newsletter-form]').forEach(function (form) {
        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        const email = form.querySelector('input[type="email"]');
        const error = form.querySelector('[data-newsletter-error]');

        if (!(email instanceof HTMLInputElement) || !(error instanceof HTMLElement)) {
            return;
        }

        const clearError = function () {
            email.setAttribute('aria-invalid', 'false');
            error.textContent = '';
            error.hidden = true;
        };

        const validateEmail = function () {
            const value = email.value.trim();
            let message = '';

            if (value === '') {
                message = form.dataset.msgEmailRequired || '';
            } else if (!email.validity.valid) {
                message = form.dataset.msgEmailInvalid || '';
            }

            if (message === '') {
                clearError();
                return true;
            }

            email.setAttribute('aria-invalid', 'true');
            error.textContent = message;
            error.hidden = false;
            return false;
        };

        form.addEventListener('submit', function (event) {
            if (!validateEmail()) {
                event.preventDefault();
                email.focus();
            }
        });

        email.addEventListener('input', function () {
            if (email.getAttribute('aria-invalid') === 'true') {
                validateEmail();
            }
        });

        clearError();
    });

    const setSubmenuOpen = function (open, moveFocus) {
        if (!(mobileMenu instanceof HTMLElement)) {
            return;
        }

        mobileMenu.classList.toggle('submenu-is-open', open);
        submenuOpen?.setAttribute('aria-expanded', open ? 'true' : 'false');
        rootMenuPanel?.setAttribute('aria-hidden', open ? 'true' : 'false');
        servicesMenuPanel?.setAttribute('aria-hidden', open ? 'false' : 'true');

        if (!moveFocus) {
            return;
        }

        window.setTimeout(function () {
            const focusTarget = open ? submenuClose : submenuOpen;

            if (focusTarget instanceof HTMLElement) {
                focusTarget.focus();
            }
        }, 360);
    };

    const setMenuOpen = function (open) {
        if (!(menuToggle instanceof HTMLElement) || !(mobileMenu instanceof HTMLElement)) {
            return;
        }

        setSubmenuOpen(open && mobileMenu.dataset.alphaInitialPanel === 'services', false);

        body.classList.toggle('menu-is-open', open);
        body.classList.toggle('mobile-menu-open', open);
        mobileMenu.classList.toggle('is-open', open);
        mobileMenu.setAttribute('aria-hidden', open ? 'false' : 'true');
        menuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        menuToggle.setAttribute('aria-label', open ? 'Zatvori izbornik' : 'Otvori izbornik');
    };

    if (menuToggle instanceof HTMLElement) {
        menuToggle.addEventListener('click', function () {
            setMenuOpen(menuToggle.getAttribute('aria-expanded') !== 'true');
        });
    }

    submenuOpen?.addEventListener('click', function (event) {
        const isKeyboardActivation = event.detail === 0;

        setSubmenuOpen(true, isKeyboardActivation);

        if (!isKeyboardActivation && event.currentTarget instanceof HTMLElement) {
            event.currentTarget.blur();
        }
    });

    submenuClose?.addEventListener('click', function (event) {
        const isKeyboardActivation = event.detail === 0;

        setSubmenuOpen(false, isKeyboardActivation);

        if (!isKeyboardActivation && event.currentTarget instanceof HTMLElement) {
            event.currentTarget.blur();
        }
    });

    mobileMenu?.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () {
            setMenuOpen(false);
        });
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            if (mobileMenu?.classList.contains('submenu-is-open')) {
                setSubmenuOpen(false, true);
                return;
            }

            setMenuOpen(false);

            if (menuToggle instanceof HTMLElement) {
                menuToggle.focus();
            }
        }
    });

    if (searchToggle instanceof HTMLElement && searchPanel instanceof HTMLElement) {
        const syncSearchExpanded = function () {
            searchToggle.setAttribute('aria-expanded', searchPanel.classList.contains('is-open') ? 'true' : 'false');
        };
        searchToggle.addEventListener('click', function () {
            window.setTimeout(syncSearchExpanded, 0);
        });
    }

    const revealIntro = function () {
        window.requestAnimationFrame(function () {
            window.requestAnimationFrame(function () {
                body.classList.add('intro-ready');
            });
        });
    };

    const fontReady = document.fonts?.load('450 96px "Bodoni Moda Variable"').catch(function () { return []; }) ?? Promise.resolve();
    const videoReady = new Promise(function (resolve) {
        if (!(video instanceof HTMLVideoElement) || video.readyState >= HTMLMediaElement.HAVE_FUTURE_DATA) {
            resolve();
            return;
        }

        let timeout = window.setTimeout(resolve, 1600);
        const finish = function () {
            window.clearTimeout(timeout);
            video.removeEventListener('canplay', finish);
            video.removeEventListener('error', finish);
            resolve();
        };
        video.addEventListener('canplay', finish, { once: true });
        video.addEventListener('error', finish, { once: true });
    });

    Promise.all([fontReady, videoReady]).then(revealIntro, revealIntro);

    const headings = Array.from(document.querySelectorAll('[data-words-slide-from-right]'));
    const imageReveals = Array.from(document.querySelectorAll('[data-image-reveal]'));
    const processReveals = Array.from(document.querySelectorAll('[data-process-reveal]'));
    const locationsReveals = Array.from(document.querySelectorAll('[data-locations-reveal]'));
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const supportsRevealObserver = 'IntersectionObserver' in window;
    const countFrames = new Set();
    let frame = 0;

    imageReveals.forEach(function (element) {
        element.querySelectorAll('.service-card-media, .image-reveal-media').forEach(function (media) {
            const image = media.querySelector('img');

            if (!(image instanceof HTMLImageElement) || !(media instanceof HTMLElement)) {
                return;
            }

            const markLoaded = function () { media.classList.add('is-loaded'); };
            if (image.complete && image.naturalWidth > 0) {
                markLoaded();
            } else {
                image.addEventListener('load', markLoaded, { once: true });
            }
        });
    });

    const animateCounters = function (stats) {
        if (!(stats instanceof HTMLElement) || stats.dataset.countStarted === 'true') {
            return;
        }
        stats.dataset.countStarted = 'true';
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        stats.querySelectorAll('[data-count-target]').forEach(function (counter, index) {
            const target = Number(counter.dataset.countTarget || 0);
            if (reduceMotion) {
                counter.textContent = String(target);
                return;
            }

            const start = window.performance.now() + (index * 90);
            const update = function (time) {
                if (time < start) {
                    const pending = window.requestAnimationFrame(update);
                    countFrames.add(pending);
                    return;
                }
                const progress = Math.min((time - start) / 1450, 1);
                counter.textContent = String(Math.round(target * (1 - Math.pow(1 - progress, 4))));
                if (progress < 1) {
                    const pending = window.requestAnimationFrame(update);
                    countFrames.add(pending);
                }
            };
            const pending = window.requestAnimationFrame(update);
            countFrames.add(pending);
        });
    };

    const revealOnEntry = function (elements, className, bottomOffset) {
        if (reduceMotion || !supportsRevealObserver) {
            elements.forEach(function (element) {
                element.classList.add(className);
            });
            return;
        }

        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) {
                    return;
                }

                entry.target.classList.add(className);
                observer.unobserve(entry.target);
            });
        }, {
            root: null,
            rootMargin: `0px 0px -${bottomOffset}% 0px`,
            threshold: 0.01,
        });

        elements.forEach(function (element) {
            observer.observe(element);
        });
    };

    revealOnEntry(headings, 'is-revealed', 20);
    revealOnEntry(imageReveals, 'is-image-revealed', 18);

    const updateReveal = function () {
        frame = 0;
        const viewportHeight = window.innerHeight;

        if (!supportsRevealObserver) {
            headings.forEach(function (heading) {
                const top = heading.getBoundingClientRect().top;
                heading.classList.toggle('is-revealed', top <= viewportHeight * 0.8 || top < 0);
            });

            imageReveals.forEach(function (element) {
                const top = element.getBoundingClientRect().top;
                element.classList.toggle('is-image-revealed', top <= viewportHeight * 0.82 || top < 0);
            });
        }

        processReveals.forEach(function (element) {
            const target = element.querySelector('.process-track') || element;
            element.classList.toggle('is-process-revealed', target.getBoundingClientRect().top <= viewportHeight * 0.78);
        });

        locationsReveals.forEach(function (element) {
            const target = element.querySelector('.locations-layout') || element;
            element.classList.toggle('is-locations-revealed', target.getBoundingClientRect().top <= viewportHeight * 0.76);
            const stats = element.querySelector('.locations-stats');
            if (stats instanceof HTMLElement && stats.getBoundingClientRect().top <= viewportHeight * 0.84) {
                stats.classList.add('is-stats-revealed');
                animateCounters(stats);
            }
        });
    };

    const scheduleReveal = function () {
        if (!frame) {
            frame = window.requestAnimationFrame(updateReveal);
        }
    };

    updateReveal();
    window.addEventListener('scroll', scheduleReveal, { passive: true });
    window.addEventListener('resize', scheduleReveal);
    window.addEventListener('load', scheduleReveal, { once: true });
    fontReady.then(scheduleReveal, scheduleReveal);

    if (video instanceof HTMLVideoElement && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver(function (entries) {
            if (entries[0]?.isIntersecting) {
                video.play().catch(function () {});
            } else {
                video.pause();
            }
        }, { threshold: 0.08 });
        observer.observe(video);
    }

    const locationTriggers = Array.from(document.querySelectorAll('[data-location-index]'));
    const setLocation = function (index) {
        locationTriggers.forEach(function (trigger) {
            const isActive = trigger.dataset.locationIndex === index;
            trigger.classList.toggle('is-active', isActive && trigger.classList.contains('map-location'));
            trigger.setAttribute('aria-expanded', isActive ? 'true' : 'false');

            const article = trigger.closest('.location-address');
            if (article instanceof HTMLElement) {
                article.classList.toggle('is-open', isActive);
                const details = article.querySelector('.location-details');
                if (details instanceof HTMLElement) {
                    details.setAttribute('aria-hidden', isActive ? 'false' : 'true');
                    details.toggleAttribute('inert', !isActive);
                    details.querySelectorAll('a').forEach(function (link) {
                        link.tabIndex = isActive ? 0 : -1;
                    });
                }
            }
        });

        document.querySelectorAll('.map-location').forEach(function (marker) {
            const isActive = marker.dataset.locationIndex === index;
            marker.classList.toggle('is-active', isActive);
            marker.setAttribute('aria-expanded', isActive ? 'true' : 'false');
        });

        document.querySelector('.locations-map')?.classList.toggle('has-active-location', index !== '');
    };

    locationTriggers.forEach(function (trigger) {
        trigger.addEventListener('click', function () {
            const index = trigger.dataset.locationIndex || '';
            setLocation(trigger.getAttribute('aria-expanded') === 'true' ? '' : index);
        });
    });
});
