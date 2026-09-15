<?php

use App\Support\Content\AuditServicePageDefaults;
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
            ->where('template_key', ServicePageTemplateRegistry::AUDIT)
            ->orderByRaw('case when code = ? then 0 else 1 end', [
                ServicePageTemplateRegistry::defaultCode(ServicePageTemplateRegistry::AUDIT),
            ])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->value('id');

        if (! $servicePageId) {
            return;
        }

        DB::table('content_service_page_translations')
            ->where('service_page_id', $servicePageId)
            ->orderBy('id')
            ->get(['id', 'locale', 'payload'])
            ->each(function (object $translation): void {
                $payload = json_decode((string) $translation->payload, true);
                $payload = is_array($payload) ? $payload : [];

                if (array_key_exists('transparency_reports', $payload)) {
                    return;
                }

                $defaults = AuditServicePageDefaults::defaultsForLocale((string) $translation->locale);
                $payload['transparency_reports'] = (array) data_get($defaults, 'transparency_reports', []);

                DB::table('content_service_page_translations')
                    ->where('id', $translation->id)
                    ->update([
                        'payload' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        'updated_at' => now(),
                    ]);
            });
    }

    public function down(): void
    {
        // Content remains in place so rolling back code never removes published document links.
    }
};
