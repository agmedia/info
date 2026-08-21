<?php

namespace Tests\Feature\Front;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class TrackingIntegrationsFeatureTest extends TestCase
{
    public function test_enabled_tracking_integrations_are_exposed_to_the_consent_aware_frontend_loader(): void
    {
        $html = $this->renderTrackingPartial([
            'enabled' => true,
            'ga4_measurement_id' => 'G-AB12CD34',
            'gtm_enabled' => true,
            'gtm_container_id' => 'GTM-EF56GH',
            'google_ads_enabled' => true,
            'google_ads_conversion_id' => 'AW-123456789',
            'meta_pixel_enabled' => true,
            'meta_pixel_id' => '123456789012345',
        ]);

        $this->assertStringContainsString('name="store-tracking-config"', $html);
        $this->assertStringContainsString('data-ga4-measurement-id="G-AB12CD34"', $html);
        $this->assertStringContainsString('data-gtm-container-id="GTM-EF56GH"', $html);
        $this->assertStringContainsString('data-google-ads-conversion-id="AW-123456789"', $html);
        $this->assertStringContainsString('data-meta-pixel-id="123456789012345"', $html);
        $this->assertStringContainsString('front-theme/scripts/tracking-integrations.js', $html);
        $this->assertStringNotContainsString('https://www.googletagmanager.com', $html);
        $this->assertStringNotContainsString('https://connect.facebook.net', $html);
    }

    public function test_disabled_or_invalid_tracking_integrations_do_not_render_a_loader(): void
    {
        $html = $this->renderTrackingPartial([
            'enabled' => false,
            'ga4_measurement_id' => 'G-AB12CD34',
            'gtm_enabled' => true,
            'gtm_container_id' => 'G-INVALID',
            'google_ads_enabled' => true,
            'google_ads_conversion_id' => 'AW-INVALID',
            'meta_pixel_enabled' => true,
            'meta_pixel_id' => 'PIXEL-INVALID',
        ]);

        $this->assertStringNotContainsString('store-tracking-config', $html);
        $this->assertStringNotContainsString('tracking-integrations.js', $html);
    }

    /**
     * @param  array<string, mixed>  $analytics
     */
    private function renderTrackingPartial(array $analytics): string
    {
        return Blade::render(
            (string) file_get_contents(resource_path('views/front/partials/analytics.blade.php')),
            ['storeSettings' => ['analytics' => $analytics]],
        );
    }
}
