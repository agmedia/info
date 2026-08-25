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

        $templateKey = ServicePageTemplateRegistry::ADVISORY;
        $servicePageId = DB::table('content_service_pages')
            ->where('template_key', $templateKey)
            ->orderByRaw('case when code = ? then 0 else 1 end', [ServicePageTemplateRegistry::defaultCode($templateKey)])
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

        // Existing CMS rows are authoritative even when intentionally blank.
        if ($translation) {
            return;
        }

        $slug = ServicePageTemplateRegistry::canonicalStructuralSlug($templateKey, 'hr');
        if (! is_string($slug) || $slug === '') {
            return;
        }

        DB::table('content_service_page_translations')->insert([
            'service_page_id' => $servicePageId,
            'locale' => 'hr',
            'title' => 'Savjetovanje',
            'slug' => $slug,
            'meta_title' => null,
            'meta_description' => null,
            'payload' => json_encode([
                'hero' => [
                    'intro' => 'Budućnost poslovanja oblikuju odluke koje donosite danas. Zato Vam pružamo stručnu financijsku i stratešku perspektivu koja pomaže prepoznati prilike, upravljati rizicima i stvarati dugoročnu vrijednost.',
                ],
                'overview' => [
                    'kicker' => 'SAVJETOVANJE',
                    'title' => 'Zašto Vam je savjetovanje bitno?',
                    'highlight_title' => 'Zašto Vam je savjetovanje bitno?',
                    'body' => [
                        'Važne poslovne odluke rijetko imaju jednostavne odgovore. Financijske, porezne i strateške odluke mogu imati dugoročan utjecaj na poslovanje, zbog čega je važno imati stručnu perspektivu na koju se možete osloniti.',
                        'Naše savjetovanje povezuje stručnost iz različitih područja kako bismo Vam pomogli sagledati širu sliku, prepoznati prilike, upravljati rizicima i donositi odluke s većom sigurnošću.',
                    ],
                ],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        // User-managed CMS content is intentionally preserved on rollback.
    }
};
