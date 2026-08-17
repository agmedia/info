<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Content\Service\Form as ServiceForm;
use App\Models\Content\Service\ServicePage;
use App\Models\User;
use App\Support\Content\ServicePageTemplateRegistry;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
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

    public function test_default_services_index_page_is_seeded(): void
    {
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::SERVICES_INDEX)
            ->with('translations')
            ->first();

        $this->assertNotNull($page);
        $this->assertSame('services', $page->code);
        $this->assertTrue((bool) $page->is_active);
        $this->assertSame(
            'Usluge',
            (string) $page->translations->firstWhere('locale', 'hr')?->title
        );
    }

    public function test_default_tax_service_page_is_seeded(): void
    {
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::TAX)
            ->with('translations')
            ->first();

        $this->assertNotNull($page);
        $this->assertSame('tax', $page->code);
        $this->assertTrue((bool) $page->is_active);
        $this->assertSame(
            'Porezi',
            (string) $page->translations->firstWhere('locale', 'hr')?->title
        );
    }

    public function test_default_eu_funds_service_page_is_seeded(): void
    {
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::EU_FUNDS)
            ->with('translations')
            ->first();

        $this->assertNotNull($page);
        $this->assertSame('eu-fondovi', $page->code);
        $this->assertTrue((bool) $page->is_active);
        $this->assertSame(
            'EU fondovi',
            (string) $page->translations->firstWhere('locale', 'hr')?->title
        );
    }

    public function test_admin_can_open_service_pages_screen(): void
    {
        $user = $this->makeAdminUser();

        $this->actingAs($user)
            ->get('/admin/content/services?locale=hr')
            ->assertOk()
            ->assertSee('Usluge')
            ->assertSee('Bankovni krediti')
            ->assertSee('Zakon o poticanju ulaganja')
            ->assertSee('Obiteljski biznis')
            ->assertSee('Porezi');
    }

    public function test_admin_can_search_advisory_subpages_on_service_pages_screen(): void
    {
        $user = $this->makeAdminUser();

        $this->actingAs($user)
            ->get('/admin/content/services?locale=hr')
            ->assertOk()
            ->assertSee('Bankovni krediti');

        Livewire::actingAs($user)
            ->test(\App\Livewire\Admin\Content\Service\Manager::class)
            ->set('locale', 'hr')
            ->set('search', 'bankovni')
            ->assertSee('Savjetovanje')
            ->assertSee('Bankovni krediti');
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
            ->assertSee('Uredi stranicu usluge')
            ->assertSee('Obiteljski biznis');
    }

    public function test_audit_service_page_edit_screen_shows_locked_audit_template_and_editor(): void
    {
        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::AUDIT)
            ->firstOrFail();

        $component = Livewire::actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id]);

        $this->assertSame(ServicePageTemplateRegistry::AUDIT, $component->get('form.template_key'));
        $this->assertStringContainsString('value="Revizija"', $component->html());
        $this->assertStringContainsString('Navigacija revizije', $component->html());
        $this->assertStringContainsString('Blok pregleda', $component->html());
    }

    public function test_tax_service_page_edit_screen_shows_locked_tax_template_and_editor(): void
    {
        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::TAX)
            ->firstOrFail();

        $component = Livewire::actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id]);

        $this->assertSame(ServicePageTemplateRegistry::TAX, $component->get('form.template_key'));
        $this->assertStringContainsString('value="Porezi"', $component->html());
        $this->assertStringContainsString('Navigacija poreza', $component->html());
        $this->assertStringContainsString('Blok usklađenosti', $component->html());
    }

    public function test_eu_funds_service_page_edit_screen_shows_locked_eu_funds_template_and_editor(): void
    {
        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::EU_FUNDS)
            ->firstOrFail();

        $component = Livewire::actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id]);

        $this->assertSame(ServicePageTemplateRegistry::EU_FUNDS, $component->get('form.template_key'));
        $this->assertStringContainsString('value="EU fondovi"', $component->html());
        $this->assertStringContainsString('Navigacija EU fondova', $component->html());
        $this->assertStringContainsString('Sekcija resursa', $component->html());

        $component->call('setTab', 'sources');

        $this->assertStringContainsString('Auto (trenutna kategorija EU fondova)', $component->html());
    }

    public function test_admin_can_upload_pdf_asset_for_eu_funds_service_page(): void
    {
        Storage::fake('public');

        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::EU_FUNDS)
            ->firstOrFail();

        Livewire::actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->set('form.locale', 'hr')
            ->set('form.translation_payload.calls.download_link.type', 'pdf')
            ->set('assetUploads.calls_download_link_path', UploadedFile::fake()->create('eu-fondovi.pdf', 120, 'application/pdf'))
            ->call('save')
            ->assertRedirect(route('admin.content.services.index', ['locale' => 'hr']));

        $page->refresh();
        $translation = $page->translation('hr')->first();
        $storedPath = (string) ($translation?->payload['calls']['download_link']['path'] ?? '');

        $this->assertNotSame('', $storedPath);
        $this->assertStringStartsWith('service-assets/eu-funds/', $storedPath);
        Storage::disk('public')->assertExists($storedPath);
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

    public function test_admin_can_update_services_index_content_used_on_front(): void
    {
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::SERVICES_INDEX)
            ->firstOrFail();

        Livewire::actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->set('form.locale', 'hr')
            ->set('form.meta_title', 'Usluge custom meta naslov')
            ->set('form.meta_description', 'Custom meta opis Usluge landing stranice.')
            ->set('form.translation_payload.showcase.title_lead', 'Sve usluge na jednom mjestu')
            ->set('form.translation_payload.showcase.intro', 'Custom uvod za pregled usluga iz admina.')
            ->set('form.translation_payload.showcase.card_action_label', 'ISTRAŽITE USLUGU')
            ->set('form.translation_payload.primary_pillars.0.title', 'Revizija custom')
            ->set('form.translation_payload.primary_pillars.0.subtitle', 'Custom podnaslov revizije')
            ->set('form.translation_payload.primary_pillars.0.text', 'Custom tekst kartice revizije.')
            ->set('form.translation_payload.primary_pillars.0.image_alt', 'Custom opis fotografije revizije')
            ->call('save')
            ->assertRedirect(route('admin.content.services.index', ['locale' => 'hr']));

        $this->get('/usluge')
            ->assertOk()
            ->assertSee('<title>Usluge custom meta naslov</title>', false)
            ->assertSee('<meta name="description" content="Custom meta opis Usluge landing stranice.">', false)
            ->assertSee('Sve usluge na jednom mjestu')
            ->assertSee('Custom uvod za pregled usluga iz admina.')
            ->assertSee('Revizija custom')
            ->assertSee('Custom podnaslov revizije')
            ->assertSee('Custom tekst kartice revizije.')
            ->assertSee('ISTRAŽITE USLUGU')
            ->assertSee('alt="Custom opis fotografije revizije"', false);
    }

    public function test_services_index_editor_follows_frontend_order_and_contains_every_visible_copy_field(): void
    {
        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::SERVICES_INDEX)
            ->firstOrFail();

        Livewire::actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->assertSee('Sadržaj s fronta')
            ->assertSee('1. Uvodna sekcija')
            ->assertSee('2. Kartice usluga')
            ->assertSee('Naslov')
            ->assertSee('Podnaslov')
            ->assertSee('Tekst poveznice na kartici')
            ->assertSee('Slika kartice')
            ->assertSee('Alternativni tekst slike')
            ->assertDontSeeHtml('wire:model="form.translation_payload.showcase.title_accent"')
            ->assertSeeHtml('wire:model="form.translation_payload.showcase.card_action_label"')
            ->assertSeeHtml('wire:model="form.translation_payload.primary_pillars.0.subtitle"')
            ->assertSeeHtml('wire:model="form.translation_payload.primary_pillars.0.image_alt"')
            ->assertSeeHtml('wire:model="landingImageUploads.audit"')
            ->assertDontSeeHtml('wire:model="form.translation_payload.audience.headline"')
            ->assertDontSeeHtml('wire:model="form.translation_payload.ffi.title"')
            ->assertDontSeeHtml('wire:model="form.translation_payload.advisory_approach.title"');
    }

    public function test_admin_can_replace_and_restore_a_services_index_card_image(): void
    {
        Storage::fake('public');

        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::SERVICES_INDEX)
            ->firstOrFail();

        Livewire::actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->set('landingImageUploads.audit', UploadedFile::fake()->image('revizija-custom.jpg', 1080, 1350))
            ->call('save')
            ->assertRedirect(route('admin.content.services.index', ['locale' => 'hr']));

        $page->refresh();
        $media = $page->getFirstMedia('services_index_audit_image');

        $this->assertNotNull($media);
        $this->assertFileExists($media->getPath());
        $expectedImageUrl = $media->hasGeneratedConversion('services_index_card_1080x1350')
            ? $media->getUrl('services_index_card_1080x1350')
            : $media->getUrl();

        $this->get('/usluge')
            ->assertOk()
            ->assertSee($expectedImageUrl, false);

        Livewire::actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->call('removeServicesIndexCardImage', 'audit');

        $this->assertNull($page->refresh()->getFirstMedia('services_index_audit_image'));
    }

    public function test_admin_can_update_advisory_subpage_content_used_on_front(): void
    {
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::ADVISORY)
            ->firstOrFail();

        Livewire::actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->set('form.locale', 'hr')
            ->set('form.translation_payload.bank_loans.overview_title', 'Custom bankovni krediti naslov')
            ->set('form.translation_payload.bank_loans.services_body.0', 'Custom tekst usluge bankovnih kredita.')
            ->set('form.translation_payload.bank_loans.help_items.0', 'custom analiza kreditne sposobnosti')
            ->set('form.translation_payload.bank_loans.approach_body.0', 'Custom pristup bankovnim kreditima.')
            ->call('save')
            ->assertRedirect(route('admin.content.services.index', ['locale' => 'hr']));

        $this->get('/savjetovanje/pribavljanje-financiranja/bankovni-krediti')
            ->assertOk()
            ->assertSee('Custom bankovni krediti naslov')
            ->assertSee('Custom tekst usluge bankovnih kredita.')
            ->assertSee('custom analiza kreditne sposobnosti')
            ->assertSee('Custom pristup bankovnim kreditima.');
    }

    private function makeAdminUser(): User
    {
        $user = User::factory()->create();

        Bouncer::role()->firstOrCreate(['name' => 'admin']);
        Bouncer::assign('admin')->to($user);

        return $user;
    }
}
