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
    const searchForm = searchPanel?.querySelector('[data-header-search-form]');
    const searchInput = searchPanel?.querySelector('[data-header-search-input]');
    const searchSuggestions = searchPanel?.querySelector('[data-header-search-suggestions]');
    let searchDebounceTimer = 0;
    let searchRequestId = 0;
    let searchAbortController = null;

    const activateDeferredStylesheets = function () {
        document.querySelectorAll('link[data-deferred-stylesheet]').forEach(function (stylesheet) {
            stylesheet.media = 'all';
        });
    };

    if (document.readyState === 'complete') {
        activateDeferredStylesheets();
    } else {
        window.addEventListener('load', activateDeferredStylesheets, { once: true });
    }

    const syncStickyHeaderState = function () {
        if (!(stickyHeader instanceof HTMLElement)) {
            return;
        }

        const shouldUseStickyBar = window.scrollY > 0;

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

    const hideSearchSuggestions = function () {
        if (!(searchSuggestions instanceof HTMLElement)) {
            return;
        }

        searchSuggestions.classList.add('hidden');
        searchSuggestions.replaceChildren();
    };

    const createSearchSuggestion = function (item) {
        const link = document.createElement('a');
        const body = document.createElement('span');
        const title = document.createElement('strong');

        link.className = 'front-search-suggestion-link';
        link.href = item.url || '#';
        body.className = 'front-search-suggestion-body';
        title.className = 'front-search-suggestion-title';
        title.textContent = item.title || '';

        if (item.image_url) {
            const media = document.createElement('span');
            const image = document.createElement('img');
            media.className = 'front-search-suggestion-media';
            image.src = item.image_url;
            image.alt = item.title || '';
            image.loading = 'lazy';
            image.decoding = 'async';
            media.append(image);
            link.append(media);
        }

        if (item.eyebrow || item.meta) {
            const meta = document.createElement('span');
            meta.className = 'front-search-suggestion-meta';

            [item.eyebrow, item.meta].filter(Boolean).forEach(function (value) {
                const label = document.createElement('span');
                label.textContent = value;
                meta.append(label);
            });

            body.append(meta);
        }

        body.append(title);

        if (item.excerpt) {
            const excerpt = document.createElement('span');
            excerpt.className = 'front-search-suggestion-excerpt';
            excerpt.textContent = item.excerpt;
            body.append(excerpt);
        }

        link.append(body);
        return link;
    };

    const renderSearchSuggestions = function (payload) {
        if (!(searchSuggestions instanceof HTMLElement)) {
            return;
        }

        searchSuggestions.replaceChildren();
        const sections = Array.isArray(payload?.sections) ? payload.sections : [];

        if (sections.length === 0) {
            const empty = document.createElement('div');
            empty.className = 'front-search-suggestion-empty';
            empty.textContent = window.CodexSearchLabels?.autosuggestEmpty || 'Nema rezultata.';
            searchSuggestions.append(empty);
            searchSuggestions.classList.remove('hidden');
            return;
        }

        sections.forEach(function (section) {
            const sectionElement = document.createElement('section');
            const heading = document.createElement('div');
            const headingTitle = document.createElement('h3');
            const list = document.createElement('div');

            sectionElement.className = 'front-search-suggestion-section';
            heading.className = 'front-search-suggestion-section-head';
            headingTitle.textContent = section.label || '';
            list.className = 'front-search-suggestion-list';
            heading.append(headingTitle);

            if (typeof section.total_count === 'number') {
                const count = document.createElement('span');
                count.textContent = String(section.total_count);
                heading.append(count);
            }

            (Array.isArray(section.items) ? section.items : []).forEach(function (item) {
                list.append(createSearchSuggestion(item));
            });

            sectionElement.append(heading, list);
            searchSuggestions.append(sectionElement);
        });

        if (payload?.results_url) {
            const footer = document.createElement('div');
            const viewAll = document.createElement('a');
            footer.className = 'front-search-suggestion-footer';
            viewAll.className = 'front-search-suggestion-all';
            viewAll.href = payload.results_url;
            viewAll.textContent = window.CodexSearchLabels?.showMore || 'Prikaži više';
            footer.append(viewAll);
            searchSuggestions.append(footer);
        }

        searchSuggestions.classList.remove('hidden');
    };

    const fetchSearchSuggestions = function (rawQuery) {
        const query = typeof rawQuery === 'string' ? rawQuery.trim() : '';
        const endpoint = searchForm instanceof HTMLFormElement ? searchForm.dataset.searchSuggestEndpoint || '' : '';

        window.clearTimeout(searchDebounceTimer);

        if (!(searchInput instanceof HTMLInputElement) || query.length < 2 || endpoint === '') {
            searchAbortController?.abort();
            searchAbortController = null;
            hideSearchSuggestions();
            return;
        }

        searchDebounceTimer = window.setTimeout(async function () {
            searchAbortController?.abort();
            searchAbortController = new AbortController();
            const currentRequestId = ++searchRequestId;

            try {
                const url = new URL(endpoint, window.location.origin);
                url.searchParams.set('q', query);
                const response = await window.fetch(url.toString(), {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    signal: searchAbortController.signal,
                });

                if (!response.ok) {
                    throw new Error(`Search suggest failed with status ${response.status}`);
                }

                const payload = await response.json();
                if (currentRequestId === searchRequestId && searchInput.value.trim() === query) {
                    renderSearchSuggestions(payload);
                }
            } catch (error) {
                if (error?.name !== 'AbortError') {
                    hideSearchSuggestions();
                }
            }
        }, 180);
    };

    const setSearchOpen = function (open) {
        if (!(searchPanel instanceof HTMLElement)) {
            return;
        }

        searchPanel.classList.toggle('is-open', open);
        stickyHeader?.classList.toggle('is-search-open', open);
        searchToggle?.setAttribute('aria-expanded', open ? 'true' : 'false');

        if (!open) {
            hideSearchSuggestions();
            return;
        }

        setMenuOpen(false);
        window.requestAnimationFrame(function () {
            searchInput?.focus();
        });
    };

    searchToggle?.addEventListener('click', function () {
        setSearchOpen(!searchPanel?.classList.contains('is-open'));
    });

    searchInput?.addEventListener('input', function (event) {
        fetchSearchSuggestions(event.currentTarget?.value || '');
    });

    searchInput?.addEventListener('focus', function () {
        fetchSearchSuggestions(searchInput.value);
    });

    searchForm?.addEventListener('submit', function (event) {
        if (searchInput instanceof HTMLInputElement && searchInput.value.trim() === '') {
            event.preventDefault();
            searchInput.focus();
        }
    });

    document.addEventListener('click', function (event) {
        if (!(event.target instanceof Node) || !(searchPanel instanceof HTMLElement)) {
            return;
        }

        if (!searchPanel.contains(event.target) && !searchToggle?.contains(event.target)) {
            hideSearchSuggestions();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            if (searchPanel?.classList.contains('is-open')) {
                setSearchOpen(false);
                searchToggle?.focus();
                return;
            }

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

    const revealIntro = function () {
        window.requestAnimationFrame(function () {
            window.requestAnimationFrame(function () {
                body.classList.add('intro-ready');
            });
        });
    };

    const heroTitle = homeHero?.querySelector('h1');
    const heroTitleText = heroTitle?.textContent?.trim() || 'VAŠ KOMPAS KROZ SVIJET FINANCIJA';
    const heroTitleStyle = heroTitle instanceof HTMLElement ? window.getComputedStyle(heroTitle) : null;
    const heroFontDescriptor = heroTitleStyle
        ? heroTitleStyle.fontWeight + ' ' + heroTitleStyle.fontSize + ' ' + heroTitleStyle.fontFamily
        : '450 96px "Bodoni Moda Variable"';
    const globalFontFamily = window.getComputedStyle(body).getPropertyValue('--front-font-family').trim();
    const fontReady = document.fonts?.load(heroFontDescriptor, heroTitleText).catch(function () { return []; }) ?? Promise.resolve();
    const globalFontReady = homeHero instanceof HTMLElement && document.fonts && globalFontFamily !== ''
        ? document.fonts.load('500 20px ' + globalFontFamily, 'POČETNA USLUGE O NAMA KARIJERA OBJAVE KONTAKT').catch(function () { return []; })
        : Promise.resolve();
    const layoutFontsReady = document.fonts?.ready ?? Promise.resolve();
    const heroFontReady = homeHero instanceof HTMLElement && document.fonts
        ? Promise.race([
            Promise.all([fontReady, globalFontReady, layoutFontsReady]),
            new Promise(function (resolve) { window.setTimeout(resolve, 1800); }),
        ])
        : Promise.resolve();
    const shouldLoadHeroVideo = video instanceof HTMLVideoElement
        && !window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const loadHeroVideo = function () {
        if (!shouldLoadHeroVideo || video.querySelector('source')) {
            return;
        }

        const isMobileHero = window.matchMedia('(max-width: 767px)').matches;
        const videoSourceUrl = isMobileHero
            ? video.dataset.alphaHeroVideoMobileSrc || ''
            : video.dataset.alphaHeroVideoDesktopSrc || '';
        const videoSourceType = isMobileHero
            ? video.dataset.alphaHeroVideoMobileType || 'video/mp4'
            : video.dataset.alphaHeroVideoDesktopType || 'video/mp4';

        if (videoSourceUrl === '') {
            return;
        }

        const source = document.createElement('source');
        const markVideoReady = function () {
            video.classList.add('is-ready');
            video.play().catch(function () {});
        };

        source.src = videoSourceUrl;
        source.type = videoSourceType;
        video.append(source);
        video.addEventListener('loadeddata', markVideoReady, { once: true });
        video.load();
    };

    if (document.readyState === 'complete') {
        loadHeroVideo();
    } else {
        window.addEventListener('load', loadHeroVideo, { once: true });
    }

    heroFontReady.then(revealIntro, revealIntro);

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

    if (shouldLoadHeroVideo && video instanceof HTMLVideoElement && 'IntersectionObserver' in window) {
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
