<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Front\Concerns\ResolvesFrontendView;
use App\Models\Content\Support\Faq;
use App\Services\Content\ContentBlockResolver;
use App\Support\Localization\FrontendLocalePolicy;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    use ResolvesFrontendView;

    public function index(Request $request): View
    {
        $locale = app()->getLocale();
        $fallbackLocale = FrontendLocalePolicy::fallbackLocale((string) $locale, (string) config('app.locale'));
        $requiresExactTranslation = FrontendLocalePolicy::requiresExactTranslation((string) $locale);

        $baseQuery = Faq::query()->where('is_active', true);
        if ($requiresExactTranslation) {
            $hasLocalizedFaq = (clone $baseQuery)
                ->whereHas('translations', fn ($query) => $query->where('locale', $locale))
                ->exists();

            abort_unless($hasLocalizedFaq, 404);
        }

        $variant = $this->frontendVariant($request);

        $faqs = $baseQuery
            ->when(
                $requiresExactTranslation,
                fn ($q) => $q->whereHas('translations', fn ($translationQuery) => $translationQuery->where('locale', $locale))
            )
            ->with(['translations' => fn ($q) => $q->whereIn('locale', [$locale, $fallbackLocale])])
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $topBlocks = app(ContentBlockResolver::class)->forPlacement(
            placement: 'faq.top',
            locale: $locale,
            targetType: 'faq',
            targetRef: null,
            frontendVariant: $variant
        );

        $bottomBlocks = app(ContentBlockResolver::class)->forPlacement(
            placement: 'faq.bottom',
            locale: $locale,
            targetType: 'faq',
            targetRef: null,
            frontendVariant: $variant
        );

        return view($this->frontendView($request, 'faq.index'), [
            'faqs' => $faqs,
            'topBlocks' => $topBlocks,
            'bottomBlocks' => $bottomBlocks,
            'locale' => $locale,
            'fallbackLocale' => $fallbackLocale,
        ]);
    }
}
