<?php

namespace Tests\Feature\Content;

use App\Models\Content\Resource\ResourceDocument;
use App\Services\Content\ResourceAssetImportService;
use Database\Seeders\ResourceDocumentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResourceAssetImportFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_resource_asset_import_localizes_downloads_and_covers_from_wordpress_xml(): void
    {
        Storage::fake('public');

        $document = ResourceDocument::query()->create([
            'code' => 'analiza-sektora-proizvodnja-tekstila',
            'group_code' => 'sector-analysis',
            'is_active' => true,
            'download_url' => 'https://alphacapitalis.com/wp-content/uploads/2024/01/Analiza-sektora_C13_2024.pdf',
            'payload' => [
                'wp_id' => 32728,
            ],
        ]);

        Http::fake([
            'https://alphacapitalis.com/wp-content/uploads/2024/01/Analiza-sektora_C13_2024.pdf' => Http::response('pdf-content', 200),
            'https://alphacapitalis.com/wp-content/uploads/2024/01/Thumbnail-za-web-analizu-transakcija.png' => Http::response('png-content', 200),
        ]);

        $filePath = $this->makeWordPressXmlExport([
            [
                'id' => 32729,
                'parent_id' => 32728,
                'title' => 'Thumbnail za web analizu transakcija',
                'url' => 'https://alphacapitalis.com/wp-content/uploads/2024/01/Thumbnail-za-web-analizu-transakcija.png',
            ],
            [
                'id' => 32730,
                'parent_id' => 32728,
                'title' => 'Analiza sektora_C13_2024',
                'url' => 'https://alphacapitalis.com/wp-content/uploads/2024/01/Analiza-sektora_C13_2024.pdf',
            ],
        ]);

        $result = app(ResourceAssetImportService::class)->import($filePath);

        $document->refresh();

        $this->assertSame(1, $result['processed_count']);
        $this->assertSame(1, $result['localized_download_count']);
        $this->assertSame(1, $result['localized_cover_count']);
        $this->assertSame(
            Storage::disk('public')->url('resource-documents/downloads/analiza-sektora-proizvodnja-tekstila.pdf'),
            $document->download_url
        );
        $this->assertSame(
            Storage::disk('public')->url('resource-documents/covers/analiza-sektora-proizvodnja-tekstila.png'),
            $document->cover_image_url
        );
        $this->assertTrue(Storage::disk('public')->exists('resource-documents/downloads/analiza-sektora-proizvodnja-tekstila.pdf'));
        $this->assertTrue(Storage::disk('public')->exists('resource-documents/covers/analiza-sektora-proizvodnja-tekstila.png'));
        $this->assertSame(
            'https://alphacapitalis.com/wp-content/uploads/2024/01/Analiza-sektora_C13_2024.pdf',
            $document->payload['remote_download_url'] ?? null
        );
        $this->assertSame(
            'https://alphacapitalis.com/wp-content/uploads/2024/01/Thumbnail-za-web-analizu-transakcija.png',
            $document->payload['remote_cover_image_url'] ?? null
        );
    }

    public function test_resource_document_seeder_keeps_existing_local_asset_urls(): void
    {
        Storage::fake('public');

        $downloadPath = 'resource-documents/downloads/poslovni-plan.pdf';
        $coverPath = 'resource-documents/covers/poslovni-plan.png';

        Storage::disk('public')->put($downloadPath, 'seeded-local-pdf');
        Storage::disk('public')->put($coverPath, 'seeded-local-cover');

        ResourceDocument::query()->create([
            'code' => 'poslovni-plan',
            'group_code' => 'downloads',
            'is_active' => true,
            'download_url' => Storage::disk('public')->url($downloadPath),
            'cover_image_url' => Storage::disk('public')->url($coverPath),
            'payload' => [
                'remote_download_url' => 'https://example.test/legacy.pdf',
            ],
        ]);

        $this->seed(ResourceDocumentSeeder::class);

        $document = ResourceDocument::query()->where('code', 'poslovni-plan')->first();

        $this->assertNotNull($document);
        $this->assertSame(Storage::disk('public')->url($downloadPath), $document->download_url);
        $this->assertSame(Storage::disk('public')->url($coverPath), $document->cover_image_url);
        $this->assertSame(
            'https://alphacapitalis.com/wp-content/uploads/2020/12/Poslovni_plan_template_Alpha_Capitalis.pdf',
            $document->payload['remote_download_url'] ?? null
        );
    }

    public function test_resource_asset_import_falls_back_to_source_page_featured_image(): void
    {
        Storage::fake('public');

        $document = ResourceDocument::query()->create([
            'code' => 'analiza-transakcija-poljoprivreda-2',
            'group_code' => 'transaction-analysis',
            'is_active' => true,
            'download_url' => 'https://alphacapitalis.com/wp-content/uploads/2021/10/Analiza-transakcija_Poljopriovreda_2021_10_15_reduce.pdf',
            'source_url' => 'https://alphacapitalis.com/resources/analiza-transakcija-poljoprivreda-2/',
            'payload' => [
                'wp_id' => 27415,
            ],
        ]);

        Http::fake([
            'https://alphacapitalis.com/wp-content/uploads/2021/10/Analiza-transakcija_Poljopriovreda_2021_10_15_reduce.pdf' => Http::response('pdf-content', 200),
            'https://alphacapitalis.com/resources/analiza-transakcija-poljoprivreda-2/' => Http::response(
                '<script>var elementorFrontendConfig = {"post":{"featuredImage":"https:\/\/alphacapitalis.com\/wp-content\/uploads\/2021\/10\/analiza_transakcija_web.jpg"}};</script>',
                200
            ),
            'https://alphacapitalis.com/wp-content/uploads/2021/10/analiza_transakcija_web.jpg' => Http::response('jpg-content', 200),
        ]);

        $filePath = $this->makeWordPressXmlExport([
            [
                'id' => 27416,
                'parent_id' => 27415,
                'title' => 'Analiza transakcija Poljoprivreda',
                'url' => 'https://alphacapitalis.com/wp-content/uploads/2021/10/Analiza-transakcija_Poljopriovreda_2021_10_15_reduce.pdf',
            ],
        ]);

        app(ResourceAssetImportService::class)->import($filePath);

        $document->refresh();

        $this->assertSame(
            Storage::disk('public')->url('resource-documents/covers/analiza-transakcija-poljoprivreda-2.jpg'),
            $document->cover_image_url
        );
        $this->assertTrue(Storage::disk('public')->exists('resource-documents/covers/analiza-transakcija-poljoprivreda-2.jpg'));
        $this->assertSame(
            'https://alphacapitalis.com/wp-content/uploads/2021/10/analiza_transakcija_web.jpg',
            $document->payload['remote_cover_image_url'] ?? null
        );
    }

    /**
     * @param  array<int, array{id:int,parent_id:int,title:string,url:string}>  $attachments
     */
    private function makeWordPressXmlExport(array $attachments): string
    {
        $items = '';

        foreach ($attachments as $attachment) {
            $items .= sprintf(
                '<item><title><![CDATA[%s]]></title><wp:post_id>%d</wp:post_id><wp:post_parent>%d</wp:post_parent><wp:post_type>attachment</wp:post_type><wp:attachment_url><![CDATA[%s]]></wp:attachment_url></item>',
                htmlspecialchars($attachment['title'], ENT_XML1 | ENT_QUOTES, 'UTF-8'),
                $attachment['id'],
                $attachment['parent_id'],
                htmlspecialchars($attachment['url'], ENT_XML1 | ENT_QUOTES, 'UTF-8')
            );
        }

        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0"
    xmlns:excerpt="http://wordpress.org/export/1.2/excerpt/"
    xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:wfw="http://wellformedweb.org/CommentAPI/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:wp="http://wordpress.org/export/1.2/">
    <channel>
        {$items}
    </channel>
</rss>
XML;

        $filePath = tempnam(sys_get_temp_dir(), 'resource-wxr-');
        file_put_contents($filePath, $xml);

        return $filePath;
    }
}
