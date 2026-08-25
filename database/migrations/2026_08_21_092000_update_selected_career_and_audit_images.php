<?php

use App\Models\Content\Page\InfoPage;
use App\Models\Content\Service\ServicePage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('media')) {
            return;
        }

        $this->updateCareerGallery();
        $this->updateAuditImages();
    }

    public function down(): void
    {
        // Editorial media is intentionally preserved on rollback.
    }

    private function updateCareerGallery(): void
    {
        if (! Schema::hasTable('content_info_pages')) {
            return;
        }

        $page = InfoPage::query()
            ->where(function ($query): void {
                $query->where('code', 'career')->orWhere('layout', 'career');
            })
            ->orderByRaw("CASE WHEN code = 'career' THEN 0 ELSE 1 END")
            ->first();

        if (! $page || $page->getMedia('career_gallery_images')->isNotEmpty()) {
            return;
        }

        $images = [
            [
                'path' => public_path('front-theme/images/career/career-office-detail.jpg'),
                'name' => 'Detalj ureda ALPHA CAPITALISA',
                'file_name' => 'career-office-detail.jpg',
                'alt_hr' => 'Brendirani prostor ureda ALPHA CAPITALISA',
                'alt_en' => 'A branded area in the ALPHA CAPITALIS office',
            ],
            [
                'path' => public_path('front-theme/images/career/career-office.jpg'),
                'name' => 'Radni prostor ALPHA CAPITALISA',
                'file_name' => 'career-office.jpg',
                'alt_hr' => 'ALPHA CAPITALIS tim u zajedničkom uredskom prostoru',
                'alt_en' => 'The ALPHA CAPITALIS team in a shared office space',
            ],
            [
                'path' => public_path('front-theme/images/career/career-team-collaboration.jpg'),
                'name' => 'Suradnja ALPHA CAPITALIS stručnjaka',
                'file_name' => 'career-team-collaboration.jpg',
                'alt_hr' => 'Dvojica ALPHA CAPITALIS stručnjaka tijekom zajedničkog rada',
                'alt_en' => 'Two ALPHA CAPITALIS specialists working together',
            ],
        ];

        foreach ($images as $image) {
            if (! is_file($image['path'])) {
                continue;
            }

            $page->addMedia($image['path'])
                ->preservingOriginal()
                ->usingName($image['name'])
                ->usingFileName($image['file_name'])
                ->withCustomProperties([
                    'alt' => [
                        'hr' => $image['alt_hr'],
                        'en' => $image['alt_en'],
                    ],
                ])
                ->toMediaCollection('career_gallery_images');
        }
    }

    private function updateAuditImages(): void
    {
        if (! Schema::hasTable('content_service_pages')) {
            return;
        }

        $imagePath = public_path('front-theme/images/services/audit-client-meeting.jpg');

        if (! is_file($imagePath)) {
            return;
        }

        $auditPage = ServicePage::query()
            ->where(fn ($query) => $query->where('code', 'audit')->orWhere('template_key', 'audit'))
            ->orderByRaw("CASE WHEN code = 'audit' THEN 0 ELSE 1 END")
            ->first();
        $servicesPage = ServicePage::query()
            ->where(fn ($query) => $query->where('code', 'services')->orWhere('template_key', 'services_index'))
            ->orderByRaw("CASE WHEN code = 'services' THEN 0 ELSE 1 END")
            ->first();

        $this->replaceImage($auditPage, $imagePath, 'service_hero_image');
        $this->replaceImage($servicesPage, $imagePath, 'services_index_audit_image');
    }

    private function replaceImage(?ServicePage $page, string $path, string $collection): void
    {
        if (! $page || $page->getFirstMedia($collection)) {
            return;
        }

        $croatianAlt = 'Predaja ALPHA CAPITALIS vizitke na poslovnom sastanku';
        $englishAlt = 'An ALPHA CAPITALIS business card being handed over at a client meeting';

        $page->addMedia($path)
            ->preservingOriginal()
            ->usingName($croatianAlt)
            ->usingFileName(basename($path))
            ->withCustomProperties([
                'alt' => [
                    'hr' => $croatianAlt,
                    'en' => $englishAlt,
                ],
            ])
            ->toMediaCollection($collection);
    }
};
