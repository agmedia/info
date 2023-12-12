<?php

use App\Actions\Fortify\ForgotPasswordController;
use App\Http\Controllers\Back\Catalog\BlogController;
use App\Http\Controllers\Back\Catalog\GalleryController;
use App\Http\Controllers\Back\Catalog\PageController;
use App\Http\Controllers\Back\DashboardController;
use App\Http\Controllers\Back\Settings\FaqController;
use App\Http\Controllers\Back\Settings\FileManagerController;
use App\Http\Controllers\Back\Settings\HistoryController;
use App\Http\Controllers\Back\Settings\QuickMenuController;
use App\Http\Controllers\Back\Settings\SettingsController;
use App\Http\Controllers\Back\Settings\System\ApplicationController;
use App\Http\Controllers\Back\UserController;
use App\Http\Controllers\Back\Widget\WidgetController;
use App\Http\Controllers\Back\Widget\WidgetGroupController;
use App\Http\Controllers\Front\CustomerController;
use App\Http\Controllers\Front\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/**
 * BACK ROUTES
 */
Route::middleware(['auth:sanctum', 'verified', 'no.customers'])->prefix('admin')->group(function () {
    Route::get('setRoles', [DashboardController::class, 'setRoles'])->name('roles.set');
    Route::get('mailing-test', [DashboardController::class, 'mailing'])->name('mailing.test');

    Route::match(['get', 'post'], '/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // MARKETING
    Route::prefix('catalog')->group(function () {
        // BLOG
        Route::get('blogs', [BlogController::class, 'index'])->name('blogs');
        Route::get('blog/create', [BlogController::class, 'create'])->name('blogs.create');
        Route::post('blog', [BlogController::class, 'store'])->name('blogs.store');
        Route::get('blog/{blog}/edit', [BlogController::class, 'edit'])->name('blogs.edit');
        Route::patch('blog/{blog}', [BlogController::class, 'update'])->name('blogs.update');
        Route::delete('blog/{blog}', [BlogController::class, 'destroy'])->name('blogs.destroy');
        // GALERIJE
        Route::get('galleries', [GalleryController::class, 'index'])->name('galleries');
        Route::get('gallery/create', [GalleryController::class, 'create'])->name('gallery.create');
        Route::post('gallery', [GalleryController::class, 'store'])->name('gallery.store');
        Route::get('gallery/{gallery}/edit', [GalleryController::class, 'edit'])->name('gallery.edit');
        Route::patch('gallery/{gallery}', [GalleryController::class, 'update'])->name('gallery.update');
        Route::delete('gallery/{gallery}', [GalleryController::class, 'destroy'])->name('gallery.destroy');
        // INFO PAGES
        Route::get('pages', [PageController::class, 'index'])->name('pages');
        Route::get('page/create', [PageController::class, 'create'])->name('pages.create');
        Route::post('page', [PageController::class, 'store'])->name('pages.store');
        Route::get('page/{page}/edit', [PageController::class, 'edit'])->name('pages.edit');
        Route::patch('page/{page}', [PageController::class, 'update'])->name('pages.update');
        Route::delete('page/{page}', [PageController::class, 'destroy'])->name('pages.destroy');
    });

    // KORISNICI
    Route::get('users', [UserController::class, 'index'])->name('users');
    Route::get('user/create', [UserController::class, 'create'])->name('users.create');
    Route::post('user', [UserController::class, 'store'])->name('users.store');
    Route::get('user/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::patch('user/{user}', [UserController::class, 'update'])->name('users.update');

    // WIDGETS
    Route::prefix('widgets')->group(function () {
        Route::get('/', [WidgetController::class, 'index'])->name('widgets');
        Route::get('create', [WidgetController::class, 'create'])->name('widget.create');
        Route::post('/', [WidgetController::class, 'store'])->name('widget.store');
        Route::get('{widget}/edit', [WidgetController::class, 'edit'])->name('widget.edit');
        Route::patch('{widget}', [WidgetController::class, 'update'])->name('widget.update');
    });

    // POSTAVKE
    Route::prefix('settings')->group(function () {
        // SISTEM
        Route::prefix('system')->group(function () {
            // APPLICATION SETTINGS
            Route::get('application', [ApplicationController::class, 'index'])->name('application.settings');
        });
        // FAQ
        Route::get('faqs', [FaqController::class, 'index'])->name('faqs');
        Route::get('faq/create', [FaqController::class, 'create'])->name('faqs.create');
        Route::post('faq', [FaqController::class, 'store'])->name('faqs.store');
        Route::get('faq/{faq}/edit', [FaqController::class, 'edit'])->name('faqs.edit');
        Route::patch('faq/{faq}', [FaqController::class, 'update'])->name('faqs.update');
        Route::delete('faq/{faq}', [FaqController::class, 'destroy'])->name('faqs.destroy');
        // HISTORY
        Route::get('history', [HistoryController::class, 'index'])->name('history');
        Route::get('history/log/{history}', [HistoryController::class, 'show'])->name('history.show');
        //
        Route::get('file-manager', [FileManagerController::class, 'index'])->name('file-manager');
    });

    // SETTINGS
    Route::get('/clean/cache', [QuickMenuController::class, 'cache'])->name('cache');
    Route::get('maintenance/on', [QuickMenuController::class, 'maintenanceModeON'])->name('maintenance.on');
    Route::get('maintenance/off', [QuickMenuController::class, 'maintenanceModeOFF'])->name('maintenance.off');
});

/**
 * CUSTOMER BACK ROUTES
 */
Route::middleware(['auth:sanctum', 'verified'])->prefix('moj-racun')->group(function () {
    Route::get('/', [CustomerController::class, 'index'])->name('moj-racun');
    Route::patch('/snimi/{user}', [CustomerController::class, 'save'])->name('moj-racun.snimi');
});

/**
 * API Routes
 */
Route::prefix('api/v2')->group(function () {
    // SEARCH
    Route::get('pretrazi', [HomeController::class, 'search'])->name('api.front.search');
    // Gallery
    Route::post('/gallery/destroy/api', [GalleryController::class, 'destroyApi'])->name('gallery.destroy.api');
    Route::post('/gallery/destroy/image', [GalleryController::class, 'destroyImage'])->name('gallery.destroy.image');
    // Blog
    Route::post('/blogs/destroy/api', [BlogController::class, 'destroyApi'])->name('blogs.destroy.api');
    Route::post('/blogs/upload/image', [BlogController::class, 'uploadBlogImage'])->name('blogs.upload.image');
    // Page
    Route::post('/pages/destroy/api', [PageController::class, 'destroyApi'])->name('pages.destroy.api');
    // SETTINGS
    Route::prefix('settings')->group(function () {
        // SYSTEM
        Route::prefix('system')->group(function () {
            // APPLICATION
            Route::prefix('application')->group(function () {
                Route::post('basic/store', [ApplicationController::class, 'basicInfoStore'])->name('api.application.basic.store');
                Route::post('maps-api/store', [ApplicationController::class, 'storeGoogleMapsApiKey'])->name('api.application.google-api.store.key');
                Route::post('cache/store', [ApplicationController::class, 'storeCacheSelect'])->name('api.application.cache.store');
            });
        });
        // FRONT SETTINGS LIST
        Route::get('/get', [SettingsController::class, 'get']);
        // WIDGET
        Route::prefix('widget')->group(function () {
            Route::post('destroy', [WidgetController::class, 'destroy'])->name('widget.destroy');
            Route::get('get-links', [WidgetController::class, 'getLinks'])->name('widget.api.get-links');
        });
    });
});

/*Route::get('/phpinfo', function () {
    return phpinfo();
})->name('index');*/

/**
 * FRONT ROUTES
 */
Route::get('/', [HomeController::class, 'index'])->name('index');
Route::get('info/{page}', [HomeController::class, 'page'])->name('front.page');
Route::post('/kontakt/posalji', [HomeController::class, 'sendContactMessage'])->name('poruka');
Route::get('/faq', [HomeController::class, 'faq'])->name('faq');
Route::get('pretrazi', [HomeController::class, 'search'])->name('pretrazi');
//
Route::get('novosti/{blog?}', [HomeController::class, 'blog'])->name('catalog.route.blog');
Route::get('galerije', [HomeController::class, 'galleries'])->name('catalog.route.galleries');
/**
 * Sitemap routes
 */
Route::redirect('/sitemap.xml', '/sitemap');
Route::get('sitemap/{sitemap?}', [HomeController::class, 'sitemapXML'])->name('sitemap');
Route::get('image-sitemap', [HomeController::class, 'sitemapImageXML'])->name('sitemap');
/**
 * Forgot password & login routes.
 */
Route::get('forgot-password', [ForgotPasswordController::class, 'showForgetPasswordForm'])->name('forget.password.get');
Route::post('forgot-password', [ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('forget.password.post');
Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');
Route::post('reset-password', [ForgotPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');
//
//Route::get('{group}/{cat?}/{subcat?}/{prod?}', [CatalogRouteController::class, 'resolve'])->name('catalog.route');


Route::fallback(function () {
    return view('errors.404');
});
