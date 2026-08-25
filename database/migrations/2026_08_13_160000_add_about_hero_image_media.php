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
                $query->where('code', 'about-us')
                    ->orWhere('layout', 'about');
            })
            ->orderByRaw("CASE WHEN code = 'about-us' THEN 0 ELSE 1 END")
            ->first();

        if (! $page || $page->getFirstMedia('about_hero_image')) {
            return;
        }

        $path = public_path('front-theme/images/about/o-nama.jpg');

        if (! is_file($path)) {
            return;
        }

        $page->addMedia($path)
            ->preservingOriginal()
            ->usingName('ALPHA CAPITALIS tim')
            ->usingFileName('o-nama.jpg')
            ->withCustomProperties([
                'alt' => [
                    'hr' => 'ALPHA CAPITALIS tim',
                    'en' => 'ALPHA CAPITALIS team',
                ],
            ])
            ->toMediaCollection('about_hero_image');
    }

    public function down(): void
    {
        // CMS-managed media is intentionally preserved on rollback.
    }
};
