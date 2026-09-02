<?php

namespace Tests\Feature\Content;

use App\Models\Catalog\Category\Category;
use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Call\CallPost;
use App\Services\Content\EuFundsCallImportService;
use App\Support\Content\EuFundsServicePageDefaults;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EuFundsCallImportFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_curated_call_statuses_keep_the_approved_five_seven_twenty_grouping(): void
    {
        $groups = collect(data_get(EuFundsServicePageDefaults::defaultsForLocale('hr'), 'calls.groups', []))
            ->keyBy('tone');

        $this->assertSame(5, count((array) data_get($groups->get('pending'), 'items', [])));
        $this->assertSame(7, count((array) data_get($groups->get('open'), 'items', [])));
        $this->assertSame(20, count((array) data_get($groups->get('closed'), 'items', [])));

        $openSlugs = collect(data_get($groups->get('open'), 'items', []))
            ->pluck('link.slug')
            ->all();

        $this->assertContains('poziv-u-najavi-dokazivanje-inovativnog-koncepta-prvi-poziv', $openSlugs);
        $this->assertContains('inovacije-procesa-u-s3-podrucjima', $openSlugs);
        $this->assertContains('izgradnja-i-opremanje-postrojenja-za-obradu-reciklabilnog-otpada-2', $openSlugs);
        $this->assertContains('program-poticanja-poduzetnistva-u-kulturnim-i-kreativnim-industrijama-u-2026-godini', $openSlugs);
    }

    public function test_blog_blueprint_slug_uses_matching_wxr_post_and_preserves_the_curated_title(): void
    {
        $xmlPath = $this->makeWordPressXmlExport(
            slug: 'poziv-u-najavi-dokazivanje-inovativnog-koncepta-drugi-poziv',
            title: 'Legacy naslov koji se ne podudara s naslovom u popisu natječaja',
            bodyHtml: '<p>Sadržaj iz svježeg WordPress izvoza.</p><ol><li>Prva stavka<br></li></ol><br><ul><li>Druga stavka</li></ul><br>'
        );

        try {
            $this->assertDatabaseCount('content_blog_posts', 0);

            $result = app(EuFundsCallImportService::class)->import($xmlPath, [
                'locale' => 'hr',
                'limit' => 1,
                'offset' => 4,
            ]);
        } finally {
            @unlink($xmlPath);
        }

        $this->assertSame(1, $result['processed_count']);
        $this->assertSame('xml', data_get($result, 'imported.0.source'));
        $this->assertSame('Dokazivanje inovativnog koncepta – Drugi poziv', data_get($result, 'imported.0.title'));
        $this->assertSame('created', data_get($result, 'imported.0.status'));
        $this->assertDatabaseCount('content_blog_posts', 0);

        $call = CallPost::query()
            ->with('translations')
            ->firstOrFail();
        $translation = $call->translations->firstWhere('locale', 'hr');

        $this->assertNotNull($translation);
        $this->assertSame('Dokazivanje inovativnog koncepta – Drugi poziv', $translation->title);
        $this->assertSame('poziv-u-najavi-dokazivanje-inovativnog-koncepta-drugi-poziv', $translation->slug);
        $this->assertStringContainsString('Sadržaj iz svježeg WordPress izvoza.', (string) $translation->body_html);
        $this->assertStringContainsString('<ol>', (string) $translation->body_html);
        $this->assertStringContainsString('Prva stavka', (string) $translation->body_html);
        $this->assertStringContainsString('<ul><li>Druga stavka</li></ul>', (string) $translation->body_html);
        $this->assertStringNotContainsString('<br', (string) $translation->body_html);
        $this->assertSame('xml', data_get($translation->payload, 'import_source'));
        $this->assertSame(
            'poziv-u-najavi-dokazivanje-inovativnog-koncepta-drugi-poziv',
            data_get($translation->payload, 'source_slug')
        );
        $this->assertNull(data_get($translation->payload, 'needs_content'));
        $this->assertSame('xml', data_get($call->payload, 'import_source'));

        $upcomingCategory = Category::query()
            ->where('scope', Category::SCOPE_CALL)
            ->where('code', 'pozivi-u-najavi')
            ->firstOrFail();
        $this->assertSame(
            'U NAJAVI',
            data_get($upcomingCategory->translations()->where('locale', 'hr')->firstOrFail()->payload, 'status_label')
        );
    }

    public function test_fresh_xml_is_authoritative_when_a_blog_post_with_the_same_slug_already_exists(): void
    {
        $blog = BlogPost::query()->create([
            'code' => 'wordpress-post-49001',
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 0,
        ]);
        $blog->translations()->create([
            'locale' => 'hr',
            'title' => 'Stara kopija objave',
            'slug' => 'poziv-u-najavi-dokazivanje-inovativnog-koncepta-drugi-poziv',
            'body_html' => '<p>Stari sadržaj iz baze.</p>',
        ]);

        $xmlPath = $this->makeWordPressXmlExport(
            slug: 'poziv-u-najavi-dokazivanje-inovativnog-koncepta-drugi-poziv',
            title: 'Svježi WXR naslov',
            bodyHtml: '<p>Novi sadržaj iz svježeg XML-a.</p>'
        );

        try {
            $result = app(EuFundsCallImportService::class)->import($xmlPath, [
                'locale' => 'hr',
                'limit' => 1,
                'offset' => 4,
            ]);
        } finally {
            @unlink($xmlPath);
        }

        $this->assertSame('xml', data_get($result, 'imported.0.source'));

        $bodyHtml = (string) CallPost::query()
            ->firstOrFail()
            ->translations()
            ->where('locale', 'hr')
            ->value('body_html');

        $this->assertStringContainsString('Novi sadržaj iz svježeg XML-a.', $bodyHtml);
        $this->assertStringNotContainsString('Stari sadržaj iz baze.', $bodyHtml);
    }

    public function test_reimport_preserves_an_existing_public_call_slug(): void
    {
        $xmlPath = $this->makeWordPressXmlExport(
            slug: 'poziv-u-najavi-dokazivanje-inovativnog-koncepta-drugi-poziv',
            title: 'Legacy naslov',
            bodyHtml: '<p>Prva verzija sadržaja.</p>'
        );

        try {
            app(EuFundsCallImportService::class)->import($xmlPath, [
                'locale' => 'hr',
                'limit' => 1,
                'offset' => 4,
            ]);

            $call = CallPost::query()->firstOrFail();
            $call->translations()->where('locale', 'hr')->update([
                'slug' => 'postojeci-javni-slug',
            ]);

            app(EuFundsCallImportService::class)->import($xmlPath, [
                'locale' => 'hr',
                'limit' => 1,
                'offset' => 4,
            ]);
        } finally {
            @unlink($xmlPath);
        }

        $this->assertDatabaseCount('content_call_posts', 1);
        $this->assertSame(
            'postojeci-javni-slug',
            CallPost::query()->firstOrFail()->translations()->where('locale', 'hr')->value('slug')
        );
    }

    private function makeWordPressXmlExport(string $slug, string $title, string $bodyHtml = '<p>Sadržaj iz svježeg WordPress izvoza.</p>'): string
    {
        $xmlPath = tempnam(sys_get_temp_dir(), 'eu-funds-call-import-');
        $this->assertNotFalse($xmlPath);

        $xml = sprintf(<<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0"
    xmlns:excerpt="http://wordpress.org/export/1.2/excerpt/"
    xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:wp="http://wordpress.org/export/1.2/">
    <channel>
        <title>WordPress Export</title>
        <item>
            <title><![CDATA[%s]]></title>
            <link>https://www.alphacapitalis.com/2026/08/27/%s/</link>
            <pubDate>Thu, 27 Aug 2026 10:00:00 +0000</pubDate>
            <dc:creator><![CDATA[admin]]></dc:creator>
            <guid isPermaLink="false">https://www.alphacapitalis.com/?p=49001</guid>
            <description></description>
            <content:encoded><![CDATA[%s]]></content:encoded>
            <excerpt:encoded><![CDATA[Sažetak iz izvoza.]]></excerpt:encoded>
            <wp:post_id>49001</wp:post_id>
            <wp:post_date><![CDATA[2026-08-27 12:00:00]]></wp:post_date>
            <wp:post_date_gmt><![CDATA[2026-08-27 10:00:00]]></wp:post_date_gmt>
            <wp:post_name><![CDATA[%s]]></wp:post_name>
            <wp:status><![CDATA[publish]]></wp:status>
            <wp:post_parent>0</wp:post_parent>
            <wp:menu_order>0</wp:menu_order>
            <wp:post_type><![CDATA[post]]></wp:post_type>
            <category domain="category" nicename="eu-fondovi"><![CDATA[EU fondovi]]></category>
        </item>
    </channel>
</rss>
XML,
            $title,
            $slug,
            $bodyHtml,
            $slug
        );

        file_put_contents($xmlPath, $xml);

        return $xmlPath;
    }
}
