<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Front\Concerns\ResolvesFrontendView;
use App\Services\Front\SiteSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class SearchController extends Controller
{
    use ResolvesFrontendView;

    public function index(Request $request, SiteSearchService $siteSearch): View
    {
        $locale = (string) app()->getLocale();
        $fallbackLocale = (string) config('app.fallback_locale', config('app.locale'));
        $query = trim((string) $request->query('q', ''));
        $results = $siteSearch->search($query, $locale, $fallbackLocale);
        $sections = $this->presentSections($results);
        $totalResults = collect($sections)->sum('total_count');

        return view($this->frontendView($request, 'search.index'), [
            'searchQuery' => $query,
            'searchSections' => $sections,
            'searchTotalResults' => $totalResults,
        ]);
    }

    public function suggest(Request $request, SiteSearchService $siteSearch): JsonResponse
    {
        $locale = (string) app()->getLocale();
        $fallbackLocale = (string) config('app.fallback_locale', config('app.locale'));
        $query = trim((string) $request->query('q', ''));

        if (mb_strlen($query) < 2) {
            return response()->json([
                'query' => $query,
                'sections' => [],
                'total_count' => 0,
                'results_url' => route('search.index', ['q' => $query]),
            ]);
        }

        $results = $siteSearch->search($query, $locale, $fallbackLocale, 4);
        $sections = $this->presentSections($results);

        return response()->json([
            'query' => $query,
            'sections' => $sections,
            'total_count' => collect($sections)->sum('total_count'),
            'results_url' => route('search.index', ['q' => $query]),
        ]);
    }

    /**
     * @param  array<string, Collection<int, array<string, mixed>>>  $results
     * @return array<int, array<string, mixed>>
     */
    private function presentSections(array $results, ?int $limitPerSection = null): array
    {
        $sections = collect([
            'blog' => __('ui.search.sections.blog'),
            'services' => __('ui.search.sections.services'),
            'glossary' => __('ui.search.sections.glossary'),
        ])->map(function (string $label, string $key) use ($results, $limitPerSection): ?array {
            $items = ($results[$key] ?? collect());
            $items = $items instanceof Collection ? $items : collect($items);
            $totalCount = $items->count();

            if ($totalCount === 0) {
                return null;
            }

            $displayItems = $limitPerSection ? $items->take($limitPerSection)->values() : $items->values();

            return [
                'key' => $key,
                'label' => $label,
                'total_count' => $totalCount,
                'shown_count' => $displayItems->count(),
                'has_more' => $totalCount > $displayItems->count(),
                'items' => $displayItems->all(),
            ];
        })->filter()->values();

        return $sections->all();
    }
}
