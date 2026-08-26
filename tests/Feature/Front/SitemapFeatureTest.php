<?php

namespace Tests\Feature\Front;

use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Call\CallPost;
use App\Models\Content\ContentBlock;
use App\Models\Content\Glossary\GlossaryTerm;
use App\Models\Content\Page\InfoPage;
use App\Models\Content\Resource\ResourceDocument;
use App\Models\Content\Service\ServicePage;
use App\Models\Settings\Local\Language;
use App\Services\Content\ContentBlockResolver;
use App\Services\Content\GlossaryImportService;
use App\Support\Content\ServicePageTemplateRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        Language::query()->create([
            'code' => 'hr',
            'locale' => 'hr_HR',
            'name' => 'Croatian',
            'native_name' => 'Hrvatski',
            'is_default' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        Language::query()->create([
            'code' => 'en',
            'locale' => 'en_GB',
            'name' => 'English',
            'native_name' => 'English',
            'is_default' => false,
            'is_active' => true,
            'sort_order' => 2,
        ]);
    }

    public function test_sitemap_lists_canonical_static_and_published_cms_urls_without_starting_a_session(): void
    {
        $blogPost = BlogPost::query()->create([
            'code' => 'published-blog-post',
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);
        $blogPost->translations()->create([
            'locale' => 'hr',
            'title' => 'Objavljeni članak',
            'slug' => 'objavljeni-clanak',
        ]);

        $inactiveBlogPost = BlogPost::query()->create([
            'code' => 'inactive-blog-post',
            'is_active' => false,
            'published_at' => now()->subDay(),
        ]);
        $inactiveBlogPost->translations()->create([
            'locale' => 'hr',
            'title' => 'Neaktivni članak',
            'slug' => 'neaktivni-clanak',
        ]);

        $infoPage = InfoPage::query()->create([
            'code' => 'bilingual-info-page',
            'layout' => 'default',
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);
        $infoPage->translations()->createMany([
            ['locale' => 'hr', 'title' => 'Hrvatska stranica', 'slug' => 'hrvatska-stranica'],
            ['locale' => 'en', 'title' => 'English page', 'slug' => 'english-page'],
        ]);

        $searchCollisionPage = InfoPage::query()->create([
            'code' => 'search-collision-page',
            'layout' => 'default',
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);
        $searchCollisionPage->translations()->create([
            'locale' => 'en',
            'title' => 'Search collision',
            'slug' => 'search',
        ]);

        $callPost = CallPost::query()->create([
            'code' => 'bilingual-call',
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);
        $callPost->translations()->createMany([
            ['locale' => 'hr', 'title' => 'Hrvatski poziv', 'slug' => 'hrvatski-poziv'],
            ['locale' => 'en', 'title' => 'English call', 'slug' => 'english-call'],
        ]);

        $resource = ResourceDocument::query()->create([
            'code' => 'published-resource',
            'group_code' => 'downloads',
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);
        $resource->translations()->create([
            'locale' => 'hr',
            'title' => 'Objavljeni resurs',
            'slug' => 'objavljeni-resurs',
        ]);

        $servicesPage = ServicePage::query()->updateOrCreate([
            'template_key' => ServicePageTemplateRegistry::SERVICES_INDEX,
        ], [
            'code' => 'services',
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);
        $servicesPage->translations()->updateOrCreate(
            ['locale' => 'hr'],
            ['title' => 'Usluge', 'slug' => 'usluge'],
        );
        $servicesPage->translations()->updateOrCreate(
            ['locale' => 'en'],
            ['title' => 'Services', 'slug' => 'services'],
        );

        $glossaryPage = InfoPage::query()->create([
            'code' => GlossaryImportService::DEFAULT_PAGE_CODE,
            'layout' => 'finance_glossary',
            'is_active' => true,
            'published_at' => now()->subDay(),
            'payload' => ['glossary_collection' => GlossaryImportService::DEFAULT_COLLECTION],
        ]);
        $glossaryPage->translations()->create([
            'locale' => 'hr',
            'title' => 'Svijet financija',
            'slug' => 'svijet-financija',
        ]);

        $term = GlossaryTerm::query()->create([
            'code' => 'published-term',
            'collection_code' => GlossaryImportService::DEFAULT_COLLECTION,
            'is_active' => true,
        ]);
        $term->translations()->create([
            'locale' => 'hr',
            'title' => 'Objavljeni pojam',
            'slug' => 'objavljeni-pojam',
        ]);

        $otherTerm = GlossaryTerm::query()->create([
            'code' => 'other-collection-term',
            'collection_code' => 'other-collection',
            'is_active' => true,
        ]);
        $otherTerm->translations()->create([
            'locale' => 'hr',
            'title' => 'Pojam iz druge zbirke',
            'slug' => 'pojam-iz-druge-zbirke',
        ]);

        $response = $this->get(route('sitemap'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

        $cacheControl = (string) $response->headers->get('Cache-Control');
        $this->assertStringContainsString('public', $cacheControl);
        $this->assertStringContainsString('max-age=3600', $cacheControl);
        $this->assertCount(0, $response->headers->getCookies());

        $xml = $response->getContent();
        $this->assertIsString($xml);
        $document = new \DOMDocument;
        $this->assertTrue($document->loadXML($xml));
        $this->assertStringContainsString('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', $xml);
        $this->assertStringContainsString('<loc>'.route('home').'</loc>', $xml);
        $this->assertStringContainsString('<loc>'.route('services.index').'</loc>', $xml);
        $this->assertStringContainsString('<loc>'.route('services.index.en').'</loc>', $xml);
        $this->assertStringContainsString('<loc>'.route('blog.show', ['slug' => 'objavljeni-clanak']).'</loc>', $xml);
        $this->assertStringNotContainsString('neaktivni-clanak', $xml);
        $this->assertStringContainsString('<loc>'.route('pages.show', ['slug' => 'hrvatska-stranica']).'</loc>', $xml);
        $this->assertStringContainsString('<loc>'.route('pages.show', ['slug' => 'english-page']).'</loc>', $xml);
        $this->assertStringNotContainsString('<loc>'.route('search.index.en').'</loc>', $xml);
        $this->assertStringContainsString('<loc>'.route('eu-funds.calls.show', ['slug' => 'hrvatski-poziv']).'</loc>', $xml);
        $this->assertStringContainsString('<loc>'.route('eu-funds.calls.show.en', ['slug' => 'english-call']).'</loc>', $xml);
        $this->assertStringContainsString('<loc>'.route('resources.show', ['slug' => 'objavljeni-resurs']).'</loc>', $xml);
        $this->assertStringContainsString('<loc>'.route('glossary.index').'</loc>', $xml);
        $this->assertStringContainsString('<loc>'.route('glossary.show', ['slug' => 'objavljeni-pojam']).'</loc>', $xml);
        $this->assertStringNotContainsString('svijet-financija</loc>', $xml);
        $this->assertStringNotContainsString('pojam-iz-druge-zbirke', $xml);
        $this->assertMatchesRegularExpression('#<lastmod>[^<]+</lastmod>#', $xml);

        $locations = collect($document->getElementsByTagName('loc'))
            ->map(static fn (\DOMElement $element): string => $element->textContent);
        $this->assertSame($locations->count(), $locations->unique()->count());
    }

    public function test_sitemap_omits_inactive_locales_and_cms_routes_without_exact_content(): void
    {
        Language::query()->where('code', 'en')->update(['is_active' => false]);

        $servicesPage = ServicePage::query()->updateOrCreate([
            'template_key' => ServicePageTemplateRegistry::SERVICES_INDEX,
        ], [
            'code' => 'services',
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);
        $servicesPage->translations()->updateOrCreate(
            ['locale' => 'hr'],
            ['title' => 'Usluge', 'slug' => 'usluge'],
        );
        $servicesPage->translations()->updateOrCreate(
            ['locale' => 'en'],
            ['title' => 'Services', 'slug' => 'services'],
        );

        $xml = $this->get(route('sitemap'))->assertOk()->getContent();

        $this->assertIsString($xml);
        $this->assertStringContainsString('<loc>'.route('services.index').'</loc>', $xml);
        $this->assertStringNotContainsString('<loc>'.route('services.index.en').'</loc>', $xml);
        $this->assertStringNotContainsString('<loc>'.route('contact.create.en').'</loc>', $xml);
        $this->assertStringNotContainsString('<loc>'.route('glossary.index').'</loc>', $xml);
    }

    public function test_sitemap_omits_english_contact_without_exact_cms_content(): void
    {
        ContentBlock::query()
            ->where('type', 'home_stats')
            ->get()
            ->each(fn (ContentBlock $block) => $block->translations()
                ->where('locale', 'en')
                ->delete());
        ContentBlockResolver::bumpCacheVersion();

        $xml = $this->get(route('sitemap'))->assertOk()->getContent();

        $this->assertIsString($xml);
        $this->assertStringContainsString('<loc>'.route('contact.create').'</loc>', $xml);
        $this->assertStringNotContainsString('<loc>'.route('contact.create.en').'</loc>', $xml);
    }

    public function test_production_robots_file_excludes_internal_routes_and_advertises_the_canonical_sitemap(): void
    {
        $robots = file_get_contents(public_path('robots.txt'));

        $this->assertIsString($robots);
        $this->assertStringContainsString("Allow: /\n", $robots);
        $this->assertStringContainsString("Disallow: /admin\n", $robots);
        $this->assertStringContainsString("Disallow: /newsletter/csrf-token\n", $robots);
        $this->assertStringContainsString(
            'Sitemap: https://www.alphacapitalis.com/sitemap.xml',
            $robots,
        );
    }
}
