<?php

use App\Models\Content\Page\InfoPage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('content_info_pages') || ! Schema::hasTable('media')) {
            return;
        }

        $page = InfoPage::query()
            ->where(function ($query): void {
                $query->where('code', 'career')
                    ->orWhere('layout', 'career');
            })
            ->orderByRaw("CASE WHEN code = 'career' THEN 0 ELSE 1 END")
            ->first();

        if (! $page || $page->getFirstMedia('career_hero_image')) {
            return;
        }

        $path = public_path('front-theme/images/career/karijera.png');

        if (! is_file($path)) {
            return;
        }

        $page->addMedia($path)
            ->preservingOriginal()
            ->usingName('ALPHA CAPITALIS tim u parku')
            ->usingFileName('karijera.png')
            ->withCustomProperties([
                'alt' => [
                    'hr' => 'ALPHA CAPITALIS tim u parku',
                    'en' => 'ALPHA CAPITALIS team in the park',
                ],
            ])
            ->toMediaCollection('career_hero_image');
    }

    public function down(): void
    {
        // CMS-managed media is intentionally preserved on rollback.
    }
};
