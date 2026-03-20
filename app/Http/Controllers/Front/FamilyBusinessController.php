<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Front\Concerns\ResolvesFrontendView;
use App\Models\Catalog\Category\Category;
use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Service\ServicePage;
use App\Models\Content\Service\ServicePageTranslation;
use App\Models\Content\Support\Faq;
use App\Models\Content\Team\TeamMember;
use App\Support\Content\ServicePageTemplateRegistry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FamilyBusinessController extends Controller
{
    use ResolvesFrontendView;

    public function show(Request $request): View
    {
        $locale = app()->getLocale();
        $fallbackLocale = (string) config('app.fallback_locale', config('app.locale', 'en'));

        [$servicePage, $servicePageTranslation] = $this->resolveServicePage($locale, $fallbackLocale);

        $pagePayload = ServicePageTemplateRegistry::mergePagePayload(
            ServicePageTemplateRegistry::FAMILY_BUSINESS,
            $servicePage?->payload
        );
        $translationPayload = ServicePageTemplateRegistry::mergeTranslationPayload(
            ServicePageTemplateRegistry::FAMILY_BUSINESS,
            $servicePageTranslation?->payload
        );

        $familyBusinessCategory = $this->resolveConfiguredBlogCategory(
            (array) ($pagePayload['blog_source'] ?? []),
            $locale,
            $fallbackLocale
        );
        $categoryTranslation = $familyBusinessCategory?->translations->firstWhere('locale', $locale)
            ?? $familyBusinessCategory?->translations->firstWhere('locale', $fallbackLocale)
            ?? $familyBusinessCategory?->translations->first();
        $categorySlug = trim((string) ($categoryTranslation?->slug ?? ''));
        $categoryName = trim((string) ($categoryTranslation?->name ?? '')) ?: 'Obiteljski biznis';

        $familyBusinessPosts = $this->resolveFamilyBusinessPosts(
            (array) ($pagePayload['blog_source'] ?? []),
            $familyBusinessCategory,
            $locale,
            $fallbackLocale
        );
        $familyBusinessFaqs = $this->resolveFamilyBusinessFaqs(
            (array) ($pagePayload['faq_source'] ?? []),
            $locale,
            $fallbackLocale
        );
        $familyBusinessTeam = $this->resolveFamilyBusinessTeam(
            (array) ($pagePayload['team_source'] ?? []),
            $locale,
            $fallbackLocale
        );

        $blogSection = (array) ($translationPayload['blog_section'] ?? []);
        $blogSection['title'] = str_replace(':category', $categoryName, (string) ($blogSection['title'] ?? ''));

        $ffiSection = (array) ($translationPayload['ffi'] ?? []);
        $ffiSection['logo_url'] = $this->resolveServiceLogoUrl($servicePage);

        return view($this->frontendView($request, 'pages.family-business'), [
            'familyBusinessPosts' => $familyBusinessPosts,
            'familyBusinessFaqs' => $familyBusinessFaqs,
            'familyBusinessTeam' => $familyBusinessTeam,
            'familyBusinessCategoryName' => $categoryName,
            'familyBusinessArchiveUrl' => $categorySlug !== ''
                ? url('/blog/'.$categorySlug)
                : route('blog.index'),
            'heroSection' => (array) ($translationPayload['hero'] ?? []),
            'audienceSection' => (array) ($translationPayload['audience'] ?? []),
            'ffiSection' => $ffiSection,
            'whatWeDoSection' => (array) ($translationPayload['what_we_do'] ?? []),
            'advisoryApproachSection' => (array) ($translationPayload['advisory'] ?? []),
            'capabilitySections' => (array) ($translationPayload['capabilities'] ?? []),
            'teamSection' => (array) ($translationPayload['team_section'] ?? []),
            'meetingSection' => (array) ($translationPayload['meeting'] ?? []),
            'blogSection' => $blogSection,
            'heroBackgroundUrl' => $this->resolveServiceHeroBackgroundUrl($servicePage),
            'brochureUrl' => $this->resolveBrochureUrl((array) $pagePayload),
            'servicePageTitle' => trim((string) ($servicePageTranslation?->title ?? '')) ?: 'Obiteljski biznis',
            'servicePageMetaTitle' => trim((string) ($servicePageTranslation?->meta_title ?? '')),
            'servicePageMetaDescription' => trim((string) ($servicePageTranslation?->meta_description ?? '')),
            'servicePageOgImage' => $this->resolveServiceHeroBackgroundUrl($servicePage),
            'locale' => $locale,
            'fallbackLocale' => $fallbackLocale,
        ]);
    }

    /**
     * @return array{0: ServicePage|null, 1: ServicePageTranslation|null}
     */
    private function resolveServicePage(string $locale, string $fallbackLocale): array
    {
        if (! Schema::hasTable('content_service_pages') || ! Schema::hasTable('content_service_page_translations')) {
            return [null, null];
        }

        $servicePage = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::FAMILY_BUSINESS)
            ->where('is_active', true)
            ->where(function (Builder $query): void {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->with([
                'translations' => fn ($query) => $query->whereIn('locale', [$locale, $fallbackLocale]),
                'media',
            ])
            ->orderByRaw('case when code = ? then 0 else 1 end', [ServicePageTemplateRegistry::defaultCode(ServicePageTemplateRegistry::FAMILY_BUSINESS)])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        if (! $servicePage) {
            return [null, null];
        }

        $translation = $servicePage->translations->firstWhere('locale', $locale)
            ?? $servicePage->translations->firstWhere('locale', $fallbackLocale)
            ?? $servicePage->translations->first();

        return [$servicePage, $translation];
    }

    private function resolveConfiguredBlogCategory(array $blogSource, string $locale, string $fallbackLocale): ?Category
    {
        $mode = (string) ($blogSource['mode'] ?? 'auto_category');
        $configuredCategoryId = (int) ($blogSource['category_id'] ?? 0);

        if ($mode === 'category' && $configuredCategoryId > 0) {
            $category = Category::query()
                ->where('scope', Category::SCOPE_BLOG)
                ->where('id', $configuredCategoryId)
                ->with([
                    'translations' => fn ($query) => $query
                        ->where('scope', Category::SCOPE_BLOG)
                        ->whereIn('locale', [$locale, $fallbackLocale]),
                ])
                ->first();

            if ($category) {
                return $category;
            }
        }

        return $this->resolveFamilyBusinessCategory($locale, $fallbackLocale);
    }

    private function resolveFamilyBusinessPosts(
        array $blogSource,
        ?Category $familyBusinessCategory,
        string $locale,
        string $fallbackLocale
    ): Collection {
        $mode = (string) ($blogSource['mode'] ?? 'auto_category');
        $limit = max(1, min(24, (int) ($blogSource['limit'] ?? 6)));

        $baseQuery = BlogPost::query()
            ->where('is_active', true)
            ->where(function (Builder $query): void {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
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
            ]);

        if ($mode === 'manual') {
            $postIds = collect((array) ($blogSource['post_ids'] ?? []))
                ->map(fn ($id): int => (int) $id)
                ->filter()
                ->values();

            if ($postIds->isEmpty()) {
                return collect();
            }

            $posts = (clone $baseQuery)
                ->whereIn('id', $postIds->all())
                ->get();

            $order = $postIds->flip();

            return $posts
                ->sortBy(fn (BlogPost $post): int => (int) ($order[$post->id] ?? 9999))
                ->values();
        }

        $resolvedCategoryId = $mode === 'category'
            ? (int) ($blogSource['category_id'] ?? 0)
            : (int) ($familyBusinessCategory?->id ?? 0);

        if ($resolvedCategoryId > 0) {
            $baseQuery->whereHas('categories', function (Builder $categoryQuery) use ($resolvedCategoryId): void {
                $categoryQuery->where('categories.id', $resolvedCategoryId);
            });
        }

        return $baseQuery
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    private function resolveFamilyBusinessFaqs(array $faqSource, string $locale, string $fallbackLocale): Collection
    {
        $mode = (string) ($faqSource['mode'] ?? 'auto_group');
        $baseQuery = Faq::query()
            ->where('is_active', true)
            ->with([
                'translations' => fn ($query) => $query->whereIn('locale', [$locale, $fallbackLocale]),
            ]);

        if ($mode === 'manual') {
            $faqIds = collect((array) ($faqSource['faq_ids'] ?? []))
                ->map(fn ($id): int => (int) $id)
                ->filter()
                ->values();

            if ($faqIds->isEmpty()) {
                return collect();
            }

            $faqs = (clone $baseQuery)
                ->whereIn('id', $faqIds->all())
                ->get();

            $order = $faqIds->flip();

            return $faqs
                ->sortBy(fn (Faq $faq): int => (int) ($order[$faq->id] ?? 9999))
                ->values();
        }

        $groupCode = $mode === 'group'
            ? trim((string) ($faqSource['group_code'] ?? ''))
            : ($this->resolveFamilyBusinessFaqGroupCode() ?? '');

        if ($groupCode !== '') {
            $baseQuery->where('group_code', $groupCode);
        }

        return $baseQuery
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    private function resolveFamilyBusinessTeam(array $teamSource, string $locale, string $fallbackLocale): Collection
    {
        $baseQuery = TeamMember::query()
            ->where('is_active', true)
            ->with(['translations', 'media'])
            ->orderBy('sort_order')
            ->orderBy('id');

        $mode = (string) ($teamSource['mode'] ?? 'auto');
        if ($mode === 'manual') {
            $memberIds = collect((array) ($teamSource['member_ids'] ?? []))
                ->map(fn ($id): int => (int) $id)
                ->filter()
                ->values();

            if ($memberIds->isEmpty()) {
                return collect();
            }

            $members = (clone $baseQuery)
                ->whereIn('id', $memberIds->all())
                ->get()
                ->map(fn (TeamMember $member): array => $this->mapTeamMember($member, $locale, $fallbackLocale));

            $order = $memberIds->flip();

            return $members
                ->sortBy(fn (array $member): int => (int) ($order[$member['id']] ?? 9999))
                ->values();
        }

        return (clone $baseQuery)
            ->get()
            ->map(fn (TeamMember $member): array => $this->mapTeamMember($member, $locale, $fallbackLocale))
            ->filter(fn (array $member): bool => $member['name'] !== '' && $this->memberBelongsToFamilyBusiness($member['departments']))
            ->values();
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
     * @param  array<int, string>  $slugCandidates
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

    private function resolveServiceHeroBackgroundUrl(?ServicePage $servicePage): string
    {
        $mediaUrl = $servicePage
            ? (string) ($servicePage->getFirstMediaUrl('service_hero_image', 'hero_1440x480') ?: $servicePage->getFirstMediaUrl('service_hero_image'))
            : '';

        if ($mediaUrl !== '') {
            return $mediaUrl;
        }

        return $this->versionedAsset('front-theme/images/services/family-business-editorial-3d.svg');
    }

    private function resolveServiceLogoUrl(?ServicePage $servicePage): string
    {
        $mediaUrl = $servicePage
            ? (string) ($servicePage->getFirstMediaUrl('service_logo', 'detail_960x960') ?: $servicePage->getFirstMediaUrl('service_logo'))
            : '';

        if ($mediaUrl !== '') {
            return $mediaUrl;
        }

        return $this->versionedAsset('front-theme/images/services/ffi-logo_40.webp');
    }

    private function resolveBrochureUrl(array $pagePayload): ?string
    {
        $configured = trim((string) ($pagePayload['brochure_url'] ?? ''));
        if ($configured !== '') {
            if (Str::startsWith($configured, ['http://', 'https://'])) {
                return $configured;
            }

            return Str::startsWith($configured, '/')
                ? url($configured)
                : asset($configured);
        }

        $relativePath = 'front-theme/documents/ALPHA_CAPITALIS_FAMILY_BUSINESS_ADVISORY_2025.pdf';
        $absolutePath = public_path($relativePath);

        return is_file($absolutePath)
            ? asset($relativePath).'?v='.filemtime($absolutePath)
            : null;
    }

    private function versionedAsset(string $relativePath): string
    {
        $absolutePath = public_path($relativePath);

        return is_file($absolutePath)
            ? asset($relativePath).'?v='.filemtime($absolutePath)
            : asset($relativePath);
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
