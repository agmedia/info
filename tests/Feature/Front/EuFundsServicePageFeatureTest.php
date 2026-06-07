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

    public function test_eu_funds_service_page_renders_five_latest_posts_below_service_cta(): void
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
            ->assertSeeText('Objave iz kategorije EU fondovi')
            ->assertSeeText('EU fondovi objava 01')
            ->assertSeeText('EU fondovi objava 02')
            ->assertSeeText('EU fondovi objava 03')
            ->assertSeeText('EU fondovi objava 04')
            ->assertSeeText('EU fondovi objava 05')
            ->assertDontSeeText('EU fondovi objava 06')
            ->assertSee('data-eu-funds-blog-splide', false)
            ->assertSee('https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js', false)
            ->assertSee('id="eu-funds-cta"', false)
            ->assertDontSee('id="eu-funds-contact"', false)
            ->assertDontSee('front-contact-form', false);

        $content = $response->getContent();
        $this->assertIsString($content);

        $ctaPosition = strpos($content, 'id="eu-funds-cta"');
        $blogPosition = strpos($content, 'id="ac-eu-blog-title"');

        $this->assertNotFalse($ctaPosition);
        $this->assertNotFalse($blogPosition);
        $this->assertLessThan($blogPosition, $ctaPosition);
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
            ->assertSeeText('Što su EU fondovi?')
            ->assertSeeText('Naše usluge')
            ->assertSeeText('Analiza i odabir natječaja')
            ->assertSeeText('Izrada projektne prijave')
            ->assertSeeText('Provedba i koordinacija projekta')
            ->assertSeeText('Naš pristup')
            ->assertSeeText('Dostupni izvori financiranja')
            ->assertSeeText('Otvoreni natječaji')
            ->assertSeeText('Natječaji u najavi')
            ->assertSeeText('Financijski instrumenti')
            ->assertSeeText('HBOR krediti')
            ->assertSeeText('HAMAG zajmovi')
            ->assertSeeText('Porezne olakšice, zakoni i uredbe')
            ->assertSeeText('Razgovarajmo o vašem projektu')
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
