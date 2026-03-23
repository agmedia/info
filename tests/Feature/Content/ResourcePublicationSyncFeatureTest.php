<?php

namespace Tests\Feature\Content;

use App\Models\Content\Resource\ResourceDocument;
use App\Models\Content\Resource\ResourceDocumentTranslation;
use App\Services\Content\ResourcePublicationSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ResourcePublicationSyncFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_publication_sync_sets_dates_for_published_documents_and_disables_missing_ones(): void
    {
        $published = ResourceDocument::query()->create([
            'code' => 'poslovni-plan',
            'group_code' => 'downloads',
            'is_active' => false,
        ]);

        ResourceDocumentTranslation::query()->create([
            'document_id' => $published->id,
            'locale' => 'hr',
            'title' => 'Poslovni plan',
            'slug' => 'poslovni-plan',
        ]);

        $missing = ResourceDocument::query()->create([
            'code' => 'legacy-resource',
            'group_code' => 'downloads',
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);

        ResourceDocumentTranslation::query()->create([
            'document_id' => $missing->id,
            'locale' => 'hr',
            'title' => 'Legacy resource',
            'slug' => 'legacy-resource',
        ]);

        Http::fake([
            'https://alphacapitalis.com/wp-json/wp/v2/resources*' => Http::response([
                [
                    'id' => 24295,
                    'slug' => 'poslovni-plan',
                    'status' => 'publish',
                    'date' => '2017-03-03T14:16:04',
                    'link' => 'https://alphacapitalis.com/resources/poslovni-plan/',
                ],
            ], 200),
        ]);

        $result = app(ResourcePublicationSyncService::class)->sync();

        $published->refresh();
        $missing->refresh();

        $this->assertSame(2, $result['synced_count']);
        $this->assertSame(1, $result['activated_count']);
        $this->assertSame(1, $result['deactivated_count']);

        $this->assertTrue((bool) $published->is_active);
        $this->assertSame('2017-03-03 14:16:04', $published->published_at?->format('Y-m-d H:i:s'));
        $this->assertSame('https://alphacapitalis.com/resources/poslovni-plan/', $published->source_url);
        $this->assertSame('publish', $published->payload['remote_publication_status'] ?? null);

        $this->assertFalse((bool) $missing->is_active);
        $this->assertNull($missing->published_at);
        $this->assertSame('missing', $missing->payload['remote_publication_status'] ?? null);
    }
}
