(function () {
    'use strict';

    window.dataLayer = window.dataLayer || [];
    window.gtag = window.gtag || function () {
        window.dataLayer.push(arguments);
    };
    window.cookieAnalyticsAllowed = false;
    window.cookieMarketingAllowed = false;
    window.canTrackAnalytics = function () {
        return window.cookieAnalyticsAllowed === true;
    };

    var applyGooglePrivacySettings = function (marketingGranted) {
        var marketingAllowed = marketingGranted === true;

        window.gtag('set', 'ads_data_redaction', !marketingAllowed);
        window.gtag('set', 'allow_google_signals', marketingAllowed);
        window.gtag('set', 'allow_ad_personalization_signals', marketingAllowed);
    };

    window.updateGoogleConsentFromCookie = function (analyticsGranted, marketingGranted) {
        window.cookieAnalyticsAllowed = analyticsGranted === true;
        window.cookieMarketingAllowed = marketingGranted === true;

        applyGooglePrivacySettings(marketingGranted);
        window.gtag('consent', 'update', {
            analytics_storage: analyticsGranted ? 'granted' : 'denied',
            ad_storage: marketingGranted ? 'granted' : 'denied',
            ad_user_data: marketingGranted ? 'granted' : 'denied',
            ad_personalization: marketingGranted ? 'granted' : 'denied',
        });

        window.dispatchEvent(new CustomEvent('store:cookie-consent-updated', {
            detail: {
                analytics: analyticsGranted === true,
                marketing: marketingGranted === true,
            },
        }));
    };

    window.gtag('consent', 'default', {
        analytics_storage: 'denied',
        ad_storage: 'denied',
        ad_user_data: 'denied',
        ad_personalization: 'denied',
        wait_for_update: 500,
    });
    applyGooglePrivacySettings(false);

    var match = document.cookie.match(/(?:^|;\s*)cc_cookie=([^;]+)/);

    if (!match) {
        return;
    }

    try {
        var storedConsent = JSON.parse(decodeURIComponent(match[1]));
        var categories = Array.isArray(storedConsent.categories) ? storedConsent.categories : [];

        window.updateGoogleConsentFromCookie(
            categories.indexOf('analytics') !== -1,
            categories.indexOf('marketing') !== -1
        );
    } catch (error) {
        // Keep all optional storage denied when the stored value is malformed.
    }
})();
