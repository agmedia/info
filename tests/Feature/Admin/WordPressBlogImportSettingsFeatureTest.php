<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Settings\System\WordPressBlogImport;
use App\Models\Catalog\Category\Category;
use App\Models\Content\Blog\BlogPost;
use App\Services\Content\WordPressBlogImportService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class WordPressBlogImportSettingsFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_blog_import_settings_page(): void
    {
        $admin = $this->makeAdminUser();

        $this->actingAs($admin)
            ->get('/admin/settings/system/imports')
            ->assertOk()
            ->assertSee('WordPress Blog Import');
    }

    public function test_admin_can_import_wordpress_xml_from_settings_page(): void
    {
        Storage::fake('public');
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');
        config()->set('media-library.disk_name', 'public');
        config()->set('media-library.queue_conversions_by_default', false);

        $image = UploadedFile::fake()->image('wordpress-cover.jpg', 1200, 800);
        $imageBytes = file_get_contents($image->getPathname());

        Http::fake([
            'https://alphacapitalis.com/wp-content/uploads/*' => Http::response($imageBytes, 200, ['Content-Type' => 'image/jpeg']),
        ]);

        $admin = $this->makeAdminUser();
        $category = $this->makeBlogCategory($admin->id);
        $xml = UploadedFile::fake()->createWithContent(
            'wordpress-export.xml',
            (string) file_get_contents(base_path('public/assets/xml-import/alphacapitalis.WordPress.2026-03-20.xml'))
        );

        Livewire::actingAs($admin)
            ->test(WordPressBlogImport::class)
            ->set('xmlUpload', $xml)
            ->set('selectedCategoryId', (string) $category->id)
            ->set('categoryMode', 'single')
            ->set('limit', '1')
            ->set('offset', '0')
            ->call('import')
            ->assertHasNoErrors()
            ->assertSee('Imported posts')
            ->assertSee('Društvo ALPHA CAPITALIS uvršteno na popis savjetnika kod EBRD-a')
            ->assertSet('storedXmlName', 'wordpress-export.xml');

        Livewire::actingAs($admin)
            ->test(WordPressBlogImport::class)
            ->assertSet('storedXmlName', 'wordpress-export.xml')
            ->assertSee('wordpress-export.xml')
            ->call('reimport')
            ->assertHasNoErrors()
            ->assertSee('UPDATED');

        $post = BlogPost::query()->where('code', 'wordpress-post-18769')->first();

        $this->assertNotNull($post);
        $this->assertTrue($post->categories->contains(fn (Category $row): bool => (int) $row->id === (int) $category->id));
        $cover = $post->getFirstMedia('blog_cover');
        $this->assertNotNull($cover);
        $this->assertSame([], $cover?->generated_conversions ?? []);
        $this->assertSame('drustvo-alpha-capitalis-uvrsteno-na-popis-savjetnika-kod-ebrd-a', $post->translation('hr')->first()?->slug);
    }

    public function test_wordpress_import_normalizes_elementor_youtube_embed_blocks(): void
    {
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        $admin = $this->makeAdminUser();
        $xmlPath = tempnam(sys_get_temp_dir(), 'wp-video-import-');
        $this->assertNotFalse($xmlPath);

        file_put_contents($xmlPath, <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0"
    xmlns:excerpt="http://wordpress.org/export/1.2/excerpt/"
    xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:wfw="http://wellformedweb.org/CommentAPI/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:wp="http://wordpress.org/export/1.2/">
    <channel>
        <title>WordPress Export</title>
        <item>
            <title>Prodaja društva u 6 koraka</title>
            <link>https://alphacapitalis.com/2024/07/02/prodaja-drustva-u-6-koraka/</link>
            <pubDate>Tue, 02 Jul 2024 10:00:00 +0000</pubDate>
            <dc:creator><![CDATA[admin]]></dc:creator>
            <guid isPermaLink="false">https://alphacapitalis.com/?p=99999</guid>
            <description></description>
            <content:encoded><![CDATA[
<div class="elementor-widget-wrap elementor-element-populated">
    <div class="elementor-element elementor-element-e4960ab elementor-widget elementor-widget-video" data-id="e4960ab" data-element_type="widget" data-e-type="widget" data-settings="{&quot;youtube_url&quot;:&quot;https:\/\/youtu.be\/ZPZUcmahc04?si=JQ37K8bLcIGAJofK&quot;,&quot;video_type&quot;:&quot;youtube&quot;,&quot;controls&quot;:&quot;yes&quot;}" data-widget_type="video.default">
        <div class="elementor-widget-container">
            <div class="elementor-wrapper elementor-open-inline">
                <iframe class="elementor-video" frameborder="0" allowfullscreen="" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" title="Prodaja društva u 6 koraka" width="640" height="360" src="https://www.youtube.com/embed/ZPZUcmahc04?controls=1&amp;rel=0&amp;playsinline=0&amp;cc_load_policy=0&amp;autoplay=0&amp;enablejsapi=1&amp;origin=https%3A%2F%2Falphacapitalis.com&amp;widgetid=1&amp;forigin=https%3A%2F%2Falphacapitalis.com%2F2024%2F07%2F02%2Fprodaja-drustva-u-6-koraka%2F&amp;aoriginsup=1&amp;gporigin=https%3A%2F%2Falphacapitalis.com%2Fblog%2Fkategorija-financije%2F&amp;vf=1" id="widget2"></iframe>
            </div>
        </div>
    </div>
</div>
<p>Tekst nakon videa.</p>
            ]]></content:encoded>
            <excerpt:encoded><![CDATA[]]></excerpt:encoded>
            <wp:post_id>99999</wp:post_id>
            <wp:post_date><![CDATA[2024-07-02 12:00:00]]></wp:post_date>
            <wp:post_date_gmt><![CDATA[2024-07-02 10:00:00]]></wp:post_date_gmt>
            <wp:comment_status><![CDATA[closed]]></wp:comment_status>
            <wp:ping_status><![CDATA[closed]]></wp:ping_status>
            <wp:post_name><![CDATA[prodaja-drustva-u-6-koraka]]></wp:post_name>
            <wp:status><![CDATA[publish]]></wp:status>
            <wp:post_parent>0</wp:post_parent>
            <wp:menu_order>0</wp:menu_order>
            <wp:post_type><![CDATA[post]]></wp:post_type>
            <wp:post_password><![CDATA[]]></wp:post_password>
            <wp:is_sticky>0</wp:is_sticky>
            <category domain="category" nicename="financije"><![CDATA[Financije]]></category>
        </item>
    </channel>
</rss>
XML
        );

        try {
            app(WordPressBlogImportService::class)->import($xmlPath, [
                'locale' => 'hr',
                'category_mode' => 'single',
                'category_name' => 'Novosti',
                'category_slug' => 'novosti',
                'user_id' => $admin->id,
            ]);
        } finally {
            @unlink($xmlPath);
        }

        $post = BlogPost::query()->where('code', 'wordpress-post-99999')->first();

        $this->assertNotNull($post);
        $bodyHtml = (string) $post?->translation('hr')->first()?->body_html;
        $this->assertStringContainsString('https://www.youtube.com/embed/ZPZUcmahc04', $bodyHtml);
        $this->assertStringContainsString('title="Prodaja društva u 6 koraka"', $bodyHtml);
        $this->assertStringContainsString('<iframe', $bodyHtml);
        $this->assertStringContainsString('<p>Tekst nakon videa.</p>', $bodyHtml);
        $this->assertStringNotContainsString('elementor-widget-video', $bodyHtml);
        $this->assertStringNotContainsString('widgetid=1', $bodyHtml);
    }

    public function test_wordpress_import_normalizes_standalone_youtube_url_between_paragraphs(): void
    {
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        $admin = $this->makeAdminUser();
        $xmlPath = tempnam(sys_get_temp_dir(), 'wp-video-text-import-');
        $this->assertNotFalse($xmlPath);

        file_put_contents($xmlPath, <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0"
    xmlns:excerpt="http://wordpress.org/export/1.2/excerpt/"
    xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:wfw="http://wellformedweb.org/CommentAPI/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:wp="http://wordpress.org/export/1.2/">
    <channel>
        <title>WordPress Export</title>
        <item>
            <title>Prodaja društva u 6 koraka</title>
            <link>https://alphacapitalis.com/2024/07/02/prodaja-drustva-u-6-koraka/</link>
            <pubDate>Tue, 02 Jul 2024 10:00:00 +0000</pubDate>
            <dc:creator><![CDATA[admin]]></dc:creator>
            <guid isPermaLink="false">https://alphacapitalis.com/?p=99998</guid>
            <description></description>
            <content:encoded><![CDATA[
<p>Prvi odlomak prije videa.</p>https://youtu.be/ZPZUcmahc04?si=JQ37K8bLcIGAJofK<p><em>Autor: primjer.</em></p>
            ]]></content:encoded>
            <excerpt:encoded><![CDATA[]]></excerpt:encoded>
            <wp:post_id>99998</wp:post_id>
            <wp:post_date><![CDATA[2024-07-02 12:00:00]]></wp:post_date>
            <wp:post_date_gmt><![CDATA[2024-07-02 10:00:00]]></wp:post_date_gmt>
            <wp:comment_status><![CDATA[closed]]></wp:comment_status>
            <wp:ping_status><![CDATA[closed]]></wp:ping_status>
            <wp:post_name><![CDATA[prodaja-drustva-u-6-koraka-tekst]]></wp:post_name>
            <wp:status><![CDATA[publish]]></wp:status>
            <wp:post_parent>0</wp:post_parent>
            <wp:menu_order>0</wp:menu_order>
            <wp:post_type><![CDATA[post]]></wp:post_type>
            <wp:post_password><![CDATA[]]></wp:post_password>
            <wp:is_sticky>0</wp:is_sticky>
            <category domain="category" nicename="financije"><![CDATA[Financije]]></category>
        </item>
    </channel>
</rss>
XML
        );

        try {
            app(WordPressBlogImportService::class)->import($xmlPath, [
                'locale' => 'hr',
                'category_mode' => 'single',
                'category_name' => 'Novosti',
                'category_slug' => 'novosti',
                'user_id' => $admin->id,
            ]);
        } finally {
            @unlink($xmlPath);
        }

        $post = BlogPost::query()->where('code', 'wordpress-post-99998')->first();

        $this->assertNotNull($post);
        $bodyHtml = (string) $post?->translation('hr')->first()?->body_html;
        $this->assertStringContainsString('<p>Prvi odlomak prije videa.</p>', $bodyHtml);
        $this->assertStringContainsString('https://www.youtube.com/embed/ZPZUcmahc04', $bodyHtml);
        $this->assertStringContainsString('<iframe', $bodyHtml);
        $this->assertStringContainsString('<p><em>Autor: primjer.</em></p>', $bodyHtml);
        $this->assertStringNotContainsString('https://youtu.be/ZPZUcmahc04?si=JQ37K8bLcIGAJofK', $bodyHtml);
    }

    private function makeAdminUser(): User
    {
        $user = User::factory()->create();

        Bouncer::role()->firstOrCreate(['name' => 'admin']);
        Bouncer::assign('admin')->to($user);

        return $user;
    }

    private function makeBlogCategory(int $userId): Category
    {
        $category = new Category([
            'scope' => Category::SCOPE_BLOG,
            'code' => 'novosti',
            'is_active' => true,
            'show_in_menu' => true,
            'sort_order' => 0,
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);

        $category->saveAsRoot();

        $category->translations()->create([
            'scope' => Category::SCOPE_BLOG,
            'locale' => 'hr',
            'name' => 'Novosti',
            'slug' => 'novosti',
        ]);

        return $category;
    }
}
