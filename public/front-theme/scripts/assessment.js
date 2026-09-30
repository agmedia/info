(function () {
    'use strict';

    const init = function () {
        const root = document.querySelector('[data-assessment-form-root]');

        if (!root) {
            return;
        }

        const serviceSelect = root.querySelector('[data-assessment-service-select]');
        const servicePanels = Array.from(root.querySelectorAll('[data-assessment-service-panel]'));
        const advisoryServiceSelect = root.querySelector('[data-advisory-service-select]');
        const advisoryConditionalSection = root.querySelector('[data-advisory-conditional-section]');
        const advisoryConditionalPanels = Array.from(root.querySelectorAll('[data-advisory-conditional]'));
        const targetRelationSelect = root.querySelector('[data-advisory-target-relation]');
        const targetFields = root.querySelector('[data-advisory-target-fields]');
        const relatedCompaniesSelect = root.querySelector('[data-advisory-related-companies]');
        const consolidatedField = root.querySelector('[data-advisory-consolidated-field]');
        const fileInput = root.querySelector('[data-assessment-file-input]');
        const fileSummary = root.querySelector('[data-assessment-file-summary]');

        if (!(serviceSelect instanceof HTMLSelectElement)) {
            return;
        }

        const controlsIn = function (container) {
            if (!(container instanceof HTMLElement)) {
                return [];
            }

            return Array.from(container.querySelectorAll('input, select, textarea, button'));
        };

        const rememberRequiredState = function (control) {
            if (!control.hasAttribute('data-assessment-initially-required')) {
                control.setAttribute('data-assessment-initially-required', control.required ? 'true' : 'false');
            }
        };

        const setContainerState = function (container, active, conditional) {
            if (!(container instanceof HTMLElement)) {
                return;
            }

            container.hidden = !active;
            container.setAttribute('aria-hidden', active ? 'false' : 'true');

            controlsIn(container).forEach(function (control) {
                rememberRequiredState(control);
                control.disabled = !active;

                if (!active) {
                    control.required = false;
                    return;
                }

                control.required = conditional
                    ? control.hasAttribute('data-required-when-active')
                    : control.getAttribute('data-assessment-initially-required') === 'true';
            });
        };

        const advisoryIsActive = function () {
            return serviceSelect.value === 'advisory';
        };

        const syncAdvisoryConditions = function () {
            const selectedService = advisoryServiceSelect instanceof HTMLSelectElement
                ? advisoryServiceSelect.value
                : '';
            const hasSelectedService = advisoryConditionalPanels.some(function (panel) {
                return panel.getAttribute('data-advisory-conditional') === selectedService;
            });
            const advisoryActive = advisoryIsActive();

            if (advisoryConditionalSection instanceof HTMLElement) {
                advisoryConditionalSection.hidden = !advisoryActive || !hasSelectedService;
                advisoryConditionalSection.setAttribute(
                    'aria-hidden',
                    advisoryActive && hasSelectedService ? 'false' : 'true'
                );
            }

            advisoryConditionalPanels.forEach(function (panel) {
                const active = advisoryActive
                    && panel.getAttribute('data-advisory-conditional') === selectedService;

                setContainerState(panel, active, true);
            });
        };

        const syncTargetFields = function () {
            const advisoryActive = advisoryIsActive();
            const differentTarget = advisoryActive
                && targetRelationSelect instanceof HTMLSelectElement
                && targetRelationSelect.value === 'different';
            const hasRelatedCompanies = differentTarget
                && relatedCompaniesSelect instanceof HTMLSelectElement
                && relatedCompaniesSelect.value === 'yes';

            setContainerState(targetFields, differentTarget, true);
            setContainerState(consolidatedField, hasRelatedCompanies, true);
        };

        const syncServicePanels = function () {
            const selectedService = serviceSelect.value;

            servicePanels.forEach(function (panel) {
                const active = panel.getAttribute('data-assessment-service-panel') === selectedService;
                setContainerState(panel, active, false);
            });

            syncAdvisoryConditions();
            syncTargetFields();
        };

        const syncFileSummary = function () {
            if (!(fileInput instanceof HTMLInputElement) || !(fileSummary instanceof HTMLElement)) {
                return;
            }

            const names = Array.from(fileInput.files || []).map(function (file) {
                return file.name;
            });

            fileSummary.textContent = names.join(', ');
        };

        serviceSelect.addEventListener('change', syncServicePanels);

        if (advisoryServiceSelect instanceof HTMLSelectElement) {
            advisoryServiceSelect.addEventListener('change', syncAdvisoryConditions);
        }

        if (targetRelationSelect instanceof HTMLSelectElement) {
            targetRelationSelect.addEventListener('change', syncTargetFields);
        }

        if (relatedCompaniesSelect instanceof HTMLSelectElement) {
            relatedCompaniesSelect.addEventListener('change', syncTargetFields);
        }

        if (fileInput instanceof HTMLInputElement) {
            fileInput.addEventListener('change', syncFileSummary);
        }

        syncServicePanels();
        syncFileSummary();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init, { once: true });
    } else {
        init();
    }
}());
