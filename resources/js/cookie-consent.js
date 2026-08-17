import * as CookieConsent from 'vanilla-cookieconsent';
import 'vanilla-cookieconsent/dist/cookieconsent.css';

const consentConfig = document.querySelector('[data-cookie-consent-config]');

if (consentConfig) {
    const locale = (consentConfig.dataset.locale || document.documentElement.lang || 'hr')
        .toLowerCase()
        .split(/[-_]/)[0];
    const language = locale === 'en' ? 'en' : 'hr';
    const privacyUrl = consentConfig.dataset.privacyUrl || '/politika-privatnosti';
    const policyLabel = consentConfig.dataset.policyLabel
        || (language === 'en' ? 'Read the privacy policy' : 'Pročitajte politiku privatnosti');
    const escapedPrivacyUrl = privacyUrl.replace(/[&<>"']/g, (character) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;',
    })[character]);

    const translations = {
        hr: {
            consentModal: {
                title: 'Kolačići i privatnost',
                description: `Alpha Capitalis koristi nužne kolačiće za ispravan i siguran rad stranice. Uz vašu privolu analitički i marketinški kolačići pomažu nam poboljšati sadržaj i komunikaciju. <a href="${escapedPrivacyUrl}">${policyLabel}</a>.`,
                acceptAllBtn: 'Prihvati sve',
                acceptNecessaryBtn: 'Samo nužni',
                showPreferencesBtn: 'Postavke',
            },
            preferencesModal: {
                title: 'Postavke kolačića',
                acceptAllBtn: 'Prihvati sve',
                acceptNecessaryBtn: 'Samo nužni',
                savePreferencesBtn: 'Spremi odabir',
                closeIconLabel: 'Zatvori postavke',
                sections: [
                    {
                        title: 'Nužni kolačići',
                        description: 'Omogućuju osnovne funkcije stranice, sigurnost i pamćenje vašeg odabira privole. Uvijek su uključeni.',
                        linkedCategory: 'necessary',
                    },
                    {
                        title: 'Analitički kolačići',
                        description: 'Google Analytics 4 pomaže nam razumjeti kako se stranica koristi kako bismo mogli poboljšati njezin sadržaj i funkcionalnost.',
                        linkedCategory: 'analytics',
                    },
                    {
                        title: 'Marketinški kolačići',
                        description: 'Služe mjerenju uspješnosti kampanja i prikazu relevantnije komunikacije na digitalnim kanalima.',
                        linkedCategory: 'marketing',
                    },
                ],
            },
        },
        en: {
            consentModal: {
                title: 'Cookies and privacy',
                description: `Alpha Capitalis uses necessary cookies to keep this website secure and working correctly. With your consent, analytics and marketing cookies help us improve our content and communication. <a href="${escapedPrivacyUrl}">${policyLabel}</a>.`,
                acceptAllBtn: 'Accept all',
                acceptNecessaryBtn: 'Necessary only',
                showPreferencesBtn: 'Settings',
            },
            preferencesModal: {
                title: 'Cookie settings',
                acceptAllBtn: 'Accept all',
                acceptNecessaryBtn: 'Necessary only',
                savePreferencesBtn: 'Save selection',
                closeIconLabel: 'Close settings',
                sections: [
                    {
                        title: 'Necessary cookies',
                        description: 'These cookies provide essential website functions, security, and storage of your consent choice. They are always enabled.',
                        linkedCategory: 'necessary',
                    },
                    {
                        title: 'Analytics cookies',
                        description: 'Google Analytics 4 helps us understand how the website is used so we can improve its content and functionality.',
                        linkedCategory: 'analytics',
                    },
                    {
                        title: 'Marketing cookies',
                        description: 'These cookies measure campaign performance and help us provide more relevant communication across digital channels.',
                        linkedCategory: 'marketing',
                    },
                ],
            },
        },
    };

    const syncConsent = () => {
        const analyticsGranted = CookieConsent.acceptedCategory('analytics');
        const marketingGranted = CookieConsent.acceptedCategory('marketing');

        window.cookieAnalyticsAllowed = analyticsGranted;
        window.cookieMarketingAllowed = marketingGranted;
        window.canTrackAnalytics = () => window.cookieAnalyticsAllowed === true;

        if (typeof window.updateGoogleConsentFromCookie === 'function') {
            window.updateGoogleConsentFromCookie(analyticsGranted, marketingGranted);
        }
    };

    const options = {
        disablePageInteraction: true,
        guiOptions: {
            consentModal: {
                layout: 'box',
                position: 'middle center',
                equalWeightButtons: true,
                flipButtons: false,
            },
            preferencesModal: {
                layout: 'box',
                position: 'middle center',
            },
        },
        categories: {
            necessary: {
                enabled: true,
                readOnly: true,
            },
            analytics: {
                enabled: false,
                readOnly: false,
            },
            marketing: {
                enabled: false,
                readOnly: false,
            },
        },
        cookie: {
            name: 'cc_cookie',
            expiresAfterDays: 182,
            sameSite: 'Lax',
        },
        onFirstConsent: syncConsent,
        onConsent: syncConsent,
        onChange: syncConsent,
        language: {
            default: language,
            translations,
        },
    };

    let initialized = false;

    const initialize = () => {
        if (initialized) {
            syncConsent();
            return;
        }

        initialized = true;
        window.CookieConsent = CookieConsent;
        CookieConsent.run(options);
        syncConsent();
    };

    const openPreferences = () => {
        initialize();

        window.setTimeout(() => {
            if (typeof CookieConsent.showPreferences === 'function') {
                CookieConsent.showPreferences();
                return;
            }

            CookieConsent.show();
        }, 60);
    };

    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-cookie-consent-trigger]');

        if (!trigger) {
            return;
        }

        event.preventDefault();
        openPreferences();
    });

    const hasStoredConsent = document.cookie
        .split(';')
        .some((entry) => entry.trim().startsWith('cc_cookie='));

    if (hasStoredConsent) {
        initialize();
    } else {
        let booted = false;
        const initializeOnce = () => {
            if (booted) {
                return;
            }

            booted = true;
            initialize();
        };

        ['pointerdown', 'keydown', 'touchstart', 'scroll'].forEach((eventName) => {
            window.addEventListener(eventName, initializeOnce, { once: true, passive: true });
        });

        window.setTimeout(initializeOnce, 6000);
    }
}
