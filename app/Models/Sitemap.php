<?php

namespace App\Models;

use App\Models\Front\Catalog\Category;
use App\Models\Front\Page;
use Illuminate\Support\Carbon;

/**
 * Class Sitemap
 * @package App\Models
 */
class Sitemap
{

    /**
     * @var string|null
     */
    private $sitemap;

    /**
     * @var array
     */
    private $response = [];


    /**
     * Sitemap constructor.
     *
     * @param string|null $sitemap
     */
    public function __construct(string $sitemap = null)
    {
        $this->sitemap = $this->setSitemap($sitemap);
    }


    /**
     * @return string|null
     */
    public function getSitemap()
    {
        return $this->sitemap;
    }


    /**
     * @return array
     */
    public function getResponse(): array
    {
        return $this->response;
    }


    /**
     * @param string $sitemap
     *
     * @return array
     */
    private function setSitemap(string $sitemap)
    {
        if ( ! $sitemap) {
            return $sitemap;
        }

        if ($sitemap == 'pages' || $sitemap == 'pages.xml') {
            return $this->getPages();
        }

        if ($sitemap == 'categories' || $sitemap == 'categories.xml') {
            return $this->getCategories();
        }

        if ($sitemap == 'images' || $sitemap == 'img') {
            return $this->getImages();
        }
    }


    /**
     * @return array
     */
    private function getImages(): array
    {
        return [];
    }


    /**
     * @return array
     */
    private function getPages(): array
    {
        $pages = Page::query()->where('group', 'page')->where('slug', '!=', 'homepage')->where('status', '=', 1)->select('slug', 'status', 'updated_at')->get();
        $blogs = Page::query()->where('group', 'blog')->where('status', '=', 1)->select('slug', 'status', 'updated_at')->get();

        $this->response[] = [
            'url' => route('index'),
            'lastmod' => Carbon::now()->startOfMonth()->tz('UTC')->toAtomString()
        ];

        $this->response[] = [
            'url' => route('front.page', ['page' => 'kontakt']),
            'lastmod' => Carbon::now()->startOfYear()->tz('UTC')->toAtomString()
        ];

        $this->response[] = [
            'url' => route('faq'),
            'lastmod' => Carbon::now()->startOfYear()->tz('UTC')->toAtomString()
        ];

        foreach ($pages as $page) {
            $this->response[] = [
                'url' => route('front.page', ['page' => $page->slug]),
                'lastmod' => $page->updated_at->tz('UTC')->toAtomString()
            ];
        }

        foreach ($blogs as $blog) {
            $this->response[] = [
                'url' => route('catalog.route.blog', ['blog' => $blog->slug]),
                'lastmod' => $blog->updated_at->tz('UTC')->toAtomString()
            ];
        }

        return $this->response;
    }


    /**
     * @return array
     */
    private function getCategories(): array
    {
        return [];
    }

}
