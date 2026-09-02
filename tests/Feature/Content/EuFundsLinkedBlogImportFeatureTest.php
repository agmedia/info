<?php

namespace Tests\Feature\Content;

use App\Models\Catalog\Category\Category;
use App\Models\Content\Blog\BlogPost;
use App\Support\Content\EuFundsLinkedBlogPostRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EuFundsLinkedBlogImportFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_imports_only_exact_linked_posts_and_preserves_them_on_rerun(): void
    {
        Storage::fake('public');
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');
        config()->set('media-library.disk_name', 'public');
        config()->set('media-library.queue_conversions_by_default', false);

        $linkedSlugs = EuFundsLinkedBlogPostRegistry::slugs('hr');
        $this->assertCount(19, $linkedSlugs);
        $this->assertContains('mali-zajmovi-za-industrijsku-tranziciju', $linkedSlugs);
        $this->assertNotContains('poziv-u-najavi-dokazivanje-inovativnog-koncepta-drugi-poziv', $linkedSlugs);

        $xmlPath = $this->makeWordPressXmlExport([
            ...$linkedSlugs,
            'objava-koja-nije-povezana-s-eu-fondovima',
        ]);

        try {
            $this->artisan('content:import-wordpress-blog', [
                'file' => $xmlPath,
                '--locale' => 'hr',
                '--eu-funds-linked-posts' => true,
            ])
                ->expectsOutputToContain('Imported 19 WordPress post(s)')
                ->assertExitCode(0);

            $this->assertSame(19, BlogPost::query()->count());
            $this->assertFalse(BlogPost::query()
                ->whereHas('translations', fn ($query) => $query
                    ->where('locale', 'hr')
                    ->where('slug', 'objava-koja-nije-povezana-s-eu-fondovima'))
                ->exists());

            $category = Category::query()
                ->where('scope', Category::SCOPE_BLOG)
                ->where('code', 'eu-fondovi')
                ->firstOrFail();
            $this->assertSame(19, $category->blogPosts()->count());

            $editedTranslation = BlogPost::query()
                ->whereHas('translations', fn ($query) => $query
                    ->where('locale', 'hr')
                    ->where('slug', $linkedSlugs[0]))
                ->firstOrFail()
                ->translation('hr')
                ->firstOrFail();
            $editedTranslation->update(['title' => 'Ručno uređeni naslov koji se mora sačuvati']);

            $this->artisan('content:import-wordpress-blog', [
                'file' => $xmlPath,
                '--locale' => 'hr',
                '--eu-funds-linked-posts' => true,
            ])
                ->expectsOutputToContain('Imported 0 WordPress post(s)')
                ->assertExitCode(0);
        } finally {
            @unlink($xmlPath);
        }

        $this->assertSame(19, BlogPost::query()->count());
        $this->assertSame('Ručno uređeni naslov koji se mora sačuvati', $editedTranslation->fresh()->title);
    }

    public function test_profile_aborts_before_writes_when_a_required_exact_slug_is_missing(): void
    {
        $linkedSlugs = EuFundsLinkedBlogPostRegistry::slugs('hr');
        $missingSlug = array_pop($linkedSlugs);
        $this->assertNotNull($missingSlug);
        $xmlPath = $this->makeWordPressXmlExport($linkedSlugs);

        try {
            $this->artisan('content:import-wordpress-blog', [
                'file' => $xmlPath,
                '--locale' => 'hr',
                '--eu-funds-linked-posts' => true,
            ])
                ->expectsOutputToContain('missing required exact slug(s): '.$missingSlug)
                ->assertExitCode(1);
        } finally {
            @unlink($xmlPath);
        }

        $this->assertDatabaseCount('content_blog_posts', 0);
        $this->assertDatabaseCount('categories', 0);
    }

    /**
     * @param  array<int, string>  $slugs
     */
    private function makeWordPressXmlExport(array $slugs): string
    {
        $xmlPath = tempnam(sys_get_temp_dir(), 'eu-funds-linked-blog-import-');
        $this->assertNotFalse($xmlPath);
        $items = '';

        foreach (array_values($slugs) as $index => $slug) {
            $postId = 50000 + $index;
            $title = 'Povezana EU fondovi objava '.($index + 1);
            $items .= sprintf(<<<'XML'
<item>
    <title><![CDATA[%s]]></title>
    <link>https://www.alphacapitalis.com/2026/08/27/%s/</link>
    <pubDate>Thu, 27 Aug 2026 10:00:00 +0000</pubDate>
    <dc:creator><![CDATA[admin]]></dc:creator>
    <guid isPermaLink="false">https://www.alphacapitalis.com/?p=%d</guid>
    <description></description>
    <content:encoded><![CDATA[<p>Sadržaj povezane EU fondovi objave.</p>]]></content:encoded>
    <excerpt:encoded><![CDATA[Sažetak povezane objave.]]></excerpt:encoded>
    <wp:post_id>%d</wp:post_id>
    <wp:post_date><![CDATA[2026-08-27 12:00:00]]></wp:post_date>
    <wp:post_date_gmt><![CDATA[2026-08-27 10:00:00]]></wp:post_date_gmt>
    <wp:post_name><![CDATA[%s]]></wp:post_name>
    <wp:status><![CDATA[publish]]></wp:status>
    <wp:post_parent>0</wp:post_parent>
    <wp:menu_order>0</wp:menu_order>
    <wp:post_type><![CDATA[post]]></wp:post_type>
    <category domain="category" nicename="uncategorized"><![CDATA[Uncategorized]]></category>
</item>
XML,
                $title,
                $slug,
                $postId,
                $postId,
                $slug,
            );
        }

        file_put_contents($xmlPath, sprintf(<<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0"
    xmlns:excerpt="http://wordpress.org/export/1.2/excerpt/"
    xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:wp="http://wordpress.org/export/1.2/">
    <channel>
        <title>WordPress Export</title>
        %s
    </channel>
</rss>
XML,
            $items,
        ));

        return $xmlPath;
    }
}
