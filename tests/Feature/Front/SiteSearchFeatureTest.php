<?php

namespace Tests\Feature\Front;

use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Blog\BlogPostTranslation;
use App\Models\Content\Glossary\GlossaryTerm;
use App\Models\Content\Glossary\GlossaryTermTranslation;
use App\Models\Content\Page\InfoPage;
use App\Models\Content\Page\InfoPageTranslation;
use App\Services\Content\GlossaryImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteSearchFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_results_page_groups_matches_by_section(): void
    {
        $this->seedGlossaryPage();
        $this->seedGlossaryTerm('Porezna osnovica', 'porezna-osnovica');
        $this->seedBlogPost('Porezni vodič za vlasnike', 'porezni-vodic');

        $this->get('/search?q=porez')
            ->assertOk()
            ->assertSee(__('ui.search.results_title'))
            ->assertSee(__('ui.search.sections.services'))
            ->assertSee(__('ui.search.sections.glossary'))
            ->assertSee(__('ui.search.sections.blog'))
            ->assertSee('Porezi')
            ->assertSee('Porezna osnovica')
            ->assertSee('Porezni vodič za vlasnike')
            ->assertSee(route('advisory.tax.show'), false)
            ->assertDontSee(route('tax.show'), false)
            ->assertSee('ac-site-search-list', false);
    }

    public function test_search_suggest_returns_grouped_text_only_sections(): void
    {
        $this->seedGlossaryPage();
        $this->seedGlossaryTerm('Porezna osnovica', 'porezna-osnovica');
        $this->seedBlogPost('Porezni vodič za vlasnike', 'porezni-vodic');

        $response = $this->getJson('/search/suggest?q=porez');

        $response
            ->assertOk()
            ->assertJsonPath('sections.0.key', 'blog')
            ->assertJsonPath('sections.1.key', 'services')
            ->assertJsonPath('sections.2.key', 'glossary')
            ->assertJsonFragment(['title' => 'Porezi'])
            ->assertJsonFragment(['title' => 'Porezna osnovica'])
            ->assertJsonFragment(['title' => 'Porezni vodič za vlasnike']);

        $this->assertNull($response->json('sections.0.items.0.image_url'));
    }

    public function test_search_suggest_matches_croatian_diacritics_from_plain_query(): void
    {
        $this->seedGlossaryPage();
        $this->seedGlossaryTerm('Računovodstveni pokazatelj', 'financijski-pokazatelj');

        $this->getJson('/search/suggest?q=racunovodstveni')
            ->assertOk()
            ->assertJsonFragment(['title' => 'Računovodstveni pokazatelj']);
    }

    private function seedGlossaryPage(): InfoPage
    {
        $page = InfoPage::query()->create([
            'code' => GlossaryImportService::DEFAULT_PAGE_CODE,
            'layout' => 'finance_glossary',
            'is_active' => true,
            'published_at' => now()->subDay(),
            'sort_order' => 1,
            'payload' => [
                'glossary_collection' => GlossaryImportService::DEFAULT_COLLECTION,
            ],
        ]);

        InfoPageTranslation::query()->create([
            'page_id' => $page->id,
            'locale' => 'hr',
            'title' => 'Glossary',
            'slug' => 'glossary',
            'excerpt' => 'Glossary page',
            'body_html' => '<p>Glossary page body.</p>',
        ]);

        InfoPageTranslation::query()->create([
            'page_id' => $page->id,
            'locale' => 'en',
            'title' => 'Glossary',
            'slug' => 'glossary',
            'excerpt' => 'Glossary page',
            'body_html' => '<p>Glossary page body.</p>',
        ]);

        return $page;
    }

    private function seedGlossaryTerm(string $title, string $slug): GlossaryTerm
    {
        $term = GlossaryTerm::query()->create([
            'code' => 'glossary-'.strtolower((string) str()->random(6)),
            'collection_code' => GlossaryImportService::DEFAULT_COLLECTION,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        GlossaryTermTranslation::query()->create([
            'term_id' => $term->id,
            'locale' => 'hr',
            'title' => $title,
            'slug' => $slug,
            'excerpt' => 'Glossary excerpt',
            'body_html' => '<p>Objašnjenje pojma '.$title.'.</p>',
        ]);

        GlossaryTermTranslation::query()->create([
            'term_id' => $term->id,
            'locale' => 'en',
            'title' => $title,
            'slug' => $slug,
            'excerpt' => 'Glossary excerpt',
            'body_html' => '<p>Glossary explanation for '.$title.'.</p>',
        ]);

        return $term;
    }

    private function seedBlogPost(string $title, string $slug): BlogPost
    {
        $post = BlogPost::query()->create([
            'code' => 'blog-'.strtolower((string) str()->random(6)),
            'is_active' => true,
            'published_at' => now()->subDay(),
            'sort_order' => 1,
        ]);

        BlogPostTranslation::query()->create([
            'post_id' => $post->id,
            'locale' => 'hr',
            'title' => $title,
            'slug' => $slug,
            'excerpt' => 'Blog excerpt',
            'body_html' => '<p>Blog body about '.$title.'.</p>',
        ]);

        BlogPostTranslation::query()->create([
            'post_id' => $post->id,
            'locale' => 'en',
            'title' => $title,
            'slug' => $slug,
            'excerpt' => 'Blog excerpt',
            'body_html' => '<p>Blog body about '.$title.'.</p>',
        ]);

        return $post;
    }
}
