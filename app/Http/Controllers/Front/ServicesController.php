<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Front\Concerns\ResolvesFrontendView;
use App\Services\Front\ServiceCardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServicesController extends Controller
{
    use ResolvesFrontendView;

    public function __construct(
        private readonly ServiceCardService $serviceCardService
    ) {
    }

    public function index(Request $request): View
    {
        $locale = app()->getLocale();
        $fallbackLocale = (string) config('app.fallback_locale', config('app.locale', 'en'));

        return view($this->frontendView($request, 'pages.services'), [
            'serviceCards' => $this->serviceCardService->cards((string) $locale, $fallbackLocale),
            'primaryServicePillars' => $this->serviceCardService->primaryPillars((string) $locale, $fallbackLocale),
            'servicePageTitle' => 'Usluge',
            'servicePageMetaTitle' => 'Usluge | Alpha Capitalis',
            'servicePageMetaDescription' => 'Pregled usluga ALPHA CAPITALISA: revizija, racunovodstvo i poslovno savjetovanje.',
            'locale' => $locale,
            'fallbackLocale' => $fallbackLocale,
        ]);
    }
}
