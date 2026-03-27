<?php

use App\Http\Controllers\Admin\AdminAiController;
use App\Http\Controllers\Admin\BlogEditorImageController;
use App\Http\Controllers\Admin\CareerApplicationDocumentController;
use App\Http\Controllers\Admin\SystemToolsController;
use App\Http\Controllers\Front\BlogController;
use App\Http\Controllers\Front\CareerApplicationController;
use App\Http\Controllers\Front\CollaborationAssessmentController;
use App\Http\Controllers\Front\ContactController;
use App\Http\Controllers\Front\FamilyBusinessController;
use App\Http\Controllers\Front\FinanceController;
use App\Http\Controllers\Front\FaqController;
use App\Http\Controllers\Front\GlossaryController;
use App\Http\Controllers\Front\LeaseCalculatorController;
use App\Http\Controllers\Front\PageController;
use App\Http\Controllers\Front\SearchController;
use App\Http\Controllers\Front\ResourceController;
use App\Http\Controllers\Front\TeamController;
use App\Http\Controllers\Front\StorefrontController;
use App\Models\Catalog\Category\Category;
use App\Models\Content\Blog\BlogPost;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

            return redirect()->back();
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
        Route::get('alpha-capitalis-tim', [TeamController::class, 'index'])->name('team.index');
        Route::get('financije', [FinanceController::class, 'show'])->name('finance.show');
        Route::get('obiteljski-biznis', [FamilyBusinessController::class, 'show'])->name('family-business.show');
        Route::get('glossary', [GlossaryController::class, 'index'])->name('glossary.index');
        Route::get('glossary/{slug}', [GlossaryController::class, 'show'])->name('glossary.show');
        Route::get('search/suggest', [SearchController::class, 'suggest'])->name('search.suggest');
        Route::get('search', [SearchController::class, 'index'])->name('search.index');

        Route::get('pages/category/{slug}', [PageController::class, 'category'])->name('pages.category');
        Route::get('page/{slug}', function (string $slug, Request $request) {
            $targetUrl = route('pages.show', ['slug' => $slug]);
            $queryString = $request->getQueryString();

            if ($queryString) {
                $targetUrl .= '?'.$queryString;
            }

            return redirect()->to($targetUrl, 301);
        })->where('slug', '[a-z0-9-]+')->name('pages.legacy');

        Route::get('contact', [ContactController::class, 'create'])->name('contact.create');
        Route::post('contact', [ContactController::class, 'store'])->name('contact.store');
        Route::get('resources', [ResourceController::class, 'index'])->name('resources.index');
        Route::get('resources/{slug}', [ResourceController::class, 'show'])->name('resources.show');
        Route::post('resources/{slug}/request', [ResourceController::class, 'store'])->name('resources.request');
        Route::get('ac-forma-robot', [CollaborationAssessmentController::class, 'create'])->name('assessment.create');
        Route::post('ac-forma-robot', [CollaborationAssessmentController::class, 'store'])->name('assessment.store');
        Route::get('leasing-kalkulator', [LeaseCalculatorController::class, 'show'])->name('lease-calculator.show');
        Route::post('karijera/prijava', [CareerApplicationController::class, 'store'])->name('career.applications.store');

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
        Route::get('users/{user}/edit', function (User $user) {
            return view('admin.users.edit', compact('user'));
        })->name('users.edit');

        Route::view('profile', 'profile')->name('profile');

        Route::prefix('content')->as('content.')->group(function (): void {
            Route::redirect('/', '/admin/content/blocks')->name('index');

            Route::view('blog', 'admin.content.blog.index')->name('blog.index');
            Route::view('blog/create', 'admin.content.blog.create')->name('blog.create');
            Route::post('blog/editor-images', BlogEditorImageController::class)->name('blog.editor-image.upload');
            Route::get('blog/{post}/edit', function (BlogPost $post) {
                return view('admin.content.blog.edit', compact('post'));
            })->name('blog.edit');

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
            Route::view('career-cv-form', 'admin.messages.career.index')->name('career.index');
            Route::view('download-requests', 'admin.messages.download-requests.index')->name('download-requests.index');
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
            ->name('pages.show');
    });
