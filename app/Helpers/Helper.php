<?php


namespace App\Helpers;

use App\Models\Back\Catalog\Category;
use App\Models\Back\Settings\Settings;
use App\Models\Back\Widget\Widget;
use App\Models\Back\Widget\WidgetGroup;
use App\Models\Front\Blog;
use App\Models\Front\Catalog\Author;
use App\Models\Back\Catalog\Review;
use App\Models\Front\Catalog\Product;
use App\Models\Front\Catalog\Publisher;
use App\Models\Front\Gallery;
use App\Models\Front\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\Compilers\BladeCompiler;

class Helper
{

    /**
     * @param float $price
     * @param int   $discount
     *
     * @return float|int
     */
    public static function calculateDiscountPrice(float $price, int $discount)
    {
        return $price - ($price * ($discount / 100));
    }


    /**
     * @param $list_price
     * @param $seling_price
     *
     * @return float|int
     */
    public static function calculateDiscount($list_price, $seling_price)
    {
        if (is_string($list_price)) {
            $list_price = str_replace('.', '', $list_price);
            $list_price = str_replace(',', '.', $list_price);
        }
        if (is_string($seling_price)) {
            $seling_price = str_replace('.', '', $seling_price);
            $seling_price = str_replace(',', '.', $seling_price);
        }

        return (($list_price - $seling_price) / $list_price) * 100;
    }


    /**
     * @return string[]
     */
    public static function abc()
    {
        return ['A', 'B', 'C', 'Ć', 'Č', 'D', 'Đ', 'Dž', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'Lj', 'M', 'N', 'Nj', 'O', 'P', 'R', 'S', 'Š', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'Ž'];
    }


    /**
     * @param string $price
     *
     * @return string
     */
    public static function priceString($price): string
    {
        if (is_float($price)) {
            $price = '"' . number_format($price, 2) . '"';
        }

        if ( ! is_string($price)) {
            return 'Not a number.!';
        }

        $set = explode('.', $price);

        if ( ! isset($set[1])) {
            $set[1] = '00';
        }

        return number_format($price, 0, '', '.') . ',<small>' . substr($set[1], 0, 2) . 'kn</small>';
    }


    /**
     * @param string $target
     * @param bool   $builder
     *
     * @return array|false|Collection
     */
    public static function search(string $target = '', bool $builder = false)
    {
        if ($target != '') {
            $response = collect();

            $products = Product::active()->where('name', 'like', '%' . $target . '%')
                ->orWhere('meta_description', 'like', '%' . $target . '%')
                ->orWhere('sku', 'like', '%' . $target . '%')
                ->orWhere('isbn', 'like', '%' . $target . '%')
                ->pluck('id');

            if ( ! $products->count()) {
                $products = collect();
            }

            $preg = explode(' ', $target, 3);

            if (isset ($preg[1]) && in_array($preg[1], $preg) && ! isset($preg[2])) {
                $authors = Author::active()->where('title', 'like', '%' . $preg[0] . '%' . $preg[1] . '%')
                                 ->orWhere('title', 'like', '%' . $preg[1] . '% ' . $preg[0] . '%')
                                 ->with('products')->get();

            } elseif (isset ($preg[2]) && in_array($preg[2], $preg)) {
                $authors = Author::active()->where('title', 'like', $preg[0] . '%' . $preg[1] . '%' . $preg[2] . '%')
                                 ->orWhere('title', 'like', $preg[2] . '%' . $preg[1] . '% ' . $preg[0] . '%')
                                 ->orWhere('title', 'like', $preg[0] . '%' . $preg[2] . '% ' . $preg[1] . '%')
                                 ->orWhere('title', 'like', $preg[1] . '%' . $preg[0] . '% ' . $preg[2] . '%')
                                 ->orWhere('title', 'like', $preg[1] . '%' . $preg[2] . '% ' . $preg[0] . '%')
                                 ->with('products')->get();

            } else {
                $authors = Author::active()->where('title', 'like', '%' . $preg[0] . '%')
                                 ->with('products')->get();
            }

            foreach ($authors as $author) {
                $products = $products->merge($author->products->pluck('id'));
            }

            $response->put('products', $products->unique()->flatten());

            if ($builder) {
                return $response;
            }

            return $response['products']->toJson();
        }

        return false;
    }


    /**
     * @param Builder $query
     * @param string  $search
     *
     * @return Builder
     */
    public static function searchByTitle(Builder $query, string $search): Builder
    {
        $preg = explode(' ', $search, 3);

        if (isset ($preg[1]) && in_array($preg[1], $preg) && ! isset($preg[2])) {
            $query->where('title', 'like', '%' . $preg[0] . '%' . $preg[1] . '%')
                  ->orWhere('title', 'like', '%' . $preg[1] . '% ' . $preg[0] . '%');

        } elseif (isset ($preg[2]) && in_array($preg[2], $preg)) {
            $query->where('title', 'like', $preg[0] . '%' . $preg[1] . '%' . $preg[2] . '%')
                  ->orWhere('title', 'like', $preg[2] . '%' . $preg[1] . '% ' . $preg[0] . '%')
                  ->orWhere('title', 'like', $preg[0] . '%' . $preg[2] . '% ' . $preg[1] . '%')
                  ->orWhere('title', 'like', $preg[1] . '%' . $preg[0] . '% ' . $preg[2] . '%')
                  ->orWhere('title', 'like', $preg[1] . '%' . $preg[2] . '% ' . $preg[0] . '%');

        } else {
            $query->where('title', 'like', '%' . $preg[0] . '%');
        }

        return $query;
    }


    /**
     * @param string $description
     *
     * @return string
     */
    public static function setDescription(string $description): string
    {
        if ($description == '') {
            return '';
        }

        $iterator = substr_count($description, '++');

        if ($iterator) {
            $offset = 0;
            $ids = [];

            for ($i = 0; $i < $iterator / 2; $i++) {
                $from = strpos($description, '++', $offset) + 2;
                $to = strpos($description, '++', $from + 2);
                $ids[] = substr($description, $from, $to - $from);

                $offset = $to + 2;
            }

            foreach ($ids as $id) {
                $widget = Widget::query()->where('id', $id)->orWhere('slug', $id)->where('status', 1)->first();

                $view = Helper::resolveWidgetDescription($widget);

                $description = str_replace('++' . $id . '++', $view, $description);
            }
        }

        return $description;
    }


    /**
     * @param $widgets
     *
     * @return string
     */
    private static function resolveWidgetDescription(Widget $widget = null): string
    {
        $compiled = '';

        if ($widget) {
            $config = json_decode($widget->resource_data, true);

            if ( ! $widget->resource) {
                $compiled .= view('front.layouts.widgets.' . $widget->slug);

            } else {
                $items = Helper::resolveResource($widget->resource, $config);

                $compiled .= view('front.layouts.widgets.' . $widget->slug, compact('items'));
            }
        }

        return $compiled;
    }


    /**
     * @param string $resource
     * @param        $config
     *
     * @return array|Builder|Builder[]|\Illuminate\Database\Eloquent\Collection|Collection
     */
    public static function resolveResource(string $resource, $config)
    {
        if ($resource == 'blog') {
            return Blog::buildWidgetQuery($config);
        }

        if ($resource == 'info') {
            return Page::buildWidgetQuery($config)->get();
        }

        if ($resource == 'gallery') {
            return Gallery::buildWidgetQuery($config);
        }

        return collect();
    }


    /**
     * @param Builder $builder
     * @param string  $query
     *
     * @return Builder|Builder[]|Collection
     */
    public static function resolveEloquentStringQuery(Builder $builder, string $query)
    {
        $end = false;
        $items = Helper::buildQueryString($query);

        foreach ($items as $item) {
            if ($item['function'] == 'get') {
                return $builder->get();
            }

            if ($item['function'] == 'paginate') {
                return $builder->paginate(intval($item['value']));
            }

            if (in_array($item['function'], ['get', 'paginate', 'take'])) {
                $end = true;
            }

            $builder->{$item['function']}($item['value']);
        }

        if ( ! $end) {
            return $builder->get();
        }

        return $builder;
    }


    /**
     * @param string $string
     *
     * @return array
     */
    public static function buildQueryString(string $string): array
    {
        $response = [];
        $arr = explode('->', $string);

        foreach ($arr as $item) {
            if ($item) {
                $item = str_replace('"', '', $item);
                $from = strpos($item, '(');
                $to = strpos($item, ')', $from) - 1;

                $function = substr($item, 0, $from);
                $value = substr($item, $from + 1, $to - $from);

                if ($function && ! in_array($function, ['delete', 'destroy', 'truncate', 'create', 'make', 'forceDelete', 'update', 'dump', 'increment', 'decrement'])) {
                    $response[] = [
                        'function' => $function,
                        'value' => $value
                    ];
                }
            }
        }

        return $response;
    }


    /**
     * @param string $title
     * @param string $target
     *
     * @return string
     */
    public static function resolveViewFilepath(string $title, string $target): string
    {
        $path = '';

        if (in_array($target, ['pages', 'page'])) {
            $path = config('settings.pages_views');
        }
        if (in_array($target, ['widgets', 'widget'])) {
            $path = config('settings.widgets_views');
        }

        return $path . $title . '.blade.php';
    }


    /**
     * @param array $data
     *
     * @return Builder
     */
    private static function reviews(array $data): Builder
    {
        $reviews = (new Review())->newQuery();

        $reviews->where('featured', '1')->limit(10)->get();

        return $reviews;
    }


    /**
     * @param string $tag
     *
     * @return \Illuminate\Cache\TaggedCache|mixed|object
     */
    public static function resolveCache(string $tag): ?object
    {
        if (env('APP_ENV') == 'local') {
            return Cache::getFacadeRoot();
        }

        return Cache::tags([$tag]);
    }

}
