<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * @var array<int, array{title:string, youtube_url:string}>
     */
    private array $videos = [
        [
            'title' => 'ALPHA CAPITALIS - Uvođenje kontroling sustava',
            'youtube_url' => 'https://www.youtube.com/watch?v=djek0uU9tpA',
        ],
        [
            'title' => 'ALPHA CAPITALIS - Benefiti kontroling sustava',
            'youtube_url' => 'https://www.youtube.com/watch?v=2NY6q82hIgo',
        ],
        [
            'title' => 'ALPHA CAPITALIS - Metode procjene vrijednosti',
            'youtube_url' => 'https://www.youtube.com/watch?v=MI0sgOtWVkU',
        ],
        [
            'title' => 'ALPHA CAPITALIS - Dubinsko snimanje',
            'youtube_url' => 'https://www.youtube.com/watch?v=giFt4ZmSlb8',
        ],
        [
            'title' => 'ALPHA CAPITALIS - Financiranje Startup-a',
            'youtube_url' => 'https://www.youtube.com/watch?v=mfjlZxpJf4U',
        ],
        [
            'title' => 'ALPHA CAPITALIS - Tranzicija obiteljskog biznisa',
            'youtube_url' => 'https://www.youtube.com/watch?v=5FFawI7XCN4',
        ],
        [
            'title' => 'ALPHA CAPITALIS - Spajanja, pripajanja i preuzimanja',
            'youtube_url' => 'https://www.youtube.com/watch?v=fTcFqkJE164',
        ],
        [
            'title' => 'ALPHA CAPITALIS - Izvori financiranja',
            'youtube_url' => 'https://www.youtube.com/watch?v=caJnbuuKo_w',
        ],
        [
            'title' => 'ALPHA CAPITALIS - Kako izraditi poslovni plan',
            'youtube_url' => 'https://www.youtube.com/watch?v=VA7LlrHMsiM',
        ],
        [
            'title' => 'ALPHA CAPITALIS - Uvod u svijet financija',
            'youtube_url' => 'https://www.youtube.com/watch?v=GivT5NzdO1c',
        ],
    ];

    public function up(): void
    {
        $pageId = DB::table('content_info_pages')
            ->where('code', 'academy')
            ->value('id');

        if (! $pageId) {
            $pageId = DB::table('content_info_page_translations')
                ->where('locale', 'hr')
                ->where('slug', 'akademija')
                ->value('page_id');
        }

        if (! $pageId) {
            return;
        }

        $page = DB::table('content_info_pages')
            ->where('id', $pageId)
            ->first(['payload']);

        $payload = $this->decodePayload($page?->payload);
        $payload['video_source'] = [
            'mode' => 'manual',
            'items' => $this->videos,
        ];

        DB::table('content_info_pages')
            ->where('id', $pageId)
            ->update([
                'payload' => $this->encodePayload($payload),
                'updated_at' => now(),
            ]);

        $this->upsertTranslationSection(
            (int) $pageId,
            'hr',
            'Online edukacija i personalizirani trening',
            ''
        );

        $this->upsertTranslationSection(
            (int) $pageId,
            'en',
            'Online education and personalized training',
            ''
        );
    }

    public function down(): void
    {
        $pageId = DB::table('content_info_pages')
            ->where('code', 'academy')
            ->value('id');

        if (! $pageId) {
            return;
        }

        $page = DB::table('content_info_pages')
            ->where('id', $pageId)
            ->first(['payload']);

        $payload = $this->decodePayload($page?->payload);
        unset($payload['video_source']);

        DB::table('content_info_pages')
            ->where('id', $pageId)
            ->update([
                'payload' => $this->encodePayload($payload),
                'updated_at' => now(),
            ]);

        foreach (['hr', 'en'] as $locale) {
            $translation = DB::table('content_info_page_translations')
                ->where('page_id', $pageId)
                ->where('locale', $locale)
                ->first(['payload']);

            if (! $translation) {
                continue;
            }

            $payload = $this->decodePayload($translation->payload);
            unset($payload['academy_video_section']);

            DB::table('content_info_page_translations')
                ->where('page_id', $pageId)
                ->where('locale', $locale)
                ->update([
                    'payload' => $this->encodePayload($payload),
                    'updated_at' => now(),
                ]);
        }
    }

    private function upsertTranslationSection(int $pageId, string $locale, string $title, string $intro): void
    {
        $translation = DB::table('content_info_page_translations')
            ->where('page_id', $pageId)
            ->where('locale', $locale)
            ->first(['payload']);

        if (! $translation) {
            return;
        }

        $payload = $this->decodePayload($translation->payload);
        $payload['academy_video_section'] = array_filter([
            'title' => $title,
            'intro' => $intro,
        ], static fn ($value): bool => $value !== '');

        DB::table('content_info_page_translations')
            ->where('page_id', $pageId)
            ->where('locale', $locale)
            ->update([
                'payload' => $this->encodePayload($payload),
                'updated_at' => now(),
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function decodePayload(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);

            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return [];
    }

    private function encodePayload(array $payload): ?string
    {
        if ($payload === []) {
            return null;
        }

        return json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
};
