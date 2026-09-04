<?php

use App\Models\Content\Page\InfoPage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const PREVIOUS_TITLE = 'AUXILIUM CAPITALIS - ulaganje u budućnost';

    private const TITLE = 'Udruga AUXILIUM CAPITALIS - ulaganje u budućnost';

    private const IMAGE_ALT = 'Udruga AUXILIUM CAPITALIS pruža podršku mladima kroz obrazovanje i razvoj.';

    public function up(): void
    {
        if (! Schema::hasTable('content_info_pages') || ! Schema::hasTable('content_info_page_translations')) {
            return;
        }

        $page = InfoPage::query()
            ->where(function ($query): void {
                $query->where('code', 'about-us')
                    ->orWhere('layout', 'about');
            })
            ->orderByRaw("CASE WHEN code = 'about-us' THEN 0 ELSE 1 END")
            ->first();

        if (! $page) {
            return;
        }

        $translation = $page->translation('hr')->first();
        if ($translation) {
            $payload = is_array($translation->payload) ? $translation->payload : [];
            $responsibility = is_array(data_get($payload, 'about_page.responsibility'))
                ? (array) data_get($payload, 'about_page.responsibility')
                : [];

            if ($responsibility !== []) {
                if (($responsibility['title'] ?? null) === self::PREVIOUS_TITLE) {
                    $responsibility['title'] = self::TITLE;
                }

                if (trim((string) ($responsibility['image_alt'] ?? '')) === '') {
                    $responsibility['image_alt'] = self::IMAGE_ALT;
                }

                data_set($payload, 'about_page.responsibility', $responsibility);
                $translation->forceFill(['payload' => $payload])->save();
            }
        }

        if (! Schema::hasTable('media') || $page->getFirstMedia('about_responsibility_image')) {
            return;
        }

        $path = public_path('front-theme/images/about/auxilium-capitalis-udruga.png');
        if (! is_file($path)) {
            return;
        }

        $page->addMedia($path)
            ->preservingOriginal()
            ->usingName('Udruga AUXILIUM CAPITALIS')
            ->usingFileName('auxilium-capitalis-udruga.png')
            ->withCustomProperties([
                'alt' => [
                    'hr' => self::IMAGE_ALT,
                ],
            ])
            ->toMediaCollection('about_responsibility_image');
    }

    public function down(): void
    {
        // CMS-managed content and media are intentionally preserved on rollback.
    }
};
