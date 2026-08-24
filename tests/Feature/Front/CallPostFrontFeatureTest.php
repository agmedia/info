<?php

namespace Tests\Feature\Front;

use App\Models\Catalog\Category\Category;
use App\Models\Catalog\Category\CategoryTranslation;
use App\Models\Content\Call\CallPost;
use App\Models\Content\Call\CallPostTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CallPostFrontFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_call_post_detail_page_renders_imported_content(): void
    {
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        $category = Category::query()->create([
            'scope' => Category::SCOPE_CALL,
            'code' => 'otvoreni-pozivi',
            'is_active' => true,
            'show_in_menu' => false,
            'sort_order' => 1,
        ]);

        CategoryTranslation::query()->create([
            'category_id' => $category->id,
            'scope' => Category::SCOPE_CALL,
            'locale' => 'hr',
            'name' => 'Otvoreni pozivi',
            'slug' => 'otvoreni-pozivi',
            'description' => 'Otvoreni pozivi',
        ]);

        $post = CallPost::query()->create([
            'code' => 'inovacijski-vauceri',
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);

        CallPostTranslation::query()->create([
            'post_id' => $post->id,
            'locale' => 'hr',
            'title' => 'Inovacijski vaučeri',
            'slug' => 'inovacijski-vauceri',
            'excerpt' => 'Sažetak poziva.',
            'body_html' => '<p>Uvezeni sadržaj poziva.</p>',
        ]);

        $post->categories()->sync([
            $category->id => [
                'sort_order' => 0,
                'is_primary' => true,
            ],
        ]);

        $response = $this->get('/eu-fondovi/pozivi/inovacijski-vauceri');

        $response->assertOk()
            ->assertSeeText('Inovacijski vaučeri')
            ->assertSeeText('Otvoreni pozivi')
            ->assertSee('Uvezeni sadržaj poziva.', false);
    }

    public function test_call_post_detail_page_strips_duplicate_lead_image_from_body(): void
    {
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        Storage::fake('public');

        $category = Category::query()->create([
            'scope' => Category::SCOPE_CALL,
            'code' => 'otvoreni-pozivi',
            'is_active' => true,
            'show_in_menu' => false,
            'sort_order' => 1,
        ]);

        CategoryTranslation::query()->create([
            'category_id' => $category->id,
            'scope' => Category::SCOPE_CALL,
            'locale' => 'hr',
            'name' => 'Otvoreni pozivi',
            'slug' => 'otvoreni-pozivi',
            'description' => 'Otvoreni pozivi',
        ]);

        $post = CallPost::query()->create([
            'code' => 'potpora-msp-ovima-za-internacionalizaciju',
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);

        $post->addMedia(public_path('front-theme/images/bck-logo.png'))
            ->preservingOriginal()
            ->usingFileName('hero-1024x576.png')
            ->usingName('Hero')
            ->toMediaCollection('call_cover');

        $coverUrl = $post->getFirstMediaUrl('call_cover');

        CallPostTranslation::query()->create([
            'post_id' => $post->id,
            'locale' => 'hr',
            'title' => 'Potpora MSP-ovima za internacionalizaciju',
            'slug' => 'potpora-msp-ovima-za-internacionalizaciju',
            'excerpt' => 'Sažetak poziva.',
            'body_html' => '<p>Uvodni tekst.</p><figure><img src="'.$coverUrl.'" alt=""></figure><p>Nastavak sadržaja.</p>',
        ]);

        $post->categories()->sync([
            $category->id => [
                'sort_order' => 0,
                'is_primary' => true,
            ],
        ]);

        $response = $this->get('/eu-fondovi/pozivi/potpora-msp-ovima-za-internacionalizaciju');

        $response->assertOk()
            ->assertSeeText('Potpora MSP-ovima za internacionalizaciju')
            ->assertSee('Uvodni tekst.', false)
            ->assertSee('Nastavak sadržaja.', false)
            ->assertSee('ac-blog-article-cover', false);

        $this->assertStringNotContainsString(
            '<figure><img src="'.$coverUrl.'" alt=""></figure>',
            $response->getContent()
        );
    }
}
