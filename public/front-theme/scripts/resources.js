(function () {
    'use strict';

    function initResourceRequestForm() {
        var form = document.querySelector('[data-resource-request-form]');
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        function clearError(field) {
            var errorNode = form.querySelector('[data-field-error="' + field + '"]');

            if (!errorNode) {
                return;
            }

            errorNode.textContent = '';
            errorNode.classList.add('hidden');
        }

        function setError(field, message) {
            var errorNode = form.querySelector('[data-field-error="' + field + '"]');

            if (!errorNode) {
                return;
            }

            errorNode.textContent = message;
            errorNode.classList.remove('hidden');
        }

        function getHeaderOffset() {
            var stickyHeader = document.querySelector('[data-front-sticky-header]');

            if (!(stickyHeader instanceof HTMLElement)) {
                return 18;
            }

            return Math.round(stickyHeader.getBoundingClientRect().height) + 18;
        }

        function scrollToForm() {
            var target = document.getElementById('resource-request-form') || form;

            if (!(target instanceof HTMLElement)) {
                return;
            }

            var targetTop = window.pageYOffset + target.getBoundingClientRect().top - getHeaderOffset();

            if (typeof window.__frontAnimateScrollTo === 'function') {
                window.__frontAnimateScrollTo(targetTop);
                return;
            }

            window.scrollTo(0, Math.max(0, targetTop));
        }

        function syncFieldState(field) {
            if (!(field instanceof HTMLElement)) {
                return;
            }

            var checkbox = field.querySelector('input[type="checkbox"]');

            if (checkbox instanceof HTMLInputElement) {
                field.classList.toggle('is-selected', checkbox.checked);
                return;
            }

            var input = field.querySelector('input, textarea, select');

            if (!(input instanceof HTMLInputElement || input instanceof HTMLTextAreaElement || input instanceof HTMLSelectElement)) {
                return;
            }

            field.classList.toggle('is-filled', input.value.trim() !== '');
        }

        function bindFieldState(field) {
            if (!(field instanceof HTMLElement)) {
                return;
            }

            var checkbox = field.querySelector('input[type="checkbox"]');

            if (checkbox instanceof HTMLInputElement) {
                checkbox.addEventListener('change', function () {
                    syncFieldState(field);
                });
                syncFieldState(field);
                return;
            }

            var input = field.querySelector('input, textarea, select');

            if (!(input instanceof HTMLInputElement || input instanceof HTMLTextAreaElement || input instanceof HTMLSelectElement)) {
                return;
            }

            input.addEventListener('focus', function () {
                field.classList.add('is-active');
            });

            input.addEventListener('blur', function () {
                field.classList.remove('is-active');
                syncFieldState(field);
            });

            ['input', 'change'].forEach(function (eventName) {
                input.addEventListener(eventName, function () {
                    syncFieldState(field);
                });
            });

            syncFieldState(field);
        }

        form.querySelectorAll('[data-resource-field], [data-resource-consent-field]').forEach(bindFieldState);

        if (form.dataset.shouldFocusForm === 'true') {
            window.requestAnimationFrame(scrollToForm);
        }

        form.addEventListener('submit', function (event) {
            event.preventDefault();

            ['name', 'email', 'accept_terms', 'recaptcha_token'].forEach(clearError);

            var nameField = form.querySelector('[name="name"]');
            var emailField = form.querySelector('[name="email"]');
            var acceptTerms = form.querySelector('[name="accept_terms"]');
            var valid = true;

            if (!(nameField instanceof HTMLInputElement) || nameField.value.trim() === '') {
                setError('name', form.dataset.msgNameRequired || '');
                valid = false;
            }

            var emailValue = emailField instanceof HTMLInputElement ? emailField.value.trim() : '';

            if (emailValue === '') {
                setError('email', form.dataset.msgEmailRequired || '');
                valid = false;
            } else if (!emailRegex.test(emailValue)) {
                setError('email', form.dataset.msgEmailInvalid || '');
                valid = false;
            }

            if (!(acceptTerms instanceof HTMLInputElement) || !acceptTerms.checked) {
                setError('accept_terms', form.dataset.msgAcceptTerms || '');
                valid = false;
            }

            if (!valid) {
                scrollToForm();
                return;
            }

            var tokenInput = form.querySelector('[data-recaptcha-token]');
            var siteKey = form.dataset.recaptchaSiteKey;
            var action = form.dataset.recaptchaAction || 'resource_download_request';

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
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initResourceRequestForm, { once: true });
    } else {
        initResourceRequestForm();
    }
}());
