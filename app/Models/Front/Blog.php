<?php

namespace App\Models\Front;

use App\Helpers\Helper;
use App\Models\Back\Settings\Tag;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Blog extends Model
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
     * @param $value
     *
     * @return array|string|string[]
     */
    public function getImageAttribute($value)
    {
        return config('settings.images_domain') . str_replace('.jpg', '.webp', $value);
    }


    /**
     *
     */
    protected static function booted()
    {
        static::addGlobalScope('blogs', function (Builder $builder) {
            $builder->where('group', 'blog');
        });
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function gallery(): HasOne
    {
        return $this->hasOne(Gallery::class, 'target_id')->where('target', '=', 'novosti')->with('images');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class, 'page_id')->select('id', 'title');
    }


    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeBasic(Builder $query): Builder
    {
        return $query->select(['id', 'title', 'image', 'short_description', 'created_at']);
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
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', 0);
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
    public function scopeDesc(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
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
     * @param int $take
     *
     * @return array
     */
    public static function getHomePageNews(int $take = 12): array
    {
        $news = Blog::query()->active()->last($take)->get();
        $top = $news->shift();
        $first = $news->shift();
        $others = $news->shift(4);
        $sidebar = $news->all();

        return [
            'top' => $top,
            'first' => $first,
            'news' => $others,
            'side' => $sidebar
        ];
    }


    /**
     * @param array $data
     *
     * @return Builder|Builder[]|Collection
     */
    public static function buildWidgetQuery(array $data)
    {
        $blogs = Blog::query();

        if (isset($data['query']) && $data['query'] && $data['query_data'] == 'replace') {
            return Helper::resolveEloquentStringQuery($blogs, $data['query']);
        }

        $blogs->active();

        if (isset($data['new']) && $data['new']) {
            $blogs->last(12);
        }

        if (isset($data['popular']) && $data['popular']) {
            $blogs->popular();
        }

        if (isset($data['items_list']) && $data['items_list']) {
            $blogs->whereIn('id', $data['list']);
        }

        //$blogs->with(['gallery', 'tags']);

        if (isset($data['query']) && $data['query'] && $data['query_data'] == 'add') {
            return Helper::resolveEloquentStringQuery($blogs, $data['query']);
        }

        return $blogs->get();
    }


    /**
     * @param int $take
     *
     * @return array
     */
    public static function getLatestNews(int $take = 6)
    {
        return Blog::query()->active()->last($take)->with('gallery')->get();
    }
}
