<?php

use App\Support\Content\FinanceServicePageDefaults;
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
            ->where('template_key', ServicePageTemplateRegistry::FINANCE)
            ->orderByRaw('case when code = ? then 0 else 1 end', [ServicePageTemplateRegistry::defaultCode(ServicePageTemplateRegistry::FINANCE)])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->value('id');

        if (! $servicePageId) {
            return;
        }

        $translation = DB::table('content_service_page_translations')
            ->where('service_page_id', $servicePageId)
            ->where('locale', 'hr')
            ->first(['payload']);

        if (! $translation) {
            return;
        }

        $payload = json_decode((string) $translation->payload, true);
        $payload = is_array($payload) ? $payload : [];
        $defaults = FinanceServicePageDefaults::defaultsForLocale('hr');

        foreach (['ma', 'due_diligence', 'valuations', 'capital_raising', 'restructuring'] as $sectionKey) {
            if (array_key_exists($sectionKey, $defaults)) {
                $payload[$sectionKey] = $defaults[$sectionKey];
            }
        }

        DB::table('content_service_page_translations')
            ->where('service_page_id', $servicePageId)
            ->where('locale', 'hr')
            ->update([
                'payload' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Intentionally left as a no-op so rollback never removes user-managed content.
    }
};
