<?php

namespace App\Services\Content;

use App\Models\Content\Resource\ResourceDocument;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ResourcePublicationSyncService
{
    /**
     * @param  array{
     *     codes?:array<int, string>
     * }  $options
     * @return array{
     *     total_remote:int,
     *     synced_count:int,
     *     activated_count:int,
     *     deactivated_count:int,
     *     documents:array<int,array{
     *         code:string,
     *         status:string,
     *         published_at:string|null,
     *         source_url:string|null
     *     }>
     * }
     */
    public function sync(array $options = []): array
    {
        $codes = collect($options['codes'] ?? [])
            ->map(fn (mixed $code): string => trim((string) $code))
            ->filter()
            ->values()
            ->all();

        $remoteDocuments = $this->fetchRemoteDocuments();
        $documents = ResourceDocument::query()
            ->when($codes !== [], fn ($query) => $query->whereIn('code', $codes))
            ->with('translations')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($documents->isEmpty()) {
            throw new RuntimeException('No resource documents matched the requested filters.');
        }

        $result = [
            'total_remote' => count($remoteDocuments),
            'synced_count' => 0,
            'activated_count' => 0,
            'deactivated_count' => 0,
            'documents' => [],
        ];

        foreach ($documents as $document) {
            $remote = $this->matchRemoteDocument($document, $remoteDocuments);
            $isPublished = $remote !== null;

            $document->forceFill([
                'is_active' => $isPublished,
                'published_at' => $remote['published_at'] ?? null,
                'source_url' => $remote['source_url'] ?? $document->source_url,
                'payload' => $this->mergePayload(
                    $document->payload,
                    $remote['remote_id'] ?? null,
                    $remote['status'] ?? 'missing'
                ),
            ])->save();

            $result['synced_count']++;

            if ($isPublished) {
                $result['activated_count']++;
            } else {
                $result['deactivated_count']++;
            }

            $result['documents'][] = [
                'code' => (string) $document->code,
                'status' => $isPublished ? 'publish' : 'missing',
                'published_at' => $document->published_at?->toAtomString(),
                'source_url' => $document->source_url,
            ];
        }

        return $result;
    }

    /**
     * @return array<int, array{
     *     remote_id:int,
     *     slug:string,
     *     status:string,
     *     published_at:string|null,
     *     source_url:string|null
     * }>
     */
    private function fetchRemoteDocuments(): array
    {
        $page = 1;
        $rows = [];

        while (true) {
            $response = Http::timeout(60)
                ->retry(2, 500)
                ->withHeaders([
                    'User-Agent' => 'AlphaCapitalis-ResourcePublicationSync/1.0',
                ])
                ->get('https://alphacapitalis.com/wp-json/wp/v2/resources', [
                    'per_page' => 100,
                    'page' => $page,
                    '_fields' => 'id,slug,status,date,link',
                ]);

            if (! $response->successful()) {
                throw new RuntimeException('Unable to fetch published resources from WordPress.');
            }

            $chunk = collect($response->json())
                ->filter(fn (mixed $row): bool => is_array($row))
                ->map(function (array $row): array {
                    return [
                        'remote_id' => (int) ($row['id'] ?? 0),
                        'slug' => trim((string) ($row['slug'] ?? '')),
                        'status' => trim((string) ($row['status'] ?? '')),
                        'published_at' => $this->normalizeDate($row['date'] ?? null),
                        'source_url' => $this->normalizeNullableString($row['link'] ?? null),
                    ];
                })
                ->filter(fn (array $row): bool => $row['remote_id'] > 0 && $row['slug'] !== '' && $row['status'] === 'publish')
                ->values()
                ->all();

            $rows = array_merge($rows, $chunk);

            if (count($chunk) < 100) {
                break;
            }

            $page++;
        }

        return $rows;
    }

    /**
     * @param  array<int, array{
     *     remote_id:int,
     *     slug:string,
     *     status:string,
     *     published_at:string|null,
     *     source_url:string|null
     * }>  $remoteDocuments
     * @return array{
     *     remote_id:int,
     *     slug:string,
     *     status:string,
     *     published_at:string|null,
     *     source_url:string|null
     * }|null
     */
    private function matchRemoteDocument(ResourceDocument $document, array $remoteDocuments): ?array
    {
        $payload = is_array($document->payload) ? $document->payload : [];
        $wpId = isset($payload['wp_id']) && is_numeric((string) $payload['wp_id'])
            ? (int) $payload['wp_id']
            : null;

        if ($wpId !== null) {
            foreach ($remoteDocuments as $remoteDocument) {
                if ((int) $remoteDocument['remote_id'] === $wpId) {
                    return $remoteDocument;
                }
            }
        }

        $candidateSlugs = collect($document->translations)
            ->pluck('slug')
            ->prepend((string) $document->code)
            ->filter(fn (mixed $slug): bool => trim((string) $slug) !== '')
            ->map(fn (mixed $slug): string => trim((string) $slug))
            ->unique()
            ->values()
            ->all();

        foreach ($candidateSlugs as $slug) {
            foreach ($remoteDocuments as $remoteDocument) {
                if ((string) $remoteDocument['slug'] === $slug) {
                    return $remoteDocument;
                }
            }
        }

        return null;
    }

    /**
     * @param  mixed  $payload
     * @return array<string, mixed>|null
     */
    private function mergePayload(mixed $payload, ?int $remoteId, string $status): ?array
    {
        $merged = is_array($payload) ? $payload : [];

        if ($remoteId !== null) {
            $merged['wp_id'] = $remoteId;
        }

        $merged['remote_publication_status'] = $status;
        $merged['remote_publication_checked_at'] = now()->toAtomString();

        return $merged !== [] ? $merged : null;
    }

    private function normalizeDate(mixed $value): ?string
    {
        $date = trim((string) ($value ?? ''));
        if ($date === '') {
            return null;
        }

        return CarbonImmutable::parse($date, 'Europe/Zagreb')->toDateTimeString();
    }

    private function normalizeNullableString(mixed $value): ?string
    {
        $string = trim((string) ($value ?? ''));

        return $string !== '' ? $string : null;
    }
}
