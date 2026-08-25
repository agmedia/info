<?php

namespace Tests\Feature\Admin;

use App\Models\Content\ContentBlock;
use App\Models\Content\Page\InfoPage;
use App\Models\Content\Service\ServicePage;
use App\Models\Content\Team\TeamMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PendingContentMigrationsSafetyFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_pending_editorial_migrations_preserve_existing_cms_copy_and_media(): void
    {
        Storage::fake('public');
        config()->set('media-library.disk_name', 'public');
        config()->set('media-library.queue_conversions_by_default', false);
        $this->seed(\Database\Seeders\ContentBlockSeeder::class);

        $heroBlock = ContentBlock::query()->where('code', 'home-alpha-hero')->firstOrFail();
        $heroTranslation = $heroBlock->translations()->where('locale', 'en')->firstOrFail();
        $heroTranslation->update([
            'title' => '',
            'subtitle' => '',
        ]);
        $heroCroatianTranslation = $heroBlock->translations()->where('locale', 'hr')->firstOrFail();
        $heroCroatianTranslation->update([
            'title' => 'CMS exact hero title',
            'subtitle' => 'CMS exact hero subtitle',
        ]);

        $statsBlock = ContentBlock::query()->where('code', 'home-alpha-stats')->firstOrFail();
        $statsTranslation = $statsBlock->translations()->where('locale', 'en')->firstOrFail();
        $statsTranslation->update([
            'payload' => [
                'stats' => [
                    ['value' => '123', 'suffix' => '+', 'label' => 'CMS projects'],
                    ['value' => '', 'suffix' => '', 'label' => 'CMS clients intentionally blank'],
                    ['value' => '', 'suffix' => '', 'label' => 'CMS experts intentionally blank'],
                    ['value' => '21', 'suffix' => '+', 'label' => 'CMS years'],
                ],
            ],
        ]);

        $serviceTranslations = collect(['audit', 'racunovodstvo', 'advisory'])
            ->mapWithKeys(function (string $code): array {
                $page = ServicePage::query()->where('code', $code)->firstOrFail();
                $croatianTranslation = $page->translations()->where('locale', 'hr')->firstOrFail();
                $englishTranslation = $page->translations()->where('locale', 'en')->firstOrFail();
                $croatianPayload = ['cms_marker' => 'exact-'.$code];
                $englishPayload = [];
                $croatianTranslation->update(['payload' => $croatianPayload]);
                $englishTranslation->update(['payload' => $englishPayload]);

                return [
                    'translation-'.$croatianTranslation->id => $croatianPayload,
                    'translation-'.$englishTranslation->id => $englishPayload,
                ];
            });

        $aboutPage = InfoPage::query()->where('code', 'about-us')->firstOrFail();
        $aboutTranslation = $aboutPage->translations()->where('locale', 'hr')->firstOrFail();
        $aboutPayload = [
            'about_page' => [],
            'cms_marker' => 'about section intentionally blank',
        ];
        $aboutTranslation->update(['payload' => $aboutPayload]);

        $careerPage = InfoPage::query()->where('code', 'career')->firstOrFail();
        $careerTranslation = $careerPage->translations()->where('locale', 'en')->firstOrFail();
        $careerPayload = [
            'career_page' => [],
            'cms_marker' => 'career section intentionally blank',
        ];
        $careerTranslation->update(['payload' => $careerPayload]);

        $privacyPage = InfoPage::query()->where('code', 'privacy-policy')->firstOrFail();
        $privacyTranslation = $privacyPage->translations()->where('locale', 'hr')->firstOrFail();
        $privacyTranslation->update([
            'title' => '',
            'body_html' => '',
        ]);

        $teamPage = InfoPage::query()->where('code', 'team-page')->firstOrFail();
        $teamPage->forceFill([
            'layout' => 'cms-team-layout',
            'is_active' => false,
            'show_in_footer' => true,
            'published_at' => null,
            'sort_order' => 777,
            'payload' => ['cms_marker' => 'team base must remain authoritative'],
        ])->saveQuietly();
        $teamCroatianTranslation = $teamPage->translations()->where('locale', 'hr')->firstOrFail();
        $teamCroatianTranslation->update([
            'title' => '',
            'slug' => '',
            'excerpt' => '',
            'body_html' => '',
            'meta_title' => '',
            'meta_description' => '',
            'payload' => ['cms_marker' => 'blank HR team copy is intentional'],
        ]);
        $teamEnglishTranslation = $teamPage->translations()->where('locale', 'en')->firstOrFail();
        $teamEnglishTranslation->update([
            'title' => 'CMS exact team title',
            'slug' => 'cms-exact-team-slug',
            'excerpt' => 'CMS exact team intro',
            'body_html' => '<p>CMS exact team body</p>',
            'meta_title' => 'CMS exact team meta title',
            'meta_description' => 'CMS exact team meta description',
            'payload' => ['cms_marker' => 'custom EN team payload'],
        ]);
        $teamPageBefore = $teamPage->fresh()->only([
            'code',
            'layout',
            'is_active',
            'show_in_footer',
            'published_at',
            'sort_order',
            'payload',
            'created_by',
            'updated_by',
        ]);
        $teamCroatianTranslationBefore = $teamCroatianTranslation->fresh()->only([
            'title',
            'slug',
            'excerpt',
            'body_html',
            'meta_title',
            'meta_description',
            'payload',
        ]);
        $teamEnglishTranslationBefore = $teamEnglishTranslation->fresh()->only([
            'title',
            'slug',
            'excerpt',
            'body_html',
            'meta_title',
            'meta_description',
            'payload',
        ]);

        $ana = TeamMember::query()->where('code', 'ana-mandic')->firstOrFail();
        $anaTranslation = $ana->translations()->where('locale', 'hr')->firstOrFail();
        $ana->forceFill(['email' => '', 'linkedin_url' => ''])->saveQuietly();
        $anaTranslation->update([
            'name' => 'Ana Mandić, PhD, ACCA',
            'position' => 'Menadžer / Financijsko savjetovanje',
            'departments' => 'Financijsko savjetovanje',
            'description_html' => '',
        ]);

        DB::table('system_settings')->updateOrInsert(
            ['key' => 'front_navigation_main'],
            [
                'value' => json_encode([], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );
        $navigationValue = DB::table('system_settings')
            ->where('key', 'front_navigation_main')
            ->value('value');

        $auditPage = ServicePage::query()->where('code', 'audit')->firstOrFail();
        $servicesPage = ServicePage::query()->where('code', 'services')->firstOrFail();

        $careerPage->clearMediaCollection('career_hero_image');
        $careerPage->clearMediaCollection('career_gallery_images');
        $auditPage->clearMediaCollection('service_hero_image');
        $servicesPage->clearMediaCollection('services_index_audit_image');

        $careerPage->addMedia(UploadedFile::fake()->image('cms-career-hero.jpg', 1200, 900))
            ->withCustomProperties(['alt' => ['hr' => 'CMS HR hero', 'en' => 'CMS EN hero']])
            ->toMediaCollection('career_hero_image');
        $careerPage->addMedia(UploadedFile::fake()->image('cms-gallery-one.jpg', 800, 600))
            ->withCustomProperties(['alt' => ['hr' => 'CMS HR galerija', 'en' => 'CMS EN gallery']])
            ->toMediaCollection('career_gallery_images');
        $auditPage->addMedia(UploadedFile::fake()->image('cms-audit-hero.jpg', 1200, 900))
            ->withCustomProperties(['alt' => ['hr' => 'CMS HR audit', 'en' => 'CMS EN audit']])
            ->toMediaCollection('service_hero_image');
        $servicesPage->addMedia(UploadedFile::fake()->image('cms-services-audit.jpg', 1200, 900))
            ->withCustomProperties(['alt' => ['hr' => 'CMS HR card', 'en' => 'CMS EN card']])
            ->toMediaCollection('services_index_audit_image');

        $mediaBefore = $this->mediaSnapshot([
            [$careerPage, 'career_hero_image'],
            [$careerPage, 'career_gallery_images'],
            [$auditPage, 'service_hero_image'],
            [$servicesPage, 'services_index_audit_image'],
        ]);
        $cacheVersionBefore = (int) Cache::get(
            config('content_blocks.cache.version_key', 'content_blocks:version'),
            1,
        );

        foreach ([
            '2026_08_13_140000_refresh_audit_service_page_intro_copy.php',
            '2026_08_13_150000_refresh_accounting_service_page_intro_copy.php',
            '2026_08_13_153000_refresh_advisory_service_page_intro_copy.php',
            '2026_08_13_160000_add_about_hero_image_media.php',
            '2026_08_13_161000_add_career_hero_image_media.php',
            '2026_08_14_093500_add_home_to_front_navigation_settings.php',
            '2026_08_17_120000_create_legal_info_pages.php',
            '2026_08_21_080000_ensure_team_info_page.php',
            '2026_08_21_090000_apply_client_content_review.php',
            '2026_08_21_091000_add_career_gallery_images.php',
            '2026_08_21_092000_update_selected_career_and_audit_images.php',
            '2026_08_21_093000_reorder_career_gallery_images.php',
            '2026_08_21_094000_emphasize_about_story_copy.php',
            '2026_08_21_095000_add_ana_mandic_team_profile.php',
        ] as $migrationFile) {
            $migration = require database_path('migrations/'.$migrationFile);
            $migration->up();
        }

        $this->assertSame('', $heroTranslation->fresh()->title);
        $this->assertSame('', $heroTranslation->fresh()->subtitle);
        $this->assertSame('CMS exact hero title', $heroCroatianTranslation->fresh()->title);
        $this->assertSame('CMS exact hero subtitle', $heroCroatianTranslation->fresh()->subtitle);
        $this->assertSame($statsTranslation->payload, $statsTranslation->fresh()->payload);

        foreach ($serviceTranslations as $translationKey => $payload) {
            $translationId = (int) str_replace('translation-', '', (string) $translationKey);
            $this->assertSame(
                $payload,
                \App\Models\Content\Service\ServicePageTranslation::query()->findOrFail($translationId)->payload,
            );
        }

        $this->assertSame($aboutPayload, $aboutTranslation->fresh()->payload);
        $this->assertSame($careerPayload, $careerTranslation->fresh()->payload);
        $this->assertSame('', $privacyTranslation->fresh()->title);
        $this->assertSame('', $privacyTranslation->fresh()->body_html);
        $this->assertSame($teamPageBefore, $teamPage->fresh()->only(array_keys($teamPageBefore)));
        $this->assertSame(
            $teamCroatianTranslationBefore,
            $teamCroatianTranslation->fresh()->only(array_keys($teamCroatianTranslationBefore)),
        );
        $this->assertSame(
            $teamEnglishTranslationBefore,
            $teamEnglishTranslation->fresh()->only(array_keys($teamEnglishTranslationBefore)),
        );
        $this->assertSame('Ana Mandić, PhD, ACCA', $anaTranslation->fresh()->name);
        $this->assertSame('Menadžer / Financijsko savjetovanje', $anaTranslation->fresh()->position);
        $this->assertSame('Financijsko savjetovanje', $anaTranslation->fresh()->departments);
        $this->assertSame('', $anaTranslation->fresh()->description_html);
        $this->assertSame('', (string) $ana->fresh()->email);
        $this->assertSame('', (string) $ana->fresh()->linkedin_url);
        $this->assertSame(
            $navigationValue,
            DB::table('system_settings')->where('key', 'front_navigation_main')->value('value'),
        );
        $this->assertGreaterThan(
            $cacheVersionBefore,
            (int) Cache::get(config('content_blocks.cache.version_key', 'content_blocks:version'), 1),
        );
        $this->assertSame($mediaBefore, $this->mediaSnapshot([
            [$careerPage->fresh(), 'career_hero_image'],
            [$careerPage->fresh(), 'career_gallery_images'],
            [$auditPage->fresh(), 'service_hero_image'],
            [$servicesPage->fresh(), 'services_index_audit_image'],
        ]));
    }

    /**
     * @param  array<int, array{0:InfoPage|ServicePage,1:string}>  $collections
     * @return array<string, array<int, array<string, mixed>>>
     */
    private function mediaSnapshot(array $collections): array
    {
        return collect($collections)->mapWithKeys(function (array $entry): array {
            [$model, $collection] = $entry;

            return [
                $model::class.'#'.$model->getKey().':'.$collection => $model->getMedia($collection)
                    ->map(fn ($media): array => [
                        'id' => $media->id,
                        'file_name' => $media->file_name,
                        'order_column' => $media->order_column,
                        'custom_properties' => $media->custom_properties,
                    ])
                    ->values()
                    ->all(),
            ];
        })->all();
    }
}
