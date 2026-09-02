<?php

namespace Tests\Feature\Content;

use App\Models\Content\Service\ServicePage;
use App\Support\Content\EuFundsServicePageDefaults;
use App\Support\Content\ServicePageTemplateRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EuFunds2026ContentReviewMigrationFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_content_review_migration_is_idempotent_and_preserves_unrelated_editor_content(): void
    {
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::EU_FUNDS)
            ->where('code', ServicePageTemplateRegistry::defaultCode(ServicePageTemplateRegistry::EU_FUNDS))
            ->firstOrFail();
        $translation = $page->translations()->where('locale', 'hr')->firstOrFail();

        $unrelatedLawCard = [
            'key' => 'cms-custom-law',
            'title' => 'CMS zakon koji nije dio ove izmjene',
            'summary' => 'Ovaj sadržaj mora ostati netaknut.',
            'custom_editor_data' => ['owner' => 'uAdmin', 'version' => 7],
        ];
        $unrelatedResourceCard = [
            'key' => 'cms-custom-program',
            'title' => 'CMS program koji nije dio ove izmjene',
            'custom_editor_data' => ['owner' => 'uAdmin', 'version' => 3],
        ];
        $existingCallGroups = [
            [
                'title' => 'Statusi koje migracija ne smije promijeniti',
                'items' => [['title' => 'Postojeći poziv']],
            ],
        ];
        $payload = [
            'cms_top_level_marker' => [
                'owner' => 'uAdmin',
                'keep' => true,
            ],
            'hero' => [
                'title' => 'Postojeći CMS naslov izvan opsega migracije',
            ],
            'calls' => [
                'title' => 'Stari pozivi',
                'groups' => $existingCallGroups,
                'custom_editor_data' => ['keep' => true],
            ],
            'resources' => [
                'title' => 'Stari resursi',
                'custom_editor_data' => ['keep' => true],
                'cards' => [
                    ['title' => 'Projektni upitnik', 'body' => ['Stari tekst upitnika.']],
                    ['title' => 'HBOR krediti', 'body' => ['Stari tekst HBOR-a.']],
                    ['title' => 'HAMAG zajmovi', 'body' => ['Stari tekst HAMAG-a.']],
                    $unrelatedResourceCard,
                ],
            ],
            'laws' => [
                'kicker' => 'CMS KICKER',
                'title' => 'CMS naslov sekcije zakona',
                'intro' => 'CMS uvod sekcije zakona',
                'custom_section_data' => ['keep' => 'exactly'],
                'cards' => [
                    [
                        'key' => 'investment-promotion-act',
                        'title' => 'Zakon o poticanju ulaganja',
                        'summary' => 'Stara verzija ciljane kartice.',
                    ],
                    $unrelatedLawCard,
                ],
            ],
            'content_revisions' => [
                'unrelated_revision' => 'keep-me',
            ],
        ];
        $translation->update(['payload' => $payload]);
        $pagePayload = ['cms_page_marker' => 'untouched'];
        $page->update(['payload' => $pagePayload]);

        $migration = require database_path('migrations/2026_09_02_100000_apply_eu_funds_2026_content_review.php');
        $migration->up();

        $updatedPayload = $translation->fresh()->payload;
        $defaults = EuFundsServicePageDefaults::defaultsForLocale('hr');

        $this->assertSame($pagePayload, $page->fresh()->payload);
        $this->assertSame($payload['cms_top_level_marker'], $updatedPayload['cms_top_level_marker']);
        $this->assertSame($payload['hero'], $updatedPayload['hero']);
        $this->assertSame('keep-me', data_get($updatedPayload, 'content_revisions.unrelated_revision'));
        $this->assertSame('2026-09-02-eu-funds-content-v1', data_get($updatedPayload, 'content_revisions.eu_funds_2026'));

        $this->assertSame($existingCallGroups, data_get($updatedPayload, 'calls.groups'));
        $this->assertSame(['keep' => true], data_get($updatedPayload, 'calls.custom_editor_data'));
        $this->assertSame([5, 7, 20], collect(data_get($defaults, 'calls.groups', []))
            ->map(fn (array $group): int => count((array) ($group['items'] ?? [])))
            ->all());
        $this->assertCount(6, (array) data_get($updatedPayload, 'calls.other_calls.items', []));
        $this->assertSame(['keep' => true], data_get($updatedPayload, 'resources.custom_editor_data'));
        $this->assertCount(7, (array) data_get($updatedPayload, 'resources.cards', []));
        $this->assertSame($unrelatedResourceCard, data_get($updatedPayload, 'resources.cards.3'));
        $this->assertSame(
            collect(data_get($defaults, 'resources.cards', []))->pluck('key')->all(),
            collect(data_get($updatedPayload, 'resources.cards', []))->pluck('key')->filter()->reject(
                fn (string $key): bool => $key === 'cms-custom-program'
            )->values()->all(),
        );

        $this->assertSame('CMS KICKER', data_get($updatedPayload, 'laws.kicker'));
        $this->assertSame('CMS naslov sekcije zakona', data_get($updatedPayload, 'laws.title'));
        $this->assertSame('CMS uvod sekcije zakona', data_get($updatedPayload, 'laws.intro'));
        $this->assertSame(['keep' => 'exactly'], data_get($updatedPayload, 'laws.custom_section_data'));
        $this->assertSame($unrelatedLawCard, data_get($updatedPayload, 'laws.cards.1'));
        $this->assertSame(
            data_get($defaults, 'laws.cards.0'),
            data_get($updatedPayload, 'laws.cards.0'),
        );

        $migration->up();

        $this->assertSame($updatedPayload, $translation->fresh()->payload);
    }
}
