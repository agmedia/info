<?php

namespace Tests\Feature\Front;

use App\Models\Catalog\Category\Category;
use App\Models\Content\Call\CallPost;
use App\Models\Content\Page\InfoPage;
use App\Models\Content\Service\ServicePage;
use App\Models\Settings\Local\Language;
use App\Services\Front\NavigationMenuService;
use App\Services\Settings\SystemSettingsService;
use App\Support\Content\ServicePageTemplateRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizedServiceRouteFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        Language::query()->updateOrCreate(['code' => 'hr'], [
            'locale' => 'hr_HR',
            'name' => 'Croatian',
            'native_name' => 'Hrvatski',
            'is_default' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        Language::query()->updateOrCreate(['code' => 'en'], [
            'locale' => 'en_GB',
            'name' => 'English',
            'native_name' => 'English',
            'is_default' => false,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $this->createLocalizedServicePages();
        app(SystemSettingsService::class)->put(NavigationMenuService::CHROME_SETTINGS_KEY, [
            'en' => [
                'header_calculator_cta_label' => 'IFRS 16 Calculator',
            ],
        ]);
    }

    public function test_fresh_sessions_infer_english_from_canonical_service_paths(): void
    {
        foreach (['/services', '/audit', '/accounting', '/advisory', '/eu-funds'] as $path) {
            $this->app['session']->flush();

            $this->get($path)
                ->assertOk()
                ->assertSessionHas('front_locale', 'en');
        }

        $this->withSession(['front_locale' => 'hr'])
            ->get('/services')
            ->assertOk()
            ->assertSessionHas('front_locale', 'en');

        $this->withSession(['front_locale' => 'en'])
            ->get('/usluge')
            ->assertOk()
            ->assertSessionHas('front_locale', 'hr');

        $this->withSession(['front_locale' => 'hr'])
            ->get('/audit')
            ->assertOk()
            ->assertSessionHas('front_locale', 'en');

        $this->withSession(['front_locale' => 'en'])
            ->get('/revizija')
            ->assertOk()
            ->assertSessionHas('front_locale', 'hr');
    }

    public function test_locale_switch_ignores_external_referers_and_preserves_safe_query_targets(): void
    {
        $this->withHeader('Referer', 'https://malicious.example/phish?next=/dashboard')
            ->get(route('front.locale.switch', ['code' => 'en']))
            ->assertRedirect(route('home'))
            ->assertSessionHas('front_locale', 'en');

        $safeTarget = route('services.index.en').'?source=language-switch#overview';
        $this->withHeader('Referer', 'https://malicious.example/phish')
            ->get(route('front.locale.switch', [
                'code' => 'en',
                'redirect' => $safeTarget,
            ]))
            ->assertRedirect($safeTarget)
            ->assertSessionHas('front_locale', 'en');

        $this->withHeader('Referer', 'https://malicious.example/phish')
            ->get(route('front.locale.switch', [
                'code' => 'hr',
                'redirect' => 'https://malicious.example/redirect?source=language-switch',
            ]))
            ->assertRedirect(route('home'))
            ->assertSessionHas('front_locale', 'hr');
    }

    public function test_search_page_uses_canonical_locale_paths_and_path_authoritative_sessions(): void
    {
        $this->assertSame('https://info.test/pretraga', route('search.index'));
        $this->assertSame('https://info.test/search', route('search.index.en'));

        $this->withSession(['front_locale' => 'hr'])
            ->get('/search?q=audit')
            ->assertOk()
            ->assertSessionHas('front_locale', 'en')
            ->assertSee('action="https://info.test/search"', false)
            ->assertSee('<meta name="robots" content="noindex,follow">', false)
            ->assertSee('"target":"https://info.test/search?q={search_term_string}"', false);

        $this->withSession(['front_locale' => 'en'])
            ->get('/pretraga?q=revizija')
            ->assertOk()
            ->assertSessionHas('front_locale', 'hr')
            ->assertSee('action="https://info.test/pretraga"', false)
            ->assertSee('<meta name="robots" content="noindex,follow">', false)
            ->assertSee('"target":"https://info.test/pretraga?q={search_term_string}"', false);
    }

    public function test_english_route_names_drive_layout_seo_open_graph_and_schema(): void
    {
        $audit = $this->getFresh('/audit');
        $audit->assertOk()
            ->assertSee('front-route-audit', false)
            ->assertSee('<title>English Audit SEO</title>', false)
            ->assertSee('<meta name="description" content="English Audit description.">', false)
            ->assertSee('<meta property="og:title" content="English Audit SEO">', false)
            ->assertSee('"@type":"WebPage","name":"English Audit SEO","url":"https://info.test/audit","description":"English Audit description."', false)
            ->assertDontSee('HR metadata sentinel');

        $accounting = $this->getFresh('/accounting');
        $accounting->assertOk()
            ->assertSee('front-route-accounting', false)
            ->assertSee('header-cta--calculator', false)
            ->assertSee('href="'.route('lease-calculator.show').'"', false)
            ->assertSee('<title>English Accounting SEO</title>', false)
            ->assertSee('"@type":"WebPage","name":"English Accounting SEO","url":"https://info.test/accounting","description":"English Accounting description."', false)
            ->assertDontSee('HR metadata sentinel');

        $euFunds = $this->getFresh('/eu-funds');
        $euFunds->assertOk()
            ->assertSee('front-route-advisory', false)
            ->assertSee('front-route-eu-funds', false)
            ->assertSee('<title>English EU Funds SEO</title>', false)
            ->assertSee('"name":"English EU Funds","item":"https://info.test/eu-funds"', false)
            ->assertSee('"@type":"WebPage","name":"English EU Funds SEO","url":"https://info.test/eu-funds","description":"English EU Funds description."', false)
            ->assertDontSee('"name":"EU fondovi"', false)
            ->assertDontSee('HR metadata sentinel');
    }

    public function test_english_call_route_uses_exact_slug_metadata_schema_and_cms_date_label(): void
    {
        $category = Category::query()->create([
            'scope' => Category::SCOPE_CALL,
            'code' => 'localized-call-status',
            'is_active' => true,
            'show_in_menu' => false,
            'sort_order' => 1,
        ]);
        $category->translations()->create([
            'scope' => Category::SCOPE_CALL,
            'locale' => 'en',
            'name' => 'Open calls',
            'slug' => 'open-calls',
        ]);

        $call = CallPost::query()->create([
            'code' => 'localized-seo-call',
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);
        $call->translations()->create([
            'locale' => 'hr',
            'title' => 'Hrvatski naslov poziva',
            'slug' => 'hrvatski-poziv',
            'meta_title' => 'HR call metadata sentinel',
            'meta_description' => 'HR call description sentinel.',
        ]);
        $call->translations()->create([
            'locale' => 'en',
            'title' => 'English call title',
            'slug' => 'english-call',
            'excerpt' => 'English call excerpt.',
            'meta_title' => 'English Call SEO',
            'meta_description' => 'English Call SEO description.',
            'payload' => [
                'date_labels' => [
                    'published' => 'Published from call CMS',
                ],
            ],
        ]);
        $call->categories()->attach($category->id, ['sort_order' => 0, 'is_primary' => true]);

        $this->getFresh('/eu-funds')
            ->assertOk()
            ->assertSeeText('Published from call CMS')
            ->assertDontSeeText('Objavljeno');

        $this->getFresh('/eu-funds/calls/english-call')
            ->assertOk()
            ->assertSee('<title>English Call SEO</title>', false)
            ->assertSee('<meta name="description" content="English Call SEO description.">', false)
            ->assertSee('"name":"English EU Funds","item":"https://info.test/eu-funds"', false)
            ->assertSee('"@type":"Article","headline":"English Call SEO"', false)
            ->assertSee('"description":"English Call SEO description."', false)
            ->assertDontSee('HR call metadata sentinel')
            ->assertDontSee('Hrvatski naslov poziva');

        $this->getFresh('/eu-funds/calls/hrvatski-poziv')->assertNotFound();
    }

    public function test_strict_english_advisory_subpage_requires_its_own_payload(): void
    {
        $this->getFresh('/advisory/tax-advisory')->assertNotFound();

        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::ADVISORY)
            ->firstOrFail();
        $translation = $page->translations()->where('locale', 'en')->firstOrFail();
        $translation->update([
            'payload' => [
                'tax' => [
                    'show_pandea' => true,
                    'hero_image_alt' => 'English tax advisory image',
                    'meta_title' => 'English tax advisory metadata',
                    'canonical_url' => 'https://example.test/tax-advisory',
                    'pandea' => ['title' => 'English network metadata'],
                ],
            ],
        ]);

        $this->getFresh('/advisory/tax-advisory')->assertNotFound();

        $translation->update([
            'payload' => [
                'tax' => [
                    'title' => 'English tax advisory',
                    'hero_intro' => 'English tax advisory intro.',
                ],
            ],
        ]);

        $this->getFresh('/advisory/tax-advisory')
            ->assertOk()
            ->assertSeeText('English tax advisory');
    }

    public function test_investment_incentives_switch_falls_back_to_exact_english_advisory_when_english_section_is_empty(): void
    {
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::ADVISORY)
            ->firstOrFail();
        $croatianTranslation = $page->translations()->where('locale', 'hr')->firstOrFail();
        $englishTranslation = $page->translations()->where('locale', 'en')->firstOrFail();
        $croatianPayload = (array) $croatianTranslation->payload;
        $englishPayload = (array) $englishTranslation->payload;
        $croatianPayload['zopu'] = [
            'title' => 'Zakon o poticanju ulaganja',
            'hero_intro' => 'Hrvatski ZoPU sadržaj iz CMS-a.',
        ];
        $englishPayload['zopu'] = [];
        $croatianTranslation->update(['payload' => $croatianPayload]);
        $englishTranslation->update(['payload' => $englishPayload]);

        $croatianPath = '/savjetovanje/pribavljanje-financiranja/zakon-o-poticanju-ulaganja';
        $englishPath = '/advisory/raising-finance/investment-incentives';

        $this->app['session']->flush();
        $croatianResponse = $this->get($croatianPath)
            ->assertOk()
            ->assertSessionHas('front_locale', 'hr')
            ->assertSeeText('Zakon o poticanju ulaganja');

        $expectedEnglishFallbackSwitch = route('front.locale.switch', [
            'code' => 'en',
            'redirect' => url('/advisory'),
        ]);
        $missingEnglishSectionSwitch = route('front.locale.switch', [
            'code' => 'en',
            'redirect' => url($englishPath),
        ]);
        $croatianResponse
            ->assertSee('href="'.$expectedEnglishFallbackSwitch.'"', false)
            ->assertDontSee('href="'.$missingEnglishSectionSwitch.'"', false);

        $this->get($expectedEnglishFallbackSwitch)
            ->assertRedirect(url('/advisory'))
            ->assertSessionHas('front_locale', 'en');

        $this->app['session']->flush();
        $this->get($englishPath)
            ->assertNotFound()
            ->assertSessionHas('front_locale', 'en');

        $this->withSession(['front_locale' => 'hr'])
            ->get($englishPath)
            ->assertNotFound()
            ->assertSessionHas('front_locale', 'en');

        $this->withSession(['front_locale' => 'en'])
            ->get($croatianPath)
            ->assertOk()
            ->assertSessionHas('front_locale', 'hr')
            ->assertSeeText('Zakon o poticanju ulaganja');
    }

    public function test_structural_service_slugs_are_locked_to_the_public_routes(): void
    {
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::AUDIT)
            ->firstOrFail();
        $translation = $page->translations()->where('locale', 'en')->firstOrFail();

        $translation->update(['slug' => 'editable-slug-that-would-404']);

        $this->assertSame('audit', $translation->fresh()->slug);
        $this->assertSame(
            'revizija',
            ServicePageTemplateRegistry::canonicalStructuralSlug(ServicePageTemplateRegistry::AUDIT, 'hr_HR')
        );
    }

    public function test_unique_info_page_slugs_are_authoritative_for_fresh_and_stale_sessions(): void
    {
        $about = InfoPage::query()->where('code', 'about-us')->firstOrFail();
        $about->update(['is_active' => true, 'published_at' => now()->subDay()]);
        $about->translations()->updateOrCreate(['locale' => 'hr'], [
            'title' => 'O nama',
            'slug' => 'o-nama',
        ]);
        $about->translations()->updateOrCreate(['locale' => 'en'], [
            'title' => 'About us',
            'slug' => 'about-us',
            'payload' => ['about_page' => ['hero' => ['title' => 'About us']]],
        ]);

        $career = InfoPage::query()->where('code', 'career')->firstOrFail();
        $career->update(['is_active' => true, 'published_at' => now()->subDay()]);
        $career->translations()->updateOrCreate(['locale' => 'hr'], [
            'title' => 'Karijera',
            'slug' => 'karijera',
        ]);
        $career->translations()->updateOrCreate(['locale' => 'en'], [
            'title' => 'Careers',
            'slug' => 'careers',
            'payload' => [],
        ]);

        $this->getFresh('/about-us')
            ->assertOk()
            ->assertSessionHas('front_locale', 'en')
            ->assertSee('href="'.route('front.locale.switch', [
                'code' => 'hr',
                'redirect' => url('/o-nama'),
            ]).'"', false);

        $this->withSession(['front_locale' => 'hr'])
            ->get('/about-us')
            ->assertOk()
            ->assertSessionHas('front_locale', 'en');

        $this->withSession(['front_locale' => 'en'])
            ->get('/o-nama')
            ->assertOk()
            ->assertSessionHas('front_locale', 'hr');

        $this->getFresh('/careers')
            ->assertOk()
            ->assertSessionHas('front_locale', 'en');

        $this->withSession(['front_locale' => 'hr'])
            ->get('/careers')
            ->assertOk()
            ->assertSessionHas('front_locale', 'en');

        $this->withSession(['front_locale' => 'en'])
            ->get('/karijera')
            ->assertOk()
            ->assertSessionHas('front_locale', 'hr');
    }

    public function test_shared_info_page_slug_keeps_the_explicit_session_locale(): void
    {
        $page = InfoPage::query()->create([
            'code' => 'shared-localized-slug',
            'layout' => 'default',
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);
        $page->translations()->create([
            'locale' => 'hr',
            'title' => 'Hrvatski zajednički slug',
            'slug' => 'shared-slug',
            'body_html' => '<p>Hrvatski sadržaj zajedničkog sluga.</p>',
        ]);
        $page->translations()->create([
            'locale' => 'en',
            'title' => 'English shared slug',
            'slug' => 'shared-slug',
            'body_html' => '<p>English shared-slug content.</p>',
        ]);

        $this->withSession(['front_locale' => 'en'])
            ->get('/shared-slug')
            ->assertOk()
            ->assertSessionHas('front_locale', 'en')
            ->assertSeeText('English shared-slug content.')
            ->assertDontSeeText('Hrvatski sadržaj zajedničkog sluga.');

        $this->withSession(['front_locale' => 'hr'])
            ->get('/shared-slug')
            ->assertOk()
            ->assertSessionHas('front_locale', 'hr')
            ->assertSeeText('Hrvatski sadržaj zajedničkog sluga.')
            ->assertDontSeeText('English shared-slug content.');
    }

    private function getFresh(string $path): \Illuminate\Testing\TestResponse
    {
        $this->app['session']->flush();

        return $this->get($path);
    }

    private function createLocalizedServicePages(): void
    {
        $definitions = [
            ServicePageTemplateRegistry::SERVICES_INDEX => ['services', 'Services', 'English Services SEO', 'English Services description.'],
            ServicePageTemplateRegistry::AUDIT => ['audit', 'English Audit', 'English Audit SEO', 'English Audit description.'],
            ServicePageTemplateRegistry::ACCOUNTING => ['accounting', 'English Accounting', 'English Accounting SEO', 'English Accounting description.'],
            ServicePageTemplateRegistry::ADVISORY => ['advisory', 'English Advisory', 'English Advisory SEO', 'English Advisory description.'],
            ServicePageTemplateRegistry::EU_FUNDS => ['eu-funds', 'English EU Funds', 'English EU Funds SEO', 'English EU Funds description.'],
        ];

        foreach ($definitions as $templateKey => [$slug, $title, $metaTitle, $metaDescription]) {
            $page = ServicePage::query()->firstOrCreate(['template_key' => $templateKey], [
                'code' => 'localized-'.$templateKey,
                'is_active' => true,
                'published_at' => now()->subDay(),
            ]);
            $page->update([
                'is_active' => true,
                'published_at' => now()->subDay(),
            ]);
            $page->translations()->updateOrCreate(['locale' => 'en'], [
                'title' => $title,
                'slug' => $slug,
                'meta_title' => $metaTitle,
                'meta_description' => $metaDescription,
                'payload' => [],
            ]);
            $page->translations()->updateOrCreate(['locale' => 'hr'], [
                'title' => 'HR title sentinel',
                'slug' => 'ignored-by-structural-lock',
                'meta_title' => 'HR metadata sentinel',
                'meta_description' => 'HR metadata sentinel description.',
                'payload' => [],
            ]);
        }
    }
}
