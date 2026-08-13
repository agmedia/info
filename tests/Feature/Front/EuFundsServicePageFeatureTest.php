<?php

namespace Tests\Feature\Front;

use App\Models\Catalog\Category\Category;
use App\Models\Catalog\Category\CategoryTranslation;
use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Blog\BlogPostTranslation;
use App\Models\Content\Call\CallPost;
use App\Models\Content\Call\CallPostTranslation;
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
            ->assertSeeText('Otvoreno')
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
}
