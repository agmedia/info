<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Settings\System\StoreSettings;
use App\Models\User;
use App\Services\Settings\SystemSettingsService;
use App\Support\Front\FontRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    private function makeAdminUser(): User
    {
        $user = User::factory()->create();
        Bouncer::assign('admin')->to($user);

        return $user;
    }
}
