<?php

namespace Tests\Feature\Front;

use App\Models\Catalog\Category\Category;
use App\Models\Catalog\Category\CategoryTranslation;
use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Blog\BlogPostTranslation;
use App\Models\Content\Service\ServicePage;
use App\Support\Content\ServicePageTemplateRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaxServicePageFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_tax_service_page_renders_seeded_content_and_custom_translation_overrides(): void
    {
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::TAX)
            ->firstOrFail();

        $page->translations()
            ->where('locale', 'hr')
            ->update([
                'payload' => [
                    'hero' => [
                        'brand_title' => 'ALPHA CAPITALIS TAX',
                        'intro' => 'Custom uvodni tekst za porezno savjetovanje.',
                    ],
                    'services' => [
                        'items' => [
                            [
                                'title' => 'Porezna mišljenja po mjeri',
                                'text' => 'Pisano mišljenje za specifične slučajeve i porezne nedoumice.',
                            ],
                        ],
                    ],
                    'meeting' => [
                        'direct_phone_label' => 'Telefon ureda',
                    ],
                ],
            ]);

        $this->get('/porezi')
            ->assertOk()
            ->assertSeeText('ALPHA CAPITALIS TAX')
            ->assertSeeText('Custom uvodni tekst za porezno savjetovanje.')
            ->assertSeeText('Porezna mišljenja po mjeri')
            ->assertSeeText('Telefon ureda')
            ->assertSeeText('Transferne cijene')
            ->assertSee('data-tax-blog-splide', false);
    }

    public function test_tax_service_page_renders_blog_posts_from_tax_category(): void
    {
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        $category = Category::query()->create([
            'scope' => Category::SCOPE_BLOG,
            'code' => 'tax',
            'is_active' => true,
            'show_in_menu' => true,
            'sort_order' => 1,
        ]);

        CategoryTranslation::query()->create([
            'category_id' => $category->id,
            'scope' => Category::SCOPE_BLOG,
            'locale' => 'hr',
            'name' => 'Porezi',
            'slug' => 'porezi',
            'description' => 'Tax description',
        ]);

        CategoryTranslation::query()->create([
            'category_id' => $category->id,
            'scope' => Category::SCOPE_BLOG,
            'locale' => 'en',
            'name' => 'Tax',
            'slug' => 'tax',
            'description' => 'Tax description',
        ]);

        $post = BlogPost::query()->create([
            'code' => 'tax-post-1',
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);

        BlogPostTranslation::query()->create([
            'post_id' => $post->id,
            'locale' => 'hr',
            'title' => 'Porezni vodič za poduzetnike',
            'slug' => 'porezni-vodic-za-poduzetnike',
            'excerpt' => 'Sažetak objave iz područja poreza.',
            'body_html' => '<p>Blog body</p>',
        ]);

        $post->categories()->sync([
            $category->id => [
                'sort_order' => 0,
                'is_primary' => true,
            ],
        ]);

        $this->get('/porezi')
            ->assertOk()
            ->assertSeeText('Najnovije objave iz kategorije Porezi')
            ->assertSeeText('Porezni vodič za poduzetnike')
            ->assertSee('/blog/porezni-vodic-za-poduzetnike', false)
            ->assertSee('data-tax-blog-splide', false);
    }
}
