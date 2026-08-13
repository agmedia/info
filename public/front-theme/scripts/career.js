document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('[data-career-form]');
    const openingsLink = document.querySelector('.ac-career-primary-cta[href="#career-open-positions"]');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    const getHeaderOffset = function () {
        const stickyHeader = document.querySelector('[data-front-sticky-header]');

        return stickyHeader instanceof HTMLElement
            ? Math.round(stickyHeader.getBoundingClientRect().height) + 18
            : 18;
    };

    const scrollToElement = function (target) {
        if (!(target instanceof HTMLElement)) {
            return;
        }

        const targetTop = window.pageYOffset + target.getBoundingClientRect().top - getHeaderOffset();

        if (typeof window.__frontAnimateScrollTo === 'function') {
            window.__frontAnimateScrollTo(targetTop);
            return;
        }

        window.scrollTo({
            top: Math.max(0, targetTop),
            behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth',
        });
    };

    if (openingsLink instanceof HTMLAnchorElement) {
        openingsLink.addEventListener('click', function (event) {
            const target = document.getElementById('career-open-positions');

            if (!(target instanceof HTMLElement)) {
                return;
            }

            event.preventDefault();
            scrollToElement(target);
        });
    }

    if (!(form instanceof HTMLFormElement)) {
        return;
    }

    const cvInput = form.querySelector('[name="cv"]');
    const fileNameNode = form.querySelector('[data-file-name]');
    const defaultFileLabel = form.dataset.fileEmptyLabel || '';

    const updateSelectedFileName = function () {
        if (!(fileNameNode instanceof HTMLElement)) {
            return;
        }

        const file = cvInput instanceof HTMLInputElement && cvInput.files?.[0]
            ? cvInput.files[0]
            : null;
        fileNameNode.textContent = file ? file.name : defaultFileLabel;
    };

    const clearError = function (field) {
        const errorNode = form.querySelector('[data-field-error="' + field + '"]');

        if (!(errorNode instanceof HTMLElement)) {
            return;
        }

        errorNode.textContent = '';
        errorNode.classList.add('hidden');
    };

    const setError = function (field, message) {
        const errorNode = form.querySelector('[data-field-error="' + field + '"]');

        if (!(errorNode instanceof HTMLElement)) {
            return;
        }

        errorNode.textContent = message;
        errorNode.classList.remove('hidden');
    };

    form.querySelectorAll('[data-field-error]').forEach(function (node) {
        node.classList.toggle('hidden', (node.textContent || '').trim() === '');
    });

    updateSelectedFileName();

    if (form.dataset.scrollOnLoad === 'true') {
        window.requestAnimationFrame(function () {
            scrollToElement(document.getElementById('career-cta') || form);
        });
    }

    const validate = function () {
        ['first_name', 'last_name', 'email', 'cv', 'accept_terms', 'recaptcha_token'].forEach(clearError);

        const firstName = form.querySelector('[name="first_name"]');
        const lastName = form.querySelector('[name="last_name"]');
        const email = form.querySelector('[name="email"]');
        const cv = form.querySelector('[name="cv"]');
        const acceptTerms = form.querySelector('[name="accept_terms"]');
        let valid = true;

        if (!(firstName instanceof HTMLInputElement) || firstName.value.trim() === '') {
            setError('first_name', form.dataset.msgFirstNameRequired || '');
            valid = false;
        }

        if (!(lastName instanceof HTMLInputElement) || lastName.value.trim() === '') {
            setError('last_name', form.dataset.msgLastNameRequired || '');
            valid = false;
        }

        const emailValue = email instanceof HTMLInputElement ? email.value.trim() : '';
        if (emailValue === '') {
            setError('email', form.dataset.msgEmailRequired || '');
            valid = false;
        } else if (!emailRegex.test(emailValue)) {
            setError('email', form.dataset.msgEmailInvalid || '');
            valid = false;
        }

        if (!(cv instanceof HTMLInputElement) || !cv.files || cv.files.length === 0) {
            setError('cv', form.dataset.msgCvRequired || '');
            valid = false;
        }

        if (!(acceptTerms instanceof HTMLInputElement) || !acceptTerms.checked) {
            setError('accept_terms', form.dataset.msgAcceptTerms || '');
            valid = false;
        }

        return valid;
    };

    if (cvInput instanceof HTMLInputElement) {
        cvInput.addEventListener('change', function () {
            updateSelectedFileName();

            if (cvInput.files && cvInput.files.length > 0) {
                clearError('cv');
            }
        });
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        if (!validate()) {
            scrollToElement(document.getElementById('career-cta') || form);
            return;
        }

        const tokenInput = form.querySelector('[data-recaptcha-token]');
        const siteKey = form.dataset.recaptchaSiteKey;
        const action = form.dataset.recaptchaAction || 'career_application_form';

        if (!(tokenInput instanceof HTMLInputElement) || !window.grecaptcha || !siteKey) {
            form.submit();
            return;
        }

        window.grecaptcha.ready(function () {
            window.grecaptcha.execute(siteKey, { action: action }).then(function (token) {
                tokenInput.value = token || '';
                form.submit();
            });
        });
    });
});
