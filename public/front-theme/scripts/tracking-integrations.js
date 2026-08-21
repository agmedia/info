(function () {
    'use strict';

    var configElement = document.querySelector('meta[name="store-tracking-config"]');

    if (!configElement) {
        return;
    }

    var config = {
        ga4MeasurementId: configElement.dataset.ga4MeasurementId || '',
        gtmContainerId: configElement.dataset.gtmContainerId || '',
        googleAdsConversionId: configElement.dataset.googleAdsConversionId || '',
        metaPixelId: configElement.dataset.metaPixelId || '',
    };
    var state = {
        googleTagLoaded: false,
        googleTagInitialized: false,
        googleDestinations: {},
        gtmLoaded: false,
        metaPixelLoaded: false,
        metaPixelInitialized: false,
    };

    var loadScript = function (id, source) {
        if (document.getElementById(id)) {
            return;
        }

        var script = document.createElement('script');
        script.id = id;
        script.async = true;
        script.src = source;
        document.head.appendChild(script);
    };

    var configureGoogleDestination = function (destinationId) {
        if (!destinationId || state.googleDestinations[destinationId]) {
            return;
        }

        if (!state.googleTagLoaded) {
            state.googleTagLoaded = true;
            loadScript(
                'store-google-tag-script',
                'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(destinationId)
            );
        }

        if (!state.googleTagInitialized) {
            state.googleTagInitialized = true;
            window.gtag('js', new Date());
        }

        state.googleDestinations[destinationId] = true;
        window.gtag('config', destinationId);
    };

    var loadGoogleTagManager = function () {
        if (!config.gtmContainerId || state.gtmLoaded) {
            return;
        }

        state.gtmLoaded = true;
        window.dataLayer.push({
            'gtm.start': new Date().getTime(),
            event: 'gtm.js',
        });
        loadScript(
            'store-google-tag-manager-script',
            'https://www.googletagmanager.com/gtm.js?id=' + encodeURIComponent(config.gtmContainerId)
        );
    };

    var ensureMetaPixelQueue = function () {
        if (window.fbq) {
            return;
        }

        var pixelQueue = function () {
            if (pixelQueue.callMethod) {
                pixelQueue.callMethod.apply(pixelQueue, arguments);
                return;
            }

            pixelQueue.queue.push(arguments);
        };

        window.fbq = pixelQueue;
        window._fbq = pixelQueue;
        pixelQueue.push = pixelQueue;
        pixelQueue.loaded = true;
        pixelQueue.version = '2.0';
        pixelQueue.queue = [];
    };

    var loadMetaPixel = function () {
        if (!config.metaPixelId) {
            return;
        }

        ensureMetaPixelQueue();
        window.fbq('consent', 'grant');

        if (!state.metaPixelLoaded) {
            state.metaPixelLoaded = true;
            loadScript(
                'store-meta-pixel-script',
                'https://connect.facebook.net/en_US/fbevents.js'
            );
        }

        if (!state.metaPixelInitialized) {
            state.metaPixelInitialized = true;
            window.fbq('init', config.metaPixelId);
            window.fbq('track', 'PageView');
        }
    };

    var revokeMetaPixelConsent = function () {
        if (!state.metaPixelLoaded || typeof window.fbq !== 'function') {
            return;
        }

        window.fbq('consent', 'revoke');
    };

    var syncTracking = function (consent) {
        var analyticsAllowed = consent && consent.analytics === true;
        var marketingAllowed = consent && consent.marketing === true;

        if (analyticsAllowed) {
            configureGoogleDestination(config.ga4MeasurementId);
        }

        if (marketingAllowed) {
            configureGoogleDestination(config.googleAdsConversionId);
            loadMetaPixel();
        } else {
            revokeMetaPixelConsent();
        }

        if (analyticsAllowed || marketingAllowed) {
            loadGoogleTagManager();
        }
    };

    window.addEventListener('store:cookie-consent-updated', function (event) {
        syncTracking(event.detail || {});
    });

    syncTracking({
        analytics: window.cookieAnalyticsAllowed === true,
        marketing: window.cookieMarketingAllowed === true,
    });
})();
