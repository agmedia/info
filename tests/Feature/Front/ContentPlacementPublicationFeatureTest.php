<?php

namespace Tests\Feature\Front;

use App\Models\Content\Blog\BlogPost;
use App\Models\Content\ContentBlock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentPlacementPublicationFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_blog_post_without_a_date_gets_an_immediate_publication_timestamp(): void
    {
        $active = BlogPost::query()->create([
            'code' => 'active-without-date',
            'is_active' => true,
            'published_at' => null,
        ]);
        $inactive = BlogPost::query()->create([
            'code' => 'inactive-without-date',
            'is_active' => false,
            'published_at' => null,
        ]);

        $this->assertNotNull($active->published_at);
        $this->assertTrue($active->published_at->betweenIncluded(now()->subMinute(), now()));
        $this->assertNull($inactive->published_at);
    }

    public function test_manual_blog_block_hides_inactive_and_scheduled_posts_until_their_publication_time(): void
    {
        app()->setLocale('hr');

        $published = $this->createBlogPost('Objavljeni članak u bloku', true, now()->subHour());
        $scheduled = $this->createBlogPost('Zakazani članak u bloku', true, now()->addDay());
        $inactive = $this->createBlogPost('Neaktivni članak u bloku', false, now()->subDay());

        $block = ContentBlock::query()->create([
            'code' => 'manual-publication-test',
            'name' => 'Manual publication test',
            'type' => 'blogs',
            'is_active' => true,
        ]);

        foreach ([$published, $scheduled, $inactive] as $sortOrder => $post) {
            $block->items()->create([
                'item_type' => 'blog',
                'item_id' => $post->id,
                'sort_order' => $sortOrder,
            ]);
        }

        $this->assertStringContainsString('Objavljeni članak u bloku', $this->renderBlock($block));
        $this->assertStringNotContainsString('Zakazani članak u bloku', $this->renderBlock($block));
        $this->assertStringNotContainsString('Neaktivni članak u bloku', $this->renderBlock($block));

        $this->travelTo($scheduled->published_at->copy()->addMinute());

        try {
            $html = $this->renderBlock($block);

            $this->assertStringContainsString('Objavljeni članak u bloku', $html);
            $this->assertStringContainsString('Zakazani članak u bloku', $html);
            $this->assertStringNotContainsString('Neaktivni članak u bloku', $html);
        } finally {
            $this->travelBack();
        }
    }

    private function createBlogPost(string $title, bool $isActive, mixed $publishedAt): BlogPost
    {
        $post = BlogPost::query()->create([
            'code' => str($title)->slug()->toString(),
            'is_active' => $isActive,
            'published_at' => $publishedAt,
        ]);

        $post->translations()->create([
            'locale' => 'hr',
            'title' => $title,
            'slug' => str($title)->slug()->toString(),
            'excerpt' => 'Sažetak '.$title,
        ]);

        return $post;
    }

    private function renderBlock(ContentBlock $block): string
    {
        return view('components.content-placement', [
            'items' => collect([[
                'block' => $block->load('items'),
                'translation' => null,
                'slot' => null,
            ]]),
        ])->render();
    }
}
