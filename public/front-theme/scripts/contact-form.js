(function () {
    'use strict';

    const forms = document.querySelectorAll('[data-contact-form]');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    forms.forEach(function (form) {
        if (form.dataset.contactValidationReady === 'true') {
            return;
        }

        form.dataset.contactValidationReady = 'true';

        const submitButton = form.querySelector('button[type="submit"]');
        const submitLabel = form.querySelector('[data-contact-submit-label]');
        const feedback = form.querySelector('[data-contact-feedback]');
        const feedbackText = form.querySelector('[data-contact-feedback-text]');
        const feedbackIcon = form.querySelector('[data-contact-feedback-icon]');
        const defaultSubmitLabel = (submitLabel?.textContent || '').trim();
        let submitting = false;

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

        const clearErrors = function () {
            form.querySelectorAll('[data-field-error]').forEach(function (node) {
                const field = node.dataset.fieldError;

                if (field) {
                    clearError(field);
                }
            });
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

        const showFeedback = function (message, state) {
            if (!feedback || !feedbackText) {
                return;
            }

            const visible = message.trim() !== '';

            feedbackText.textContent = message;
            feedback.hidden = !visible;
            feedback.classList.toggle('is-error', visible && state === 'error');

            if (feedbackIcon) {
                feedbackIcon.className = state === 'error'
                    ? 'fa-light fa-circle-exclamation'
                    : 'fa-light fa-circle-check';
                feedbackIcon.dataset.contactFeedbackIcon = '';
                feedbackIcon.setAttribute('aria-hidden', 'true');
            }
        };

        const setSubmitting = function (active) {
            submitting = active;
            form.setAttribute('aria-busy', active ? 'true' : 'false');

            if (submitButton) {
                submitButton.disabled = active;
            }

            if (submitLabel) {
                submitLabel.textContent = active
                    ? (form.dataset.msgSubmitting || defaultSubmitLabel)
                    : defaultSubmitLabel;
            }
        };

        form.querySelectorAll('[data-field-error]').forEach(function (node) {
            node.hidden = (node.textContent || '').trim() === '';
            node.classList.toggle('hidden', node.hidden);
        });

        const validate = function () {
            clearErrors();

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

        const firstVisibleError = function () {
            const error = Array.from(form.querySelectorAll('[data-field-error]')).find(function (node) {
                return !node.hidden && (node.textContent || '').trim() !== '';
            });

            return (error?.textContent || '').trim();
        };

        const requestRecaptchaToken = function () {
            const tokenInput = form.querySelector('[data-recaptcha-token]');
            const siteKey = form.dataset.recaptchaSiteKey;
            const action = form.dataset.recaptchaAction || 'contact_form';

            if (!siteKey) {
                return Promise.resolve();
            }

            if (!tokenInput || !window.grecaptcha) {
                return Promise.reject(new Error('reCAPTCHA is unavailable.'));
            }

            return new Promise(function (resolve, reject) {
                try {
                    window.grecaptcha.ready(function () {
                        window.grecaptcha.execute(siteKey, { action: action })
                            .then(function (token) {
                                tokenInput.value = token || '';
                                resolve();
                            })
                            .catch(reject);
                    });
                } catch (error) {
                    reject(error);
                }
            });
        };

        const parseJson = async function (response) {
            try {
                return await response.json();
            } catch (error) {
                return {};
            }
        };

        const submitForm = async function () {
            if (submitting) {
                return;
            }

            showFeedback('', 'success');

            if (!validate()) {
                showFeedback(firstVisibleError(), 'error');
                return;
            }

            if (typeof window.fetch !== 'function') {
                showFeedback(form.dataset.msgSubmitFailed || '', 'error');
                return;
            }

            setSubmitting(true);

            try {
                await requestRecaptchaToken();

                const response = await window.fetch(form.action, {
                    method: (form.method || 'POST').toUpperCase(),
                    body: new FormData(form),
                    credentials: 'same-origin',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                const payload = await parseJson(response);

                if (!response.ok || payload.ok === false) {
                    clearErrors();

                    if (payload.errors && typeof payload.errors === 'object') {
                        Object.entries(payload.errors).forEach(function (entry) {
                            const field = entry[0];
                            const messages = Array.isArray(entry[1]) ? entry[1] : [entry[1]];
                            const message = String(messages.find(Boolean) || '');

                            if (message !== '') {
                                setError(field, message);
                            }
                        });
                    }

                    showFeedback(
                        firstVisibleError() || String(payload.message || form.dataset.msgSubmitFailed || ''),
                        'error'
                    );
                    return;
                }

                clearErrors();
                form.reset();

                const tokenInput = form.querySelector('[data-recaptcha-token]');
                if (tokenInput) {
                    tokenInput.value = '';
                }

                showFeedback(String(payload.message || ''), 'success');
            } catch (error) {
                showFeedback(form.dataset.msgSubmitFailed || '', 'error');
            } finally {
                setSubmitting(false);
            }
        };

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            submitForm();
        });
    });
}());
