<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Front\Concerns\ResolvesFrontendView;
use App\Models\Content\Career\JobOpening;
use App\Services\Front\NavigationMenuService;
use App\Support\Localization\FrontendLocalePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;

class CareerJobOpeningController extends Controller
{
    use ResolvesFrontendView;

    public function show(Request $request, string $slug): View
    {
        $locale = (string) app()->getLocale();
        $fallbackLocale = FrontendLocalePolicy::fallbackLocale(
            $locale,
            (string) config('app.fallback_locale', config('app.locale', 'hr')),
        );
        $jobOpening = JobOpening::query()
            ->published()
            ->whereHas('translations', function (Builder $query) use ($locale, $slug): void {
                $query
                    ->where('locale', $locale)
                    ->where('slug', $slug);
            })
            ->with(['translations' => fn ($query) => $query->where('locale', $locale)])
            ->firstOrFail();

        $translation = $jobOpening->translations->firstWhere('locale', $locale);

        abort_unless($translation, 404);

        $careerPageUrl = $this->careerPageUrl($locale);
        abort_if($careerPageUrl === '', 404);

        return view($this->frontendView($request, 'career.show'), [
            'jobOpening' => $jobOpening,
            'jobOpeningTranslation' => $translation,
            'locale' => $locale,
            'fallbackLocale' => $fallbackLocale,
            'careerPageUrl' => $careerPageUrl,
            'careerBackUrl' => $careerPageUrl.'#career-open-positions',
            'isAdminPreview' => false,
        ]);
    }

    public function preview(Request $request, JobOpening $jobOpening): View
    {
        $requestedLocale = strtolower(trim((string) $request->query('locale', '')));
        $locale = preg_match('/^[a-z]{2}(?:[-_][a-z]{2})?$/', $requestedLocale) === 1
            ? $requestedLocale
            : (string) app()->getLocale();
        $fallbackLocale = FrontendLocalePolicy::fallbackLocale(
            $locale,
            (string) config('app.fallback_locale', config('app.locale', 'hr')),
        );

        App::setLocale($locale);

        $jobOpening->load([
            'translations' => fn ($query) => $query->whereIn(
                'locale',
                array_values(array_unique([$locale, $fallbackLocale])),
            ),
        ]);

        $translation = $jobOpening->translations->firstWhere('locale', $locale);
        abort_unless($translation, 404);

        $careerPageUrl = $this->careerPageUrl($locale);
        $resolvedCareerPageUrl = $careerPageUrl !== '' ? $careerPageUrl : route('home');

        return view($this->frontendView($request, 'career.show'), [
            'jobOpening' => $jobOpening,
            'jobOpeningTranslation' => $translation,
            'locale' => $locale,
            'fallbackLocale' => $fallbackLocale,
            'careerPageUrl' => $resolvedCareerPageUrl,
            'careerBackUrl' => $careerPageUrl !== ''
                ? $careerPageUrl.'#career-open-positions'
                : $resolvedCareerPageUrl,
            'isAdminPreview' => true,
        ]);
    }

    private function careerPageUrl(string $locale): string
    {
        return app(NavigationMenuService::class)->infoPageUrlForLocale('career', $locale);
    }
}
