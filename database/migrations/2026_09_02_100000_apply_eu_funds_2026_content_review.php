<?php

use App\Support\Content\EuFundsServicePageDefaults;
use App\Support\Content\ServicePageTemplateRegistry;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const REVISION = '2026-09-02-eu-funds-content-v1';

    public function up(): void
    {
        if (! Schema::hasTable('content_service_pages') || ! Schema::hasTable('content_service_page_translations')) {
            return;
        }

        $servicePageId = DB::table('content_service_pages')
            ->where('template_key', ServicePageTemplateRegistry::EU_FUNDS)
            ->orderByRaw('case when code = ? then 0 else 1 end', [
                ServicePageTemplateRegistry::defaultCode(ServicePageTemplateRegistry::EU_FUNDS),
            ])
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

        if (data_get($payload, 'content_revisions.eu_funds_2026') === self::REVISION) {
            return;
        }

        $defaults = EuFundsServicePageDefaults::defaultsForLocale('hr');
        $payload['calls'] = $this->mergeApprovedFields(
            (array) ($payload['calls'] ?? []),
            (array) ($defaults['calls'] ?? []),
            ['kicker', 'title', 'intro', 'view_all_label', 'download_link', 'other_calls'],
        );
        $payload['resources'] = $this->mergeResourceCards(
            $this->mergeApprovedFields(
                (array) ($payload['resources'] ?? []),
                (array) ($defaults['resources'] ?? []),
                ['kicker', 'title', 'intro'],
            ),
            (array) ($defaults['resources'] ?? []),
        );
        $payload['laws'] = $this->mergeInvestmentPromotionCard(
            (array) ($payload['laws'] ?? []),
            (array) ($defaults['laws'] ?? []),
        );

        data_set($payload, 'content_revisions.eu_funds_2026', self::REVISION);

        DB::table('content_service_page_translations')
            ->where('id', $translation->id)
            ->update([
                'payload' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Client-approved public content is intentionally preserved.
    }

    /**
     * Update only the fields approved for this content revision. In particular,
     * calls.groups stays untouched because live call statuses belong to the
     * database-backed call categories and must not be rewritten here.
     *
     * @param  array<string, mixed>  $currentSection
     * @param  array<string, mixed>  $defaultSection
     * @param  array<int, string>  $fields
     * @return array<string, mixed>
     */
    private function mergeApprovedFields(array $currentSection, array $defaultSection, array $fields): array
    {
        foreach ($fields as $field) {
            if (array_key_exists($field, $defaultSection)) {
                $currentSection[$field] = $defaultSection[$field];
            }
        }

        return $currentSection;
    }

    /**
     * Replace the six reviewed program cards by key/title, append missing ones,
     * and preserve unrelated cards editors may have added in uAdmin.
     *
     * @param  array<string, mixed>  $currentSection
     * @param  array<string, mixed>  $defaultSection
     * @return array<string, mixed>
     */
    private function mergeResourceCards(array $currentSection, array $defaultSection): array
    {
        $cards = array_values((array) ($currentSection['cards'] ?? []));

        foreach ((array) ($defaultSection['cards'] ?? []) as $replacement) {
            if (! is_array($replacement)) {
                continue;
            }

            $replacementKey = trim((string) ($replacement['key'] ?? ''));
            $replacementTitle = trim((string) ($replacement['title'] ?? ''));
            $index = collect($cards)->search(function (mixed $card) use ($replacementKey, $replacementTitle): bool {
                if (! is_array($card)) {
                    return false;
                }

                $cardKey = trim((string) ($card['key'] ?? ''));
                $cardTitle = trim((string) ($card['title'] ?? ''));

                return ($replacementKey !== '' && $cardKey === $replacementKey)
                    || ($replacementTitle !== '' && $cardTitle === $replacementTitle);
            });

            if ($index === false) {
                $cards[] = $replacement;
            } else {
                $cards[(int) $index] = $replacement;
            }
        }

        $currentSection['cards'] = array_values($cards);

        return $currentSection;
    }

    /**
     * Replace only the requested investment-promotion card and preserve any
     * unrelated law cards that editors may already have adjusted in uAdmin.
     *
     * @param  array<string, mixed>  $currentSection
     * @param  array<string, mixed>  $defaultSection
     * @return array<string, mixed>
     */
    private function mergeInvestmentPromotionCard(array $currentSection, array $defaultSection): array
    {
        $replacement = collect((array) ($defaultSection['cards'] ?? []))
            ->first(fn (mixed $card): bool => is_array($card)
                && (($card['key'] ?? '') === 'investment-promotion-act'
                    || ($card['title'] ?? '') === 'Zakon o poticanju ulaganja'));

        if (! is_array($replacement)) {
            return $currentSection;
        }

        $cards = array_values((array) ($currentSection['cards'] ?? []));
        $index = collect($cards)->search(fn (mixed $card): bool => is_array($card)
            && (($card['key'] ?? '') === 'investment-promotion-act'
                || ($card['title'] ?? '') === 'Zakon o poticanju ulaganja'));

        if ($index === false) {
            array_unshift($cards, $replacement);
        } else {
            $cards[(int) $index] = $replacement;
        }

        $currentSection['cards'] = array_values($cards);

        return $currentSection;
    }
};
