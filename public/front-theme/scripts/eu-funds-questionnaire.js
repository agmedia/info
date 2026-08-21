(function () {
    const forms = document.querySelectorAll('[data-eu-funds-questionnaire-form]');

    const toggleTargets = function (targets, active) {
        targets.forEach(function (target) {
            target.classList.toggle('hidden', !active);
            target.setAttribute('aria-hidden', active ? 'false' : 'true');
        });
    };

    const bindConditionalFields = function (form) {
        const relatedTargets = form.querySelectorAll('[data-conditional-target="related_companies"]');
        const relatedInputs = form.querySelectorAll('[data-conditional-toggle="related_companies"]');
        const sectorOtherTargets = form.querySelectorAll('[data-conditional-target="project_sector_other"]');
        const sectorOtherCheckboxes = form.querySelectorAll('[data-conditional-checkbox="project_sector_other"]');

        const updateRelatedCompanies = function () {
            const active = Array.from(relatedInputs).some(function (input) {
                return input.checked && input.value === 'yes';
            });

            toggleTargets(relatedTargets, active);
        };

        const updateProjectSectorOther = function () {
            const active = Array.from(sectorOtherCheckboxes).some(function (input) {
                return input.checked;
            });

            toggleTargets(sectorOtherTargets, active);
        };

        relatedInputs.forEach(function (input) {
            input.addEventListener('change', updateRelatedCompanies);
        });
        sectorOtherCheckboxes.forEach(function (input) {
            input.addEventListener('change', updateProjectSectorOther);
        });

        updateRelatedCompanies();
        updateProjectSectorOther();
    };

    forms.forEach(function (form) {
        bindConditionalFields(form);

        form.addEventListener('submit', function (event) {
            const tokenInput = form.querySelector('[data-recaptcha-token]');
            const siteKey = form.dataset.recaptchaSiteKey;
            const action = form.dataset.recaptchaAction || 'eu_funds_questionnaire_form';

            if (!tokenInput || !window.grecaptcha || !siteKey) {
                return;
            }

            event.preventDefault();

            window.grecaptcha.ready(function () {
                window.grecaptcha.execute(siteKey, { action: action }).then(function (token) {
                    tokenInput.value = token || '';
                    form.submit();
                });
            });
        });
    });
}());
