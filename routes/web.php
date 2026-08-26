<?php

use App\Http\Controllers\Admin\AdminAiController;
use App\Http\Controllers\Admin\BlogEditorImageController;
use App\Http\Controllers\Admin\CallEditorImageController;
use App\Http\Controllers\Admin\CareerApplicationDocumentController;
use App\Http\Controllers\Admin\SystemToolsController;
use App\Http\Controllers\Front\AccountingController;
use App\Http\Controllers\Front\AdvisoryController;
use App\Http\Controllers\Front\AuditController;
use App\Http\Controllers\Front\BlogController;
use App\Http\Controllers\Front\CallPostController;
use App\Http\Controllers\Front\CareerApplicationController;
use App\Http\Controllers\Front\CollaborationAssessmentController;
use App\Http\Controllers\Front\ContactController;
use App\Http\Controllers\Front\EuFundsController;
use App\Http\Controllers\Front\EuFundsQuestionnaireController;
use App\Http\Controllers\Front\FaqController;
use App\Http\Controllers\Front\GlossaryController;
use App\Http\Controllers\Front\LeaseCalculatorController;
use App\Http\Controllers\Front\NewsletterCsrfTokenController;
use App\Http\Controllers\Front\NewsletterSubscriptionController;
use App\Http\Controllers\Front\PageController;
use App\Http\Controllers\Front\PublicStorageController;
use App\Http\Controllers\Front\ResourceController;
use App\Http\Controllers\Front\SearchController;
use App\Http\Controllers\Front\ServicesController;
use App\Http\Controllers\Front\SitemapController;
use App\Http\Controllers\Front\StorefrontController;
use App\Http\Controllers\Front\TeamController;
use App\Http\Middleware\EnsureFrontendRouteLocale;
use App\Http\Middleware\InferFrontendLocaleFromInfoPageSlug;
use App\Models\Catalog\Category\Category;
use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Call\CallPost;
use App\Models\Content\ContentBlock;
use App\Models\Content\ContentBlockSlot;
use App\Models\Content\Glossary\GlossaryTerm;
use App\Models\Content\Page\InfoPage;
use App\Models\Content\Resource\ResourceDocument;
use App\Models\Content\Service\ServicePage;
use App\Models\Content\Support\Faq;
use App\Models\Content\Team\TeamMember;
use App\Models\Settings\Local\Language;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

Route::get('sitemap.xml', SitemapController::class)
    ->withoutMiddleware([
        StartSession::class,
        ShareErrorsFromSession::class,
        ValidateCsrfToken::class,
    ])
    ->name('sitemap');

Route::get('storage/{path}', PublicStorageController::class)
    ->where('path', '.*')
    ->name('public-storage.show');

Route::get('newsletter/csrf-token', NewsletterCsrfTokenController::class)
    ->middleware('throttle:30,1')
    ->name('newsletter.csrf-token');

Route::middleware(['front.locale', 'front.device'])
    ->group(function (): void {
        Route::get('locale/{code}', function (string $code, Request $request) {
            $fallback = strtolower((string) config('app.locale', 'en'));
            $target = strtolower(trim($code));

            try {
                $available = Language::query()
                    ->where('is_active', true)
                    ->pluck('code')
                    ->map(static fn ($item) => strtolower((string) $item))
                    ->values()
                    ->all();
            } catch (\Throwable) {
                $available = [$fallback];
            }

            if (! in_array($target, $available, true)) {
                $target = in_array($fallback, $available, true)
                    ? $fallback
                    : (string) ($available[0] ?? $fallback);
            }

            $request->session()->put('front_locale', $target);

            $redirectTarget = trim((string) $request->query('redirect', ''));
            if ($redirectTarget !== '') {
                $parts = parse_url($redirectTarget);
                $requestHost = strtolower($request->getHost());
                $applicationHost = strtolower((string) parse_url((string) config('app.url'), PHP_URL_HOST));
                $redirectHost = is_array($parts) ? strtolower(trim((string) ($parts['host'] ?? ''))) : '';
                $redirectScheme = is_array($parts) ? strtolower(trim((string) ($parts['scheme'] ?? ''))) : '';
                $isSafeRelativeTarget = str_starts_with($redirectTarget, '/')
                    && ! str_starts_with($redirectTarget, '//')
                    && ! str_contains($redirectTarget, '\\');
                $isSafeAbsoluteTarget = in_array($redirectScheme, ['http', 'https'], true)
                    && $redirectHost !== ''
                    && in_array($redirectHost, array_filter([$requestHost, $applicationHost]), true)
                    && ! isset($parts['user'])
                    && ! isset($parts['pass']);

                if ($isSafeRelativeTarget || $isSafeAbsoluteTarget) {
                    return redirect()->to($redirectTarget);
                }
            }

            return redirect()->to(route('home'));
        })->name('front.locale.switch');

        Route::get('/', [StorefrontController::class, 'home'])->name('home');

        Route::get('blog', [BlogController::class, 'index'])->name('blog.index');
        Route::get('blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
        Route::get('{year}/{month}/{day}/{slug}', [BlogController::class, 'legacy'])
            ->whereNumber('year')
            ->whereNumber('month')
            ->whereNumber('day')
            ->name('blog.legacy');
        Route::get('faq', [FaqController::class, 'index'])->name('faq.index');
        Route::get('alpha-capitalis-tim', [TeamController::class, 'index'])
            ->middleware(EnsureFrontendRouteLocale::class.':hr')
            ->name('team.index');
        Route::get('alpha-capitalis-team', [TeamController::class, 'index'])
            ->middleware(EnsureFrontendRouteLocale::class.':en')
            ->name('team.index.en');
        Route::get('usluge', [ServicesController::class, 'index'])
            ->middleware(EnsureFrontendRouteLocale::class.':hr')
            ->name('services.index');
        Route::get('services', [ServicesController::class, 'index'])
            ->middleware(EnsureFrontendRouteLocale::class.':en')
            ->name('services.index.en');
        Route::get('savjetovanje', [AdvisoryController::class, 'show'])
            ->middleware(EnsureFrontendRouteLocale::class.':hr')
            ->name('advisory.show');
        Route::get('advisory', [AdvisoryController::class, 'show'])
            ->middleware(EnsureFrontendRouteLocale::class.':en')
            ->name('advisory.show.en');
        Route::get('savjetovanje/financijsko-savjetovanje', [AdvisoryController::class, 'financial'])
            ->middleware(EnsureFrontendRouteLocale::class.':hr')
            ->name('advisory.finance.show');
        Route::get('advisory/financial-advisory', [AdvisoryController::class, 'financial'])
            ->middleware(EnsureFrontendRouteLocale::class.':en')
            ->name('advisory.finance.show.en');
        Route::get('savjetovanje/prodaja-i-kupnja-poduzeca', [AdvisoryController::class, 'ma'])
            ->middleware(EnsureFrontendRouteLocale::class.':hr')
            ->name('advisory.ma.show');
        Route::get('advisory/sale-and-purchase-of-companies', [AdvisoryController::class, 'ma'])
            ->middleware(EnsureFrontendRouteLocale::class.':en')
            ->name('advisory.ma.show.en');
        Route::get('savjetovanje/dubinska-snimanja', [AdvisoryController::class, 'dueDiligence'])
            ->middleware(EnsureFrontendRouteLocale::class.':hr')
            ->name('advisory.due-diligence.show');
        Route::get('advisory/due-diligence', [AdvisoryController::class, 'dueDiligence'])
            ->middleware(EnsureFrontendRouteLocale::class.':en')
            ->name('advisory.due-diligence.show.en');
        Route::get('savjetovanje/procjena-vrijednosti-drustva', [AdvisoryController::class, 'valuations'])
            ->middleware(EnsureFrontendRouteLocale::class.':hr')
            ->name('advisory.valuations.show');
        Route::get('advisory/company-valuation', [AdvisoryController::class, 'valuations'])
            ->middleware(EnsureFrontendRouteLocale::class.':en')
            ->name('advisory.valuations.show.en');
        Route::get('savjetovanje/porezno-savjetovanje', [AdvisoryController::class, 'tax'])
            ->middleware(EnsureFrontendRouteLocale::class.':hr')
            ->name('advisory.tax.show');
        Route::get('advisory/tax-advisory', [AdvisoryController::class, 'tax'])
            ->middleware(EnsureFrontendRouteLocale::class.':en')
            ->name('advisory.tax.show.en');
        Route::get('savjetovanje/pribavljanje-financiranja', [AdvisoryController::class, 'funding'])
            ->middleware(EnsureFrontendRouteLocale::class.':hr')
            ->name('advisory.funding.show');
        Route::get('advisory/raising-finance', [AdvisoryController::class, 'funding'])
            ->middleware(EnsureFrontendRouteLocale::class.':en')
            ->name('advisory.funding.show.en');
        Route::get('savjetovanje/pribavljanje-financiranja/bankovni-krediti', [AdvisoryController::class, 'bankLoans'])
            ->middleware(EnsureFrontendRouteLocale::class.':hr')
            ->name('advisory.bank-loans.show');
        Route::get('advisory/raising-finance/bank-loans', [AdvisoryController::class, 'bankLoans'])
            ->middleware(EnsureFrontendRouteLocale::class.':en')
            ->name('advisory.bank-loans.show.en');
        Route::get('savjetovanje/pribavljanje-financiranja/zakon-o-poticanju-ulaganja', [AdvisoryController::class, 'investmentIncentives'])
            ->middleware(EnsureFrontendRouteLocale::class.':hr')
            ->name('advisory.investment-incentives.show');
        Route::get('advisory/raising-finance/investment-incentives', [AdvisoryController::class, 'investmentIncentives'])
            ->middleware(EnsureFrontendRouteLocale::class.':en')
            ->name('advisory.investment-incentives.show.en');
        Route::get('financije', function (Request $request) {
            $targetUrl = route('advisory.finance.show');
            $queryString = $request->getQueryString();

            if ($queryString) {
                $targetUrl .= '?'.$queryString;
            }

            return redirect()->to($targetUrl, 301);
        })->middleware(EnsureFrontendRouteLocale::class.':hr')->name('finance.show');
        Route::get('racunovodstvo', [AccountingController::class, 'show'])
            ->middleware(EnsureFrontendRouteLocale::class.':hr')
            ->name('accounting.show');
        Route::get('accounting', [AccountingController::class, 'show'])
            ->middleware(EnsureFrontendRouteLocale::class.':en')
            ->name('accounting.show.en');
        Route::get('revizija', [AuditController::class, 'show'])
            ->middleware(EnsureFrontendRouteLocale::class.':hr')
            ->name('audit.show');
        Route::get('audit', [AuditController::class, 'show'])
            ->middleware(EnsureFrontendRouteLocale::class.':en')
            ->name('audit.show.en');
        Route::get('porezi', function (Request $request) {
            $targetUrl = route('advisory.tax.show');
            $queryString = $request->getQueryString();

            if ($queryString) {
                $targetUrl .= '?'.$queryString;
            }

            return redirect()->to($targetUrl, 301);
        })->middleware(EnsureFrontendRouteLocale::class.':hr')->name('tax.show');
        Route::get('eu-fondovi', [EuFundsController::class, 'show'])
            ->middleware(EnsureFrontendRouteLocale::class.':hr')
            ->name('eu-funds.show');
        Route::get('eu-funds', [EuFundsController::class, 'show'])
            ->middleware(EnsureFrontendRouteLocale::class.':en')
            ->name('eu-funds.show.en');
        Route::get('eu-fondovi/upitnik', [EuFundsQuestionnaireController::class, 'create'])
            ->middleware(EnsureFrontendRouteLocale::class.':hr')
            ->name('eu-funds.questionnaire.create');
        Route::post('eu-fondovi/upitnik', [EuFundsQuestionnaireController::class, 'store'])
            ->middleware(EnsureFrontendRouteLocale::class.':hr')
            ->name('eu-funds.questionnaire.store');
        Route::get('eu-funds/questionnaire', [EuFundsQuestionnaireController::class, 'create'])
            ->middleware(EnsureFrontendRouteLocale::class.':en')
            ->name('eu-funds.questionnaire.create.en');
        Route::post('eu-funds/questionnaire', [EuFundsQuestionnaireController::class, 'store'])
            ->middleware(EnsureFrontendRouteLocale::class.':en')
            ->name('eu-funds.questionnaire.store.en');
        Route::get('eu-fondovi/pozivi/{slug}', [CallPostController::class, 'show'])
            ->middleware(EnsureFrontendRouteLocale::class.':hr')
            ->name('eu-funds.calls.show');
        Route::get('eu-funds/calls/{slug}', [CallPostController::class, 'show'])
            ->middleware(EnsureFrontendRouteLocale::class.':en')
            ->name('eu-funds.calls.show.en');
        Route::get('glossary', [GlossaryController::class, 'index'])->name('glossary.index');
        Route::get('glossary/{slug}', [GlossaryController::class, 'show'])->name('glossary.show');
        Route::get('search/suggest', [SearchController::class, 'suggest'])->name('search.suggest');
        Route::get('pretraga', [SearchController::class, 'index'])
            ->middleware(EnsureFrontendRouteLocale::class.':hr')
            ->name('search.index');
        Route::get('search', [SearchController::class, 'index'])
            ->middleware(EnsureFrontendRouteLocale::class.':en')
            ->name('search.index.en');

        Route::get('pages/category/o-nama', function (Request $request) {
            $targetUrl = route('pages.show', ['slug' => 'o-nama']);
            $queryString = $request->getQueryString();

            if ($queryString) {
                $targetUrl .= '?'.$queryString;
            }

            return redirect()->to($targetUrl, 301);
        })->name('pages.category.about-legacy');
        Route::get('pages/category/{slug}', [PageController::class, 'category'])->name('pages.category');
        Route::get('page/{slug}', function (string $slug, Request $request) {
            $targetUrl = route('pages.show', ['slug' => $slug]);
            $queryString = $request->getQueryString();

            if ($queryString) {
                $targetUrl .= '?'.$queryString;
            }

            return redirect()->to($targetUrl, 301);
        })->where('slug', '[a-z0-9-]+')->name('pages.legacy');

        Route::get('kontakt', [ContactController::class, 'create'])
            ->middleware(EnsureFrontendRouteLocale::class.':hr')
            ->name('contact.create');
        Route::post('kontakt', [ContactController::class, 'store'])
            ->middleware(EnsureFrontendRouteLocale::class.':hr')
            ->name('contact.store');
        Route::get('contact', [ContactController::class, 'create'])
            ->middleware(EnsureFrontendRouteLocale::class.':en')
            ->name('contact.create.en');
        Route::post('contact', [ContactController::class, 'store'])
            ->middleware(EnsureFrontendRouteLocale::class.':en')
            ->name('contact.store.en');
        Route::post('newsletter/prijava', NewsletterSubscriptionController::class)
            ->middleware([EnsureFrontendRouteLocale::class.':hr', 'throttle:newsletter-subscriptions'])
            ->name('newsletter.subscribe');
        Route::post('newsletter/subscribe', NewsletterSubscriptionController::class)
            ->middleware([EnsureFrontendRouteLocale::class.':en', 'throttle:newsletter-subscriptions'])
            ->name('newsletter.subscribe.en');
        Route::get('resources', [ResourceController::class, 'index'])->name('resources.index');
        Route::get('resources/{slug}', [ResourceController::class, 'show'])->name('resources.show');
        Route::post('resources/{slug}/request', [ResourceController::class, 'store'])->name('resources.request');
        Route::get('ac-forma-robot', [CollaborationAssessmentController::class, 'create'])->name('assessment.create');
        Route::post('ac-forma-robot', [CollaborationAssessmentController::class, 'store'])->name('assessment.store');
        Route::get('leasing-kalkulator', [LeaseCalculatorController::class, 'show'])->name('lease-calculator.show');
        Route::post('karijera/prijava', [CareerApplicationController::class, 'store'])
            ->middleware(EnsureFrontendRouteLocale::class.':hr')
            ->name('career.applications.store');
        Route::post('careers/apply', [CareerApplicationController::class, 'store'])
            ->middleware(EnsureFrontendRouteLocale::class.':en')
            ->name('career.applications.store.en');

    });

Route::get('dashboard', function (Request $request) {
    return redirect()->route('admin.dashboard');
})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['admin.locale', 'auth', 'verified', 'admin.access', 'admin.maintenance-bypass', 'admin.ability'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function (): void {
        Route::redirect('/', '/admin/dashboard');

        Route::view('dashboard', 'admin.dashboard')->name('dashboard');

        Route::view('categories', 'admin.categories')->name('categories');
        Route::view('categories/create', 'admin.categories.create')->name('categories.create');
        Route::get('categories/{category}/edit', function (Category $category) {
            return view('admin.categories.edit', compact('category'));
        })->name('categories.edit');

        Route::view('users', 'admin.users.index')->name('users');
        Route::view('users/access', 'admin.users.access')->name('users.access');
        Route::view('users/create', 'admin.users.create')->name('users.create');
        Route::get('users/{user}/edit', function (User $user) {
            return view('admin.users.edit', compact('user'));
        })->name('users.edit');

        Route::view('profile', 'profile')->name('profile');

        Route::prefix('content')->as('content.')->group(function (): void {
            Route::redirect('/', '/admin/content/blocks')->name('index');

            Route::view('blog', 'admin.content.blog.index')->name('blog.index');
            Route::view('blog/create', 'admin.content.blog.create')->name('blog.create');
            Route::post('blog/editor-images', BlogEditorImageController::class)->name('blog.editor-image.upload');
            Route::get('blog/{post}/preview', [BlogController::class, 'preview'])
                ->middleware(['front.locale', 'front.device'])
                ->name('blog.preview');
            Route::get('blog/{post}/edit', function (BlogPost $post) {
                return view('admin.content.blog.edit', compact('post'));
            })->name('blog.edit');

            Route::view('calls', 'admin.content.calls.index')->name('calls.index');
            Route::view('calls/create', 'admin.content.calls.create')->name('calls.create');
            Route::post('calls/editor-images', CallEditorImageController::class)->name('calls.editor-image.upload');
            Route::get('calls/{callPost}/edit', function (CallPost $callPost) {
                return view('admin.content.calls.edit', compact('callPost'));
            })->name('calls.edit');

            Route::view('team', 'admin.content.team.index')->name('team.index');
            Route::view('team/create', 'admin.content.team.create')->name('team.create');
            Route::get('team/{member}/edit', function (TeamMember $member) {
                return view('admin.content.team.edit', compact('member'));
            })->name('team.edit');

            Route::view('glossary', 'admin.content.glossary.index')->name('glossary.index');
            Route::view('glossary/create', 'admin.content.glossary.create')->name('glossary.create');
            Route::get('glossary/{term}/edit', function (GlossaryTerm $term) {
                return view('admin.content.glossary.edit', compact('term'));
            })->name('glossary.edit');

            Route::view('pages', 'admin.content.pages.index')->name('pages.index');
            Route::view('pages/create', 'admin.content.pages.create')->name('pages.create');
            Route::get('pages/{page}/edit', function (InfoPage $page) {
                return view('admin.content.pages.edit', compact('page'));
            })->name('pages.edit');

            Route::view('resources', 'admin.content.resources.index')->name('resources.index');
            Route::view('resources/create', 'admin.content.resources.create')->name('resources.create');
            Route::get('resources/{document}/edit', function (ResourceDocument $document) {
                return view('admin.content.resources.edit', compact('document'));
            })->name('resources.edit');

            Route::view('services', 'admin.content.services.index')->name('services.index');
            Route::view('services/create', 'admin.content.services.create')->name('services.create');
            Route::get('services/{servicePage}/edit', function (ServicePage $servicePage) {
                abort_if(
                    $servicePage->template_key === \App\Support\Content\ServicePageTemplateRegistry::FAMILY_BUSINESS,
                    404
                );

                return view('admin.content.services.edit', compact('servicePage'));
            })->name('services.edit');

            Route::view('faqs', 'admin.content.faqs.index')->name('faqs.index');
            Route::view('faqs/create', 'admin.content.faqs.create')->name('faqs.create');
            Route::get('faqs/{faq}/edit', function (Faq $faq) {
                return view('admin.content.faqs.edit', compact('faq'));
            })->name('faqs.edit');

            Route::view('comments', 'admin.content.comments.index')->name('comments.index');

            Route::view('blocks', 'admin.content.blocks.index')->name('blocks');
            Route::view('blocks/create', 'admin.content.blocks.create')->name('blocks.create');
            Route::get('blocks/{block}/edit', function (ContentBlock $block) {
                return view('admin.content.blocks.edit', compact('block'));
            })->name('blocks.edit');

            Route::view('navigation', 'admin.content.navigation.index')->name('navigation');

            Route::view('slots', 'admin.content.slots.index')->name('slots');
            Route::view('slots/create', 'admin.content.slots.create')->name('slots.create');
            Route::get('slots/{slot}/edit', function (ContentBlockSlot $slot) {
                return view('admin.content.slots.edit', compact('slot'));
            })->name('slots.edit');
        });

        Route::prefix('messages')->as('messages.')->group(function (): void {
            Route::view('contact', 'admin.messages.contact.index')->name('contact.index');
            Route::view('procjena-suradnje', 'admin.messages.collaboration-assessment.index')->name('collaboration-assessment.index');
            Route::view('career-cv-form', 'admin.messages.career.index')->name('career.index');
            Route::view('download-requests', 'admin.messages.download-requests.index')->name('download-requests.index');
            Route::view('eu-fondovi-upitnik', 'admin.messages.eu-funds-questionnaire.index')->name('eu-funds-questionnaire.index');
            Route::view('newsletter', 'admin.messages.newsletter.index')->name('newsletter.index');
            Route::get('career-cv-form/{careerApplication}/download', CareerApplicationDocumentController::class)
                ->name('career.download');
        });

        Route::prefix('settings')->as('settings.')->group(function (): void {
            Route::redirect('/', '/admin/settings/system/store-settings')->name('index');

            Route::get('system/runtime', function () {
                $current = auth()->user();
                abort_unless(
                    $current && ($current->isA('superadmin') || $current->can('settings.system.runtime.manage')),
                    403
                );

                return view('admin.settings.system.runtime');
            })->name('system.runtime');

            Route::view('system/admin-appearance-controls', 'admin.settings.system.admin-appearance-controls')
                ->name('system.admin-appearance-controls');
            Route::view('system/catalog-features', 'admin.settings.system.catalog-features')
                ->name('system.catalog-features');
            Route::view('system/store-settings', 'admin.settings.system.store-settings')->name('system.store-settings');
            Route::view('system/imports', 'admin.settings.system.imports')->name('system.imports');
            Route::get('api', function () {
                $current = auth()->user();
                abort_unless(
                    $current && (
                        $current->isA('superadmin')
                        || $current->isA('admin')
                        || $current->can('settings.api.manage')
                    ),
                    403
                );

                return view('admin.settings.api.index');
            })
                ->middleware('catalog.feature:catalog_use_api')
                ->name('api.index');
            Route::view('local/languages', 'admin.settings.local.resource', ['resource' => 'languages'])->name('local.languages');
            Route::view('user', 'admin.settings.user.index')->name('user.index');
        });

        Route::prefix('system')->as('system.')->group(function (): void {
            Route::post('cache/clear', [SystemToolsController::class, 'clearCache'])->name('cache.clear');
            Route::post('maintenance/on', [SystemToolsController::class, 'maintenanceOn'])->name('maintenance.on');
            Route::post('maintenance/off', [SystemToolsController::class, 'maintenanceOff'])->name('maintenance.off');
        });

        Route::prefix('ai')->as('ai.')->group(function (): void {
            Route::post('preview', [AdminAiController::class, 'preview'])->name('preview');
            Route::post('execute', [AdminAiController::class, 'execute'])->name('execute');
        });
    });

Route::redirect('profile', '/admin/profile')
    ->middleware(['auth', 'verified'])
    ->name('profile');

Route::post('logout', function (Request $request) {
    Auth::guard('web')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
})->middleware('auth')->name('logout');

require __DIR__.'/auth.php';

Route::middleware(['front.locale', 'front.device'])
    ->group(function (): void {
        // Keep CMS page slugs last so fixed top-level routes win first.
        Route::get('{slug}', [PageController::class, 'show'])
            ->where('slug', '[a-z0-9-]+')
            ->middleware(InferFrontendLocaleFromInfoPageSlug::class)
            ->name('pages.show');
    });
