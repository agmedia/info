<?php

use App\Support\Content\AdvisoryServicePageDefaults;
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

        $servicePageId = DB::table('content_service_pages')
            ->where('template_key', ServicePageTemplateRegistry::ADVISORY)
            ->orderByRaw('case when code = ? then 0 else 1 end', [ServicePageTemplateRegistry::defaultCode(ServicePageTemplateRegistry::ADVISORY)])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->value('id');

        if (! $servicePageId) {
            return;
        }

        $translation = DB::table('content_service_page_translations')
            ->where('service_page_id', $servicePageId)
            ->where('locale', 'hr')
            ->first(['id', 'payload']);

        if (! $translation) {
            return;
        }

        $payload = json_decode((string) ($translation->payload ?? ''), true);
        $payload = is_array($payload) ? $payload : [];
        $defaults = AdvisoryServicePageDefaults::defaultsForLocale('hr');

        foreach ([
            'hero',
            'overview',
            'services_intro',
            'service_cards',
            'funding',
            'bank_loans',
            'zopu',
            'ma',
            'valuations',
            'due_diligence',
            'tax',
            'approach',
            'meeting',
            'blog_section',
        ] as $sectionKey) {
            $payload[$sectionKey] = $defaults[$sectionKey];
        }

        DB::table('content_service_page_translations')
            ->where('id', $translation->id)
            ->update([
                'meta_title' => 'Savjetovanje',
                'payload' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Intentionally left as a no-op so rollback never removes user-managed content.
    }
};
