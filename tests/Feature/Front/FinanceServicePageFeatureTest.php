<?php

namespace Tests\Feature\Front;

use App\Models\Catalog\Category\Category;
use App\Models\Catalog\Category\CategoryTranslation;
use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Blog\BlogPostTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceServicePageFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_finance_service_page_renders_updated_hr_content_in_document_order(): void
    {
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        $this->get('/financije')
            ->assertOk()
            ->assertSeeText('Spajanja i preuzimanja (M&A)')
            ->assertSeeText('Ugovor o povjerljivosti, memorandum i ponude')
            ->assertSeeText('Nakon potpisivanja Ugovora o povjerljivosti podataka šalje se Informacijski memorandum')
            ->assertSeeText('Studije su prilagođene potrebama investitora, vlasnika i financijskih institucija')
            ->assertSeeText('Ključne aktivnosti')
            ->assertSee('<table class="ac-finance-phase-table">', false);
    }

    public function test_finance_service_page_renders_blog_posts_from_finance_category(): void
    {
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        $category = Category::query()->create([
            'scope' => Category::SCOPE_BLOG,
            'code' => 'finance',
            'is_active' => true,
            'show_in_menu' => true,
            'sort_order' => 1,
        ]);

        CategoryTranslation::query()->create([
            'category_id' => $category->id,
            'scope' => Category::SCOPE_BLOG,
            'locale' => 'hr',
            'name' => 'Financije',
            'slug' => 'finance',
            'description' => 'Finance description',
        ]);

        CategoryTranslation::query()->create([
            'category_id' => $category->id,
            'scope' => Category::SCOPE_BLOG,
            'locale' => 'en',
            'name' => 'Finance',
            'slug' => 'finance',
            'description' => 'Finance description',
        ]);

        $post = BlogPost::query()->create([
            'code' => 'finance-post-1',
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);

        BlogPostTranslation::query()->create([
            'post_id' => $post->id,
            'locale' => 'hr',
            'title' => 'Nova objava iz financija',
            'slug' => 'nova-objava-iz-financija',
            'excerpt' => 'Sažetak objave iz financija.',
            'body_html' => '<p>Blog body</p>',
        ]);

        $post->categories()->sync([
            $category->id => [
                'sort_order' => 0,
                'is_primary' => true,
            ],
        ]);

        $this->get('/financije')
            ->assertOk()
            ->assertSeeText('Zadnje objave i novosti')
            ->assertSeeText('Nova objava iz financija')
            ->assertSee('data-finance-blog-splide', false)
            ->assertSee('/blog/nova-objava-iz-financija', false);
    }
}
