<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Settings\System\WordPressCallImport;
use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Call\CallPost;
use App\Models\User;
use App\Support\Content\EuFundsLinkedBlogPostRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class WordPressCallImportSettingsFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_livewire_temporary_upload_limit_accepts_the_supported_call_xml_size(): void
    {
        $this->assertContains('max:51200', config('livewire.temporary_file_upload.rules', []));
    }

    public function test_default_flow_imports_linked_posts_and_rerun_preserves_manual_blog_edits(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');
        config()->set('media-library.disk_name', 'public');
        config()->set('media-library.queue_conversions_by_default', false);

        $admin = $this->makeAdminUser();
        $linkedSlugs = EuFundsLinkedBlogPostRegistry::slugs('hr');
        $xml = UploadedFile::fake()->createWithContent(
            'fresh-eu-funds.xml',
            $this->makeWordPressXmlExport([
                ...$linkedSlugs,
                'poziv-u-najavi-dokazivanje-inovativnog-koncepta-drugi-poziv',
            ])
        );

        $component = Livewire::actingAs($admin)
            ->test(WordPressCallImport::class)
            ->assertSet('importLinkedBlogPosts', true)
            ->assertSet('linkedBlogPostTargetCount', 19)
            ->assertSee('Also import the 19 linked EU-funds blog posts')
            ->set('xmlUpload', $xml)
            ->set('limit', '1')
            ->call('import')
            ->assertHasNoErrors()
            ->assertSet('errorMessage', null)
            ->assertSet('result.processed_count', 1)
            ->assertSet('result.linked_blog_posts.enabled', true)
            ->assertSet('result.linked_blog_posts.target_count', 19)
            ->assertSet('result.linked_blog_posts.imported_count', 19)
            ->assertSet('result.linked_blog_posts.skipped_existing_count', 0)
            ->assertSee('Linked blog posts: 19 new, 0 already present, 19 approved targets.')
            ->assertDispatched(
                'notify',
                type: 'success',
                message: 'Imported 1 EU funds call item(s). Linked blog posts: 19 new, 0 already present.'
            );

        $this->assertSame(19, BlogPost::query()->count());
        $this->assertSame(1, CallPost::query()->count());

        $editedTranslation = BlogPost::query()
            ->whereHas('translations', fn ($query) => $query
                ->where('locale', 'hr')
                ->where('slug', $linkedSlugs[0]))
            ->firstOrFail()
            ->translation('hr')
            ->firstOrFail();
        $editedTranslation->update(['title' => 'Ručno uređeni naslov povezanog bloga']);

        $component
            ->call('reimport')
            ->assertHasNoErrors()
            ->assertSet('errorMessage', null)
            ->assertSet('result.processed_count', 1)
            ->assertSet('result.linked_blog_posts.imported_count', 0)
            ->assertSet('result.linked_blog_posts.skipped_existing_count', 19)
            ->assertSee('Linked blog posts: 0 new, 19 already present, 19 approved targets.')
            ->assertDispatched(
                'notify',
                type: 'success',
                message: 'Imported 1 EU funds call item(s). Linked blog posts: 0 new, 19 already present.'
            );

        $this->assertSame(19, BlogPost::query()->count());
        $this->assertSame(1, CallPost::query()->count());
        $this->assertSame('Ručno uređeni naslov povezanog bloga', $editedTranslation->fresh()->title);
    }

    public function test_linked_blog_import_can_be_explicitly_disabled(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        $admin = $this->makeAdminUser();
        $xml = UploadedFile::fake()->createWithContent(
            'calls-only.xml',
            $this->makeWordPressXmlExport([
                'poziv-u-najavi-dokazivanje-inovativnog-koncepta-drugi-poziv',
            ])
        );

        Livewire::actingAs($admin)
            ->test(WordPressCallImport::class)
            ->set('xmlUpload', $xml)
            ->set('limit', '1')
            ->set('importLinkedBlogPosts', false)
            ->call('import')
            ->assertHasNoErrors()
            ->assertSet('errorMessage', null)
            ->assertSet('result.processed_count', 1)
            ->assertSet('result.linked_blog_posts.enabled', false)
            ->assertSet('result.linked_blog_posts.imported_count', 0)
            ->assertSee('Linked blog posts were skipped for this run.')
            ->assertDispatched(
                'notify',
                type: 'success',
                message: 'Imported 1 EU funds call item(s). Linked blog posts were skipped.'
            );

        $this->assertDatabaseCount('content_blog_posts', 0);
        $this->assertDatabaseCount('content_call_posts', 1);
    }

    public function test_default_flow_stops_before_importing_calls_when_linked_post_preflight_fails(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        $admin = $this->makeAdminUser();
        $linkedSlugs = EuFundsLinkedBlogPostRegistry::slugs('hr');
        $missingSlug = array_pop($linkedSlugs);
        $this->assertNotNull($missingSlug);
        $xml = UploadedFile::fake()->createWithContent(
            'incomplete-eu-funds.xml',
            $this->makeWordPressXmlExport([
                ...$linkedSlugs,
                'poziv-u-najavi-dokazivanje-inovativnog-koncepta-drugi-poziv',
            ])
        );
        $expectedError = 'The WordPress XML export is missing required exact slug(s): '.$missingSlug;

        Livewire::actingAs($admin)
            ->test(WordPressCallImport::class)
            ->set('xmlUpload', $xml)
            ->set('limit', '1')
            ->call('import')
            ->assertHasNoErrors()
            ->assertSet('result', null)
            ->assertSet('errorMessage', $expectedError)
            ->assertSee($expectedError)
            ->assertDispatched('notify', type: 'danger', message: $expectedError);

        $this->assertDatabaseCount('content_blog_posts', 0);
        $this->assertDatabaseCount('content_call_posts', 0);
    }

    private function makeAdminUser(): User
    {
        $user = User::factory()->create();

        Bouncer::role()->firstOrCreate(['name' => 'superadmin']);
        Bouncer::assign('superadmin')->to($user);

        return $user;
    }

    /**
     * @param  array<int, string>  $slugs
     */
    private function makeWordPressXmlExport(array $slugs): string
    {
        $items = '';

        foreach (array_values($slugs) as $index => $slug) {
            $postId = 51000 + $index;
            $items .= sprintf(<<<'XML'
<item>
    <title><![CDATA[WordPress objava %d]]></title>
    <link>https://www.alphacapitalis.com/2026/08/27/%s/</link>
    <pubDate>Thu, 27 Aug 2026 10:00:00 +0000</pubDate>
    <dc:creator><![CDATA[admin]]></dc:creator>
    <guid isPermaLink="false">https://www.alphacapitalis.com/?p=%d</guid>
    <description></description>
    <content:encoded><![CDATA[<p>Sadržaj iz svježeg XML-a.</p>]]></content:encoded>
    <excerpt:encoded><![CDATA[Sažetak iz svježeg XML-a.]]></excerpt:encoded>
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
                $index + 1,
                $slug,
                $postId,
                $postId,
                $slug,
            );
        }

        return sprintf(<<<'XML'
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
        );
    }
}
