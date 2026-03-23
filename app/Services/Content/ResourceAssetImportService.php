<?php

namespace App\Services\Content;

use App\Models\Content\Resource\ResourceDocument;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use SimpleXMLElement;
use Throwable;

class ResourceAssetImportService
{
    /**
     * @param  array{
     *     codes?:array<int, string>,
     *     force?:bool
     * }  $options
     * @return array{
     *     processed_count:int,
     *     localized_download_count:int,
     *     localized_cover_count:int,
     *     error_count:int,
     *     documents:array<int,array{
     *         code:string,
     *         download_status:string,
     *         cover_status:string,
     *         download_url:string|null,
     *         cover_image_url:string|null,
     *         error:string|null
     *     }>
     * }
     */
    public function import(string $filePath, array $options = []): array
    {
        $this->extendExecutionTime();

        $resolvedPath = $this->resolveFilePath($filePath);
        $codes = collect($options['codes'] ?? [])
            ->map(fn (mixed $code): string => trim((string) $code))
            ->filter()
            ->values()
            ->all();
        $force = (bool) ($options['force'] ?? false);

        $attachmentIndex = $this->loadAttachmentIndex($resolvedPath);

        $documents = ResourceDocument::query()
            ->when($codes !== [], fn ($query) => $query->whereIn('code', $codes))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($documents->isEmpty()) {
            throw new RuntimeException('No resource documents matched the requested filters.');
        }

        $result = [
            'processed_count' => 0,
            'localized_download_count' => 0,
            'localized_cover_count' => 0,
            'error_count' => 0,
            'documents' => [],
        ];

        foreach ($documents as $document) {
            $this->extendExecutionTime();

            $row = $this->localizeDocument($document, $attachmentIndex, $force);

            $result['processed_count']++;
            $result['documents'][] = $row;

            if ($row['download_status'] === 'localized') {
                $result['localized_download_count']++;
            }

            if ($row['cover_status'] === 'localized') {
                $result['localized_cover_count']++;
            }

            if ($row['error'] !== null) {
                $result['error_count']++;
            }
        }

        return $result;
    }

    /**
     * @param  array<int, array<int, array{id:int,url:string,title:string}>>  $attachmentIndex
     * @return array{
     *     code:string,
     *     download_status:string,
     *     cover_status:string,
     *     download_url:string|null,
     *     cover_image_url:string|null,
     *     error:string|null
     * }
     */
    private function localizeDocument(ResourceDocument $document, array $attachmentIndex, bool $force): array
    {
        try {
            return DB::transaction(function () use ($document, $attachmentIndex, $force): array {
                $document->refresh();

                $payload = is_array($document->payload) ? $document->payload : [];
                $wpId = isset($payload['wp_id']) && is_numeric((string) $payload['wp_id'])
                    ? (int) $payload['wp_id']
                    : 0;
                $attachments = $wpId > 0 ? ($attachmentIndex[$wpId] ?? []) : [];

                $remoteDownloadUrl = $this->resolveRemoteDownloadUrl($document, $payload, $attachments);
                $remoteCoverImageUrl = $this->resolveRemoteCoverImageUrl($document, $payload, $attachments);

                if ($remoteCoverImageUrl === null) {
                    $remoteCoverImageUrl = $this->resolveRemoteCoverImageFromSourcePage($document);
                }

                $downloadResult = $this->localizeAsset(
                    remoteUrl: $remoteDownloadUrl,
                    directory: 'resource-documents/downloads',
                    basename: (string) $document->code,
                    currentUrl: $document->download_url,
                    force: $force
                );

                $coverResult = $this->localizeAsset(
                    remoteUrl: $remoteCoverImageUrl,
                    directory: 'resource-documents/covers',
                    basename: (string) $document->code,
                    currentUrl: $document->cover_image_url,
                    force: $force
                );

                if ($remoteDownloadUrl !== null) {
                    $payload['remote_download_url'] = $remoteDownloadUrl;
                }

                if ($remoteCoverImageUrl !== null) {
                    $payload['remote_cover_image_url'] = $remoteCoverImageUrl;
                }

                if ($wpId > 0) {
                    $payload['wordpress_attachment_ids'] = array_values(array_map(
                        static fn (array $attachment): int => (int) $attachment['id'],
                        $attachments
                    ));
                }

                $document->forceFill([
                    'download_url' => $downloadResult['url'],
                    'cover_image_url' => $coverResult['url'],
                    'payload' => $payload,
                ])->save();

                return [
                    'code' => (string) $document->code,
                    'download_status' => $downloadResult['status'],
                    'cover_status' => $coverResult['status'],
                    'download_url' => $downloadResult['url'],
                    'cover_image_url' => $coverResult['url'],
                    'error' => null,
                ];
            });
        } catch (Throwable $exception) {
            return [
                'code' => (string) $document->code,
                'download_status' => 'failed',
                'cover_status' => 'failed',
                'download_url' => $document->download_url,
                'cover_image_url' => $document->cover_image_url,
                'error' => $exception->getMessage(),
            ];
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<int, array{id:int,url:string,title:string}>  $attachments
     */
    private function resolveRemoteDownloadUrl(ResourceDocument $document, array $payload, array $attachments): ?string
    {
        $currentUrl = $this->normalizeUrl((string) ($document->download_url ?? ''));
        if ($this->isRemoteUrl($currentUrl)) {
            return $currentUrl;
        }

        $payloadUrl = $this->normalizeUrl((string) ($payload['remote_download_url'] ?? ''));
        if ($this->isRemoteUrl($payloadUrl)) {
            return $payloadUrl;
        }

        foreach ($attachments as $attachment) {
            if ($this->isDocumentAttachment((string) $attachment['url'])) {
                return $attachment['url'];
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<int, array{id:int,url:string,title:string}>  $attachments
     */
    private function resolveRemoteCoverImageUrl(ResourceDocument $document, array $payload, array $attachments): ?string
    {
        $currentUrl = $this->normalizeUrl((string) ($document->cover_image_url ?? ''));
        if ($this->isRemoteUrl($currentUrl)) {
            return $currentUrl;
        }

        $payloadUrl = $this->normalizeUrl((string) ($payload['remote_cover_image_url'] ?? ''));
        if ($this->isRemoteUrl($payloadUrl)) {
            return $payloadUrl;
        }

        foreach ($attachments as $attachment) {
            if ($this->isImageAttachment((string) $attachment['url']) && $this->looksLikeThumbnail($attachment)) {
                return $attachment['url'];
            }
        }

        foreach ($attachments as $attachment) {
            if ($this->isImageAttachment((string) $attachment['url'])) {
                return $attachment['url'];
            }
        }

        return null;
    }

    private function resolveRemoteCoverImageFromSourcePage(ResourceDocument $document): ?string
    {
        $sourceUrl = $this->normalizeNullableUrl($document->source_url);
        if ($sourceUrl === null) {
            return null;
        }

        $response = Http::timeout(60)
            ->retry(1, 250)
            ->withHeaders([
                'User-Agent' => 'AlphaCapitalis-ResourceAssetImport/1.0',
            ])
            ->get($sourceUrl);

        if (! $response->successful()) {
            return null;
        }

        $html = (string) $response->body();

        if (preg_match('/"featuredImage":"([^"]+)"/', $html, $matches) === 1) {
            $url = $this->decodeEmbeddedUrl($matches[1]);
            if ($this->isRemoteUrl($url)) {
                return $url;
            }
        }

        if (preg_match('/<meta\s+property=["\']og:image["\']\s+content=["\']([^"\']+)["\']/i', $html, $matches) === 1) {
            $url = $this->normalizeUrl($matches[1]);
            if ($this->isRemoteUrl($url)) {
                return $url;
            }
        }

        return null;
    }

    /**
     * @return array{status:string,url:string|null}
     */
    private function localizeAsset(
        ?string $remoteUrl,
        string $directory,
        string $basename,
        ?string $currentUrl,
        bool $force
    ): array {
        $currentUrl = $this->normalizeNullableUrl($currentUrl);
        $currentLocalPath = $this->storagePathFromUrl($currentUrl);

        if (! $force && $currentLocalPath !== null && Storage::disk('public')->exists($currentLocalPath)) {
            return [
                'status' => 'kept',
                'url' => Storage::disk('public')->url($currentLocalPath),
            ];
        }

        $remoteUrl = $this->normalizeNullableUrl($remoteUrl);
        if ($remoteUrl === null) {
            return [
                'status' => $currentUrl !== null ? 'kept' : 'missing',
                'url' => $currentUrl,
            ];
        }

        $extension = $this->resolveExtension($remoteUrl);
        $path = trim($directory, '/').'/'.$basename.'.'.$extension;

        if (! $force && Storage::disk('public')->exists($path)) {
            return [
                'status' => 'localized',
                'url' => Storage::disk('public')->url($path),
            ];
        }

        $response = Http::timeout(120)
            ->retry(2, 500)
            ->withHeaders([
                'User-Agent' => 'AlphaCapitalis-ResourceAssetImport/1.0',
            ])
            ->get($remoteUrl);

        if (! $response->successful()) {
            throw new RuntimeException(sprintf(
                'Asset download failed for "%s" with HTTP %d.',
                $remoteUrl,
                $response->status()
            ));
        }

        Storage::disk('public')->put($path, $response->body());

        return [
            'status' => 'localized',
            'url' => Storage::disk('public')->url($path),
        ];
    }

    private function resolveFilePath(string $filePath): string
    {
        $resolvedPath = trim($filePath);

        if ($resolvedPath === '') {
            throw new RuntimeException('A WordPress XML export path is required.');
        }

        if (! is_file($resolvedPath)) {
            throw new RuntimeException('WordPress XML export not found: '.$resolvedPath);
        }

        return $resolvedPath;
    }

    /**
     * @return array<int, array<int, array{id:int,url:string,title:string}>>
     */
    private function loadAttachmentIndex(string $filePath): array
    {
        $previous = libxml_use_internal_errors(true);

        try {
            $xml = simplexml_load_file($filePath, SimpleXMLElement::class, LIBXML_NOCDATA | LIBXML_NONET);
        } finally {
            libxml_use_internal_errors($previous);
        }

        if (! $xml instanceof SimpleXMLElement || ! isset($xml->channel)) {
            throw new RuntimeException('The WordPress XML export could not be parsed.');
        }

        $attachments = [];

        foreach ($xml->channel->item as $item) {
            $namespaces = $item->getNamespaces(true);
            $wp = isset($namespaces['wp']) ? $item->children($namespaces['wp']) : null;

            if (! $wp instanceof SimpleXMLElement || trim((string) $wp->post_type) !== 'attachment') {
                continue;
            }

            $parentId = is_numeric((string) $wp->post_parent) ? (int) $wp->post_parent : 0;
            $attachmentId = is_numeric((string) $wp->post_id) ? (int) $wp->post_id : 0;
            $url = $this->normalizeUrl((string) $wp->attachment_url);

            if ($parentId <= 0 || $attachmentId <= 0 || $url === '') {
                continue;
            }

            $attachments[$parentId] ??= [];
            $attachments[$parentId][] = [
                'id' => $attachmentId,
                'url' => $url,
                'title' => trim((string) $item->title),
            ];
        }

        return $attachments;
    }

    /**
     * @param  array{id:int,url:string,title:string}  $attachment
     */
    private function looksLikeThumbnail(array $attachment): bool
    {
        $needle = strtolower(trim((string) ($attachment['title'] ?? '')).' '.strtolower((string) ($attachment['url'] ?? '')));

        return str_contains($needle, 'thumbnail')
            || str_contains($needle, 'thumb')
            || str_contains($needle, 'cover');
    }

    private function isDocumentAttachment(string $url): bool
    {
        return in_array($this->resolveExtension($url), ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'], true);
    }

    private function isImageAttachment(string $url): bool
    {
        return in_array($this->resolveExtension($url), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'], true);
    }

    private function resolveExtension(string $url): string
    {
        $path = (string) parse_url($url, PHP_URL_PATH);
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if ($extension === 'jpeg') {
            return 'jpg';
        }

        return $extension !== '' ? $extension : 'bin';
    }

    private function isRemoteUrl(string $url): bool
    {
        return str_starts_with($url, 'http://') || str_starts_with($url, 'https://');
    }

    private function storagePathFromUrl(?string $url): ?string
    {
        if ($url === null) {
            return null;
        }

        $path = (string) parse_url($url, PHP_URL_PATH);
        if (! str_starts_with($path, '/storage/')) {
            return null;
        }

        $relativePath = ltrim(substr($path, strlen('/storage/')), '/');

        return $relativePath !== '' ? $relativePath : null;
    }

    private function normalizeNullableUrl(?string $url): ?string
    {
        $normalized = $this->normalizeUrl((string) ($url ?? ''));

        return $normalized !== '' ? $normalized : null;
    }

    private function normalizeUrl(string $url): string
    {
        return trim(html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    private function decodeEmbeddedUrl(string $value): string
    {
        $decoded = stripcslashes($value);
        $decoded = str_replace('\/', '/', $decoded);

        return $this->normalizeUrl($decoded);
    }

    private function extendExecutionTime(): void
    {
        if (function_exists('set_time_limit')) {
            @set_time_limit(0);
        }
    }
}
