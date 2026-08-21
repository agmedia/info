<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Services\Settings\SystemSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class DashboardFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_dashboard_with_panels(): void
    {
        $admin = $this->makeUserWithRole('admin');

        $this->actingAs($admin)
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Pregled posjećenosti')
            ->assertSee('Novi blog post')
            ->assertSee('Blog objave')
            ->assertSee('Postavke stranice')
            ->assertSee('Otvori web')
            ->assertSee('Kako povezati GA4')
            ->assertSee('G-YCD72KQJTC')
            ->assertDontSee('Upiti kroz vrijeme');
    }

    public function test_dashboard_hides_loyalty_and_tracking_sections_when_disabled(): void
    {
        app(SystemSettingsService::class)->putMany([
            'user_loyalty_enabled' => false,
            'user_tracking_enabled' => false,
        ]);

        $admin = $this->makeUserWithRole('admin');

        $this->actingAs($admin)
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertDontSee('Loyalty Net Points')
            ->assertDontSee('Recent Loyalty Activity')
            ->assertDontSee('Recent Tracking Events');
    }

    public function test_admin_defaults_to_croatian_locale(): void
    {
        $admin = $this->makeUserWithRole('admin');

        $this->actingAs($admin)
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSessionHas('admin_locale', 'hr')
            ->assertSee('Nadzorna ploča');
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
