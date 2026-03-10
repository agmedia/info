<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Front\Concerns\ResolvesFrontendView;
use App\Models\Catalog\Category\Category;
use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Support\Faq;
use App\Models\Content\Team\TeamMember;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FamilyBusinessController extends Controller
{
    use ResolvesFrontendView;

    public function show(Request $request): View
    {
        $locale = app()->getLocale();
        $fallbackLocale = (string) config('app.fallback_locale', config('app.locale', 'en'));

        $familyBusinessCategory = $this->resolveFamilyBusinessCategory($locale, $fallbackLocale);
        $categoryTranslation = $familyBusinessCategory?->translations->firstWhere('locale', $locale)
            ?? $familyBusinessCategory?->translations->firstWhere('locale', $fallbackLocale)
            ?? $familyBusinessCategory?->translations->first();
        $categorySlug = trim((string) ($categoryTranslation?->slug ?? ''));
        $categoryName = trim((string) ($categoryTranslation?->name ?? '')) ?: 'Obiteljski biznis';

        $familyBusinessPosts = BlogPost::query()
            ->where('is_active', true)
            ->where(function (Builder $query): void {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->when($familyBusinessCategory, function (Builder $query) use ($familyBusinessCategory): void {
                $query->whereHas('categories', function (Builder $categoryQuery) use ($familyBusinessCategory): void {
                    $categoryQuery->where('categories.id', $familyBusinessCategory->id);
                });
            })
            ->with([
                'translations' => fn ($query) => $query->whereIn('locale', [$locale, $fallbackLocale]),
                'categories' => fn ($query) => $query
                    ->where('scope', Category::SCOPE_BLOG)
                    ->with([
                        'translations' => fn ($translationQuery) => $translationQuery
                            ->where('scope', Category::SCOPE_BLOG)
                            ->whereIn('locale', [$locale, $fallbackLocale]),
                    ]),
                'media',
            ])
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        $familyBusinessFaqGroupCode = $this->resolveFamilyBusinessFaqGroupCode();
        $familyBusinessFaqs = Faq::query()
            ->where('is_active', true)
            ->when($familyBusinessFaqGroupCode !== null, fn (Builder $query) => $query->where('group_code', $familyBusinessFaqGroupCode))
            ->with([
                'translations' => fn ($query) => $query->whereIn('locale', [$locale, $fallbackLocale]),
            ])
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $familyBusinessTeam = TeamMember::query()
            ->where('is_active', true)
            ->with(['translations', 'media'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (TeamMember $member): array => $this->mapTeamMember($member, $locale, $fallbackLocale))
            ->filter(fn (array $member): bool => $member['name'] !== '' && $this->memberBelongsToFamilyBusiness($member['departments']))
            ->values();

        return view($this->frontendView($request, 'pages.family-business'), [
            'familyBusinessPosts' => $familyBusinessPosts,
            'familyBusinessFaqs' => $familyBusinessFaqs,
            'familyBusinessTeam' => $familyBusinessTeam,
            'familyBusinessCategoryName' => $categoryName,
            'familyBusinessArchiveUrl' => $categorySlug !== ''
                ? url('/blog/'.$categorySlug)
                : route('blog.index'),
            'locale' => $locale,
            'fallbackLocale' => $fallbackLocale,
        ]);
    }

    private function resolveFamilyBusinessFaqGroupCode(): ?string
    {
        $groupCandidates = Faq::query()
            ->where('is_active', true)
            ->distinct()
            ->pluck('group_code')
            ->filter(fn ($group): bool => trim((string) $group) !== '')
            ->values();

        $preferredCodes = [
            'obiteljski-biznis',
            'obiteljski_biznis',
            'obiteljski-business',
            'family-business',
            'family_business',
            'familybusiness',
        ];

        $bestMatch = null;
        $bestScore = 100;

        foreach ($groupCandidates as $groupCode) {
            $rawCode = trim((string) $groupCode);
            $normalizedCode = str($rawCode)->lower()->ascii()->replace('_', '-')->value();

            if (in_array($normalizedCode, $preferredCodes, true)) {
                return $rawCode;
            }

            $score = 100;

            if (str_contains($normalizedCode, 'obitelj') && (str_contains($normalizedCode, 'biznis') || str_contains($normalizedCode, 'business'))) {
                $score = 8;
            } elseif (str_contains($normalizedCode, 'family') && str_contains($normalizedCode, 'business')) {
                $score = 12;
            } elseif (str_contains($normalizedCode, 'nasljed') && (str_contains($normalizedCode, 'obitelj') || str_contains($normalizedCode, 'family'))) {
                $score = 18;
            }

            if ($score < $bestScore) {
                $bestScore = $score;
                $bestMatch = $rawCode;
            }
        }

        return $bestMatch;
    }

    /**
     * @return array<string, mixed>
     */
    private function mapTeamMember(TeamMember $member, string $locale, string $fallbackLocale): array
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

        $photoUrl = (string) ($member->getFirstMediaUrl('team_photo', 'detail_960x960')
            ?: $member->getFirstMediaUrl('team_photo'));
        $descriptionHtml = (string) ($translation?->description_html ?? '');
        $descriptionText = trim((string) preg_replace('/\s+/u', ' ', strip_tags($descriptionHtml)));
        $descriptionExcerpt = (string) Str::limit($descriptionText, 320, '...');

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
            'initials' => $this->initialsForName($name !== '' ? $name : (string) $member->code),
        ];
    }

    /**
     * @param  array<int, string>  $departments
     */
    private function memberBelongsToFamilyBusiness(array $departments): bool
    {
        foreach ($departments as $department) {
            $normalized = str($department)
                ->lower()
                ->ascii()
                ->replace('_', '-')
                ->replace(' ', '-')
                ->value();

            if (in_array($normalized, [
                'obiteljski-biznis',
                'obiteljski-business',
                'obiteljski-biznisi',
                'family-business',
                'family-businesses',
            ], true)) {
                return true;
            }

            if (str_contains($normalized, 'obitelj') && (str_contains($normalized, 'biznis') || str_contains($normalized, 'business'))) {
                return true;
            }

            if (str_contains($normalized, 'family') && str_contains($normalized, 'business')) {
                return true;
            }
        }

        return false;
    }

    private function resolveFamilyBusinessCategory(string $locale, string $fallbackLocale): ?Category
    {
        $slugCandidates = [
            'obiteljski-biznis',
            'obiteljski-business',
            'obiteljski-businessi',
            'obiteljski-biznisi',
            'family-business',
            'family-businesses',
        ];

        $categories = Category::query()
            ->where('scope', Category::SCOPE_BLOG)
            ->where('is_active', true)
            ->with([
                'translations' => fn ($query) => $query
                    ->where('scope', Category::SCOPE_BLOG)
                    ->whereIn('locale', [$locale, $fallbackLocale]),
            ])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return $categories
            ->map(fn (Category $category): array => [
                'category' => $category,
                'score' => $this->familyBusinessCategoryScore($category, $locale, $fallbackLocale, $slugCandidates),
            ])
            ->filter(fn (array $item): bool => $item['score'] < 100)
            ->sortBy([
                ['score', 'asc'],
                [fn (array $item): int => (int) $item['category']->sort_order, 'asc'],
                [fn (array $item): int => (int) $item['category']->id, 'asc'],
            ])
            ->pluck('category')
            ->first();
    }

    /**
     * Lower score means a stronger family-business category match.
     *
     * @param array<int, string> $slugCandidates
     */
    private function familyBusinessCategoryScore(
        Category $category,
        string $locale,
        string $fallbackLocale,
        array $slugCandidates
    ): int {
        $bestScore = 100;

        foreach ($category->translations as $translation) {
            $localeBonus = match ((string) $translation->locale) {
                $locale => 0,
                $fallbackLocale => 4,
                default => 8,
            };

            $slug = str((string) ($translation->slug ?? ''))->lower()->ascii()->value();
            $name = str((string) ($translation->name ?? ''))->lower()->ascii()->value();
            $haystack = trim($slug.' '.$name.' '.strtolower((string) $category->code));

            if (in_array($slug, $slugCandidates, true)) {
                return 0 + $localeBonus;
            }

            if (str_contains($slug, 'obitelj') && (str_contains($slug, 'biznis') || str_contains($slug, 'business'))) {
                $bestScore = min($bestScore, 8 + $localeBonus);
            }

            if (str_contains($name, 'obitelj') && (str_contains($name, 'biznis') || str_contains($name, 'business'))) {
                $bestScore = min($bestScore, 10 + $localeBonus);
            }

            if (str_contains($haystack, 'family') && str_contains($haystack, 'business')) {
                $bestScore = min($bestScore, 14 + $localeBonus);
            }

            if ((str_contains($haystack, 'nasljed') || str_contains($haystack, 'succession'))
                && (str_contains($haystack, 'obitelj') || str_contains($haystack, 'family'))
            ) {
                $bestScore = min($bestScore, 18 + $localeBonus);
            }
        }

        return $bestScore;
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
