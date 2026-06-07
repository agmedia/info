<?php

use App\Support\Content\EuFundsServicePageDefaults;
use App\Support\Content\ServicePageTemplateRegistry;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('content_service_pages') || ! Schema::hasTable('content_service_page_translations')) {
            return;
        }

        $templateKey = ServicePageTemplateRegistry::EU_FUNDS;
        $servicePageId = DB::table('content_service_pages')
            ->where('template_key', $templateKey)
            ->orderByRaw('case when code = ? then 0 else 1 end', [ServicePageTemplateRegistry::defaultCode($templateKey)])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->value('id');

        if (! $servicePageId) {
            return;
        }

        $now = now();

        foreach (['hr', 'en'] as $locale) {
            $translation = DB::table('content_service_page_translations')
                ->where('service_page_id', $servicePageId)
                ->where('locale', $locale)
                ->first(['id', 'payload']);

            if (! $translation) {
                continue;
            }

            $payload = json_decode((string) ($translation->payload ?? ''), true);
            $payload = is_array($payload) ? $payload : [];
            $defaults = EuFundsServicePageDefaults::defaultsForLocale($locale);

            foreach ([
                'hero',
                'overview',
                'process',
                'approach',
                'source_modules',
                'calls',
                'resources',
                'laws',
                'testimonials',
                'meeting',
                'blog_section',
            ] as $sectionKey) {
                $payload[$sectionKey] = $defaults[$sectionKey] ?? [];
            }

            unset($payload['chart']);

            DB::table('content_service_page_translations')
                ->where('id', $translation->id)
                ->update([
                    'meta_title' => $locale === 'hr' ? 'EU fondovi' : 'EU Funds',
                    'meta_description' => $locale === 'hr'
                        ? 'Savjetovanje za EU fondove, natječaje, HBOR i HAMAG instrumente te porezne olakšice.'
                        : 'Advisory support for EU funds, funding calls, HBOR and HAMAG instruments, and tax incentives.',
                    'payload' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'updated_at' => $now,
                ]);
        }
    }

    public function down(): void
    {
        // Intentionally left as a no-op so rollback never restores public-facing clutter.
    }
};
