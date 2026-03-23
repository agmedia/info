<?php

namespace Database\Seeders;

use App\Models\Content\Resource\ResourceDocument;
use App\Support\Content\ResourceDocumentGroupRegistry;
use Illuminate\Database\Seeder;

class ResourceDocumentSeeder extends Seeder
{
    public function run(): void
    {
        /** @var array<int, array<string, mixed>> $rows */
        $rows = require database_path('seeders/data/resource_documents.php');

        foreach ($rows as $row) {
            $code = trim((string) ($row['code'] ?? ''));
            $remoteDownloadUrl = $this->normalizeNullableUrl($row['download_url'] ?? null);
            $remoteCoverImageUrl = $this->normalizeNullableUrl($row['cover_image_url'] ?? null);
            $existing = ResourceDocument::query()->where('code', $code)->first();

            $payload = $this->mergePayload(
                $existing?->payload,
                $row['payload'] ?? null,
                $remoteDownloadUrl,
                $remoteCoverImageUrl
            );

            $document = ResourceDocument::query()->updateOrCreate(
                ['code' => $code],
                [
                    'group_code' => (string) ($row['group_code'] ?: ResourceDocumentGroupRegistry::DOWNLOADS),
                    'is_active' => $this->preferExistingBoolean($existing?->is_active, $row['is_active'] ?? null, true),
                    'published_at' => $this->preferExistingPublishedAt($existing?->published_at?->toDateTimeString(), $row['published_at'] ?? null),
                    'sort_order' => (int) ($row['sort_order'] ?? 0),
                    'download_url' => $this->preferExistingUrl($existing?->download_url, $remoteDownloadUrl),
                    'cover_image_url' => $this->preferExistingUrl($existing?->cover_image_url, $remoteCoverImageUrl),
                    'source_url' => $row['source_url'] ?? null,
                    'payload' => $payload,
                ]
            );

            $translation = $row['translation'] ?? [];

            $document->translations()->updateOrCreate(
                ['locale' => (string) ($translation['locale'] ?? 'hr')],
                [
                    'title' => (string) ($translation['title'] ?? $row['code']),
                    'slug' => (string) ($translation['slug'] ?? $row['code']),
                    'excerpt' => $translation['excerpt'] ?? null,
                    'meta_title' => $translation['meta_title'] ?? null,
                    'meta_description' => $translation['meta_description'] ?? null,
                    'payload' => $translation['payload'] ?? null,
                ]
            );
        }
    }

    /**
     * @param  mixed  $existingPayload
     * @param  mixed  $seedPayload
     * @return array<string, mixed>|null
     */
    private function mergePayload(mixed $existingPayload, mixed $seedPayload, ?string $remoteDownloadUrl, ?string $remoteCoverImageUrl): ?array
    {
        $payload = [];

        if (is_array($existingPayload)) {
            $payload = array_merge($payload, $existingPayload);
        }

        if (is_array($seedPayload)) {
            $payload = array_merge($payload, $seedPayload);
        }

        if ($remoteDownloadUrl !== null) {
            $payload['remote_download_url'] = $remoteDownloadUrl;
        }

        if ($remoteCoverImageUrl !== null) {
            $payload['remote_cover_image_url'] = $remoteCoverImageUrl;
        }

        return $payload !== [] ? $payload : null;
    }

    private function preferExistingUrl(?string $existingUrl, ?string $seedUrl): ?string
    {
        $existingUrl = $this->normalizeNullableUrl($existingUrl);
        if ($existingUrl === null) {
            return $seedUrl;
        }

        if ($this->isLocalStorageUrl($existingUrl) || ! $this->isWordPressUploadsUrl($existingUrl)) {
            return $existingUrl;
        }

        return $seedUrl;
    }

    private function preferExistingBoolean(mixed $existingValue, mixed $seedValue, bool $default): bool
    {
        if ($existingValue !== null) {
            return (bool) $existingValue;
        }

        if ($seedValue !== null) {
            return (bool) $seedValue;
        }

        return $default;
    }

    private function preferExistingPublishedAt(?string $existingValue, mixed $seedValue): ?string
    {
        if ($existingValue !== null) {
            return $existingValue;
        }

        $seed = trim((string) ($seedValue ?? ''));

        return $seed !== '' ? $seed : null;
    }

    private function isLocalStorageUrl(string $url): bool
    {
        $path = (string) parse_url($url, PHP_URL_PATH);

        return str_starts_with($path, '/storage/');
    }

    private function isWordPressUploadsUrl(string $url): bool
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = (string) parse_url($url, PHP_URL_PATH);

        return $host === 'alphacapitalis.com' && str_starts_with($path, '/wp-content/uploads/');
    }

    private function normalizeNullableUrl(mixed $value): ?string
    {
        $url = trim((string) ($value ?? ''));

        return $url !== '' ? $url : null;
    }
}
