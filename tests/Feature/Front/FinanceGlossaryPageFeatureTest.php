<?php

namespace Tests\Feature\Front;

use App\Models\Content\Glossary\GlossaryTerm;
use App\Models\Content\Glossary\GlossaryTermTranslation;
use App\Models\Content\Page\InfoPage;
use App\Models\Content\Page\InfoPageTranslation;
use App\Services\Content\GlossaryImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceGlossaryPageFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_finance_glossary_page_renders_index_and_filter_state(): void
    {
        $page = InfoPage::query()->create([
            'code' => GlossaryImportService::DEFAULT_PAGE_CODE,
            'layout' => 'finance_glossary',
            'is_active' => true,
            'published_at' => now()->subDay(),
            'payload' => [
                'glossary_collection' => GlossaryImportService::DEFAULT_COLLECTION,
                'glossary_kicker' => 'Rječnik pojmova',
            ],
        ]);

        InfoPageTranslation::query()->create([
            'page_id' => $page->id,
            'locale' => 'hr',
            'title' => 'Svijet financija',
            'slug' => 'svijet-financija',
            'excerpt' => 'Pojmovi iz financija i računovodstva.',
        ]);

        $term = GlossaryTerm::query()->create([
            'code' => 'svijet-financija-20001',
            'collection_code' => GlossaryImportService::DEFAULT_COLLECTION,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        GlossaryTermTranslation::query()->create([
            'term_id' => $term->id,
            'locale' => 'hr',
            'title' => 'Bilanca',
            'slug' => 'bilanca',
            'excerpt' => 'Kratki opis bilance.',
            'body_html' => '<p>Bilanca prikazuje imovinu, obveze i kapital.</p>',
            'payload' => [
                'synonyms' => ['Izvještaj o financijskom položaju'],
                'tags' => ['financije', 'izvještaji'],
            ],
        ]);

        $this->get('/glossary?letter=B&q=bilanca')
            ->assertOk()
            ->assertSee('Bilanca')
            ->assertSee('Opširnije')
            ->assertSee('data-active-letter="B"', false);
    }

    public function test_finance_glossary_term_has_a_dedicated_detail_page(): void
    {
        $page = InfoPage::query()->create([
            'code' => GlossaryImportService::DEFAULT_PAGE_CODE,
            'layout' => 'finance_glossary',
            'is_active' => true,
            'published_at' => now()->subDay(),
            'payload' => [
                'glossary_collection' => GlossaryImportService::DEFAULT_COLLECTION,
                'glossary_kicker' => 'Rječnik pojmova',
            ],
        ]);

        InfoPageTranslation::query()->create([
            'page_id' => $page->id,
            'locale' => 'hr',
            'title' => 'Svijet financija',
            'slug' => 'svijet-financija',
            'excerpt' => 'Pojmovi iz financija i računovodstva.',
        ]);

        $term = GlossaryTerm::query()->create([
            'code' => 'svijet-financija-20001',
            'collection_code' => GlossaryImportService::DEFAULT_COLLECTION,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        GlossaryTermTranslation::query()->create([
            'term_id' => $term->id,
            'locale' => 'hr',
            'title' => 'Bilanca',
            'slug' => 'bilanca',
            'excerpt' => 'Kratki opis bilance.',
            'body_html' => "Bilanca prikazuje imovinu, obveze i kapital.\n\n<strong>Drugi odlomak.</strong>",
            'meta_title' => 'Bilanca | Glossary',
            'meta_description' => 'Detaljno objašnjenje pojma bilanca.',
            'payload' => [
                'synonyms' => ['Izvještaj o financijskom položaju'],
            ],
        ]);

        $this->get('/glossary/bilanca')
            ->assertOk()
            ->assertSee('Bilanca | Glossary', false)
            ->assertSee('Svijet financija')
            ->assertSee('Natrag u Svijet financija')
            ->assertSee('<p>Bilanca prikazuje imovinu, obveze i kapital.</p>', false)
            ->assertSee('<p><strong>Drugi odlomak.</strong></p>', false)
            ->assertSee('Izvještaj o financijskom položaju');
    }

    public function test_glossary_detail_uses_only_first_sentence_as_intro_when_excerpt_repeats_body(): void
    {
        $page = InfoPage::query()->create([
            'code' => GlossaryImportService::DEFAULT_PAGE_CODE,
            'layout' => 'finance_glossary',
            'is_active' => true,
            'published_at' => now()->subDay(),
            'payload' => [
                'glossary_collection' => GlossaryImportService::DEFAULT_COLLECTION,
            ],
        ]);

        InfoPageTranslation::query()->create([
            'page_id' => $page->id,
            'locale' => 'hr',
            'title' => 'Svijet financija',
            'slug' => 'svijet-financija',
        ]);

        $term = GlossaryTerm::query()->create([
            'code' => 'svijet-financija-20002',
            'collection_code' => GlossaryImportService::DEFAULT_COLLECTION,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        GlossaryTermTranslation::query()->create([
            'term_id' => $term->id,
            'locale' => 'hr',
            'title' => 'Dobit tekućeg razdoblja',
            'slug' => 'dobit-tekuceg-razdoblja',
            'excerpt' => 'Prva rečenica. Druga rečenica.',
            'body_html' => "Prva rečenica. Druga rečenica.\n\n<strong>Treći odlomak.</strong>",
        ]);

        $response = $this->get('/glossary/dobit-tekuceg-razdoblja');

        $response->assertOk()
            ->assertSee('Prva rečenica.')
            ->assertSee('Druga rečenica.')
            ->assertSee('Treći odlomak.');

        $content = $response->getContent();

        $this->assertMatchesRegularExpression(
            '/<div class="ac-page-title-copy">\s*<p class="ac-glossary-detail-category">Svijet financija<\/p>\s*<\/div>/s',
            $content
        );
        $this->assertMatchesRegularExpression(
            '/<div class="content-richtext pt-6">\s*<p>Prva rečenica\.<\/p>\s*<p>Druga rečenica\.<\/p>\s*<p><strong>Treći odlomak\.<\/strong><\/p>\s*<\/div>/s',
            $content
        );
    }

    public function test_home_header_links_world_of_finance_navigation_item_to_glossary_page(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(route('glossary.index'), false)
            ->assertDontSee('#edukacija-svijet-financija', false);
    }

    public function test_clean_generic_page_url_redirects_to_glossary_index(): void
    {
        $page = InfoPage::query()->create([
            'code' => GlossaryImportService::DEFAULT_PAGE_CODE,
            'layout' => 'finance_glossary',
            'is_active' => true,
            'published_at' => now()->subDay(),
            'payload' => [
                'glossary_collection' => GlossaryImportService::DEFAULT_COLLECTION,
            ],
        ]);

        InfoPageTranslation::query()->create([
            'page_id' => $page->id,
            'locale' => 'hr',
            'title' => 'Svijet financija',
            'slug' => 'svijet-financija',
            'excerpt' => 'Pojmovi iz financija i računovodstva.',
        ]);

        $this->get('/svijet-financija?letter=B&q=bilanca')
            ->assertStatus(301)
            ->assertRedirect(route('glossary.index', ['letter' => 'B', 'q' => 'bilanca']));
    }

    public function test_legacy_generic_page_url_redirects_to_clean_generic_page_url(): void
    {
        $page = InfoPage::query()->create([
            'code' => GlossaryImportService::DEFAULT_PAGE_CODE,
            'layout' => 'finance_glossary',
            'is_active' => true,
            'published_at' => now()->subDay(),
            'payload' => [
                'glossary_collection' => GlossaryImportService::DEFAULT_COLLECTION,
            ],
        ]);

        InfoPageTranslation::query()->create([
            'page_id' => $page->id,
            'locale' => 'hr',
            'title' => 'Svijet financija',
            'slug' => 'svijet-financija',
            'excerpt' => 'Pojmovi iz financija i računovodstva.',
        ]);

        $this->get('/page/svijet-financija?letter=B&q=bilanca')
            ->assertStatus(301)
            ->assertRedirect(route('pages.show', ['slug' => 'svijet-financija']).'?letter=B&q=bilanca');
    }
}
