<?php

namespace App\Providers;

use App\Helpers\Helper;
use App\Models\Front\Catalog\Category;
use App\Models\Front\Page;
use App\Models\User;
use App\Models\Front\Catalog\Product;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $categories = Page::getCategories();
        View::share('categories', $categories);

        Paginator::useBootstrap();
    }
}
