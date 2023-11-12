<?php

namespace App\Models\Front;

use App\Helpers\Helper;
use App\Models\Back\Catalog\Gallery\GalleryImage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{

    /**
     * @var string
     */
    protected $table = 'galleries';

    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * @var string[]
     */
    protected $appends = [];


    /**
     * @param bool $all
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images(bool $all = true)
    {
        if ($all) {
            return $this->hasMany(GalleryImage::class, 'gallery_id')->orderBy('sort_order');
        }

        return $this->hasMany(GalleryImage::class, 'gallery_id')->where('sort_order', '=', 0);
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Blog()
    {
        return $this->belongsTo(Blog::class, 'target_id')->select('id', 'title', 'short_description');
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
    public function scopeLast(Builder $query, $count = 7): Builder
    {
        return $query->orderBy('created_at', 'desc')->limit($count);
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
    public function scopeBasic(Builder $query): Builder
    {
        return $query->select(['id', 'title', 'featured', 'sort_order']);
    }


    /**
     * @param array $data
     *
     * @return Builder
     */
    public static function buildWidgetQuery(array $data)
    {
        $galleries = Gallery::query();

        if (isset($data['query']) && $data['query'] && $data['query_data'] == 'replace') {
            return Helper::resolveEloquentStringQuery($galleries, $data['query']);
        }

        $galleries->active();

        if (isset($data['new']) && $data['new']) {
            $galleries->last(12);
        }

        if (isset($data['popular']) && $data['popular']) {
            $galleries->featured();
        }

        if (isset($data['items_list']) && $data['items_list']) {
            $galleries->whereIn('id', $data['list']);
        }

        $galleries->with(['images']);

        if (isset($data['query']) && $data['query'] && $data['query_data'] == 'add') {
            return Helper::resolveEloquentStringQuery($galleries, $data['query']);
        }

        return $galleries->get();
    }

}
