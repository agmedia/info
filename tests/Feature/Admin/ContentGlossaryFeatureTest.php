<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Content\Glossary\Form as GlossaryForm;
use App\Models\Content\Glossary\GlossaryTerm;
use App\Models\Content\Page\InfoPage;
use App\Services\Content\GlossaryImportService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class ContentGlossaryFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_glossary_routes_are_available_in_admin_content_area(): void
    {
        $user = $this->makeAdminUser();

        $this->actingAs($user)
            ->get('/admin/content/glossary')
            ->assertOk();
    }

    public function test_admin_can_create_glossary_term(): void
    {
        $user = $this->makeAdminUser();

        Livewire::actingAs($user)
            ->test(GlossaryForm::class)
            ->set('form.code', 'finance-term-1')
            ->set('collectionCodeSelection', '__custom__')
            ->set('customCollectionCode', 'svijet-financija')
            ->set('form.is_active', true)
            ->set('form.sort_order', 4)
            ->set('form.locale', 'hr')
            ->set('form.title', 'Bilanca')
            ->set('form.slug', 'bilanca')
            ->set('form.excerpt', 'Kratki opis bilance.')
            ->set('form.body_html', '<p>Bilanca prikazuje imovinu, obveze i kapital.</p>')
            ->call('save')
            ->assertRedirect(route('admin.content.glossary.index', ['locale' => 'hr']));

        $term = GlossaryTerm::query()->where('code', 'finance-term-1')->first();

        $this->assertNotNull($term);
        $this->assertSame('svijet-financija', $term->collection_code);
        $this->assertSame(4, $term->sort_order);
        $this->assertSame('Bilanca', (string) $term->translation('hr')->first()?->title);
    }

    public function test_glossary_import_command_creates_finance_page_and_terms(): void
    {
        $csv = implode("\n", [
            'Id,Title,Excerpt,Description,Synonyms,Variations,Categories,Abbreviation,Tags,Image',
            '20001,Bilanca,,<p>Bilanca prikazuje imovinu i obveze.</p>,Izvještaj o financijskom položaju,,Svijet Financija,,financije|izvještaji,',
            '20002,Bruto dobit,,<p>Bruto dobit predstavlja razliku između prihoda i troška prodane robe.</p>,,Bruto marža,Svijet Financija,,profit|dobit,',
        ]);

        $file = tempnam(sys_get_temp_dir(), 'glossary');
        $this->assertNotFalse($file);
        file_put_contents($file, $csv);

        try {
            $this->artisan('content:import-glossary', [
                'file' => $file,
                '--locale' => 'hr',
                '--collection' => GlossaryImportService::DEFAULT_COLLECTION,
                '--page-title' => 'Svijet financija',
                '--page-slug' => 'svijet-financija',
            ])->assertExitCode(0);
        } finally {
            @unlink($file);
        }

        $this->assertSame(2, GlossaryTerm::query()->count());

        $page = InfoPage::query()->where('code', GlossaryImportService::DEFAULT_PAGE_CODE)->first();
        $this->assertNotNull($page);
        $this->assertSame('finance_glossary', $page->layout);
        $this->assertSame(
            'Svijet financija',
            (string) $page->translations()->where('locale', 'hr')->value('title')
        );

        $bilanca = GlossaryTerm::query()->where('code', GlossaryImportService::DEFAULT_COLLECTION.'-20001')->first();
        $this->assertNotNull($bilanca);
        $this->assertSame('bilanca', (string) $bilanca->translations()->where('locale', 'hr')->value('slug'));
        $payload = $bilanca->translations()->where('locale', 'hr')->value('payload');
        $this->assertSame(
            ['Izvještaj o financijskom položaju'],
            is_array($payload) ? ($payload['synonyms'] ?? []) : []
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
