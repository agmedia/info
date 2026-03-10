<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($captchaEnabled): ?>
    <?php $__env->startPush('scripts'); ?>
        <script src="https://www.google.com/recaptcha/api.js?render=<?php echo e($captchaSiteKey); ?>"></script>
    <?php $__env->stopPush(); ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        (function () {
            const forms = document.querySelectorAll('[data-contact-form]');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            forms.forEach(function (form) {
                const clearError = function (field) {
                    const errorNode = form.querySelector('[data-field-error="' + field + '"]');
                    if (!errorNode) {
                        return;
                    }
                    errorNode.textContent = '';
                    errorNode.classList.add('hidden');
                    errorNode.style.display = 'none';
                };

                const setError = function (field, message) {
                    const errorNode = form.querySelector('[data-field-error="' + field + '"]');
                    if (!errorNode) {
                        return;
                    }
                    errorNode.textContent = message;
                    errorNode.classList.remove('hidden');
                    errorNode.style.display = 'block';
                };

                form.querySelectorAll('[data-field-error]').forEach(function (node) {
                    if ((node.textContent || '').trim() === '') {
                        node.style.display = 'none';
                    } else {
                        node.style.display = 'block';
                        node.classList.remove('hidden');
                    }
                });

                const validate = function () {
                    ['name', 'first_name', 'email', 'message', 'accept_terms', 'recaptcha_token'].forEach(clearError);

                    const fullName = form.querySelector('[name="name"]');
                    const firstName = form.querySelector('[name="first_name"]');
                    const lastName = form.querySelector('[name="last_name"]');
                    const email = form.querySelector('[name="email"]');
                    const message = form.querySelector('[name="message"]');
                    const acceptTerms = form.querySelector('[name="accept_terms"]');
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

                    grecaptcha.ready(function () {
                        grecaptcha.execute(siteKey, { action: action }).then(function (token) {
                            tokenInput.value = token || '';
                            form.submit();
                        });
                    });
                });
            });
        }());
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH /Users/tomek/Herd/info/resources/views/front/desktop/contact/partials/form-script.blade.php ENDPATH**/ ?>