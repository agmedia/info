<?php

use App\Models\Content\Page\InfoPage;
use App\Models\Content\Service\ServicePage;
use App\Models\Content\Team\TeamMember;
use App\Services\Content\ContentBlockResolver;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->updateHomepage();
        $this->updateAboutPage();
        $this->updateCareerPage();
        $this->updateTeam();
        $this->replaceEditorialImages();
        ContentBlockResolver::bumpCacheVersion();
    }

    public function down(): void
    {
        // Editorial copy and media are intentionally preserved on rollback.
    }

    private function updateHomepage(): void
    {
        if (! Schema::hasTable('content_blocks') || ! Schema::hasTable('content_block_translations')) {
            return;
        }

        $heroId = DB::table('content_blocks')->where('code', 'home-alpha-hero')->value('id');

        if ($heroId) {
            foreach (['hr', 'en'] as $locale) {
                $translation = DB::table('content_block_translations')
                    ->where('content_block_id', $heroId)
                    ->where('locale', $locale)
                    ->first(['id', 'title', 'subtitle']);

                if ($translation) {
                    continue;
                }

                DB::table('content_block_translations')->insert([
                    'content_block_id' => $heroId,
                    'locale' => $locale,
                    'title' => $locale === 'hr'
                        ? 'Vaš kompas kroz svijet financija'
                        : 'Your compass through the world of finance',
                    'subtitle' => $locale === 'hr'
                        ? 'Računovodstvo, revizija i savjetovanje — sve na jednom mjestu.'
                        : 'Accounting, audit and advisory — all in one place.',
                    'body_html' => null,
                    'cta_label' => null,
                    'cta_url' => null,
                    'payload' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function updateAboutPage(): void
    {
        if (! Schema::hasTable('content_info_pages') || ! Schema::hasTable('content_info_page_translations')) {
            return;
        }

        $pageId = DB::table('content_info_pages')
            ->where(function ($query): void {
                $query->where('code', 'about-us')->orWhere('layout', 'about');
            })
            ->orderByRaw("CASE WHEN code = 'about-us' THEN 0 ELSE 1 END")
            ->value('id');

        if (! $pageId) {
            return;
        }

        // Existing translation rows, including empty sections, remain exactly as
        // editors saved them. This review migration no longer synthesizes copy.
    }

    private function updateCareerPage(): void
    {
        if (! Schema::hasTable('content_info_pages') || ! Schema::hasTable('content_info_page_translations')) {
            return;
        }

        $pageId = DB::table('content_info_pages')
            ->where(function ($query): void {
                $query->where('code', 'career')->orWhere('layout', 'career');
            })
            ->orderByRaw("CASE WHEN code = 'career' THEN 0 ELSE 1 END")
            ->value('id');

        if (! $pageId) {
            return;
        }

        // Existing translation rows, including empty sections, remain exactly as
        // editors saved them. This review migration no longer synthesizes copy.
    }

    private function updateTeam(): void
    {
        if (! Schema::hasTable('content_team_members') || ! Schema::hasTable('content_team_member_translations')) {
            return;
        }

        $anaId = DB::table('content_team_members')->where('code', 'ana-mandic')->value('id')
            ?: DB::table('content_team_member_translations')
                ->whereRaw('LOWER(name) LIKE ?', ['%ana mandi%'])
                ->value('team_member_id');
        if (! $anaId) {
            DB::table('content_team_members')->where('sort_order', '>=', 7)->increment('sort_order');

            $anaId = DB::table('content_team_members')->insertGetId([
                'code' => 'ana-mandic',
                'is_active' => true,
                'sort_order' => 7,
                'email' => 'ana.mandic@alphacapitalis.com',
                'linkedin_url' => 'https://www.linkedin.com/in/ana-mandic-phd-aa572b44',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $anaTranslation = DB::table('content_team_member_translations')
            ->where('team_member_id', $anaId)
            ->where('locale', 'hr');
        $anaTranslationData = [
            'name' => 'Ana Mandić',
            'position' => 'Menadžer / Savjetovanje',
            'departments' => 'Savjetovanje',
            'description_html' => $this->anaMandicBiography(),
            'updated_at' => now(),
        ];

        if (! $anaTranslation->exists()) {
            DB::table('content_team_member_translations')->insert($anaTranslationData + [
                'team_member_id' => $anaId,
                'locale' => 'hr',
                'created_at' => now(),
            ]);
        }

        $anaPhotoPath = public_path('front-theme/images/team/ana-mandic.png');
        $ana = TeamMember::query()->find($anaId);

        if (
            Schema::hasTable('media')
            && $ana
            && is_file($anaPhotoPath)
            && ! $ana->getFirstMedia('team_photo')
        ) {
            $ana->addMedia($anaPhotoPath)
                ->preservingOriginal()
                ->usingName('Ana Mandić')
                ->usingFileName(basename($anaPhotoPath))
                ->withCustomProperties([
                    'alt' => [
                        'hr' => 'Ana Mandić',
                        'en' => 'Ana Mandić',
                    ],
                ])
                ->toMediaCollection('team_photo');
        }
    }

    private function replaceEditorialImages(): void
    {
        if (! Schema::hasTable('media')) {
            return;
        }

        if (Schema::hasTable('content_info_pages')) {
            $careerPage = InfoPage::query()
                ->where(function ($query): void {
                    $query->where('code', 'career')->orWhere('layout', 'career');
                })
                ->orderByRaw("CASE WHEN code = 'career' THEN 0 ELSE 1 END")
                ->first();

            $this->replaceImage(
                $careerPage,
                public_path('front-theme/images/career/career-team-building.jpg'),
                'career_hero_image',
                'ALPHA CAPITALIS tim na team buildingu',
                'ALPHA CAPITALIS team at a team-building gathering',
            );
        }

        if (! Schema::hasTable('content_service_pages')) {
            return;
        }

        $auditImagePath = public_path('front-theme/images/services/audit-client-meeting.jpg');
        $auditPage = ServicePage::query()
            ->where(fn ($query) => $query->where('code', 'audit')->orWhere('template_key', 'audit'))
            ->orderByRaw("CASE WHEN code = 'audit' THEN 0 ELSE 1 END")
            ->first();
        $servicesPage = ServicePage::query()
            ->where(fn ($query) => $query->where('code', 'services')->orWhere('template_key', 'services_index'))
            ->orderByRaw("CASE WHEN code = 'services' THEN 0 ELSE 1 END")
            ->first();

        $this->replaceImage(
            $auditPage,
            $auditImagePath,
            'service_hero_image',
            'Predaja ALPHA CAPITALIS vizitke na poslovnom sastanku',
            'An ALPHA CAPITALIS business card being handed over at a client meeting',
        );
        $this->replaceImage(
            $servicesPage,
            $auditImagePath,
            'services_index_audit_image',
            'Predaja ALPHA CAPITALIS vizitke na poslovnom sastanku',
            'An ALPHA CAPITALIS business card being handed over at a client meeting',
        );
    }

    private function replaceImage(
        InfoPage|ServicePage|null $model,
        string $path,
        string $collection,
        string $croatianAlt,
        string $englishAlt,
    ): void {
        if (! $model || ! is_file($path) || $model->getFirstMedia($collection)) {
            return;
        }

        $model->addMedia($path)
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

    private function anaMandicBiography(): string
    {
        return <<<'HTML'
<p>Ana posjeduje ACCA kvalifikaciju i doktorat znanosti s Ekonomskog fakulteta u Zagrebu.</p>
<p>Ima više od dvanaest godina iskustva u globalnoj konzultantskoj tvrtki, radeći na projektima savjetovanja pri transakcijama, uključujući:</p>
<ul>
<li><strong>Transakcijske usluge:</strong> vođenje financijskih due diligence analiza u različitim industrijama za strateške i financijske investitore, pružanje podrške klijentima u HR due diligence angažmanima te izrada planova sinergije i planova razdvajanja poslovanja.</li>
<li><strong>Upravljanje financijskim i računovodstvenim aspektima transakcija:</strong> strukturiranje transakcije i priprema dokumentacije, uključujući SPA i ostale transakcijske dokumente.</li>
<li><strong>Procjene vrijednosti:</strong> iskustvo u procjenama vrijednosti primjenom prihodovne, tržišne i likvidacijske metode.</li>
<li><strong>Savjetovanje pri zaduživanju:</strong> pružanje sveobuhvatne podrške klijentima pri pribavljanju i strukturiranju financiranja transakcija.</li>
<li><strong>Spajanja i preuzimanja (M&amp;A):</strong> podrška kupcima i prodavateljima tijekom procesa spajanja i preuzimanja.</li>
<li><strong>Forenzika:</strong> vođenje dijela istrage u okviru forenzične računovodstvene istrage.</li>
<li><strong>Financijske usluge:</strong> vođenje due diligence procesa portfelja nenaplativih potraživanja.</li>
</ul>
HTML;
    }
};
