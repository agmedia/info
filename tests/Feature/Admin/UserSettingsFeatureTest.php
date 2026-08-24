<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Settings\User\UserFeatures;
use App\Models\User;
use App\Services\Settings\SystemSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class UserSettingsFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_user_settings_page(): void
    {
        $admin = $this->makeUserWithRole('admin');

        $this->actingAs($admin)
            ->get('/admin/settings/user')
            ->assertOk()
            ->assertSee('User Settings');
    }

    public function test_editor_can_open_requested_settings_pages(): void
    {
        $editor = $this->makeUserWithRole('editor');

        $this->actingAs($editor)
            ->get('/admin/settings/user')
            ->assertOk()
            ->assertSee('User Settings');

        $this->actingAs($editor)
            ->get('/admin/settings/system/admin-appearance-controls')
            ->assertOk()
            ->assertSee('Admin Appearance');

        $this->actingAs($editor)
            ->get('/admin/settings/system/store-settings')
            ->assertOk()
            ->assertSee('Site Settings')
            ->assertDontSee(route('admin.settings.system.imports'), false);

        $this->actingAs($editor)
            ->get('/admin/settings/system/imports')
            ->assertForbidden();
    }

    public function test_admin_can_save_user_tracking_switch_and_clear_legacy_loyalty_settings(): void
    {
        $admin = $this->makeUserWithRole('admin');

        Livewire::actingAs($admin)
            ->test(UserFeatures::class)
            ->set('form.user_tracking_enabled', false)
            ->call('save')
            ->assertHasNoErrors();

        $settings = app(SystemSettingsService::class);

        $this->assertFalse((bool) $settings->get('user_tracking_enabled', true));
        $this->assertFalse((bool) $settings->get('user_loyalty_enabled', true));
        $this->assertSame(0.0, (float) $settings->get('loyalty_points_per_currency', 1));
        $this->assertSame(0.0, (float) $settings->get('loyalty_min_order_total', 1));
        $this->assertSame('zero_out', (string) $settings->get('loyalty_reversal_mode', ''));
    }

    private function makeUserWithRole(string $role): User
    {
        Bouncer::role()->firstOrCreate(['name' => 'superadmin']);
        Bouncer::role()->firstOrCreate(['name' => 'admin']);
        Bouncer::role()->firstOrCreate(['name' => 'editor']);
        Bouncer::role()->firstOrCreate(['name' => 'customer']);

        $user = User::factory()->create();
        Bouncer::assign($role)->to($user);

        return $user;
    }
}
