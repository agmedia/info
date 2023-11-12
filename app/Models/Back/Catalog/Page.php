<?php

namespace App\Models\Back\Catalog;

use App\Helpers\Helper;
use App\Models\Back\Settings\Category;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Page extends Model
{

    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'pages';

    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * @var Request
     */
    protected $request;


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
    public function scopeSubgroups(Builder $query, string $only = null, string $without_target = null): Builder
    {
        if ($only) {
            return $query->groupBy('subgroup')->whereNotNull('subgroup')->where('subgroup', '==', $only);
        }

        if ($without_target) {
            return $query->groupBy('subgroup')->whereNotNull('subgroup')->where('subgroup', '!=', $without_target);
        }

        return $query->groupBy('subgroup')->whereNotNull('subgroup');
    }


    /**
     * Validate new category Request.
     *
     * @param Request $request
     *
     * @return $this
     */
    public function validateRequest(Request $request)
    {
        $request->validate([
            'title' => 'required'
        ]);

        $this->request = $request;

        return $this;
    }


    /**
     * Store new category.
     *
     * @return false
     */
    public function create()
    {
        $id = $this->insertGetId($this->createModelArray());

        if ($id) {
            return $this->find($id);
        }

        return false;
    }


    /**
     * @param Category $category
     *
     * @return false
     */
    public function edit()
    {
        $id = $this->update($this->createModelArray('update'));

        if ($id) {
            return $this->find($this->id);
        }

        return false;
    }


    /**
     * @param Category $category
     *
     * @return bool
     */
    public function resolveImage(Page $page)
    {
        if ($this->request->hasFile('image')) {
            $name = Str::slug($page->title) . '-' . Str::random(9) . '.' . $this->request->image->extension();

            $this->request->image->storeAs('/', $name, 'page');

            return $page->update([
                'image' => config('filesystems.disks.page.url') . $name
            ]);
        }

        return false;
    }

    /*******************************************************************************
     *                                Copyright : AGmedia                           *
     *                              email: filip@agmedia.hr                         *
     *******************************************************************************/

    /**
     * @param string $method
     *
     * @return array
     */
    private function createModelArray(string $method = 'insert'): array
    {
        $response = [
            'category_id'       => null,
            'group'             => 'page',
            'subgroup'          => $this->resolveSubgroup(),
            'resource'          => $this->request->resource,
            'resource_data'     => $this->setResourceData(),
            'title'             => $this->request->title,
            'short_description' => $this->request->short_description,
            'meta_title'        => $this->request->meta_title,
            'meta_description'  => $this->request->meta_description,
            'slug'              => $this->resolveSlug(),
            'keywords'          => null,
            'publish_date'      => null,
            'keywords'          => false,
            'status'            => (isset($this->request->status) and $this->request->status == 'on') ? 1 : 0,
            'updated_at'        => Carbon::now()
        ];

        if ($this->request->description) {
            $response['description'] = $this->request->description;
        }

        if ($method == 'insert') {
            $response['created_at'] = Carbon::now();
        }

        return $response;
    }


    /**
     * @return mixed|string|null
     */
    private function resolveSubgroup()
    {
        if ($this->request->group) {
            return $this->request->group;
        }

        if (isset($this->request->special) and $this->request->special == 'on') {
            if ($this->request->description) {
                $filepath = Helper::resolveViewFilepath($this->resolveSlug(), 'pages');

                Storage::disk('view')->put($filepath, $this->request->description);
            }

            return 'special';
        }

        return null;
    }


    /**
     * @return string
     */
    private function resolveSlug(): string
    {
        return isset($this->request->slug) ? Str::slug($this->request->slug) : Str::slug($this->request->title);
    }


    /**
     * @return false|string
     */
    private function setResourceData()
    {
        $query = '';

        if (isset($this->request->query_string) && $this->request->query_string) {
            $query = str_replace('"', '', $this->request->query_string);
        }

        $data = [
            'query' => $query,
            'query_data' => 'replace'
        ];

        return json_encode($data);
    }

    /*******************************************************************************
     *                                Copyright : AGmedia                           *
     *                              email: filip@agmedia.hr                         *
     *******************************************************************************/

    /**
     * @param Request $request
     *
     * @return Builder
     */
    public static function adminSearch(Request $request): Builder
    {
        $pages = Page::query()->where('group', 'page');

        if ($request->has('search') && $request->search) {
            $pages->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->has('group') && $request->group) {
            $pages->where('subgroup', $request->group);
        }

        if ( ! $request->has('group')) {
            $pages->where('subgroup', '!=', 'special');
        }

        return $pages;
    }
}
