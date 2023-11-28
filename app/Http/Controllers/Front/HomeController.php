<?php

namespace App\Http\Controllers\Front;

use App\Helpers\Helper;
use App\Helpers\Recaptcha;
use App\Http\Controllers\Controller;
use App\Imports\ProductImport;
use App\Mail\ContactFormMessage;
use App\Models\Back\Catalog\Review;
use App\Models\Front\Blog;
use App\Models\Front\Faq;
use App\Models\Front\Gallery;
use App\Models\Front\Page;
use App\Models\Sitemap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Facades\Image;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class HomeController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        /*$page = Helper::resolveCache('home')->remember('news', config('cache.life'), function () {
            return Page::query()->where('group', 'page')->where('subgroup', 'special')->where('title', 'Homepage');
        });*/

        $page = Page::query()->special()->where('slug', 'homepage')->first();

        $page->description = Page::resolveDescription($page);

        return view('front.pages.page', compact('page'));
    }


    /**
     * @param Blog $blog
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function blog(Blog $blog)
    {
        $page = Page::resolvePage($blog);
        $blog = $blog->exists ? $blog : null;

        $page->description = Page::resolveDescription($page, $blog);

        return view('front.pages.page', compact('page'));
    }


    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function galleries()
    {
        $news      = Blog::getHomePageNews();
        $galleries = Gallery::query()->latest()->with(['blog', 'images'])->get();

        //dd($galleries->toArray());

        return view('front.pages.galleries', compact('galleries', 'news'));
    }


    /**
     * @param Page $page
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function page(Page $page)
    {
        $template              = Page::resolvePage($page);
        $template->description = Page::resolveDescription($template, $page);

        return view('front.pages.page')->with(['page' => $template]);
    }


    /**
     * @param Faq $faq
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function faq()
    {
        $faq = Faq::where('status', 1)->get();

        return view('front.faq', compact('faq'));
    }


    /**
     *
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        if ($request->has(config('settings.search_keyword'))) {
            if ( ! $request->input(config('settings.search_keyword'))) {
                return redirect()->back()->with(['error' => 'Oops..! Zaboravili ste upisati pojam za pretraživanje..!']);
            }

            $group  = null;
            $cat    = null;
            $subcat = null;

            $ids = Helper::search(
                $request->input(config('settings.search_keyword'))
            );

            $crumbs = null;

            return view('front.catalog.category.index', compact('group', 'cat', 'subcat', 'ids', 'crumbs'));
        }

        if ($request->has(config('settings.search_keyword') . '_api')) {
            $search = Helper::search(
                $request->input(config('settings.search_keyword') . '_api')
            );

            return response()->json($search);
        }

        return response()->json(['error' => 'Greška kod pretrage..! Molimo pokušajte ponovo ili nas kotaktirajte! HVALA...']);
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function sendContactMessage(Request $request)
    {
        Log::info($request->toArray());

        return response()->json(['success' => 'Vaša poruka je uspješno poslana.! Odgovoriti ćemo vam uskoro.']);

        $request->validate([
            'template-contactform-name'    => 'required',
            'template-contactform-email'   => 'required|email',
            'template-contactform-phone'   => 'required',
            'template-contactform-message' => 'required',
        ]);

        // Recaptcha
        /*$recaptcha = (new Recaptcha())->check($request->toArray());

        Log::info($recaptcha->ok());

        if ( ! $recaptcha->ok()) {
            return redirect()->route('front.page', ['page' => 'kontakt', 'error' => 'ReCaptcha Error! Kontaktirajte administratora!']);
        }*/

        $message = $request->toArray();

        dispatch(function () use ($message) {
            $sent = Mail::to(config('mail.admin'))->send(new ContactFormMessage($message));

            Log::info($sent);
        });

        return redirect()->route('front.page', ['page' => 'kontakt', 'success' => 'Vaša poruka je uspješno poslana.! Odgovoriti ćemo vam uskoro.']);
    }


    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function imageCache(Request $request)
    {
        $src = $request->input('src');

        $cacheimage = Image::cache(function ($image) use ($src) {
            $image->make($src);
        }, config('imagecache.lifetime'));

        return Image::make($cacheimage)->response();
    }


    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function thumbCache(Request $request)
    {
        if ( ! $request->has('src')) {
            return asset('media/img/knjiga-detalj.jpg');
        }

        $cacheimage = Image::cache(function ($image) use ($request) {
            $width  = 400;
            $height = 400;

            if ($request->has('size')) {
                if (strpos($request->input('size'), 'x') !== false) {
                    $size   = explode('x', $request->input('size'));
                    $width  = $size[0];
                    $height = $size[1];
                }
            } else {
                $width = $request->input('size');
            }

            $image->make($request->input('src'))->resize($width, $height);

        }, config('imagecache.lifetime'));

        return Image::make($cacheimage)->response();
    }


    /**
     * @param Request $request
     * @param null    $sitemap
     *
     * @return \Illuminate\Http\Response
     */
    public function sitemapXML(Request $request, $sitemap = null)
    {
        if ( ! $sitemap) {
            $items = config('settings.sitemap');

            return response()->view('front.layouts.partials.sitemap-index', [
                'items' => $items
            ])->header('Content-Type', 'text/xml');
        }

        $sm = new Sitemap($sitemap);

        return response()->view('front.layouts.partials.sitemap', [
            'items' => $sm->getSitemap()
        ])->header('Content-Type', 'text/xml');
    }


    /**
     * @return \Illuminate\Http\Response
     */
    public function sitemapImageXML()
    {
        $sm = new Sitemap('images');

        return response()->view('front.layouts.partials.sitemap-image', [
            'items' => $sm->getResponse()
        ])->header('Content-Type', 'text/xml');
    }

}
