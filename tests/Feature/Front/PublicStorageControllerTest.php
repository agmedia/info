<?php

namespace Tests\Feature\Front;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicStorageControllerTest extends TestCase
{
    public function test_public_storage_asset_is_served_when_request_reaches_laravel(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('media/example.txt', 'public asset');

        $response = $this->get('/storage/media/example.txt')
            ->assertOk();

        $this->assertStringContainsString(
            'max-age=31536000',
            (string) $response->headers->get('Cache-Control')
        );
        $this->assertSame(
            'public asset',
            file_get_contents($response->baseResponse->getFile()->getPathname())
        );
    }

    public function test_public_storage_asset_rejects_traversal_paths(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('media/example.txt', 'public asset');

        $this->get('/storage/media/%2E%2E/example.txt')
            ->assertNotFound();
    }
}
