<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Settings\System\StoreSettings;
use App\Models\User;
use App\Services\Front\StoreSettingsService as FrontStoreSettingsService;
use App\Services\Settings\SystemSettingsService;
use App\Support\Front\FontRegistry;
use App\Support\Front\HeroFontRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class StoreSettingsFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_website_font_defaults_to_manrope_and_can_be_saved(): void
    {
        $admin = $this->makeAdminUser();

        Livewire::actingAs($admin)
            ->test(StoreSettings::class)
            ->assertSet('form.store_front_google_font', 'manrope')
            ->set('tab', 'appearance')
            ->assertSee('Manrope')
            ->assertSee('Inter')
            ->assertSee('General Sans')
            ->assertSee('data-tom-select', false)
            ->set('form.store_front_google_font', 'general-sans')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(
            'general-sans',
            app(SystemSettingsService::class)->get('store_front_google_font')
        );
    }

    public function test_website_font_rejects_values_outside_the_supported_catalog(): void
    {
        $admin = $this->makeAdminUser();

        Livewire::actingAs($admin)
            ->test(StoreSettings::class)
            ->set('form.store_front_google_font', 'unsupported-font')
            ->call('save')
            ->assertHasErrors(['form.store_front_google_font']);
    }

    public function test_saved_website_font_is_selected_when_the_appearance_tab_is_rendered(): void
    {
        $admin = $this->makeAdminUser();
        app(SystemSettingsService::class)->put('store_front_google_font', 'general-sans');

        Livewire::actingAs($admin)
            ->test(StoreSettings::class)
            ->assertSet('form.store_front_google_font', 'general-sans')
            ->set('tab', 'appearance')
            ->assertSeeHtml('<option value="general-sans" selected>General Sans · Fontshare</option>');
    }

    public function test_every_registered_font_has_a_frontend_css_mapping(): void
    {
        $css = (string) file_get_contents(public_path('front-theme/styles/typography.css'));

        $this->assertGreaterThan(10, count(FontRegistry::keys()));

        foreach (FontRegistry::keys() as $key) {
            $this->assertStringContainsString('data-front-font="'.$key.'"', $css);
        }
    }

    public function test_homepage_hero_settings_and_separate_videos_can_be_saved(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdminUser();

        Livewire::actingAs($admin)
            ->test(StoreSettings::class)
            ->assertSet('form.store_home_hero_font', HeroFontRegistry::DEFAULT)
            ->set('tab', 'hero')
            ->assertSee('Bodoni Moda')
            ->assertSee('Desktop video')
            ->assertSee('Mobilni video')
            ->set('form.store_home_hero_font', 'manrope')
            ->assertSet('form.store_home_hero_font_weight', 400)
            ->assertSeeHtml('<option value="200">200</option>')
            ->set('form.store_home_hero_font', 'playfair-display')
            ->set('form.store_home_hero_font_weight', 700)
            ->set('form.store_home_hero_title', 'Naslov iz postavki')
            ->set('form.store_home_hero_subtitle', 'Podnaslov iz postavki')
            ->set('form.store_home_hero_primary_label', 'Prvi gumb')
            ->set('form.store_home_hero_primary_url', '/contact')
            ->set('form.store_home_hero_secondary_label', 'Drugi gumb')
            ->set('form.store_home_hero_secondary_url', '/usluge')
            ->set('homeHeroDesktopVideoUpload', UploadedFile::fake()->create('desktop.mp4', 1024, 'video/mp4'))
            ->set('homeHeroMobileVideoUpload', UploadedFile::fake()->create('mobile.webm', 768, 'video/webm'))
            ->call('save')
            ->assertSet('form.store_home_hero_font', 'playfair-display')
            ->assertSet('form.store_home_hero_font_weight', 700)
            ->assertHasNoErrors();

        $settings = app(SystemSettingsService::class);
        $desktopPath = (string) $settings->get('store_home_hero_desktop_video_path');
        $mobilePath = (string) $settings->get('store_home_hero_mobile_video_path');

        $this->assertSame('playfair-display', $settings->get('store_home_hero_font'));
        $this->assertSame(700, $settings->get('store_home_hero_font_weight'));
        $this->assertSame('Naslov iz postavki', $settings->get('store_home_hero_title'));
        $this->assertNotSame($desktopPath, $mobilePath);
        Storage::disk('public')->assertExists($desktopPath);
        Storage::disk('public')->assertExists($mobilePath);
    }

    public function test_homepage_hero_rejects_an_unsupported_font(): void
    {
        $admin = $this->makeAdminUser();

        Livewire::actingAs($admin)
            ->test(StoreSettings::class)
            ->set('form.store_home_hero_font', 'unsupported-font')
            ->call('save')
            ->assertHasErrors(['form.store_home_hero_font']);
    }

    public function test_homepage_hero_only_accepts_weights_available_for_the_selected_font(): void
    {
        $admin = $this->makeAdminUser();

        Livewire::actingAs($admin)
            ->test(StoreSettings::class)
            ->set('form.store_home_hero_font', 'dm-serif-display')
            ->assertSet('form.store_home_hero_font_weight', 400)
            ->set('form.store_home_hero_font_weight', 700)
            ->call('save')
            ->assertHasErrors(['form.store_home_hero_font_weight']);
    }

    public function test_admin_form_omits_settings_that_have_no_public_site_consumer(): void
    {
        $admin = $this->makeAdminUser();

        $component = Livewire::actingAs($admin)->test(StoreSettings::class)
            ->assertDontSee('Newsletter')
            ->assertDontSee('Announcement bar')
            ->set('tab', 'branding')
            ->assertDontSee('Footer Link Columns');

        $form = $component->get('form');
        $this->assertIsArray($form);

        foreach ([
            'store_footer_col_1_title',
            'store_footer_col_1_page_ids',
            'store_footer_col_1_custom_links',
            'store_footer_col_2_title',
            'store_footer_col_2_page_ids',
            'store_footer_col_2_custom_links',
            'store_footer_col_3_title',
            'store_footer_col_3_page_ids',
            'store_footer_col_3_custom_links',
            'store_newsletter_provider',
            'store_newsletter_mailchimp_api_key',
            'store_newsletter_mailchimp_list_id',
            'store_newsletter_klaviyo_api_key',
            'store_newsletter_klaviyo_list_id',
            'store_announcement_enabled',
            'store_announcement_text',
            'store_announcement_url',
            'store_announcement_new_tab',
        ] as $unusedKey) {
            $this->assertArrayNotHasKey($unusedKey, $form);
        }
    }

    public function test_saved_public_settings_have_an_exact_frontend_shape_without_secrets_or_legacy_fields(): void
    {
        app(SystemSettingsService::class)->putMany([
            'store_brand_name' => 'Alpha public test',
            'store_footer_phone' => '+385 1 555 0101',
            'store_captcha_recaptcha_v3_enabled' => true,
            'store_captcha_recaptcha_v3_site_key' => 'public-site-key',
            'store_captcha_recaptcha_v3_secret_key' => 'must-not-reach-views',
            'store_email_smtp_password' => 'smtp-secret',
            'store_newsletter_mailchimp_api_key' => 'legacy-newsletter-secret',
            'store_schema_category_enabled' => true,
        ]);

        $frontendSettings = app(FrontStoreSettingsService::class)->all();

        $this->assertSame([
            'branding',
            'typography',
            'home_hero',
            'blog',
            'footer',
            'official_entities',
            'captcha',
            'analytics',
            'seo',
            'og',
            'schema',
        ], array_keys($frontendSettings));
        $this->assertSame([
            'recaptcha_v3_enabled' => true,
            'recaptcha_v3_site_key' => 'public-site-key',
        ], $frontendSettings['captcha']);
        $this->assertSame('Alpha public test', $frontendSettings['branding']['store_name']);
        $this->assertSame('+385 1 555 0101', $frontendSettings['footer']['phone']);
        $this->assertArrayNotHasKey('email', $frontendSettings);
        $this->assertArrayNotHasKey('newsletter', $frontendSettings);
        $this->assertArrayNotHasKey('announcement', $frontendSettings);
        $this->assertArrayNotHasKey('category_enabled', $frontendSettings['schema']);
    }

    public function test_branding_footer_and_schema_fields_are_saved_and_exposed_to_the_frontend(): void
    {
        $admin = $this->makeAdminUser();

        Livewire::actingAs($admin)
            ->test(StoreSettings::class)
            ->set('form.store_brand_name', 'Alpha frontend contract')
            ->set('form.store_footer_phone', '+385 1 555 0102')
            ->set('form.store_footer_email_sales', 'sales@example.test')
            ->set('form.store_footer_email_support', 'support@example.test')
            ->set('form.store_footer_hours', 'Pon–Pet 08:00–16:00')
            ->set('form.store_footer_bottom_copyright_text', 'Testna prava pridržana.')
            ->set('form.store_social_x_url', 'https://x.com/alpha-contract')
            ->set('form.store_social_facebook_url', 'https://facebook.com/alpha-contract')
            ->set('form.store_social_linkedin_url', 'https://linkedin.com/company/alpha-contract')
            ->set('form.store_social_instagram_url', 'https://instagram.com/alpha-contract')
            ->set('form.store_social_tiktok_url', 'https://tiktok.com/@alpha-contract')
            ->set('form.store_social_youtube_url', 'https://youtube.com/@alpha-contract')
            ->set('form.store_schema_org_type', 'LocalBusiness')
            ->set('form.store_schema_business_name', 'Alpha schema contract')
            ->set('form.store_schema_business_phone', '+385 1 555 0103')
            ->set('form.store_schema_business_email', 'schema@example.test')
            ->set('form.store_schema_address_street', 'Testna 10')
            ->set('form.store_schema_address_city', 'Zagreb')
            ->set('form.store_schema_address_region', 'Grad Zagreb')
            ->set('form.store_schema_address_postal_code', '10000')
            ->set('form.store_schema_address_country', 'hr')
            ->call('save')
            ->assertHasNoErrors();

        $frontendSettings = app(FrontStoreSettingsService::class)->all();

        $this->assertSame([
            'phone' => '+385 1 555 0102',
            'email_sales' => 'sales@example.test',
            'email_support' => 'support@example.test',
            'hours' => 'Pon–Pet 08:00–16:00',
            'bottom_links' => [],
            'bottom_copyright_text' => 'Testna prava pridržana.',
        ], $frontendSettings['footer']);
        $this->assertSame('https://tiktok.com/@alpha-contract', $frontendSettings['branding']['social']['tiktok']['url']);
        $this->assertSame('https://youtube.com/@alpha-contract', $frontendSettings['branding']['social']['youtube']['url']);
        $this->assertSame('LocalBusiness', $frontendSettings['schema']['org_type']);
        $this->assertSame('Alpha schema contract', $frontendSettings['schema']['business_name']);
        $this->assertSame('Testna 10', $frontendSettings['schema']['address_street']);
        $this->assertSame('Zagreb', $frontendSettings['schema']['address_city']);
        $this->assertSame('Grad Zagreb', $frontendSettings['schema']['address_region']);
        $this->assertSame('10000', $frontendSettings['schema']['address_postal_code']);
        $this->assertSame('HR', $frontendSettings['schema']['address_country']);
    }

    public function test_ga4_field_rejects_a_google_tag_manager_container_id(): void
    {
        $admin = $this->makeAdminUser();

        Livewire::actingAs($admin)
            ->test(StoreSettings::class)
            ->set('tab', 'integrations')
            ->set('form.store_analytics_enabled', true)
            ->set('form.store_analytics_ga4_measurement_id', 'GTM-P898Q4XG')
            ->call('save')
            ->assertHasErrors(['form.store_analytics_ga4_measurement_id']);
    }

    public function test_ga4_measurement_id_is_normalized_and_saved(): void
    {
        $admin = $this->makeAdminUser();

        Livewire::actingAs($admin)
            ->test(StoreSettings::class)
            ->set('form.store_analytics_enabled', true)
            ->set('form.store_analytics_ga4_measurement_id', '  g-ab12cd34  ')
            ->call('save')
            ->assertHasNoErrors();

        $settings = app(SystemSettingsService::class);
        $this->assertTrue((bool) $settings->get('store_analytics_enabled'));
        $this->assertSame('G-AB12CD34', $settings->get('store_analytics_ga4_measurement_id'));
    }

    public function test_tracking_integration_ids_are_normalized_saved_and_exposed_to_the_frontend(): void
    {
        $admin = $this->makeAdminUser();

        Livewire::actingAs($admin)
            ->test(StoreSettings::class)
            ->set('tab', 'integrations')
            ->assertSee('Google Tag Manager')
            ->assertSee('Google Ads')
            ->assertSee('Meta (Facebook) Pixel')
            ->set('form.store_analytics_gtm_enabled', true)
            ->set('form.store_analytics_gtm_container_id', '  gtm-ab12cd  ')
            ->set('form.store_analytics_google_ads_enabled', true)
            ->set('form.store_analytics_google_ads_conversion_id', '  aw-123456789  ')
            ->set('form.store_analytics_meta_pixel_enabled', true)
            ->set('form.store_analytics_meta_pixel_id', '  123456789012345  ')
            ->call('save')
            ->assertHasNoErrors();

        $settings = app(SystemSettingsService::class);
        $this->assertTrue((bool) $settings->get('store_analytics_gtm_enabled'));
        $this->assertSame('GTM-AB12CD', $settings->get('store_analytics_gtm_container_id'));
        $this->assertTrue((bool) $settings->get('store_analytics_google_ads_enabled'));
        $this->assertSame('AW-123456789', $settings->get('store_analytics_google_ads_conversion_id'));
        $this->assertTrue((bool) $settings->get('store_analytics_meta_pixel_enabled'));
        $this->assertSame('123456789012345', $settings->get('store_analytics_meta_pixel_id'));

        $frontendSettings = app(FrontStoreSettingsService::class)->analytics();
        $this->assertTrue($frontendSettings['gtm_enabled']);
        $this->assertSame('GTM-AB12CD', $frontendSettings['gtm_container_id']);
        $this->assertTrue($frontendSettings['google_ads_enabled']);
        $this->assertSame('AW-123456789', $frontendSettings['google_ads_conversion_id']);
        $this->assertTrue($frontendSettings['meta_pixel_enabled']);
        $this->assertSame('123456789012345', $frontendSettings['meta_pixel_id']);
    }

    public function test_tracking_integration_ids_are_validated_when_enabled(): void
    {
        $admin = $this->makeAdminUser();

        Livewire::actingAs($admin)
            ->test(StoreSettings::class)
            ->set('form.store_analytics_gtm_enabled', true)
            ->set('form.store_analytics_gtm_container_id', 'G-INVALID')
            ->set('form.store_analytics_google_ads_enabled', true)
            ->set('form.store_analytics_google_ads_conversion_id', 'GTM-INVALID')
            ->set('form.store_analytics_meta_pixel_enabled', true)
            ->set('form.store_analytics_meta_pixel_id', 'pixel-invalid')
            ->call('save')
            ->assertHasErrors([
                'form.store_analytics_gtm_container_id',
                'form.store_analytics_google_ads_conversion_id',
                'form.store_analytics_meta_pixel_id',
            ]);
    }

    private function makeAdminUser(): User
    {
        $user = User::factory()->create();
        Bouncer::assign('admin')->to($user);

        return $user;
    }
}
