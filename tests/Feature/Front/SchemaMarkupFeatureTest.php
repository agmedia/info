<?php

namespace Tests\Feature\Front;

use App\Models\Content\Blog\BlogPost;
use App\Models\Settings\Local\Language;
use App\Services\Settings\SystemSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchemaMarkupFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_blog_author_is_emitted_as_the_canonical_organization(): void
    {
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

        app(SystemSettingsService::class)->putMany([
            'store_schema_business_name' => 'ALPHA CAPITALIS D.O.O.',
            'store_schema_blog_author_name' => 'ALPHA CAPITALIS D.O.O.',
        ]);

        $post = BlogPost::query()->create([
            'code' => 'schema-author-test',
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);
        $post->translations()->create([
            'locale' => 'hr',
            'title' => 'Schema autor test',
            'slug' => 'schema-autor-test',
            'body_html' => '<p>Sadržaj članka.</p>',
        ]);

        $response = $this->get(route('blog.show', ['slug' => 'schema-autor-test']))
            ->assertOk();

        preg_match_all(
            '#<script type="application/ld\+json">(.*?)</script>#s',
            $response->getContent(),
            $schemaMatches,
        );
        $blogPosting = collect($schemaMatches[1] ?? [])
            ->map(static fn (string $json): mixed => json_decode($json, true))
            ->firstWhere('@type', 'BlogPosting');

        $this->assertIsArray($blogPosting);
        $this->assertSame('Organization', data_get($blogPosting, 'author.@type'));
        $this->assertSame(url('/').'#organization', data_get($blogPosting, 'author.@id'));
        $this->assertSame(url('/').'#organization', data_get($blogPosting, 'publisher.@id'));
    }
}
