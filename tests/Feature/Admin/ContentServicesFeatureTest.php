<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Content\Service\Form as ServiceForm;
use App\Models\Content\Service\ServicePage;
use App\Models\User;
use App\Support\Content\ServicePageTemplateRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class ContentServicesFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_family_business_service_page_is_seeded(): void
    {
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::FAMILY_BUSINESS)
            ->with('translations')
            ->first();

        $this->assertNotNull($page);
        $this->assertSame('family-business', $page->code);
        $this->assertTrue((bool) $page->is_active);
        $this->assertSame(
            'Obiteljski biznis',
            (string) $page->translations->firstWhere('locale', 'hr')?->title
        );
    }

    public function test_admin_can_open_service_pages_screen(): void
    {
        $user = $this->makeAdminUser();

        $this->actingAs($user)
            ->get('/admin/content/services?locale=hr')
            ->assertOk()
            ->assertSee('Obiteljski biznis');
    }

    public function test_admin_can_open_seeded_service_page_edit_screen(): void
    {
        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::FAMILY_BUSINESS)
            ->firstOrFail();

        $this->actingAs($user)
            ->get("/admin/content/services/{$page->id}/edit?locale=hr")
            ->assertOk()
            ->assertSee('Edit Service Page')
            ->assertSee('Obiteljski biznis');
    }

    public function test_admin_can_update_seeded_service_page(): void
    {
        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::FAMILY_BUSINESS)
            ->firstOrFail();

        Livewire::actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->set('form.locale', 'hr')
            ->set('form.title', 'Obiteljski biznis Plus')
            ->set('form.slug', 'obiteljski-biznis-plus')
            ->set('form.translation_payload.hero.brand_title', 'ALPHA CAPITALIS PLUS')
            ->set('form.translation_payload.capability_cta.label', 'Rezervirajte konzultacije')
            ->set('form.translation_payload.brochure_label', 'Preuzmite vodič')
            ->call('save')
            ->assertRedirect(route('admin.content.services.index', ['locale' => 'hr']));

        $page->refresh();
        $translation = $page->translation('hr')->first();

        $this->assertNotNull($translation);
        $this->assertSame('Obiteljski biznis Plus', (string) $translation->title);
        $this->assertSame('obiteljski-biznis-plus', (string) $translation->slug);
        $this->assertSame('ALPHA CAPITALIS PLUS', $translation->payload['hero']['brand_title'] ?? null);
        $this->assertSame('Rezervirajte konzultacije', $translation->payload['capability_cta']['label'] ?? null);
        $this->assertSame('Preuzmite vodič', $translation->payload['brochure_label'] ?? null);
    }

    private function makeAdminUser(): User
    {
        $user = User::factory()->create();

        Bouncer::role()->firstOrCreate(['name' => 'admin']);
        Bouncer::assign('admin')->to($user);

        return $user;
    }
}
