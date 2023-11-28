<?php

namespace App\Models\Front;

use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Page extends Model
{

    /**
     * @var string
     */
    protected $table = 'pages';

    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];


    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }


    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 1);
    }


    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeLast(Builder $query, $count = 9): Builder
    {
        return $query->orderBy('created_at', 'desc')->limit($count);
    }


    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopePopular(Builder $query, $count = 9): Builder
    {
        return $query->orderBy('viewed', 'desc')->limit($count);
    }


    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeFeatured(Builder $query, $count = 9): Builder
    {
        return $query->where('featured', 1)->orderBy('updated_at', 'desc')->limit($count);
    }


    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeSubgroup(Builder $query, string $subgroup): Builder
    {
        return $query->where('group', '=', 'page')->where('subgroup', '=', $subgroup)->select('id', 'subgroup', 'title', 'slug');
    }


    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeSubgroups(Builder $query): Builder
    {
        return $query->groupBy('subgroup')->whereNotNull('subgroup');
    }


    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeGroups(Builder $query): Builder
    {
        return $query->groupBy('group')->whereNotNull('group');
    }


    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeSpecial(Builder $query): Builder
    {
        return $query->where('group', 'page')->where('subgroup', 'special');
    }

    /*******************************************************************************
     *                                Copyright : AGmedia                           *
     *                              email: filip@agmedia.hr                         *
     *******************************************************************************/

    /**
     * @param array $data
     *
     * @return Builder
     */
    public static function buildWidgetQuery(array $data): Builder
    {
        $pages = Page::query();

        if (isset($data['query']) && $data['query'] && $data['query_data'] == 'replace') {
            return Helper::resolveEloquentStringQuery($pages, $data['query']);
        }

        $pages->active();

        if (isset($data['new']) && $data['new']) {
            $pages->last(12);
        }

        if (isset($data['popular']) && $data['popular']) {
            $pages->popular();
        }

        if (isset($data['items_list']) && $data['items_list']) {
            $pages->whereIn('id', $data['list']);
        }

        $pages->with(['gallery', 'tags']);

        if (isset($data['query']) && $data['query'] && $data['query_data'] == 'add') {
            return Helper::resolveEloquentStringQuery($pages, $data['query']);
        }

        return $pages;
    }


    /**
     * @param Page $page
     *
     * @return string
     */
    public static function resolveDescription(Page $page, $item = null): string
    {
        if ($page->subgroup == 'special') {
            if ($item) {
                $description = view('front.pages.special.' . $page->slug, compact('page', 'item'));

            } else {
                $items = collect();

                if ($page->resource) {
                    $config = json_decode($page->resource_data, true);
                    $items = Helper::resolveResource($page->resource, $config);
                }

                $description = view('front.pages.special.' . $page->slug, compact('page', 'items'));
            }

            return Helper::setDescription($description ?: '');
        }

        return Helper::setDescription(isset($page->description) ? $page->description : '');
    }


    /**
     * @param object $page
     *
     * @return Page
     */
    public static function resolvePage(object $page): Page
    {
        if (get_class($page) == Page::class) {
            if ($page->exists) {
                if ($page->slug == 'kontakt') {
                    return Page::query()->special()->where('slug', 'kontakt')->first();
                }

                if (in_array($page->slug, ['who-are-we', 'what-we-offer', 'contact-us'])) {
                    return Page::query()->special()->where('slug', $page->slug)->first();
                }

                return Page::query()->special()->where('slug', 'stranica')->first();
            }
        }

        if (get_class($page) == Blog::class) {
            if ($page->exists) {
                return Page::query()->special()->where('slug', 'novost')->first();
            }

            return Page::query()->special()->where('slug', 'novosti')->first();
        }

        return Page::query()->first();
    }


    /**
     * @return array
     */
    public static function getCategories(): array
    {
        return [
            'docs'  => self::query()->active()->subgroup('Dokumenti')->take(3)->get()->toArray(),
            'links' => self::query()->active()->subgroup('Linkovi')->take(3)->get()->toArray(),
            'info'  => self::query()->active()->subgroup('Info')->take(7)->get()->toArray(),
            'gdpr'  => self::query()->active()->subgroup('GDPR')->take(4)->get()->toArray(),
        ];
    }
}
