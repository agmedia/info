<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('system_settings')) {
            return;
        }

        $setting = DB::table('system_settings')
            ->where('key', 'front_navigation_main')
            ->first(['id', 'value']);

        if (! $setting) {
            return;
        }

        $items = json_decode((string) ($setting->value ?? ''), true);

        if (! is_array($items)) {
            return;
        }

        $hasHomeItem = collect($items)->contains(static function ($item): bool {
            if (! is_array($item) || (string) ($item['type'] ?? '') !== 'custom') {
                return false;
            }

            $urls = array_filter([
                trim((string) ($item['url'] ?? '')),
                trim((string) ($item['url_translations']['hr'] ?? '')),
                trim((string) ($item['url_translations']['en'] ?? '')),
            ]);

            return in_array('/', $urls, true);
        });

        if ($hasHomeItem) {
            return;
        }

        $items = collect($items)
            ->filter(static fn ($item): bool => is_array($item))
            ->sortBy(static fn (array $item): int => (int) ($item['sort_order'] ?? 0))
            ->values()
            ->map(static function (array $item, int $index): array {
                $item['sort_order'] = $index + 1;

                return $item;
            })
            ->all();

        array_unshift($items, [
            'url' => '/',
            'type' => 'custom',
            'label' => 'Početna',
            'page_id' => 0,
            'is_active' => true,
            'sort_order' => 0,
            'show_dropdown' => false,
            'open_in_new_tab' => false,
            'url_translations' => [
                'en' => '/',
                'hr' => '/',
            ],
            'label_translations' => [
                'en' => 'Home',
                'hr' => 'Početna',
            ],
        ]);

        DB::table('system_settings')
            ->where('id', $setting->id)
            ->update([
                'value' => json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => now(),
            ]);

        Cache::forget('settings.system.map');
    }

    public function down(): void
    {
        // User-managed CMS navigation is intentionally preserved on rollback.
    }
};
