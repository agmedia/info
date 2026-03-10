<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Content\Navigation\Manager as NavigationManager;
use App\Models\Content\Page\InfoPage;
use App\Models\Content\Page\InfoPageTranslation;
use App\Models\User;
use App\Services\Front\NavigationMenuService;
use App\Services\Settings\SystemSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class ContentNavigationFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_navigation_page(): void
    {
        $user = $this->makeAdminUser();

        $this->actingAs($user)
            ->get('/admin/content/navigation')
            ->assertOk()
            ->assertSee(__('admin.content.navigation.title'));
    }

    public function test_admin_can_save_navigation_config(): void
    {
        $user = $this->makeAdminUser();

        $page = InfoPage::query()->create([
            'code' => 'faq',
            'layout' => 'default',
            'is_active' => true,
            'show_in_footer' => false,
            'sort_order' => 20,
        ]);

        InfoPageTranslation::query()->create([
            'page_id' => $page->id,
            'locale' => 'en',
            'title' => 'FAQ',
            'slug' => 'faq',
        ]);

        Livewire::actingAs($user)
            ->test(NavigationManager::class)
            ->set('form.items', [
                [
                    'type' => 'page',
                    'label' => '',
                    'page_id' => $page->id,
                    'url' => '',
                    'open_in_new_tab' => false,
                    'show_dropdown' => false,
                    'is_active' => true,
                    'sort_order' => 0,
                ],
                [
                    'type' => 'custom',
                    'label' => 'Kontakt',
                    'page_id' => 0,
                    'url' => '/contact',
                    'open_in_new_tab' => true,
                    'show_dropdown' => false,
                    'is_active' => true,
                    'sort_order' => 1,
                ],
            ])
            ->call('save')
            ->assertHasNoErrors();

        $saved = app(SystemSettingsService::class)->get(NavigationMenuService::SETTINGS_KEY, []);

        $this->assertIsArray($saved);
        $this->assertCount(2, $saved);
        $this->assertSame('page', $saved[0]['type']);
        $this->assertSame((int) $page->id, (int) $saved[0]['page_id']);
        $this->assertSame('custom', $saved[1]['type']);
        $this->assertSame('/contact', $saved[1]['url']);
        $this->assertTrue((bool) $saved[1]['open_in_new_tab']);
    }

    private function makeAdminUser(): User
    {
        $user = User::factory()->create();

        Bouncer::role()->firstOrCreate(['name' => 'admin']);
        Bouncer::assign('admin')->to($user);

        return $user;
    }
}
