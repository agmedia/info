<?php

namespace Tests\Feature\Front;

use App\Models\Catalog\Category\Category;
use App\Models\Catalog\Category\CategoryTranslation;
use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Blog\BlogPostTranslation;
use App\Models\Content\Call\CallPost;
use App\Models\Content\Call\CallPostTranslation;
use App\Models\Content\Service\ServicePage;
use App\Models\Settings\Local\Language;
use App\Support\Content\ServicePageTemplateRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EuFundsServicePageFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_eu_funds_service_page_renders_three_latest_posts_above_standard_service_cta(): void
    {
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        $category = Category::query()->create([
            'scope' => Category::SCOPE_BLOG,
            'code' => 'eu-fondovi',
            'is_active' => true,
            'show_in_menu' => true,
            'sort_order' => 1,
        ]);

        CategoryTranslation::query()->create([
            'category_id' => $category->id,
            'scope' => Category::SCOPE_BLOG,
            'locale' => 'hr',
            'name' => 'EU fondovi',
            'slug' => 'eu-fondovi',
            'description' => 'EU fondovi description',
        ]);

        CategoryTranslation::query()->create([
            'category_id' => $category->id,
            'scope' => Category::SCOPE_BLOG,
            'locale' => 'en',
            'name' => 'EU Funds',
            'slug' => 'eu-funds',
            'description' => 'EU funds description',
        ]);

        foreach (range(1, 6) as $index) {
            $post = BlogPost::query()->create([
                'code' => 'eu-funds-post-'.$index,
                'is_active' => true,
                'published_at' => now()->subDays($index),
            ]);

            BlogPostTranslation::query()->create([
                'post_id' => $post->id,
                'locale' => 'hr',
                'title' => sprintf('EU fondovi objava %02d', $index),
                'slug' => sprintf('eu-fondovi-objava-%02d', $index),
                'excerpt' => sprintf('Sažetak EU fondovi objave %02d.', $index),
                'body_html' => '<p>Blog body</p>',
            ]);

            $post->categories()->sync([
                $category->id => [
                    'sort_order' => 0,
                    'is_primary' => true,
                ],
            ]);
        }

        $response = $this->get('/eu-fondovi');

        $response->assertOk()
            ->assertSee('aria-label="Objave iz kategorije EU fondovi"', false)
            ->assertSeeText('EU fondovi objava 01')
            ->assertSeeText('EU fondovi objava 02')
            ->assertSeeText('EU fondovi objava 03')
            ->assertDontSeeText('EU fondovi objava 04')
            ->assertDontSeeText('EU fondovi objava 05')
            ->assertDontSeeText('EU fondovi objava 06')
            ->assertSee('news-section ac-advisory-news', false)
            ->assertSee('contact-cta ac-advisory-contact-cta', false)
            ->assertSee('id="eu-funds-cta"', false)
            ->assertSee('front-theme/styles/pages/eu-funds.css', false)
            ->assertDontSee('data-eu-funds-blog-splide', false)
            ->assertDontSee('https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js', false)
            ->assertDontSee('id="eu-funds-contact"', false)
            ->assertDontSee('front-contact-form', false);

        $content = $response->getContent();
        $this->assertIsString($content);

        $ctaPosition = strpos($content, 'id="eu-funds-cta"');
        $blogPosition = strpos($content, 'id="ac-eu-funds-news-title"');

        $this->assertNotFalse($ctaPosition);
        $this->assertNotFalse($blogPosition);
        $this->assertLessThan($ctaPosition, $blogPosition);
    }

    public function test_eu_funds_service_page_prefers_call_posts_from_content_module(): void
    {
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        $upcomingCategory = Category::query()->create([
            'scope' => Category::SCOPE_CALL,
            'code' => 'pozivi-u-najavi',
            'is_active' => true,
            'show_in_menu' => false,
            'sort_order' => 1,
        ]);

        CategoryTranslation::query()->create([
            'category_id' => $upcomingCategory->id,
            'scope' => Category::SCOPE_CALL,
            'locale' => 'hr',
            'name' => 'Pozivi u najavi',
            'slug' => 'pozivi-u-najavi',
            'description' => 'Pozivi u najavi',
        ]);

        $openCategory = Category::query()->create([
            'scope' => Category::SCOPE_CALL,
            'code' => 'otvoreni-pozivi',
            'is_active' => true,
            'show_in_menu' => false,
            'sort_order' => 2,
        ]);

        CategoryTranslation::query()->create([
            'category_id' => $openCategory->id,
            'scope' => Category::SCOPE_CALL,
            'locale' => 'hr',
            'name' => 'Otvoreni pozivi',
            'slug' => 'otvoreni-pozivi',
            'description' => 'Otvoreni pozivi',
            'payload' => ['status_label' => 'CMS status otvoreno'],
        ]);

        $call = CallPost::query()->create([
            'code' => 'integrator',
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);

        CallPostTranslation::query()->create([
            'post_id' => $call->id,
            'locale' => 'hr',
            'title' => 'Integrator',
            'slug' => 'integrator',
            'excerpt' => 'Opis poziva Integrator.',
            'body_html' => '<p>Detalji poziva Integrator.</p>',
            'payload' => [
                'date_labels' => [
                    'published' => 'Objavljeno',
                ],
            ],
        ]);

        $call->categories()->sync([
            $openCategory->id => [
                'sort_order' => 0,
                'is_primary' => true,
            ],
        ]);

        $response = $this->get('/eu-fondovi');

        $response->assertOk()
            ->assertSeeText('Otvoreni pozivi')
            ->assertSeeText('CMS status otvoreno')
            ->assertSeeText('Integrator')
            ->assertSeeText('Objavljeno:')
            ->assertSee('/eu-fondovi/pozivi/integrator', false);
    }

    public function test_eu_funds_service_page_renders_service_layout_and_separate_sources(): void
    {
        $response = $this->get('/eu-fondovi');

        $response->assertOk()
            ->assertSeeText('EU fondovi')
            ->assertSee('aria-label="Što su EU fondovi?"', false)
            ->assertSee('aria-label="Naše usluge"', false)
            ->assertSeeText('Analiza i odabir natječaja')
            ->assertSeeText('Izrada projektne prijave')
            ->assertSeeText('Provedba i koordinacija projekta')
            ->assertSee('aria-label="Naš pristup"', false)
            ->assertSee('aria-label="Dostupni izvori financiranja"', false)
            ->assertSeeText('Otvoreni natječaji')
            ->assertSeeText('Natječaji u najavi')
            ->assertSeeText('Financijski instrumenti')
            ->assertSeeText('HBOR krediti')
            ->assertSeeText('HAMAG zajmovi')
            ->assertSee('aria-label="Porezne olakšice, zakoni i uredbe"', false)
            ->assertSee('aria-label="Razgovarajmo o vašem projektu"', false)
            ->assertSee(route('eu-funds.questionnaire.create'), false)
            ->assertDontSeeText('Pregledajte natječaje')
            ->assertDontSeeText('VFO, Mehanizam oporavka')
            ->assertDontSeeText('14+ mlrd EUR')
            ->assertDontSeeText('11+ mlrd EUR')
            ->assertDontSeeText('6,3 mlrd EUR')
            ->assertDontSee('https://alphacapitalis.com/eu-fondovi-upitnik/', false)
            ->assertDontSeeText('poveznice su postavljene samo tamo gdje već postoji lokalni blog zapis ili lokalni dokument')
            ->assertDontSee('ac-eu-process-index', false)
            ->assertDontSee('>01<', false);
    }

    public function test_english_eu_funds_links_require_exact_translations_and_use_localized_questionnaire_url(): void
    {
        Language::query()->updateOrCreate(['code' => 'hr'], [
            'locale' => 'hr_HR',
            'name' => 'Croatian',
            'native_name' => 'Hrvatski',
            'direction' => 'ltr',
            'is_default' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        Language::query()->updateOrCreate(['code' => 'en'], [
            'locale' => 'en_US',
            'name' => 'English',
            'native_name' => 'English',
            'direction' => 'ltr',
            'is_default' => false,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $post = BlogPost::query()->create([
            'code' => 'croatian-only-resource',
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);
        BlogPostTranslation::query()->create([
            'post_id' => $post->id,
            'locale' => 'hr',
            'title' => 'Samo hrvatski resurs',
            'slug' => 'samo-hrvatski-resurs',
            'body_html' => '<p>Hrvatski sadržaj.</p>',
        ]);

        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::EU_FUNDS)
            ->firstOrFail();
        $translation = $page->translations()->where('locale', 'en')->firstOrFail();
        $payload = (array) $translation->payload;
        $payload['resources'] = [
            'title' => 'English resources',
            'intro' => '',
            'cards' => [[
                'title' => 'Project questionnaire',
                'body_html' => '',
                'primary_link' => [
                    'type' => 'external',
                    'label' => 'Complete the questionnaire',
                    'url' => '/eu-fondovi/upitnik',
                ],
                'secondary_link' => [
                    'type' => 'pdf',
                    'label' => 'Brochure',
                    'path' => 'front-theme/documents/eu-fondovi/zakon-o-poticanju-ulaganja-brosura.pdf',
                ],
                'groups' => [[
                    'label' => 'Further reading',
                    'items' => [[
                        'title' => 'More information',
                        'link' => [
                            'type' => 'blog',
                            'slug' => 'samo-hrvatski-resurs',
                        ],
                    ]],
                ]],
            ]],
        ];
        $translation->update(['payload' => $payload]);

        $response = $this->withSession(['front_locale' => 'en'])->get('/eu-funds');

        $response->assertOk()
            ->assertSee('href="'.route('eu-funds.questionnaire.create.en').'"', false)
            ->assertSeeText('More information')
            ->assertDontSee('href="'.route('blog.show', ['slug' => 'samo-hrvatski-resurs']).'"', false)
            ->assertDontSee('front-theme/documents/eu-fondovi/zakon-o-poticanju-ulaganja-brosura.pdf', false)
            ->assertDontSee('/eu-fondovi/upitnik', false);

        BlogPostTranslation::query()->create([
            'post_id' => $post->id,
            'locale' => 'en',
            'title' => 'English resource',
            'slug' => 'english-resource',
            'body_html' => '<p>English content.</p>',
        ]);
        $payload['resources']['cards'][0]['secondary_link']['locale'] = 'en';
        $translation->update(['payload' => $payload]);

        $this->withSession(['front_locale' => 'en'])
            ->get('/eu-funds')
            ->assertOk()
            ->assertSee('href="'.route('blog.show', ['slug' => 'english-resource']).'"', false)
            ->assertSee('front-theme/documents/eu-fondovi/zakon-o-poticanju-ulaganja-brosura.pdf', false)
            ->assertDontSee('/blog/samo-hrvatski-resurs', false);
    }
}
