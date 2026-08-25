<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Front\Concerns\ResolvesFrontendView;
use App\Models\Content\Page\InfoPage;
use App\Models\Content\Team\TeamMember;
use App\Support\Localization\FrontendLocalePolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TeamController extends Controller
{
    use ResolvesFrontendView;

    public function index(Request $request): View
    {
        $locale = app()->getLocale();
        $fallbackLocale = FrontendLocalePolicy::fallbackLocale(
            (string) $locale,
            (string) config('app.fallback_locale', config('app.locale', 'en'))
        );
        $teamPage = InfoPage::query()
            ->where('code', 'team-page')
            ->where('is_active', true)
            ->where(function ($query): void {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->with(['translations' => fn ($query) => $query->when(
                FrontendLocalePolicy::requiresExactTranslation((string) $locale),
                fn ($translationQuery) => $translationQuery->whereIn('locale', [$locale, $fallbackLocale])
            )])
            ->first();
        $teamPageTranslation = $teamPage?->translations->firstWhere('locale', $locale)
            ?? $teamPage?->translations->firstWhere('locale', $fallbackLocale)
            ?? $teamPage?->translations->first();
        abort_if(
            FrontendLocalePolicy::requiresExactTranslation((string) $locale) && ! $teamPageTranslation,
            404
        );
        $teamIntro = trim((string) ($teamPageTranslation?->excerpt ?? ''))
            ?: (string) __('ui.team.subtitle');

        $members = TeamMember::query()
            ->where('is_active', true)
            ->when(
                FrontendLocalePolicy::requiresExactTranslation((string) $locale),
                fn ($query) => $query->whereHas('translations', fn ($translationQuery) => $translationQuery->where('locale', $locale))
            )
            ->with([
                'translations' => fn ($query) => $query->when(
                    FrontendLocalePolicy::requiresExactTranslation((string) $locale),
                    fn ($translationQuery) => $translationQuery->whereIn('locale', [$locale, $fallbackLocale])
                ),
                'media',
            ])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (TeamMember $member): array => $this->mapMember($member, $locale, $fallbackLocale))
            ->filter(fn (array $member): bool => $member['name'] !== '')
            ->values();

        return view($this->frontendView($request, 'team.index'), [
            'members' => $members,
            'locale' => $locale,
            'fallbackLocale' => $fallbackLocale,
            'teamPage' => $teamPage,
            'teamPageTranslation' => $teamPageTranslation,
            'teamIntro' => $teamIntro,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function mapMember(TeamMember $member, string $locale, string $fallbackLocale): array
    {
        $translation = $member->translations->firstWhere('locale', $locale)
            ?? $member->translations->firstWhere('locale', $fallbackLocale)
            ?? $member->translations->first();

        $name = trim((string) ($translation?->name ?? ''));
        $position = trim((string) ($translation?->position ?? ''));
        $departments = collect(preg_split('/[\r\n,]+/u', (string) ($translation?->departments ?? '')) ?: [])
            ->map(static fn (string $department): string => trim($department))
            ->filter()
            ->values()
            ->all();

        $photoUrl = (string) ($member->getFirstMediaUrl('team_photo')
            ?: $member->getFirstMediaUrl('team_photo', 'detail_960x960'));
        $descriptionHtml = (string) ($translation?->description_html ?? '');
        $descriptionText = trim((string) preg_replace('/\s+/u', ' ', strip_tags($descriptionHtml)));
        $descriptionExcerpt = (string) Str::limit($descriptionText, 320, '...');

        $initials = $this->initialsForName($name !== '' ? $name : (string) $member->code);

        return [
            'id' => (int) $member->id,
            'name' => $name,
            'position' => $position,
            'departments' => $departments,
            'description_html' => $descriptionHtml,
            'description_excerpt' => $descriptionExcerpt,
            'has_long_description' => $descriptionText !== '' && $descriptionExcerpt !== $descriptionText,
            'email' => trim((string) ($member->email ?? '')),
            'mobile_phone' => trim((string) ($member->mobile_phone ?? '')),
            'facebook_url' => trim((string) ($member->facebook_url ?? '')),
            'twitter_url' => trim((string) ($member->twitter_url ?? '')),
            'linkedin_url' => trim((string) ($member->linkedin_url ?? '')),
            'photo_url' => $photoUrl,
            'initials' => $initials,
        ];
    }

    private function initialsForName(string $value): string
    {
        $parts = collect(preg_split('/\s+/u', trim($value)) ?: [])
            ->filter()
            ->take(2)
            ->map(static fn (string $part): string => Str::upper(Str::substr($part, 0, 1)))
            ->implode('');

        return $parts !== '' ? $parts : 'AC';
    }
}
