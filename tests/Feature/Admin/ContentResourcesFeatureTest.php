<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Content\Resource\Form as ResourceForm;
use App\Models\Content\Resource\ResourceDocument;
use App\Models\User;
use Database\Seeders\ResourceDocumentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class ContentResourcesFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_resource_documents_screen(): void
    {
        $this->seed(ResourceDocumentSeeder::class);

        $user = $this->makeAdminUser();

        $this->actingAs($user)
            ->get('/admin/content/resources?locale=hr')
            ->assertOk()
            ->assertSee(__('admin.content.resources.manager.title'))
            ->assertSee('Analiza sektora: Proizvodnja tekstila');
    }

    public function test_admin_can_create_resource_document(): void
    {
        $user = $this->makeAdminUser();

        Livewire::actingAs($user)
            ->test(ResourceForm::class)
            ->set('form.code', 'custom-download')
            ->set('form.group_code', 'downloads')
            ->set('form.is_active', true)
            ->set('form.locale', 'hr')
            ->set('form.title', 'Custom Download')
            ->set('form.slug', 'custom-download')
            ->set('form.excerpt', 'Interni dokument za preuzimanje.')
            ->set('form.download_url', 'https://example.test/files/custom-download.pdf')
            ->call('save')
            ->assertRedirect(route('admin.content.resources.index', ['locale' => 'hr']));

        $document = ResourceDocument::query()->where('code', 'custom-download')->first();

        $this->assertNotNull($document);
        $this->assertSame('downloads', $document->group_code);
        $this->assertSame('https://example.test/files/custom-download.pdf', $document->download_url);
        $this->assertSame('Custom Download', (string) $document->translation('hr')->first()?->title);
    }

    public function test_resource_document_seeder_imports_wordpress_resource_listing(): void
    {
        $this->seed(ResourceDocumentSeeder::class);

        $this->assertSame(38, ResourceDocument::query()->count());

        $document = ResourceDocument::query()
            ->where('code', 'poslovni-plan')
            ->with('translations')
            ->first();

        $this->assertNotNull($document);
        $this->assertSame('downloads', $document->group_code);
        $this->assertTrue((bool) $document->is_active);
        $this->assertSame(
            'https://alphacapitalis.com/wp-content/uploads/2020/12/Poslovni_plan_template_Alpha_Capitalis.pdf',
            (string) $document->download_url
        );
        $this->assertSame(
            'Poslovni plan',
            (string) $document->translations->firstWhere('locale', 'hr')?->title
        );
    }

    private function makeAdminUser(): User
    {
        $user = User::factory()->create();

        Bouncer::role()->firstOrCreate(['name' => 'admin']);
        Bouncer::assign('admin')->to($user);

        return $user;
    }
}
