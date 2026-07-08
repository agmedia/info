<?php

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

        $payload['hero'] = array_replace((array) ($payload['hero'] ?? []), [
            'subtitle_lead' => 'Savjetovanje',
            'subtitle_accent' => '',
            'intro' => 'Savjetovanje (Advisory) pruža stručnu podršku društvima, investitorima i poduzetnicima u donošenju financijskih i strateških odluka te stvaranju dugoročne vrijednosti.',
        ]);

        $payload['overview'] = array_replace((array) ($payload['overview'] ?? []), [
            'kicker' => 'ŠTO JE SAVJETOVANJE?',
            'title' => 'Što je savjetovanje?',
            'body' => [
                'Savjetovanje (Advisory) pruža stručnu podršku društvima, investitorima i poduzetnicima u donošenju strateških financijskih odluka kroz usluge spajanja i preuzimanja, procjene vrijednosti, dubinska snimanja, pribavljanje kapitala i financijsko restrukturiranje, s ciljem upravljanja rizicima i stvaranja dugoročne vrijednosti.',
            ],
        ]);

        $payload['services_intro'] = array_replace((array) ($payload['services_intro'] ?? []), [
            'kicker' => 'USLUGE SAVJETOVANJA',
            'title' => 'Usluge savjetovanja',
        ]);

        $payload['blog_section'] = array_replace((array) ($payload['blog_section'] ?? []), [
            'intro' => 'Zadnje objave i novosti iz područja financija, poreza, transakcija i savjetovanja.',
        ]);

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
