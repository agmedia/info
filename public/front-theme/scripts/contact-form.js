(function () {
    'use strict';

    const forms = document.querySelectorAll('[data-contact-form]');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    forms.forEach(function (form) {
        if (form.dataset.contactValidationReady === 'true') {
            return;
        }

        form.dataset.contactValidationReady = 'true';

        const getField = function (field) {
            return form.querySelector('[name="' + field + '"]');
        };

        const clearError = function (field) {
            const errorNode = form.querySelector('[data-field-error="' + field + '"]');
            const fieldNode = getField(field);

            if (errorNode) {
                errorNode.textContent = '';
                errorNode.hidden = true;
                errorNode.classList.add('hidden');
            }

            if (fieldNode) {
                fieldNode.setAttribute('aria-invalid', 'false');
            }
        };

        const setError = function (field, message) {
            const errorNode = form.querySelector('[data-field-error="' + field + '"]');
            const fieldNode = getField(field);

            if (errorNode) {
                errorNode.textContent = message;
                errorNode.hidden = false;
                errorNode.classList.remove('hidden');
            }

            if (fieldNode) {
                fieldNode.setAttribute('aria-invalid', 'true');
            }
        };

        form.querySelectorAll('[data-field-error]').forEach(function (node) {
            node.hidden = (node.textContent || '').trim() === '';
            node.classList.toggle('hidden', node.hidden);
        });

        const validate = function () {
            ['name', 'first_name', 'email', 'message', 'accept_terms', 'recaptcha_token'].forEach(clearError);

            const fullName = getField('name');
            const firstName = getField('first_name');
            const lastName = getField('last_name');
            const email = getField('email');
            const message = getField('message');
            const acceptTerms = getField('accept_terms');
            let valid = true;

            if (firstName) {
                const firstNameValue = firstName.value.trim();

                if (firstNameValue === '') {
                    setError('first_name', form.dataset.msgNameRequired || '');
                    valid = false;
                }

                if (fullName) {
                    const nameParts = [firstNameValue, lastName ? lastName.value.trim() : ''].filter(Boolean);
                    fullName.value = nameParts.join(' ');
                }
            } else if (!fullName || fullName.value.trim() === '') {
                setError('name', form.dataset.msgNameRequired || '');
                valid = false;
            }

            const emailValue = email ? email.value.trim() : '';
            if (emailValue === '') {
                setError('email', form.dataset.msgEmailRequired || '');
                valid = false;
            } else if (!emailRegex.test(emailValue)) {
                setError('email', form.dataset.msgEmailInvalid || '');
                valid = false;
            }

            const messageValue = message ? message.value.trim() : '';
            if (messageValue === '') {
                setError('message', form.dataset.msgMessageRequired || '');
                valid = false;
            } else if (messageValue.length < 10) {
                setError('message', form.dataset.msgMessageMin || '');
                valid = false;
            }

            if (!acceptTerms || !acceptTerms.checked) {
                setError('accept_terms', form.dataset.msgAcceptTerms || '');
                valid = false;
            }

            return valid;
        };

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            if (!validate()) {
                return;
            }

            const tokenInput = form.querySelector('[data-recaptcha-token]');
            const siteKey = form.dataset.recaptchaSiteKey;
            const action = form.dataset.recaptchaAction || 'contact_form';
            if (!tokenInput || !window.grecaptcha || !siteKey) {
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
}());
